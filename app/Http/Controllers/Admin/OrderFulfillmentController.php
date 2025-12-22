<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPicking;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderFulfillmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.variant.product', 'items.variant.color', 'items.variant.size', 'picking.warehouse')
            ->whereIn('status', ['processing', 'pending', 'shipping', 'delivered'])
            ->where(function ($q) {
                $q->whereDoesntHave('picking')
                  ->orWhereHas('picking', function ($p) {
                      $p->whereIn('status', ['PENDING', 'CONFIRMED', 'PICKING', 'PACKED', 'SHIPPED']);
                  });
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                  ->orWhere('buyer_name', 'like', "%$search%")
                  ->orWhere('buyer_phone', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('picking', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        if ($request->filled('warehouse')) {
            $query->whereHas('picking', function ($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse);
            });
        }

        $orders = $query->latest()->paginate(20);
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();

        return view('admin.orders.fulfillment.index', compact('orders', 'warehouses'));
    }

    public function confirm(Request $request, Order $order)
    {
        try {
            if ($order->picking && $order->picking->status !== 'PENDING') {
                throw new \Exception('Đơn hàng đã được chọn kho');
            }

            $validated = $request->validate(['warehouse_id' => 'required|integer|exists:warehouses,id']);

            DB::transaction(function () use ($order, $validated) {
                $warehouseId = $validated['warehouse_id'];
                $totalCost = 0;

                foreach ($order->items as $item) {
                    $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                        ->where('variant_id', $item->variant_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$stock || $stock->available < $item->quantity) {
                        throw new \Exception("Không đủ tồn kho: {$item->variant->name}");
                    }

                    $totalCost += ((int)$item->variant->cost_price ?? 0) * $item->quantity;

                    $availableBefore = $stock->available;
                    $stock->update([
                        'available' => $stock->available - $item->quantity,
                        'reserved' => $stock->reserved + $item->quantity,
                    ]);
                    $stock->syncOnHand();

                    InventoryLog::create([
                        'warehouse_id' => $warehouseId,
                        'variant_id' => $item->variant_id,
                        'action' => 'RESERVE',
                        'quantity_before' => $availableBefore,
                        'quantity_change' => -$item->quantity,
                        'quantity_after' => $stock->available,
                        'reference_type' => 'order',
                        'reference_id' => (string)$order->id,
                        'user_id' => Auth::id(),
                        'notes' => "Reserve #{$order->code}",
                    ]);
                }

                if (!$order->picking) {
                    $order->picking()->create([
                        'order_id' => $order->id,
                        'status' => 'CONFIRMED',
                        'warehouse_id' => $warehouseId,
                    ]);
                } else {
                    $order->picking->update([
                        'status' => 'CONFIRMED',
                        'warehouse_id' => $warehouseId,
                    ]);
                }

                $order->update([
                    'status' => 'processing',
                    'total_cost' => $totalCost,
                    'updated_by' => Auth::id(),
                ]);
            });

            return back()->with('success', 'Xác nhận đơn hàng thành công.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load('items.variant.product', 'items.variant.color', 'items.variant.size', 'picking.warehouse');
        return view('admin.orders.fulfillment.show', compact('order'));
    }

    public function completePacking(OrderPicking $picking)
    {
        try {
            $order = $picking->order;
            if ($order->status !== 'processing') {
                throw new \Exception('Đơn hàng không ở trạng thái xử lý');
            }
            if ($picking->status !== 'CONFIRMED') {
                throw new \Exception('Picking chưa được xác nhận');
            }

            DB::transaction(function () use ($picking, $order) {
                $warehouseId = $picking->warehouse_id;

                foreach ($order->items as $item) {
                    $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                        ->where('variant_id', $item->variant_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$stock || $stock->reserved < $item->quantity) {
                        throw new \Exception('Số lượng reserved không đủ');
                    }

                    $reservedBefore = $stock->reserved;
                    $stock->update([
                        'reserved' => $stock->reserved - $item->quantity,
                        'quarantine' => $stock->quarantine + $item->quantity,
                    ]);
                    $stock->syncOnHand();

                    InventoryLog::create([
                        'warehouse_id' => $warehouseId,
                        'variant_id' => $item->variant_id,
                        'action' => 'ADJUSTMENT',
                        'quantity_before' => $reservedBefore,
                        'quantity_change' => -$item->quantity,
                        'quantity_after' => $stock->reserved,
                        'reference_type' => 'order_packing',
                        'reference_id' => (string)$order->id,
                        'user_id' => Auth::id(),
                        'notes' => "Đóng gói #{$order->code}",
                    ]);
                }

                $picking->update(['status' => 'PACKED']);
                $order->update(['status' => 'shipping', 'updated_by' => Auth::id()]);
            });

            return redirect()->route('admin.orders.fulfillment.show', $picking->order)
                ->with('success', 'Đóng gói thành công.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function completeShipping(Order $order)
    {
        try {
            if ($order->status !== 'shipping') {
                throw new \Exception('Đơn hàng không ở trạng thái giao hàng');
            }

            $picking = $order->picking;
            if (!$picking || $picking->status !== 'PACKED') {
                throw new \Exception('Picking chưa được đóng gói');
            }

            DB::transaction(function () use ($order, $picking) {
                $warehouseId = $picking->warehouse_id;

                foreach ($order->items as $item) {
                    $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                        ->where('variant_id', $item->variant_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$stock || $stock->quarantine < $item->quantity) {
                        throw new \Exception('Số lượng quarantine không đủ');
                    }

                    $quarantineBefore = $stock->quarantine;
                    $stock->decrement('quarantine', $item->quantity);
                    $stock->syncOnHand();

                    InventoryLog::create([
                        'warehouse_id' => $warehouseId,
                        'variant_id' => $item->variant_id,
                        'action' => 'OUT',
                        'quantity_before' => $quarantineBefore,
                        'quantity_change' => -$item->quantity,
                        'quantity_after' => $stock->quarantine,
                        'reference_type' => 'order_shipping',
                        'reference_id' => (string)$order->id,
                        'user_id' => Auth::id(),
                        'notes' => "Giao hàng #{$order->code}",
                    ]);
                }

                $picking->update(['status' => 'SHIPPED']);
                $order->update([
                    'status' => 'delivered',
                    'payment_status' => 'paid',
                    'updated_by' => Auth::id()
                ]);
            });

            return back()->with('success', 'Cập nhật giao hàng thành công.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
