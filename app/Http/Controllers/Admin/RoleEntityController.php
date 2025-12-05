<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleEntityController extends Controller
{
    public function index()
    {
        $roles = Role::query()
            ->orderByRaw("CASE WHEN LOWER(name) = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(10);
        
        // Đếm số user đang sử dụng mỗi role
        $roleUserCounts = [];
        $roleUserTableExists = false;
        
        try {
            DB::table('role_user')->limit(1)->get();
            $roleUserTableExists = true;
        } catch (\Exception $e) {
            $roleUserTableExists = false;
        }
        
        foreach ($roles as $role) {
            $count = 0;
            
            // Map role name sang role integer để đếm từ bảng users
            $roleName = strtolower($role->name);
            $roleInteger = null;
            if ($roleName === 'admin') {
                $roleInteger = User::ROLE_ADMIN;
            } elseif ($roleName === 'staff') {
                $roleInteger = User::ROLE_STAFF;
            }
            
            // Đếm từ trường role integer (nguồn dữ liệu chính xác nhất cho Admin/Staff)
            if ($roleInteger !== null) {
                $count = User::where('role', $roleInteger)->count();
            } else {
                // Nếu không phải Admin hoặc Staff, đếm từ bảng role_user
                if ($roleUserTableExists) {
                    try {
                        $count = DB::table('role_user')
                            ->where('role_id', $role->id)
                            ->count();
                    } catch (\Exception $e) {
                        $count = 0;
                    }
                } else {
                    try {
                        $count = User::whereHas('roles', function($query) use ($role) {
                            $query->where('roles.id', $role->id);
                        })->count();
                    } catch (\Exception $e) {
                        $count = 0;
                    }
                }
            }
            
            $roleUserCounts[$role->id] = $count;
        }
        
        return view('admin.rbac.roles.index', compact('roles', 'roleUserCounts'));
    }

    public function create()
    {
        return view('admin.rbac.roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Role::create($data);
        return redirect()->route('admin.rbac.roles.index')->with('success', 'Tạo role thành công');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $assigned = $role->permissions()->pluck('permissions.id')->toArray();
        return view('admin.rbac.roles.edit', compact('role', 'permissions', 'assigned'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
            'description' => ['nullable', 'string', 'max:255'],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['exists:permissions,id'],
        ]);

        $role->update($data);
        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }
        return redirect()->route('admin.rbac.roles.index')->with('success', 'Cập nhật role thành công');
    }

    public function destroy(Role $role)
    {
        try {
            // Kiểm tra nếu role là "Admin" thì không cho xóa
            if (strtolower($role->name) === 'admin') {
                return redirect()->route('admin.rbac.roles.index')
                    ->with('error', 'Không thể xóa role "Admin"! Role này là bắt buộc trong hệ thống.');
            }

            // Kiểm tra xem role có đang được sử dụng bởi user nào không
            $userCount = 0;
            try {
                // Kiểm tra xem bảng role_user có tồn tại không
                $roleUserTableExists = false;
                try {
                    DB::table('role_user')->limit(1)->get();
                    $roleUserTableExists = true;
                } catch (\Exception $e) {
                    $roleUserTableExists = false;
                }

                if ($roleUserTableExists) {
                    // Đếm số user đang sử dụng role này
                    $userCount = DB::table('role_user')
                        ->where('role_id', $role->id)
                        ->count();
                } else {
                    // Nếu bảng chưa tồn tại, kiểm tra bằng quan hệ
                    try {
                        $userCount = User::whereHas('roles', function($query) use ($role) {
                            $query->where('roles.id', $role->id);
                        })->count();
                    } catch (\Exception $e) {
                        $userCount = 0;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error checking role usage: ' . $e->getMessage());
                $userCount = 0;
            }

            // Nếu có user đang sử dụng role này, không cho xóa
            if ($userCount > 0) {
                return redirect()->route('admin.rbac.roles.index')
                    ->with('error', "Không thể xóa role \"{$role->name}\"! Hiện có {$userCount} tài khoản đang sử dụng role này.");
            }

            // Xóa role
            $role->delete();
            
            return redirect()->route('admin.rbac.roles.index')
                ->with('success', 'Đã xóa role thành công');
        } catch (\Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage());
            return redirect()->route('admin.rbac.roles.index')
                ->with('error', 'Có lỗi xảy ra khi xóa role. Vui lòng thử lại sau.');
        }
    }
}


