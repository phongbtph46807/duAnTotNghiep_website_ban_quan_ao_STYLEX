<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        // Load relationships để gửi đầy đủ thông tin
        $this->order = $order->load(['items.product', 'items.variant']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast trên channel public để tất cả admin/staff có thể nghe
        return [
            new Channel('orders'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.created';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'code' => $this->order->code,
            'status' => $this->order->status,
            'payment_status' => $this->order->payment_status,
            'payment_method' => $this->order->payment_method,
            'full_name' => $this->order->full_name,
            'email' => $this->order->email,
            'phone' => $this->order->phone,
            'total' => $this->order->total,
            'total_amount' => $this->order->total_amount,
            'created_at' => $this->order->created_at->toDateTimeString(),
            'items_count' => $this->order->items->count(),
            'items' => $this->order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product ? $item->product->name : 'N/A',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'line_total' => $item->line_total,
                ];
            })->toArray(),
        ];
    }
}

