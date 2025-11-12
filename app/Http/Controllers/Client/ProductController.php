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

        if ($request->has('category')) {
            $categoryId = $request->input('category');
            
            $category = Category::with('childrenRecursive')->find($categoryId);
            //Nếu category tồn tại
            if ($category) {
                // Lấy danh sách ID của chính nó VÀ TẤT CẢ con cháu
                $allCategoryIds = $category->getAllDescendantIdsAndSelf();
                // Lọc tất cả sản phẩm có category_id nằm trong danh sách ID này
                $query->whereIn('category_id', $allCategoryIds);
            } else {
                // Trường hợp ai đó nhập URL linh tinh với ID không tồn tại
                $query->where('category_id', $categoryId); // Sẽ không trả về gì
            }
        }
        
        // Sắp xếp mặc định
        $query->orderBy('created_at', 'desc');
        
        // Phân trang
        $products = $query->paginate(12);
        $products->appends($request->query());
        
        return view('client.product.index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
    
    /**
     * Hiển thị trang chi tiết sản phẩm.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'productImages', 'productVariants'])
            ->where('is_active', 1)
            ->findOrFail($id);
        
        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::with(['category', 'primaryImage'])
            ->where('is_active', 1)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();
        
        return view('client.product.detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
