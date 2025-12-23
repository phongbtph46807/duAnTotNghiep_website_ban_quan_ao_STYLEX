<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockInRequest;
use App\Models\TransferRequest;
use App\Models\CountRequest;
use App\Models\DefectAssessment;
use App\Models\ReturnRequest;
use App\Models\StockOutInvoice;
use App\Models\StockOutInvoiceItem;
use App\Models\Warehouse;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class WarehouseTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 1)->first() ?? User::factory()->create(['role' => 1]);
        $staff = User::where('role', 2)->first() ?? User::factory()->create(['role' => 2]);
        $warehouseManager = User::where('role', 3)->first() ?? User::factory()->create(['role' => 3]);

        $warehouses = Warehouse::all();
        if ($warehouses->isEmpty()) {
            $this->call(WarehouseSeeder::class);
            $warehouses = Warehouse::all();
        }

        $variants = ProductVariant::limit(10)->get();
        if ($variants->isEmpty()) {
            $this->call(ProductSeeder::class);
            $variants = ProductVariant::limit(10)->get();
        }

        // 1. NHẬP KHO (Stock In)
        $this->seedStockIn($warehouses, $variants, $admin, $warehouseManager);

        // 3. CHUYỂN KHO (Transfer)
        $this->seedTransfer($warehouses, $variants, $admin, $warehouseManager);

        // 4. KIỂM KÊ (Count)
        $this->seedCount($warehouses, $variants, $admin, $warehouseManager);

        // 5. HÀNG HỎI (Defect Assessment)
        $this->seedDefectAssessment($warehouses, $variants, $admin, $warehouseManager);

        // 6. TRẢ/ĐỔI HÀNG (Return/Exchange)
        $this->seedReturn($admin, $staff);

        // 7. HÓA ĐƠN THANH LÝ (Stock Out Invoice)
        $this->seedStockOutInvoice($warehouses, $variants, $admin, $warehouseManager);

        $this->command->info('✓ Warehouse transactions seeded successfully');
    }

    private function seedStockIn($warehouses, $variants, $admin, $warehouseManager): void
    {
        $warehouse = $warehouses->first();

        for ($i = 1; $i <= 3; $i++) {
            $variant = $variants->random();

            StockInRequest::create([
                'warehouse_id' => $warehouse->id,
                'variant_id' => $variant->id,
                'batch_number' => 'BATCH-IN-' . date('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'quantity' => rand(50, 200),
                'cost_price' => $variant->product->price * 0.6,
                'received_date' => Carbon::now()->subDays(rand(1, 10)),
                'supplier_name' => 'Nhà cung cấp ' . $i,
                'supplier_contact' => '0' . rand(100000000, 999999999),
                'invoice_number' => 'INV-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'status' => ['PENDING', 'QC_PASSED', 'CONFIRMED'][rand(0, 2)],
                'created_by' => $warehouseManager->id,
                'confirmed_by' => rand(0, 1) ? $admin->id : null,
                'confirmed_at' => rand(0, 1) ? Carbon::now() : null,
                'notes' => 'Nhập kho từ nhà cung cấp - Lô hàng ' . $i,
            ]);
        }

        $this->command->info('  ✓ Stock In: 3 records');
    }

    private function seedTransfer($warehouses, $variants, $admin, $warehouseManager): void
    {
        if ($warehouses->count() < 2) {
            $this->command->info('  ⚠ Transfer: Cần ít nhất 2 kho');
            return;
        }

        $fromWarehouse = $warehouses->first();
        $toWarehouse = $warehouses->skip(1)->first();

        for ($i = 1; $i <= 3; $i++) {
            $variant = $variants->random();

            TransferRequest::create([
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $toWarehouse->id,
                'variant_id' => $variant->id,
                'quantity' => rand(20, 100),
                'status' => ['PENDING', 'OUT_CONFIRMED', 'IN_CONFIRMED', 'COMPLETED'][rand(0, 3)],
                'created_by' => $warehouseManager->id,
                'out_confirmed_by' => rand(0, 1) ? $admin->id : null,
                'in_confirmed_by' => rand(0, 1) ? $admin->id : null,
                'notes' => 'Chuyển kho từ ' . $fromWarehouse->name . ' sang ' . $toWarehouse->name,
            ]);
        }

        $this->command->info('  ✓ Transfer: 3 records');
    }

    private function seedCount($warehouses, $variants, $admin, $warehouseManager): void
    {
        $warehouse = $warehouses->first();

        for ($i = 1; $i <= 3; $i++) {
            $variant = $variants->random();
            $systemQty = rand(100, 500);
            $physicalQty = $systemQty + rand(-10, 10);

            CountRequest::create([
                'warehouse_id' => $warehouse->id,
                'variant_id' => $variant->id,
                'system_qty' => $systemQty,
                'available_qty' => rand(50, $systemQty),
                'reserved_qty' => rand(10, 50),
                'quarantine_qty' => rand(0, 20),
                'damaged_qty' => rand(0, 10),
                'physical_qty' => $physicalQty,
                'difference' => $physicalQty - $systemQty,
                'status' => ['PENDING', 'COUNTED', 'CONFIRMED'][rand(0, 2)],
                'created_by' => $warehouseManager->id,
                'counted_by' => rand(0, 1) ? $admin->id : null,
                'confirmed_by' => rand(0, 1) ? $admin->id : null,
                'notes' => 'Kiểm kê định kỳ - Lô ' . $i,
            ]);
        }

        $this->command->info('  ✓ Count: 3 records');
    }

    private function seedDefectAssessment($warehouses, $variants, $admin, $warehouseManager): void
    {
        $warehouse = $warehouses->first();

        for ($i = 1; $i <= 3; $i++) {
            $variant = $variants->random();
            $classification = ['REWORK', 'SCRAP'][rand(0, 1)];

            DefectAssessment::create([
                'warehouse_id' => $warehouse->id,
                'variant_id' => $variant->id,
                'quantity' => rand(5, 20),
                'defect_level' => ['LOW', 'MEDIUM', 'HIGH'][rand(0, 2)],
                'defect_type' => ['Lỗi may', 'Lỗi vải', 'Lỗi màu', 'Lỗi kích cỡ'][rand(0, 3)],
                'defect_description' => 'Mô tả lỗi chi tiết - Lô ' . $i,
                'classification' => $classification,
                'repair_cost' => rand(10000, 50000),
                'material_cost' => rand(5000, 20000),
                'status' => ['PENDING', 'APPROVED', 'COMPLETED', 'REJECTED'][rand(0, 3)],
                'created_by' => $warehouseManager->id,
                'assessed_by' => rand(0, 1) ? $admin->id : null,
                'approved_by' => rand(0, 1) ? $admin->id : null,
                'completed_by' => rand(0, 1) ? $admin->id : null,
                'notes' => 'Đánh giá hàng hỏng - Phân loại: ' . $classification,
            ]);
        }

        $this->command->info('  ✓ Defect Assessment: 3 records');
    }

    private function seedReturn($admin, $staff): void
    {
        $orders = Order::limit(3)->get();
        if ($orders->isEmpty()) {
            $this->command->info('  ⚠ Return: Cần có đơn hàng');
            return;
        }

        foreach ($orders as $order) {
            $orderItems = $order->items()->limit(2)->get();
            if ($orderItems->isEmpty()) continue;

            $returnRequest = ReturnRequest::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'rma_number' => 'RMA-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'type' => ['RETURN', 'EXCHANGE'][rand(0, 1)],
                'reason' => ['DEFECTIVE', 'NOT_AS_DESCRIBED', 'WRONG_SIZE', 'WRONG_COLOR'][rand(0, 3)],
                'reason_description' => 'Lý do trả/đổi hàng chi tiết',
                'status' => ['PENDING', 'APPROVED', 'RECEIVED', 'QC_PASSED', 'COMPLETED'][rand(0, 4)],
                'approved_by' => rand(0, 1) ? $admin->id : null,
                'received_by' => rand(0, 1) ? $admin->id : null,
                'qc_by' => rand(0, 1) ? $admin->id : null,
                'approved_at' => rand(0, 1) ? Carbon::now() : null,
                'received_at' => rand(0, 1) ? Carbon::now() : null,
                'qc_at' => rand(0, 1) ? Carbon::now() : null,
                'qc_notes' => 'Ghi chú QC',
                'notes' => 'Ghi chú trả/đổi hàng',
            ]);

            foreach ($orderItems as $item) {
                $returnRequest->items()->create([
                    'order_item_id' => $item->id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'condition' => ['UNOPENED', 'OPENED', 'DAMAGED'][rand(0, 2)],
                    'item_notes' => 'Ghi chú sản phẩm',
                ]);
            }
        }

        $this->command->info('  ✓ Return/Exchange: ' . $orders->count() . ' records');
    }

    private function seedStockOutInvoice($warehouses, $variants, $admin, $warehouseManager): void
    {
        $warehouse = $warehouses->first();

        for ($i = 1; $i <= 3; $i++) {
            $type = ['NORMAL', 'CLEARANCE', 'RETURN'][rand(0, 2)];
            $totalAmount = 0;

            $invoice = StockOutInvoice::create([
                'invoice_number' => 'INV-OUT-' . date('Ymd') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'warehouse_id' => $warehouse->id,
                'type' => $type,
                'total_amount' => 0,
                'status' => ['PENDING', 'COMPLETED'][rand(0, 1)],
                'created_by' => $warehouseManager->id,
                'completed_by' => rand(0, 1) ? $admin->id : null,
                'completed_at' => rand(0, 1) ? Carbon::now() : null,
                'notes' => 'Hóa đơn thanh lý - Loại: ' . $type,
            ]);

            // Thêm items
            for ($j = 1; $j <= rand(2, 4); $j++) {
                $variant = $variants->random();
                $quantity = rand(5, 30);
                $unitPrice = $type === 'CLEARANCE' ? intval($variant->product->price * 0.7) : $variant->product->price;
                $lineTotal = $quantity * $unitPrice;
                $totalAmount += $lineTotal;

                $invoice->items()->create([
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $invoice->update(['total_amount' => $totalAmount]);
        }

        $this->command->info('  ✓ Stock Out Invoice: 3 records');
    }
}
