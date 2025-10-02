<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Blog / Nội dung
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->foreignId('author_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });

        // 2. Báo cáo Quản trị (Admin Reports - Thống kê Lương/Hiệu suất)
        Schema::create('admin_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('report_date')->comment('Ngày báo cáo, thường là ngày cuối tháng');
            
            // Chi phí nhân sự
            $table->decimal('total_salary_paid', 10, 2);
            $table->decimal('total_commission', 10, 2)->default(0);

            // Hiệu suất công việc
            $table->integer('orders_processed_count')->default(0);
            $table->integer('inventory_transactions_count')->default(0);

            $table->unique(['user_id', 'report_date']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_reports');
        Schema::dropIfExists('blogs');
    }
};
