<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query tất cả yêu cầu rút tiền (bao gồm cả soft deleted để debug)
        $query = WithdrawRequest::with(['user', 'processor'])
            ->orderBy('created_at', 'desc');

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên người dùng hoặc email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $withdrawRequests = $query->paginate(20);

        // Thống kê
        $stats = [
            'pending' => WithdrawRequest::where('status', 'pending')->count(),
            'approved' => WithdrawRequest::where('status', 'approved')->count(),
            'rejected' => WithdrawRequest::where('status', 'rejected')->count(),
            'completed' => WithdrawRequest::where('status', 'completed')->count(),
            'total_amount_pending' => WithdrawRequest::where('status', 'pending')->sum('amount') ?? 0,
        ];

        return view('admin.withdraw-requests.index', compact('withdrawRequests', 'stats'));
    }

    /**
     * Approve withdraw request
     */
    public function approve(Request $request, $id)
    {
        $withdrawRequest = WithdrawRequest::findOrFail($id);

        if ($withdrawRequest->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        DB::transaction(function () use ($withdrawRequest, $request) {
            $user = $withdrawRequest->user;
            
            // Chỉ đánh dấu đã duyệt, KHÔNG trừ tiền (admin sẽ chuyển khoản bên ngoài)
            $history = $user->wallet_history ?? [];
            if (!is_array($history)) $history = [];

            // Cập nhật lịch sử yêu cầu rút tiền
            foreach ($history as &$item) {
                if (isset($item['withdraw_request_id']) && $item['withdraw_request_id'] == $withdrawRequest->id) {
                    $item['status'] = 'approved';
                    $item['note'] = ($item['note'] ?? 'Yêu cầu rút tiền') . ' (Đã duyệt - Chờ chuyển khoản)';
                    break;
                }
            }

            // Thêm giao dịch duyệt vào lịch sử (chưa trừ tiền)
            $history[] = [
                'note' => 'Yêu cầu rút tiền đã được duyệt - Chờ chuyển khoản',
                'type' => 'withdraw_approved',
                'amount' => (int) $withdrawRequest->amount,
                'balance_before' => (int) $user->wallet_balance,
                'balance_after' => (int) $user->wallet_balance, // Chưa trừ tiền
                'order_id' => '-',
                'order_code' => '-',
                'withdraw_request_id' => $withdrawRequest->id,
                'created_at' => now()->toDateTimeString(),
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name,
            ];

            $user->wallet_history = $history;
            $user->save();

            // Cập nhật trạng thái yêu cầu
            $withdrawRequest->status = WithdrawRequest::STATUS_APPROVED;
            $withdrawRequest->processed_by = Auth::id();
            $withdrawRequest->processed_at = now();
            $withdrawRequest->admin_note = $request->admin_note;
            $withdrawRequest->save();
        });

        return back()->with('success', 'Đã duyệt yêu cầu rút tiền. Vui lòng chuyển khoản và đánh dấu hoàn thành.');
    }

    /**
     * Reject withdraw request
     */
    public function reject(Request $request, $id)
    {
        $withdrawRequest = WithdrawRequest::findOrFail($id);

        if ($withdrawRequest->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        DB::transaction(function () use ($withdrawRequest, $request) {
            $user = $withdrawRequest->user;
            
            // Cập nhật lịch sử - đánh dấu yêu cầu bị từ chối (không cần hoàn tiền vì chưa trừ)
            $history = $user->wallet_history ?? [];
            if (!is_array($history)) $history = [];

            // Cập nhật lịch sử yêu cầu rút tiền
            foreach ($history as &$item) {
                if (isset($item['withdraw_request_id']) && $item['withdraw_request_id'] == $withdrawRequest->id) {
                    $item['status'] = 'rejected';
                    $item['note'] = ($item['note'] ?? 'Yêu cầu rút tiền') . ' (Đã từ chối)';
                    break;
                }
            }

            // Thêm giao dịch từ chối vào lịch sử
            $history[] = [
                'note' => 'Yêu cầu rút tiền bị từ chối: ' . ($request->admin_note ?: 'Không có lý do'),
                'type' => 'withdraw_rejected',
                'amount' => (int) $withdrawRequest->amount,
                'balance_before' => (int) $user->wallet_balance,
                'balance_after' => (int) $user->wallet_balance, // Không thay đổi vì chưa trừ tiền
                'order_id' => '-',
                'order_code' => '-',
                'withdraw_request_id' => $withdrawRequest->id,
                'created_at' => now()->toDateTimeString(),
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name,
            ];
            
            $user->wallet_history = $history;
            $user->save();

            // Cập nhật trạng thái yêu cầu
            $withdrawRequest->status = WithdrawRequest::STATUS_REJECTED;
            $withdrawRequest->processed_by = Auth::id();
            $withdrawRequest->processed_at = now();
            $withdrawRequest->admin_note = $request->admin_note ?: 'Yêu cầu bị từ chối';
            $withdrawRequest->save();
        });

        return back()->with('success', 'Đã từ chối yêu cầu rút tiền.');
    }

    /**
     * Complete withdraw request (đã chuyển tiền)
     */
    public function complete(Request $request, $id)
    {
        $withdrawRequest = WithdrawRequest::findOrFail($id);

        if ($withdrawRequest->status !== 'approved') {
            return back()->with('error', 'Chỉ có thể hoàn thành yêu cầu đã được duyệt.');
        }

        DB::transaction(function () use ($withdrawRequest, $request) {
            $user = $withdrawRequest->user;
            $amount = (int) $withdrawRequest->amount;
            
            // Admin đã chuyển khoản bên ngoài, chỉ cần trừ tiền từ ví
            // Không cần kiểm tra số dư vì admin đã chuyển khoản thực tế
            $before = (int) $user->wallet_balance;
            $after = max(0, $before - $amount); // Đảm bảo không âm

            // Trừ tiền khi admin hoàn thành (đã chuyển khoản xong)
            $history = $user->wallet_history ?? [];
            if (!is_array($history)) $history = [];

            // Cập nhật lịch sử yêu cầu rút tiền
            foreach ($history as &$item) {
                if (isset($item['withdraw_request_id']) && $item['withdraw_request_id'] == $withdrawRequest->id) {
                    $item['status'] = 'completed';
                    $item['balance_before'] = $before;
                    $item['balance_after'] = $after;
                    $item['note'] = ($item['note'] ?? 'Yêu cầu rút tiền') . ' (Đã chuyển tiền)';
                    break;
                }
            }

            // Thêm giao dịch hoàn thành vào lịch sử
            $history[] = [
                'note' => 'Đã chuyển tiền thành công: ' . ($request->admin_note ?: 'Đã chuyển tiền vào tài khoản'),
                'type' => 'withdraw_completed',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'order_id' => '-',
                'order_code' => '-',
                'withdraw_request_id' => $withdrawRequest->id,
                'created_at' => now()->toDateTimeString(),
                'created_by' => Auth::id(),
                'created_by_name' => Auth::user()->name,
            ];
            
            $user->wallet_balance = $after;
            $user->wallet_history = $history;
            $user->save();

            // Cập nhật trạng thái yêu cầu
            $withdrawRequest->status = WithdrawRequest::STATUS_COMPLETED;
            $withdrawRequest->admin_note = ($withdrawRequest->admin_note ? $withdrawRequest->admin_note . "\n" : '') . 
                ($request->admin_note ?: 'Đã chuyển tiền thành công');
            $withdrawRequest->save();
        });

        return back()->with('success', 'Đã hoàn thành yêu cầu rút tiền và trừ tiền từ ví người dùng.');
    }
}
