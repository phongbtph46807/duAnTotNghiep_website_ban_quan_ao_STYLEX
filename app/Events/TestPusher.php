<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
// Quan trọng: Phải có ShouldBroadcast
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; 
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// 1. Phải thêm "implements ShouldBroadcast" ở đây
class TestPusher implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Nhận dữ liệu khi khởi tạo Event
     */
    public function __construct($message = "Chào StyleX, Pusher đã thông suốt!")
    {
        $this->message = $message;
    }

    /**
     * 2. Đổi sang Channel (Công khai) để test cho dễ. 
     * PrivateChannel cần phải login và cấu hình phức tạp hơn.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('stylex-channel'), 
        ];
    }

    /**
     * 3. (Tùy chọn) Tên của sự kiện khi phía Frontend nhận
     */
    public function broadcastAs(): string
    {
        return 'test-event';
    }
}