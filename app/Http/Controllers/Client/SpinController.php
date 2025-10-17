<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpinPrize;
use App\Models\UserSpin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SpinController extends Controller
{
    /**
     * Hiển thị trang vòng quay may mắn.
     */
    public function index()
    {
        return view('client.spin-lucky.index');
    }

    /**
     * Lấy danh sách các giải thưởng để hiển thị trên vòng quay.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrizes()
    {
        // Chỉ lấy những thông tin cần thiết cho client
        $prizes = SpinPrize::select('id', 'name')->get();
        return response()->json($prizes);
    }

    /**
     * Xử lý yêu cầu quay thưởng từ người dùng.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function spin(Request $request)
    {
        // Giả sử người dùng đã đăng nhập.
        // Trong ứng dụng thực tế, bạn nên dùng middleware('auth').
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Vui lòng đăng nhập để thực hiện quay thưởng.'], 401);
        }

        // === Logic chính: Chọn giải thưởng dựa trên tỷ lệ ===
        $prizes = SpinPrize::all();
        $winningPrize = null;

        // Tính tổng xác suất
        $totalProbability = $prizes->sum('probability');
        $randomNumber = mt_rand(1, $totalProbability);

        $cumulativeProbability = 0;
        foreach ($prizes as $prize) {
            $cumulativeProbability += $prize->probability;
            if ($randomNumber <= $cumulativeProbability) {
                $winningPrize = $prize;
                break;
            }
        }

        if (!$winningPrize) {
            // Trường hợp dự phòng nếu không có giải thưởng nào được chọn
            return response()->json(['error' => 'Đã có lỗi xảy ra trong quá trình quay. Vui lòng thử lại.'], 500);
        }

        // Bắt đầu một transaction để đảm bảo toàn vẹn dữ liệu
        DB::beginTransaction();
        try {
            // Lưu lại lịch sử lần quay
            UserSpin::create([
                'user_id' => $user->id,
                'prize_id' => $winningPrize->id,
                'spin_time' => now(),
                'is_claimed' => false, // Mặc định là chưa nhận
            ]);

            // TODO: Xử lý logic cộng thưởng cho người dùng nếu cần
            // Ví dụ: nếu type là 'voucher', bạn có thể thêm voucher cho user ở đây.
            // if ($winningPrize->type === 'voucher') { ... }

            DB::commit(); // Hoàn tất transaction

            // Trả về giải thưởng đã trúng cho client
            return response()->json([
                'success' => true,
                'prize' => [
                    'id' => $winningPrize->id,
                    'name' => $winningPrize->name,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu có lỗi
            // Log lỗi lại để debug
            // Log::error('Lỗi khi lưu kết quả quay thưởng: ' . $e->getMessage());
            return response()->json(['error' => 'Không thể lưu kết quả quay thưởng, vui lòng thử lại.'], 500);
        }
    }
}
