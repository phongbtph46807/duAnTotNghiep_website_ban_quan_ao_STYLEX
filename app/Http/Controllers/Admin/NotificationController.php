<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $type = request('type');
        
        $query = Notification::where('user_id', auth()->id());
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $notifications = $query->latest()->paginate(20);
        
        $typeCounts = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
        
        return view('admin.notifications.index', compact('notifications', 'typeCounts'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Đã đánh dấu là đã đọc');
    }

    public function markAllAsRead()
    {
        $type = request('type');
        
        $query = Notification::where('user_id', auth()->id())->whereNull('read_at');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $query->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'Đã đánh dấu tất cả là đã đọc');
    }
}
