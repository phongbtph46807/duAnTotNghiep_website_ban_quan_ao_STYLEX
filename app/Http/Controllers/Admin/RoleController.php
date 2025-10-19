<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::query()->latest('id');

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
            
            // Get role statistics
            $roleStats = User::selectRaw('
                count(*) as total_users,
                sum(role = 0) as user_count,
                sum(role = 1) as admin_count,
                sum(role = 2) as staff_count
            ')->first();

            return view('admin.roles.index', compact('users', 'roleStats'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
        }
    }

    public function updateRole(Request $request, User $user)
    {
        try {
            $request->validate([
                'role' => 'required|in:0,1,2'
            ]);

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
}
