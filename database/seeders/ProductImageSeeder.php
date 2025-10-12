<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::first();
        if (!$product) {
            $this->command->warn('⚠️ Không có product nào — hãy chạy ProductSeeder trước.');
            return;
        }

        $variant = ProductVariant::where('product_id', $product->id)->first();

        // Xoá dữ liệu cũ để tránh lỗi trùng
        // \Schema::disableForeignKeyConstraints();
        // ProductImage::truncate();
        // \Schema::enableForeignKeyConstraints();

        // Ảnh chính của sản phẩm
        ProductImage::create([
            'product_id' => $product->id,
            'variant_id' => null,
            'image_url' => $product->default_image ?? 'products/default.jpg',
            'sort_order' => 1,
            'is_main' => true,
        ]);

        // Ảnh phụ của sản phẩm
        ProductImage::create([
            'product_id' => $product->id,
            'variant_id' => null,
            'image_url' => 'products/ao-thun-basic-back.jpg',
            'sort_order' => 2,
            'is_main' => false,
        ]);

        // Ảnh của variant (nếu có)
        if ($variant) {
            ProductImage::create([
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'image_url' => 'products/variants/ao-thun-blue.jpg',
                'sort_order' => 1,
                'is_main' => false,
            ]);
        }
    }
}
