<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Đơn hàng (Orders)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->foreignId('admin_handler_id')->nullable()->constrained('users')->comment('Nhân viên xử lý/xác nhận đơn hàng');

            $table->enum('status', ['PENDING', 'PROCESSING', 'SHIPPED', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->timestamps();
        });

        // 2. Chi tiết đơn hàng (Địa chỉ, Ghi chú)
        Schema::create('order_details', function (Blueprint $table) {
            $table->foreignId('order_id')->primary()->constrained()->onDelete('cascade');
            $table->string('recipient_name');
            $table->string('phone');
            $table->string('address');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Các món hàng trong đơn (Ghi giá lịch sử)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('snapshot_price', 10, 2)->comment('Giá bán tại thời điểm đặt hàng');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
    }
};
