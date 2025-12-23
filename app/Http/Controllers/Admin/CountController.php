<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CountRequest;
use App\Models\Warehouse;
use App\Models\StockInRequest;
use App\Models\WarehouseStock;
use App\Models\InventoryLog;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CountController extends Controller
{
    public function index()
    {
        $requests = CountRequest::with(['warehouse', 'variant.product', 'createdBy', 'countedBy', 'confirmedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $batchNumbers = $requests->pluck('batch_number')->filter()->unique()->toArray();
        $batches = StockInRequest::whereIn('batch_number', $batchNumbers)->get()->keyBy('batch_number');

        foreach ($requests as $request) {
            $request->current_stock = $request->batch_number && isset($batches[$request->batch_number]) 
                ? $batches[$request->batch_number]->quantity 
                : 0;
        }

        return view('admin.inventory.count.index', compact('requests'));
    }

    public function create()
    {
        $batches = WarehouseStock::with(['warehouse', 'variant.product', 'variant.color', 'variant.size', 'stockInRequest'])
            ->where('on_hand', '>', 0)
            ->whereNotNull('batch_number')
            ->distinct()
            ->get();

        return view('admin.inventory.count.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'variant_id' => 'required|exists:product_variants,id',
            'batch_number' => 'required|string',
        ]);

        try {
            $stock = WarehouseStock::where('warehouse_id', $validated['warehouse_id'])
                ->where('variant_id', $validated['variant_id'])
                ->where('batch_number', $validated['batch_number'])
                ->firstOrFail();
            
            $existingCount = CountRequest::where('warehouse_id', $validated['warehouse_id'])
                ->where('variant_id', $validated['variant_id'])
                ->where('batch_number', $validated['batch_number'])
                ->whereIn('status', ['PENDING', 'CONFIRMED'])
                ->first();
            
            if ($existingCount) {
                return back()->with('error', 'Lô hàng này đã có yêu cầu kiểm kê. Không thể tạo thêm.');
            }
            
            CountRequest::create([
                'warehouse_id' => $validated['warehouse_id'],
                'variant_id' => $validated['variant_id'],
                'batch_number' => $validated['batch_number'],
                'location' => $stock->location,
                'system_qty' => $stock->on_hand,
                'status' => 'PENDING',
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('admin.inventory.count.index')
                ->with('success', 'Tạo yêu cầu kiểm kê thành công');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function count($id)
    {
        $request = CountRequest::with(['warehouse', 'variant.product', 'variant.color', 'variant.size'])->findOrFail($id);

        if ($request->status !== 'PENDING') {
            return back()->with('error', 'Chỉ có thể kiểm kê khi ở trạng thái PENDING');
        }

        $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
            ->where('variant_id', $request->variant_id)
            ->where('batch_number', $request->batch_number)
            ->with('stockInRequest')
            ->first() ?? new WarehouseStock([
                'on_hand' => 0,
                'available' => 0,
                'reserved' => 0,
                'quarantine' => 0,
                'damaged' => 0
            ]);

        $batchInfo = $stock->stockInRequest;

        return view('admin.inventory.count.count', compact('request', 'stock', 'batchInfo'));
    }

    public function confirmCount(Request $request, $id)
    {
        $validated = $request->validate([
            'available_qty' => 'required|integer|min:0',
            'reserved_qty' => 'required|integer|min:0',
            'quarantine_qty' => 'required|integer|min:0',
            'damaged_qty' => 'required|integer|min:0',
            'defect_level' => 'nullable|in:LIGHT,MEDIUM,HEAVY',
            'classification' => 'nullable|in:REWORK,SCRAP,B-GRADE',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $countRequest = CountRequest::findOrFail($id);
            
            if ($countRequest->status !== 'PENDING') {
                return back()->with('error', 'Chỉ có thể kiểm kê khi ở trạng thái PENDING');
            }
            
            $totalQty = $validated['available_qty'] + $validated['reserved_qty'] + $validated['quarantine_qty'] + $validated['damaged_qty'];
            
            DB::transaction(function () use ($countRequest, $validated, $totalQty) {
                $stock = WarehouseStock::where('warehouse_id', $countRequest->warehouse_id)
                    ->where('variant_id', $countRequest->variant_id)
                    ->where('batch_number', $countRequest->batch_number)
                    ->lockForUpdate()
                    ->firstOrCreate(
                        [
                            'warehouse_id' => $countRequest->warehouse_id,
                            'variant_id' => $countRequest->variant_id,
                            'batch_number' => $countRequest->batch_number
                        ],
                        ['on_hand' => 0, 'available' => 0, 'reserved' => 0, 'quarantine' => 0, 'damaged' => 0]
                    );

                $quantityBefore = $stock->on_hand;
                
                $stock->update([
                    'available' => $validated['available_qty'],
                    'reserved' => $validated['reserved_qty'],
                    'quarantine' => $validated['quarantine_qty'],
                    'damaged' => $validated['damaged_qty'],
                    'on_hand' => $totalQty,
                ]);

                $countRequest->update([
                    'physical_qty' => $totalQty,
                    'difference' => $totalQty - $countRequest->system_qty,
                    'available_qty' => $validated['available_qty'],
                    'reserved_qty' => $validated['reserved_qty'],
                    'quarantine_qty' => $validated['quarantine_qty'],
                    'damaged_qty' => $validated['damaged_qty'],
                    'status' => 'PENDING_ADJUSTMENT',
                    'counted_by' => Auth::id(),
                ]);

                InventoryLog::create([
                    'warehouse_id' => $countRequest->warehouse_id,
                    'variant_id' => $countRequest->variant_id,
                    'action' => 'ADJUSTMENT',
                    'quantity_before' => $quantityBefore,
                    'quantity_change' => $totalQty - $quantityBefore,
                    'quantity_after' => $totalQty,
                    'reference_type' => 'count_batch',
                    'reference_id' => $countRequest->id,
                    'user_id' => Auth::id(),
                    'notes' => $validated['notes'] ?? '',
                ]);

                if ($validated['damaged_qty'] > 0) {
                    \App\Models\DefectAssessment::create([
                        'warehouse_id' => $countRequest->warehouse_id,
                        'variant_id' => $countRequest->variant_id,
                        'batch_number' => $countRequest->batch_number,
                        'quantity' => $validated['damaged_qty'],
                        'defect_level' => $validated['defect_level'] ?? 'MEDIUM',
                        'classification' => $validated['classification'] ?? 'SCRAP',
                        'status' => 'PENDING',
                        'created_by' => Auth::id(),
                        'notes' => 'Từ kiểm kê lô ' . $countRequest->batch_number,
                    ]);
                }
            });

            return redirect()->route('admin.inventory.count.index')
                ->with('success', 'Xác nhận kiểm kê thành công. Vui lòng tiếp tục điều chỉnh');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function confirmAdjustment(Request $request, $id)
    {
        $validated = $request->validate([
            'available_qty' => 'required|integer|min:0',
            'reserved_qty' => 'required|integer|min:0',
            'quarantine_qty' => 'required|integer|min:0',
            'damaged_qty' => 'required|integer|min:0',
            'defect_level' => 'nullable|in:LIGHT,MEDIUM,HEAVY',
            'classification' => 'nullable|in:REWORK,SCRAP,B-GRADE',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $countRequest = CountRequest::findOrFail($id);
            
            if ($countRequest->status !== 'PENDING_ADJUSTMENT') {
                return back()->with('error', 'Chỉ có thể điều chỉnh khi ở trạng thái chờ điều chỉnh');
            }
            
            $totalQty = $validated['available_qty'] + $validated['reserved_qty'] + $validated['quarantine_qty'] + $validated['damaged_qty'];
            
            DB::transaction(function () use ($countRequest, $validated, $totalQty) {
                $stock = WarehouseStock::where('warehouse_id', $countRequest->warehouse_id)
                    ->where('variant_id', $countRequest->variant_id)
                    ->where('batch_number', $countRequest->batch_number)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $quantityBefore = $stock->on_hand;
                    
                    $stock->update([
                        'available' => $validated['available_qty'],
                        'reserved' => $validated['reserved_qty'],
                        'quarantine' => $validated['quarantine_qty'],
                        'damaged' => $validated['damaged_qty'],
                        'on_hand' => $totalQty,
                    ]);

                    InventoryLog::create([
                        'warehouse_id' => $countRequest->warehouse_id,
                        'variant_id' => $countRequest->variant_id,
                        'action' => 'ADJUSTMENT',
                        'quantity_before' => $quantityBefore,
                        'quantity_change' => $totalQty - $quantityBefore,
                        'quantity_after' => $totalQty,
                        'reference_type' => 'count_adjustment',
                        'reference_id' => $countRequest->id,
                        'user_id' => Auth::id(),
                        'notes' => $validated['notes'] ?? '',
                    ]);
                }

                $countRequest->update([
                    'available_qty' => $validated['available_qty'],
                    'reserved_qty' => $validated['reserved_qty'],
                    'quarantine_qty' => $validated['quarantine_qty'],
                    'damaged_qty' => $validated['damaged_qty'],
                    'status' => 'CONFIRMED',
                    'confirmed_by' => Auth::id(),
                ]);

                if ($validated['damaged_qty'] > 0) {
                    $existingDefect = \App\Models\DefectAssessment::where('warehouse_id', $countRequest->warehouse_id)
                        ->where('variant_id', $countRequest->variant_id)
                        ->where('batch_number', $countRequest->batch_number)
                        ->where('status', 'PENDING')
                        ->first();

                    if ($existingDefect) {
                        $existingDefect->update([
                            'quantity' => $validated['damaged_qty'],
                            'defect_level' => $validated['defect_level'] ?? 'MEDIUM',
                            'classification' => $validated['classification'] ?? 'SCRAP',
                        ]);
                    }
                }
            });

            return redirect()->route('admin.inventory.count.index')
                ->with('success', 'Điều chỉnh kiểm kê thành công');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


}
