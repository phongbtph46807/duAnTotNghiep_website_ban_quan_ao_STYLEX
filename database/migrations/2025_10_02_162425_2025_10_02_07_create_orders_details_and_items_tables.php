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
            $table->string('order_code', 50)->unique();
            // Tạm thời là unsignedBigInteger
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->unsignedBigInteger('admin_handler_id')->nullable()->comment('Nhân viên xử lý/xác nhận đơn hàng'); 
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->enum('status', ['PENDING', 'PROCESSING', 'SHIPPED', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->decimal('total_amount', 10, 2); 
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0)->comment('Tổng tiền thuế');
            $table->decimal('final_amount', 10, 2);
            $table->boolean('is_paid')->default(false)->comment('Trạng thái thanh toán nhanh');
            $table->timestamps();
        });
        // 2. Chi tiết đơn hàng
        Schema::create('order_details', function (Blueprint $table) {
            $table->foreignId('order_id')->primary()->constrained()->onDelete('cascade');
            $table->string('recipient_name', 100);
            $table->string('phone', 15);
            $table->string('address', 500);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        // 3. Các món hàng trong đơn
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('restrict');
            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('snapshot_price', 10, 2)->comment('Giá bán tại thời điểm đặt hàng');
            $table->decimal('snapshot_tax_rate', 5, 4)->default(0)->comment('Tỷ lệ thuế tại thời điểm đặt hàng');
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