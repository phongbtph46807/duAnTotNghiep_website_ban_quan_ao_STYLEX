<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_logs')) {
            Schema::create('inventory_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->unsignedBigInteger('variant_id')->nullable();
                $table->enum('action', ['IN', 'OUT', 'TRANSFER', 'ADJUSTMENT'])->default('IN');
                $table->integer('quantity_before')->default(0);
                $table->integer('quantity_change');
                $table->integer('quantity_after')->default(0);
                $table->string('reference_type')->nullable();
                $table->string('reference_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['warehouse_id', 'variant_id', 'created_at']);
                $table->index(['action', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
