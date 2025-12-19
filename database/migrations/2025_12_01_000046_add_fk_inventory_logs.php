<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            if (!$this->hasForeignKey('inventory_logs', 'warehouse_id')) {
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            }
            if (!$this->hasForeignKey('inventory_logs', 'variant_id')) {
                $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            }
            if (!$this->hasForeignKey('inventory_logs', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        try {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
            });
        } catch (Exception $e) {}
        
        try {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->dropForeign(['variant_id']);
            });
        } catch (Exception $e) {}
        
        try {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (Exception $e) {}
    }

    private function hasForeignKey($table, $column)
    {
        $keyName = $table . '_' . $column . '_foreign';
        $foreignKeys = \DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [$table, $column]);
        return count($foreignKeys) > 0;
    }
};
