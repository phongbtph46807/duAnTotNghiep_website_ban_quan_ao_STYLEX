@extends('client.layouts.app')

@section('title', 'Theo dõi đơn hàng - ' . env('APP_NAME'))

@section('content')
<div class="container p-t-40 p-b-60">
<style>
        .order-track-card{background:#fff;border:1px solid #eee;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.05);padding:24px;max-width:960px;margin:0 auto;}
        .order-track-title{font-weight:800;font-size:22px;margin-bottom:12px;display:flex;align-items:center;gap:10px;}
        .order-track-title:before{content:"";width:6px;height:24px;background:#6777ef;border-radius:6px;}
        .order-track-meta{font-size:14px;color:#555;margin-bottom:16px;}
        .order-badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;}
        .status-pending{background:#fff7e6;color:#d48806;border:1px solid #ffe58f;}
        .status-processing{background:#e6f4ff;color:#1677ff;border:1px solid #91caff;}
        .status-completed{background:#f6ffed;color:#389e0d;border:1px solid #b7eb8f;}
        .status-cancelled{background:#fff1f0;color:#cf1322;border:1px solid #ffa39e;}
        .timeline{margin:20px 0 10px;padding-left:0;list-style:none;position:relative;}
        .timeline:before{content:"";position:absolute;left:12px;top:0;bottom:0;width:2px;background:#f0f0f0;}
        .timeline-item{position:relative;padding-left:32px;padding-bottom:16px;}
        .timeline-item:last-child{padding-bottom:0;}
        .timeline-dot{position:absolute;left:7px;top:3px;width:10px;height:10px;border-radius:50%;background:#d9d9d9;}
        .timeline-item.active .timeline-dot{background:#6777ef;box-shadow:0 0 0 4px rgba(103,119,239,.2);}
        .timeline-title{font-weight:600;font-size:14px;}
        .timeline-time{font-size:12px;color:#888;}
        .order-items{margin-top:20px;border-top:1px solid #eee;padding-top:16px;}
        .order-item-row{display:flex;gap:10px;padding:10px 0;border-bottom:1px dashed #eee;}
        .order-item-row:last-child{border-bottom:none;}
        .order-item-row img{width:56px;height:56px;border-radius:8px;object-fit:cover;}
        .order-item-info{flex:1;min-width:0;}
        .order-item-name{font-weight:600;color:#222;}
        .order-item-attrs{font-size:12px;color:#666;margin-top:2px;white-space:normal;word-break:break-word;}
        .order-item-qty{font-size:13px;color:#333;}
        .order-item-price{text-align:right;font-weight:600;min-width:90px;}
        .order-summary-line{display:flex;justify-content:space-between;margin-top:6px;font-size:14px;}
        .order-summary-total{font-size:16px;font-weight:700;color:#6777ef;margin-top:8px;border-top:1px solid #eee;padding-top:8px;}
        .btn-outline{border:1px solid #d9d9d9;color:#555;background:#fff;border-radius:8px;padding:10px 14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.3s;position:relative;z-index:10;pointer-events:auto;}
        .btn-outline:hover{background:#f5f5f5;}
        .btn-outline:active{transform:scale(0.98);}
        .btn-outline:disabled{opacity:0.6;cursor:not-allowed;pointer-events:none;}
        .btn-primary-x{background:#6777ef;color:#fff;border:none;border-radius:8px;padding:10px 14px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.3s;position:relative;z-index:10;pointer-events:auto;}
        .btn-primary-x:hover{filter:brightness(0.95);}
        .btn-primary-x:active{transform:scale(0.98);}
        form.cancel-order-form{position:relative;z-index:10;pointer-events:auto;display:inline-block;}
        form[action*="cancel"]{position:relative;z-index:1;display:inline-block;}
        @media(max-width:767px){
            .order-item-row{flex-wrap:wrap;}
            .order-item-price{text-align:left;}
        }
</style>

    @if(session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="order-track-card">
        <h1 class="order-track-title">Trạng thái đơn hàng</h1>

        @if(!$order)
            <p class="order-track-meta">Không tìm thấy đơn hàng. Vui lòng kiểm tra lại đường dẫn hoặc mã đơn.</p>
            <form method="GET" action="{{ route('client.order.track') }}" class="m-t-15">
                <div class="row g-2">
                    <div class="col-md-8">
                        <input type="text" name="code" class="co-input" placeholder="Nhập mã đơn / số điện thoại" required>
                    </div>
                    <div class="col-md-4">
                        <button class="btn-primary-x w-100">Tra cứu đơn hàng</button>
                    </div>
                </div>
    </form>
        @else
            <div class="order-track-meta">
                <div><strong>Mã đơn:</strong> {{ $order->code }}</div>
                <div><strong>Ngày đặt:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}</div>
                <div>
                    <strong>Trạng thái:</strong>
        @php
                        $statusClass = [
                            'pending' => 'status-pending',
                            'processing' => 'status-processing',
                            'shipping' => 'status-processing',
                            'completed' => 'status-completed',
                            'delivered' => 'status-completed',
                            'cancelled' => 'status-cancelled',
                            'returned' => 'status-cancelled',
                        ][$order->status] ?? 'status-pending';

                        $statusLabel = [
                            'pending' => 'Chờ xác nhận',
                            'processing' => 'Vận chuyển',
                            'shipping' => 'Chờ giao hàng',
                            'completed' => 'Hoàn thành',
                            'delivered' => 'Đã giao',
                            'cancelled' => 'Đã hủy',
                            'returned' => 'Trả hàng/Hoàn tiền',
                        ][$order->status] ?? 'Chờ xử lý';
        @endphp
                    <span class="order-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
                <div style="margin-top:6px;">
                    <strong>Người nhận:</strong> {{ $order->full_name }} - {{ $order->phone }}<br>
                    <strong>Địa chỉ:</strong> {{ $order->address }}, {{ $order->city }}<br>
                    <strong>Phương thức thanh toán:</strong>
                    @if($order->payment_method === 'cod')
                        COD
                    @else
                        Online
                    @endif
                    &nbsp;|&nbsp;
                    <strong>Trạng thái thanh toán:</strong>
                    @switch($order->payment_status)
                        @case('paid') Đã thanh toán @break
                        @case('refunded') Đã hoàn tiền @break
                        @default Chưa thanh toán
                    @endswitch
      </div>
      </div>

            @php
                $timelineSteps = [
                    ['key' => 'pending', 'label' => 'Đã tiếp nhận đơn hàng'],
                    ['key' => 'processing', 'label' => 'Đang chuẩn bị hàng'],
                    ['key' => 'shipping', 'label' => 'Đơn vị vận chuyển đã nhận'],
                    ['key' => 'completed', 'label' => 'Đã giao thành công'],
                ];
                $statusRank = [
                    'pending' => 1,
                    'processing' => 2,
                    'shipping' => 3,
                    'completed' => 4,
                    'delivered' => 4,
                ][$order->status] ?? 0;
            @endphp

            <ul class="timeline">
                <li class="timeline-item {{ $statusRank >= 1 ? 'active' : '' }}">
                    <span class="timeline-dot"></span>
                    <div class="timeline-title">Đã tiếp nhận đơn hàng</div>
                    <div class="timeline-time">{{ $order->created_at?->format('d/m/Y H:i') }}</div>
                </li>
                <li class="timeline-item {{ $statusRank >= 2 ? 'active' : '' }}">
                    <span class="timeline-dot"></span>
                    <div class="timeline-title">Đang chuẩn bị / đóng gói</div>
                    <div class="timeline-time">Đơn hàng đang được xử lý tại kho.</div>
                </li>
                <li class="timeline-item {{ $statusRank >= 3 ? 'active' : '' }}">
                    <span class="timeline-dot"></span>
                    <div class="timeline-title">Đang giao hàng</div>
                    <div class="timeline-time">Đã bàn giao cho đơn vị vận chuyển.</div>
                </li>
                <li class="timeline-item {{ $statusRank >= 4 ? 'active' : '' }}">
                    <span class="timeline-dot"></span>
                    <div class="timeline-title">Đã giao thành công</div>
                    <div class="timeline-time">Cập nhật khi đơn chuyển sang trạng thái hoàn tất</div>
                </li>
                @if(in_array($order->status, ['cancelled','returned']))
                    <li class="timeline-item active">
                        <span class="timeline-dot"></span>
                        <div class="timeline-title">
                            {{ $order->status === 'returned' ? 'Đơn hàng đã trả/hoàn tiền' : 'Đơn hàng đã hủy' }}
                        </div>
                        <div class="timeline-time">Vui lòng liên hệ chăm sóc khách hàng nếu cần hỗ trợ.</div>
                    </li>
                @endif
            </ul>

            <div class="order-items">
                <h5 style="font-weight:700;margin-bottom:8px;">Sản phẩm trong đơn</h5>
            @foreach($order->items as $item)
                    <div class="order-item-row">
                        <img src="{{ $item->product->default_image_url ?? asset('client/images/product/product-01.jpg') }}" alt="IMG">
                        <div class="order-item-info">
                            <div class="order-item-name">{{ $item->product->name ?? 'Sản phẩm' }}</div>
                            @php
                            $parts = [];
                            if($item->variant && $item->variant->size){ $parts[] = 'Size: '.$item->variant->size->name; }
                            if($item->variant && $item->variant->color){ $parts[] = 'Màu: '.$item->variant->color->name; }
                            $textureNames = $item->product && $item->product->relationLoaded('productVariants')
                                ? $item->product->productVariants
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
                            <div class="order-item-attrs">{{ implode(' - ', $parts) }}</div>
                        @endif
                        </div>
                        <div class="order-item-qty">x {{ $item->quantity }}</div>
                        <div class="order-item-price">{{ number_format($item->line_total, 0, ',', '.') }} ₫</div>
                    </div>
            @endforeach

                {{-- Hiển thị form đánh giá nếu đơn hàng đã hoàn thành --}}
                @if($order && in_array($order->status, ['completed', 'delivered']))
                    <div class="m-t-30" style="border-top:2px solid #eee;padding-top:24px;">
                        <h5 style="font-weight:700;margin-bottom:20px;color:#333;display:flex;align-items:center;gap:8px;">
                            <i class="ri-star-line" style="color:#ffc107;font-size:20px;"></i>
                            Đánh giá sản phẩm
                        </h5>
                        @foreach($order->items as $item)
                            @if(!isset($item->is_reviewed) || !$item->is_reviewed)
                                <div class="review-item-card" data-item-id="{{ $item->id }}" style="background:linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);border:1px solid #e9ecef;border-radius:12px;padding:20px;margin-bottom:16px;box-shadow:0 2px 8px rgba(0,0,0,0.04);transition:all 0.3s ease;">
                                    <div class="d-flex gap-3 align-items-start">
                                        <div style="position:relative;">
                                        <img src="{{ $item->product->default_image_url ?? asset('client/images/product/product-01.jpg') }}" 
                                             alt="{{ $item->product->name }}" 
                                                 style="width:80px;height:80px;border-radius:10px;object-fit:cover;border:2px solid #e9ecef;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                                        </div>
                                        <div style="flex:1;min-width:0;">
                                            <div style="font-weight:700;margin-bottom:6px;color:#212529;font-size:15px;">{{ $item->product->name }}</div>
                                            @if($item->variant)
                                                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                                                    @if($item->variant->size)
                                                        <span style="background:#e7f3ff;color:#0066cc;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:600;">
                                                            <i class="ri-ruler-line" style="font-size:10px;"></i> Size: {{ $item->variant->size->name }}
                                                        </span>
                                                    @endif
                                                    @if($item->variant->color)
                                                        <span style="background:#fff4e6;color:#d97706;padding:3px 8px;border-radius:4px;font-size:11px;font-weight:600;">
                                                            <i class="ri-palette-line" style="font-size:10px;"></i> Màu: {{ $item->variant->color->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            <div style="font-size:12px;color:#6c757d;margin-bottom:12px;">
                                                <i class="ri-shopping-cart-line" style="font-size:11px;"></i> Số lượng: {{ $item->quantity }}
                                            </div>
                                            
                                            <div class="review-form" style="margin-top:16px;padding-top:16px;border-top:1px dashed #dee2e6;">
                                                <div style="margin-bottom:12px;">
                                                    <label style="font-size:13px;font-weight:600;margin-bottom:8px;display:block;color:#495057;">
                                                        <i class="ri-star-fill" style="color:#ffc107;font-size:12px;"></i> Đánh giá của bạn:
                                                    </label>
                                                    <div class="star-rating" data-item-id="{{ $item->id }}" style="display:flex;gap:4px;align-items:center;">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <span class="star" data-rating="{{ $i }}" style="font-size:28px;color:#ddd;cursor:pointer;transition:all 0.2s ease;user-select:none;">★</span>
                                                        @endfor
                                                        <span class="rating-text" style="margin-left:8px;font-size:12px;color:#6c757d;font-weight:500;"></span>
                                                    </div>
                                                </div>
                                                <div style="margin-bottom:12px;">
                                                    <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;color:#495057;">
                                                        <i class="ri-message-3-line" style="font-size:12px;"></i> Nhận xét (tùy chọn):
                                                    </label>
                                                <textarea class="review-content" 
                                                          data-item-id="{{ $item->id }}" 
                                                          placeholder="Chia sẻ cảm nhận của bạn về sản phẩm này..."
                                                              style="width:100%;min-height:100px;padding:12px;border:1px solid #ced4da;border-radius:8px;font-size:13px;resize:vertical;font-family:inherit;transition:all 0.3s ease;background:#fff;"></textarea>
                                                </div>
                                                <button type="button" 
                                                        class="submit-review-btn" 
                                                        data-order-id="{{ $order->id }}" 
                                                        data-item-id="{{ $item->id }}"
                                                        data-product-id="{{ $item->product_id }}"
                                                        data-variant-id="{{ $item->variant_id }}"
                                                        style="margin-top:4px;padding:10px 20px;background:linear-gradient(135deg, #6777ef 0%, #764ba2 100%);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:13px;box-shadow:0 4px 12px rgba(103,119,239,0.3);transition:all 0.3s ease;display:inline-flex;align-items:center;gap:6px;">
                                                    <i class="ri-send-plane-fill" style="font-size:14px;"></i>
                                                    Gửi đánh giá
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div style="background:linear-gradient(135deg, #e7f5ff 0%, #d0ebff 100%);border:1px solid #74c0fc;border-radius:10px;padding:16px;margin-bottom:16px;box-shadow:0 2px 4px rgba(116,192,252,0.1);">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:40px;height:40px;background:#1677ff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="ri-check-line" style="color:#fff;font-size:20px;font-weight:bold;"></i>
                                        </div>
                                        <div style="flex:1;">
                                            <div style="font-weight:600;color:#1677ff;margin-bottom:4px;font-size:14px;">
                                                Đã đánh giá biến thể này
                                            </div>
                                            <div style="font-weight:600;color:#212529;margin-bottom:4px;">{{ $item->product->name }}</div>
                                            @if($item->variant)
                                                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px;">
                                                    @if($item->variant->size)
                                                        <span style="background:#fff;color:#0066cc;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600;border:1px solid #74c0fc;">
                                                            Size: {{ $item->variant->size->name }}
                                                        </span>
                                                    @endif
                                                    @if($item->variant->color)
                                                        <span style="background:#fff;color:#d97706;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600;border:1px solid #ffd43b;">
                                                            Màu: {{ $item->variant->color->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="order-summary">
                    <div class="order-summary-line">
                        <span>Tạm tính</span>
                        <span>{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="order-summary-line" style="color:#28a745;">
                            <span>Giảm giá</span>
                            <span>-{{ number_format($order->discount, 0, ',', '.') }} ₫</span>
                        </div>
                    @endif
                    @if($order->tax_amount > 0)
                        <div class="order-summary-line">
                            <span>Thuế</span>
                            <span>{{ number_format($order->tax_amount, 0, ',', '.') }} ₫</span>
                        </div>
                    @endif
                    @if($order->shipping_fee > 0)
                        <div class="order-summary-line">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                        </div>
                    @endif
                    <div class="order-summary-total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($order->total, 0, ',', '.') }} ₫</span>
                    </div>
                </div>
            </div>

            <div class="m-t-20 d-flex justify-content-between flex-wrap" style="gap:10px;position:relative;z-index:1;">
                <a href="{{ route('client.order.list') }}" class="co-hint" style="text-decoration:none;position:relative;z-index:1;">← Xem lịch sử đơn hàng</a>
                <div class="d-flex gap-2 flex-wrap" style="align-items:center;position:relative;z-index:1;">
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('client.order.cancel', $order) }}" class="cancel-order-form" style="display:inline-block;margin:0;position:relative;z-index:1;">
                            @csrf
                            <button type="submit" class="btn-outline cancel-order-btn" style="border:1px solid #ff4d4f;color:#ff4d4f;background:#fff;border-radius:8px;padding:10px 14px;font-weight:600;cursor:pointer;transition:all 0.3s;">
                                Hủy đơn hàng
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('home') }}" class="btn-primary-x continue-shopping-btn" style="margin-left: 10px; white-space:nowrap;display:inline-block;text-decoration:none;padding:10px 14px;border-radius:8px;background:#6777ef;color:#fff;font-weight:600;transition:all 0.3s;border:none;cursor:pointer;">
                        Tiếp tục mua sắm
                    </a>
                </div>
      </div>
    @endif
  </div>
</div>

@if($order && in_array($order->status, ['completed', 'delivered']))
<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
.star {
    transition: all 0.2s ease;
    display: inline-block;
}
.star:hover {
    transform: scale(1.2);
}
.star-rating:hover .star {
    opacity: 0.7;
}
.star-rating:hover .star:hover {
    opacity: 1;
    transform: scale(1.3);
}
.submit-review-btn {
    transition: all 0.3s ease;
}
.submit-review-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(103,119,239,0.4) !important;
}
.submit-review-btn:active:not(:disabled) {
    transform: translateY(0);
}
.review-item-card {
    transition: all 0.3s ease;
}
.review-item-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
    transform: translateY(-2px);
}
.review-content:focus {
    border-color: #6777ef !important;
    box-shadow: 0 0 0 3px rgba(103,119,239,0.1) !important;
    outline: none;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý đánh giá sao với hiệu ứng đẹp hơn
    document.querySelectorAll('.star-rating').forEach(function(ratingEl) {
        const stars = ratingEl.querySelectorAll('.star');
        const ratingText = ratingEl.querySelector('.rating-text');
        let selectedRating = 0;
        
        const ratingLabels = {
            1: 'Rất không hài lòng',
            2: 'Không hài lòng',
            3: 'Bình thường',
            4: 'Hài lòng',
            5: 'Rất hài lòng'
        };
        
        stars.forEach(function(star, index) {
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                highlightStars(stars, rating);
                if (ratingText) {
                    ratingText.textContent = ratingLabels[rating] || '';
                    ratingText.style.color = '#495057';
                }
            });
            
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                highlightStars(stars, selectedRating);
                ratingEl.dataset.selectedRating = selectedRating;
                if (ratingText) {
                    ratingText.textContent = ratingLabels[selectedRating] || '';
                    ratingText.style.color = '#1677ff';
                    ratingText.style.fontWeight = '600';
                }
            });
        });
        
        ratingEl.addEventListener('mouseleave', function() {
            highlightStars(stars, selectedRating);
            if (ratingText && selectedRating > 0) {
                ratingText.textContent = ratingLabels[selectedRating] || '';
                ratingText.style.color = '#1677ff';
            } else if (ratingText) {
                ratingText.textContent = '';
            }
        });
    });
    
    function highlightStars(stars, rating) {
        stars.forEach(function(star, index) {
            if (index < rating) {
                star.style.color = '#ffc107';
                star.style.textShadow = '0 0 8px rgba(255,193,7,0.5)';
            } else {
                star.style.color = '#ddd';
                star.style.textShadow = 'none';
            }
        });
    }
    
    // Xử lý submit review - Đảm bảo dùng đúng JavaScript
    document.querySelectorAll('.submit-review-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Lấy data từ button attributes
            const orderId = this.getAttribute('data-order-id');
            const itemId = this.getAttribute('data-item-id');
            const productId = this.getAttribute('data-product-id');
            const variantId = this.getAttribute('data-variant-id');
            
            // Tìm rating và content elements
            const ratingEl = document.querySelector(`.star-rating[data-item-id="${itemId}"]`);
            const contentEl = document.querySelector(`.review-content[data-item-id="${itemId}"]`);
            
            // Lấy rating đã chọn
            const rating = ratingEl ? parseInt(ratingEl.dataset.selectedRating || 0) : 0;
            const content = contentEl ? contentEl.value.trim() : '';
            
            // Validate rating
            if (!rating || rating === 0) {
                alert('Vui lòng chọn số sao đánh giá!');
                return false;
            }
            
            // Validate orderId và itemId
            if (!orderId || !itemId) {
                alert('Thông tin đơn hàng không hợp lệ!');
                return false;
            }
            
            // Disable button và thêm loading state
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite;"></i> Đang gửi...';
            btn.style.opacity = '0.7';
            btn.style.cursor = 'not-allowed';
            
            // Gửi request - sử dụng route helper hoặc URL đầy đủ
            const apiUrl = "{{ url('/api/reviews') }}";
            
            // Chuẩn bị data - đảm bảo gửi đầy đủ thông tin
            const requestData = {
                order_id: parseInt(orderId),
                order_item_id: parseInt(itemId),
                product_id: productId ? parseInt(productId) : null,
                rating: parseInt(rating),
                content: content || ''
            };
            
            // Thêm variant_id nếu có (quan trọng để đánh giá theo variant)
            if (variantId && variantId !== 'null' && variantId !== '' && variantId !== 'undefined') {
                requestData.variant_id = parseInt(variantId);
            }
            
            console.log('Sending review data:', requestData);
            
            // Lấy CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value || '';
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Lỗi bảo mật. Vui lòng tải lại trang và thử lại.');
                btn.disabled = false;
                btn.innerHTML = originalText;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                return false;
            }
            
            // Gửi request bằng Fetch API
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(requestData)
            })
            .then(async response => {
                // Kiểm tra response status
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server trả về dữ liệu không hợp lệ');
                }
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || `Lỗi ${response.status}: ${response.statusText}`);
                }
                
                return data;
            })
            .then(data => {
                console.log('Review submitted successfully:', data);
                
                if (data.success) {
                    // Hiển thị thông báo thành công đẹp hơn
                    const successMsg = document.createElement('div');
                    successMsg.style.cssText = 'position:fixed;top:20px;right:20px;background:linear-gradient(135deg, #10b981 0%, #059669 100%);color:#fff;padding:16px 24px;border-radius:12px;box-shadow:0 8px 24px rgba(16,185,129,0.3);z-index:99999;font-weight:600;max-width:350px;animation:slideInRight 0.3s ease;display:flex;align-items:center;gap:10px;';
                    successMsg.innerHTML = `
                        <i class="ri-checkbox-circle-fill" style="font-size:24px;"></i>
                        <div>
                            <div style="font-size:16px;margin-bottom:2px;">Thành công!</div>
                            <div style="font-size:13px;opacity:0.95;">${data.message || 'Cảm ơn bạn đã đánh giá!'}</div>
                        </div>
                    `;
                    document.body.appendChild(successMsg);
                    
                    // Ẩn form đánh giá đã gửi
                    const reviewCard = btn.closest('.review-item-card');
                    if (reviewCard) {
                        reviewCard.style.transition = 'opacity 0.5s ease';
                        reviewCard.style.opacity = '0';
                        setTimeout(() => {
                            reviewCard.remove();
                        }, 500);
                    }
                    
                    // Tự động reload sau 2 giây
                    setTimeout(() => {
                        successMsg.style.opacity = '0';
                        successMsg.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => {
                            successMsg.remove();
                    location.reload();
                        }, 300);
                    }, 2000);
                } else {
                    // Hiển thị lỗi
                    alert(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                }
            })
            .catch(error => {
                console.error('Error submitting review:', error);
                alert(error.message || 'Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.');
                btn.disabled = false;
                btn.innerHTML = originalText;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            });
            
            return false;
        });
    });
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form hủy đơn hàng
    const cancelForm = document.querySelector('.cancel-order-form');
    if (cancelForm) {
        cancelForm.addEventListener('submit', function(e) {
            const confirmed = confirm('Bạn chắc chắn muốn hủy đơn hàng này?');
            if (!confirmed) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            // Disable button để tránh double submit
            const submitBtn = this.querySelector('.cancel-order-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Đang xử lý...';
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';
            }
        });
    }

    // Đảm bảo link "Tiếp tục mua sắm" hoạt động
    const continueShoppingLink = document.querySelector('.continue-shopping-btn');
    if (continueShoppingLink) {
        continueShoppingLink.addEventListener('click', function(e) {
            // Đảm bảo link hoạt động bình thường
            // Không preventDefault để cho phép navigation
        });
    }
});
</script>

@if($order)
@push('scripts')
{{-- Load Laravel Echo và Pusher --}}
@vite(['resources/js/app.js'])
@php
    $currentUserId = Auth::check() ? Auth::id() : null;
@endphp
<script>
// ========== REALTIME ORDER STATUS UPDATES ==========
// Lắng nghe event cập nhật trạng thái đơn hàng từ admin
(function(){
    const orderId = {{ $order->id }};
    const orderUserId = {{ $order->user_id ?? 'null' }};
    const currentUserId = {{ $currentUserId ?? 'null' }};
    
    // Chỉ chạy khi window.Echo đã được load (từ bootstrap.js)
    // Đợi Echo load xong
    function initRealtime() {
        if (typeof window.Echo === 'undefined') {
            console.warn('⚠️ Laravel Echo not loaded yet, retrying...');
            setTimeout(initRealtime, 500);
            return;
        }
        
        console.log('✅ Laravel Echo loaded, initializing realtime...');
        
        // Nếu order có user_id, lắng nghe trên private channel
        if (orderUserId && currentUserId && currentUserId === orderUserId) {
            // User đã đăng nhập và là chủ đơn hàng
            window.Echo.private(`user.${orderUserId}.orders`)
                .listen('.order.status.updated', (e) => {
                    handleOrderUpdate(e, orderId);
                });
            console.log('✅ Realtime order tracking enabled for order #' + orderId + ' (private channel)');
        } else if (!orderUserId) {
            // Nếu order không có user_id, lắng nghe trên public channel orders
            // (chỉ khi admin broadcast, sẽ có thông tin order trong event)
            window.Echo.channel('orders')
                .listen('.order.status.updated', (e) => {
                    // Kiểm tra xem có phải đơn hàng hiện tại không (theo code hoặc id)
                    const orderCode = "{{ $order->code }}";
                    if (e.code === orderCode || e.id === orderId) {
                        handleOrderUpdate(e, orderId);
                    }
                });
            console.log('✅ Realtime order tracking enabled for order #' + orderId + ' (public channel)');
        } else {
            console.log('⚠️ Current user is not the order owner, realtime disabled');
        }
    }
    
    // Hàm escape HTML để tránh XSS
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Hàm xử lý cập nhật order
    function handleOrderUpdate(orderData, orderId) {
        console.log('Order status updated (track page):', orderData);
        
        // Chỉ xử lý nếu là đơn hàng hiện tại
        if (orderData.id !== orderId) {
            return;
        }
                
        const newStatus = orderData.status;
        
        // Cập nhật status badge
        const statusBadge = document.querySelector('.order-badge');
        if (statusBadge) {
            const statusClassMap = {
                'pending': 'status-pending',
                'processing': 'status-processing',
                'shipping': 'status-processing',
                'completed': 'status-completed',
                'delivered': 'status-completed',
                'cancelled': 'status-cancelled',
                'returned': 'status-cancelled',
                'cancel_request': 'status-pending',
                'return_request': 'status-pending',
            };
            
            const statusLabelMap = {
                'pending': 'Chờ xác nhận',
                'processing': 'Vận chuyển',
                'shipping': 'Chờ giao hàng',
                'completed': 'Hoàn thành',
                'delivered': 'Đã giao',
                'cancelled': 'Đã hủy',
                'returned': 'Trả hàng/Hoàn tiền',
                'cancel_request': 'Yêu cầu hủy',
                'return_request': 'Yêu cầu trả hàng',
            };
            
            // Xóa class cũ và thêm class mới
            statusBadge.className = 'order-badge ' + (statusClassMap[newStatus] || 'status-pending');
            statusBadge.textContent = statusLabelMap[newStatus] || 'Chờ xử lý';
        }
        
        // Cập nhật timeline
        const statusRankMap = {
            'pending': 1,
            'processing': 2,
            'shipping': 3,
            'completed': 4,
            'delivered': 4,
        };
        
        const newRank = statusRankMap[newStatus] || 0;
        const timelineItems = document.querySelectorAll('.timeline-item');
        timelineItems.forEach((item, index) => {
            if (index < newRank) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
        
        // Thêm timeline item cho cancelled/returned nếu cần
        if (['cancelled', 'returned'].includes(newStatus)) {
            const timeline = document.querySelector('.timeline');
            const lastItem = timeline?.querySelector('.timeline-item:last-child');
            const lastTitle = lastItem?.querySelector('.timeline-title')?.textContent || '';
            if (timeline && !lastTitle.includes('hủy') && !lastTitle.includes('trả')) {
                const newItem = document.createElement('li');
                newItem.className = 'timeline-item active';
                
                // Tạo dot
                const dot = document.createElement('span');
                dot.className = 'timeline-dot';
                newItem.appendChild(dot);
                
                // Tạo title
                const title = document.createElement('div');
                title.className = 'timeline-title';
                title.textContent = newStatus === 'returned' ? 'Đơn hàng đã trả/hoàn tiền' : 'Đơn hàng đã hủy';
                newItem.appendChild(title);
                
                // Tạo time
                const time = document.createElement('div');
                time.className = 'timeline-time';
                time.textContent = 'Vui lòng liên hệ chăm sóc khách hàng nếu cần hỗ trợ.';
                newItem.appendChild(time);
                
                timeline.appendChild(newItem);
            }
        }
        
        // Cập nhật buttons dựa trên status mới
        const actionsContainer = document.querySelector('.d-flex.gap-2.flex-wrap');
        if (actionsContainer) {
            const cancelForm = actionsContainer.querySelector('.cancel-order-form');
            
            // Nếu status không còn là pending, ẩn form hủy đơn
            if (newStatus !== 'pending' && cancelForm) {
                cancelForm.style.display = 'none';
            } else if (newStatus === 'pending' && cancelForm) {
                cancelForm.style.display = 'inline-block';
            }
        }
        
        // Hiển thị thông báo
        const statusLabelMap = {
            'pending': 'Chờ xác nhận',
            'processing': 'Đang xử lý',
            'shipping': 'Chờ giao hàng',
            'delivered': 'Đã giao',
            'completed': 'Hoàn thành',
            'cancel_request': 'Yêu cầu hủy',
            'return_request': 'Yêu cầu trả hàng',
            'cancelled': 'Đã hủy',
            'returned': 'Trả hàng/Hoàn tiền',
        };
        
        // Hiển thị notification
        const notification = document.createElement('div');
        notification.style.cssText = 'position:fixed;top:20px;right:20px;background:#10b981;color:#fff;padding:16px 24px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.15);z-index:99999;font-weight:600;max-width:350px;animation:slideInRight 0.3s ease;';
        
        // Tạo title
        const titleDiv = document.createElement('div');
        titleDiv.style.cssText = 'font-size:16px;margin-bottom:4px;';
        titleDiv.textContent = '📦 Cập nhật đơn hàng';
        notification.appendChild(titleDiv);
        
        // Tạo content
        const contentDiv = document.createElement('div');
        contentDiv.style.cssText = 'font-size:13px;opacity:0.95;margin-top:4px;';
        const orderCode = orderData.code || '';
        const statusLabel = statusLabelMap[newStatus] || newStatus;
        contentDiv.innerHTML = 'Đơn hàng #' + escapeHtml(orderCode) + ' đã chuyển sang: <strong>' + escapeHtml(statusLabel) + '</strong>';
        notification.appendChild(contentDiv);
        
        document.body.appendChild(notification);
        
        // Thêm animation CSS nếu chưa có
        if (!document.querySelector('#realtime-notification-style')) {
            const style = document.createElement('style');
            style.id = 'realtime-notification-style';
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }
    
    // Khởi tạo realtime khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRealtime);
    } else {
        initRealtime();
    }
})();
</script>
@endpush
@endif

@endsection