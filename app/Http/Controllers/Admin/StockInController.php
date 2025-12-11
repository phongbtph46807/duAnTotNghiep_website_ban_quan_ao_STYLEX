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
            'received_date' => 'nullable|date',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, &$stockInRequest) {
                $validated['created_by'] = auth()->id();
                $validated['status'] = 'PENDING';
                $validated['received_date'] = $validated['received_date'] ?? now()->toDateString();

                $stockInRequest = StockInRequest::create($validated);
                
                // Nhập vào QUARANTINE ngay khi tạo phiếu
                $stock = WarehouseStock::firstOrCreate(
                    [
                        'warehouse_id' => $validated['warehouse_id'],
                        'variant_id' => $validated['variant_id']
                    ],
                    [
                        'on_hand' => 0,
                        'available' => 0,
                        'reserved' => 0,
                        'quarantine' => 0,
                        'damaged' => 0,
                        'clearance' => 0
                    ]
                );
                
                $stock->update([
                    'on_hand' => $stock->on_hand + $validated['quantity'],
                    'quarantine' => $stock->quarantine + $validated['quantity'],
                ]);
                
                // Ghi log
                InventoryLog::create([
                    'warehouse_id' => $validated['warehouse_id'],
                    'variant_id' => $validated['variant_id'],
                    'action' => 'IN',
                    'quantity_before' => $stock->on_hand - $validated['quantity'],
                    'quantity_change' => $validated['quantity'],
                    'quantity_after' => $stock->on_hand,
                    'reference_type' => 'stock_in',
                    'reference_id' => $stockInRequest->id,
                    'user_id' => auth()->id(),
                    'notes' => "Nhập kho vào QUARANTINE - Lô {$validated['batch_number']}",
                ]);

                // Thông báo phiếu chờ duyệt
                $this->notificationService->notifyPendingApproval('stock_in', $stockInRequest->id, $stockInRequest->batch_number);
            });

            return redirect()->route('admin.inventory.stock-in.index')
                ->with('success', 'Tạo yêu cầu nhập kho thành công. Hàng đã vào khu cách ly chờ QC');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function qc($id)
    {
        $request = StockInRequest::find($id);

        if (!$request || $request->status !== 'PENDING') {
            return back()->with('error', 'Chỉ có thể QC yêu cầu ở trạng thái PENDING');
        }

        return view('admin.inventory.stock-in.qc', compact('request'));
    }

    public function confirmQC(Request $httpRequest, $id)
    {
        $validated = $httpRequest->validate([
            'passed_qty' => 'required|integer|min:0',
            'failed_qty' => 'required|integer|min:0',
            'failed_handling' => 'required|in:damaged,return_supplier',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $request = StockInRequest::findOrFail($id);
            
            if ($validated['passed_qty'] + $validated['failed_qty'] !== $request->quantity) {
                return back()->with('error', 'Tổng số lượng QC không khớp với số lượng nhập');
            }

            DB::transaction(function () use ($request, $validated, $httpRequest) {
                // Tạo QC result trong bảng warehouse_qc_results
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

                // Cập nhật status của request
                $request->update([
                    'status' => $validated['passed_qty'] > 0 ? 'QC_PASSED' : 'QC_FAILED',
                ]);

                // Cập nhật warehouse_stocks
                $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
                    ->where('variant_id', $request->variant_id)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    if ($validated['failed_handling'] === 'return_supplier') {
                        // Trả hàng cho supplier: trừ tồn kho hoàn toàn
                        $stock->update([
                            'on_hand' => $stock->on_hand - $validated['failed_qty'],
                            'quarantine' => $stock->quarantine - $request->quantity,
                            'available' => $stock->available + $validated['passed_qty'],
                        ]);
                    } else {
                        // Chuyển vào damaged: giữ tồn kho nhưng chuyển sang damaged
                        $stock->update([
                            'quarantine' => $stock->quarantine - $request->quantity,
                            'available' => $stock->available + $validated['passed_qty'],
                            'damaged' => $stock->damaged + $validated['failed_qty'],
                        ]);
                        
                        // Tự động tạo Defect Assessment cho hàng damaged
                        if ($validated['failed_qty'] > 0) {
                            \App\Models\DefectAssessment::create([
                                'warehouse_id' => $request->warehouse_id,
                                'variant_id' => $request->variant_id,
                                'quantity' => $validated['failed_qty'],
                                'defect_level' => 'MEDIUM',
                                'defect_type' => 'QC_FAILED',
                                'defect_description' => 'Hàng không đạt chuẩn QC khi nhập kho - Lô: ' . $request->batch_number . ' - NCC: ' . ($request->supplier_name ?? 'N/A'),
                                'notes' => 'Tự động tạo từ QC fail\nQC Notes: ' . ($validated['notes'] ?? 'Không có ghi chú'),
                                'status' => 'PENDING',
                                'stock_in_request_id' => $request->id,
                                'created_by' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Tạo supplier return request nếu chọn trả hàng
                if ($validated['failed_qty'] > 0 && $validated['failed_handling'] === 'return_supplier') {
                    // Ghi log trả hàng
                    InventoryLog::create([
                        'warehouse_id' => $request->warehouse_id,
                        'variant_id' => $request->variant_id,
                        'action' => 'OUT',
                        'quantity_before' => $stock->on_hand + $validated['failed_qty'],
                        'quantity_change' => -$validated['failed_qty'],
                        'quantity_after' => $stock->on_hand,
                        'reference_type' => 'supplier_return',
                        'reference_id' => $request->id,
                        'user_id' => auth()->id(),
                        'notes' => "Trả hàng cho NCC do QC fail - Lô {$request->batch_number}",
                    ]);
                }

                // Thông báo QC failed nếu tỷ lệ cao
                if ($validated['failed_qty'] > 0) {
                    $this->notificationService->notifyQCFailed($request, $validated['failed_qty'], $request->quantity);
                }
            });
            
            return redirect()->route('admin.inventory.stock-in.index')
                ->with('success', 'Hoàn thành QC thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi QC: ' . $e->getMessage());
        }
    }

    public function confirm($id)
    {
        try {
            $request = StockInRequest::findOrFail($id);
            
            if (!$request->canBeConfirmed()) {
                return back()->with('error', 'Phiếu không thể xác nhận ở trạng thái hiện tại.');
            }
            
            $request->update([
                'status' => 'CONFIRMED',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);
            
            return redirect()->route('admin.inventory.stock-in.index')
                ->with('success', 'Xác nhận nhập kho thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi xác nhận: ' . $e->getMessage());
        }
    }
    
    /**
     * Từ chối phiếu nhập kho
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
            $request = StockInRequest::findOrFail($id);
            
            // Chỉ cho phép từ chối ở trạng thái PENDING hoặc QC_FAILED
            if (!in_array($request->status, ['PENDING', 'QC_FAILED'])) {
                return back()->with('error', 'Chỉ có thể từ chối phiếu ở trạng thái PENDING hoặc QC_FAILED');
            }

            $request->update([
                'status' => 'CANCELLED',
                'notes' => ($request->notes ? $request->notes . "\n\n" : '') . 
                           "[TỪ CHỐI] " . now()->format('d/m/Y H:i') . " - " . 
                           auth()->user()->name . ": " . $validated['rejection_reason'],
            ]);

            return redirect()->route('admin.inventory.stock-in.index')
                ->with('success', 'Đã từ chối phiếu nhập kho');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi từ chối phiếu: ' . $e->getMessage());
        }
    }
}
