<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Hồ sơ người dùng (Tạo bảng, KHÔNG thêm FK cho user_id ngay)
        // Dùng unsignedBigInteger để tạo cột, sau đó thêm FK ở bước 4.
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary(); 
            $table->string('phone_number', 15)->nullable();
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->foreignId('current_tier_id')->nullable()->constrained('loyalty_tiers')->onDelete('set null');
            $table->date('tier_expiry_date')->nullable();
            $table->timestamps();
        });

        // 2. ACL Pivots (Tạo bảng, KHÔNG thêm FK cho user_id và role_id ngay)
        Schema::create('role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['role_id', 'user_id']);
        });
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });

        // 3. CẬP NHẬT BẢNG USERS (ALTER TABLE USERS) - An toàn khi file users đã được đổi tên (B1)
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email_verified_at');
            $table->decimal('salary', 10, 2)->nullable()->comment('Lương cơ bản hàng tháng');
            $table->date('hire_date')->nullable();
        });

        // 4. THÊM FOREIGN KEY BỊ TÁCH VÀ BỊ THIẾU (Tất cả phải dùng Schema::table)
        
        // Thêm FK cho user_profiles
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        // Thêm FK cho ACL Pivots
        Schema::table('role_user', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('permission_role', function (Blueprint $table) {
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        // 5. Thêm các Foreign Key còn lại (giống như logic cũ)
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
        Schema::table('user_vouchers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('admin_handler_id')->references('id')->on('users')->onDelete('restrict');
        });
        Schema::table('user_spins', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('author_id')->references('id')->on('users')->onDelete('restrict');
        });
        Schema::table('admin_reports', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Drop cột đã thêm vào bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'salary', 'hire_date']);
        });
        
        // Drop tất cả các Foreign Key (Đảo ngược thứ tự add)
        Schema::table('admin_reports', function (Blueprint $table) { $table->dropForeign(['user_id']); });
        Schema::table('posts', function (Blueprint $table) { $table->dropForeign(['author_id']); });
        Schema::table('reviews', function (Blueprint $table) { $table->dropForeign(['user_id']); });
        Schema::table('user_spins', function (Blueprint $table) { $table->dropForeign(['user_id']); });
        Schema::table('orders', function (Blueprint $table) { 
            $table->dropForeign(['user_id']);
            $table->dropForeign(['admin_handler_id']);
        });
        Schema::table('carts', function (Blueprint $table) { $table->dropForeign(['user_id']); });
        Schema::table('user_vouchers', function (Blueprint $table) { $table->dropForeign(['user_id']); });
        Schema::table('inventory_logs', function (Blueprint $table) { $table->dropForeign(['user_id']); });
        
        // Drop FKs của các bảng vừa tạo
        Schema::table('permission_role', function (Blueprint $table) { $table->dropForeign(['permission_id']); $table->dropForeign(['role_id']); });
        Schema::table('role_user', function (Blueprint $table) { $table->dropForeign(['role_id']); $table->dropForeign(['user_id']); });
        Schema::table('user_profiles', function (Blueprint $table) { $table->dropForeign(['user_id']); });

        // Drop các bảng Pivot và Profile
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('user_profiles');
    }
};