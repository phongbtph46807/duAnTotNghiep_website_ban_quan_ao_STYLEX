<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'wms_low_stock_threshold', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'wms_auto_create_stock', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'wms_cost_method', 'value' => 'WAC', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'wms_auto_reserve_hours', 'value' => '24', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'wms_require_batch', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'wms_enable_negative_stock', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'report_default_period', 'value' => '30', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'loyalty_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'loyalty_point_ratio', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_name', 'value' => 'StyleX Fashion Store', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_phone', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
