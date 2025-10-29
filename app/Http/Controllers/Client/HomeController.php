<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ.
     */
    public function index()
    {
        // 1. Lấy danh mục từ database
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->limit(6)
            ->get();
        
        // 2. Lấy sản phẩm từ database
        $products = Product::with(['category', 'primaryImage'])
            ->where('is_active', 1)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        // 3. Trả về view của trang chủ và "gán" (pass) biến vào view
        return view('client.index', [
            'categories' => $categories,
            'products' => $products
        ]);
    }
}
