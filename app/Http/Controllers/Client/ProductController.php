<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Texture;
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
        
        // Lấy danh sách chất liệu đang hoạt động để lọc
        $textures = Texture::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        
        // Quick view (server-rendered)
        $quickProduct = null;
        if ($request->filled('quick_view')) {
            $quickProduct = Product::with(['category', 'productImages', 'productVariants.color', 'productVariants.size', 'productVariants.texture'])
                ->where('is_active', 1)
                ->find($request->quick_view);
        }

        return view('client.products.index', [
            'categories'       => $categories,
            'selectedCategory' => $request->category,
            'quickProduct'     => $quickProduct,
            'textures'         => $textures,
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
