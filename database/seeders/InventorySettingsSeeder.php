<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class InventorySettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            'low_stock_threshold' => 10,
            'notify_low_stock' => 1,
            'notify_new_order' => 1,
            'notify_pending_approval' => 1,
            'notify_qc_failed' => 1,
            'notify_count_discrepancy' => 1,
            'notify_defect_found' => 1,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}