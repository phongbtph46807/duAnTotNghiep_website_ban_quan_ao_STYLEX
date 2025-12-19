<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            ['name' => 'Kho Hà Nội', 'code' => 'HN', 'type' => 'PHYSICAL', 'operational_status' => 'ACTIVE', 'address' => 'Hà Nội'],
            ['name' => 'Kho TP HCM', 'code' => 'HCM', 'type' => 'PHYSICAL', 'operational_status' => 'ACTIVE', 'address' => 'TP HCM'],
            ['name' => 'Kho Đà Nẵng', 'code' => 'DN', 'type' => 'PHYSICAL', 'operational_status' => 'ACTIVE', 'address' => 'Đà Nẵng'],
            ['name' => 'Kho Ảo', 'code' => 'VIRTUAL', 'type' => 'VIRTUAL', 'operational_status' => 'ACTIVE', 'address' => null],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::updateOrCreate(
                ['code' => $warehouse['code']],
                $warehouse
            );
        }

        $this->command->info('✓ Created warehouses');
    }
}
