<?php

// app/Console/Commands/GenerateDailyReport.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\AdminReport;
use Carbon\Carbon;

class GenerateDailyReport extends Command
{
    protected $signature = 'report:generate-daily {--date=}';
    protected $description = 'Tổng hợp báo cáo kinh doanh hàng ngày (orders, inventory, commission...)';

    public function handle()
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::yesterday()->startOfDay();

        $this->info("Đang tổng hợp báo cáo cho ngày {$date->toDateString()}...");

        // Tổng hợp đơn hàng trong ngày
        $orders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->selectRaw('COUNT(id) as total_orders, SUM(total) as total_revenue')
            ->first();

        // Tổng hợp sản phẩm bán ra trong ngày
        $items = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereDate('orders.created_at', $date)
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->first();

        // Đếm log kho (nếu có inventory_logs)
        $inventoryLogs = DB::table('inventory_logs')
            ->whereDate('created_at', $date)
            ->count();

        // Tổng hợp hoàn tiền (payment_status = refunded)
        $refunds = DB::table('orders')
            ->whereDate('updated_at', $date)
            ->where('payment_status', 'refunded')
            ->sum('total');

        // Giả định commission 5% doanh thu
        $commission = ($orders->total_revenue ?? 0) * 0.05;

        // Lưu vào bảng admin_reports
        AdminReport::updateOrCreate(
            ['report_date' => $date->toDateString()],
            [
                'user_id' => 1, // hoặc admin_id nếu có
                'total_salary_paid' => 0,
                'total_commission' => $commission,
                'orders_processed_count' => $orders->total_orders ?? 0,
                'inventory_transactions_count' => $inventoryLogs ?? 0,
            ]
        );

        $this->info("✅ Báo cáo ngày {$date->toDateString()} đã được lưu.");
        return Command::SUCCESS;
    }
}

