<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Chỉ hiển thị Admin và Staff, không hiển thị User
            $query = User::query()->whereIn('role', [1, 2])->orderByRaw('CASE 
                WHEN role = 1 THEN 1 
                WHEN role = 2 THEN 2 
                ELSE 3 
            END')->latest('id');

            // Filter by role
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Search by name or email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }

            $users = $query->paginate(10);
            
            // Get role statistics - tổng số tài khoản có quyền
            $totalUsers = User::whereIn('role', [1, 2])->count();
            
            // Get statistics by Role from database
            $roles = Role::orderBy('name')->get();
            $roleStats = [];
            
            // Kiểm tra xem bảng role_user có tồn tại không
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
                
                // Đếm từ trường role integer (nguồn dữ liệu chính xác nhất)
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
                    }
                }
                
                $roleStats[] = [
                    'role' => $role,
                    'count' => $count
                ];
            }

            return view('admin.roles.index', compact('users', 'totalUsers', 'roleStats'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        }
    }

    public function updateRole(Request $request, User $user)
    {
        try {
            $request->validate([
                'role' => 'required|in:1,2' // Chỉ cho phép Admin hoặc Staff
            ]);

            // Kiểm tra nếu đang thay đổi admin cuối cùng thành staff
            if ($user->role == User::ROLE_ADMIN && $request->role == User::ROLE_STAFF) {
                $adminCount = User::where('role', User::ROLE_ADMIN)->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể thay đổi admin cuối cùng thành staff! Hệ thống cần ít nhất 1 tài khoản admin.'
                    ], 400);
                }
            }

            $user->update([
                'role' => $request->role,
                'is_admin' => $request->role == 1 ? 1 : 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật quyền thành công',
                'role_name' => $user->role_name
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
            ], 500);
        }
    }

    public function bulkUpdateRoles(Request $request)
    {
        try {
            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'role' => 'required|in:0,1,2'
            ]);

            DB::transaction(function () use ($request) {
                User::whereIn('id', $request->user_ids)
                    ->update([
                        'role' => $request->role,
                        'is_admin' => $request->role == 1 ? 1 : 0
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật quyền hàng loạt thành công'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
            ], 500);
        }
    }

    public function create()
    {
        // Lấy danh sách các tài khoản Admin và Staff hiện có
        $existingUsers = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])
            ->select('id', 'name', 'email', 'role', 'status')
            ->orderByRaw('CASE 
                WHEN role = 1 THEN 1 
                WHEN role = 2 THEN 2 
                ELSE 3 
            END')
            ->orderBy('name')
            ->get();
        
        // Lấy danh sách tất cả roles từ database
        $roles = Role::orderBy('name')->get();
        
        // Lấy danh sách tất cả permissions
        $permissions = Permission::orderBy('name')->get();
        
        return view('admin.roles.create', compact('existingUsers', 'roles', 'permissions'));
    }

    public function edit(User $user)
    {
        // Lấy danh sách tất cả roles từ database
        $roles = Role::orderBy('name')->get();
        
        // Lấy roles hiện tại của user
        $userRoles = [];
        try {
            $userRoles = $user->roles->pluck('id')->toArray();
        } catch (\Exception $e) {
            $userRoles = [];
        }
        
        // Lấy danh sách tất cả permissions
        $permissions = Permission::orderBy('name')->get();
        
        // Lấy permissions hiện tại của user
        $userPermissions = [];
        try {
            $userPermissions = $user->permissions->pluck('id')->toArray();
        } catch (\Exception $e) {
            $userPermissions = [];
        }
        
        return view('admin.roles.edit', compact('user', 'roles', 'userRoles', 'permissions', 'userPermissions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'role_ids' => 'required|array|min:1',
                'role_ids.*' => 'exists:roles,id',
                'status' => 'required|in:active,inactive,blocked',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            DB::transaction(function () use ($request) {
                // Xác định role integer dựa trên role đầu tiên được chọn (để tương thích với hệ thống cũ)
                $firstRole = Role::find($request->role_ids[0]);
                $roleInteger = User::ROLE_USER; // Mặc định
                if ($firstRole) {
                    // Map role name sang role integer
                    $roleInteger = match(strtolower($firstRole->name)) {
                        'admin' => User::ROLE_ADMIN,
                        'staff' => User::ROLE_STAFF,
                        default => User::ROLE_USER
                    };
                }

                $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                    'role' => $roleInteger,
                    'is_admin' => $roleInteger == User::ROLE_ADMIN ? 1 : 0,
                'status' => $request->status,
                'email_verified_at' => now()
            ]);

                // Gán roles
                try {
                    $user->roles()->sync($request->role_ids);
                } catch (\Exception $e) {
                    Log::warning('Cannot sync roles: ' . $e->getMessage());
                }

                // Gán permissions nếu có
                if ($request->has('permissions') && is_array($request->permissions)) {
                    try {
                        $user->permissions()->sync($request->permissions);
                    } catch (\Exception $e) {
                        Log::warning('Cannot sync permissions: ' . $e->getMessage());
                    }
                }
            });

            return redirect()->route('admin.roles.index')->with('success', 'Tạo tài khoản thành công!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau')->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
                'role_ids' => 'required|array|min:1',
                'role_ids.*' => 'exists:roles,id',
                'status' => 'required|in:active,inactive,blocked',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            // Kiểm tra nếu đang sửa admin cuối cùng
            if ($user->role == User::ROLE_ADMIN) {
                $adminCount = User::where('role', User::ROLE_ADMIN)->count();
                if ($adminCount <= 1) {
                    // Kiểm tra xem có đang thay đổi role từ admin sang khác không
                    $firstRole = Role::find($request->role_ids[0]);
                    $newRoleInteger = User::ROLE_USER;
                    if ($firstRole) {
                        $newRoleInteger = match(strtolower($firstRole->name)) {
                            'admin' => User::ROLE_ADMIN,
                            'staff' => User::ROLE_STAFF,
                            default => User::ROLE_USER
                        };
                    }
                    
                    if ($newRoleInteger != User::ROLE_ADMIN) {
                        return redirect()->back()->with('error', 'Không thể thay đổi admin cuối cùng! Hệ thống cần ít nhất 1 tài khoản admin.')->withInput();
                    }
                    
                    // Nếu đang thay đổi status thành inactive hoặc blocked
                    if (in_array($request->status, ['inactive', 'blocked'])) {
                        return redirect()->back()->with('error', 'Không thể vô hiệu hóa admin cuối cùng! Hệ thống cần ít nhất 1 tài khoản admin hoạt động.')->withInput();
                    }
                }
            }

            DB::transaction(function () use ($request, $user) {
                // Xác định role integer dựa trên role đầu tiên được chọn
                $firstRole = Role::find($request->role_ids[0]);
                $roleInteger = User::ROLE_USER;
                if ($firstRole) {
                    $roleInteger = match(strtolower($firstRole->name)) {
                        'admin' => User::ROLE_ADMIN,
                        'staff' => User::ROLE_STAFF,
                        default => User::ROLE_USER
                    };
            }

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                    'role' => $roleInteger,
                    'is_admin' => $roleInteger == User::ROLE_ADMIN ? 1 : 0,
                'status' => $request->status
            ];

            // Chỉ cập nhật password nếu có
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

                // Cập nhật roles
                try {
                    $user->roles()->sync($request->role_ids);
                } catch (\Exception $e) {
                    Log::warning('Cannot sync roles: ' . $e->getMessage());
                }

                // Cập nhật permissions
                if ($request->has('permissions')) {
                    try {
                        $user->permissions()->sync($request->permissions);
                    } catch (\Exception $e) {
                        Log::warning('Cannot sync permissions: ' . $e->getMessage());
                    }
                } else {
                    // Nếu không có permissions nào được chọn, xóa tất cả
                    try {
                        $user->permissions()->detach();
                    } catch (\Exception $e) {
                        Log::warning('Cannot detach permissions: ' . $e->getMessage());
                    }
                }
            });

            return redirect()->route('admin.roles.index')->with('success', 'Cập nhật tài khoản thành công!');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau')->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            // Không cho phép xóa chính mình
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể xóa chính tài khoản của mình!'
                ], 400);
            }

            // Kiểm tra nếu đang xóa admin và chỉ còn 1 admin duy nhất
            if ($user->role == User::ROLE_ADMIN) {
                $adminCount = User::where('role', User::ROLE_ADMIN)->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể xóa admin cuối cùng! Hệ thống cần ít nhất 1 tài khoản admin.'
                    ], 400);
                }
            }

            // Xóa user hoàn toàn khỏi database
            $user->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa tài khoản thành công!'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
            ], 500);
        }
    }

    public function checkAdminCount()
    {
        $adminCount = User::where('role', User::ROLE_ADMIN)->count();
        return response()->json(['admin_count' => $adminCount]);
    }
}