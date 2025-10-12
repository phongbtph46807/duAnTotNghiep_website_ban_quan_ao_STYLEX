<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Local Brand', 'description' => 'Thương hiệu thời trang Việt Nam', 'logo' => 'brands/local-brand.png'],
            ['name' => 'Nike', 'description' => 'Thương hiệu thể thao nổi tiếng toàn cầu', 'logo' => 'brands/nike.png'],
            ['name' => 'Adidas', 'description' => 'Thương hiệu thời trang thể thao đến từ Đức', 'logo' => 'brands/adidas.png'],
            ['name' => 'Uniqlo', 'description' => 'Thương hiệu thời trang Nhật Bản', 'logo' => 'brands/uniqlo.png'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brand['name'])],
                [
                    'name' => $brand['name'],
                    'description' => $brand['description'],
                    'logo' => $brand['logo'],
                ]
            );
        }
    }
}
