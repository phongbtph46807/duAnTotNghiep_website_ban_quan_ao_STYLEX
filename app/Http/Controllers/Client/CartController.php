<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display cart page
     */
    public function index()
    {
        $userId = Auth::id();
        
        $cartItems = Cart::with([
            'product.productImages',
            'product.primaryImage',
            'variant.color',
            'variant.size',
            'variant.texture'
        ])
        ->where('user_id', $userId)
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
                'image_url' => $item->product->default_image_url
            ];
            
            $total += $price * $item->quantity;
        }

        return view('client.cart.index', [
            'cartData' => $cartData,
            'total' => $total
        ]);
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);
        
        // Kiểm tra variant nếu có
        if ($request->variant_id) {
            $variant = ProductVariant::findOrFail($request->variant_id);
            if ($variant->product_id != $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant không thuộc sản phẩm này'
                ], 422);
            }
        }

        $userId = Auth::id();
        
        // Tìm cart item hiện có với cùng user, product, và variant
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            // Cập nhật số lượng nếu item đã tồn tại
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Tạo mới cart item
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
            'cart_item' => $cartItem->load(['product', 'variant'])
        ]);
    }

    /**
     * Get cart items
     */
    public function getCart()
    {
        $userId = Auth::id();
        
        $cartItems = Cart::with([
            'product.productImages',
            'product.primaryImage',
            'variant.color',
            'variant.size',
            'variant.texture'
        ])
        ->where('user_id', $userId)
        ->get();

        $totalAmount = 0;
        
        foreach ($cartItems as $item) {
            if ($item->variant && $item->variant->price) {
                $totalAmount += $item->variant->price * $item->quantity;
            } elseif ($item->product) {
                $price = $item->product->price_sale ?? $item->product->price;
                $totalAmount += $price * $item->quantity;
            }
        }

        return response()->json([
            'success' => true,
            'cart_items' => $cartItems,
            'total_amount' => $totalAmount,
            'item_count' => $cartItems->count()
        ]);
    }

    /**
     * Update cart item
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng',
            'cart_item' => $cartItem->load(['product', 'variant'])
        ]);
    }

    /**
     * Remove cart item
     */
    public function remove($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
        ]);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng'
        ]);
    }
}
