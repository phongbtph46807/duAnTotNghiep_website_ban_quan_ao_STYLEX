<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Admin (role = 1) - redirect về dashboard
            if ($user->role == 1) {
                return redirect()->route('admin.dashboard');
            }
            // Staff (role = 2) - redirect về trang đơn hàng
            elseif ($user->role == 2) {
                return redirect()->route('admin.orders.index');
            }
            // Warehouse Manager (role = 3) - redirect về trang đơn hàng hoặc inventory
            elseif ($user->role == 3) {
                return redirect()->route('admin.orders.index');
            }
            // Regular user (role = 0)
            else {
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}
