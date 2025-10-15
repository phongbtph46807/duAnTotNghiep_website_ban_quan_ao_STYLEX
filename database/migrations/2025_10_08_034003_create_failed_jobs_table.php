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
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // ID duy nhất cho Job lỗi
            $table->text('connection');       // Kết nối đã dùng
            $table->text('queue');            // Hàng đợi đã dùng
            $table->longText('payload');      // Dữ liệu Job
            $table->longText('exception');    // Chi tiết lỗi
            $table->timestamp('failed_at')->useCurrent(); // Thời gian Job lỗi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};
