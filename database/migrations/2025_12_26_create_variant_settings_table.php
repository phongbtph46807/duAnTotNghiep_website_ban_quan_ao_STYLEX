<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('min_stock_level')->default(10);
            $table->integer('max_stock_level')->nullable();
            $table->integer('reorder_point')->nullable();
            $table->integer('reorder_quantity')->nullable();
            $table->boolean('enable_low_stock_alert')->default(true);
            $table->timestamps();
            $table->unique('variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_settings');
    }
};
