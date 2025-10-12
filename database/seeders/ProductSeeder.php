<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy brand và category đầu tiên để đảm bảo tồn tại
        $brand = Brand::first();
        $category = Category::first();

        // Nếu không có thì bỏ qua hoặc tạo nhanh
        if (!$brand || !$category) {
            $brand = Brand::create([
                'name' => 'Default Brand',
                'slug' => 'default-brand',
                'description' => 'Thương hiệu mặc định cho sản phẩm chưa có thương hiệu.',
                'logo' => 'brands/default.png',
            ]);

            $category = Category::create([
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
                'description' => 'Danh mục mặc định cho sản phẩm chưa phân loại.',
            ]);
        }

        Product::firstOrCreate(
            ['slug' => 'ao-thun-basic-cotton'], // điều kiện kiểm tra
            [ // dữ liệu nếu chưa tồn tại
                'sku' => 'SP001',
                'name' => 'Áo Thun Basic Cotton',
                'short_description' => 'Áo thun unisex cotton thoáng mát, năng động.',
                'description' => 'Áo thun basic phù hợp đi học, đi chơi, chất vải cotton thoáng mát và dễ phối đồ.',
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'default_image' => 'products/ao-thun-basic.jpg',
                'base_price' => 199000,
                'cost_price' => 120000,
                'total_stock' => 50,
                'weight' => 0.25,
                'is_active' => true,
                'visibility' => 'both',
                'additional' => json_encode([
                    'material' => 'cotton 100%',
                    'style' => 'unisex',
                    'made_in' => 'Vietnam'
                ]),
            ]
        );

        Product::firstOrCreate(
            ['slug' => 'quan-jean-nam-slimfit'],
            [
                'sku' => 'SP002',
                'name' => 'Quần Jean Nam Slimfit',
                'short_description' => 'Quần jean nam dáng slimfit hiện đại.',
                'description' => 'Thiết kế trẻ trung, dễ phối đồ, co giãn nhẹ phù hợp đi làm hoặc đi chơi.',
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'default_image' => 'products/quan-jean-nam.jpg',
                'base_price' => 399000,
                'cost_price' => 250000,
                'total_stock' => 30,
                'weight' => 0.75,
                'is_active' => true,
                'visibility' => 'both',
                'additional' => json_encode([
                    'material' => 'jean cotton co giãn',
                    'color' => 'xanh đậm',
                    'made_in' => 'Vietnam'
                ]),
            ]
        );
    }
}
