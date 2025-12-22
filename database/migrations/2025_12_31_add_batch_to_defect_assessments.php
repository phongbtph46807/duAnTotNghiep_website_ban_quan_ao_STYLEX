<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('defect_assessments', function (Blueprint $table) {
            $table->string('batch_number')->nullable()->after('variant_id')->comment('Lô hàng');
            $table->integer('batch_cost_price')->nullable()->after('batch_number')->comment('Giá gốc của lô');
        });
    }

    public function down(): void
    {
        Schema::table('defect_assessments', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'batch_cost_price']);
        });
    }
};
