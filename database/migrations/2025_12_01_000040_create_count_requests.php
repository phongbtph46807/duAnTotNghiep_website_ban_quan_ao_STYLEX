<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('count_requests');

        Schema::create('count_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('system_qty');
            $table->integer('available_qty')->nullable();
            $table->integer('reserved_qty')->nullable();
            $table->integer('quarantine_qty')->nullable();
            $table->integer('damaged_qty')->nullable();
            $table->integer('physical_qty')->nullable();
            $table->integer('difference')->nullable();
            $table->enum('status', ['PENDING', 'COUNTED', 'CONFIRMED', 'CANCELLED'])->default('PENDING');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('counted_by')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['warehouse_id', 'status']);
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('counted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('count_requests');
    }
};
