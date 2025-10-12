<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\InventoryLog;

class InventoryLogSeeder extends Seeder
{
    public function run(): void
    {
        $variant = ProductVariant::first();
        if (!$variant) {
            $this->command->warn('⚠️ Không có variant nào — hãy chạy ProductVariantSeeder trước.');
            return;
        }

        // \Schema::disableForeignKeyConstraints();
        // InventoryLog::truncate();
        // \Schema::enableForeignKeyConstraints();

        // Nhập kho ban đầu
        InventoryLog::create([
            'variant_id' => $variant->id,
            'change' => +50,
            'reason' => 'initial stock',
            'reference_id' => null,
        ]);

        // Bán hàng (xuất kho)
        InventoryLog::create([
            'variant_id' => $variant->id,
            'change' => -2,
            'reason' => 'order',
            'reference_id' => 101, // ví dụ: ID đơn hàng
        ]);

        // Nhập lại hàng lỗi (trả lại)
        InventoryLog::create([
            'variant_id' => $variant->id,
            'change' => +1,
            'reason' => 'return',
            'reference_id' => 102,
        ]);
    }
}
