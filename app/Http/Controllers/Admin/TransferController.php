<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\ProductVariant;
use App\Models\TransferRequest;
use App\Models\WarehouseStock;
use App\Services\InventoryService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $movements = TransferRequest::with(['fromWarehouse', 'toWarehouse', 'variant.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.transfer.index', compact('movements'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();
        $variants = ProductVariant::with('product', 'color', 'size')->get();

        return view('admin.inventory.transfer.create', compact('warehouses', 'variants'));
    }

    public function getBatches($warehouseId, $variantId)
    {
        $batches = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('variant_id', $variantId)
            ->where('available', '>', 0)
            ->whereNotNull('batch_number')
            ->whereNotNull('location')
            ->select('batch_number', 'available', 'location')
            ->get()
            ->map(function ($stock) {
                return [
                    'batch_number' => $stock->batch_number,
                    'available' => $stock->available,
                    'location' => $stock->location,
                ];
            });

        return response()->json($batches);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'variant_id' => 'required|exists:product_variants,id',
            'batch_number' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $stock = WarehouseStock::where('warehouse_id', $validated['from_warehouse_id'])
                ->where('variant_id', $validated['variant_id'])
                ->where(function($q) use ($validated) {
                    $q->where('batch_number', $validated['batch_number'])
                      ->orWhereNull('batch_number');
                })
                ->first();

            if (!$stock) {
                return back()->withInput()->with('error', 'Lô hàng không tồn tại trong kho. Vui lòng kiểm tra lại.');
            }

            if ($stock->available < $validated['quantity']) {
                return back()->withInput()->with('error', 'Không đủ tồn kho. Có sẵn: ' . $stock->available);
            }

            $validated['created_by'] = auth()->id();
            $validated['status'] = 'PENDING';
            $validated['batch_number'] = $stock->batch_number ?? $validated['batch_number'];

            TransferRequest::create($validated);

            return redirect()->route('admin.inventory.transfer.index')
                ->with('success', 'Tạo yêu cầu chuyển kho thành công');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function confirmOut($id)
    {
        try {
            InventoryService::confirmTransferOut($id);
            return redirect()->route('admin.inventory.transfer.index')
                ->with('success', 'Xac nhan xuat kho thanh cong');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function confirmIn($id)
    {
        try {
            InventoryService::confirmTransferIn($id);
            return redirect()->route('admin.inventory.transfer.index')
                ->with('success', 'Xac nhan nhap kho thanh cong');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function confirmQC($id, Request $request)
    {
        try {
            $transfer = TransferRequest::findOrFail($id);

            if ($transfer->status !== 'QC_CHECKING') {
                return back()->with('error', 'Chỉ có thể QC chuyen kho ở trạng thái QC_CHECKING');
            }

            $validated = $request->validate([
                'qc_passed_qty' => 'required|integer|min:0',
                'qc_failed_qty' => 'required|integer|min:0',
            ]);

            InventoryService::confirmTransferQC($transfer->id, $validated['qc_passed_qty'], $validated['qc_failed_qty']);

            return redirect()->route('admin.inventory.transfer.index')
                ->with('success', 'Hoàn thành QC chuyen kho thành công');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
