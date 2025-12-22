<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('to_warehouse_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('quantity');
            $table->enum('status', ['PENDING', 'OUT_CONFIRMED', 'QC_CHECKING', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('out_confirmed_by')->nullable();
            $table->unsignedBigInteger('in_confirmed_by')->nullable();
            $table->unsignedBigInteger('qc_confirmed_by')->nullable();
            $table->string('batch_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['from_warehouse_id', 'status']);
            $table->index(['to_warehouse_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
