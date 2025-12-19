<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('warehouse_stocks')) {
            if (!Schema::hasColumn('warehouse_stocks', 'status')) {
                Schema::table('warehouse_stocks', function (Blueprint $table) {
                    $table->enum('status', ['PENDING', 'CONFIRMED'])->default('CONFIRMED')->after('damaged');
                });
            }
        }

        if (Schema::hasTable('product_batches')) {
            if (!Schema::hasColumn('product_batches', 'warehouse_id')) {
                Schema::table('product_batches', function (Blueprint $table) {
                    $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('cascade')->after('variant_id');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('warehouse_stocks')) {
            Schema::table('warehouse_stocks', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasTable('product_batches')) {
            Schema::table('product_batches', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            });
        }
    }
};
