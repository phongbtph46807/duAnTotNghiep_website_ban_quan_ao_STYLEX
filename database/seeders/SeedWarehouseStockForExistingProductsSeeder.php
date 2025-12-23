<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\ProductVariant;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class SeedWarehouseStockForExistingProductsSeeder extends Seeder
{
    /**
     * Seeder để nhập tồn kho cho các sản phẩm hiện có trong web
     * Không xóa dữ liệu cũ, chỉ thêm hoặc cập nhật tồn kho
     */
    public function run(): void
    {
        // Lấy tất cả kho vật lý đang hoạt động
        $warehouses = Warehouse::where('type', 'PHYSICAL')
            ->where('operational_status', 'ACTIVE')
            ->get();

        if ($warehouses->isEmpty()) {
            $this->command->warn('Không tìm thấy kho vật lý nào. Vui lòng chạy WarehouseSeeder trước!');
            return;
        }

        // Lấy tất cả biến thể sản phẩm hiện có
        $variants = ProductVariant::with('product')->get();

        if ($variants->isEmpty()) {
            $this->command->warn('Không tìm thấy sản phẩm nào trong hệ thống!');
            return;
        }

        $totalCreated = 0;
        $totalUpdated = 0;

        $this->command->info('Bắt đầu nhập tồn kho cho ' . $variants->count() . ' biến thể sản phẩm...');
        $this->command->newLine();

        foreach ($variants as $variant) {
            // Bỏ qua variant không có product
            if (!$variant->product) {
                continue;
            }

            foreach ($warehouses as $warehouse) {
                // Kiểm tra xem đã có tồn kho chưa
                $existingStock = WarehouseStock::where('warehouse_id', $warehouse->id)
                    ->where('variant_id', $variant->id)
                    ->first();

                if ($existingStock) {
                    // Nếu đã có tồn kho, cộng thêm số lượng mới vào tồn kho hiện có
                    $oldOnHand = $existingStock->on_hand;
                    $oldAvailable = $existingStock->available;
                    
                    // Nếu tồn kho hiện tại = 0, đặt lại thành số lượng mới thay vì cộng thêm
                    if ($oldAvailable == 0 && $oldOnHand == 0) {
                        $newQuantity = $this->generateStockQuantity($variant);
                        $newOnHand = $newQuantity;
                        $newAvailable = $newQuantity;
                        
                        $existingStock->update([
                            'on_hand' => $newOnHand,
                            'available' => $newAvailable,
                            'reserved' => 0,
                            'quarantine' => 0,
                            'damaged' => 0,
                            'clearance' => 0,
                        ]);
                        
                        $totalUpdated++;
                        $productName = $variant->product->name ?? 'N/A';
                        $this->command->info("✓ Đặt lại tồn kho (từ 0): {$productName} - {$variant->sku} tại {$warehouse->name} (0 → {$newOnHand})");
                    } else {
                        // Nếu đã có tồn kho > 0, cộng thêm
                        $newQuantity = $this->generateStockQuantity($variant);
                        $newOnHand = $oldOnHand + $newQuantity;
                        $newAvailable = $oldAvailable + $newQuantity;
                        
                        // Giữ nguyên reserved, quarantine, damaged, clearance
                        $existingStock->update([
                            'on_hand' => $newOnHand,
                            'available' => $newAvailable,
                            // Giữ nguyên các giá trị khác
                        ]);

                        $totalUpdated++;
                        $productName = $variant->product->name ?? 'N/A';
                        $this->command->info("✓ Cập nhật tồn kho: {$productName} - {$variant->sku} tại {$warehouse->name} ({$oldOnHand} + {$newQuantity} = {$newOnHand})");
                    }
                } else {
                    // Nếu chưa có tồn kho, tạo mới
                    $onHand = $this->generateStockQuantity($variant);
                    $available = $onHand;
                    $reserved = 0;

                    WarehouseStock::create([
                        'warehouse_id' => $warehouse->id,
                        'variant_id' => $variant->id,
                        'batch_number' => $this->generateBatchNumber(),
                        'location' => $this->generateLocation($warehouse),
                        'on_hand' => $onHand,
                        'available' => $available,
                        'reserved' => $reserved,
                        'quarantine' => 0,
                        'damaged' => 0,
                        'clearance' => 0,
                    ]);

                    $totalCreated++;
                    $productName = $variant->product->name ?? 'N/A';
                    $this->command->info("✓ Tạo mới tồn kho: {$productName} - {$variant->sku} tại {$warehouse->name} ({$onHand} sản phẩm)");
                }
            }
        }

        $this->command->newLine();
        $this->command->info("═══════════════════════════════════════════════════════════");
        $this->command->info("✓ Hoàn thành nhập tồn kho!");
        $this->command->info("  - Đã tạo mới: {$totalCreated} bản ghi");
        $this->command->info("  - Đã cập nhật: {$totalUpdated} bản ghi");
        $this->command->info("  - Tổng số kho: {$warehouses->count()}");
        $this->command->info("  - Tổng số biến thể: {$variants->count()}");
        $this->command->info("═══════════════════════════════════════════════════════════");
    }

    /**
     * Tạo số lượng tồn kho ngẫu nhiên dựa trên giá sản phẩm
     * Sản phẩm giá cao sẽ có ít tồn kho hơn
     */
    private function generateStockQuantity(ProductVariant $variant): int
    {
        $price = $variant->price ?? $variant->product->price ?? 0;

        if ($price >= 1000000) {
            // Sản phẩm cao cấp (>1 triệu): 10-50 sản phẩm
            return rand(10, 50);
        } elseif ($price >= 500000) {
            // Sản phẩm trung cấp (500k-1 triệu): 30-100 sản phẩm
            return rand(30, 100);
        } elseif ($price >= 200000) {
            // Sản phẩm phổ thông (200k-500k): 50-150 sản phẩm
            return rand(50, 150);
        } else {
            // Sản phẩm giá rẻ (<200k): 100-300 sản phẩm
            return rand(100, 300);
        }
    }

    /**
     * Tạo mã batch number
     */
    private function generateBatchNumber(): string
    {
        return 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Tạo vị trí lưu kho
     */
    private function generateLocation(Warehouse $warehouse): string
    {
        $zones = ['A', 'B', 'C', 'D'];
        $rows = range(1, 20);
        $shelves = range(1, 5);

        $zone = $zones[array_rand($zones)];
        $row = $rows[array_rand($rows)];
        $shelf = $shelves[array_rand($shelves)];

        return "{$zone}-{$row}-{$shelf}";
    }
}

