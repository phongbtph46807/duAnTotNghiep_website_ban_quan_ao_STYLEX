<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Giả sử bạn có ít nhất một category_id là 1
        $categoryId = 1;

        for ($i = 1; $i <= 10; $i++) {
            $name = "Sản phẩm demo $i";
            DB::table('products')->insert([
                'name' => $name,
                'category_id' => $categoryId,
                'slug' => Str::slug($name) . '-' . $i,
                'thumbnail' => 'uploads/products/demo' . $i . '.jpg',
                'is_active' => $i % 3 == 0 ? 1 : 0,
                'description' => "Mô tả cho sản phẩm demo $i",
                'is_featured' => $i % 3 == 0 ? 1 : 0,
                'meta_title' => "Meta title của sản phẩm demo $i",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
