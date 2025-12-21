<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel public cho orders - tất cả admin/staff có thể nghe
Broadcast::channel('orders', function ($user) {
    if (!$user) {
        return false;
    }
    
    // Kiểm tra theo RBAC roles
    if ($user->roles) {
        $roleNames = $user->roles->pluck('name')->toArray();
        if (in_array('Admin', $roleNames) || in_array('Staff', $roleNames)) {
            return true;
        }
    }
    
    // Fallback: kiểm tra role field cũ
    return in_array($user->role, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_STAFF]);
});

// Private channel cho từng user - chỉ user đó mới nhận được update cho đơn hàng của họ
Broadcast::channel('user.{userId}.orders', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
