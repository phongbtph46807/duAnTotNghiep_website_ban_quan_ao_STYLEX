<?php

namespace App\Http\Controllers\Client;

use App\Events\Order as EventsOrder;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Jobs\SendOrderInvoiceMail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redis;

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

    private function processVnPayPayment(array $dataRequest)
    {
        if (!isset($dataRequest['total'])) {
            return back()->with('warning', 'Vui lòng kiểm tra lại giá sản phẩm !!!');
        }

        $vnp_Url       = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('client.checkout.vnpayReturn'); // callback về đây
        $vnp_TmnCode   = 'CW3MWMKN';
        $vnp_HashSecret = "2EQ9DCNFBR3H0GRQ4RCVHYTO1VZYXFLZ";
        $vnp_Locale    = 'vn';
        $vnp_TxnRef    = Str::uuid();
        $vnp_Amount    = $dataRequest['total'] * 100;
        $vnp_IpAddr    = request()->ip();
        $vnp_OrderInfo = "Thanh toán Vnpay";
        $vnp_OrderType = "Thanh toán hóa đơn";

        $inputData = [
            "vnp_Version"   => "2.1.0",
            "vnp_TmnCode"   => $vnp_TmnCode,
            "vnp_Amount"    => $vnp_Amount,
            "vnp_Command"   => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"  => "VND",
            "vnp_IpAddr"    => $vnp_IpAddr,
            "vnp_Locale"    => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef"    => $vnp_TxnRef,
        ];

        ksort($inputData);

        $query    = "";
        $hashdata = "";
        $i        = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode(string: $key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;

        if (!empty($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Lưu tạm dữ liệu request để callback dùng
        Redis::setex("order:$vnp_TxnRef", 900, json_encode([
            'order_id' => $vnp_TxnRef,
            'data'     => $dataRequest, // full_name, phone, email, address, total...
        ]));

        return redirect($vnp_Url);
    }

    public function place(Request $request)
    {
        $request->validate([
            'full_name'       => 'required|string|max:255',
            'phone'           => 'required|string|max:30',
            'email'           => 'nullable|email',
            'address'         => 'required|string|max:500',
            'city'            => 'required|string|max:120',
            'payment_method'  => 'required|in:cod,online',
        ]);

        $owner = $this->getOwnerKeys();
        $items = $this->baseCartQuery()->with(['product', 'variant'])->get();
        if ($items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng trống.');
        }

        // Tính tổng
        $total = 0.0;
        foreach ($items as $it) {
            $total += $this->resolveItemPrice($it->product, $it->variant) * (int) $it->quantity;
        }

        // Gộp request + total
        $dataRequest = array_merge($request->all(), [
            'total' => $total,
        ]);

        // Nếu thanh toán ONLINE -> chuyển sang VNPAY, chưa tạo order
        if ($request->payment_method === 'online') {
            return $this->processVnPayPayment($dataRequest, $items, $owner, $total);
        }

        // ======= CASE COD: tạo order luôn như cũ =======

        $order = DB::transaction(function () use ($request, $owner, $items, $total) {
            $order = Order::create([
                'user_id'        => $owner['user_id'],
                'session_id'     => $owner['session_id'],
                'code'           => 'OD' . strtoupper(substr(sha1(uniqid('', true)), 0, 10)),
                'full_name'      => $request->full_name,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'city'           => $request->city,
                'address'        => $request->address,
                'note'           => $request->note,
                'subtotal'       => (int) $total,
                'shipping_fee'   => 0,
                'discount'       => 0,
                'total'          => (int) $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid', // COD
                'status'         => 'pending',
            ]);

            foreach ($items as $it) {
                $price = (int) $this->resolveItemPrice($it->product, $it->variant);
                $qty   = (int) $it->quantity;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $it->product->id,
                    'variant_id' => $it->variant_id,
                    'quantity'   => $qty,
                    'price'      => $price,
                    'line_total' => $price * $qty,
                ]);
            }

            Cart::clearOwner($owner['user_id'], $owner['session_id']);

            try {
                Log::info('Dispatching EventsOrder from CheckoutController (COD)', [
                    'order_id'   => $order->id,
                    'order_code' => $order->code,
                ]);

                EventsOrder::dispatch(
                    $request->full_name,
                    $request->phone ?? '',
                    $request->email ?? '',
                    $request->address ?? '',
                    $total,
                    $items->toArray(),
                );
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch EventsOrder (COD)', [
                    'order_id' => $order->id ?? null,
                    'message'  => $e->getMessage(),
                ]);
            }

            return $order;
        });

        if (!empty($order->email)) {
            try {
                Log::info('Dispatching SendOrderInvoiceMail job', [
                    'order_id' => $order->id,
                    'email'    => $order->email,
                ]);
                SendOrderInvoiceMail::dispatch($order->id, $order->email);
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch SendOrderInvoiceMail job', [
                    'order_id' => $order->id ?? null,
                    'email'    => $order->email ?? null,
                    'message'  => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('client.checkout.thankyou', ['id' => $order->id]);
    }

    public function vnpayReturn(Request $request)
    {
        Log::info("=== VNPAY CALLBACK START ===");

        $vnp_HashSecret = "2EQ9DCNFBR3H0GRQ4RCVHYTO1VZYXFLZ";

        // Lấy toàn bộ tham số vnp_*
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) === "vnp_") {
                $inputData[$key] = $value;
            }
        }

        Log::info("VNPAY RAW INPUT:", $inputData);

        // 1. Lấy secure hash từ VNPAY gửi về
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;

        // 2. Bỏ các field hash ra khỏi mảng
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        // 3. Sắp xếp key
        ksort($inputData);

        // 4. BUILD hashData GIỐNG HỆT BÊN HÀM processVnPayPayment
        $i        = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $checkHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        Log::info("Hash Check:", [
            'generated_hash'  => $checkHash,
            'vnp_secure_hash' => $vnp_SecureHash,
            'hash_match'      => $checkHash === $vnp_SecureHash,
            'hashData'        => $hashData,
        ]);

        if ($checkHash !== $vnp_SecureHash) {
            Log::error("❌ Sai chữ ký – Redirect về giỏ hàng");
            return redirect()->route('client.cart.index')
                ->with('error', 'Chữ ký không hợp lệ. Thanh toán không thành công.');
        }

        // ==== TỪ ĐÂY TRỞ ĐI GIỮ NGUYÊN LOGIC CỦA BẠN ====
        $responseCode = $inputData['vnp_ResponseCode'] ?? null;
        $txnRef       = $inputData['vnp_TxnRef'] ?? null;

        Log::info("Transaction Status:", [
            'vnp_ResponseCode' => $responseCode,
            'vnp_TxnRef'       => $txnRef,
        ]);

        if ($responseCode !== '00') {
            Log::warning("❌ Thanh toán bị hủy hoặc thất bại. Không tạo order.");
            return redirect()->route('client.cart.index')
                ->with('error', 'Thanh toán không thành công hoặc đã bị hủy.');
        }


        // Lấy data trong redis
        $raw = Redis::get("order:$txnRef");

        Log::info("Redis Check:", [
            "key" => "order:$txnRef",
            "raw" => $raw,
        ]);

        if (!$raw) {
            Log::error("❌ Redis không tìm thấy dữ liệu order. Hết hạn hoặc sai key.");
            return redirect()->route('client.cart.index')
                ->with('error', 'Không tìm thấy thông tin đơn hàng tạm. Vui lòng đặt lại.');
        }

        $cache = json_decode($raw, true);
        Log::info("Redis Decoded:", $cache);

        $reqData = $cache['data'] ?? [];
        Log::info("Cached Request Data:", $reqData);

        // Lấy lại cart
        $owner = $this->getOwnerKeys();
        $items = $this->baseCartQuery()->with(['product', 'variant'])->get();

        Log::info("Cart Items:", [
            "count" => $items->count(),
            "items" => $items->toArray(),
        ]);

        if ($items->isEmpty()) {
            Log::error("❌ Giỏ hàng trống – Không thể tạo order");
            return redirect()->route('client.cart.index')
                ->with('error', 'Giỏ hàng trống hoặc đã thay đổi.');
        }

        // Tính tổng
        $total = 0.0;
        foreach ($items as $it) {
            $total += $this->resolveItemPrice($it->product, $it->variant) * (int) $it->quantity;
        }

        Log::info("Total Verification:", [
            'vnp_Amount'  => $inputData['vnp_Amount'],
            'cart_total'  => $total,
            'match'       => (int)$inputData['vnp_Amount'] === (int)($total * 100),
        ]);

        if ((int)$inputData['vnp_Amount'] !== (int)($total * 100)) {
            Log::error("❌ Số tiền không khớp. Không tạo order.");
            return redirect()->route('client.cart.index')
                ->with('error', 'Số tiền thanh toán không khớp.');
        }

        // Tạo order
        try {
            $order = DB::transaction(function () use ($reqData, $owner, $items, $total) {

                Log::info("=== TẠO ORDER BẮT ĐẦU ===");

                $order = Order::create([
                    'user_id'        => $owner['user_id'],
                    'session_id'     => $owner['session_id'],
                    'code'           => 'OD' . strtoupper(substr(sha1(uniqid('', true)), 0, 10)),
                    'full_name'      => $reqData['full_name'] ?? '',
                    'phone'          => $reqData['phone'] ?? '',
                    'email'          => $reqData['email'] ?? '',
                    'city'           => $reqData['city'] ?? '',
                    'address'        => $reqData['address'] ?? '',
                    'note'           => $reqData['note'] ?? null,
                    'subtotal'       => (int) $total,
                    'shipping_fee'   => 0,
                    'discount'       => 0,
                    'total'          => (int) $total,
                    'payment_method' => 'online',
                    'payment_status' => 'paid',
                    'status'         => 'pending',
                ]);

                Log::info("Order Created:", $order->toArray());

                foreach ($items as $it) {
                    $price = (int) $this->resolveItemPrice($it->product, $it->variant);
                    $qty   = (int) $it->quantity;

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $it->product->id,
                        'variant_id' => $it->variant_id,
                        'quantity'   => $qty,
                        'price'      => $price,
                        'line_total' => $price * $qty,
                    ]);
                }

                Log::info("Order Items Created");

                Cart::clearOwner($owner['user_id'], $owner['session_id']);
                Log::info("Cart Cleared");

                return $order;
            });

            Log::info("=== ORDER HOÀN THÀNH ===");

            Redis::del("order:$txnRef");
            Log::info("Redis key deleted: order:$txnRef");

            return redirect()->route('client.checkout.thankyou', ['id' => $order->id]);
        } catch (\Throwable $e) {
            Log::error("❌ EXCEPTION KHI TẠO ORDER", [
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return redirect()->route('client.cart.index')
                ->with('error', 'Có lỗi khi tạo đơn hàng.');
        }
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
            $order = Order::with(['items.product', 'items.variant.size', 'items.variant.color', 'items.variant.texture'])
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

    public function invoice(string $code)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        $order = Order::with(['items.product', 'items.variant.size', 'items.variant.color', 'items.variant.texture'])
            ->when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
            })
            ->where(function ($q) use ($code) {
                $q->where('code', $code)->orWhere('id', $code);
            })
            ->firstOrFail();

        $items = [];
        foreach ($order->items as $it) {
            $variant = $it->variant;
            $variantLabelParts = [];
            if ($variant && $variant->size) {
                $variantLabelParts[] = $variant->size->name;
            }
            if ($variant && $variant->color) {
                $variantLabelParts[] = $variant->color->name;
            }
            if ($variant && $variant->texture) {
                $variantLabelParts[] = $variant->texture->name;
            }

            $items[] = [
                'product_name' => $it->product?->name ?? ('#' . (string) $it->product_id),
                'variant_label' => implode(' / ', array_filter($variantLabelParts)),
                'quantity' => (int) $it->quantity,
                'unit_price' => (int) $it->price,
                'line_total' => (int) $it->line_total,
            ];
        }

        $d = [
            'order_code' => $order->code,
            'full_name' => $order->full_name,
            'phone' => $order->phone,
            'email' => $order->email,
            'city' => $order->city,
            'address' => $order->address,
            'note' => $order->note,
            'subtotal' => (int) $order->subtotal,
            'shipping_fee' => (int) $order->shipping_fee,
            'discount' => (int) $order->discount,
            'total' => (int) $order->total,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'status' => $order->status,
            'items' => $items,
            'placed_at' => optional($order->created_at)->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('admin.mails.invoice_order', ['d' => $d])->setPaper('a4');
        return $pdf->download('invoice_' . ($order->code ?? $order->id) . '.pdf');
    }
}


