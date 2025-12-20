<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Tạo đánh giá sản phẩm từ đơn hàng
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:1000',
            'tags' => 'nullable|array',
        ]);

        $userId = Auth::id();
        $sessionId = session()->getId();

        // Kiểm tra user đã login hoặc có session để đánh giá
        if (!$userId && !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập hoặc có session để đánh giá.'
            ], 401);
        }

        // Kiểm tra quyền sở hữu đơn hàng
        $order = Order::where('id', $request->order_id)
            ->where(function($q) use ($userId, $sessionId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->firstOrFail();

        // Kiểm tra đơn hàng đã hoàn thành chưa
        if (!in_array($order->status, ['completed', 'delivered'])) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể đánh giá sản phẩm từ đơn hàng đã hoàn thành.'
            ], 400);
        }

        // Lấy order item
        $orderItem = $order->items()->findOrFail($request->order_item_id);
        $productId = $orderItem->getAttribute('product_id');
        $variantId = $orderItem->getAttribute('variant_id');

        // Kiểm tra đã đánh giá chưa (kiểm tra theo product_id + variant_id + order_id)
        $existingReviewQuery = Review::where('order_id', $order->id)
            ->where('product_id', $productId);
        
        // Xử lý null variant_id đúng cách (vì NULL = NULL = FALSE trong SQL)
        if ($variantId === null) {
            $existingReviewQuery = $existingReviewQuery->whereNull('product_variant_id');
        } else {
            $existingReviewQuery = $existingReviewQuery->where('product_variant_id', $variantId);
        }
        
        $existingReview = $existingReviewQuery->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này rồi.'
            ], 400);
        }

        // Tạo review
        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'product_variant_id' => $variantId ?? null,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'content' => $request->content,
            'tags' => $request->tags ?? [],
            'status' => 'public', // Match với getProductReviews() - chỉ hiển thị public
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá của bạn đã được gửi. Cảm ơn bạn!',
            'review' => $review->load('user', 'product')
        ]);
    }

    /**
     * Lấy danh sách review của một sản phẩm
     */
    public function getProductReviews($productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->where('status', 'public') // Chỉ hiển thị review công khai
            ->with(['user:id,name', 'productVariant', 'media'])
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }
}
