<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo của user
     * Chỉ lấy thông báo về đơn hàng (order_status_changed)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $limit = $request->get('limit', 10);
        
        // Chỉ lấy thông báo về đơn hàng của user đó
        $notifications = DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('type', 'order_status_changed') // Chỉ lấy thông báo về đơn hàng
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($notif) {
                $notif->data = json_decode($notif->data ?? '{}', true);
                $notif->created_at_formatted = \Carbon\Carbon::parse($notif->created_at)->diffForHumans();
                return $notif;
            });

        // Chỉ đếm thông báo về đơn hàng chưa đọc
        $unreadCount = DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('type', 'order_status_changed') // Chỉ đếm thông báo về đơn hàng
            ->whereNull('read_at')
            ->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        }

        return view('client.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Đánh dấu thông báo là đã đọc
     * Chỉ xử lý thông báo về đơn hàng
     */
    public function markAsRead(Request $request, $id = null)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if ($id) {
            // Đánh dấu một thông báo cụ thể (chỉ thông báo về đơn hàng)
            DB::table('notifications')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->where('type', 'order_status_changed')
                ->update(['read_at' => now()]);
        } else {
            // Đánh dấu tất cả thông báo về đơn hàng là đã đọc
            DB::table('notifications')
                ->where('user_id', $user->id)
                ->where('type', 'order_status_changed')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        // Chỉ đếm thông báo về đơn hàng chưa đọc
        $unreadCount = DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('type', 'order_status_changed')
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }
}

