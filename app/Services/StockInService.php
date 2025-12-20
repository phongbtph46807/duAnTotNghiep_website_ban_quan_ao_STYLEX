<?php

namespace App\Services;

use App\Models\StockInRequest;
use App\Models\WarehouseStock;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockInService
{
    public function createStockInRequest(array $data): StockInRequest
    {
        return DB::transaction(function () use ($data) {
            // Tạo stock in request
            $stockInRequest = StockInRequest::create(array_merge($data, [
                'status' => StockInRequest::STATUS_PENDING,
                'created_by' => Auth::id(),
            ]));

            // Cập nhật warehouse stock - thêm vào quarantine
            $this->updateWarehouseStock(
                $data['warehouse_id'],
                $data['variant_id'],
                $data['quantity'],
                'quarantine'
            );

            // Ghi inventory log
            $this->logInventoryAction($stockInRequest, 'STOCK_IN_CREATED');

            return $stockInRequest;
        });
    }

    public function performQc(StockInRequest $stockInRequest, array $qcData): StockInRequest
    {
        return DB::transaction(function () use ($stockInRequest, $qcData) {
            $stockInRequest->update([
                'qc_passed_qty' => $qcData['qc_passed_qty'],
                'qc_failed_qty' => $qcData['qc_failed_qty'],
                'status' => $qcData['qc_passed_qty'] > 0 ? 
                    StockInRequest::STATUS_QC_PASSED : 
                    StockInRequest::STATUS_QC_FAILED,
                'qc_by' => Auth::id(),
                'qc_at' => now(),
                'notes' => ($stockInRequest->notes ?? '') . "\n" . ($qcData['qc_notes'] ?? ''),
            ]);

            // Ghi log QC
            $this->logInventoryAction($stockInRequest, 'STOCK_IN_QC_COMPLETED');

            return $stockInRequest;
        });
    }

    public function confirmStockIn(StockInRequest $stockInRequest, array $confirmData = []): StockInRequest
    {
        return DB::transaction(function () use ($stockInRequest, $confirmData) {
            // Chuyển hàng từ quarantine sang available
            $this->moveStock(
                $stockInRequest->warehouse_id,
                $stockInRequest->variant_id,
                $stockInRequest->qc_passed_qty,
                'quarantine',
                'available'
            );

            // Nếu có hàng failed, chuyển sang damaged
            if ($stockInRequest->qc_failed_qty > 0) {
                $this->moveStock(
                    $stockInRequest->warehouse_id,
                    $stockInRequest->variant_id,
                    $stockInRequest->qc_failed_qty,
                    'quarantine',
                    'damaged'
                );
            }

            $stockInRequest->update([
                'status' => StockInRequest::STATUS_CONFIRMED,
                'confirmed_by' => Auth::id(),
                'confirmed_at' => now(),
                'notes' => ($stockInRequest->notes ?? '') . "\n" . ($confirmData['confirm_notes'] ?? ''),
            ]);

            $this->logInventoryAction($stockInRequest, 'STOCK_IN_CONFIRMED');

            return $stockInRequest;
        });
    }

    public function cancelStockIn(StockInRequest $stockInRequest, string $reason = ''): StockInRequest
    {
        return DB::transaction(function () use ($stockInRequest, $reason) {
            // Trừ lại số lượng từ quarantine
            $this->updateWarehouseStock(
                $stockInRequest->warehouse_id,
                $stockInRequest->variant_id,
                -$stockInRequest->quantity,
                'quarantine'
            );

            $stockInRequest->update([
                'status' => StockInRequest::STATUS_CANCELLED,
                'notes' => ($stockInRequest->notes ?? '') . "\nHủy: " . $reason,
            ]);

            $this->logInventoryAction($stockInRequest, 'STOCK_IN_CANCELLED');

            return $stockInRequest;
        });
    }

    private function updateWarehouseStock(int $warehouseId, int $variantId, int $quantity, string $type): void
    {
        $stock = WarehouseStock::firstOrCreate([
            'warehouse_id' => $warehouseId,
            'variant_id' => $variantId,
        ], [
            'on_hand' => 0,
            'available' => 0,
            'reserved' => 0,
            'quarantine' => 0,
            'damaged' => 0,
        ]);

        $stock->increment($type, $quantity);
        $stock->increment('on_hand', $quantity);
    }

    private function moveStock(int $warehouseId, int $variantId, int $quantity, string $from, string $to): void
    {
        $stock = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('variant_id', $variantId)
            ->first();

        if ($stock) {
            $stock->decrement($from, $quantity);
            $stock->increment($to, $quantity);
        }
    }

    private function logInventoryAction(StockInRequest $stockInRequest, string $action): void
    {
        InventoryLog::create([
            'warehouse_id' => $stockInRequest->warehouse_id,
            'variant_id' => $stockInRequest->variant_id,
            'action' => $action,
            'quantity' => $stockInRequest->quantity,
            'reference_type' => 'stock_in_request',
            'reference_id' => $stockInRequest->id,
            'user_id' => Auth::id(),
            'notes' => "Stock In Request #{$stockInRequest->id}",
        ]);
    }
}