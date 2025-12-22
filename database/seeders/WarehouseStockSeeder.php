<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseStockSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('warehouse_stocks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $warehouses = DB::table('warehouses')
            ->where('type', 'PHYSICAL')
            ->where('operational_status', 'ACTIVE')
            ->pluck('id')
            ->toArray();

        $variants = DB::table('product_variants')->pluck('id')->toArray();

        if (empty($warehouses) || empty($variants)) {
            $this->command->error('Thiếu dữ liệu: warehouses hoặc product_variants!');
            return;
        }

        foreach ($variants as $variantId) {
            foreach ($warehouses as $warehouseId) {
                $onHand = rand(50, 200);
                $available = $onHand - rand(0, 20);
                $reserved = $onHand - $available;

                DB::table('warehouse_stocks')->updateOrInsert(
                    [
                        'warehouse_id' => $warehouseId,
                        'variant_id' => $variantId,
                    ],
                    [
                        'on_hand' => $onHand,
                        'available' => $available,
                        'reserved' => $reserved,
                        'quarantine' => 0,
                        'damaged' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('✓ Đã nhập kho cho ' . count($variants) . ' biến thể vào ' . count($warehouses) . ' kho!');
    }
}
