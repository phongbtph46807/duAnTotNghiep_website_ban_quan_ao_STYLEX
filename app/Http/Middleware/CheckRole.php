<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        // Convert string roles to integers for comparison
        $requiredRoles = array_map('intval', $roles);
        
        // Check if user has any of the required roles
        if (!in_array($user->role, $requiredRoles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Kiểm tra permission nếu route có tên (dựa trên route name)
        $route = $request->route();
        if ($route && $route->getName()) {
            $routeName = $route->getName();
            if (!$user->hasPermission($routeName)) {
                abort(403, 'Bạn không có quyền thực hiện hành động này.');
            }
        }

        return $next($request);
    }
}