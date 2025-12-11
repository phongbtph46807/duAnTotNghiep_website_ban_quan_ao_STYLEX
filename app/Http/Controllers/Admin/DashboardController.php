<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class DashboardController extends Controller
{
    /**
     * @phpstan-ignore-next-line
     */
    public function index(){
        $user = Auth::user();

        if ($user->isWarehouseManager()) {
            // Warehouse Manager - redirect vào tổng quan kho
            return redirect()->route('admin.inventory.dashboard');
        }

        // Lấy dữ liệu KPIs cho Admin và Staff
        $dateFrom = Carbon::now()->subDays(30); // 30 ngày gần đây
        $kpis = $this->getDashboardKPIs($dateFrom);
        
        // Lấy dữ liệu đơn hàng gần đây
        $recentOrders = $this->getRecentOrders();
        
        // Lấy sản phẩm bán chạy
        $bestSellingProducts = $this->getBestSellingProducts();

        if ($user->isAdmin()) {
            // Admin dashboard với đầy đủ quyền
            return view('admin.dashboard', [
                'userRole' => 'admin',
                'dashboardTitle' => 'Admin Dashboard',
                'kpis' => $kpis,
                'recentOrders' => $recentOrders,
                'bestSellingProducts' => $bestSellingProducts,
                'permissions' => [
                    'manage_users' => true,
                    'manage_roles' => true,
                    'manage_products' => true,
                    'manage_categories' => true,
                    'manage_posts' => true,
                    'manage_loyalty' => true,
                    'manage_tax_shipping' => true,
                    'view_reports' => true
                ]
            ]);
        } elseif ($user->isStaff()) {
            // Staff dashboard với quyền hạn chế
            return view('admin.dashboard', [
                'userRole' => 'staff',
                'dashboardTitle' => 'Staff Dashboard',
                'kpis' => $kpis,
                'recentOrders' => $recentOrders,
                'bestSellingProducts' => $bestSellingProducts,
                'permissions' => [
                    'manage_users' => false,
                    'manage_roles' => false,
                    'manage_products' => true,
                    'manage_categories' => true,
                    'manage_posts' => true,
                    'manage_loyalty' => false,
                    'manage_tax_shipping' => false,
                    'view_reports' => true
                ]
            ]);
        }

        // Fallback - không nên xảy ra vì đã có middleware checkRole
        return redirect()->route('loginView')->with('error', 'Không có quyền truy cập');
    }

    private function getDashboardKPIs($dateFrom)
    {
        // Tổng doanh thu
        $totalEarnings = DB::table('orders')
            ->where('status', 'COMPLETED')
            ->where('created_at', '>=', $dateFrom)
            ->sum('total_amount');

        // Tổng số đơn hàng
        $totalOrders = DB::table('orders')
            ->where('created_at', '>=', $dateFrom)
            ->count();

        // Tổng số khách hàng mới
        $newCustomers = DB::table('users')
            ->where('role', 0) // Customer role
            ->where('created_at', '>=', $dateFrom)
            ->count();

        // Tổng số sản phẩm
        $totalProducts = DB::table('products')
            ->where('status', 'ACTIVE')
            ->count();

        // Đơn hàng theo trạng thái
        $orderStats = DB::table('orders')
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('
                COUNT(CASE WHEN status = "PENDING" THEN 1 END) as pending_orders,
                COUNT(CASE WHEN status = "CONFIRMED" THEN 1 END) as confirmed_orders,
                COUNT(CASE WHEN status = "COMPLETED" THEN 1 END) as completed_orders,
                COUNT(CASE WHEN status = "CANCELLED" THEN 1 END) as cancelled_orders
            ')
            ->first();

        // Doanh thu theo ngày (7 ngày gần đây)
        $dailyRevenue = DB::table('orders')
            ->where('status', 'COMPLETED')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'totalEarnings' => $totalEarnings ?: 0,
            'totalOrders' => $totalOrders,
            'newCustomers' => $newCustomers,
            'totalProducts' => $totalProducts,
            'pendingOrders' => $orderStats->pending_orders ?: 0,
            'confirmedOrders' => $orderStats->confirmed_orders ?: 0,
            'completedOrders' => $orderStats->completed_orders ?: 0,
            'cancelledOrders' => $orderStats->cancelled_orders ?: 0,
            'dailyRevenue' => $dailyRevenue
        ];
    }

    private function getRecentOrders()
    {
        return DB::table('orders as o')
            ->join('users as u', 'o.user_id', '=', 'u.id')
            ->select('o.id', 'o.order_code', 'u.name as customer_name', 
                    'o.total_amount', 'o.status', 'o.created_at')
            ->orderBy('o.created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function getBestSellingProducts()
    {
        return DB::table('order_items as oi')
            ->join('product_variants as pv', 'oi.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->leftJoin('colors as c', 'pv.color_id', '=', 'c.id')
            ->leftJoin('sizes as s', 'pv.size_id', '=', 's.id')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('o.status', 'COMPLETED')
            ->select('p.name as product_name', 'c.name as color', 's.name as size',
                    DB::raw('SUM(oi.quantity) as total_sold'),
                    DB::raw('SUM(oi.quantity * oi.price) as total_revenue'),
                    'p.image')
            ->groupBy('pv.id', 'p.name', 'c.name', 's.name', 'p.image')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
    }
}
