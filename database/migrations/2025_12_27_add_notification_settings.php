<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            // Ngưỡng cảnh báo
            ['key' => 'qc_failed_threshold', 'value' => '10'],
            ['key' => 'count_discrepancy_threshold', 'value' => '5'],
            
            // Bật/Tắt thông báo
            ['key' => 'notify_new_order', 'value' => '1'],
            ['key' => 'notify_low_stock', 'value' => '1'],
            ['key' => 'notify_pending_approval', 'value' => '1'],
            ['key' => 'notify_qc_failed', 'value' => '1'],
            ['key' => 'notify_count_discrepancy', 'value' => '1'],
            ['key' => 'notify_defect_found', 'value' => '1'],
            
            // Cleanup
            ['key' => 'notification_cleanup_read_days', 'value' => '30'],
            ['key' => 'notification_cleanup_unread_days', 'value' => '90'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        // Xóa các settings cũ không dùng
        DB::table('settings')->whereIn('key', [
            'costing_method',
            'allow_negative_stock',
            'auto_reorder',
            'negative_stock_notifications',
        ])->delete();
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'qc_failed_threshold',
            'count_discrepancy_threshold',
            'notify_new_order',
            'notify_low_stock',
            'notify_pending_approval',
            'notify_qc_failed',
            'notify_count_discrepancy',
            'notify_defect_found',
            'notification_cleanup_read_days',
            'notification_cleanup_unread_days',
        ])->delete();
    }
};
