<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_in_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->string('batch_number')->unique();
            $table->integer('quantity');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->date('received_date')->nullable();

            //Nhà cung cấp
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->string('invoice_number')->nullable();

            $table->enum('status', ['PENDING', 'QC_PASSED', 'QC_FAILED', 'CONFIRMED', 'CANCELLED'])->default('PENDING');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['warehouse_id', 'status']);
            $table->index(['variant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_in_requests');
    }
};
