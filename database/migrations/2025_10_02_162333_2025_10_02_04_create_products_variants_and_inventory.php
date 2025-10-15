<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        // Lịch sử nhập/xuất kho
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            // Tạm thời là unsignedBigInteger
            $table->unsignedBigInteger('user_id')->nullable()->comment('Người thực hiện thao tác'); 
            $table->enum('type', ['IMPORT', 'SALE', 'RETURN', 'ADJUSTMENT', 'TRANSFER'])->comment('Loại giao dịch tồn kho');
            $table->integer('quantity_change')->comment('Số lượng thay đổi (dương cho nhập, âm cho xuất)');
            $table->string('reference_type', 50)->nullable()->comment('Loại đối tượng tham chiếu');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('inventory_logs');
    }
};