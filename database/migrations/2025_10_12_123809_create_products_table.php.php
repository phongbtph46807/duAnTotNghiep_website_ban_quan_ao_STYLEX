<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description', 512)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('default_image')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->integer('total_stock')->default(0);
            $table->decimal('weight', 8, 3)->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('visibility', ['hidden', 'catalog', 'search', 'both'])->default('both');
            $table->json('additional')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
