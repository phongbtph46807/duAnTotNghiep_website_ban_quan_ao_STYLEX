<?php

namespace App\Http\Controllers;

use App\Models\{Setting, ProductVariant, WarehouseStock};
use Illuminate\Support\Facades\DB;

class TestLowStockController extends Controller
{
    public function test()
    {
        $lowStockThreshold = (int) Setting::where('key', 'low_stock_threshold')->value('value') ?? 10;
        
        // Kiểm tra có bảng warehouse_stocks không
        $hasTable = DB::getSchemaBuilder()->hasTable('warehouse_stocks');
        
        // Đếm tổng số variant
        $totalVariants = ProductVariant::count();
        
        // Đếm số variant có tồn kho
        $variantsWithStock = DB::table('warehouse_stocks')
            ->select('variant_id')
            ->groupBy('variant_id')
            ->havingRaw('SUM(on_hand) > 0')
            ->count();
        
        // Lấy danh sách variant có tồn kho thấp
        $lowStockVariants = DB::table('warehouse_stocks as ws')
            ->join('product_variants as pv', 'ws.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->select('pv.id', 'pv.sku', 'p.name as product_name', 
                    DB::raw('SUM(ws.on_hand) as total_stock'))
            ->groupBy('pv.id', 'pv.sku', 'p.name')
            ->havingRaw('SUM(ws.on_hand) > 0 AND SUM(ws.on_hand) <= ?', [$lowStockThreshold])
            ->orderBy('total_stock', 'asc')
            ->get();
        
        // Lấy một số mẫu dữ liệu warehouse_stocks
        $sampleStocks = WarehouseStock::with(['variant.product', 'warehouse'])
            ->where('on_hand', '>', 0)
            ->limit(10)
            ->get();
        
        return response()->json([
            'low_stock_threshold' => $lowStockThreshold,
            'has_warehouse_stocks_table' => $hasTable,
            'total_variants' => $totalVariants,
            'variants_with_stock' => $variantsWithStock,
            'low_stock_count' => $lowStockVariants->count(),
            'low_stock_variants' => $lowStockVariants,
            'sample_stocks' => $sampleStocks->map(function($stock) {
                return [
                    'warehouse' => $stock->warehouse->name ?? 'N/A',
                    'sku' => $stock->variant->sku ?? 'N/A',
                    'product' => $stock->variant->product->name ?? 'N/A',
                    'on_hand' => $stock->on_hand,
                    'available' => $stock->available,
                ];
            })
        ]);
    }
}