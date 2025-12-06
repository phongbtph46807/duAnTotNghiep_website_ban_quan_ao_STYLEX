<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Lấy tên foreign key constraints liên quan đến user_id và product_id
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'carts' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
            AND (COLUMN_NAME = 'user_id' OR COLUMN_NAME = 'product_id')
        ");
        
        // Drop tất cả foreign key liên quan
        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE `carts` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }
        
        // Drop unique constraint cũ
        DB::statement('ALTER TABLE `carts` DROP INDEX `unique_cart_item`');
        
        // Tạo lại foreign key cho user_id và product_id
        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        
        // Thêm variant_id
        Schema::table('carts', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable()->after('product_id');
        });
        
        // Tạo foreign key cho variant_id
        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
        
        // Tạo unique constraint mới với variant_id
        Schema::table('carts', function (Blueprint $table) {
            $table->unique(['user_id', 'variant_id'], 'unique_cart_variant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
            
            // Khôi phục unique constraint cũ
            $table->dropUnique('unique_cart_variant');
            $table->unique(['user_id', 'product_id', 'size', 'color'], 'unique_cart_item');
        });
    }
};
