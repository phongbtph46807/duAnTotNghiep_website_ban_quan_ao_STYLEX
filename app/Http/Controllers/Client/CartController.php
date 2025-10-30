<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /** Display cart page */
    public function index()
    {
        if (!Auth::check()) {
            $sessionItems = $this->getSessionCartItems();
            $cartData = [];
            $total = 0;
            foreach ($sessionItems as $it) {
                $product = Product::find($it['product_id']);
                $variant = $it['variant_id'] ? ProductVariant::find($it['variant_id']) : null;
                if (!$product) { continue; }
                $price = $this->resolveItemPrice($product, $variant);
                $qty = (int) $it['quantity'];
                $cartData[] = [
                    'id' => $this->buildSessionItemId($product->id, $it['variant_id'] ?? null),
                    'product' => $product,
                    'variant_id' => $it['variant_id'] ?? null,
                    'quantity' => $qty,
                    'price' => $price,
                    'size' => $variant && $variant->size ? $variant->size->name : null,
                    'color' => $variant && $variant->color ? $variant->color->name : null,
                    'texture' => $variant && $variant->texture ? $variant->texture->name : null,
                    'image_url' => $product->default_image_url,
                    'line_total' => $price * $qty,
                ];
                $total += $price * $qty;
            }

            return view('client.cart.shopping', [
                'cartData' => $cartData,
                'total' => $total
            ]);
        }

        // If user just logged in and still has guest items in session, migrate them to DB then clear
        $sessionItems = $this->getSessionCartItems();
        if (!empty($sessionItems)) {
            $owner = $this->getOwnerKeys();
            foreach ($sessionItems as $it) {
                $existing = $this->baseCartQuery()
                    ->where('product_id', $it['product_id'])
                    ->where('variant_id', $it['variant_id'] ?? null)
                    ->first();
                if ($existing) {
                    $existing->quantity += (int) ($it['quantity'] ?? 1);
                    $existing->save();
                } else {
                    Cart::create([
                        'user_id' => $owner['user_id'],
                        'session_id' => $owner['session_id'],
                        'product_id' => $it['product_id'],
                        'variant_id' => $it['variant_id'] ?? null,
                        'quantity' => (int) ($it['quantity'] ?? 1),
                    ]);
                }
            }
            // Clear guest cart to avoid stale header counts
            $this->putSessionCartItems([]);
        }

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
            $price = 0;
            $size = null;
            $color = null;
            $texture = null;
            
            if ($item->variant && $item->variant->price) {
                $price = $item->variant->price;
                $size = $item->variant->size ? $item->variant->size->name : null;
                $color = $item->variant->color ? $item->variant->color->name : null;
                $texture = $item->variant->texture ? $item->variant->texture->name : null;
            } elseif ($item->product) {
                $price = $item->product->price_sale ?? $item->product->price;
            }
            
            $cartData[] = [
                'id' => $item->id,
                'product' => $item->product,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price' => $price,
                'size' => $size,
                'color' => $color,
                'texture' => $texture,
                'image_url' => $item->product->default_image_url,
                'line_total' => $price * $item->quantity,
            ];
            
            $total += $price * $item->quantity;
        }

        // Merge guest session items (if any) so header badge and page match
        $sessionItems = $this->getSessionCartItems();
        if (!empty($sessionItems)) {
            foreach ($sessionItems as $it) {
                $product = Product::find($it['product_id']);
                if (!$product) { continue; }
                $variant = $it['variant_id'] ? ProductVariant::find($it['variant_id']) : null;
                $price = $this->resolveItemPrice($product, $variant);
                $qty = (int) ($it['quantity'] ?? 1);
                $cartData[] = [
                    'id' => $this->buildSessionItemId($product->id, $it['variant_id'] ?? null),
                    'product' => $product,
                    'variant_id' => $it['variant_id'] ?? null,
                    'quantity' => $qty,
                    'price' => $price,
                    'size' => $variant && $variant->size ? $variant->size->name : null,
                    'color' => $variant && $variant->color ? $variant->color->name : null,
                    'texture' => $variant && $variant->texture ? $variant->texture->name : null,
                    'image_url' => $product->default_image_url,
                    'line_total' => $price * $qty,
                ];
                $total += $price * $qty;
            }
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
        $variant = $request->variant_id ? ProductVariant::findOrFail($request->variant_id) : null;

        if (!Auth::check()) {
            $items = $this->getSessionCartItems();
            $key = $this->buildSessionItemId($product->id, $request->variant_id);
            if (isset($items[$key])) {
                $items[$key]['quantity'] += (int) $request->quantity;
            } else {
                $items[$key] = [
                    'id' => $key,
                    'product_id' => $product->id,
                    'variant_id' => $request->variant_id,
                    'quantity' => (int) $request->quantity,
                ];
            }
            $this->putSessionCartItems($items);
            if ($request->ajax() || $request->wantsJson() || $request->boolean('ajax')) {
                // compute cart count (sum quantities)
                $count = 0; foreach ($items as $it) { $count += (int) ($it['quantity'] ?? 1); }
                $price = $this->resolveItemPrice($product, $variant);
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm vào giỏ hàng',
                    'cart_count' => $count,
                    'cart_item' => [
                        'id' => $key,
                        'product' => $product,
                        'variant' => $variant,
                        'variant_id' => $request->variant_id,
                        'quantity' => (int) $items[$key]['quantity'],
                        'price' => $price,
                    ],
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
