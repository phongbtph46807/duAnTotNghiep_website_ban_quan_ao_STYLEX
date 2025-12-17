<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang hồ sơ cá nhân
     */
    public function index()
    {
        $user = auth()->user();
        return view('client.profile.index', compact('user'));
    }

    public function card()
    {
        $user = auth()->user();

        $walletHistory = collect($user->wallet_history ?? [])
            ->sortByDesc(function ($item) {
                // sort theo created_at (string) -> strtotime
                return strtotime($item['created_at'] ?? '1970-01-01 00:00:00');
            })
            ->values()
            ->all();
        return view('client.profile.cards.index', compact('user', 'walletHistory'));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'bank_code'      => 'required|string|max:20',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:100',
            'amount'         => 'required|integer|min:100000', // tối thiểu 100k
            'note'           => 'nullable|string|max:255',
        ], [
            'amount.min' => 'Số tiền rút tối thiểu là 100.000 ₫.',
        ]);

        try {
            DB::transaction(function () use ($request) {


                $user = User::query()->whereKey(Auth::id())->lockForUpdate()->firstOrFail();

                $amount = (int) $request->amount;

                // check số dư
                if ($amount > (int) $user->wallet_balance) {
                    throw ValidationException::withMessages([
                        'amount' => 'Số tiền rút vượt quá số dư ví.',
                    ]);
                }

                $before = (int) $user->wallet_balance;
                $after  = $before - $amount;

                $history = $user->wallet_history;
                if (!is_array($history)) $history = [];

                $history[] = [
                    'note'            => $request->note ?: 'Yêu cầu rút tiền',
                    'type'            => 'withdraw',
                    'amount'          => $amount,

                    'balance_before'  => $before,
                    'balance_after'   => $after,
                    'order_id' => '-',
                    'order_code' => '-',
                    'created_at'      => now()->toDateTimeString(),
                    'created_by'      => $user->id,
                    'created_by_name' => $user->name,

                    // thông tin nhận tiền
'bank_code'       => $request->bank_code,
                    'account_number'  => $request->account_number,
                    'account_name'    => $request->account_name,
                ];

                // Trừ tiền ngay để tránh user rút 2 lần.
                // (Chuẩn hơn là có wallet_hold/frozen, nhưng theo mô hình JSON hiện tại thì làm vậy là hợp lý nhất)
                $user->wallet_balance = $after;
                $user->wallet_history = $history;
                $user->save();
            });

            return back()->with('success', 'Đã gửi yêu cầu rút tiền. Vui lòng chờ duyệt.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return back()->with('error', 'Có lỗi khi rút tiền. Vui lòng thử lại.');
        }
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