<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\BatchMovement;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $negativeStock = $this->getNegativeStockReport();
        $cogsErrors = $this->getCOGSErrors();
        $failedTransactions = $this->getFailedTransactions();
        $performance = $this->getPerformanceMetrics();
        $batchAnalysis = $this->getBatchAnalysis();
        $auditTrail = $this->getAuditTrail();
        
        return view('admin.reports.index', compact(
            'negativeStock', 'cogsErrors', 'failedTransactions', 
            'performance', 'batchAnalysis', 'auditTrail'
        ));
    }

    private function getNegativeStockReport()
    {
        return DB::table('products')
            ->select('sku', 'name', 'total_stock', 'updated_at', 
                DB::raw('total_stock * COALESCE(cost_price, price * 0.6) as negative_value'))
            ->where('total_stock', '<', 0)
            ->orderBy('total_stock', 'asc')
            ->get();
    }

    private function getCOGSErrors()
    {
        return DB::table('batch_movements as bm')
            ->join('product_batches as pb', 'bm.batch_id', '=', 'pb.id')
            ->join('products as p', 'pb.variant_id', '=', 'p.id')
            ->select('bm.id', 'p.sku', 'bm.quantity', 'bm.created_at', 'bm.notes')
            ->where('bm.type', 'OUT')
            ->where(function($query) {
                $query->whereNull('pb.cost_price')
                      ->orWhere('pb.cost_price', '=', 0);
            })
            ->orderBy('bm.created_at', 'desc')
            ->limit(50)
            ->get();
    }

    private function getFailedTransactions()
    {
        $totalAttempts = BatchMovement::where('type', 'OUT')->count();
        $failedCount = BatchMovement::where('type', 'OUT')
            ->where('notes', 'like', '%error%')
            ->orWhere('notes', 'like', '%failed%')
            ->count();
            
        return [
            'total_attempts' => $totalAttempts,
            'failed_count' => $failedCount,
            'success_rate' => $totalAttempts > 0 ? round((($totalAttempts - $failedCount) / $totalAttempts) * 100, 2) : 100,
            'recent_failures' => BatchMovement::where('type', 'OUT')
                ->where(function($query) {
                    $query->where('notes', 'like', '%error%')
                          ->orWhere('notes', 'like', '%failed%');
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    private function getPerformanceMetrics()
    {
        $avgProcessingTime = DB::table('batch_movements')
            ->where('type', 'OUT')
            ->where('created_at', '>=', now()->subDays(7))
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, created_at, updated_at)'));

        $realtimeRate = DB::table('batch_movements')
            ->where('type', 'OUT')
            ->where('created_at', '>=', now()->subDays(7))
            ->where(DB::raw('TIMESTAMPDIFF(SECOND, created_at, updated_at)'), '<=', 3)
            ->count();

        $totalTransactions = DB::table('batch_movements')
            ->where('type', 'OUT')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return [
            'avg_processing_time' => round($avgProcessingTime ?? 0, 2),
            'realtime_rate' => $totalTransactions > 0 ? round(($realtimeRate / $totalTransactions) * 100, 2) : 0,
            'total_transactions_week' => $totalTransactions
        ];
    }

    private function getBatchAnalysis()
    {
        $fifoTransactions = DB::table('batch_movements')
            ->where('type', 'OUT')
            ->where('notes', 'like', '%FIFO%')
            ->count();

        $totalOutTransactions = DB::table('batch_movements')
            ->where('type', 'OUT')
            ->count();

        $emptyBatches = DB::table('product_batches')
            ->where('quantity', '=', 0)
            ->count();

        $totalBatches = DB::table('product_batches')->count();

        return [
            'fifo_usage_rate' => $totalOutTransactions > 0 ? round(($fifoTransactions / $totalOutTransactions) * 100, 2) : 0,
            'empty_batches' => $emptyBatches,
            'partial_batches' => $totalBatches - $emptyBatches,
            'batch_depth_avg' => $this->calculateBatchDepth()
        ];
    }

    private function calculateBatchDepth()
    {
        $result = DB::table('batch_movements')
            ->select(DB::raw('AVG(batch_count) as avg_depth'))
            ->fromSub(function ($query) {
                $query->select('reference_id', DB::raw('COUNT(DISTINCT batch_id) as batch_count'))
                    ->from('batch_movements')
                    ->where('type', 'OUT')
                    ->whereNotNull('reference_id')
                    ->groupBy('reference_id');
            }, 'sub')
            ->first();
            
        return round($result->avg_depth ?? 0, 1);
    }

    private function getAuditTrail()
    {
        return DB::table('batch_movements as bm')
            ->join('product_batches as pb', 'bm.batch_id', '=', 'pb.id')
            ->join('products as p', 'pb.variant_id', '=', 'p.id')
            ->leftJoin('users as u', 'bm.user_id', '=', 'u.id')
            ->select(
                'p.sku', 'bm.quantity', 'pb.batch_number', 'pb.cost_price as unit_cost',
                'bm.reference_id', 'u.name as user_name', 'bm.created_at', 'bm.notes'
            )
            ->where('bm.type', 'OUT')
            ->orderBy('bm.created_at', 'desc')
            ->limit(100)
            ->get();
    }
}