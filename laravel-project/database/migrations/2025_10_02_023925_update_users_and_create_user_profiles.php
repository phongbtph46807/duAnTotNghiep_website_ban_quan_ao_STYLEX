<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cập nhật Bảng Users (Thêm cột quản trị, lương)
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email_verified_at');
            $table->decimal('salary', 10, 2)->nullable()->comment('Lương cơ bản hàng tháng');
            $table->date('hire_date')->nullable();
        });

        // Hồ sơ người dùng (User Profile)
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->onDelete('cascade');
            $table->string('phone_number', 15)->nullable();
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->foreignId('current_tier_id')->nullable()->constrained('loyalty_tiers')->onDelete('set null');
            $table->date('tier_expiry_date')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('user_profiles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'salary', 'hire_date']);
        });
    }
};
