<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Chuyển cột status từ ENUM sang VARCHAR để cho phép trạng thái mới (cancel_request, return_request, delivered, shipping, returned)
        DB::statement("ALTER TABLE orders MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Quay lại enum gốc (có thể fail nếu đang có giá trị khác enum). Cân nhắc không rollback để tránh lỗi.
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};

