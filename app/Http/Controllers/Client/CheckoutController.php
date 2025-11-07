<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    private function getOwnerKeys(): array
    {
        $userId = Auth::id();
        if ($userId) {
            return ['user_id' => $userId, 'session_id' => null];
        }
        return ['user_id' => null, 'session_id' => session()->getId()];
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

    private function resolveItemPrice(Product $product, ?ProductVariant $variant): float
    {
        if ($variant && $variant->price) {
            return (float) $variant->price;
        }
        $base = $product->price_sale ?? $product->price;
        return (float) $base;
    }

    public function index()
    {
        $items = $this->baseCartQuery()
            ->with(['product.productImages', 'product.primaryImage', 'variant.size', 'variant.color', 'variant.texture'])
            ->get();

        $cartData = [];
        $total = 0.0;
        foreach ($items as $it) {
            $product = $it->product;
            if (!$product) {
                continue;
            }
            $variant = $it->variant;
            $price = $this->resolveItemPrice($product, $variant);
            $line = $price * (int) $it->quantity;
            $total += $line;
            $cartData[] = [
                'id' => $it->id,
                'product' => $product,
                'variant' => $variant,
                'variant_id' => $it->variant_id,
                'quantity' => (int) $it->quantity,
                'price' => $price,
                'line_total' => $line,
            ];
        }

        return view('client.checkout.index', [
            'cartData' => $cartData,
            'total' => $total,
        ]);
    }

    public function place(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:120',
            'payment_method' => 'required|in:cod,online',
        ]);

        $owner = $this->getOwnerKeys();
        $items = $this->baseCartQuery()->with(['product', 'variant'])->get();
        if ($items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng trống.');
        }

        // Tính tổng để gửi sang cổng thanh toán (nếu cần)
        $total = 0.0;
        foreach ($items as $it) {
            $total += $this->resolveItemPrice($it->product, $it->variant) * (int) $it->quantity;
        }

        // Tạo Order + OrderItems (transaction)
        $order = DB::transaction(function () use ($request, $owner, $items, $total) {
            $order = Order::create([
                'user_id' => $owner['user_id'],
                'session_id' => $owner['session_id'],
                'code' => 'OD' . strtoupper(substr(sha1(uniqid('', true)), 0, 10)),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'city' => $request->city,
                'address' => $request->address,
                'note' => $request->note,
                'subtotal' => (int) $total,
                'shipping_fee' => 0,
                'discount' => 0,
                'total' => (int) $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'online' ? 'paid' : 'unpaid',
                'status' => 'pending',
            ]);

            foreach ($items as $it) {
                $price = (int) $this->resolveItemPrice($it->product, $it->variant);
                $qty = (int) $it->quantity;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $it->product->id,
                    'variant_id' => $it->variant_id,
                    'quantity' => $qty,
                    'price' => $price,
                    'line_total' => $price * $qty,
                ]);
            }

            // Clear cart after order created
            Cart::clearOwner($owner['user_id'], $owner['session_id']);
            return $order;
        });

        $msg = $request->payment_method === 'online'
            ? 'Thanh toán online thành công! Mã đơn: ' . $order->code
            : 'Đặt hàng thành công (COD). Mã đơn: ' . $order->code;
        return redirect()->route('client.checkout.thankyou', ['id' => $order->id]);
    }

    public function thankyou($id)
    {
        $order = Order::with('items.product', 'items.variant.size', 'items.variant.color', 'items.variant.texture')->findOrFail($id);
        return view('client.checkout.thankyou', compact('order'));
    }
    public function track(Request $request)
    {
        $order = null;
        if ($request->has('code') && $request->code) {
            $order = Order::with([
                'items.product',
                'items.product.productVariants.size',
                'items.product.productVariants.color',
                'items.product.productVariants.texture',
                'items.variant.size',
                'items.variant.color',
                'items.variant.texture'
            ])
                ->where('code', $request->code)->orWhere('id', $request->code)
                ->orWhere('phone', $request->code)->latest()->first();
        }
        return view('client.order.track', compact('order'));
    }

    public function orderList()
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        $orders = \App\Models\Order::query()
            ->when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when(!$userId, function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
            })
            ->orderByDesc('created_at')->with(['items.product', 'items.variant.size', 'items.variant.color', 'items.variant.texture'])->get();
        return view('client.order.index', compact('orders'));
    }

    public function destroy($id)
    {
        $order = Order::where('id', $id)
                      ->where('user_id', auth::id())
                      ->firstOrFail();

        if (in_array($order->status, ['pending', 'processing'])) {
            $order->delete();
            return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công!');
        }

        return redirect()->back()->with('error', 'Không thể xoá đơn hàng đã giao hoặc đã huỷ!');
    }
}
