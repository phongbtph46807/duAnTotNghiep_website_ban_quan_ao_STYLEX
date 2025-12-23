<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event broadcast khi trạng thái đơn hàng được cập nhật
 * Tuân theo chuẩn Laravel Broadcasting
 * Dùng ShouldBroadcastNow để broadcast ngay lập tức, không qua queue
 */
class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Order model instance
     */
    public $order;

    /**
     * Create a new event instance.
     * 
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        // Chỉ load relationships cần thiết để giảm delay
        // Không load toàn bộ để tăng tốc độ broadcast
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     * 
     * Theo chuẩn Laravel Broadcasting:
     * - Channel: Public channel cho tất cả admin/staff
     * - PrivateChannel: Private channel cho từng user
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            // Public channel cho admin/staff (được authorize trong routes/channels.php)
            new Channel('orders'),
            // Public channel cho order tracking - không cần auth
            new Channel('order.' . $this->order->code . '.track'),
        ];
        
        // Nếu order có user_id, broadcast trên private channel cho user đó
        // Private channel cần được authorize trong routes/channels.php
        if ($this->order->user_id) {
            $channels[] = new PrivateChannel('user.' . $this->order->user_id . '.orders');
        }
        
        return $channels;
    }

    /**
     * The event's broadcast name.
     * 
     * Theo chuẩn Laravel: Tên này sẽ được prefix với dấu chấm (.) khi lắng nghe
     * Frontend sẽ lắng nghe: '.order.status.updated'
     * 
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    /**
     * Get the data to broadcast.
     * 
     * Chỉ broadcast dữ liệu cần thiết, không broadcast toàn bộ model
     * để tránh lộ thông tin nhạy cảm và giảm kích thước payload
     * Load relationships chỉ khi cần để tăng tốc độ broadcast
     * 
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        // Chỉ load relationships khi cần thiết để giảm delay
        $updatedByUser = null;
        $updatedByRoles = [];
        if ($this->order->updated_by) {
            $updatedByUser = \App\Models\User::with('roles')->find($this->order->updated_by);
            if ($updatedByUser && $updatedByUser->roles) {
                $updatedByRoles = $updatedByUser->roles->map(function($role) {
                    return ['name' => $role->name, 'color' => $role->color ?? '#6c757d'];
                })->toArray();
            }
        }
        
        return [
            'id' => $this->order->id,
            'code' => $this->order->code,
            'status' => $this->order->status,
            'payment_status' => $this->order->payment_status,
            'full_name' => $this->order->full_name,
            'email' => $this->order->email,
            'total' => $this->order->total,
            'total_amount' => $this->order->total_amount,
            'created_at' => $this->order->created_at?->toDateTimeString(),
            'updated_at' => $this->order->updated_at?->toDateTimeString(),
            'updated_by' => $this->order->updated_by,
            'updated_by_name' => $updatedByUser?->name,
            'updated_by_roles' => $updatedByRoles,
            'items_count' => $this->order->items()->count(), // Dùng query thay vì load collection
        ];
    }

    // ShouldBroadcastNow tự động broadcast ngay lập tức, không cần shouldQueue()
}

