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
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            // lưu danh sách tag trải nghiệm người dùng tick, dạng JSON
            $table->json('tags')->nullable()->comment('Các tiêu chí được chọn: ["Form áo vừa vặn", "Màu sắc tươi sáng"]');

            // lưu trạng thái đánh giá (cho phép admin duyệt)
            $table->enum('status', ['public', 'hidden'])->default('public')->comment('Trạng thái hiển thị');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            //
        });
    }
};
