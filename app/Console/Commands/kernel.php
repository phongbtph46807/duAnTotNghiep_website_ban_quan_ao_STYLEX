<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Đăng ký các Artisan Command tùy chỉnh của bạn
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // Nếu bạn có route console (tùy chọn)
        require base_path('routes/console.php');
    }

    /**
     * Đăng ký các tác vụ định kỳ
     */
    protected function schedule(Schedule $schedule): void
    {
        // Chạy báo cáo kinh doanh hàng ngày lúc 00:15 sáng
        $schedule->command('report:generate-daily')
            ->dailyAt('00:15')
            ->appendOutputTo(storage_path('logs/daily-report.log'));
    }
}
