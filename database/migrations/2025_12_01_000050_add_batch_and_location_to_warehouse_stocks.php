<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->string('batch_number')->nullable()->after('variant_id');
            $table->string('location')->nullable()->after('batch_number');
            
            // Cập nhật unique constraint
            $table->dropUnique(['warehouse_id', 'variant_id']);
            $table->unique(['warehouse_id', 'variant_id', 'batch_number']);
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->dropUnique(['warehouse_id', 'variant_id', 'batch_number']);
            $table->unique(['warehouse_id', 'variant_id']);
            $table->dropColumn(['batch_number', 'location']);
        });
    }
};
