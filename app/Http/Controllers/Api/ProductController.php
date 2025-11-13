<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(ProductFilterRequest $request)
    {
        $q = Product::query()->with(['category', 'productVariants.color', 'productVariants.size']);

        // only active/visible products (optional)
        $q->where('is_active', true);

        // keyword search
        $keyword = $request->input('keyword');
        if ($keyword) {
            // simple approach: name + short_description + description
            $q->where(function ($qq) use ($keyword) {
                $qq->where('name', 'like', "%{$keyword}%")
                    ->orWhere('short_description', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // filters
        if ($category = $request->input('category_id')) {
            $q->where('category_id', $category);
        }
        if ($brand = $request->input('brand_id')) {
            $q->where('brand_id', $brand);
        }

        // price range filter — consider base_price or variant price
        $min = $request->input('min_price');
        $max = $request->input('max_price');
        if ($min !== null && $max !== null) {
            $q->whereBetween('base_price', [$min, $max]);
        } elseif ($min !== null) {
            $q->where('base_price', '>=', $min);
        } elseif ($max !== null) {
            $q->where('base_price', '<=', $max);
        }

        // filters that require variants (color/size/texture/in_stock)
        if ($color = $request->input('color_id')) {
            $q->whereHas('variants', fn($qq) => $qq->where('color_id', $color));
        }
        if ($size = $request->input('size_id')) {
            $q->whereHas('variants', fn($qq) => $qq->where('size_id', $size));
        }
        if ($texture = $request->input('texture_id')) {
            $q->whereHas('variants', fn($qq) => $qq->where('texture_id', $texture));
        }
        if (($inStock = $request->input('in_stock')) !== null) {
            if ($inStock == 1) {
                // product has at least one variant stock > 0 or total_stock > 0
                $q->where(function ($qq) {
                    $qq->where('total_stock', '>', 0)
                        ->orWhereHas('variants', fn($q2) => $q2->where('stock_quantity', '>', 0));
                });
            } else {
                $q->where('total_stock', '<=', 0)
                    ->whereDoesntHave('variants', fn($q3) => $q3->where('stock_quantity', '>', 0));
            }
        }

        // sort
        $sort = $request->input('sort', 'relevance');
        if ($sort === 'price_asc') {
            $q->orderBy('base_price', 'asc');
        } elseif ($sort === 'price_desc') {
            $q->orderBy('base_price', 'desc');
        } elseif ($sort === 'newest') {
            $q->orderBy('created_at', 'desc');
        } else {
            // relevance: if keyword present, try to order by match (simple approach)
            if ($keyword) {
                // naive scoring: name matches first
                $q->orderByRaw("CASE WHEN name LIKE ? THEN 1 WHEN short_description LIKE ? THEN 2 ELSE 3 END", ["%{$keyword}%", "%{$keyword}%"]);
            } else {
                $q->orderBy('id', 'desc');
            }
        }

        // pagination
        $perPage = (int) $request->input('per_page', 15);
        $page = (int) $request->input('page', 1);

        // caching (simple example): cache by query string for 30s
        $cacheKey = 'products:' . md5(http_build_query($request->all()) . "|page={$page}|per={$perPage}");

        $result = Cache::remember($cacheKey, 30, function () use ($q, $perPage) {
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
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'productImages',
            'productVariants.color',
            'productVariants.size',
            'productVariants.texture',
            'reviews.user',
            'reviews.media',
            'reviews.productVariant'
        ]);
        return new ProductResource($product);
    }
}
