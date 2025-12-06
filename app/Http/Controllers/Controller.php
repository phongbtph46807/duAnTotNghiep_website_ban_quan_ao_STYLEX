<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;

abstract class Controller extends BaseController
{
    public function __construct()
    {
        // Share header mini-cart data from controller layer (MVC)
        [$items, $itemCount, $total] = $this->buildHeaderCartData();
        View::share('headerCartItems', $items);
        View::share('headerCartItemCount', $itemCount);
        View::share('headerCartTotal', $total);
    }

    protected function buildHeaderCartData(): array
    {
        $items = [];
        $itemCount = 0;
        $total = 0;

        if (Auth::check()) {
            $rows = Cart::with(['product', 'variant'])
                ->where('user_id', Auth::id())
                ->get();
            foreach ($rows as $row) {
                $variant = $row->variant;
                $product = $row->product;
                // Sử dụng cùng logic với CartController::resolveItemPrice()
                // Ưu tiên: variant price > product price_sale > product price
                if ($variant && $variant->price && $variant->price > 0) {
                    $price = (float) $variant->price;
                } elseif ($product->price_sale && $product->price_sale > 0) {
                    $price = (float) $product->price_sale;
                } else {
                    $price = (float) $product->price;
                }
                $itemCount += (int) $row->quantity;
                $total += $price * (int) $row->quantity;
                $items[] = [
                    'id' => $row->id,
                    'product' => $row->product,
                    'variant' => $variant,
                    'quantity' => (int) $row->quantity,
                    'price' => $price,
                ];
            }
            // Merge guest leftovers in session
            $sessionItems = session('cart.items', []);
            foreach ($sessionItems as $it) {
                $product = Product::find($it['product_id']);
                if (!$product) { continue; }
                $variant = isset($it['variant_id']) && $it['variant_id'] ? ProductVariant::find($it['variant_id']) : null;
                // Sử dụng cùng logic với CartController::resolveItemPrice()
                // Ưu tiên: variant price > product price_sale > product price
                if ($variant && $variant->price && $variant->price > 0) {
                    $price = (float) $variant->price;
                } elseif ($product->price_sale && $product->price_sale > 0) {
                    $price = (float) $product->price_sale;
                } else {
                    $price = (float) $product->price;
                }
                $key = sha1('p' . $product->id . '-v' . (($it['variant_id'] ?? null) ?: 'null'));
                $qty = (int) ($it['quantity'] ?? 1);
                $itemCount += $qty;
                $total += $price * $qty;
                $items[] = [
                    'id' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }
        } else {
            $sessionItems = session('cart.items', []);
            foreach ($sessionItems as $it) {
                $product = Product::find($it['product_id']);
                if (!$product) { continue; }
                $variant = isset($it['variant_id']) && $it['variant_id'] ? ProductVariant::find($it['variant_id']) : null;
                // Sử dụng cùng logic với CartController::resolveItemPrice()
                // Ưu tiên: variant price > product price_sale > product price
                if ($variant && $variant->price && $variant->price > 0) {
                    $price = (float) $variant->price;
                } elseif ($product->price_sale && $product->price_sale > 0) {
                    $price = (float) $product->price_sale;
                } else {
                    $price = (float) $product->price;
                }
                $key = sha1('p' . $product->id . '-v' . (($it['variant_id'] ?? null) ?: 'null'));
                $qty = (int) ($it['quantity'] ?? 1);
                $itemCount += $qty;
                $total += $price * $qty;
                $items[] = [
                    'id' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $qty,
                    'price' => $price,
                ];
            }
        }

        return [$items, $itemCount, $total];
    }
}
