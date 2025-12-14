<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\LoyaltyService;
use App\Models\LoyaltyTier;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ cá nhân
     */
    public function index(LoyaltyService $loyaltyService)
    {
        $allTiers = LoyaltyTier::orderBy('min_spend_required')->get();
        $user = auth()->user();

        // Khởi tạo & lấy loyalty
        $userLoyalty = $loyaltyService->initializeUserLoyalty($user);
        $currentTier = $loyaltyService->getCurrentTier($user);
        $nextTierData = $loyaltyService->getNextTierProgress($user);

        return view('client.profile.index', compact(
            'user',
            'userLoyalty',
            'currentTier',
            'nextTierData',
            'allTiers'
        ));
    }

    /**
     * Cập nhật thông tin hồ sơ
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ];

            // Xử lý đổi mật khẩu
            if ($request->filled('current_password') && $request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()->with('error', 'Mật khẩu hiện tại không đúng!')->withInput();
                }
                $updateData['password'] = Hash::make($request->password);
            }

            // Xử lý upload avatar
            if ($request->hasFile('avatar')) {
                // Xóa avatar cũ nếu có
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Lưu avatar mới
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $updateData['avatar'] = $avatarPath;
            }

            $user->update($updateData);

            return redirect()->route('client.profile.index')->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau')->withInput();
        }
    }
}

