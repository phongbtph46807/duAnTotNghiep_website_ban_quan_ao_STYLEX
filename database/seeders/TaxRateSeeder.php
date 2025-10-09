<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxRate;

class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'VAT 0%',   'rate' => 0.00],
            ['name' => 'VAT 5%',   'rate' => 0.05],
            ['name' => 'VAT 8%',   'rate' => 0.08],
            ['name' => 'VAT 10%',  'rate' => 0.10],
            ['name' => 'Luxury 35%', 'rate' => 0.35],
        ];

        foreach ($data as $row) {
            TaxRate::updateOrCreate(
                ['name' => $row['name']],
                ['rate' => $row['rate']]
            );
        }

        // (Tuỳ chọn) thêm 10 bản ghi ngẫu nhiên từ factory
        // TaxRate::factory()->count(10)->create();
    }
}
