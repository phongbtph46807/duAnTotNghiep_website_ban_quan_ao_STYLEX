<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ trước khi seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (Schema::hasTable('colors')) {
            DB::table('colors')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $colors = [
            ['name' => 'Đen', 'hex_code' => '#000000', 'status' => 1],
            ['name' => 'Trắng', 'hex_code' => '#FFFFFF', 'status' => 1],
            ['name' => 'Xám', 'hex_code' => '#808080', 'status' => 1],
            ['name' => 'Xanh Navy', 'hex_code' => '#000080', 'status' => 1],
            ['name' => 'Xanh Dương', 'hex_code' => '#0000FF', 'status' => 1],
            ['name' => 'Đỏ', 'hex_code' => '#FF0000', 'status' => 1],
            ['name' => 'Hồng', 'hex_code' => '#FFC0CB', 'status' => 1],
            ['name' => 'Vàng', 'hex_code' => '#FFFF00', 'status' => 1],
            ['name' => 'Cam', 'hex_code' => '#FFA500', 'status' => 1],
            ['name' => 'Xanh Lá', 'hex_code' => '#008000', 'status' => 1],
            ['name' => 'Nâu', 'hex_code' => '#A52A2A', 'status' => 1],
            ['name' => 'Be', 'hex_code' => '#F5F5DC', 'status' => 1],
        ];

        foreach ($colors as $color) {
            DB::table('colors')->insert([
                'name' => $color['name'],
                'hex_code' => $color['hex_code'],
                'status' => $color['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Đã tạo ' . count($colors) . ' colors.');
    }
}
