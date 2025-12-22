<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPicking;
use App\Models\WarehouseStock;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderFulfillmentService
{
    public function confirmOrder(Order $order)
    {
        DB::transaction(function () use ($order) {
            $picking = $order->picking;
            if (!$picking) {
                throw new \Exception('Chưa chọn kho');
            }

            if ($picking->status !== 'PENDING') {
                throw new \Exception('Đơn hàng đã được xác nhận');
            }

            $warehouseId = $picking->warehouse_id;
            $totalCost = 0;

            // Kiểm tra và trừ tồn kho
            foreach ($order->items as $item) {
                $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                    ->where('variant_id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->on_hand < $item->quantity) {
                    throw new \Exception("Không đủ tồn kho: {$item->variant->name}");
                }

                $totalCost += ((int)$item->variant->cost_price ?? 0) * $item->quantity;

                $stock->update([
                    'on_hand' => $stock->on_hand - $item->quantity,
                    'available' => $stock->available - $item->quantity,
                ]);

                InventoryLog::create([
                    'warehouse_id' => $warehouseId,
                    'variant_id' => $item->variant_id,
                    'action' => 'RESERVE',
                    'quantity_before' => $stock->on_hand + $item->quantity,
                    'quantity_change' => -$item->quantity,
                    'quantity_after' => $stock->on_hand,
                    'reference_type' => 'order',
                    'reference_id' => (string)$order->id,
                    'user_id' => Auth::id(),
                    'notes' => "Reserve #{$order->code}",
                ]);
            }

            $picking->update(['status' => 'CONFIRMED']);
            $order->update([
                'status' => 'processing',
                'total_cost' => $totalCost,
            ]);
        });
    }

    public function completePacking(OrderPicking $picking)
    {
        if ($picking->status !== 'CONFIRMED') {
            throw new \Exception('Không thể đóng gói');
        }
        $picking->update(['status' => 'PACKED']);
    }

    public function completeShipping(Order $order)
    {
        if ($order->status !== 'processing') {
            throw new \Exception('Không thể cập nhật trạng thái giao hàng');
        }
        $order->update(['status' => 'completed']);
    }

    public function updateOrderStatus(Order $order, $newStatus)
    {
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => ['returned'],
            'cancelled' => [],
            'returned' => [],
        ];

        $currentStatus = $order->status;
        $allowed = $validTransitions[$currentStatus] ?? [];

        if (!in_array($newStatus, $allowed)) {
            throw new \Exception("Không thể chuyển từ {$currentStatus} sang {$newStatus}");
        }

        $order->update(['status' => $newStatus]);
    }

    public function cancelOrder(Order $order, $returnStatus = false)
    {
        DB::transaction(function () use ($order, $returnStatus) {
            $picking = $order->picking;

            if ($picking && in_array($picking->status, ['CONFIRMED', 'PICKING', 'PACKED'])) {
                foreach ($order->items as $item) {
                    $stock = WarehouseStock::where('warehouse_id', $picking->warehouse_id)
                        ->where('variant_id', $item->variant_id)
                        ->lockForUpdate()
                        ->first();

                    if ($stock) {
                        $stock->update([
                            'on_hand' => $stock->on_hand + $item->quantity,
                            'available' => $stock->available + $item->quantity,
                        ]);

                        InventoryLog::create([
                            'warehouse_id' => $picking->warehouse_id,
                            'variant_id' => $item->variant_id,
                            'action' => 'ADJUSTMENT',
                            'quantity_before' => $stock->on_hand - $item->quantity,
                            'quantity_change' => $item->quantity,
                            'quantity_after' => $stock->on_hand,
                            'reference_type' => 'order_cancel',
                            'reference_id' => (string)$order->id,
                            'user_id' => Auth::id(),
                            'notes' => "Hoàn #{$order->code}",
                        ]);
                    }
                }
            }

            $order->update([
                'status' => $returnStatus ? 'returned' : 'cancelled',
            ]);

            if ($picking) {
                $picking->update(['status' => 'CANCELLED']);
            }
        });
    }
}
