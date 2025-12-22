<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ProductVariant, Warehouse, Product, Setting, Notification, User, InventoryLog, WarehouseStock, CountRequest, DefectAssessment};
use App\Services\InventoryService;
use App\Services\StockService;
use App\Http\Requests\Admin\Inventory\SettingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class InventoryController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.inventory.dashboard');
    }

    public function dashboard()
    {
        $lowStockThreshold = (int) Setting::where('key', 'low_stock_threshold')->value('value') ?? 10;

        $onHandStock = 0;
        $availableStock = 0;
        $reservedStock = 0;
        $quarantineStock = 0;
        $damagedStock = 0;
        $clearanceStock = 0;

        if (DB::getSchemaBuilder()->hasTable('warehouse_stocks')) {
            $onHandStock = DB::table('warehouse_stocks')->sum('on_hand') ?? 0;
            $availableStock = DB::table('warehouse_stocks')->sum('available') ?? 0;
            $reservedStock = DB::table('warehouse_stocks')->sum('reserved') ?? 0;
            $quarantineStock = DB::table('warehouse_stocks')->sum('quarantine') ?? 0;
            $damagedStock = DB::table('warehouse_stocks')->sum('damaged') ?? 0;
            $clearanceStock = DB::table('warehouse_stocks')->sum('clearance') ?? 0;
        }

        $lowStockVariants = collect();
        $topSellingVariants = collect();
        $stockByWarehouse = collect();
        $pendingOutTransfers = collect();
        $pendingCountRequests = collect();
        $pendingDefectAssessments = collect();

        if (DB::getSchemaBuilder()->hasTable('warehouse_stocks')) {
            // Lấy danh sách variant có tồn kho thấp
            $lowStockVariants = DB::table('warehouse_stocks as ws')
                ->join('product_variants as pv', 'ws.variant_id', '=', 'pv.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->select('pv.id', 'pv.sku', 'p.name as product_name', 
                        DB::raw('SUM(ws.on_hand) as total_stock'))
                ->groupBy('pv.id', 'pv.sku', 'p.name')
                ->havingRaw('SUM(ws.on_hand) > 0 AND SUM(ws.on_hand) <= ?', [$lowStockThreshold])
                ->orderBy('total_stock', 'asc')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'id' => $item->id,
                        'sku' => $item->sku,
                        'product' => (object) ['name' => $item->product_name],
                        'total_on_hand_stock' => $item->total_stock
                    ];
                });

            $topSellingVariants = DB::table('order_items as oi')
                ->join('product_variants as pv', 'oi.variant_id', '=', 'pv.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->select('pv.id', 'pv.sku', 'p.name as product_name',
                        DB::raw('SUM(oi.quantity) as total_sold'))
                ->where('o.status', 'COMPLETED')
                ->where('o.created_at', '>=', now()->subDays(7))
                ->groupBy('pv.id', 'pv.sku', 'p.name')
                ->orderBy('total_sold', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'id' => $item->id,
                        'sku' => $item->sku,
                        'product' => (object) ['name' => $item->product_name],
                        'total_sold' => $item->total_sold
                    ];
                });

            $stockByWarehouse = Warehouse::where('operational_status', 'ACTIVE')
                ->get()
                ->map(function ($warehouse) {
                    $warehouse->on_hand_qty = DB::table('warehouse_stocks')
                        ->where('warehouse_id', $warehouse->id)
                        ->sum('on_hand') ?? 0;
                    $warehouse->available_qty = DB::table('warehouse_stocks')
                        ->where('warehouse_id', $warehouse->id)
                        ->sum('available') ?? 0;
                    $warehouse->reserved_qty = DB::table('warehouse_stocks')
                        ->where('warehouse_id', $warehouse->id)
                        ->sum('reserved') ?? 0;
                    $warehouse->quarantine_qty = DB::table('warehouse_stocks')
                        ->where('warehouse_id', $warehouse->id)
                        ->sum('quarantine') ?? 0;
                    $warehouse->damaged_qty = DB::table('warehouse_stocks')
                        ->where('warehouse_id', $warehouse->id)
                        ->sum('damaged') ?? 0;
                    $warehouse->clearance_qty = DB::table('warehouse_stocks')
                        ->where('warehouse_id', $warehouse->id)
                        ->sum('clearance') ?? 0;
                    return $warehouse;
                });

            $pendingOutTransfers = collect();
            if (Schema::hasColumn('warehouse_stocks', 'status')) {
                $pendingOutTransfers = WarehouseStock::where('status', 'PENDING')
                    ->where('reserved', '>', 0)
                    ->with(['warehouse', 'variant'])
                    ->get();
            }

            $pendingCountRequests = CountRequest::where('status', 'PENDING')
                ->with(['warehouse', 'variant.product', 'createdBy'])
                ->latest()
                ->limit(5)
                ->get();

            $pendingDefectAssessments = DefectAssessment::where('status', 'PENDING')
                ->with(['warehouse', 'variant.product', 'createdBy'])
                ->latest()
                ->limit(5)
                ->get();
        }

        $unreadNotifications = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.inventory.dashboard', compact(
            'onHandStock', 'availableStock', 'reservedStock', 'quarantineStock', 'damagedStock', 'clearanceStock',
            'lowStockVariants', 'lowStockThreshold',
            'topSellingVariants', 'stockByWarehouse', 'pendingOutTransfers',
            'pendingCountRequests', 'pendingDefectAssessments', 'unreadNotifications'
        ));
    }

    public function currentStock()
    {
        $search = request('search');
        $warehouseId = request('warehouse_id');
        $lowStockThreshold = (int) Setting::where('key', 'low_stock_threshold')->value('value') ?? 10;

        $query = ProductVariant::with('product:id,name,thumbnail');

        if ($search) {
            $searchTerm = '%' . $search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('sku', 'like', $searchTerm)
                    ->orWhereHas('product', function ($q2) use ($searchTerm) {
                        $q2->where('name', 'like', $searchTerm);
                    });
            });
        }

        $variantsWithStock = $query->paginate(20)->withQueryString();

        $variantsWithStock->getCollection()->transform(function ($variant) use ($warehouseId, $lowStockThreshold) {
            if ($warehouseId) {
                $details = StockService::getVariantStockDetails($variant->id, $warehouseId);
                $variant->stock_details = $details;
                $variant->total_on_hand_stock = $details['on_hand'];
            } else {
                $variant->total_on_hand_stock = StockService::getVariantTotalStock($variant->id);
            }
            $variant->is_low_stock = $variant->total_on_hand_stock <= $lowStockThreshold;
            return $variant;
        });

        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();
        $lowStockCount = $variantsWithStock->getCollection()->filter(fn($v) => $v->is_low_stock)->count();

        return view('admin.inventory.current-stock', compact('variantsWithStock', 'warehouses', 'lowStockCount', 'lowStockThreshold'));
    }

    public function showLogs()
    {
        $warehouseId = request('warehouse_id');
        $action = request('action');

        $query = InventoryLog::with(['warehouse', 'variant.product', 'user'])
            ->latest('created_at');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($action) {
            $query->where('action', $action);
        }

        $logs = $query->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();

        return view('admin.inventory.log-history', compact('logs', 'warehouses'));
    }

    public function reports()
    {
        $timeRange = request('time_range', '30');

        $inventoryValueByWarehouse = Warehouse::where('operational_status', 'ACTIVE')
            ->get()
            ->map(function ($warehouse) {
                $warehouse->total_quantity = DB::table('warehouse_stocks')
                    ->where('warehouse_id', $warehouse->id)
                    ->sum('on_hand') ?? 0;
                $warehouse->total_value = DB::table('warehouse_stocks')
                    ->where('warehouse_stocks.warehouse_id', $warehouse->id)
                    ->sum('on_hand');
                return $warehouse;
            });

        $fastMovingVariants = ProductVariant::join('order_items', 'product_variants.id', '=', 'order_items.variant_id')
            ->with('product:id,name')
            ->select('product_variants.id', 'product_variants.sku', 'product_variants.product_id')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->whereDate('order_items.created_at', '>=', now()->subDays($timeRange))
            ->groupBy('product_variants.id', 'product_variants.sku', 'product_variants.product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Thống kê defect assessments theo classification
        $defectStats = DB::table('defect_assessments')
            ->whereDate('created_at', '>=', now()->subDays($timeRange))
            ->selectRaw('classification, COUNT(*) as count, SUM(quantity) as total_qty')
            ->groupBy('classification')
            ->get()
            ->keyBy('classification');

        // Thống kê defect assessments theo defect_level
        $defectByLevel = DB::table('defect_assessments')
            ->whereDate('created_at', '>=', now()->subDays($timeRange))
            ->selectRaw('defect_level, COUNT(*) as count, SUM(quantity) as total_qty')
            ->groupBy('defect_level')
            ->get()
            ->keyBy('defect_level');

        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();

        return view('admin.inventory.reports', compact(
            'inventoryValueByWarehouse', 'fastMovingVariants', 'warehouses', 'timeRange',
            'defectStats', 'defectByLevel'
        ));
    }

    public function settings()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.inventory.settings', compact('settings'));
    }

    public function updateSettings(SettingRequest $request)
    {
        try {
            $validated = $request->validated();
            
            foreach ($validated as $key => $value) {
                // Xử lý checkbox (nếu không check thì không gửi lên)
                if (str_starts_with($key, 'notify_')) {
                    $value = $value ?? 0;
                }
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
            
            // Xử lý các checkbox không được gửi lên (tắt)
            $notifyKeys = [
                'notify_new_order',
                'notify_low_stock',
                'notify_pending_approval',
                'notify_qc_failed',
                'notify_count_discrepancy',
                'notify_defect_found',
            ];
            
            foreach ($notifyKeys as $key) {
                if (!isset($validated[$key])) {
                    Setting::updateOrCreate(['key' => $key], ['value' => 0]);
                }
            }

            return redirect()->route('admin.inventory.settings')
                ->with('success', 'Cập nhật cài đặt thành công!');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Lỗi khi lưu cài đặt: ' . $e->getMessage());
        }
    }
}
