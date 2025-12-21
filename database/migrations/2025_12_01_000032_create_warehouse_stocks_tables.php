<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('on_hand')->default(0)->comment('Tồn kho thực tế');
            $table->integer('available')->default(0)->comment('Sẵn sàng bán');
            $table->integer('reserved')->default(0)->comment('Đã đặt hàng');
            $table->integer('quarantine')->default(0)->comment('Chờ QC');
            $table->integer('damaged')->default(0)->comment('Hỏng');
            $table->timestamps();
            $table->unique(['warehouse_id', 'variant_id']);
            $table->index(['warehouse_id', 'available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_stocks');
    }
};
