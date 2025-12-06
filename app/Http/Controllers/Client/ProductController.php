<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị trang danh sách sản phẩm.
     */
    public function index(Request $request)
    {
        // Lấy danh mục từ database
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->get();

        // Query sản phẩm
        $query = Product::with(['category', 'primaryImage'])
            ->where('is_active', 1);

        // Filter theo danh mục
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Sắp xếp mặc định
        $query->orderBy('created_at', 'desc');

        // Phân trang
        $products = $query->paginate(12);

        return view('client.product.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->category,
        ]);
    }

    /**
     * Hiển thị trang chi tiết sản phẩm.
     */
    public function show($id)
    {
        $product = Product::with([
            'category',
            'productImages',
            'productVariants.color',
            'productVariants.size',
            'productVariants.texture',
            'reviews.user',
            'reviews.media',
            'reviews.experiences',
        ])
            ->where('is_active', 1)
            ->findOrFail($id);

        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::with(['category', 'primaryImage'])
            ->where('is_active', 1)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        // === Lấy tất cả review của sản phẩm ===
        $reviews = $product->reviews->sortByDesc('created_at')->where('status','public');
        //  Tính trung bình rating
        $avgRating = round($reviews->avg('rating'), 1);
        // ===  Lấy một vài đánh giá gần nhất ===
        $latestReviews = $reviews->take(5)->map(function ($review) {
            return [
                'id' => $review->id,
                'user' => [
                    'name' => $review->user->name ?? 'Ẩn danh',
                    'avatar' => $review->user->avatar ?? null,
                ],
                'rating' => (int)$review->rating,
                'comment' => $review->content,
                'variant' => $review->productVariant?->attribute_summary,
                'tags'  => $review->tags,
                'media' => $review->media->pluck('url'),
                'created_at' => $review->created_at->diffForHumans(),
            ];
        });
        
        return view('client.product.detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'latestReviews' => $latestReviews,
            'avgRating' => $avgRating,
        ]);
    }
}
