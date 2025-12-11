<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_out_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
                $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
                $table->string('batch_number')->unique();
                $table->integer('quantity');

                $table->enum('status', ['PENDING', 'QC_PASSED', 'QC_FAILED', 'CONFIRMED', 'CANCELLED'])->default('PENDING');

                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['warehouse_id', 'status']);
                $table->index(['variant_id', 'status']);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out_requests');
    }
};
