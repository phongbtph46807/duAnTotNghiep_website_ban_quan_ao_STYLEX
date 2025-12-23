<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\ProductVariant;
use App\Models\StockInRequest;
use App\Models\WarehouseStock;
use App\Models\InventoryLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StockInController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }


    public function index()
    {
        $batches = StockInRequest::with(['variant.product', 'warehouse', 'createdBy', 'confirmedBy', 'latestQcResult.qcBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.stock-in.index', compact('batches'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('operational_status', 'ACTIVE')->get();
        $variants = ProductVariant::with('product', 'color', 'size')->get();

        return view('admin.inventory.stock-in.create', compact('warehouses', 'variants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'variant_id' => 'required|exists:product_variants,id',
            'batch_number' => 'required|string|unique:stock_in_requests,batch_number',
            'quantity' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $validated['created_by'] = auth()->id();
                $validated['status'] = 'PENDING';
                $validated['received_date'] = $validated['received_date'] ?? now()->toDateString();

                $stockInRequest = StockInRequest::create($validated);

                $stockData = [
                    'warehouse_id' => $validated['warehouse_id'],
                    'variant_id' => $validated['variant_id']
                ];

                if (Schema::hasColumn('warehouse_stocks', 'batch_number')) {
                    $stockData['batch_number'] = $validated['batch_number'];
                }

                $stock = WarehouseStock::firstOrCreate(
                    $stockData,
                    [
                        'on_hand' => 0,
                        'available' => 0,
                        'reserved' => 0,
                        'quarantine' => 0,
                        'damaged' => 0,
                        'clearance' => 0
                    ]
                );

                $quantityBefore = $stock->on_hand;
                $stock->update([
                    'on_hand' => $stock->on_hand + $validated['quantity'],
                    'quarantine' => $stock->quarantine + $validated['quantity'],
                ]);

                InventoryLog::create([
                    'warehouse_id' => $validated['warehouse_id'],
                    'variant_id' => $validated['variant_id'],
                    'action' => 'IN',
                    'quantity_before' => $quantityBefore,
                    'quantity_change' => $validated['quantity'],
                    'quantity_after' => $stock->on_hand,
                    'reference_type' => 'stock_in',
                    'reference_id' => $stockInRequest->id,
                    'user_id' => auth()->id(),
                    'notes' => "Nhập kho vào QUARANTINE - Lô {$validated['batch_number']}",
                ]);

                $this->notificationService->notifyPendingApproval('stock_in', $stockInRequest->id, $stockInRequest->batch_number);
            });

            return redirect()->route('admin.inventory.stock-in.index')
                ->with('success', 'Tạo yêu cầu nhập kho thành công. Hàng đã vào khu cách ly chờ QC');
        } catch (\Exception $e) {
            Log::error('Stock In Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo yêu cầu nhập kho');
        }
    }

    public function qc($id)
    {
        $request = StockInRequest::findOrFail($id);

        if ($request->status !== 'PENDING') {
            return back()->with('error', 'Chỉ có thể QC yêu cầu ở trạng thái PENDING');
        }

        return view('admin.inventory.stock-in.qc', compact('request'));
    }

    public function confirmQC(Request $httpRequest, $id)
    {
        $validated = $httpRequest->validate([
            'passed_qty' => 'required|integer|min:0',
            'failed_qty' => 'required|integer|min:0',
            'failed_handling' => 'nullable|in:damaged,return_supplier',
            'defect_type' => 'nullable|string',
            'defect_level' => 'nullable|in:LIGHT,MEDIUM,HEAVY',
            'accept_shortage' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $request = StockInRequest::findOrFail($id);
            $total = $validated['passed_qty'] + $validated['failed_qty'];

            if ($total > $request->quantity) {
                return back()->with('error', 'Tổng số lượng QC vượt quá số lượng nhập');
            }

            if ($total < $request->quantity && !$validated['accept_shortage']) {
                return back()->with('error', 'Vui lòng chấp nhận thiếu hàng hoặc nhập đủ số lượng');
            }

            if ($validated['failed_qty'] > 0 && !$validated['failed_handling']) {
                return back()->with('error', 'Vui lòng chọn cách xử lý hàng không đạt');
            }

            DB::transaction(function () use ($request, $validated, $total, $httpRequest) {
                \App\Models\WarehouseQcResult::create([
                    'request_type' => 'STOCK_IN',
                    'request_id' => $request->id,
                    'variant_id' => $request->variant_id,
                    'total_qty' => $request->quantity,
                    'passed_qty' => $validated['passed_qty'],
                    'failed_qty' => $validated['failed_qty'],
                    'qc_by' => auth()->id(),
                    'qc_at' => now(),
                    'qc_notes' => ($validated['notes'] ?? '') .
                                 ($validated['failed_qty'] > 0 ?
                                  "\n[Xử lý hàng fail: " . ($validated['failed_handling'] === 'return_supplier' ? 'Trả NCC' : 'Vào kho hỏng') . "]" : ''),
                ]);

                $request->update([
                    'status' => $validated['passed_qty'] > 0 ? 'QC_PASSED' : 'QC_FAILED',
                ]);

                $query = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                    ->where('variant_id', $request->variant_id);

                if (Schema::hasColumn('warehouse_stocks', 'batch_number')) {
                    $query->where('batch_number', $request->batch_number);
                }

                $stock = $query->lockForUpdate()->first();

                if ($stock) {
                    $quantityBefore = $stock->on_hand;
                    $actualFailed = $validated['failed_qty'];
                    $shortage = $request->quantity - $total;

                    if ($validated['failed_handling'] === 'return_supplier') {
                        if ($stock->quarantine < $total) {
                            throw new \Exception('Tồn kho quarantine không đủ để trả hàng');
                        }

                        $stock->update([
                            'on_hand' => $stock->on_hand - $actualFailed,
                            'quarantine' => $stock->quarantine - $actualFailed,
                        ]);

                        InventoryLog::create([
                            'warehouse_id' => $request->warehouse_id,
                            'variant_id' => $request->variant_id,
                            'action' => 'OUT',
                            'quantity_before' => $quantityBefore,
                            'quantity_change' => -$actualFailed,
                            'quantity_after' => $stock->on_hand,
                            'reference_type' => 'supplier_return',
                            'reference_id' => $request->id,
                            'user_id' => auth()->id(),
                            'notes' => "Trả hàng cho NCC do QC fail - Lô {$request->batch_number}",
                        ]);
                    } else {
                        if ($stock->quarantine < $total) {
                            throw new \Exception('Tồn kho quarantine không đủ');
                        }

                        $stock->update([
                            'on_hand' => $stock->on_hand - $actualFailed,
                            'quarantine' => $stock->quarantine - $actualFailed,
                            'damaged' => $stock->damaged + $actualFailed,
                        ]);

                        if ($actualFailed > 0) {
                            \App\Models\DefectAssessment::create([
                                'warehouse_id' => $request->warehouse_id,
                                'variant_id' => $request->variant_id,
                                'quantity' => $actualFailed,
                                'defect_level' => $validated['defect_level'] ?? 'MEDIUM',
                                'defect_type' => $validated['defect_type'] ?? 'QC_FAILED',
                                'defect_description' => 'Hàng không đạt chuẩn QC khi nhập kho - Lô: ' . $request->batch_number . ' - NCC: ' . ($request->supplier_name ?? 'N/A'),
                                'batch_number' => $request->batch_number,
                                'location' => $request->location,
                                'notes' => 'Tự động tạo từ QC fail\nQC Notes: ' . ($validated['notes'] ?? 'Không có ghi chú'),
                                'status' => 'PENDING',
                                'stock_in_request_id' => $request->id,
                                'created_by' => auth()->id(),
                            ]);

                            InventoryLog::create([
                                'warehouse_id' => $request->warehouse_id,
                                'variant_id' => $request->variant_id,
                                'action' => 'ADJUSTMENT',
                                'quantity_before' => $quantityBefore,
                                'quantity_change' => -$actualFailed,
                                'quantity_after' => $stock->on_hand,
                                'reference_type' => 'defect_assessment',
                                'reference_id' => $request->id,
                                'user_id' => auth()->id(),
                                'notes' => "Chuyển vào kho hàng hỏng từ QC fail - Lô {$request->batch_number}",
                            ]);
                        }
                    }
                }

                if ($total < $request->quantity) {
                    $shortage = $request->quantity - $total;
                    $request->update([
                        'quantity' => $shortage,
                        'status' => 'QC_FAILED',
                        'notes' => ($request->notes ? $request->notes . "\n\n" : '') .
                                   "[THỪA HÀNG] Thiếu {$shortage} sản phẩm - " . ($validated['notes'] ?? 'Không có ghi chú'),
                    ]);
                } elseif ($validated['failed_qty'] > 0) {
                    $request->update(['quantity' => $validated['failed_qty']]);
                }

                if ($validated['passed_qty'] > 0 && $validated['failed_qty'] === 0 && $total === $request->quantity) {
                    $newBatchNumber = $request->batch_number . '-PASS-' . now()->format('YmdHis');
                    StockInRequest::create([
                        'warehouse_id' => $request->warehouse_id,
                        'variant_id' => $request->variant_id,
                        'batch_number' => $newBatchNumber,
                        'quantity' => $validated['passed_qty'],
                        'cost_price' => $request->cost_price,
                        'location' => $request->location,
                        'received_date' => $request->received_date,
                        'supplier_name' => $request->supplier_name,
                        'supplier_contact' => $request->supplier_contact,
                        'invoice_number' => $request->invoice_number,
                        'status' => 'QC_PASSED',
                        'created_by' => auth()->id(),
                        'notes' => "Tự động tạo từ QC pass của lô {$request->batch_number}",
                    ]);

                    if (Schema::hasColumn('warehouse_stocks', 'batch_number')) {
                        WarehouseStock::create([
                            'warehouse_id' => $request->warehouse_id,
                            'variant_id' => $request->variant_id,
                            'batch_number' => $newBatchNumber,
                            'location' => $request->location,
                            'on_hand' => $validated['passed_qty'],
                            'available' => $validated['passed_qty'],
                            'reserved' => 0,
                            'quarantine' => 0,
                            'damaged' => 0,
                            'clearance' => 0,
                        ]);
                    }
                }

                if ($validated['failed_qty'] > 0) {
                    $this->notificationService->notifyQCFailed($request, $validated['failed_qty'], $request->quantity);
                }
            });

            return redirect()->route('admin.inventory.stock-in.index')
                ->with('success', 'Hoàn thành QC thành công.');
        } catch (\Exception $e) {
            Log::error('QC Error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi QC phiếu nhập kho');
        }
    }

    public function confirm($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $request = StockInRequest::findOrFail($id);

                if (!$request->canBeConfirmed()) {
                    throw new \Exception('Phiếu không thể xác nhận ở trạng thái hiện tại.');
                }

                $query = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                    ->where('variant_id', $request->variant_id);

                if (Schema::hasColumn('warehouse_stocks', 'batch_number')) {
                    $query->where('batch_number', $request->batch_number);
                }

                $stock = $query->first();
                if ($stock && $request->location) {
                    $stock->update(['location' => $request->location]);
                }

                $request->update([
                    'status' => 'CONFIRMED',
                    'confirmed_by' => auth()->id(),
                    'confirmed_at' => now(),
                ]);
            });

            return redirect()->route('admin.inventory.stock-in.index')
                ->with('success', 'Xác nhận nhập kho thành công.');
        } catch (\Exception $e) {
            Log::error('Confirm Error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xác nhận phiếu');
        }
    }

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
            $request = StockInRequest::findOrFail($id);

            if (!in_array($request->status, ['PENDING', 'QC_FAILED'])) {
                return back()->with('error', 'Chỉ có thể từ chối phiếu ở trạng thái PENDING hoặc QC_FAILED');
            }

            DB::transaction(function () use ($request, $validated) {
                $query = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                    ->where('variant_id', $request->variant_id);

                if (Schema::hasColumn('warehouse_stocks', 'batch_number')) {
                    $query->where('batch_number', $request->batch_number);
                }

                $stock = $query->lockForUpdate()->first();

                if ($stock) {
                    if ($stock->on_hand < $request->quantity || $stock->quarantine < $request->quantity) {
                        throw new \Exception('Tồn kho không đủ để hoàn lại');
                    }

                    $quantityBefore = $stock->on_hand;
                    $stock->update([
                        'on_hand' => $stock->on_hand - $request->quantity,
                        'quarantine' => $stock->quarantine - $request->quantity,
                    ]);

                    InventoryLog::create([
                        'warehouse_id' => $request->warehouse_id,
                        'variant_id' => $request->variant_id,
                        'action' => 'OUT',
                        'quantity_before' => $quantityBefore,
                        'quantity_change' => -$request->quantity,
                        'quantity_after' => $stock->on_hand,
                        'reference_type' => 'stock_in_rejection',
                        'reference_id' => $request->id,
                        'user_id' => auth()->id(),
                        'notes' => "Từ chối phiếu nhập kho - Lô {$request->batch_number}",
                    ]);
                }

                $request->update([
                    'status' => 'CANCELLED',
                    'notes' => ($request->notes ? $request->notes . "\n\n" : '') .
                               "[TỪ CHỐI] " . now()->format('d/m/Y H:i') . " - " .
                               auth()->user()->name . ": " . $validated['rejection_reason'],
                ]);
            });

            return redirect()->route('admin.inventory.stock-in.index')
                ->with('success', 'Đã từ chối phiếu nhập kho');
        } catch (\Exception $e) {
            Log::error('Reject Error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi từ chối phiếu');
        }
    }
}
