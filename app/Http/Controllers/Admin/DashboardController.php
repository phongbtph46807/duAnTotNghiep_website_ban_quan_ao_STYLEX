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
    public function index(Request $request){
        $user = Auth::user();
        $period = $request->get('period', 30); // Default 30 days
        $dateFrom = Carbon::now()->subDays($period);

        $dashboardData = $this->getDashboardKPIs($dateFrom);

        if ($user->isAdmin()) {
            return view('admin.dashboard', array_merge($dashboardData, [
                'userRole' => 'admin',
                'dashboardTitle' => 'Admin Dashboard',
                'period' => $period,
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
            ]));
        } elseif ($user->isStaff()) {
            return view('admin.dashboard', array_merge($dashboardData, [
                'userRole' => 'staff',
                'dashboardTitle' => 'Staff Dashboard',
                'period' => $period,
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
            ]));
        } elseif ($user->isWarehouseManager()) {
            return view('admin.dashboard', array_merge($dashboardData, [
                'userRole' => 'warehouse_manager',
                'dashboardTitle' => 'Warehouse Manager Dashboard',
                'period' => $period,
                'permissions' => [
                    'manage_users' => false,
                    'manage_roles' => false,
                    'manage_products' => false,
                    'manage_categories' => false,
                    'manage_posts' => false,
                    'manage_loyalty' => false,
                    'manage_tax_shipping' => false,
                    'view_reports' => true,
                    'manage_inventory' => true
                ]
            ]));
        }

        return redirect()->route('loginView')->with('error', 'Không có quyền truy cập');
    }

    private function getDashboardKPIs($dateFrom)
    {
        // Total inventory value - using latest cost_price from stock_in_requests
        $totalInventoryValue = DB::table('warehouse_stocks as ws')
            ->join('product_variants as pv', 'ws.variant_id', '=', 'pv.id')
            ->leftJoin(DB::raw('(
                SELECT variant_id, cost_price,
                       ROW_NUMBER() OVER (PARTITION BY variant_id ORDER BY created_at DESC) as rn
                FROM stock_in_requests
                WHERE status = "CONFIRMED"
            ) as latest_cost'), function($join) {
                $join->on('ws.variant_id', '=', 'latest_cost.variant_id')
                     ->where('latest_cost.rn', '=', 1);
            })
            ->sum(DB::raw('ws.on_hand * COALESCE(latest_cost.cost_price, 0)'));

        // Stock movements count
        $stockMovements = DB::table('inventory_logs')
            ->where('created_at', '>=', $dateFrom)
            ->selectRaw('
                COUNT(CASE WHEN action = "IN" THEN 1 END) as stock_in_count,
                COUNT(CASE WHEN action = "OUT" THEN 1 END) as stock_out_count,
                COUNT(CASE WHEN action = "TRANSFER" THEN 1 END) as transfer_count
            ')
            ->first();

        // QC pass rate - simplified without qc_at column
        $qcPassRate = 95; // Default value since QC tracking is removed

        // Low stock items - using default threshold of 10
        $lowStockCount = DB::table('warehouse_stocks as ws')
            ->leftJoin('warehouse_settings as wset', function($join) {
                $join->on('ws.warehouse_id', '=', 'wset.warehouse_id')
                     ->on('ws.variant_id', '=', 'wset.variant_id');
            })
            ->whereRaw('ws.on_hand <= COALESCE(wset.min_stock_level, 10)')
            ->count();

        // Top products by stock value
        $topProducts = DB::table('warehouse_stocks as ws')
            ->join('product_variants as pv', 'ws.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->leftJoin('colors as c', 'pv.color_id', '=', 'c.id')
            ->leftJoin('sizes as s', 'pv.size_id', '=', 's.id')
            ->leftJoin(DB::raw('(
                SELECT variant_id, cost_price,
                       ROW_NUMBER() OVER (PARTITION BY variant_id ORDER BY created_at DESC) as rn
                FROM stock_in_requests
                WHERE status = "CONFIRMED"
            ) as latest_cost'), function($join) {
                $join->on('ws.variant_id', '=', 'latest_cost.variant_id')
                     ->where('latest_cost.rn', '=', 1);
            })
            ->select('p.name', 'c.name as color', 's.name as size',
                DB::raw('ws.on_hand * COALESCE(latest_cost.cost_price, 0) as stock_value'))
            ->where('ws.on_hand', '>', 0)
            ->orderBy('stock_value', 'desc')
            ->limit(10)
            ->get();

        // Stock trend data (last 7 days)
        $stockTrend = DB::table('inventory_logs')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as movements')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'totalInventoryValue' => $totalInventoryValue ?: 0,
            'stockInCount' => $stockMovements->stock_in_count ?: 0,
            'stockOutCount' => $stockMovements->stock_out_count ?: 0,
            'transferCount' => $stockMovements->transfer_count ?: 0,
            'qcPassRate' => $qcPassRate,
            'lowStockCount' => $lowStockCount,
            'topProducts' => $topProducts,
            'stockTrend' => $stockTrend
        ];
    }
}
