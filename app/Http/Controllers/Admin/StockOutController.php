<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\ProductVariant;
use App\Models\StockOutRequest;
use App\Models\WarehouseStock;
use App\Models\InventoryLog;
use App\Models\WarehouseQcResult;
use App\Models\WarehouseNote;
use App\Services\InventoryService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    protected $inventoryService;
    protected $notificationService;

    public function __construct(InventoryService $inventoryService, NotificationService $notificationService)
    {
        $this->inventoryService = $inventoryService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $batches = StockOutRequest::with(['variant.product', 'warehouse', 'createdBy', 'confirmedBy', 'latestQcResult.qcBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.stock-out.index', compact('batches'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();
        $variants = ProductVariant::with('product', 'color', 'size')->get();

        return view('admin.inventory.stock-out.create', compact('warehouses', 'variants'));
    }

    public function store(Request $httpRequest)
    {
        $validated = $httpRequest->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'variant_id' => 'required|exists:product_variants,id',
            'batch_number' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($validated, &$stockOutRequest) {
                $stock = WarehouseStock::where('warehouse_id', $validated['warehouse_id'])
                    ->where('variant_id', $validated['variant_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->available < $validated['quantity']) {
                    throw new \Exception('Không đủ tồn kho. Có sẵn: ' . ($stock->available ?? 0));
                }

                // Reserve ngay khi tạo phiếu
                $stock->update([
                    'available' => $stock->available - $validated['quantity'],
                    'reserved' => $stock->reserved + $validated['quantity'],
                ]);

                $validated['created_by'] = auth()->id();
                $validated['status'] = 'PENDING';

                $stockOutRequest = StockOutRequest::create($validated);

                // Ghi log
                InventoryLog::create([
                    'warehouse_id' => $validated['warehouse_id'],
                    'variant_id' => $validated['variant_id'],
                    'action' => 'OUT',
                    'quantity_before' => $stock->available + $validated['quantity'],
                    'quantity_change' => -$validated['quantity'],
                    'quantity_after' => $stock->available,
                    'reference_type' => 'stock_out',
                    'reference_id' => $stockOutRequest->id,
                    'user_id' => auth()->id(),
                    'notes' => "Reserve xuất kho - Lô {$validated['batch_number']}",
                ]);

                // Thông báo phiếu chờ duyệt
                $this->notificationService->notifyPendingApproval('stock_out', $stockOutRequest->id, $stockOutRequest->batch_number);
            });

            return redirect()->route('admin.inventory.stock-out.index')
                ->with('success', 'Tạo yêu cầu xuất kho thành công. Đã reserve tồn kho');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function qc($id)
    {
        $request = StockOutRequest::find($id);

        if (!$request || $request->status !== 'PENDING') {
            return back()->with('error', 'Chỉ có thể QC yêu cầu ở trạng thái PENDING');
        }

        return view('admin.inventory.stock-out.qc', compact('request'));
    }

    public function confirmQC(Request $httpRequest, $id)
    {
        $validated = $httpRequest->validate([
            'passed_qty' => 'required|integer|min:0',
            'failed_qty' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $request = StockOutRequest::findOrFail($id);

            if ($validated['passed_qty'] + $validated['failed_qty'] !== $request->quantity) {
                return back()->with('error', 'Tổng số lượng QC không khớp với số lượng xuất');
            }

            DB::transaction(function () use ($request, $validated, $httpRequest) {
                // Tạo QC result trong bảng warehouse_qc_results
                WarehouseQcResult::create([
                    'request_type' => 'STOCK_OUT',
                    'request_id' => $request->id,
                    'variant_id' => $request->variant_id,
                    'total_qty' => $request->quantity,
                    'passed_qty' => $validated['passed_qty'],
                    'failed_qty' => $validated['failed_qty'],
                    'qc_by' => auth()->id(),
                    'qc_at' => now(),
                    'qc_notes' => $validated['notes'] ?? null,
                ]);

                $request->update([
                    'status' => $validated['passed_qty'] > 0 ? 'QC_PASSED' : 'QC_FAILED',
                ]);

                // Cập nhật warehouse_stocks: chuyển failed từ reserved sang damaged
                if ($validated['failed_qty'] > 0) {
                    $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                        ->where('variant_id', $request->variant_id)
                        ->lockForUpdate()
                        ->first();

                    if ($stock) {
                        $stock->update([
                            'reserved' => $stock->reserved - $validated['failed_qty'],
                            'damaged' => $stock->damaged + $validated['failed_qty'],
                        ]);
                    }
                }
            });

            return redirect()->route('admin.inventory.stock-out.index')
                ->with('success', 'Hoàn thành QC thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi QC: ' . $e->getMessage());
        }
    }

    public function confirm($id)
    {
        try {
            $request = StockOutRequest::with('qcResults')->findOrFail($id);

            if ($request->status !== 'QC_PASSED') {
                return back()->with('error', 'Chỉ có thể xác nhận yêu cầu ở trạng thái QC_PASSED');
            }

            $latestQc = $request->qcResults()->latest()->first();
            if (!$latestQc || $latestQc->passed_qty <= 0) {
                return back()->with('error', 'Không có số lượng pass để xuất kho');
            }

            DB::transaction(function () use ($request, $latestQc) {
                // Trừ reserved, giảm on_hand
                $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                    ->where('variant_id', $request->variant_id)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $stock->update([
                        'on_hand' => $stock->on_hand - $latestQc->passed_qty,
                        'reserved' => $stock->reserved - $latestQc->passed_qty,
                    ]);

                    // Ghi log
                    InventoryLog::create([
                        'warehouse_id' => $request->warehouse_id,
                        'variant_id' => $request->variant_id,
                        'action' => 'OUT',
                        'quantity_before' => $stock->on_hand + $latestQc->passed_qty,
                        'quantity_change' => -$latestQc->passed_qty,
                        'quantity_after' => $stock->on_hand,
                        'reference_type' => 'stock_out',
                        'reference_id' => $request->id,
                        'user_id' => auth()->id(),
                        'notes' => "Xuất kho thành công - Lô {$request->batch_number}",
                    ]);
                }

                $request->update([
                    'status' => 'CONFIRMED',
                    'confirmed_by' => auth()->id(),
                    'confirmed_at' => now(),
                ]);
            });

            return redirect()->route('admin.inventory.stock-out.index')
                ->with('success', 'Xác nhận xuất kho thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi xác nhận xuất kho: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối phiếu xuất kho
     */
    public function reject(Request $httpRequest, $id)
    {
        $validated = $httpRequest->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ], [
            'rejection_reason.required' => 'Vui lòng nhập lý do từ chối',
            'rejection_reason.min' => 'Lý do từ chối phải có ít nhất 10 ký tự',
            'rejection_reason.max' => 'Lý do từ chối không được quá 500 ký tự',
        ]);

        try {
            $request = StockOutRequest::findOrFail($id);

            // Chỉ cho phép từ chối ở trạng thái PENDING hoặc QC_FAILED
            if (!in_array($request->status, ['PENDING', 'QC_FAILED'])) {
                return back()->with('error', 'Chỉ có thể từ chối phiếu ở trạng thái PENDING hoặc QC_FAILED');
            }

            DB::transaction(function () use ($request, $validated) {
                // Hoàn lại reserved
                $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                    ->where('variant_id', $request->variant_id)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $stock->update([
                        'available' => $stock->available + $request->quantity,
                        'reserved' => $stock->reserved - $request->quantity,
                    ]);

                    // Ghi log
                    InventoryLog::create([
                        'warehouse_id' => $request->warehouse_id,
                        'variant_id' => $request->variant_id,
                        'action' => 'ADJUSTMENT',
                        'quantity_before' => $stock->available - $request->quantity,
                        'quantity_change' => $request->quantity,
                        'quantity_after' => $stock->available,
                        'reference_type' => 'stock_out_reject',
                        'reference_id' => $request->id,
                        'user_id' => auth()->id(),
                        'notes' => "Hoàn tồn kho do từ chối phiếu xuất - Lô {$request->batch_number}",
                    ]);
                }

                $request->update([
                    'status' => 'CANCELLED',
                    'notes' => ($request->notes ? $request->notes . "\n\n" : '') .
                               "[TỪ CHỐI] " . now()->format('d/m/Y H:i') . " - " .
                               auth()->user()->name . ": " . $validated['rejection_reason'],
                ]);
            });

            return redirect()->route('admin.inventory.stock-out.index')
                ->with('success', 'Đã từ chối phiếu xuất kho');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi từ chối phiếu: ' . $e->getMessage());
        }
    }
}
