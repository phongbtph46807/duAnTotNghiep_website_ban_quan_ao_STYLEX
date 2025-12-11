<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;

class TestPermissionController extends Controller
{
    public function debug()
    {
        $user = auth()->user();
        
        $debug = [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_role_name' => $user->role_name,
        ];
        
        // Kiểm tra role có tồn tại không
        $role = Role::where('name', 'Warehouse Manager')->first();
        $debug['warehouse_role_exists'] = $role ? 'Yes' : 'No';
        
        if ($role) {
            $debug['warehouse_role_permissions_count'] = $role->permissions()->count();
            $debug['warehouse_role_permissions'] = $role->permissions()->pluck('name')->toArray();
        }
        
        // Test permission cụ thể
        $testPermission = 'admin.inventory.dashboard';
        $debug['has_permission_' . $testPermission] = $user->hasPermission($testPermission) ? 'Yes' : 'No';
        
        return response()->json($debug);
    }
}