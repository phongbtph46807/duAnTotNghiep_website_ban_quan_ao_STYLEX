<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{DefectAssessment, Warehouse, ProductVariant, WarehouseStock, StockInRequest, StockOutInvoice, StockOutInvoiceItem, InventoryLog};
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class DefectAssessmentController extends Controller
{
    public function complete($id)
    {
        try {
            $defect = DefectAssessment::findOrFail($id);

            if ($defect->status !== 'APPROVED') {
                return redirect()->back()->with('error', 'Chi co the hoan thanh bao cao da duoc phe duyet!');
            }

            $data = request()->validate([
                'repair_cost' => 'nullable|integer|min:0',
                'material_cost' => 'nullable|integer|min:0',
                'other_cost' => 'nullable|integer|min:0',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $stock = WarehouseStock::where('warehouse_id', $defect->warehouse_id)
                ->where('variant_id', $defect->variant_id)
                ->first();

            if (!$stock || $stock->damaged < $defect->quantity) {
                DB::rollBack();
                return redirect()->back()->with('error', 'So luong hong trong kho khong du!');
            }

            $damagedBefore = $stock->damaged;
            $stock->decrement('damaged', $defect->quantity);
            
            InventoryLog::create([
                'warehouse_id' => $defect->warehouse_id,
                'variant_id' => $defect->variant_id,
                'action' => 'ADJUSTMENT',
                'quantity_before' => $damagedBefore,
                'quantity_change' => -$defect->quantity,
                'quantity_after' => $stock->damaged,
                'reference_type' => 'defect_assessment',
                'reference_id' => $defect->id,
                'user_id' => Auth::id(),
                'notes' => "Thanh ly hang hong: {$defect->classification}",
            ]);

            $stockInRequest = null;
            if ($defect->classification === 'SCRAP') {
                $this->createClearanceInvoice($defect, $data);
            } elseif ($defect->classification === 'B-GRADE') {
                $stock->update([
                    'clearance' => ($stock->clearance ?? 0) + $defect->quantity,
                ]);
                
                InventoryLog::create([
                    'warehouse_id' => $defect->warehouse_id,
                    'variant_id' => $defect->variant_id,
                    'action' => 'ADJUSTMENT',
                    'quantity_before' => $stock->on_hand,
                    'quantity_change' => 0,
                    'quantity_after' => $stock->on_hand,
                    'reference_type' => 'defect_assessment',
                    'reference_id' => $defect->id,
                    'user_id' => Auth::id(),
                    'notes' => "Chuyen hang B-GRADE tu damaged sang clearance",
                ]);
            } else {
                $totalCost = ($data['repair_cost'] ?? 0) + ($data['material_cost'] ?? 0) + ($data['other_cost'] ?? 0);
                $costPrice = $totalCost > 0 ? round($totalCost / $defect->quantity) : 0;

                $stockInRequest = StockInRequest::create([
                    'warehouse_id' => $defect->warehouse_id,
                    'variant_id' => $defect->variant_id,
                    'quantity' => $defect->quantity,
                    'batch_number' => 'REWORK-' . date('YmdHis'),
                    'cost_price' => $costPrice,
                    'received_date' => now()->toDateString(),
                    'status' => 'PENDING',
                    'created_by' => auth()->id(),
                    'notes' => "Sua chua tu lo hong - Phan loai: {$defect->classification}",
                ]);
            }

            $defect->update([
                'status' => 'COMPLETED',
                'completed_by' => auth()->id(),
                'repair_cost' => $data['repair_cost'] ?? 0,
                'material_cost' => $data['material_cost'] ?? 0,
                'notes' => $data['notes'] ?? $defect->notes,
                'stock_in_request_id' => $stockInRequest?->id,
            ]);

            DB::commit();

            return redirect()->route('admin.inventory.defect.index')
                ->with('success', 'Hoan thanh xu ly hang hong thanh cong!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Loi: ' . $e->getMessage());
        }
    }
}
