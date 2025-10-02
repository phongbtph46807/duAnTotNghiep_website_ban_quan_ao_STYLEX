<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Thanh toán
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
            $table->enum('method', ['COD', 'BANK_TRANSFER', 'VNPAY', 'ZALOPAY']);
            $table->decimal('amount', 10, 2);
            $table->string('transaction_code')->nullable();
            $table->enum('status', ['PENDING', 'PAID', 'FAILED', 'REFUNDED'])->default('PENDING');
            $table->timestamps();
        });

        // 2. Vận chuyển
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('shipping_carriers')->onDelete('restrict');
            $table->string('tracking_number')->nullable();
            $table->enum('status', ['PENDING', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED', 'RETURNED'])->default('PENDING');
            $table->timestamps();
        });

        // 3. Vòng quay (Gamification)
        Schema::create('spin_prizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['VOUCHER', 'LOYALTY_POINTS', 'NONE']);
            $table->string('value_reference')->nullable()->comment('FK voucher_id hoặc giá trị điểm');
            $table->decimal('probability', 5, 4)->comment('Xác suất quay trúng (0.0000 - 1.0000)');
            $table->timestamps();
        });

        // 4. Lịch sử lượt quay của người dùng
        Schema::create('user_spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('prize_id')->nullable()->constrained('spin_prizes')->onDelete('set null');
            $table->timestamp('spin_time');
            $table->boolean('is_claimed')->default(false);
            $table->timestamps();
        });

        // 5. Đánh giá sản phẩm
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1 đến 5 sao
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('user_spins');
        Schema::dropIfExists('spin_prizes');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('payments');
    }
};
