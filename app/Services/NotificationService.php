<?php

namespace App\Services;

use App\Models\User;
use App\Models\Setting;
use App\Events\NotificationCreated;
use App\Events\NewOrderCreated;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function getLowStockThreshold()
    {
        return (int) Setting::where('key', 'low_stock_threshold')->value('value') ?? 10;
    }
    
    private function isNotificationEnabled($type)
    {
        $key = 'notify_' . $type;
        return (bool) Setting::where('key', $key)->value('value') ?? true;
    }
    // Thông báo tồn kho thấp
    public function notifyLowStock($variant, $warehouse, $currentStock)
    {
        if (!$this->isNotificationEnabled('low_stock')) return;
        
        // Kiểm tra đã có thông báo trong 24h chưa
        $exists = DB::table('notifications')
            ->where('type', 'low_stock')
            ->where('data->variant_id', $variant->id)
            ->where('data->warehouse_id', $warehouse->id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();
        
        if ($exists) return; // Không tạo thông báo trùng
        
        // Gửi cho Admin và Staff
        $managers = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get();

        foreach ($managers as $user) {
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'low_stock',
                'title' => 'Cảnh báo tồn kho thấp',
                'message' => "{$variant->name} tại {$warehouse->name} chỉ còn {$currentStock} sản phẩm",
                'data' => json_encode([
                    'variant_id' => $variant->id,
                    'warehouse_id' => $warehouse->id,
                    'current_stock' => $currentStock,
                    'url' => route('admin.inventory.current-stock')
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Thông báo không đủ tồn kho
    public function notifyInsufficientStock($order, $variant, $required, $available)
    {
        if (!$this->isNotificationEnabled('low_stock')) return;
        
        // Gửi cho Admin
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        foreach ($admins as $user) {
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'insufficient_stock',
                'title' => 'Không đủ tồn kho',
                'message' => "Đơn #{$order->code}: {$variant->name} cần {$required}, chỉ còn {$available}",
                'data' => json_encode([
                    'order_id' => $order->id,
                    'variant_id' => $variant->id,
                    'required' => $required,
                    'available' => $available,
                    'url' => route('admin.orders.show', $order->id)
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Thông báo phiếu chờ duyệt
    public function notifyPendingApproval($requestType, $requestId, $requestCode)
    {
        if (!$this->isNotificationEnabled('pending_approval')) return;
        
        // Gửi cho Admin và Staff
        $managers = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get();

        $typeNames = [
            'stock_in' => 'Phiếu nhập kho',
            'stock_out' => 'Phiếu xuất kho',
            'transfer' => 'Phiếu chuyển kho',
        ];

        foreach ($managers as $user) {
            $notification = [
                'user_id' => $user->id,
                'type' => 'pending_approval',
                'title' => 'Phiếu chờ duyệt',
                'message' => "{$typeNames[$requestType]} #{$requestCode} cần được duyệt",
                'data' => json_encode([
                    'request_type' => $requestType,
                    'request_id' => $requestId,
                    'url' => route('admin.inventory.dashboard')
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table('notifications')->insert($notification);
            broadcast(new NotificationCreated($notification));
        }
    }

    // Thông báo đơn hàng mới
    public function notifyNewOrder($order)
    {
        if (!$this->isNotificationEnabled('new_order')) return;
        
        // Gửi cho Admin và Staff
        $staff = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get();

        foreach ($staff as $user) {
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'new_order',
                'title' => 'Đơn hàng mới',
                'message' => "Đơn hàng #{$order->code} - {$order->full_name} - " . number_format($order->total) . 'đ',
                'data' => json_encode([
                    'order_id' => $order->id,
                    'url' => route('admin.orders.show', $order->id)
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Broadcast event để cập nhật realtime
        broadcast(new NewOrderCreated($order->fresh()))->toOthers();
    }

    // Thông báo QC failed
    public function notifyQCFailed($stockIn, $failedQty, $totalQty)
    {
        if (!$this->isNotificationEnabled('qc_failed')) return;
        
        $failRate = ($failedQty / $totalQty) * 100;
        $threshold = (int) Setting::where('key', 'qc_failed_threshold')->value('value') ?? 10;
        
        if ($failRate < $threshold) return;

        // Gửi cho Admin và Staff
        $managers = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get();

        foreach ($managers as $user) {
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'qc_failed',
                'title' => 'Cảnh báo QC Failed cao',
                'message' => "Phiếu nhập #{$stockIn->batch_number}: {$failedQty}/{$totalQty} hỏng (" . round($failRate, 1) . "%)",
                'data' => json_encode([
                    'stock_in_id' => $stockIn->id,
                    'failed_qty' => $failedQty,
                    'total_qty' => $totalQty,
                    'fail_rate' => $failRate,
                    'url' => route('admin.inventory.dashboard')
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Thông báo chênh lệch kiểm kê
    public function notifyCountDiscrepancy($countRequest, $totalDiscrepancy)
    {
        if (!$this->isNotificationEnabled('count_discrepancy')) return;
        
        $threshold = (int) Setting::where('key', 'count_discrepancy_threshold')->value('value') ?? 5;
        if (abs($totalDiscrepancy) < $threshold) return;

        $managers = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get();

        foreach ($managers as $user) {
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'count_discrepancy',
                'title' => 'Cảnh báo chênh lệch kiểm kê',
                'message' => "Kiểm kê tại {$countRequest->warehouse->name}: Chênh lệch " . abs($totalDiscrepancy) . " sản phẩm",
                'data' => json_encode([
                    'count_request_id' => $countRequest->id,
                    'discrepancy' => $totalDiscrepancy,
                    'url' => route('admin.inventory.count.index')
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Thông báo hàng hỏng phát hiện
    public function notifyDefectFound($defectAssessment, $damagedQty)
    {
        if (!$this->isNotificationEnabled('defect_found')) return;
        
        $managers = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])->get();

        foreach ($managers as $user) {
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'defect_found',
                'title' => 'Phát hiện hàng hỏng',
                'message' => "Phát hiện {$damagedQty} sản phẩm hỏng tại {$defectAssessment->warehouse->name}",
                'data' => json_encode([
                    'defect_id' => $defectAssessment->id,
                    'damaged_qty' => $damagedQty,
                    'url' => route('admin.inventory.dashboard')
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
