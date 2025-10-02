<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Ghi nhật ký Nhập/Xuất kho (Audit Log)
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants');
            $table->foreignId('user_id')->constrained('users')->comment('Nhân viên thực hiện thao tác');
            $table->enum('type', ['IMPORT', 'EXPORT', 'ADJUSTMENT']);
            $table->integer('quantity_change');
            $table->decimal('cost_per_unit', 10, 2)->nullable()->comment('Giá nhập hàng');
            $table->string('reference')->nullable()->comment('Mã đơn nhập hàng, mã đơn bán hàng...');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_logs');
    }
};
