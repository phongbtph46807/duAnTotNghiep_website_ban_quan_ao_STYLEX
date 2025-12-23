<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel public cho orders - tất cả admin/staff/warehouse manager có thể nghe
Broadcast::channel('orders', function ($user) {
    if (!$user) {
        return false;
    }
    
    // Kiểm tra theo RBAC roles
    if ($user->roles && $user->roles->isNotEmpty()) {
        $roleNames = $user->roles->pluck('name')->toArray();
        if (in_array('Admin', $roleNames) || in_array('Staff', $roleNames) || in_array('Warehouse Manager', $roleNames)) {
            return true;
        }
    }
    
    // Fallback: kiểm tra role field cũ (ưu tiên role integer vì chính xác hơn)
    return in_array($user->role, [
        \App\Models\User::ROLE_ADMIN, 
        \App\Models\User::ROLE_STAFF,
        \App\Models\User::ROLE_WAREHOUSE_MANAGER
    ]);
});

// Private channel cho từng user - chỉ user đó mới nhận được update cho đơn hàng của họ
Broadcast::channel('user.{userId}.orders', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Private channel cho notifications của từng user
Broadcast::channel('user.{userId}.notifications', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Public channel cho order tracking - không cần auth, chỉ cần có order code
// Cho phép khách hàng theo dõi đơn hàng mà không cần đăng nhập
Broadcast::channel('order.{orderCode}.track', function ($user, $orderCode) {
    // Public channel - ai cũng có thể listen (không cần auth)
    // Bảo mật: chỉ broadcast order code, không broadcast thông tin nhạy cảm
    return true;
});
