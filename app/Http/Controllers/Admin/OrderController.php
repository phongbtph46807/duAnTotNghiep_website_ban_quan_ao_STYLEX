<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product']);

        // 🔍 Tìm kiếm theo tên, email hoặc mã đơn
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 📦 Lọc theo trạng thái đơn hàng
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Lọc theo tên khách hàng
        if ($request->filled('full_name')) {
            $query->where('full_name', 'like', '%' . $request->full_name . '%');
        }

        // Lọc theo mã đơn
        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        // 💳 Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // 📅 Lọc theo ngày tạo
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 📊 Thống kê
        $orderStats = Order::selectRaw("
            COUNT(id) as total_orders,
            SUM(status = 'pending') as pending_orders,
            SUM(status = 'processing') as processing_orders,
            SUM(status = 'completed') as completed_orders,
            SUM(status = 'cancelled') as cancelled_orders
        ")->first();

        // Phân trang linh hoạt
        $perPage = (int) $request->input('per_page', 10);
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        // Lấy danh sách
        $orders = $query->paginate($perPage)->appends($request->query());

        return view('admin.orders.index', compact('orders', 'orderStats'));
    }

    // ✅ API đổi trạng thái AJAX
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        return response()->json(['message' => 'Cập nhật trạng thái đơn hàng thành công!']);
    }
}
