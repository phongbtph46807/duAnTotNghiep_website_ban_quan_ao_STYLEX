<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Hiển thị trang danh sách yêu thích của user.
     */
    public function index(Request $request)
    {
        // Lớp bảo vệ: Chỉ cho user đã đăng nhập truy cập
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $user = $request->user();

        $products = $user->wishlistProducts()
            ->with('primaryImage')
            ->where('is_active', 1)
            ->paginate(10);

        return view('client.wishlist.index', compact('products'));
    }

    /**
     * Thêm hoặc Xóa sản phẩm khỏi Wishlist (Toggle)
     * Route: POST /wishlist/toggle
     * @param Request $request Chứa product_id trong body (JSON)
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        // 1. Xác thực dữ liệu
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        $productId = $request->input('product_id');

        // Kiểm tra User đã đăng nhập (Dù đã có middleware, đây là lớp bảo vệ API)
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để thực hiện chức năng này.'], 401);
        }

        // 2. Kiểm tra trạng thái hiện tại
        $isWishlisted = $user->wishlistProducts()->where('product_id', $productId)->exists();

        $product = Product::find($productId);
        if ($isWishlisted) {
            // Đã có: Tiến hành XÓA
            $user->wishlistProducts()->detach($productId);
            $action = 'removed';
            $message = 'Đã xóa "' . $product->name . '" khỏi danh sách yêu thích!';
        } else {
            // Chưa có: Tiến hành THÊM
            $user->wishlistProducts()->syncWithoutDetaching($productId);
            $action = 'added';
            $message = 'Đã thêm "' . $product->name . '" vào danh sách yêu thích!';
        }
        // 3. Trả về JSON cho Frontend
        $newCount = $user->wishlistProducts()->count();
        return response()->json([
            'status' => $action,
            'message' => $message,
            'newCount' => $newCount
        ]);
    }
}
