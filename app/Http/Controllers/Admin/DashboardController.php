<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 30);
        $dateFrom = Carbon::now()->subDays($period);

        $dashboardData = $this->getDashboardKPIs($dateFrom, $period);

        if ($user->isAdmin()) {
            return view('admin.dashboard', array_merge($dashboardData, [
                'userRole' => 'admin',
                'dashboardTitle' => 'Admin Dashboard',
                'period' => $period,
            ]));
        } elseif ($user->isStaff()) {
            return view('admin.dashboard', array_merge($dashboardData, [
                'userRole' => 'staff',
                'dashboardTitle' => 'Staff Dashboard',
                'period' => $period,
            ]));
        }

        return redirect()->route('loginView')->with('error', 'Không có quyền truy cập');
    }

    private function getDashboardKPIs($dateFrom, $period)
    {
        $dateTo = Carbon::now();
        $datePrevFrom = $dateFrom->copy()->subDays($period);
        $datePrevTo = $dateFrom->copy()->subDay();

        // 1. SALES METRICS (Current Period)
        $salesMetrics = DB::table('orders')
            ->where('created_at', '>=', $dateFrom)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                SUM(subtotal) as total_subtotal,
                SUM(discount) as total_discount,
                SUM(tax_amount) as total_tax,
                SUM(shipping_fee) as total_shipping,
                COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_orders,
                COUNT(CASE WHEN payment_status = "unpaid" THEN 1 END) as unpaid_orders
            ')
            ->first();

        // 1b. SALES METRICS (Previous Period)
        $salesMetricsPrev = DB::table('orders')
            ->whereBetween('created_at', [$datePrevFrom, $datePrevTo])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('COUNT(*) as total_orders, SUM(total) as total_revenue')
            ->first();

        // Calculate growth
        $revenueGrowth = $this->calculateGrowth(
            $salesMetricsPrev->total_revenue ?? 0,
            $salesMetrics->total_revenue ?? 0
        );
        $ordersGrowth = $this->calculateGrowth(
            $salesMetricsPrev->total_orders ?? 0,
            $salesMetrics->total_orders ?? 0
        );

        // 2. PROFIT METRICS
        $profitMetrics = DB::table('orders as o')
            ->join('order_items as oi', 'o.id', '=', 'oi.order_id')
            ->where('o.created_at', '>=', $dateFrom)
            ->where('o.status', '!=', 'cancelled')
            ->selectRaw('
                SUM(o.subtotal) as revenue,
                COALESCE(SUM(o.total_cost), 0) as cost,
                SUM(o.subtotal - COALESCE(o.total_cost, 0)) as profit
            ')
            ->first();

        $profitMetricsPrev = DB::table('orders as o')
            ->join('order_items as oi', 'o.id', '=', 'oi.order_id')
            ->whereBetween('o.created_at', [$datePrevFrom, $datePrevTo])
            ->where('o.status', '!=', 'cancelled')
            ->selectRaw('SUM(o.subtotal - COALESCE(o.total_cost, 0)) as profit')
            ->first();

        $profit = $profitMetrics->profit ?? 0;
        $revenue = $profitMetrics->revenue ?? 0;
        $profitMargin = $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0;
        $profitGrowth = $this->calculateGrowth(
            $profitMetricsPrev->profit ?? 0,
            $profit
        );

        // 3. ORDER STATUS BREAKDOWN
        $orderStatus = DB::table('orders')
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending,
                COUNT(CASE WHEN status = "processing" THEN 1 END) as processing,
                COUNT(CASE WHEN status = "shipping" THEN 1 END) as shipping,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as completed,
                COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled,
                COUNT(CASE WHEN status = "returned" THEN 1 END) as returned
            ')
            ->first();

        // 4. INVENTORY METRICS
        $inventoryMetrics = DB::table('warehouse_stocks as ws')
            ->join('product_variants as pv', 'ws.variant_id', '=', 'pv.id')
            ->selectRaw('
                COUNT(*) as total_variants,
                SUM(ws.on_hand) as total_on_hand,
                SUM(ws.available) as total_available,
                SUM(ws.on_hand * pv.cost_price) as inventory_value
            ')
            ->first();

        // 5. LOW STOCK ITEMS
        $lowStockCount = DB::table('warehouse_stocks')
            ->where('on_hand', '<=', 10)
            ->count();

        // 6. TOP PRODUCTS BY SALES
        $topProductsBySales = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->where('o.created_at', '>=', $dateFrom)
            ->where('o.status', '!=', 'cancelled')
            ->selectRaw('
                p.id,
                p.name,
                SUM(oi.quantity) as total_qty,
                SUM(oi.line_total) as total_revenue,
                COUNT(DISTINCT o.id) as order_count
            ')
            ->groupBy('p.id', 'p.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // 7. TOP PRODUCTS BY PROFIT
        $topProductsByProfit = DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->join('product_variants as pv', 'oi.variant_id', '=', 'pv.id')
            ->where('o.created_at', '>=', $dateFrom)
            ->where('o.status', '!=', 'cancelled')
            ->selectRaw('
                p.id,
                p.name,
                SUM(oi.quantity) as total_qty,
                SUM(oi.line_total) as total_revenue,
                SUM(oi.quantity * pv.cost_price) as total_cost,
                SUM(oi.line_total - (oi.quantity * pv.cost_price)) as profit
            ')
            ->groupBy('p.id', 'p.name')
            ->orderBy('profit', 'desc')
            ->limit(5)
            ->get();

        // 8. DAILY SALES TREND
        $dailySalesTrend = DB::table('orders')
            ->where('created_at', '>=', $dateFrom)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 9. PAYMENT METHOD BREAKDOWN
        $paymentMethods = DB::table('orders')
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(total) as amount
            ')
            ->groupBy('payment_method')
            ->get();

        // 10. CUSTOMER METRICS
        $customerMetrics = DB::table('orders')
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('
                COUNT(DISTINCT user_id) as unique_customers,
                COUNT(DISTINCT CASE WHEN user_id IS NOT NULL THEN user_id END) as registered_customers,
                COUNT(DISTINCT CASE WHEN user_id IS NULL THEN session_id END) as guest_customers
            ')
            ->first();

        // 11. WAREHOUSE ACTIVITY
        $warehouseActivity = DB::table('inventory_logs')
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('
                COUNT(CASE WHEN action = "IN" THEN 1 END) as stock_in,
                COUNT(CASE WHEN action = "OUT" THEN 1 END) as stock_out,
                COUNT(CASE WHEN action = "TRANSFER" THEN 1 END) as transfers,
                COUNT(CASE WHEN action = "ADJUSTMENT" THEN 1 END) as adjustments
            ')
            ->first();

        // 12. ALERTS
        $alerts = $this->generateAlerts($orderStatus, $lowStockCount, $salesMetrics->unpaid_orders ?? 0);

        return [
            // Sales
            'totalOrders' => $salesMetrics->total_orders ?? 0,
            'totalRevenue' => $salesMetrics->total_revenue ?? 0,
            'totalSubtotal' => $salesMetrics->total_subtotal ?? 0,
            'totalDiscount' => $salesMetrics->total_discount ?? 0,
            'totalTax' => $salesMetrics->total_tax ?? 0,
            'totalShipping' => $salesMetrics->total_shipping ?? 0,
            'paidOrders' => $salesMetrics->paid_orders ?? 0,
            'unpaidOrders' => $salesMetrics->unpaid_orders ?? 0,

            // Growth
            'revenueGrowth' => $revenueGrowth,
            'ordersGrowth' => $ordersGrowth,
            'profitGrowth' => $profitGrowth,

            // Profit
            'totalProfit' => $profit,
            'profitMargin' => $profitMargin,

            // Order Status
            'pendingOrders' => $orderStatus->pending ?? 0,
            'processingOrders' => $orderStatus->processing ?? 0,
            'shippingOrders' => $orderStatus->shipping ?? 0,
            'completedOrders' => $orderStatus->completed ?? 0,
            'cancelledOrders' => $orderStatus->cancelled ?? 0,
            'returnedOrders' => $orderStatus->returned ?? 0,

            // Inventory
            'totalVariants' => $inventoryMetrics->total_variants ?? 0,
            'totalOnHand' => $inventoryMetrics->total_on_hand ?? 0,
            'totalAvailable' => $inventoryMetrics->total_available ?? 0,
            'inventoryValue' => $inventoryMetrics->inventory_value ?? 0,
            'lowStockCount' => $lowStockCount,

            // Top Products
            'topProductsBySales' => $topProductsBySales,
            'topProductsByProfit' => $topProductsByProfit,

            // Trends
            'dailySalesTrend' => $dailySalesTrend,
            'paymentMethods' => $paymentMethods,

            // Customers
            'uniqueCustomers' => $customerMetrics->unique_customers ?? 0,
            'registeredCustomers' => $customerMetrics->registered_customers ?? 0,
            'guestCustomers' => $customerMetrics->guest_customers ?? 0,

            // Warehouse
            'stockIn' => $warehouseActivity->stock_in ?? 0,
            'stockOut' => $warehouseActivity->stock_out ?? 0,
            'transfers' => $warehouseActivity->transfers ?? 0,
            'adjustments' => $warehouseActivity->adjustments ?? 0,

            // Alerts
            'alerts' => $alerts,
        ];
    }

    private function calculateGrowth($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function generateAlerts($orderStatus, $lowStockCount, $unpaidOrders)
    {
        $alerts = [];

        if ($orderStatus->pending > 10) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'ri-time-line',
                'title' => 'Đơn chờ xử lý nhiều',
                'message' => "Có {$orderStatus->pending} đơn chờ xác nhận",
            ];
        }

        if ($lowStockCount > 5) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'ri-alert-line',
                'title' => 'Hàng sắp hết',
                'message' => "Có {$lowStockCount} sản phẩm cần bổ sung",
            ];
        }

        if ($unpaidOrders > 5) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'ri-bank-card-line',
                'title' => 'Thanh toán chưa hoàn tất',
                'message' => "Có {$unpaidOrders} đơn chưa thanh toán",
            ];
        }

        return $alerts;
    }
}
