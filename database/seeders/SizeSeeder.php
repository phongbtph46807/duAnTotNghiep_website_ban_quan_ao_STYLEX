<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ trước khi seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (Schema::hasTable('sizes')) {
            DB::table('sizes')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sizes = [
            ['name' => 'S', 'description' => 'Small', 'status' => 1],
            ['name' => 'M', 'description' => 'Medium', 'status' => 1],
            ['name' => 'L', 'description' => 'Large', 'status' => 1],
            ['name' => 'XL', 'description' => 'Extra Large', 'status' => 1],
            ['name' => 'XXL', 'description' => 'Double Extra Large', 'status' => 1],
            ['name' => 'XS', 'description' => 'Extra Small', 'status' => 1],
        ];

        foreach ($sizes as $size) {
            DB::table('sizes')->insert([
                'name' => $size['name'],
                'description' => $size['description'],
                'status' => $size['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Đã tạo ' . count($sizes) . ' sizes.');
    }
}
