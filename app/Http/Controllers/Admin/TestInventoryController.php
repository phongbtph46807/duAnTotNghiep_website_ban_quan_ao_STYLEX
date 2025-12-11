<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockInRequest;
use App\Models\StockOutRequest;
use App\Models\TransferRequest;
use App\Models\CountRequest;
use App\Models\DefectAssessment;
use App\Models\WarehouseQcResult;

class TestInventoryController extends Controller
{
    public function testAllTransactions()
    {
        $debug = [];
        
        // 1. Stock In với QC workflow
        $stockIns = StockInRequest::with(['latestQcResult.qcBy', 'variant.product'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        $debug['stock_in'] = $stockIns->map(function($request) {
            $qcResult = $request->latestQcResult;
            return [
                'id' => $request->id,
                'batch_number' => $request->batch_number,
                'status' => $request->status,
                'has_qc_result' => $qcResult ? 'Yes' : 'No',
                'qc_by' => $qcResult?->qcBy?->name ?? 'N/A',
                'passed_qty' => $qcResult?->passed_qty ?? 0,
                'failed_qty' => $qcResult?->failed_qty ?? 0,
                'qc_notes' => $qcResult?->qc_notes ?? 'N/A',
            ];
        });
        
        // 2. Stock Out với QC workflow
        $stockOuts = StockOutRequest::with(['latestQcResult.qcBy', 'variant.product'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        $debug['stock_out'] = $stockOuts->map(function($request) {
            $qcResult = $request->latestQcResult;
            return [
                'id' => $request->id,
                'batch_number' => $request->batch_number,
                'status' => $request->status,
                'has_qc_result' => $qcResult ? 'Yes' : 'No',
                'passed_qty' => $qcResult?->passed_qty ?? 0,
                'failed_qty' => $qcResult?->failed_qty ?? 0,
            ];
        });
        
        // 3. Defect Assessment (bao gồm tự động tạo từ QC fail)
        $defects = \App\Models\DefectAssessment::with(['stockInRequest', 'variant.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $debug['defect_assessments'] = $defects->map(function($defect) {
            return [
                'id' => $defect->id,
                'status' => $defect->status,
                'defect_type' => $defect->defect_type ?? 'N/A',
                'quantity' => $defect->quantity,
                'from_stock_in' => $defect->stock_in_request_id ? 'Yes' : 'No',
                'batch_number' => $defect->stockInRequest?->batch_number ?? 'N/A',
                'product' => $defect->variant?->product?->name ?? 'N/A',
            ];
        });
        
        // 4. Warehouse Stocks status
        $warehouseStocks = \App\Models\WarehouseStock::with(['variant.product'])
            ->where(function($query) {
                $query->where('damaged', '>', 0)
                      ->orWhere('clearance', '>', 0);
            })
            ->limit(5)
            ->get();
            
        $debug['special_stocks'] = $warehouseStocks->map(function($stock) {
            return [
                'product' => $stock->variant?->product?->name ?? 'N/A',
                'on_hand' => $stock->on_hand,
                'available' => $stock->available,
                'damaged' => $stock->damaged,
                'clearance' => $stock->clearance ?? 0,
                'quarantine' => $stock->quarantine,
            ];
        });
        
        // Summary
        $debug['workflow_summary'] = [
            'total_qc_results' => WarehouseQcResult::count(),
            'qc_with_failures' => WarehouseQcResult::where('failed_qty', '>', 0)->count(),
            'auto_created_defects' => \App\Models\DefectAssessment::whereNotNull('stock_in_request_id')->count(),
            'total_damaged_items' => \App\Models\WarehouseStock::sum('damaged'),
            'total_clearance_items' => \App\Models\WarehouseStock::sum('clearance'),
            'pending_defect_assessments' => \App\Models\DefectAssessment::where('status', 'PENDING')->count(),
            'b_grade_assessments' => \App\Models\DefectAssessment::where('classification', 'B-GRADE')->count(),
        ];
        
        return response()->json($debug);
    }
}