<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Chỉ thêm nếu cột chưa tồn tại
            if (!Schema::hasColumn('carts', 'variant_id')) {
                $table->foreignId('variant_id')
                    ->nullable()
                    ->constrained('product_variants')
                    ->onDelete('cascade');
            }

            // Xóa unique constraint cũ (nếu tồn tại)
            try {
                $table->dropUnique('unique_cart_item');
            } catch (\Exception $e) {
                // bỏ qua nếu chưa tồn tại
            }

            // Thêm unique constraint mới
            try {
                $table->unique(['user_id', 'variant_id'], 'unique_cart_variant');
            } catch (\Exception $e) {
                // bỏ qua nếu đã tồn tại
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Xóa constraint mới
            try {
                $table->dropUnique('unique_cart_variant');
            } catch (\Exception $e) {
                // bỏ qua nếu chưa có
            }

            // Chỉ xóa nếu cột tồn tại
            if (Schema::hasColumn('carts', 'variant_id')) {
                try {
                    $table->dropForeign(['variant_id']);
                } catch (\Exception $e) {
                    // bỏ qua nếu chưa có foreign key
                }

                $table->dropColumn('variant_id');
            }

            // Thêm lại unique cũ
            try {
                $table->unique(['user_id', 'product_id', 'size', 'color'], 'unique_cart_item');
            } catch (\Exception $e) {
                // bỏ qua nếu đã tồn tại
            }
        });
    }
};

