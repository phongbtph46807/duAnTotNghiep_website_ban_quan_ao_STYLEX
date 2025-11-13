<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Spin;
use App\Models\SpinUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpinController extends Controller
{
    public function index()
    {
        // Lấy tất cả phần thưởng đang hoạt động
        $spins = Spin::where('is_active', 1)
            ->with('voucher')
            ->orderBy('probability', 'desc')
            ->get();

        // Lịch sử quay của user
        $userSpins = [];
        $canSpin = false;
        $spinToday = 0;

        if (Auth::check()) {
            $userSpins = SpinUser::where('user_id', Auth::id())
                ->with('spin.voucher')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Kiểm tra đã quay hôm nay chưa
            $today = now()->startOfDay();
            $spinToday = SpinUser::where('user_id', Auth::id())
                ->where('created_at', '>=', $today)
                ->count();

            $canSpin = $spinToday < 1;
        }

        return view('client.spins.index', compact('spins', 'userSpins', 'canSpin', 'spinToday'));
    }

    public function spin(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Vui lòng đăng nhập để quay thưởng!'], 401);
        }

        // Check spin limit (1 lần/ngày)
        $today = now()->startOfDay();
        $spinToday = SpinUser::where('user_id', Auth::id())
            ->where('created_at', '>=', $today)
            ->count();

        if ($spinToday >= 1) {
            return response()->json(['error' => 'Bạn đã hết lượt quay hôm nay!'], 400);
        }

        // Get active spins
        $spins = Spin::where('is_active', 1)->get();

        if ($spins->isEmpty()) {
            return response()->json(['error' => 'Không có phần thưởng khả dụng!'], 404);
        }

        // Random spin based on probability
        $totalProbability = $spins->sum('probability');
        $random = rand(1, $totalProbability);

        $currentSum = 0;
        $selectedSpin = null;

        foreach ($spins as $spin) {
            $currentSum += $spin->probability;
            if ($random <= $currentSum) {
                $selectedSpin = $spin;
                break;
            }
        }

        // Save spin result
        $spinUser = SpinUser::create([
            'user_id' => Auth::id(),
            'spin_id' => $selectedSpin->id,
            'time_spin' => now(),
            'is_claimed' => false,
        ]);

        $spinUser->load('spin.voucher');

        return response()->json([
            'success' => true,
            'spin' => $selectedSpin,
            'voucher' => $selectedSpin->voucher,
            'spin_user_id' => $spinUser->id,
        ]);
    }

    public function claim(Request $request, SpinUser $spinUser)
    {
        if ($spinUser->user_id !== Auth::id()) {
            return response()->json(['error' => 'Không có quyền!'], 403);
        }

        if ($spinUser->is_claimed) {
            return response()->json(['error' => 'Phần thưởng đã được nhận!'], 400);
        }

        $spinUser->update(['is_claimed' => true]);

        return response()->json(['success' => true, 'message' => 'Nhận thưởng thành công!']);
    }

    public function history()
    {
        $userSpins = SpinUser::where('user_id', Auth::id())
            ->with('spin.voucher')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.spins.history', compact('userSpins'));
    }
}
