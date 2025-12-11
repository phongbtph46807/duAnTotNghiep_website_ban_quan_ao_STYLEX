<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Đổi tên key từ wms_low_stock_threshold sang low_stock_threshold
        DB::table('settings')
            ->where('key', 'wms_low_stock_threshold')
            ->update(['key' => 'low_stock_threshold']);
        
        // Tạo setting mặc định nếu chưa có
        $exists = DB::table('settings')->where('key', 'low_stock_threshold')->exists();
        if (!$exists) {
            DB::table('settings')->insert([
                'key' => 'low_stock_threshold',
                'value' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Đổi ngược lại
        DB::table('settings')
            ->where('key', 'low_stock_threshold')
            ->update(['key' => 'wms_low_stock_threshold']);
    }
};
