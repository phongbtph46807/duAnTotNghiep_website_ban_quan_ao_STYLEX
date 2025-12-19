<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_qc_results', function (Blueprint $table) {
            $table->id();
            $table->string('request_type', 50); // STOCK_IN, STOCK_OUT
            $table->unsignedBigInteger('request_id');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('total_qty');
            $table->integer('passed_qty');
            $table->integer('failed_qty');
            $table->foreignId('qc_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('qc_at')->nullable();
            $table->text('qc_notes')->nullable();
            $table->timestamps();
            
            $table->index(['request_type', 'request_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_qc_results');
    }
};
