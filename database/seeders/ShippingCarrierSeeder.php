<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingCarrier;

class ShippingCarrierSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Giao Hàng Nhanh (GHN)',       'code' => 'GHN'],
            ['name' => 'Giao Hàng Tiết Kiệm (GHTK)', 'code' => 'GHTK'],
            ['name' => 'Viettel Post',                'code' => 'VIETTEL'],
            ['name' => 'VNPost',                      'code' => 'VNPOST'],
            ['name' => 'J&T Express',                 'code' => 'JT'],
            ['name' => 'Ninja Van',                   'code' => 'NINJA'],
            ['name' => 'Best Express',                'code' => 'BEST'],
            ['name' => 'DHL',                         'code' => 'DHL'],
            ['name' => 'FedEx',                       'code' => 'FEDEX'],
            ['name' => 'UPS',                         'code' => 'UPS'],
        ];

        foreach ($rows as $row) {
            ShippingCarrier::updateOrCreate(
                ['code' => $row['code']],
                ['name' => $row['name'], 'active' => true]
            );
        }

        // Nếu muốn random thêm vài hãng giả lập:
        // ShippingCarrier::factory()->count(5)->create();
    }
}
