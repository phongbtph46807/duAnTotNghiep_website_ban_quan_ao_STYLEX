<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPicking;
use App\Models\Warehouse;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\Request;

class OrderFulfillmentController extends Controller
{
    private $fulfillmentService;

    public function __construct(OrderFulfillmentService $fulfillmentService)
    {
        $this->fulfillmentService = $fulfillmentService;
    }

    public function index(Request $request)
    {
        $query = Order::with('items', 'picking')
            ->whereIn('status', ['processing', 'pending'])
            ->whereHas('picking', function ($q) {
                $q->whereIn('status', ['PENDING', 'CONFIRMED', 'PACKED']);
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
            if (!$order->picking) {
                $order->picking()->create(['status' => 'PENDING']);
            }
            $order->picking->update(['status' => 'CONFIRMED']);
            return back()->with('success', 'Xác nhận đơn hàng thành công.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load('items.variant.product', 'picking.warehouse');
        return view('admin.orders.fulfillment.show', compact('order'));
    }

    public function completePacking(Request $request, Order $order)
    {
        $request->validate([
            'warehouse_id' => 'required|integer|exists:warehouses,id',
        ]);

        try {
            $warehouseId = $request->input('warehouse_id');

            foreach ($order->items as $item) {
                $stock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if (!$stock || $stock->available < $item->quantity) {
                    throw new \Exception('Tồn kho không đủ.');
                }
            }

            $order->picking->update([
                'warehouse_id' => $warehouseId,
                'status' => 'PACKED'
            ]);
            
            return redirect()->route('admin.orders.fulfillment.show', $order)
                ->with('success', 'Đóng gói thành công.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function completeShipping(Order $order)
    {
        try {
            $this->fulfillmentService->completeShipping($order);
            return back()->with('success', 'Cập nhật giao hàng thành công.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
