<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\OrderStatusUpdated;

class OrderController extends Controller
{
    /**
     * API lấy số lượng yêu cầu hủy/trả hàng đang chờ
     */
    public function getPendingRequestsCount()
    {
        $count = Order::whereIn('status', ['cancel_request', 'return_request'])->count();
        return response()->json(['count' => $count]);
    }

    /**
     * API lấy danh sách thông báo (đơn hàng mới và yêu cầu)
     */
    public function getNotifications()
    {
        // Đơn hàng mới (pending) trong 24h qua
        $newOrders = Order::where('status', 'pending')
            ->where('created_at', '>=', now()->subDay())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'code', 'full_name', 'total', 'created_at']);

        // Yêu cầu hủy/trả hàng đang chờ
        $pendingRequests = Order::whereIn('status', ['cancel_request', 'return_request'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'code', 'status', 'full_name', 'created_at']);

        return response()->json([
            'new_orders' => $newOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'customer' => $order->full_name,
                    'total' => number_format($order->total) . ' ₫',
                    'created_at' => $order->created_at->diffForHumans(),
                    'url' => route('admin.orders.index', ['code' => $order->code]),
                ];
            }),
            'pending_requests' => $pendingRequests->map(function ($order) {
                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'status' => $order->status,
                    'status_label' => $order->status === 'cancel_request' ? 'Yêu cầu hủy' : 'Yêu cầu trả hàng',
                    'customer' => $order->full_name,
                    'created_at' => $order->created_at->diffForHumans(),
                    'url' => route('admin.orders.index', ['code' => $order->code]),
                ];
            }),
            'new_orders_count' => $newOrders->count(),
            'pending_requests_count' => $pendingRequests->count(),
        ]);
    }

    public function index(Request $request)
    {
        // Base query dùng chung cho hai bảng (đơn đang giao & đơn đã hoàn thành/hủy)
        $baseQuery = Order::with(['items.product', 'updatedByUser.roles']);

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
        $requestQuery = Order::with(['items.product', 'updatedByUser.roles']);
        
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
        try {
            $order = Order::findOrFail($id);
            
            // Hỗ trợ cả JSON và form data
            // Laravel tự động parse JSON khi có Content-Type: application/json
            $newStatus = $request->input('status');
            
            // Nếu không có trong input, thử đọc từ JSON body trực tiếp
            if (empty($newStatus)) {
                $content = $request->getContent();
                if (!empty($content)) {
                    $jsonData = json_decode($content, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($jsonData['status'])) {
                        $newStatus = $jsonData['status'];
                    }
                }
            }

            // Validate status
            if (empty($newStatus)) {
                Log::warning('Order status update failed: empty status', [
                    'order_id' => $id,
                    'request_data' => $request->all(),
                    'request_content' => $request->getContent(),
                    'content_type' => $request->header('Content-Type')
                ]);
                return response()->json([
                    'message' => 'Trạng thái không được để trống!',
                    'error' => 'validation_error'
                ], 400);
            }

            // Validate status values
            // Lưu ý: 'shipping' và 'shipped' đều được chấp nhận (shipping = đang giao, shipped = đã giao)
            $validStatuses = ['pending', 'confirmed', 'processing', 'shipping', 'shipped', 'delivered', 'completed', 'cancelled', 'cancel_request', 'return_request', 'returned'];
            if (!in_array($newStatus, $validStatuses)) {
                return response()->json([
                    'message' => 'Trạng thái không hợp lệ!',
                    'error' => 'validation_error'
                ], 400);
            }

            // Kiểm tra: chỉ cho phép chọn "completed" khi status hiện tại là "delivered"
            if ($newStatus === 'completed' && $order->status !== 'delivered') {
                return response()->json([
                    'message' => 'Chỉ có thể chọn "Hoàn thành" sau khi đơn hàng đã ở trạng thái "Đã giao"!',
                    'error' => 'validation_error'
                ], 400);
            }

            $order->status = $newStatus;
            // Lưu người cập nhật trạng thái
            $order->updated_by = Auth::id();

            // Tự động cập nhật trạng thái thanh toán:
            // - Khi đơn hoàn thành/đã giao -> chuyển sang paid nếu đang unpaid
            // - Khi đơn trả hàng -> nếu đã thanh toán thì chuyển refunded
            if (in_array($newStatus, ['completed', 'delivered']) && $order->payment_status === 'unpaid') {
                $order->payment_status = 'paid';
            }
            if ($newStatus === 'returned' && $order->payment_status === 'paid') {
                $order->payment_status = 'refunded';
            }

            $order->save();

            // Broadcast event để cập nhật realtime
            broadcast(new OrderStatusUpdated($order->fresh()))->toOthers();

            return response()->json([
                'message' => 'Cập nhật trạng thái đơn hàng thành công!',
                'success' => true
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Không tìm thấy đơn hàng!',
                'error' => 'not_found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng: ' . $e->getMessage(),
                'error' => 'server_error'
            ], 500);
        }
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

            $order->status = 'cancelled';
            // Optional: lưu trạng thái refund cho dễ kiểm soát
            $order->payment_status = 'refunded';
            // Lưu người cập nhật
            $order->updated_by = Auth::id();

            $order->save();
        });

        // Broadcast event sau khi transaction commit
        $order->refresh();
        broadcast(new OrderStatusUpdated($order->load(['items.product', 'updatedByUser.roles'])))->toOthers();

        // Trả về JSON nếu là AJAX request, ngược lại redirect back
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Đã duyệt yêu cầu hủy đơn thành công!',
                'success' => true,
                'order' => $order->load(['items.product', 'updatedByUser.roles'])
            ]);
        }

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

        // Broadcast event sau khi transaction commit
        $order->refresh();
        broadcast(new OrderStatusUpdated($order->load(['items.product', 'updatedByUser.roles'])))->toOthers();

        // Trả về JSON nếu là AJAX request, ngược lại redirect back
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Đã duyệt yêu cầu trả hàng/hoàn tiền thành công!',
                'success' => true,
                'order' => $order->load(['items.product', 'updatedByUser.roles'])
            ]);
        }

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
