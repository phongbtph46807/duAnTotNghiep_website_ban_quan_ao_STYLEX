<?php

namespace App\Services;

use App\Models\WarehouseStock;
use App\Models\InventoryLog;
use App\Models\TransferRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    public static function confirmTransferOut(int $requestId): void
    {
        DB::transaction(function () use ($requestId) {
            $request = TransferRequest::findOrFail($requestId);
            
            if ($request->status !== 'PENDING') {
                throw new \Exception('Chi co the xac nhan xuat chuyen kho khi o trang thai PENDING');
            }

            $stock = WarehouseStock::where('warehouse_id', $request->from_warehouse_id)
                ->where('variant_id', $request->variant_id)
                ->where('batch_number', $request->batch_number)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                throw new \Exception('Khong tim thay lo hang trong kho');
            }

            if ($stock->available < $request->quantity) {
                throw new \Exception('Khong du ton kho. Co san: ' . $stock->available);
            }

            $stock->update([
                'available' => $stock->available - $request->quantity,
                'on_hand' => $stock->on_hand - $request->quantity,
            ]);

            $request->update(['status' => 'OUT_CONFIRMED', 'out_confirmed_by' => Auth::id()]);

            InventoryLog::create([
                'warehouse_id' => $request->from_warehouse_id,
                'variant_id' => $request->variant_id,
                'action' => 'TRANSFER',
                'quantity_before' => $stock->on_hand + $request->quantity,
                'quantity_change' => -$request->quantity,
                'quantity_after' => $stock->on_hand,
                'reference_type' => 'transfer',
                'reference_id' => $requestId,
                'user_id' => Auth::id(),
                'notes' => "Xuat chuyen kho - Lo {$request->batch_number}",
            ]);
        });
    }

    public static function confirmTransferIn(int $requestId): void
    {
        DB::transaction(function () use ($requestId) {
            $request = TransferRequest::findOrFail($requestId);
            
            if ($request->status !== 'OUT_CONFIRMED') {
                throw new \Exception('Chi co the xac nhan nhap chuyen kho khi da xac nhan xuat');
            }

            $toStock = WarehouseStock::firstOrCreate(
                [
                    'warehouse_id' => $request->to_warehouse_id,
                    'variant_id' => $request->variant_id,
                    'batch_number' => $request->batch_number
                ],
                [
                    'on_hand' => 0,
                    'available' => 0,
                    'reserved' => 0,
                    'quarantine' => 0,
                    'damaged' => 0
                ]
            );

            $toStock->update([
                'on_hand' => $toStock->on_hand + $request->quantity,
                'quarantine' => $toStock->quarantine + $request->quantity,
            ]);

            $request->update(['status' => 'QC_CHECKING', 'in_confirmed_by' => Auth::id()]);

            InventoryLog::create([
                'warehouse_id' => $request->to_warehouse_id,
                'variant_id' => $request->variant_id,
                'action' => 'TRANSFER',
                'quantity_before' => $toStock->on_hand - $request->quantity,
                'quantity_change' => $request->quantity,
                'quantity_after' => $toStock->on_hand,
                'reference_type' => 'transfer',
                'reference_id' => $requestId,
                'user_id' => Auth::id(),
                'notes' => "Nhap chuyen kho vao QUARANTINE - Lo {$request->batch_number}",
            ]);
        });
    }

    public static function confirmTransferQC(int $requestId, int $qcPassedQty, int $qcFailedQty): void
    {
        DB::transaction(function () use ($requestId, $qcPassedQty, $qcFailedQty) {
            $request = TransferRequest::findOrFail($requestId);
            
            if ($request->status !== 'QC_CHECKING') {
                throw new \Exception('Chi co the QC chuyen kho khi o trang thai QC_CHECKING');
            }

            if ($qcPassedQty + $qcFailedQty !== $request->quantity) {
                throw new \Exception('Tong so luong QC khong khop. Yeu cau: ' . $request->quantity . ', Nhan: ' . ($qcPassedQty + $qcFailedQty));
            }

            $stock = WarehouseStock::where('warehouse_id', $request->to_warehouse_id)
                ->where('variant_id', $request->variant_id)
                ->where('batch_number', $request->batch_number)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->quarantine < $request->quantity) {
                throw new \Exception('Khong tim thay lo hang hoac so luong quarantine khong khop');
            }

            $quantityBefore = $stock->on_hand;
            $stock->update([
                'quarantine' => $stock->quarantine - $request->quantity,
                'available' => $stock->available + $qcPassedQty,
                'damaged' => $stock->damaged + $qcFailedQty,
                'on_hand' => $stock->on_hand - $qcFailedQty,
            ]);

            $request->update([
                'status' => 'COMPLETED',
                'qc_confirmed_by' => Auth::id()
            ]);

            // Chỉ ghi log nếu có hàng fail
            if ($qcFailedQty > 0) {
                InventoryLog::create([
                    'warehouse_id' => $request->to_warehouse_id,
                    'variant_id' => $request->variant_id,
                    'action' => 'ADJUSTMENT',
                    'quantity_before' => $quantityBefore,
                    'quantity_change' => -$qcFailedQty,
                    'quantity_after' => $stock->on_hand,
                    'reference_type' => 'transfer_qc',
                    'reference_id' => $requestId,
                    'user_id' => Auth::id(),
                    'notes' => "QC chuyen kho - Fail: {$qcFailedQty}",
                ]);
            }
        });
    }
}
