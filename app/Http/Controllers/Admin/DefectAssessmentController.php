<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefectAssessment;
use App\Models\StockInRequest;
use App\Models\WarehouseStock;
use App\Models\ProductVariant;
use App\Models\StockOutInvoice;
use App\Models\StockOutInvoiceItem;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DefectAssessmentController extends Controller
{
    public function index()
    {
        $assessments = DefectAssessment::with(['warehouse', 'variant.product', 'createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.defect.index', compact('assessments'));
    }

    public function create()
    {
        return view('admin.inventory.defect.create');
    }

    public function store(Request $request)
    {
        return back()->with('error', 'Defects are created automatically from count workflow');
    }

    public function assess($id)
    {
        $defect = DefectAssessment::findOrFail($id);
        return view('admin.inventory.defect.assess', compact('defect'));
    }

    public function confirmAssess(Request $request, $id)
    {
        return back()->with('error', 'Use approve method instead');
    }

    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'repair_cost' => 'nullable|integer|min:0',
        ]);

        try {
            $defect = DefectAssessment::findOrFail($id);
            
            if ($defect->status !== 'PENDING') {
                return back()->with('error', 'Chỉ có thể duyệt đánh giá ở trạng thái PENDING');
            }

            DB::transaction(function () use ($defect, $validated) {
                $originalBatch = StockInRequest::where('batch_number', $defect->batch_number)
                    ->where('warehouse_id', $defect->warehouse_id)
                    ->where('variant_id', $defect->variant_id)
                    ->first();

                if (!$originalBatch) {
                    throw new \Exception('Không tìm thấy lô hàng gốc');
                }

                if ($defect->classification === 'REWORK') {
                    $newCostPrice = $originalBatch->cost_price + ($validated['repair_cost'] ?? 0);
                    $newBatchNumber = 'REWORK-' . $defect->id . '-' . now()->format('YmdHis');
                    
                    StockInRequest::create([
                        'warehouse_id' => $defect->warehouse_id,
                        'variant_id' => $defect->variant_id,
                        'batch_number' => $newBatchNumber,
                        'quantity' => $defect->quantity,
                        'cost_price' => $newCostPrice,
                        'received_date' => now()->toDateString(),
                        'supplier_name' => $originalBatch->supplier_name,
                        'supplier_contact' => $originalBatch->supplier_contact,
                        'location' => $defect->location,
                        'status' => 'PENDING',
                        'created_by' => Auth::id(),
                        'notes' => 'Sửa chữa từ lô ' . $defect->batch_number,
                    ]);
                    
                    WarehouseStock::updateOrCreate(
                        [
                            'warehouse_id' => $defect->warehouse_id,
                            'variant_id' => $defect->variant_id,
                            'batch_number' => $newBatchNumber,
                        ],
                        [
                            'location' => $defect->location,
                            'on_hand' => $defect->quantity,
                            'available' => $defect->quantity,
                            'reserved' => 0,
                            'quarantine' => 0,
                            'damaged' => 0,
                        ]
                    );

                    InventoryLog::create([
                        'warehouse_id' => $defect->warehouse_id,
                        'variant_id' => $defect->variant_id,
                        'action' => 'ADJUSTMENT',
                        'quantity_before' => 0,
                        'quantity_change' => $defect->quantity,
                        'quantity_after' => $defect->quantity,
                        'reference_type' => 'defect_assessment',
                        'reference_id' => $defect->id,
                        'user_id' => Auth::id(),
                        'notes' => 'Tạo lô sửa chữa ' . $newBatchNumber . ' - Chi phí: ' . ($validated['repair_cost'] ?? 0),
                    ]);
                    
                    $stock = WarehouseStock::where('warehouse_id', $defect->warehouse_id)
                        ->where('variant_id', $defect->variant_id)
                        ->where('batch_number', $defect->batch_number)
                        ->first();

                    if ($stock && $stock->damaged >= $defect->quantity) {
                        $quantityBefore = $stock->damaged;
                        $stock->decrement('damaged', $defect->quantity);
                        $stock->decrement('on_hand', $defect->quantity);

                        InventoryLog::create([
                            'warehouse_id' => $defect->warehouse_id,
                            'variant_id' => $defect->variant_id,
                            'action' => 'ADJUSTMENT',
                            'quantity_before' => $quantityBefore,
                            'quantity_change' => -$defect->quantity,
                            'quantity_after' => $stock->damaged,
                            'reference_type' => 'defect_assessment',
                            'reference_id' => $defect->id,
                            'user_id' => Auth::id(),
                            'notes' => 'Xóa hàng hỏng từ lô ' . $defect->batch_number,
                        ]);
                    }
                } elseif ($defect->classification === 'B-GRADE') {
                    $variant = ProductVariant::findOrFail($defect->variant_id);
                    $salePrice = $variant->selling_price ?? 0;
                    $bgradePrice = (int)($salePrice * 0.8);

                    $invoice = StockOutInvoice::create([
                        'invoice_number' => 'BGRADE-' . $defect->id . '-' . now()->format('YmdHis'),
                        'warehouse_id' => $defect->warehouse_id,
                        'type' => 'CLEARANCE',
                        'total_amount' => $bgradePrice * $defect->quantity,
                        'status' => 'PENDING',
                        'created_by' => Auth::id(),
                        'notes' => 'Thanh lý từ lô ' . $defect->batch_number,
                    ]);

                    StockOutInvoiceItem::create([
                        'stock_out_invoice_id' => $invoice->id,
                        'variant_id' => $defect->variant_id,
                        'quantity' => $defect->quantity,
                        'unit_price' => $bgradePrice,
                        'line_total' => $bgradePrice * $defect->quantity,
                        'defect_assessment_id' => $defect->id,
                    ]);

                    InventoryLog::create([
                        'warehouse_id' => $defect->warehouse_id,
                        'variant_id' => $defect->variant_id,
                        'action' => 'OUT',
                        'quantity_before' => 0,
                        'quantity_change' => $defect->quantity,
                        'quantity_after' => 0,
                        'reference_type' => 'defect_assessment',
                        'reference_id' => $defect->id,
                        'user_id' => Auth::id(),
                        'notes' => 'Tạo đơn thanh lý ' . $invoice->invoice_number . ' - Giá: ' . $bgradePrice . '/cái',
                    ]);
                    
                    $stock = WarehouseStock::where('warehouse_id', $defect->warehouse_id)
                        ->where('variant_id', $defect->variant_id)
                        ->where('batch_number', $defect->batch_number)
                        ->first();

                    if ($stock && $stock->damaged >= $defect->quantity) {
                        $quantityBefore = $stock->damaged;
                        $stock->decrement('damaged', $defect->quantity);
                        $stock->decrement('on_hand', $defect->quantity);

                        InventoryLog::create([
                            'warehouse_id' => $defect->warehouse_id,
                            'variant_id' => $defect->variant_id,
                            'action' => 'ADJUSTMENT',
                            'quantity_before' => $quantityBefore,
                            'quantity_change' => -$defect->quantity,
                            'quantity_after' => $stock->damaged,
                            'reference_type' => 'defect_assessment',
                            'reference_id' => $defect->id,
                            'user_id' => Auth::id(),
                            'notes' => 'Xóa hàng hỏng từ lô ' . $defect->batch_number,
                        ]);
                    }
                } else {
                    InventoryLog::create([
                        'warehouse_id' => $defect->warehouse_id,
                        'variant_id' => $defect->variant_id,
                        'action' => 'OUT',
                        'quantity_before' => 0,
                        'quantity_change' => -$defect->quantity,
                        'quantity_after' => 0,
                        'reference_type' => 'defect_assessment',
                        'reference_id' => $defect->id,
                        'user_id' => Auth::id(),
                        'notes' => 'Tiêu hủy hàng hỏng từ lô ' . $defect->batch_number,
                    ]);
                    
                    $stock = WarehouseStock::where('warehouse_id', $defect->warehouse_id)
                        ->where('variant_id', $defect->variant_id)
                        ->where('batch_number', $defect->batch_number)
                        ->first();

                    if ($stock && $stock->damaged >= $defect->quantity) {
                        $quantityBefore = $stock->damaged;
                        $stock->decrement('damaged', $defect->quantity);
                        $stock->decrement('on_hand', $defect->quantity);

                        InventoryLog::create([
                            'warehouse_id' => $defect->warehouse_id,
                            'variant_id' => $defect->variant_id,
                            'action' => 'ADJUSTMENT',
                            'quantity_before' => $quantityBefore,
                            'quantity_change' => -$defect->quantity,
                            'quantity_after' => $stock->damaged,
                            'reference_type' => 'defect_assessment',
                            'reference_id' => $defect->id,
                            'user_id' => Auth::id(),
                            'notes' => 'Xóa hàng hỏng từ lô ' . $defect->batch_number,
                        ]);
                    }
                }

                $defect->update([
                    'repair_cost' => $validated['repair_cost'] ?? 0,
                    'status' => 'APPROVED',
                    'approved_by' => Auth::id(),
                ]);
            });

            return back()->with('success', 'Duyệt đánh giá thành công');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, $id)
    {
        try {
            $defect = DefectAssessment::findOrFail($id);
            
            if ($defect->status !== 'APPROVED') {
                return back()->with('error', 'Chỉ có thể hoàn thành khi ở trạng thái APPROVED');
            }

            $defect->update([
                'status' => 'COMPLETED',
                'completed_by' => Auth::id(),
            ]);

            InventoryLog::create([
                'warehouse_id' => $defect->warehouse_id,
                'variant_id' => $defect->variant_id,
                'action' => 'ADJUSTMENT',
                'quantity_before' => 0,
                'quantity_change' => 0,
                'quantity_after' => 0,
                'reference_type' => 'defect_assessment',
                'reference_id' => $defect->id,
                'user_id' => Auth::id(),
                'notes' => 'Hoàn thành xử lý defect từ lô ' . $defect->batch_number,
            ]);

            return back()->with('success', 'Hoàn thành xử lý defect thành công');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            $defect = DefectAssessment::findOrFail($id);
            
            if ($defect->status !== 'PENDING') {
                return back()->with('error', 'Chỉ có thể từ chối khi ở trạng thái PENDING');
            }

            $defect->update([
                'status' => 'REJECTED',
                'rejection_reason' => $validated['rejection_reason'],
                'rejected_by' => Auth::id(),
            ]);

            InventoryLog::create([
                'warehouse_id' => $defect->warehouse_id,
                'variant_id' => $defect->variant_id,
                'action' => 'ADJUSTMENT',
                'quantity_before' => 0,
                'quantity_change' => 0,
                'quantity_after' => 0,
                'reference_type' => 'defect_assessment',
                'reference_id' => $defect->id,
                'user_id' => Auth::id(),
                'notes' => 'Từ chối: ' . $validated['rejection_reason'],
            ]);

            return back()->with('success', 'Từ chối đánh giá thành công');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
