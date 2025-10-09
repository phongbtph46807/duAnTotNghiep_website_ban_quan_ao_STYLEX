<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingCarrier;

class ShippingCarrierSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Giao Hàng Nhanh (GHN)',
            'Giao Hàng Tiết Kiệm (GHTK)',
            'Viettel Post',
            'VNPost',
            'J&T Express',
            'Ninja Van',
            'Best Express',
            'DHL',
            'FedEx',
            'UPS',
        ];

        foreach ($names as $name) {
            ShippingCarrier::firstOrCreate(['name' => $name]);
        }

        // Nếu muốn random thêm vài hãng giả lập:
        // ShippingCarrier::factory()->count(5)->create();
    }
}
