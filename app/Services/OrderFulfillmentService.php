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
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Xác nhận đơn hàng - Reserve tồn kho
     */
    public function confirmOrder(Order $order, int $warehouseId)
    {
        DB::transaction(function () use ($order, $warehouseId) {
            // 1. Kiểm tra tồn kho
            foreach ($order->items as $item) {
                $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                    ->where('variant_id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();
                
                if (!$stock || $stock->available < $item->quantity) {
                    // Thông báo không đủ tồn kho
                    $this->notificationService->notifyInsufficientStock(
                        $order, 
                        $item->variant, 
                        $item->quantity, 
                        $stock ? $stock->available : 0
                    );
                    throw new \Exception("Không đủ tồn kho cho sản phẩm: {$item->variant->name}");
                }
            }
            
            // 2. Reserve tồn kho
            foreach ($order->items as $item) {
                $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                    ->where('variant_id', $item->variant_id)
                    ->first();
                
                $availableBefore = $stock->available;
                
                $stock->update([
                    'available' => $stock->available - $item->quantity,
                    'reserved' => $stock->reserved + $item->quantity,
                ]);
                
                // 3. Ghi log
                InventoryLog::create([
                    'warehouse_id' => $warehouseId,
                    'variant_id' => $item->variant_id,
                    'action' => 'OUT',
                    'quantity_before' => $availableBefore,
                    'quantity_change' => -$item->quantity,
                    'quantity_after' => $stock->available,
                    'reference_type' => 'order',
                    'reference_id' => (string)$order->id,
                    'user_id' => Auth::id(),
                    'notes' => "Reserve cho đơn hàng #{$order->code}",
                ]);
                
                // Kiểm tra tồn kho thấp sau khi reserve
                $threshold = $this->notificationService->getLowStockThreshold();
                if ($stock->available < $threshold) {
                    $this->notificationService->notifyLowStock(
                        $item->variant,
                        $stock->warehouse,
                        $stock->available
                    );
                }
            }
            
            // 4. Cập nhật đơn hàng
            $order->update([
                'fulfillment_status' => 'CONFIRMED',
                'status' => 'processing'
            ]);
            
            // 5. Tạo picking
            OrderPicking::create([
                'order_id' => $order->id,
                'warehouse_id' => $warehouseId,
                'status' => 'PENDING',
            ]);
        });
    }

    public function startPicking(Order $order, int $warehouseId)
    {
        DB::transaction(function () use ($order, $warehouseId) {
            $picking = OrderPicking::where('order_id', $order->id)
                ->where('warehouse_id', $warehouseId)
                ->firstOrFail();
            
            $picking->update([
                'status' => 'PICKING',
                'started_at' => now(),
                'assigned_to' => Auth::id(),
            ]);
            
            $order->update(['fulfillment_status' => 'PICKING']);
        });
    }

    public function completePacking(OrderPicking $picking)
    {
        DB::transaction(function () use ($picking) {
            $picking->update([
                'status' => 'PACKED',
                'completed_at' => now(),
            ]);
            
            $picking->order->update(['fulfillment_status' => 'PACKED']);
        });
    }

    /**
     * Giao hàng - Trừ tồn kho thực tế
     */
    public function shipOrder(Order $order)
    {
        DB::transaction(function () use ($order) {
            $picking = $order->picking()->first();
            
            if (!$picking) {
                // Tự động tạo picking nếu chưa có
                $picking = OrderPicking::create([
                    'order_id' => $order->id,
                    'warehouse_id' => 1, // Default warehouse
                    'status' => 'READY_TO_SHIP',
                ]);
            }
            
            // 1. Trừ tồn kho thực tế
            foreach ($order->items as $item) {
                $stock = WarehouseStock::where('warehouse_id', $picking->warehouse_id)
                    ->where('variant_id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();
                
                if (!$stock) {
                    throw new \Exception("Không tìm thấy tồn kho cho sản phẩm: {$item->variant->name}");
                }
                
                // Kiểm tra tồn kho available thay vì reserved
                if ($stock->available < $item->quantity) {
                    throw new \Exception("Không đủ tồn kho cho sản phẩm: {$item->variant->name}");
                }
                
                $onHandBefore = $stock->on_hand;
                
                // Nếu có reserved thì trừ reserved, không thì trừ available
                if ($stock->reserved >= $item->quantity) {
                    $stock->update([
                        'on_hand' => $stock->on_hand - $item->quantity,
                        'reserved' => $stock->reserved - $item->quantity,
                    ]);
                } else {
                    $stock->update([
                        'on_hand' => $stock->on_hand - $item->quantity,
                        'available' => $stock->available - $item->quantity,
                    ]);
                }
                
                // 2. Ghi log
                InventoryLog::create([
                    'warehouse_id' => $picking->warehouse_id,
                    'variant_id' => $item->variant_id,
                    'action' => 'OUT',
                    'quantity_before' => $onHandBefore,
                    'quantity_change' => -$item->quantity,
                    'quantity_after' => $stock->on_hand,
                    'reference_type' => 'order',
                    'reference_id' => (string)$order->id,
                    'user_id' => Auth::id(),
                    'notes' => "Xuất kho cho đơn hàng #{$order->code}",
                ]);
            }
            
            // 3. Cập nhật đơn hàng
            $order->update([
                'fulfillment_status' => 'SHIPPED',
                'status' => 'completed'
            ]);
            
            // 4. Cập nhật picking
            $picking->update(['status' => 'READY_TO_SHIP']);
        });
    }
    
    /**
     * Hủy đơn hàng - Hoàn tồn kho
     */
    public function cancelOrder(Order $order)
    {
        DB::transaction(function () use ($order) {
            // Chỉ hoàn nếu đã confirm (đã reserve)
            if ($order->status === 'processing') {
                $picking = $order->picking()->first();
                
                if ($picking) {
                    foreach ($order->items as $item) {
                        $stock = WarehouseStock::where('warehouse_id', $picking->warehouse_id)
                            ->where('variant_id', $item->variant_id)
                            ->lockForUpdate()
                            ->first();
                        
                        if ($stock) {
                            $availableBefore = $stock->available;
                            
                            $stock->update([
                                'available' => $stock->available + $item->quantity,
                                'reserved' => $stock->reserved - $item->quantity,
                            ]);
                            
                            // Ghi log
                            InventoryLog::create([
                                'warehouse_id' => $picking->warehouse_id,
                                'variant_id' => $item->variant_id,
                                'action' => 'ADJUSTMENT',
                                'quantity_before' => $availableBefore,
                                'quantity_change' => $item->quantity,
                                'quantity_after' => $stock->available,
                                'reference_type' => 'order_cancel',
                                'reference_id' => (string)$order->id,
                                'user_id' => Auth::id(),
                                'notes' => "Hoàn tồn kho do hủy đơn #{$order->code}",
                            ]);
                        }
                    }
                }
            }
            
            // Cập nhật đơn hàng
            $order->update([
                'status' => 'cancelled',
                'fulfillment_status' => 'CANCELLED'
            ]);
            
            // Cập nhật picking
            if ($picking = $order->picking()->first()) {
                $picking->update(['status' => 'CANCELLED']);
            }
        });
    }
}