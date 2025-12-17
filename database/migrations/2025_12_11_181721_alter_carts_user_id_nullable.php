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
        // Lấy tên foreign key constraint liên quan đến user_id
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'carts' 
            AND COLUMN_NAME = 'user_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        // Drop foreign key constraint cũ
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `carts` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Bỏ qua nếu không tìm thấy
            }
        }

        // Sửa cột user_id thành nullable
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        // Tạo lại foreign key với nullable
        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Xóa foreign key
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {
                // Bỏ qua nếu không tồn tại
            }
        });

        // Xóa các bản ghi có user_id = NULL trước khi đổi lại thành NOT NULL
        DB::table('carts')->whereNull('user_id')->delete();

        // Đổi lại thành NOT NULL
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        // Tạo lại foreign key không nullable
        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
