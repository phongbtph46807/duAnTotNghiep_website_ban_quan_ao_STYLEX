<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = Session::get('cart', []);
        $cartData = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::with('productImages')->find($productId);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                ];
                $total += $product->price * $item['quantity'];
            }
        }

        return view('client.cart.index', compact('cartData', 'total'));
    }

    /**
     * Add product to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity;
        $size = $request->size;
        $color = $request->color;

        $cart = Session::get('cart', []);

        // Check if product already in cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'quantity' => $quantity,
                'size' => $size,
                'color' => $color,
            ];
        }

        Session::put('cart', $cart);

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Update cart item
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Giỏ hàng đã được cập nhật',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Clear all cart
     */
    public function clear()
    {
        Session::forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Giỏ hàng đã được xóa',
        ]);
    }

    /**
     * Get cart count
     */
    public function getCartCount()
    {
        $cart = Session::get('cart', []);
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Get cart data for AJAX
     */
    public function getCart()
    {
        $cart = Session::get('cart', []);
        $cartData = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::with('productImages')->find($productId);
            if ($product) {
                $itemTotal = $product->price * $item['quantity'];
                $total += $itemTotal;
                
                // Get image URL from database
                $firstImage = $product->productImages->first();
                if ($firstImage) {
                    // Check if image exists in uploads folder
                    $imagePath = public_path($firstImage->image_path);
                    if (file_exists($imagePath)) {
                        $image = asset($firstImage->image_path);
                    } else {
                        // Fallback to sample image if not found
                        $image = asset('client/images/product-01.jpg');
                    }
                } else {
                    $image = asset('client/images/product-01.jpg');
                }
                
                $cartData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $image,
                    'quantity' => $item['quantity'],
                    'total' => $itemTotal,
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                ];
            }
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'cart' => $cartData,
            'count' => $cartCount,
            'total' => $total,
        ]);
    }
}

