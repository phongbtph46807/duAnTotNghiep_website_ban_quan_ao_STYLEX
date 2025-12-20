<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Base query dùng chung cho hai bảng (đơn đang giao & đơn đã hoàn thành/hủy)
        $baseQuery = Order::with(['items.product', 'updatedByUser']);

        // 🔍 Tìm kiếm theo tên, email hoặc mã đơn
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 📦 Lọc theo trạng thái đơn hàng (áp dụng cho cả 2 bảng)
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }
        // Lọc theo tên khách hàng
        if ($request->filled('full_name')) {
            $baseQuery->where('full_name', 'like', '%' . $request->full_name . '%');
        }

        // Lọc theo mã đơn
        if ($request->filled('code')) {
            $baseQuery->where('code', 'like', '%' . $request->code . '%');
        }

        // 💳 Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $baseQuery->where('payment_status', $request->payment_status);
        }

        // 📅 Lọc theo ngày tạo
        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // 📊 Thống kê theo trạng thái
        $orderStats = Order::selectRaw("
            COUNT(id) as total_orders,
            SUM(status = 'pending')     as pending_orders,
            SUM(status = 'processing')  as processing_orders,
            SUM(status = 'shipping')    as shipping_orders,
            SUM(status = 'completed')   as completed_orders,
            SUM(status = 'cancelled')   as cancelled_orders,
            SUM(status = 'returned')    as returned_orders
        ")->first();

        // Phân trang linh hoạt
        $perPage = (int) $request->input('per_page', 10);
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        // Yêu cầu hủy / trả - Query riêng không bị ảnh hưởng bởi filter status
        $requestQuery = Order::with(['items.product', 'updatedByUser']);
        
        // Áp dụng các filter khác (trừ status) để vẫn có thể tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $requestQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('full_name')) {
            $requestQuery->where('full_name', 'like', '%' . $request->full_name . '%');
        }
        
        if ($request->filled('code')) {
            $requestQuery->where('code', 'like', '%' . $request->code . '%');
        }
        
        if ($request->filled('payment_status')) {
            $requestQuery->where('payment_status', $request->payment_status);
        }
        
        if ($request->filled('date_from')) {
            $requestQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $requestQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Luôn hiển thị các yêu cầu hủy/trả bất kể filter status
        $requestOrders = $requestQuery
            ->whereIn('status', ['cancel_request', 'return_request'])
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'request_page')
            ->appends($request->except('request_page'));
// Nhóm giao vận (chờ xác nhận / đang xử lý / đang giao)
        $shippingStatuses = ['pending', 'processing', 'shipping'];
        $shippingOrders = (clone $baseQuery)
            ->whereIn('status', $shippingStatuses)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'shipping_page')
            ->appends($request->except('shipping_page'));

        // Nhóm đã hoàn thành / đã giao
        $completedStatuses = ['completed', 'delivered'];
        $completedOrders = (clone $baseQuery)
            ->whereIn('status', $completedStatuses)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'completed_page')
            ->appends($request->except('completed_page'));

        // Nhóm đã hủy
        $cancelOrders = (clone $baseQuery)
            ->where('status', 'cancelled')
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'cancel_page')
            ->appends($request->except('cancel_page'));

        // Nhóm trả hàng / hoàn tiền
        $returnOrders = (clone $baseQuery)
            ->where('status', 'returned')
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'return_page')
            ->appends($request->except('return_page'));

        return view('admin.orders.index', compact(
            'shippingOrders',
            'completedOrders',
            'cancelOrders',
            'returnOrders',
            'orderStats',
            'requestOrders'
        ));
    }

    // ✅ API đổi trạng thái AJAX
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $newStatus = $request->input('status');

        $order->status = $newStatus;
        // Lưu người cập nhật trạng thái
        $order->updated_by = Auth::id();

        // Tự động cập nhật trạng thái thanh toán dựa trên trạng thái đơn hàng
        if (in_array($newStatus, ['completed', 'delivered']) && $order->payment_status === 'unpaid') {
            $order->payment_status = 'paid';
        }
        if ($newStatus === 'returned' && $order->payment_status === 'paid') {
            $order->payment_status = 'refunded';
        }

        $order->save();

        return response()->json(['message' => 'Cập nhật trạng thái đơn hàng thành công!']);
    }

    public function approveCancel(Request $request, Order $order)
    {


        DB::transaction(function () use ($order) {

            $order = Order::query()->whereKey($order->id)->lockForUpdate()->first();

            if ($order->status === 'cancelled') {
                return;
            }

            if ($order->payment_status === 'paid') {

                $user = User::query()->whereKey($order->user_id)->lockForUpdate()->firstOrFail();

                $amount = (int) $order->total;
                $before = (int) $user->wallet_balance;
                $after  = $before + $amount;

                $history = $user->wallet_history ?? [];
                $history[] = [
                    'type'            => 'refund',        
                    'amount'          => $amount,
                    'balance_before'  => $before,
                    'balance_after'   => $after,
                    'order_id'        => $order->id,
                    'order_code'        => $order->code,
                    'note'            => 'Hoàn tiền do duyệt hủy đơn',
                    'created_at'      => now()->toDateTimeString(),
                    'created_by'      => Auth::id(),        
                    'created_by_name'      => Auth::user()->name,       
                ];

                $user->wallet_balance = $after;
                $user->wallet_history = $history;
                $user->save();
            }

            $order->status = 'cancelled';
            
            $order->payment_status = 'refunded';
            // Lưu người cập nhật
            $order->updated_by = Auth::id();

            $order->save();
        });

        return back()->with('success', 'Đã duyệt yêu cầu hủy đơn.');
    }

    public function approveReturn(Request $request, Order $order)
    {

        DB::transaction(function () use ($order) {

            $order = Order::query()->whereKey($order->id)->lockForUpdate()->first();

            if ($order->status === 'returned') {
                return;
            }

            if ($order->payment_status === 'paid') {

                $user = User::query()->whereKey($order->user_id)->lockForUpdate()->firstOrFail();

                $amount = (int) $order->total;
                $before = (int) $user->wallet_balance;
                $after  = $before + $amount;

                $history = $user->wallet_history ?? [];
                $history[] = [
                    'type'            => 'refund',          // refund | withdraw
                    'amount'          => $amount,
                    'balance_before'  => $before,
                    'balance_after'   => $after,
                    'order_id'        => $order->id,
                    'order_code'        => $order->code,
                    'note'            => 'Hoàn tiền do duyệt hủy đơn',
                    'created_at'      => now()->toDateTimeString(),
                    'created_by'      => Auth::id(),        // ai duyệt
                    'created_by_name'      => Auth::user()->name,        // ai duyệt
                ];

                $user->wallet_balance = $after;
                $user->wallet_history = $history;
                $user->save();
            }

            $order->status = 'returned';
            // Optional: lưu trạng thái refund cho dễ kiểm soát
            $order->payment_status = 'refunded';
            // Lưu người cập nhật
            $order->updated_by = Auth::id();

            $order->save();
        });
return back()->with('success', 'Đã duyệt yêu cầu trả hàng/hoàn tiền.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);
        $order->payment_status = $data['payment_status'];
        $order->save();
        return back()->with('success', 'Cập nhật trạng thái thanh toán thành công.');
    }
}
