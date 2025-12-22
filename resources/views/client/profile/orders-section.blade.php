<style>
    .order-tabs{
        display:flex;
        gap:16px;
        overflow-x:auto;
        margin-bottom:24px;
        border-bottom:2px solid #e0e0e0;
        padding-bottom:8px;
    }
    .order-tab{
        position:relative;
        padding:8px 12px;
        font-weight:600;
        color:#666;
        text-decoration:none;
        white-space:nowrap;
        font-size:14px;
        transition:all 0.3s;
    }
    .order-tab:hover{
        color:#6777ef;
    }
    .order-tab.active{
        color:#6777ef;
    }
    .order-tab.active:after{
        content:"";
        position:absolute;
        left:0;
        right:0;
        bottom:-10px;
        height:3px;
        background:#6777ef;
        border-radius:3px 3px 0 0;
    }
    .order-card{
        background:#fff;
        border:1px solid #e0e0e0;
        border-radius:12px;
        padding:24px;
        margin-bottom:20px;
        box-shadow:0 2px 12px rgba(0,0,0,0.08);
        transition:box-shadow 0.3s;
    }
    .order-card:hover{
        box-shadow:0 4px 16px rgba(0,0,0,0.12);
    }
    .order-card__header{
        display:flex;
        flex-wrap:wrap;
        gap:12px;
        justify-content:space-between;
        border-bottom:1px dashed #e0e0e0;
        padding-bottom:16px;
        margin-bottom:16px;
    }
    .order-card__status{
        font-weight:700;
        font-size:16px;
        color:#6777ef;
        margin-bottom:8px;
    }
    .order-card__meta{
        font-size:14px;
        color:#666;
        line-height:1.6;
    }
    .order-item{
        display:flex;
        gap:16px;
        padding:16px 0;
        border-bottom:1px dashed #f0f0f0;
    }
    .order-item:last-child{
        border-bottom:none;
    }
    .order-item img{
        width:80px;
        height:80px;
        border-radius:8px;
        object-fit:cover;
        border:1px solid #eee;
    }
    .order-item__info{
        flex:1;
        min-width:0;
    }
    .order-item__name{
        font-weight:600;
        font-size:15px;
        color:#333;
        margin-bottom:6px;
        line-height:1.4;
    }
    .order-item__attrs{
        font-size:13px;
        color:#666;
        margin-top:4px;
        line-height:1.5;
    }
    .order-item__price{
        text-align:right;
        font-weight:600;
        font-size:15px;
        color:#333;
        white-space:nowrap;
    }
    .order-card__footer{
        display:flex;
        justify-content:space-between;
        align-items:center;
        flex-wrap:wrap;
        gap:12px;
        margin-top:16px;
        padding-top:16px;
        border-top:1px dashed #e0e0e0;
    }
    .order-total{
        font-size:18px;
        font-weight:700;
        color:#333;
    }
    .order-actions{
        display:flex;
        gap:10px;
        align-items:center;
        flex-wrap:wrap;
    }
    .order-actions form{
        margin:0;
    }
    .btn-outline,
    .btn-primary-x{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:10px 18px;
        border-radius:8px;
        font-weight:600;
        font-size:14px;
        text-decoration:none;
        transition:all 0.3s;
    }
    .btn-outline{
        border:1px solid #d9d9d9;
        color:#555;
        background:#fff;
    }
    .btn-outline:hover{
        border-color:#6777ef;
        color:#6777ef;
        background:#f8f9ff;
    }
    .btn-primary-x{
        background:#6777ef;
        color:#fff;
        border:none;
    }
    .btn-primary-x:hover{
        background:#5568d3;
        transform:translateY(-1px);
        box-shadow:0 4px 8px rgba(103,119,239,0.3);
    }
    @media(max-width:575px){
        .order-tabs{
            gap:12px;
        }
        .order-tab{
            font-size:13px;
            padding:6px 10px;
        }
        .order-card{
            padding:16px;
        }
        .order-card__header{
            flex-direction:column;
            align-items:flex-start;
        }
        .order-item{
            flex-wrap:wrap;
            gap:12px;
        }
        .order-item img{
            width:70px;
            height:70px;
        }
        .order-item__price{
            text-align:left;
            width:100%;
        }
        .order-card__footer{
            flex-direction:column;
            align-items:flex-start;
        }
        .order-actions{
            width:100%;
        }
        .btn-outline,
        .btn-primary-x{
            flex:1;
            justify-content:center;
        }
    }
</style>

<div class="card profile-card">
    <div class="profile-card-header">
        <h4 class="mb-0" style="font-weight: 600; color: #333;">
            <i class="ri-shopping-bag-line me-2" style="color: #6777ef;"></i>Đơn hàng của tôi
        </h4>
    </div>
    <div class="profile-card-body">
        @php
            $tabLinks = [
                null => 'Tất cả',
            ] + ($statusTabs ?? []);
            $statusLabels = [
                'pending' => 'Chờ xác nhận',
                'processing' => 'Đang xử lý',
                'shipping' => 'Chờ giao hàng',
                'delivered' => 'Đã giao',
                'completed' => 'Hoàn thành',
                'cancel_request' => 'Yêu cầu hủy',
                'return_request' => 'Yêu cầu trả hàng',
                'cancelled' => 'Đã hủy',
                'returned' => 'Trả hàng/Hoàn tiền',
            ];
        @endphp

        @if(session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        <div class="order-tabs">
            @foreach($tabLinks as $key => $label)
                <a class="order-tab {{ ($key === null && !$activeStatus) || ($activeStatus === $key) ? 'active' : '' }}"
                   href="{{ route('client.profile.index', ['tab' => 'orders', 'status' => $key]) }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if($orders->isEmpty())
            <div class="order-card">
                <p class="m-b-0">Chưa có đơn hàng nào cho trạng thái này.</p>
            </div>
        @endif

        @foreach($orders as $order)
            <div class="order-card" data-order-id="{{ $order->id }}" data-order-status="{{ $order->status }}">
                <div class="order-card__header">
                    <div>
                        <div class="order-card__status">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</div>
                        <div class="order-card__meta">Mã đơn: {{ $order->code }}</div>
                        <div class="order-card__meta">
                            Thanh toán:
                            @if($order->payment_method === 'cod')
                                COD
                            @else
                                Online
                            @endif
                            &nbsp;|&nbsp;
                            Trạng thái:
                            @switch($order->payment_status)
                                @case('paid') Đã thanh toán @break
                                @case('refunded') Đã hoàn tiền @break
                                @default Chưa thanh toán
                            @endswitch
                        </div>
                    </div>
                    <div class="order-card__meta text-right">
                        Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }}
                    </div>
                </div>

                @foreach($order->items as $item)
                    <div class="order-item">
                        @php
                            $product = $item->product;
                            if (!$product && $item->product_id) {
                                $product = \App\Models\Product::withTrashed()->find($item->product_id);
                            }
                            $productName = $product ? $product->name : ('Sản phẩm #' . $item->product_id);
                            $productImage = $product ? ($product->default_image_url ?? asset('client/images/product/product-01.jpg')) : asset('client/images/product/product-01.jpg');
                        @endphp
                        <img src="{{ $productImage }}" alt="{{ $productName }}">
                        <div class="order-item__info">
                            <div class="order-item__name">{{ $productName }}</div>
                            @php
                                $parts = [];
                                if($item->variant && $item->variant->size){ $parts[] = 'Size: '.$item->variant->size->name; }
                                if($item->variant && $item->variant->color){ $parts[] = 'Màu: '.$item->variant->color->name; }
                                $textureNames = $product && $product->relationLoaded('productVariants')
                                    ? $product->productVariants
                                        ->map(fn($variant) => optional($variant->texture)->name)
                                        ->filter()
                                        ->unique()
                                        ->values()
                                        ->toArray()
                                    : [];
                                if(!empty($textureNames)){
                                    $parts[] = 'Chất liệu: '.implode(', ', $textureNames);
                                } elseif($item->variant && $item->variant->texture){
                                    $parts[] = 'Chất liệu: '.$item->variant->texture->name;
                                }
                            @endphp
                            @if(!empty($parts))
                                <div class="order-item__attrs">{{ implode(' - ', $parts) }}</div>
                            @endif
                            @if(in_array($order->status, ['completed', 'delivered']))
                                <div class="mt-2">
                                    @if(isset($item->is_reviewed) && $item->is_reviewed)
                                        <span class="text-success" style="font-size:12px;">
                                            ✓ Đã đánh giá
                                        </span>
                                    @else
                                        <button type="button" 
                                                class="btn-review-item" 
                                                data-order-id="{{ $order->id }}"
                                                data-item-id="{{ $item->id }}"
                                                data-product-id="{{ $item->product_id }}"
                                                data-variant-id="{{ $item->variant_id }}"
                                                data-product-name="{{ $productName }}"
                                                style="background:#6777ef;color:#fff;border:none;padding:4px 12px;border-radius:6px;font-size:12px;cursor:pointer;">
                                            ★ Đánh giá
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="order-item__price">
                            x {{ $item->quantity }}<br>
                            {{ number_format($item->line_total, 0, ',', '.') }} ₫
                        </div>
                    </div>
                @endforeach

                <div class="order-card__footer">
                    <div class="order-total">Tổng: {{ number_format($order->total, 0, ',', '.') }} ₫</div>
                    <div class="order-actions">
                        <a class="btn-outline" href="{{ route('client.order.track', ['code' => $order->code]) }}">Xem chi tiết</a>
                        @if($order->status === 'pending')
                            <button type="button"
                                    class="btn-outline btn-cancel-order"
                                    data-order-id="{{ $order->id }}"
                                    data-order-code="{{ $order->code }}"
                                    style="border-color:#ff4d4f;color:#ff4d4f;">
                                Hủy đơn
                            </button>
                        @endif
                        @if(in_array($order->status, ['completed','delivered']))
                            <button type="button"
                                    class="btn-outline btn-return-order"
                                    data-order-id="{{ $order->id }}"
                                    data-order-code="{{ $order->code }}"
                                    style="border-color:#f59e0b;color:#f59e0b;">
                                Yêu cầu trả hàng
                            </button>
                            <a class="btn-primary-x" href="{{ route('client.products.index') }}">Mua lại</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Modals và scripts sẽ được include từ profile/index.blade.php --}}

