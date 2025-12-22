<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backup dữ liệu cũ nếu có
        $oldData = [];
        if (Schema::hasTable('defect_assessments')) {
            $oldData = DB::table('defect_assessments')->get()->toArray();
            Schema::dropIfExists('defect_assessments');
        }

        Schema::create('defect_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('quantity');
            $table->enum('defect_level', ['LIGHT', 'MEDIUM', 'HEAVY'])->default('LIGHT');

            // Đánh giá
            $table->string('defect_type')->nullable();
            $table->text('defect_description')->nullable();
            $table->text('description')->nullable();
            $table->enum('classification', ['REWORK', 'SCRAP', 'B-GRADE'])->nullable();

            // Chi phí
            $table->integer('repair_cost')->default(0);
            $table->integer('material_cost')->default(0);

            // Trạng thái
            $table->enum('status', ['PENDING', 'APPROVED', 'COMPLETED', 'REJECTED'])->default('PENDING')->index();

            // Người thực hiện
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('assessed_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();

            // Ghi chú
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Liên kết
            $table->unsignedBigInteger('stock_in_request_id')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('location')->nullable();

            $table->timestamps();
            $table->index(['warehouse_id', 'status']);

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assessed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('stock_in_request_id')->references('id')->on('stock_in_requests')->onDelete('set null');
        });

        // Restore dữ liệu cũ
        if (!empty($oldData)) {
            foreach ($oldData as $row) {
                DB::table('defect_assessments')->insert((array)$row);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('defect_assessments');
    }
};
