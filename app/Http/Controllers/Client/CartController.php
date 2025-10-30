<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// removed FormRequest for add to cart

class CartController extends Controller
{
    private function getOwnerKeys(): array
    {
        $userId = Auth::id();
        if ($userId) {
            return ['user_id' => $userId, 'session_id' => null];
        }
        return ['user_id' => null, 'session_id' => session()->getId()];
    }

    private function buildSessionItemId(int $productId, $variantId): string
    {
        return sha1('p'.$productId.'-v'.($variantId ?? 'null'));
    }

    private function getSessionCartItems(): array
    {
        return session()->get('cart.items', []);
    }

    private function putSessionCartItems(array $items): void
    {
        session()->put('cart.items', $items);
    }

    private function baseCartQuery()
    {
        $owner = $this->getOwnerKeys();
        return Cart::query()
            ->when($owner['user_id'], function ($q) use ($owner) {
                $q->where('user_id', $owner['user_id']);
            }, function ($q) use ($owner) {
                $q->where('session_id', $owner['session_id']);
            });
    }

    private function resolveItemPrice(Product $product, ?ProductVariant $variant): int|float
    {
        if ($variant && $variant->price) {
            return (float) $variant->price;
        }
        $base = $product->price_sale ?? $product->price;
        return (float) $base;
    }

    private function resolveVariantByAttributes(int $productId, ?string $sizeName, ?string $colorName, ?string $textureName): ?ProductVariant
    {
        // If no attribute provided, do NOT guess a variant
        if (!$sizeName && !$colorName && !$textureName) {
            return null;
        }
        $q = ProductVariant::query()->where('product_id', $productId)
            ->with(['size', 'color', 'texture']);
        if ($sizeName) {
            $q->whereHas('size', function($qq) use ($sizeName){ $qq->where('name', $sizeName); });
        }
        if ($colorName) {
            $q->whereHas('color', function($qq) use ($colorName){ $qq->where('name', $colorName); });
        }
        if ($textureName) {
            $q->whereHas('texture', function($qq) use ($textureName){ $qq->where('name', $textureName); });
        }
        return $q->first();
    }

    /** Display cart page */
    public function index()
    {
        // Unified: always read cart from DB, keyed by user_id or session_id
        $cartItems = $this->baseCartQuery()
        ->with([
            'product.productImages',
            'product.primaryImage',
            'variant.color',
            'variant.size',
            'variant.texture'
        ])
        ->get();

        $cartData = [];
        $total = 0;

        foreach ($cartItems as $item) {
            $variant = $item->variant;
            $product = $item->product;
            $size = $variant && $variant->size ? $variant->size->name : null;
            $color = $variant && $variant->color ? $variant->color->name : null;
            $texture = $variant && $variant->texture ? $variant->texture->name : null;
            $price = $variant && $variant->price ? (float) $variant->price : (float) ($product->price_sale ?? $product->price);
            
            $cartData[] = [
                'id' => $item->id,
                'product' => $product,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price' => $price,
                'size' => $size,
                'color' => $color,
                'texture' => $texture,
                'image_url' => $product->default_image_url,
                'line_total' => $price * $item->quantity,
            ];
            
            $total += $price * $item->quantity;
        }

        return view('client.cart.shopping', [
            'cartData' => $cartData,
            'total' => $total
        ]);
    }

    /** Add item to cart (redirect only) */
    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $variant = null;
        if ($request->filled('variant_id')) {
            $variant = ProductVariant::findOrFail($request->variant_id);
        } else {
            // Fallback: resolve by attribute names to avoid nulls
            $variant = $this->resolveVariantByAttributes(
                (int) $request->product_id,
                $request->input('size_name'),
                $request->input('color_name'),
                $request->input('texture_name')
            );
            if ($variant) {
                $request->merge(['variant_id' => $variant->id]);
            }
        }

        // Validate: if product has variants, a valid variant must be selected
        if ($product->productVariants()->exists() && !$variant) {
            if ($request->ajax() || $request->wantsJson() || $request->boolean('ajax')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn đầy đủ biến thể (kích thước, màu sắc, chất liệu) trước khi thêm vào giỏ.'
                ], 422);
            }
            return back()->with('error', 'Vui lòng chọn đầy đủ biến thể (kích thước, màu sắc, chất liệu).')->withInput();
        }

        if (!Auth::check()) {
            // Store guest cart in DB using session_id
            $owner = $this->getOwnerKeys();
            $existingItem = $this->baseCartQuery()
                ->where('product_id', $request->product_id)
                ->where('variant_id', $request->variant_id)
                ->first();
            if ($existingItem) {
                $existingItem->quantity += (int) $request->quantity;
                $existingItem->save();
            } else {
                Cart::create([
                    'user_id' => $owner['user_id'],
                    'session_id' => $owner['session_id'],
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity' => (int) $request->quantity,
                ]);
            }

            if ($request->ajax() || $request->wantsJson() || $request->boolean('ajax')) {
                $count = (int) $this->baseCartQuery()->sum('quantity');
                $item = $this->baseCartQuery()
                    ->where('product_id', $request->product_id)
                    ->where('variant_id', $request->variant_id)
                    ->with(['product', 'variant.size', 'variant.color', 'variant.texture'])
                    ->first();
                $price = $this->resolveItemPrice($product, $variant);
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm vào giỏ hàng',
                    'cart_count' => (int) $count,
                    'cart_item' => $item ? [
                        'id' => $item->id,
                        'product' => $item->product,
                        'variant' => $item->variant,
                        'variant_id' => $item->variant_id,
                        'quantity' => (int) $item->quantity,
                        'price' => $price,
                    ] : null,
                ]);
            }
            return redirect()->route('client.cart.index')->with('success', 'Đã thêm vào giỏ hàng');
        }

        $owner = $this->getOwnerKeys();
        $existingItem = $this->baseCartQuery()
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += (int) $request->quantity;
            $existingItem->save();
        } else {
            Cart::create([
                'user_id' => $owner['user_id'],
                'session_id' => $owner['session_id'],
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => (int) $request->quantity,
            ]);
        }

        if ($request->ajax() || $request->wantsJson() || $request->boolean('ajax')) {
            $count = (int) Cart::query()
                ->when($owner['user_id'], fn($q) => $q->where('user_id', $owner['user_id']), fn($q) => $q->where('session_id', $owner['session_id']))
                ->sum('quantity');
            $item = $this->baseCartQuery()
                ->where('product_id', $request->product_id)
                ->where('variant_id', $request->variant_id)
                ->with(['product', 'variant.size', 'variant.color', 'variant.texture'])
                ->first();
            $price = $this->resolveItemPrice($product, $variant);
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'cart_count' => (int) $count,
                'cart_item' => $item ? [
                    'id' => $item->id,
                    'product' => $item->product,
                    'variant' => $item->variant,
                    'variant_id' => $item->variant_id,
                    'quantity' => (int) $item->quantity,
                    'price' => $price,
                ] : null,
            ]);
        }

        return redirect()->route('client.cart.index')->with('success', 'Đã thêm vào giỏ hàng');
    }

    /** Remove item from cart (AJAX-friendly) */
    public function remove(Request $request, $id)
    {
        // Guest cart (session)
        if (!Auth::check()) {
            $items = $this->getSessionCartItems();
            if (isset($items[$id])) {
                unset($items[$id]);
                $this->putSessionCartItems($items);
            }
            $count = 0; foreach ($items as $it) { $count += (int) ($it['quantity'] ?? 1); }
            return response()->json(['success' => true, 'cart_count' => $count]);
        }

        // Authenticated cart (database)
        $owner = $this->getOwnerKeys();
        $cart = $this->baseCartQuery()->where('id', $id)->first();
        if ($cart) {
            $cart->delete();
        }
        $count = (int) $this->baseCartQuery()->sum('quantity');
        return response()->json(['success' => true, 'cart_count' => $count]);
    }
}
