<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure warehouse_settings table exists for min_stock_level
        if (!Schema::hasTable('warehouse_settings')) {
            Schema::create('warehouse_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
                $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
                $table->integer('min_stock_level')->default(10);
                $table->integer('max_stock_level')->default(1000);
                $table->integer('reorder_point')->default(20);
                $table->integer('reorder_quantity')->default(100);
                $table->timestamps();
                
                $table->unique(['warehouse_id', 'variant_id']);
            });
        }

        // Add confirmed_at timestamp to stock_in_requests if not exists
        if (!Schema::hasColumn('stock_in_requests', 'confirmed_at')) {
            Schema::table('stock_in_requests', function (Blueprint $table) {
                $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stock_in_requests', 'confirmed_at')) {
            Schema::table('stock_in_requests', function (Blueprint $table) {
                $table->dropColumn('confirmed_at');
            });
        }
    }
};