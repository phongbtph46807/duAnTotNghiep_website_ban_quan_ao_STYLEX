<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 10 banner mẫu
        for ($i = 1; $i <= 10; $i++) {
            DB::table('banners')->insert([
                'title'         => 'Banner ' . $i,
                'redirect_url'  => 'Banner-' . $i,
                'image'         => 'banners/sample' . $i . '.jpg',
                'content'       => 'Nội dung banner số ' . $i,
                'order'         => $i,
                'status'        => rand(0, 1),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
