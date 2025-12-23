<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Refresh user từ database để đảm bảo có dữ liệu mới nhất (quan trọng khi vừa đổi role)
        $user->refresh();
        
        // Map role integers to role names (tương thích với routes hiện tại dùng số)
        $roleNameMap = [
            1 => 'Admin',
            2 => 'Staff',
            3 => 'Warehouse Manager',
        ];
        
        // Convert string roles to role integers
        $requiredRoleInts = array_map('intval', $roles);
        
        // Ưu tiên kiểm tra trường role (integer) trước - đây là nguồn dữ liệu chính xác nhất
        $hasAccess = false;
        if (in_array($user->role, $requiredRoleInts)) {
            $hasAccess = true;
            // Tự động đồng bộ role từ trường role sang RBAC (nếu chưa có)
            $this->syncRoleToRBAC($user, $user->role);
        }
        
        // Fallback: Nếu không tìm thấy trong role integer, kiểm tra RBAC (roles relationship)
        if (!$hasAccess) {
            // Load relationships với fresh query để tránh cache
            $user->loadMissing('roles');
        
        // Lấy tất cả roles từ database để hỗ trợ role mới
        $allRoles = Role::pluck('name', 'id')->toArray();
        
            // Convert required roles to role names
        $requiredRoleNames = [];
        $requiredRoleIds = [];
        foreach ($roles as $role) {
            $roleInt = (int) $role;
            if (isset($roleNameMap[$roleInt])) {
                $requiredRoleNames[] = $roleNameMap[$roleInt];
            } elseif (isset($allRoles[$roleInt])) {
                $requiredRoleIds[] = $roleInt;
                $requiredRoleNames[] = $allRoles[$roleInt];
            } else {
                $requiredRoleNames[] = $role;
            }
        }
        
        // Kiểm tra bằng role ID (cho role mới từ database)
        if (!empty($requiredRoleIds)) {
            $userRoleIds = $user->roles()->pluck('roles.id')->toArray();
            foreach ($requiredRoleIds as $requiredId) {
                if (in_array($requiredId, $userRoleIds)) {
                    $hasAccess = true;
                    break;
                }
            }
        }
        
        // Kiểm tra bằng role name
        if (!$hasAccess && !empty($requiredRoleNames)) {
                $userRoles = $user->roles()->pluck('name')->toArray();
                foreach ($requiredRoleNames as $requiredName) {
                    if (in_array($requiredName, $userRoles)) {
                        $hasAccess = true;
                        break;
                    }
                }
            }
        }
        
        if (!$hasAccess) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Nếu user đã pass qua kiểm tra role ở trên, nghĩa là họ có role phù hợp với route group
        // -> Cho phép truy cập ngay, không cần kiểm tra permission nữa
        // (Permission sẽ được kiểm tra ở các middleware khác hoặc controller nếu cần)

        return $next($request);
    }

    /**
     * Tự động đồng bộ role từ trường role cũ sang RBAC
     */
    private function syncRoleToRBAC($user, $roleInt): void
    {
        try {
            $roleNameMap = [
                1 => 'Admin',
                2 => 'Staff',
                3 => 'Warehouse Manager',
            ];

            if (!isset($roleNameMap[$roleInt])) {
                return;
            }

            $roleName = $roleNameMap[$roleInt];
            
            // Kiểm tra xem user đã có role này trong RBAC chưa
            $hasRole = $user->roles()->where('name', $roleName)->exists();
            
            if (!$hasRole) {
                // Tìm hoặc tạo role trong bảng roles
                $role = Role::firstOrCreate(
                    ['name' => $roleName],
                    ['description' => "Role được tự động đồng bộ từ trường role cũ"]
                );
                
                // Gán role cho user (nếu chưa có)
                if (!$user->roles()->where('roles.id', $role->id)->exists()) {
                    $user->roles()->attach($role->id);
                }
            }
        } catch (\Exception $e) {
            // Bỏ qua lỗi nếu không thể đồng bộ (có thể do bảng chưa tồn tại)
            // Log nếu cần thiết
        }
    }
}