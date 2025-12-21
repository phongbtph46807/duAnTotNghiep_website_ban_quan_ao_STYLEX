<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\InventoryLog;
use App\Models\StockInRequest;
use App\Models\StockOutRequest;
use App\Models\DefectAssessment;
use App\Models\CountRequest;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarehouseReportController extends Controller
{
    // 1.1. Báo cáo Tồn kho Hiện tại
    public function currentStock(Request $request)
    {
        $query = WarehouseStock::with(['warehouse', 'variant.product'])
            ->where('on_hand', '>', 0);

        // Filter theo kho
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter theo sản phẩm
        if ($request->search) {
            $query->whereHas('variant.product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        // Chỉ hiển thị tồn kho thấp
        if ($request->low_stock_only) {
            $query->whereRaw('available <= (
                SELECT COALESCE(min_stock_level, 10) 
                FROM warehouse_settings 
                WHERE warehouse_id = warehouse_stocks.warehouse_id 
                AND variant_id = warehouse_stocks.variant_id
            )');
        }

        $stocks = $query->paginate(20);
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();

        return view('admin.reports.current-stock', compact('stocks', 'warehouses'));
    }

    // 1.2. Báo cáo Lịch sử Giao dịch Kho
    public function inventoryLogs(Request $request)
    {
        $query = InventoryLog::with(['warehouse', 'variant.product', 'user'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();

        return view('admin.reports.inventory-logs', compact('logs', 'warehouses'));
    }

    // 1.3. Báo cáo Nhập/Xuất/Tồn
    public function inOutStock(Request $request)
    {
        $period = $request->period ?? 'month';
        $warehouseId = $request->warehouse_id;
        $variantId = $request->variant_id;

        $dateRange = $this->getDateRange($period);

        // Tồn đầu kỳ
        $beginningStock = WarehouseStock::when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
            ->sum('on_hand');

        // Nhập trong kỳ
        $stockIn = StockInRequest::where('status', 'CONFIRMED')
            ->whereBetween('confirmed_at', $dateRange)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
            ->with('qcResults')
            ->get()
            ->sum(fn($item) => $item->qcResults->sum('passed_qty'));

        // Xuất trong kỳ
        $stockOut = StockOutRequest::where('status', 'CONFIRMED')
            ->whereBetween('confirmed_at', $dateRange)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
            ->with('qcResults')
            ->get()
            ->sum(fn($item) => $item->qcResults->sum('passed_qty'));

        // Tồn cuối kỳ
        $endingStock = $beginningStock + $stockIn - $stockOut;

        $data = compact('beginningStock', 'stockIn', 'stockOut', 'endingStock');
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();
        $variants = ProductVariant::with('product')->get();

        return view('admin.reports.in-out-stock', compact('data', 'warehouses', 'variants'));
    }

    // 1.4. Báo cáo Hàng Hỏng
    public function defectReport(Request $request)
    {
        $query = DefectAssessment::with(['warehouse', 'variant.product', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->defect_level) {
            $query->where('defect_level', $request->defect_level);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from && $request->date_to) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }

        $defects = $query->paginate(20);

        // Tính tỷ lệ hỏng
        $totalDefects = $query->sum('quantity');
        $totalStock = WarehouseStock::sum('on_hand');
        $defectRate = $totalStock > 0 ? round(($totalDefects / $totalStock) * 100, 2) : 0;

        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();

        return view('admin.reports.defect-report', compact('defects', 'defectRate', 'warehouses'));
    }

    // 1.5. Báo cáo Kiểm kê
    public function countReport(Request $request)
    {
        $query = CountRequest::with(['warehouse', 'variant.product', 'createdBy', 'countedBy'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from && $request->date_to) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }

        $counts = $query->paginate(20);
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();

        return view('admin.reports.count-report', compact('counts', 'warehouses'));
    }

    // 3.1. Báo cáo Sản phẩm Bán chạy
    public function topSelling(Request $request)
    {
        $days = $request->days ?? 30;
        $limit = $request->limit ?? 20;

        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', now()->subDays($days))
            ->select(
                'products.name',
                'products.sku',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.line_total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();

        return view('admin.reports.top-selling', compact('topProducts', 'days', 'limit'));
    }

    // 3.2. Báo cáo Sản phẩm Tồn đọng
    public function slowMoving(Request $request)
    {
        $days = $request->days ?? 90;

        $slowMoving = DB::table('warehouse_stocks')
            ->join('product_variants', 'warehouse_stocks.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('order_items', function($join) use ($days) {
                $join->on('order_items.variant_id', '=', 'warehouse_stocks.variant_id')
                     ->where('order_items.created_at', '>=', now()->subDays($days));
            })
            ->where('warehouse_stocks.on_hand', '>', 0)
            ->select(
                'products.name',
                'products.sku',
                'warehouse_stocks.on_hand',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as sold_qty'),
                DB::raw('DATEDIFF(NOW(), warehouse_stocks.updated_at) as days_in_stock'),
                DB::raw('CASE 
                    WHEN warehouse_stocks.on_hand > 0 AND COALESCE(SUM(order_items.quantity), 0) > 0 
                    THEN ROUND(COALESCE(SUM(order_items.quantity), 0) / warehouse_stocks.on_hand, 2)
                    ELSE 0 
                END as turnover_rate')
            )
            ->groupBy('warehouse_stocks.id', 'products.name', 'products.sku', 'warehouse_stocks.on_hand', 'warehouse_stocks.updated_at')
            ->having('days_in_stock', '>', $days)
            ->orHaving('turnover_rate', '<', 0.1)
            ->orderBy('days_in_stock', 'desc')
            ->paginate(20);

        return view('admin.reports.slow-moving', compact('slowMoving', 'days'));
    }

    // Export báo cáo
    public function export(Request $request, $type)
    {
        $format = $request->get('export'); // excel hoặc pdf
        
        switch ($type) {
            case 'current-stock':
                return $this->exportCurrentStock($request, $format);
            case 'inventory-logs':
                return $this->exportInventoryLogs($request, $format);
            case 'defect-report':
                return $this->exportDefectReport($request, $format);
            default:
                return back()->with('error', 'Loại báo cáo không hỗ trợ');
        }
    }

    private function exportCurrentStock($request, $format)
    {
        $query = WarehouseStock::with(['warehouse', 'variant.product'])
            ->where('on_hand', '>', 0);

        // Apply filters
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->search) {
            $query->whereHas('variant.product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $stocks = $query->get();
        $filename = 'ton-kho-hien-tai-' . date('Y-m-d');

        if ($format === 'excel') {
            return $this->exportToExcel($stocks, $filename, 'current-stock');
        } else {
            return $this->exportToPDF($stocks, $filename, 'current-stock');
        }
    }

    private function exportInventoryLogs($request, $format)
    {
        $query = InventoryLog::with(['warehouse', 'variant.product', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->action) {
            $query->where('action', $request->action);
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(1000)->get(); // Giới hạn 1000 records
        $filename = 'lich-su-giao-dich-' . date('Y-m-d');

        if ($format === 'excel') {
            return $this->exportToExcel($logs, $filename, 'inventory-logs');
        } else {
            return $this->exportToPDF($logs, $filename, 'inventory-logs');
        }
    }

    private function exportToExcel($data, $filename, $type)
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
        ];

        $content = $this->generateExcelContent($data, $type);
        
        return response($content, 200, $headers);
    }

    private function exportToPDF($data, $filename, $type)
    {
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"',
        ];

        // Sử dụng view để tạo PDF content
        $html = view('admin.reports.exports.' . $type, compact('data'))->render();
        
        // Nếu có thư viện PDF như DomPDF
        // $pdf = PDF::loadHTML($html);
        // return $pdf->download($filename . '.pdf');
        
        // Tạm thời return HTML
        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    private function generateExcelContent($data, $type)
    {
        $content = "<table border='1'>";
        
        if ($type === 'current-stock') {
            $content .= "<tr><th>SKU</th><th>Sản phẩm</th><th>Kho</th><th>Tổng tồn</th><th>Sẵn sàng</th><th>Đã đặt</th><th>Chờ QC</th><th>Hỏng</th></tr>";
            foreach ($data as $item) {
                $content .= "<tr>";
                $content .= "<td>" . $item->variant->product->sku . "</td>";
                $content .= "<td>" . $item->variant->product->name . "</td>";
                $content .= "<td>" . $item->warehouse->name . "</td>";
                $content .= "<td>" . number_format($item->on_hand) . "</td>";
                $content .= "<td>" . number_format($item->available) . "</td>";
                $content .= "<td>" . number_format($item->reserved) . "</td>";
                $content .= "<td>" . number_format($item->quarantine) . "</td>";
                $content .= "<td>" . number_format($item->damaged) . "</td>";
                $content .= "</tr>";
            }
        } elseif ($type === 'inventory-logs') {
            $content .= "<tr><th>Thời gian</th><th>Loại GD</th><th>Sản phẩm</th><th>Kho</th><th>SL Trước</th><th>Thay đổi</th><th>SL Sau</th><th>Người thực hiện</th><th>Ghi chú</th></tr>";
            foreach ($data as $item) {
                $content .= "<tr>";
                $content .= "<td>" . $item->created_at->format('d/m/Y H:i') . "</td>";
                $content .= "<td>" . $item->action . "</td>";
                $content .= "<td>" . ($item->variant->product->name ?? 'N/A') . "</td>";
                $content .= "<td>" . ($item->warehouse->name ?? 'N/A') . "</td>";
                $content .= "<td>" . number_format($item->quantity_before) . "</td>";
                $content .= "<td>" . number_format($item->quantity_change) . "</td>";
                $content .= "<td>" . number_format($item->quantity_after) . "</td>";
                $content .= "<td>" . ($item->user->name ?? 'System') . "</td>";
                $content .= "<td>" . $item->notes . "</td>";
                $content .= "</tr>";
            }
        }
        
        $content .= "</table>";
        return $content;
    }

    // Helper method
    private function getDateRange($period)
    {
        switch ($period) {
            case 'week':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'month':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'quarter':
                return [now()->startOfQuarter(), now()->endOfQuarter()];
            case 'year':
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return [now()->startOfMonth(), now()->endOfMonth()];
        }
    }
}