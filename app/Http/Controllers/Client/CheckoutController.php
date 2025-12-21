<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
// use App\Jobs\SendOrderInvoiceMail;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingCarrier;
use App\Models\TaxRate;
use App\Services\VoucherService;
// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\LoyaltyService;
use App\Services\NotificationService;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Log;


class CheckoutController extends Controller
{
    protected VoucherService $voucherService;
    protected LoyaltyService $loyaltyService;
    protected NotificationService $notificationService;

    public function __construct(VoucherService $voucherService, LoyaltyService $loyaltyService, NotificationService $notificationService)
    {
        $this->voucherService = $voucherService;
        $this->loyaltyService = $loyaltyService;
        $this->notificationService = $notificationService;
    }
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
        return $this->cartQueryForOwner($this->getOwnerKeys());
    }

    private function cartQueryForOwner(array $owner)
    {
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

    public function index(Request $request)
    {
        $items = $this->baseCartQuery()
            ->with([
                'product.productImages',
                'product.primaryImage',
                'product.productVariants.texture',
                'variant.size',
                'variant.color',
                'variant.texture'
            ])
            ->get();

        // Nếu giỏ hàng trống thì không cho vào trang thanh toán
        if ($items->isEmpty()) {
            return redirect()
                ->route('client.cart.index')
                ->with('error', 'Giỏ hàng trống, không thể thanh toán. Vui lòng thêm sản phẩm trước.');
        }

        $cartData = [];
        $subtotal = 0.0;
        foreach ($items as $it) {
            $product = $it->product;
            if (!$product) {
                continue;
            }
            $variant = $it->variant;
            $price = $this->resolveItemPrice($product, $variant);
            $line = $price * (int) $it->quantity;
            $subtotal += $line;

            $textureNames = [];
            if ($product && $product->relationLoaded('productVariants')) {
                $textureNames = $product->productVariants
                    ->map(function ($variant) {
                        return optional($variant->texture)->name;
                    })
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
            }

            $cartData[] = [
                'id' => $it->id,
                'product' => $product,
                'variant' => $variant,
                'variant_id' => $it->variant_id,
                'quantity' => (int) $it->quantity,
                'price' => $price,
                'line_total' => $line,
                'textures' => $textureNames,
            ];
        }

        // Calculate discount using VoucherService (trước thuế & phí ship)
        $voucherResult = $this->voucherService->recalculateDiscount($subtotal);
        $voucherDiscount = $voucherResult['discount'];
        $totalAfterVoucher = $voucherResult['total'];
        $appliedVoucher = $this->voucherService->getAppliedVoucher();

        // Tính loyalty discount nếu user đã đăng nhập
        $loyaltyDiscount = 0;
        $currentTier = null;
        if (Auth::check()) {
            $user = Auth::user();
            $currentTier = $this->loyaltyService->getCurrentTier($user);
            if ($currentTier) {
                // Áp dụng discount trên subtotal sau khi trừ voucher
                $loyaltyDiscount = $this->loyaltyService->calculateDiscount($user, $totalAfterVoucher);
            }
        }

        // Tổng giảm giá = voucher discount + loyalty discount
        $totalDiscount = $voucherDiscount + $loyaltyDiscount;

        // Ưu tiên ID đơn vị vận chuyển từ query (chọn từ giỏ hàng) nếu có
        $shippingCarrierId = $request->query('shipping_carrier_id');
        if ($shippingCarrierId) {
            session([
                'cart.shipping_carrier_id' => (int) $shippingCarrierId,
            ]);
        } else {
            $shippingCarrierId = session('cart.shipping_carrier_id');
        }

        // Lấy đơn vị vận chuyển đã chọn từ session (nếu có)
        $shippingCarrier = $shippingCarrierId
            ? ShippingCarrier::where('active', true)->find($shippingCarrierId)
            : null;

        $shippingFee = $shippingCarrier ? (float) ($shippingCarrier->fee ?? 0) : 0.0;

        // Lấy thuế mặc định (VD: chọn mức VAT có rate lớn nhất)
        $taxRate = TaxRate::orderByDesc('rate')->first();
        $taxAmount = 0.0;
        if ($taxRate && $subtotal > 0) {
            $taxAmount = (float) round($subtotal * (float) $taxRate->rate);
        }

        // Tổng cuối cùng = tiền hàng - giảm giá + thuế + phí ship
        $grandTotal = max(0, $subtotal - $totalDiscount + $taxAmount + $shippingFee);

        return view('client.checkout.index', [
            'cartData' => $cartData,
            'subtotal' => $subtotal,
            'discount' => $totalDiscount,
            'voucherDiscount' => $voucherDiscount,
            'loyaltyDiscount' => $loyaltyDiscount,
            'currentTier' => $currentTier,
            'total' => $grandTotal,
            'taxAmount' => $taxAmount,
            'taxRate' => $taxRate,
            'shippingFee' => $shippingFee,
            'shippingCarrier' => $shippingCarrier,
            'voucher' => $appliedVoucher,
        ]);
    }


    public function thankyou($id)
    {
        try {
        $order = Order::with('items.product', 'items.variant.size', 'items.variant.color', 'items.variant.texture')->findOrFail($id);
        return view('client.checkout.thankyou', compact('order'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }
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
                'items.variant.texture',
                'reviews' // Load reviews để kiểm tra đã đánh giá chưa
            ])
                ->where('code', $request->code)->orWhere('id', $request->code)
                ->orWhere('phone', $request->code)->latest()->first();

            // Nếu có order, kiểm tra xem sản phẩm nào đã được đánh giá
            if ($order) {
                $reviewed = $order->reviews->map(fn($r) => [
                    'p' => $r->product_id,
                    'v' => $r->product_variant_id,
                ])->toArray();
                foreach ($order->items as $item) {
                    $item->is_reviewed = collect($reviewed)->contains(function($rev) use ($item) {
                        return $rev['p'] == $item->product_id &&
                            (($rev['v'] === null && $item->variant_id === null) || ($rev['v'] == $item->variant_id));
                    });
                }
            }
        }
        return view('client.orders.track', compact('order'));
    }

    public function orderList(Request $request)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();
        $statusTabs = [
            'pending' => 'Chờ xác nhận',
            'processing' => 'Đang xử lý',
            'shipping' => 'Chờ giao hàng',
            'delivered' => 'Đã giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Hủy', // Gộp cancel_request và cancelled
            'returned' => 'Trả hàng', // Gộp return_request và returned
        ];
        $statusFilter = $request->query('status');
        if ($statusFilter && !array_key_exists($statusFilter, $statusTabs)) {
            $statusFilter = null;
        }

        $orders = \App\Models\Order::query()
            ->when($userId, function ($q) use ($userId, $sessionId) {
                // Ưu tiên đơn gắn với user hiện tại
                $q->where(function ($qq) use ($userId, $sessionId) {
                    $qq->where('user_id', $userId);

                    // Kèm theo các đơn chưa gắn user nhưng cùng session hiện tại (nếu có)
                    if ($sessionId) {
                        $qq->orWhere(function ($qq2) use ($sessionId) {
                            $qq2->whereNull('user_id')
                                ->where('session_id', $sessionId);
                        });
                    }

                    // Kèm theo các đơn chưa gắn user nhưng trùng email đăng nhập
                    $email = Auth::user()->email ?? null;
                    if ($email) {
                        $qq->orWhere(function ($qq3) use ($email) {
                            $qq3->whereNull('user_id')
                                ->where('email', $email);
                        });
                    }
                });
            })
            ->when(!$userId, function ($q) use ($sessionId) {
                // Khách chưa đăng nhập: lọc theo session hiện tại
                $q->where('session_id', $sessionId);
            })
            ->when($statusFilter, function ($q) use ($statusFilter) {
                // Gộp các trạng thái liên quan
                if ($statusFilter === 'cancelled') {
                    // Hiển thị cả cancel_request và cancelled
                    $q->whereIn('status', ['cancel_request', 'cancelled']);
                } elseif ($statusFilter === 'returned') {
                    // Hiển thị cả return_request và returned
                    $q->whereIn('status', ['return_request', 'returned']);
                } else {
                    $q->where('status', $statusFilter);
                }
            })
            ->orderByDesc('created_at')
            ->with([
                'items.product.productVariants.texture',
                'items.product.productImages',
                'items.product.primaryImage',
                'items.variant',
                'items.variant.size',
                'items.variant.color',
                'items.variant.texture',
                'reviews' // Load reviews để kiểm tra đã đánh giá chưa
            ])
            ->get();

        // Đánh dấu sản phẩm đã được đánh giá
        foreach ($orders as $order) {
            if (in_array($order->status, ['completed', 'delivered'])) {
                $reviewed = $order->reviews->map(fn($r) => [
                    'p' => $r->product_id,
                    'v' => $r->product_variant_id,
                ])->toArray();
                foreach ($order->items as $item) {
                    $item->is_reviewed = collect($reviewed)->contains(function($rev) use ($item) {
                        return $rev['p'] == $item->product_id &&
                            (($rev['v'] === null && $item->variant_id === null) || ($rev['v'] == $item->variant_id));
                    });
                }
            }
        }

        return view('client.orders.index', [
            'orders' => $orders,
            'activeStatus' => $statusFilter,
            'statusTabs' => $statusTabs,
        ]);
    }

    public function pollStatus(Request $request)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        $orders = \App\Models\Order::query()
            ->select('id','code','status','updated_at')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->get();

        return response()->json([
            'data' => $orders->map(fn($o) => [
                'id' => $o->id,
                'code' => $o->code,
                'status' => $o->status,
                'updated_at' => optional($o->updated_at)->timestamp,
            ])
        ]);
    }

    public function cancel(Request $request, Order $order)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        if (($order->user_id && $order->user_id !== $userId) ||
            (!$order->user_id && $order->session_id !== $sessionId)
        ) {
            abort(403, 'Bạn không có quyền hủy đơn này.');
        }

        if (!in_array($order->status, ['pending','processing','shipping'])) {
            return back()->with('error', 'Chỉ có thể hủy đơn ở trạng thái chờ xác nhận / đang xử lý / đang giao.');
        }

        $data = $request->validate([
            'cancel_reason' => 'required|string|max:1000',
            'cancel_images.*' => 'nullable|image|max:2048', // mỗi ảnh tối đa 2MB
        ]);

        $storedImages = [];
        if ($request->hasFile('cancel_images')) {
            foreach ($request->file('cancel_images') as $file) {
                if ($file && $file->isValid()) {
                    $storedImages[] = $file->store('cancel-reasons', 'public');
                }
            }
        }

        $order->status = 'cancel_request';
        $order->cancel_reason = $data['cancel_reason'] ?? null;
        $order->cancel_images = $storedImages ?: null;
        $order->save();

        // Broadcast event để cập nhật badge realtime cho admin
        broadcast(new OrderStatusUpdated($order->fresh()))->toOthers();

        return back()->with('success', 'Yêu cầu hủy đã được gửi. Vui lòng đợi admin duyệt.');
    }

    public function requestReturn(Request $request, Order $order)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        if (($order->user_id && $order->user_id !== $userId) ||
            (!$order->user_id && $order->session_id !== $sessionId)) {
            abort(403, 'Bạn không có quyền thao tác đơn này.');
        }

        if (!in_array($order->status, ['completed','delivered'])) {
            return back()->with('error', 'Chỉ có thể yêu cầu trả hàng khi đơn đã hoàn thành/giao xong.');
        }

        $data = $request->validate([
            'return_reason' => 'required|string|max:1000',
            'return_images.*' => 'nullable|image|max:2048',
        ]);

        $storedImages = [];
        if ($request->hasFile('return_images')) {
            foreach ($request->file('return_images') as $file) {
                if ($file && $file->isValid()) {
                    $storedImages[] = $file->store('return-reasons', 'public');
                }
            }
        }

        $order->status = 'return_request';
        $order->return_reason = $data['return_reason'] ?? null;
        $order->return_images = $storedImages ?: null;
        $order->save();

        // Broadcast event để cập nhật badge realtime cho admin
        broadcast(new OrderStatusUpdated($order->fresh()))->toOthers();

        return back()->with('success', 'Yêu cầu trả hàng đã được gửi. Vui lòng đợi admin duyệt.');
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:1000',
            'variant_id' => 'nullable|integer',
        ]);

        $userId = Auth::id();
        $sessionId = session()->getId();

        // Kiểm tra quyền sở hữu đơn hàng
        $order = Order::where('id', $request->order_id)
            ->where(function ($q) use ($userId, $sessionId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })
            ->firstOrFail();

        // Kiểm tra đơn hàng đã hoàn thành chưa
        if (!in_array($order->status, ['completed', 'delivered'])) {
            return back()->with('error', 'Chỉ có thể đánh giá sản phẩm từ đơn hàng đã hoàn thành.');
        }

        // Lấy order item
        $orderItem = $order->items()->findOrFail($request->order_item_id);
        $productId = $orderItem->product_id;
        // Ưu tiên variant gửi lên (đảm bảo đúng biến thể được chọn)
        $variantId = $request->input('variant_id') ?: $orderItem->variant_id;

        // Kiểm tra variant_id có tồn tại trong product_variants không
        $validVariantId = null;
        if ($variantId) {
            $variantExists = \App\Models\ProductVariant::where('id', $variantId)->exists();
            if ($variantExists) {
                $validVariantId = $variantId;
            }
        }

        // Kiểm tra đã đánh giá chưa
        $existingReview = \App\Models\Review::where('order_id', $order->id)
            ->where(function ($q) use ($productId, $validVariantId) {
                $q->where('product_id', $productId);
                if ($validVariantId) {
                    $q->where('product_variant_id', $validVariantId);
                } else {
                    $q->whereNull('product_variant_id');
                }
            })
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        // Lấy thông tin biến thể để lưu hiển thị
        $variantColor = $orderItem->variant?->color?->name;
        $variantSize = $orderItem->variant?->size?->name;

        // Tạo review
        \App\Models\Review::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'product_variant_id' => $validVariantId,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'content' => $request->input('content', ''),
            'tags' => [],
            'status' => 'public',
            'variant_color' => $variantColor,
            'variant_size' => $variantSize,
            'media' => [],
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }

    public function place(Request $request)
    {
        $request->validate([
            'buyer_full_name' => 'required|string|max:255',
            'buyer_phone'     => 'required|string|max:30',
            'buyer_email'     => 'nullable|email',
            'full_name'       => 'required|string|max:255',
            'phone'           => 'required|string|max:30',
            'email'           => 'nullable|email',
            'address'         => 'required|string|max:500',
            'city'            => 'required|string|max:120',
            'payment_method'  => 'required|in:cod,online',
        ], [
            'buyer_full_name.required'    => 'Vui lòng nhập tên người đặt',
            'buyer_full_name.string'      => 'Tên người đặt phải là chữ',
            'buyer_full_name.max'         => 'Tên người đặt không được vượt quá 255 ký tự',
            'buyer_phone.required'        => 'Vui lòng nhập số điện thoại người đặt',
            'buyer_phone.string'          => 'Số điện thoại không hợp lệ',
            'buyer_phone.max'             => 'Số điện thoại không được vượt quá 30 ký tự',
            'buyer_email.email'           => 'Email người đặt không hợp lệ',
            'full_name.required'          => 'Vui lòng nhập tên người nhận',
            'full_name.string'            => 'Tên người nhận phải là chữ',
            'full_name.max'               => 'Tên người nhận không được vượt quá 255 ký tự',
            'phone.required'              => 'Vui lòng nhập số điện thoại người nhận',
            'phone.string'                => 'Số điện thoại không hợp lệ',
            'phone.max'                   => 'Số điện thoại không được vượt quá 30 ký tự',
            'email.email'                 => 'Email không hợp lệ',
            'address.required'            => 'Vui lòng nhập địa chỉ nhận hàng',
            'address.string'              => 'Địa chỉ phải là chữ',
            'address.max'                 => 'Địa chỉ không được vượt quá 500 ký tự',
            'city.required'               => 'Vui lòng chọn tỉnh/thành phố',
            'city.string'                 => 'Tỉnh/Thành phố không hợp lệ',
            'city.max'                    => 'Tỉnh/Thành phố không được vượt quá 120 ký tự',
            'payment_method.required'     => 'Vui lòng chọn phương thức thanh toán',
            'payment_method.in'           => 'Phương thức thanh toán không hợp lệ',
        ]);

        $owner = $this->getOwnerKeys();
        $items = $this->baseCartQuery()->with(['product', 'variant'])->get();
        if ($items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng trống.');
        }

        // 1. SUBTOTAL
        $subtotal = 0.0;
        foreach ($items as $it) {
            $subtotal += $this->resolveItemPrice($it->product, $it->variant) * (int) $it->quantity;
        }

        // 2. VOUCHER
        $voucherResult     = $this->voucherService->recalculateDiscount($subtotal);
        $voucherDiscount   = $voucherResult['discount'];
        $totalAfterVoucher = $voucherResult['total'];
        $appliedVoucher    = $this->voucherService->getAppliedVoucher();

        // 3. LOYALTY
        $loyaltyDiscount = 0;
        if (Auth::check() && $owner['user_id']) {
            $user            = Auth::user();
            $loyaltyDiscount = $this->loyaltyService->calculateDiscount($user, $totalAfterVoucher);
        }

        $totalDiscount = $voucherDiscount + $loyaltyDiscount;

        // 4. SHIP
        $shippingCarrierId = session('cart.shipping_carrier_id');
        $shippingCarrier   = $shippingCarrierId
            ? ShippingCarrier::where('active', true)->find($shippingCarrierId)
            : null;
        $shippingFee = $shippingCarrier ? (float) ($shippingCarrier->fee ?? 0) : 0.0;

        // 5. TAX
        $taxRate   = TaxRate::orderByDesc('rate')->first();
        $taxAmount = 0.0;
        if ($taxRate && $subtotal > 0) {
            $taxAmount = (float) round($subtotal * (float) $taxRate->rate);
        }

        // 6. GRAND TOTAL
        $grandTotal = max(0, $subtotal - $totalDiscount + $taxAmount + $shippingFee);
        $total      = $grandTotal;

        // Data gửi sang VNPAY & callback dùng lại
        $dataRequest = array_merge($request->all(), [
            'subtotal'            => $subtotal,
            'discount'            => $totalDiscount,
            'shipping_fee'        => $shippingFee,
            'tax_amount'          => $taxAmount,
            'tax_rate_id'         => $taxRate?->id,
            'shipping_carrier_id' => $shippingCarrier?->id,
            'total'               => $total,
        ]);

        // ONLINE → VNPAY
        if ($request->payment_method === 'online') {
            return $this->processVnPayPayment($dataRequest);
        }

        // COD → tạo order luôn
        $order = DB::transaction(function () use (
            $request,
            $owner,
            $items,
            $subtotal,
            $totalDiscount,
            $taxRate,
            $taxAmount,
            $shippingCarrier,
            $shippingFee,
            $grandTotal
        ) {
            $order = Order::create([
                'user_id'             => $owner['user_id'],
                'session_id'          => $owner['session_id'],
                'code'                => 'OD' . strtoupper(substr(sha1(uniqid('', true)), 0, 10)),
                'full_name'           => $request->full_name,
                'phone'               => $request->phone,
                'email'               => $request->email,
                'city'                => $request->city,
                'address'             => $request->address,
                'note'                => $request->note,
                'subtotal'            => (int) $subtotal,
                'shipping_fee'        => (int) $shippingFee,
                'discount'            => (int) $totalDiscount,
                'tax_rate_id'         => $taxRate?->id,
                'tax_amount'          => (int) $taxAmount,
                'shipping_carrier_id' => $shippingCarrier?->id,
                'total'               => (int) $grandTotal,
                'payment_method'      => $request->payment_method,
                'payment_status'      => 'unpaid',
                'status'              => 'pending',
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

            if ($owner['user_id']) {
                $user = \App\Models\User::find($owner['user_id']);
                if ($user) {
                    $this->loyaltyService->updateUserSpending($user, $grandTotal);
                }
            }

            Cart::clearOwner($owner['user_id'], $owner['session_id']);
            $this->voucherService->removeFromSession();

            return $order;
        });

        // Thông báo đơn hàng mới cho admin/staff
        $this->notificationService->notifyNewOrder($order);

        return redirect()->route('client.checkout.thankyou', ['id' => $order->id]);
    }

    public function vnpayReturn(Request $request)
    {
        Log::info("=== VNPAY CALLBACK START ===", [
            'full_url' => $request->fullUrl(),
        ]);

        $vnp_HashSecret = "TRGJ5Z3UY1YOO1QY35RKSU063180BJT4";

        // Lấy toàn bộ tham số vnp_* từ QUERY
        $inputData = [];
        foreach ($request->query() as $key => $value) {
            if (substr($key, 0, 4) === "vnp_") {
                $inputData[$key] = $value;
            }
        }
        Log::info("VNPAY RAW INPUT:", $inputData);

        // Lấy secure hash VNPAY gửi về
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        // Build hashData giống hệt bên gửi đi
        $hashData  = $this->buildVnpHashData($inputData);
        $checkHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        Log::info("Hash Check:", [
            'generated_hash'  => $checkHash,
            'vnp_secure_hash' => $vnp_SecureHash,
            'hash_match'      => hash_equals(strtolower((string)$checkHash), strtolower((string)$vnp_SecureHash)),
            'hashData'        => $hashData,
        ]);

        if (!hash_equals(strtolower((string)$checkHash), strtolower((string)$vnp_SecureHash))) {
            Log::error("❌ Sai chữ ký – Redirect về giỏ hàng");
            return redirect()->route('client.cart.index')
                ->with('error', 'Chữ ký không hợp lệ. Thanh toán không thành công.');
        }

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

        // ==== LẤY DATA TỪ REDIS ====
        $raw = Cache::get("order:$txnRef");
        Log::info("Redis Check:", [
            "key" => "order:$txnRef",
            "raw" => $raw,
        ]);

        if (!$raw) {
            Log::error("❌ Redis không tìm thấy dữ liệu order. Hết hạn hoặc sai key.");
            return redirect()->route('client.cart.index')
                ->with('error', 'Không tìm thấy thông tin đơn hàng tạm. Vui lòng đặt lại.');
        }

        $cache   = json_decode($raw, true);
        $reqData = $cache['data'] ?? [];
        Log::info("Cached Request Data:", $reqData);

        // Lấy lại cart để tạo OrderItem
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

        // Tổng / thuế / ship / discount lấy từ dữ liệu đã lưu
        $subtotal          = (float) ($reqData['subtotal'] ?? 0);
        $discount          = (float) ($reqData['discount'] ?? 0);
        $shippingFee       = (float) ($reqData['shipping_fee'] ?? 0);
        $taxAmount         = (float) ($reqData['tax_amount'] ?? 0);
        $taxRateId         = $reqData['tax_rate_id'] ?? null;
        $shippingCarrierId = $reqData['shipping_carrier_id'] ?? null;
        $total             = (float) ($reqData['total'] ?? 0);

        // So sánh số tiền
        $vnpAmount   = (int) ($inputData['vnp_Amount'] ?? 0);
        $localAmount = (int) round($total * 100);

        Log::info("Total Verification:", [
            'vnp_Amount'  => $vnpAmount,
            'local_x100'  => $localAmount,
            'match'       => $vnpAmount === $localAmount,
        ]);

        if ($vnpAmount !== $localAmount) {
            Log::error("❌ Số tiền không khớp. Không tạo order.");
            return redirect()->route('client.cart.index')
                ->with('error', 'Số tiền thanh toán không khớp.');
        }

        // ==== TẠO ORDER ====
        try {
            $order = DB::transaction(function () use (
                $reqData,
                $owner,
                $items,
                $subtotal,
                $discount,
                $shippingFee,
                $taxAmount,
                $taxRateId,
                $shippingCarrierId,
                $total
            ) {
                Log::info("=== TẠO ORDER BẮT ĐẦU ===");

                $order = Order::create([
                    'user_id'             => $owner['user_id'],
                    'session_id'          => $owner['session_id'],
                    'code'                => 'OD' . strtoupper(substr(sha1(uniqid('', true)), 0, 10)),
                    'full_name'           => $reqData['full_name'] ?? '',
                    'phone'               => $reqData['phone'] ?? '',
                    'email'               => $reqData['email'] ?? '',
                    'city'                => $reqData['city'] ?? '',
                    'address'             => $reqData['address'] ?? '',
                    'note'                => $reqData['note'] ?? null,
                    'subtotal'            => (int) $subtotal,
                    'shipping_fee'        => (int) $shippingFee,
                    'discount'            => (int) $discount,
                    'tax_rate_id'         => $taxRateId,
                    'tax_amount'          => (int) $taxAmount,
                    'shipping_carrier_id' => $shippingCarrierId,
                    'total'               => (int) $total,
                    'payment_method'      => 'online',
                    'payment_status'      => 'paid',
                    'status'              => 'pending',
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

                // Loyalty
                if ($owner['user_id']) {
                    $user = \App\Models\User::find($owner['user_id']);
                    if ($user) {
                        $this->loyaltyService->updateUserSpending($user, $total);
                    }
                }

                // Clear cart + voucher + session ship
                Cart::clearOwner($owner['user_id'], $owner['session_id']);
                $this->voucherService->removeFromSession();
                session()->forget('cart.shipping_carrier_id');

                Log::info("Cart & voucher cleared");

                return $order;
            });

            Log::info("=== ORDER HOÀN THÀNH ===");

            // Thông báo đơn hàng mới cho admin/staff
            $this->notificationService->notifyNewOrder($order);

            // Xóa key redis
            Cache::forget("order:$txnRef");

            return redirect()->route('client.checkout.thankyou', ['id' => $order->id]);
        } catch (\Throwable $e) {
            Log::error("❌ EXCEPTION KHI TẠO ORDER", [
                'msg'  => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return redirect()->route('client.cart.index')
                ->with('error', 'Có lỗi khi tạo đơn hàng.');
        }
    }


    private function processVnPayPayment(array $dataRequest)
    {
        if (empty($dataRequest['total']) || $dataRequest['total'] <= 0) {
            return back()->with('warning', 'Vui lòng kiểm tra lại giá sản phẩm !!!');
        }

        // Cấu hình sandbox
        $vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl  = route('client.checkout.vnpayReturn');
        $vnp_TmnCode    = 'CW3MWMKN'; // đúng mã trong email VNPAY
        $vnp_HashSecret = "TRGJ5Z3UY1YOO1QY35RKSU063180BJT4"; // đúng chuỗi bí mật trong email VNPAY

        $vnp_TxnRef    = (string) Str::uuid();
        $vnp_Amount    = (int) $dataRequest['total'] * 100;
        $vnp_IpAddr    = request()->ip();
        $vnp_OrderInfo = "Thanh toan don hang";
        $vnp_OrderType = "other";
        $vnp_Locale    = 'vn';

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

        // Chuỗi hashData đúng chuẩn
        $hashData      = $this->buildVnpHashData($inputData);
        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Build URL query (có thể dùng http_build_query, không ảnh hưởng hash)
        $queryString = http_build_query($inputData, '', '&');
        $vnp_Url     = $vnp_Url . "?" . $queryString . '&vnp_SecureHash=' . $vnpSecureHash;

        // Lưu dữ liệu tạm để callback dùng
        Cache::put("order:$vnp_TxnRef", json_encode([
            'order_id' => $vnp_TxnRef,
            'data'     => $dataRequest,
        ]), now()->addSeconds(900));

        Log::info('VNPAY URL', [
            'url'        => $vnp_Url,
            'hashData'   => $hashData,
            'secureHash' => $vnpSecureHash,
        ]);

        return redirect($vnp_Url);
    }

    /**
     * Build VNPAY hash data string with consistent encoding.
     */
    private function buildVnpHashData(array $inputData): string
    {
        ksort($inputData);

        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        return $hashData;
    }


    private function vnpCacheKey(string $ref): string
    {
        return "vnpay:{$ref}";
    }

    private function createOrderFromCart(
        array $owner,
        $items,
        Request $request,
        array $pricing,
        ?TaxRate $taxRate,
        ?ShippingCarrier $shippingCarrier,
        string $paymentMethod,
        string $paymentStatus
    ) {
        return DB::transaction(function () use ($owner, $items, $request, $pricing, $taxRate, $shippingCarrier, $paymentMethod, $paymentStatus) {
            $order = Order::create([
                'user_id' => $owner['user_id'],
                'session_id' => $owner['session_id'],
                'code' => 'OD' . strtoupper(substr(sha1(uniqid('', true)), 0, 10)),
                'buyer_name' => $request->buyer_full_name ?? $request->full_name,
                'buyer_phone' => $request->buyer_phone ?? $request->phone,
                'buyer_email' => $request->buyer_email ?? $request->email,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'city' => $request->city,
                'address' => $request->address,
                'note' => $request->note,
                'subtotal' => (int) ($pricing['subtotal'] ?? 0),
                'shipping_fee' => (int) ($pricing['shippingFee'] ?? 0),
                'discount' => (int) ($pricing['totalDiscount'] ?? $pricing['discount'] ?? 0),
                'tax_rate_id' => $taxRate?->id,
                'tax_amount' => (int) ($pricing['taxAmount'] ?? 0),
                'shipping_carrier_id' => $shippingCarrier?->id,
                'total' => (int) ($pricing['grandTotal'] ?? 0),
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
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

            Cart::clearOwner($owner['user_id'], $owner['session_id']);
            $this->voucherService->removeFromSession();

            return $order;
        });
    }

    // TODO: Uncomment when dompdf package is installed and invoice view is created
    // public function invoice(string $code)
    // {
    //     $userId = Auth::id();
    //     $sessionId = session()->getId();

    //     $order = Order::with([
    //             'items.product',
    //             'items.variant.size',
    //             'items.variant.color',
    //             'items.variant.texture',
    //         ])
    //         ->when($userId, function($q) use ($userId){
    //             $q->where('user_id', $userId);
    //         }, function($q) use ($sessionId){
    //             $q->where('session_id', $sessionId);
    //         })
    //         ->where(function($q) use ($code){
    //             $q->where('code', $code)
    //                 ->orWhere('id', $code);
    //         })
    //         ->firstOrFail();

    //     $items = [];
    //     foreach ($order->items as $item) {
    //         $variant = $item->variant;
    //         $variantParts = [];
    //         if ($variant && $variant->size) {
    //             $variantParts[] = $variant->size->name;
    //         }
    //         if ($variant && $variant->color) {
    //             $variantParts[] = $variant->color->name;
    //         }
    //         if ($variant && $variant->texture) {
    //             $variantParts[] = $variant->texture->name;
    //         }

    //         $items[] = [
    //             'product_name' => $item->product?->name ?? ('#' . (string)$item->product_id),
    //             'variant_label' => implode(' / ', array_filter($variantParts)),
    //             'quantity' => (int) $item->quantity,
    //             'unit_price' => (int) $item->price,
    //             'line_total' => (int) $item->line_total,
    //         ];
    //     }

    //     $data = [
    //         'order_code' => $order->code,
    //         'full_name' => $order->full_name,
    //         'phone' => $order->phone,
    //         'email' => $order->email,
    //         'city' => $order->city,
    //         'address' => $order->address,
    //         'note' => $order->note,
    //         'subtotal' => (int) $order->subtotal,
    //         'shipping_fee' => (int) $order->shipping_fee,
    //         'discount' => (int) $order->discount,
    //         'total' => (int) $order->total,
    //         'payment_method' => $order->payment_method,
    //         'payment_status' => $order->payment_status,
    //         'status' => $order->status,
    //         'items' => $items,
    //         'placed_at' => optional($order->created_at)->format('d/m/Y H:i'),
    //     ];

    //     $pdf = Pdf::loadView('admin.mails.invoice_order', ['d' => $data])->setPaper('a4');
    //     return $pdf->download('invoice_' . ($order->code ?? $order->id) . '.pdf');
    // }
}

