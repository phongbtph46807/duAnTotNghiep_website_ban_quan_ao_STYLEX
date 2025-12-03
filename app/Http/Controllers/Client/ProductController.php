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
        // Lấy danh mục cha và danh mục con từ database
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->with(['children' => function($query) {
                $query->where('status', 1);
            }])
            ->get();
        
        // Query sản phẩm
        $query = Product::with(['category', 'primaryImage'])
            ->where('is_active', 1);
        
        // Filter theo danh mục (bao gồm cả category con)
        if ($request->has('category') && $request->category) {
            $categoryId = $request->category;
            // Lấy tất cả category con của category được chọn
            $categoryIds = Category::where('id', $categoryId)
                ->orWhere('parent_id', $categoryId)
                ->pluck('id')
                ->toArray();
            $query->whereIn('category_id', $categoryIds);
        }
        
        // Sắp xếp mặc định
        $query->orderBy('created_at', 'desc');
        
        // Lấy tất cả sản phẩm (không phân trang)
        $products = $query->get();

        // Quick view (server-rendered)
        $quickProduct = null;
        if ($request->filled('quick_view')) {
            $quickProduct = Product::with(['category', 'productImages', 'productVariants.color', 'productVariants.size', 'productVariants.texture'])
                ->where('is_active', 1)
                ->find($request->quick_view);
        }

        return view('client.products.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->category,
            'quickProduct' => $quickProduct,
        ]);
    }
    
    /**
     * Hiển thị trang chi tiết sản phẩm.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'productImages', 'productVariants.color', 'productVariants.size', 'productVariants.texture'])
            ->where('is_active', 1)
            ->findOrFail($id);
        
        // Lấy sản phẩm liên quan (cùng danh mục)
        $relatedProducts = Product::with(['category', 'primaryImage'])
            ->where('is_active', 1)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();
        
        return view('client.products.detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
