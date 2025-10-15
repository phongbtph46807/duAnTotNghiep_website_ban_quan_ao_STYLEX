<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        // 1. Sản phẩm
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name', 200); 
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title', 160)->nullable();
            $table->timestamps();
        });
        // 2. Biến thể (SKU)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku', 100)->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable()->comment('Giá khuyến mãi');
            $table->integer('weight_grams')->default(0);
            $table->foreignId('color_id')->constrained('colors');
            $table->foreignId('size_id')->constrained('sizes');
            $table->foreignId('texture_id')->constrained('textures');
            $table->unique(['product_id', 'color_id', 'size_id', 'texture_id'], 'variant_unique');
            $table->timestamps();
        });
        // 3. Ảnh theo Biến thể (Pivot)
        Schema::create('variant_media', function (Blueprint $table) {
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            $table->boolean('is_thumbnail')->default(false);
            $table->integer('display_order')->default(0);
            $table->primary(['variant_id', 'media_id']);
        });
        // 4. Tồn kho & Giá vốn
        Schema::create('variant_inventory', function (Blueprint $table) {
            $table->foreignId('variant_id')->primary()->constrained('product_variants')->onDelete('cascade');
            $table->integer('current_stock')->default(0);
            $table->integer('safety_stock')->default(0)->comment('Mức tồn kho an toàn để cảnh báo');
            $table->decimal('weighted_avg_cost', 10, 2)->default(0)->comment('Giá vốn bình quân');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('variant_inventory');
        Schema::dropIfExists('variant_media');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};