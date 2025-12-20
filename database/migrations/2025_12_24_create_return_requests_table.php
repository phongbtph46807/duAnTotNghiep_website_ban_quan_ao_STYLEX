<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('rma_number')->unique();
            $table->enum('type', ['RETURN', 'EXCHANGE'])->default('RETURN');
            $table->enum('reason', ['DEFECTIVE', 'NOT_AS_DESCRIBED', 'WRONG_SIZE', 'WRONG_COLOR', 'CHANGED_MIND', 'OTHER'])->default('OTHER');
            $table->text('reason_description')->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'RECEIVED', 'QC_PASSED', 'QC_FAILED', 'COMPLETED'])->default('PENDING');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('qc_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('qc_at')->nullable();
            $table->text('qc_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('order_id');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained('return_requests')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->enum('condition', ['UNOPENED', 'OPENED', 'DAMAGED', 'DEFECTIVE'])->default('OPENED');
            $table->text('item_notes')->nullable();
            $table->timestamps();
            $table->index('return_request_id');
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained('return_requests')->onDelete('cascade');
            $table->integer('amount');
            $table->enum('status', ['PENDING', 'APPROVED', 'PROCESSED', 'FAILED'])->default('PENDING');
            $table->enum('method', ['ORIGINAL_PAYMENT', 'WALLET', 'BANK_TRANSFER'])->default('ORIGINAL_PAYMENT');
            $table->string('transaction_id')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('return_request_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('return_requests');
    }
};
