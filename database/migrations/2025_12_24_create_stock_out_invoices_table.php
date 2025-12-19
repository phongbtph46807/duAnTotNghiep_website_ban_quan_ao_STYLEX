<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_out_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->enum('type', ['NORMAL', 'CLEARANCE', 'RETURN'])->default('NORMAL');
            $table->integer('total_amount');
            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('warehouse_id');
            $table->index('type');
            $table->index('status');
        });

        Schema::create('stock_out_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_invoice_id')->constrained('stock_out_invoices')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('line_total');
            $table->foreignId('defect_assessment_id')->nullable()->constrained('defect_assessments')->onDelete('set null');
            $table->timestamps();
            $table->index('stock_out_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out_invoice_items');
        Schema::dropIfExists('stock_out_invoices');
    }
};
