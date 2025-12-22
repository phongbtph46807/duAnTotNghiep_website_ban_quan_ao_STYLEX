<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function index(ProductFilterRequest $request)
    {
        try {
        $q = Product::query()
            ->with([
                'brand',
                'category',
                'images',
                'variants.color',
                'variants.size',
            ]);

        // only active/visible products (optional)
        $q->where('is_active', true);

        // keyword search - hỗ trợ nhiều tên parameter: keyword, search, q
        $keyword = $request->input('keyword')
                ?? $request->input('search')
                ?? $request->input('q');

        // Trim và loại bỏ khoảng trắng thừa, chỉ tìm kiếm nếu keyword không rỗng
        $searchKeyword = null;
        if ($keyword) {
            $trimmedKeyword = trim($keyword);

            if (!empty($trimmedKeyword) && strlen($trimmedKeyword) > 0) {
                $searchKeyword = $trimmedKeyword;
                // Laravel tự động escape khi dùng where('column', 'like', ...)
                // Tìm kiếm trong name, description
                // Nếu muốn tìm theo SKU, tìm trong variants thông qua relationship
                $q->where(function($qq) use ($searchKeyword) {
                    $qq->where('name', 'like', "%{$searchKeyword}%")
                       ->orWhere('description', 'like', "%{$searchKeyword}%")
                       // Tìm kiếm trong SKU của variants
                       ->orWhereHas('variants', function($qvv) use ($searchKeyword) {
                           $qvv->where('sku', 'like', "%{$searchKeyword}%");
            });
                });
            }
        }

        // filters
        if ($category = $request->input('category_id')) {
            $categoryIds = Category::query()
                ->where('id', $category)
                ->orWhere('parent_id', $category)
                ->pluck('id')
                ->toArray();

            if (!empty($categoryIds)) {
                $q->whereIn('category_id', $categoryIds);
            } else {
            $q->where('category_id', $category);
            }
        }
        if ($brand = $request->input('brand_id')) {
            $q->where('brand_id', $brand);
        }

        // price range filter — consider base_price or variant price
        $min = $request->input('min_price');
        $max = $request->input('max_price');
        if ($min !== null || $max !== null) {
            $q->where(function($qq) use ($min, $max) {
                if ($min !== null) {
                    $qq->whereRaw('COALESCE(price_sale, price) >= ?', [$min]);
                }
                if ($max !== null) {
                    $qq->whereRaw('COALESCE(price_sale, price) <= ?', [$max]);
                }
            });
        }

        // filters that require variants (color/size/texture/in_stock)
        if ($color = $request->input('color_id')) {
            $q->whereHas('variants', fn($qq)=> $qq->where('color_id', $color));
        }
        if ($size = $request->input('size_id')) {
            $q->whereHas('variants', fn($qq)=> $qq->where('size_id', $size));
        }
        if ($texture = $request->input('texture_id')) {
            $q->whereHas('variants', fn($qq)=> $qq->where('texture_id', $texture));
        }
        if (($inStock = $request->input('in_stock')) !== null) {
            if ($inStock == 1) {
                // product has at least one variant stock > 0 or total_stock > 0
                $q->where(function($qq){
                    $qq->where('total_stock','>',0)
                       ->orWhereHas('variants', fn($q2)=> $q2->where('stock_quantity','>',0));
                });
            } else {
                $q->where('total_stock','<=',0)
                  ->whereDoesntHave('variants', fn($q3)=> $q3->where('stock_quantity','>',0));
            }
        }

        // sort
        $sort = $request->input('sort','relevance');
        if ($sort === 'price_asc') {
            $q->orderByRaw('COALESCE(price_sale, price) ASC');
        } elseif ($sort === 'price_desc') {
            $q->orderByRaw('COALESCE(price_sale, price) DESC');
        } elseif ($sort === 'newest') {
            $q->orderBy('created_at','desc');
        } else {
            // relevance: if keyword present, try to order by match (simple approach)
            if ($searchKeyword) {
                // naive scoring: name matches first, then description
                $q->orderByRaw("CASE WHEN name LIKE ? THEN 1 WHEN description LIKE ? THEN 2 ELSE 3 END", ["%{$searchKeyword}%","%{$searchKeyword}%"]);
            } else {
                $q->orderBy('id','desc');
            }
        }

            // pagination - tăng per_page lên số lớn để hiển thị nhiều sản phẩm
        $perPage = (int) $request->input('per_page', 15);
            // Giới hạn tối đa 10000 sản phẩm mỗi trang để tránh quá tải
            if ($perPage > 10000) {
                $perPage = 10000;
            }
        $page = (int) $request->input('page', 1);

        // caching (simple example): cache by query string for 30s
        $cacheKey = 'products:' . md5(http_build_query($request->all()) . "|page={$page}|per={$perPage}");

        $result = Cache::remember($cacheKey, 30, function() use ($q, $perPage) {
            return $q->paginate($perPage);
        });

        return ProductResource::collection($result)
            ->additional([
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Product API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Trả về 200 với data rỗng thay vì 500 để frontend không bị lỗi
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 15,
                    'total' => 0,
                ],
                'error' => config('app.debug') ? $e->getMessage() : 'Có lỗi xảy ra khi tải sản phẩm'
            ], 200);
        }
    }

    public function show(Product $product)
    {
        $product->load(['brand','category','images','variants.color','variants.size','variants.texture']);
        return new ProductResource($product);
    }
}
