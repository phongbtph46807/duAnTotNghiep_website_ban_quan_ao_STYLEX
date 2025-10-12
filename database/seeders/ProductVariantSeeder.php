<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // Kiểm tra product tồn tại
        $product = Product::first();
        if (!$product) {
            $this->command->warn('⚠️ Không có product nào trong database, hãy chạy ProductSeeder trước.');
            return;
        }

        // Lấy ngẫu nhiên 1 color và size (hoặc null)
        $color = Color::first();
        $size = Size::first();

        // Xoá dữ liệu cũ để tránh trùng (an toàn khi dev)
        // \Schema::disableForeignKeyConstraints();
        // ProductVariant::truncate();
        // \Schema::enableForeignKeyConstraints();

        // Seed variant mặc định
        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'VAR-' . strtoupper(uniqid()),
            'color_id' => $color?->id,
            'size_id' => $size?->id,
            'price' => $product->base_price,
            'stock_quantity' => 10,
            'is_default' => true,
            'image' => $product->default_image,
            'attributes' => json_encode([
                'texture' => 'cotton mềm',
                'fit' => 'regular',
            ]),
        ]);

        // Seed thêm vài variant khác nhau
        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'VAR-' . strtoupper(uniqid()),
            'color_id' => $color?->id,
            'size_id' => $size?->id,
            'price' => $product->base_price + 50000,
            'stock_quantity' => 5,
            'is_default' => false,
            'image' => 'products/variant-blue.jpg',
            'attributes' => json_encode([
                'color_name' => 'Xanh navy',
                'material' => 'cotton co giãn',
            ]),
        ]);
    }
}
