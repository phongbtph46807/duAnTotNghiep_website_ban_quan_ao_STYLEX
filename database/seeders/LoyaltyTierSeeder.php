<?php

namespace Database\Seeders;

use App\Models\LoyaltyTier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoyaltyTierSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ trước khi seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LoyaltyTier::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Định nghĩa các cấp độ với ngưỡng chi tiêu và ưu đãi tương ứng
        $tiers = [
            // Cấp độ khởi điểm
            ['name' => 'Đồng', 'min_spend_required' => 0, 'discount_rate' => 0.0, 'color' => '#8B4513', 'text_color' => '#ffffff'],

            // Các cấp độ trung bình
            ['name' => 'Bạc', 'min_spend_required' => 5000000, 'discount_rate' => 5.0, 'color' => '#c0c0c0', 'text_color' => '#ffffff'],
            ['name' => 'Vàng', 'min_spend_required' => 15000000, 'discount_rate' => 10.0, 'color' => '#ffd700', 'text_color' => '#000000'],

            // Các cấp độ cao cấp (VIP)
            ['name' => 'Bạch Kim', 'min_spend_required' => 35000000, 'discount_rate' => 15.0, 'color' => '#e5e4e2', 'text_color' => '#000000'],
            ['name' => 'Kim Cương', 'min_spend_required' => 80000000, 'discount_rate' => 20.0, 'color' => '#b9f2ff', 'text_color' => '#000000'],
        ];

        foreach ($tiers as $tierData) {
            LoyaltyTier::create($tierData);
        }
    }
}
