<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->integer('change'); // số lượng thay đổi (+10, -2)
            $table->string('reason')->nullable(); // ví dụ: order, return, adjust
            $table->unsignedBigInteger('reference_id')->nullable(); // ID đơn hàng hoặc phiếu nhập
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
