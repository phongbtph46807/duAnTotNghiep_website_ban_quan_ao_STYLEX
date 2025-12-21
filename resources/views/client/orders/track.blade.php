@extends('client.layouts.app')

@section('title', 'Theo dõi đơn hàng - ' . env('APP_NAME'))

@section('content')
<div style="background:#f8f8f8;min-height:100vh;padding:20px 0;">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
.track-wrapper{max-width:900px;margin:0 auto;padding:0 15px;}
.track-header{background:linear-gradient(135deg,#717fe0 0%,#6c7ae0 100%);color:#fff;padding:30px;border-radius:12px;margin-bottom:25px;box-shadow:0 4px 16px rgba(113,127,224,0.2);}
.track-header h1{font-size:26px;font-weight:700;margin-bottom:20px;}
.header-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:15px;}
.header-item{background:rgba(255,255,255,0.15);padding:12px;border-radius:8px;}
.header-label{font-size:11px;opacity:0.85;text-transform:uppercase;letter-spacing:0.5px;}
.header-value{font-size:15px;font-weight:600;margin-top:6px;word-break:break-word;}
.status-badge{display:inline-block;padding:6px 14px;border-radius:20px;font-weight:600;font-size:12px;margin-top:12px;}
.status-pending{background:#fff3cd;color:#856404;}
.status-processing{background:#cfe2ff;color:#084298;}
.status-shipped{background:#cfe2ff;color:#084298;}
.status-delivered{background:#d1e7dd;color:#0f5132;}
.status-completed{background:#d1e7dd;color:#0f5132;}
.status-cancelled{background:#f8d7da;color:#842029;}
.card{background:#fff;border-radius:10px;padding:25px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.05);border:1px solid #e9ecef;}
.card-title{font-size:16px;font-weight:700;margin-bottom:18px;display:flex;align-items:center;gap:10px;color:#333;}
.card-title i{font-size:22px;color:#717fe0;}
.timeline{position:relative;padding:15px 0;}
.timeline-item{display:flex;gap:20px;margin-bottom:25px;position:relative;}
.timeline-item:not(:last-child)::before{content:'';position:absolute;left:19px;top:50px;width:2px;height:calc(100% + 15px);background:#e0e0e0;}
.timeline-item.completed::before{background:#717fe0;}
.timeline-dot{width:40px;height:40px;border-radius:50%;background:#e0e0e0;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-weight:700;color:#999;flex-shrink:0;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.timeline-item.completed .timeline-dot{background:#717fe0;color:#fff;}
.timeline-item.active .timeline-dot{background:#717fe0;color:#fff;box-shadow:0 0 0 6px rgba(113,127,224,0.2);}
.timeline-content{flex:1;padding-top:2px;}
.timeline-title{font-weight:600;font-size:14px;color:#333;margin-bottom:4px;}
.timeline-desc{font-size:12px;color:#666;line-height:1.4;}
.timeline-time{font-size:11px;color:#999;margin-top:4px;}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:15px;}
.info-item{padding:15px;background:#f8f9fa;border-radius:8px;border-left:3px solid #717fe0;}
.info-label{font-size:12px;color:#666;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;}
.info-value{font-size:14px;font-weight:600;color:#333;word-break:break-word;}
.products-list{display:grid;gap:12px;}
.product-item{display:flex;gap:12px;padding:12px;background:#f8f9fa;border-radius:8px;border:1px solid #e9ecef;}
.product-img{width:70px;height:70px;border-radius:6px;object-fit:cover;flex-shrink:0;}
.product-info{flex:1;min-width:0;}
.product-name{font-weight:600;font-size:13px;color:#333;margin-bottom:4px;}
.product-attrs{font-size:11px;color:#666;margin-bottom:6px;line-height:1.3;}
.product-qty-price{display:flex;justify-content:space-between;align-items:center;font-size:12px;}
.product-qty{color:#666;}
.product-price{font-weight:700;color:#717fe0;}
.summary-box{background:#f8f9fa;padding:18px;border-radius:8px;border:1px solid #e9ecef;}
.summary-row{display:flex;justify-content:space-between;margin-bottom:10px;font-size:13px;}
.summary-row:last-child{border-top:2px solid #dee2e6;padding-top:10px;margin-bottom:0;font-weight:700;font-size:15px;color:#717fe0;}
.review-card{background:#f8f9fa;padding:18px;border-radius:8px;border:1px solid #e9ecef;margin-bottom:12px;}
.review-card:last-child{margin-bottom:0;}
.review-header{display:flex;gap:12px;margin-bottom:12px;}
.review-img{width:60px;height:60px;border-radius:6px;object-fit:cover;flex-shrink:0;}
.review-title{font-weight:600;font-size:13px;color:#333;margin-bottom:3px;}
.review-attrs{font-size:11px;color:#666;}
.star-group{display:flex;gap:6px;margin-bottom:12px;}
.star{font-size:28px;color:#ddd;cursor:pointer;transition:all 0.2s;}
.star:hover{color:#ffc107;transform:scale(1.15);}
.star.active{color:#ffc107;}
.form-group{margin-bottom:12px;}
.form-label{font-size:12px;font-weight:600;color:#333;margin-bottom:6px;display:block;}
.form-control{width:100%;padding:10px;border:1px solid #dee2e6;border-radius:6px;font-size:12px;font-family:inherit;resize:vertical;}
.form-control:focus{outline:none;border-color:#717fe0;box-shadow:0 0 0 3px rgba(113,127,224,0.1);}
.btn-group{display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;}
.btn{padding:11px 20px;border-radius:6px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:all 0.3s;font-size:13px;display:inline-flex;align-items:center;gap:6px;}
.btn-primary{background:#717fe0;color:#fff;}
.btn-primary:hover{background:#6c7ae0;transform:translateY(-2px);box-shadow:0 4px 12px rgba(113,127,224,0.3);}
.btn-secondary{background:#e9ecef;color:#333;border:1px solid #dee2e6;}
.btn-secondary:hover{background:#dee2e6;}
.btn-danger{background:#dc3545;color:#fff;}
.btn-danger:hover{background:#c82333;}
.btn-submit{background:#717fe0;color:#fff;padding:10px 18px;border:none;border-radius:6px;font-weight:600;cursor:pointer;font-size:12px;}
.btn-submit:hover{background:#6c7ae0;}
.btn-submit:disabled{opacity:0.6;cursor:not-allowed;}
.no-order{text-align:center;padding:80px 20px;}
.no-order-icon{font-size:80px;margin-bottom:20px;}
.no-order-text{font-size:20px;color:#333;margin-bottom:10px;font-weight:600;}
.no-order-desc{color:#666;margin-bottom:30px;font-size:14px;}
.search-box{display:flex;gap:10px;margin-bottom:20px;}
.search-box input{flex:1;padding:12px;border:1px solid #dee2e6;border-radius:6px;font-size:13px;}
.search-box button{padding:12px 24px;background:#717fe0;color:#fff;border:none;border-radius:6px;font-weight:600;cursor:pointer;}
.reviewed-badge{background:#d1e7dd;border:1px solid #badbcc;padding:12px;border-radius:8px;display:flex;align-items:center;gap:10px;}
.reviewed-icon{width:36px;height:36px;background:#0f5132;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;flex-shrink:0;}
.reviewed-text{flex:1;}
.reviewed-title{font-weight:600;color:#0f5132;font-size:13px;}
.reviewed-name{font-size:12px;color:#0f5132;}
@media(max-width:768px){
.track-header{padding:20px;}
.track-header h1{font-size:22px;}
.header-grid{grid-template-columns:repeat(2,1fr);}
.card{padding:18px;}
.info-grid{grid-template-columns:1fr;}
.product-item{flex-direction:column;}
.product-img{width:100%;height:100px;}
.btn-group{flex-direction:column;}
.btn{width:100%;justify-content:center;}
.search-box{flex-direction:column;}
.search-box input{width:100%;}
}
</style>

<div class="track-wrapper">
    @if(session('success'))
        <div style="background:#d1e7dd;color:#0f5132;padding:12px 16px;border-radius:8px;margin-bottom:20px;border-left:4px solid #0f5132;">{{ session('success') }}</div>
    @elseif(session('error'))
        <div style="background:#f8d7da;color:#842029;padding:12px 16px;border-radius:8px;margin-bottom:20px;border-left:4px solid #842029;">{{ session('error') }}</div>
    @endif

    @if(!$order)
        <div class="no-order">
            <div class="no-order-icon">📦</div>
            <div class="no-order-text">Không tìm thấy đơn hàng</div>
            <div class="no-order-desc">Vui lòng nhập mã đơn hàng hoặc số điện thoại để tra cứu</div>
            <form method="GET" action="{{ route('client.order.track') }}" class="search-box">
                <input type="text" name="code" placeholder="Nhập mã đơn / số điện thoại" required>
                <button type="submit">Tra cứu</button>
            </form>
            <a href="{{ route('home') }}" class="btn btn-secondary">← Quay lại trang chủ</a>
        </div>
    @else
        <!-- Header -->
        <div class="track-header">
            <h1>📦 Theo dõi đơn hàng</h1>
            <div class="header-grid">
                <div class="header-item">
                    <div class="header-label">Mã đơn</div>
                    <div class="header-value">{{ $order->code }}</div>
                </div>
                <div class="header-item">
                    <div class="header-label">Ngày đặt</div>
                    <div class="header-value">{{ $order->created_at?->format('d/m/Y') }}</div>
                </div>
                <div class="header-item">
                    <div class="header-label">Người nhận</div>
                    <div class="header-value">{{ $order->full_name }}</div>
                </div>
                <div class="header-item">
                    <div class="header-label">Điện thoại</div>
                    <div class="header-value">{{ $order->phone }}</div>
                </div>
            </div>
            @php
                $statusClass = [
                    'pending' => 'status-pending',
                    'processing' => 'status-processing',
                    'shipping' => 'status-shipped',
                    'delivered' => 'status-delivered',
                    'completed' => 'status-completed',
                    'cancelled' => 'status-cancelled',
                    'returned' => 'status-cancelled',
                    'cancel_request' => 'status-pending',
                    'return_request' => 'status-pending',
                ][$order->status] ?? 'status-pending';

                $statusLabel = [
                    'pending' => 'Chờ xác nhận',
                    'processing' => 'Đang xử lý',
                    'shipping' => 'Chờ giao hàng',
                    'delivered' => 'Đã giao',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                    'returned' => 'Trả hàng/Hoàn tiền',
                    'cancel_request' => 'Yêu cầu hủy',
                    'return_request' => 'Yêu cầu trả hàng',
                ][$order->status] ?? 'Chờ xử lý';
            @endphp
            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-title">
                <i class="ri-map-pin-line"></i> Trạng thái vận chuyển
            </div>
            @php
                $timelineSteps = [
                    ['label' => 'Đã tiếp nhận', 'desc' => 'Đơn hàng đã được tiếp nhận'],
                    ['label' => 'Đang xử lý', 'desc' => 'Đang chuẩn bị và đóng gói'],
                    ['label' => 'Đang giao', 'desc' => 'Bàn giao cho đơn vị vận chuyển'],
                    ['label' => 'Đã giao', 'desc' => 'Giao hàng thành công'],
                ];
                $statusRank = [
                    'pending' => 1,
                    'processing' => 2,
                    'shipping' => 3,
                    'delivered' => 4,
                    'completed' => 4,
                ][$order->status] ?? 0;
            @endphp
            <div class="timeline">
                @foreach($timelineSteps as $index => $step)
                    <div class="timeline-item {{ $statusRank > $index ? 'completed' : '' }} {{ $statusRank == $index + 1 ? 'active' : '' }}">
                        <div class="timeline-dot">{{ $index + 1 }}</div>
                        <div class="timeline-content">
                            <div class="timeline-title">{{ $step['label'] }}</div>
                            <div class="timeline-desc">{{ $step['desc'] }}</div>
                            @if($statusRank > $index)
                                <div class="timeline-time">✓ Hoàn thành</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Delivery Info -->
        <div class="card">
            <div class="card-title">
                <i class="ri-home-line"></i> Thông tin giao hàng
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Địa chỉ</div>
                    <div class="info-value">{{ $order->address }}, {{ $order->city }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Thanh toán</div>
                    <div class="info-value">
                        @if($order->payment_method === 'cod')
                            💵 COD
                        @else
                            💳 Online
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Trạng thái TT</div>
                    <div class="info-value">
                        @switch($order->payment_status)
                            @case('paid') ✓ Đã thanh toán @break
                            @case('refunded') ↩ Đã hoàn tiền @break
                            @default ⏳ Chưa thanh toán
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="card">
            <div class="card-title">
                <i class="ri-shopping-bag-line"></i> Sản phẩm ({{ $order->items->count() }})
            </div>
            <div class="products-list">
                @foreach($order->items as $item)
                    <div class="product-item">
                        <img src="{{ $item->product->default_image_url ?? asset('client/images/product/product-01.jpg') }}" alt="IMG" class="product-img">
                        <div class="product-info">
                            <div class="product-name">{{ $item->product->name ?? 'Sản phẩm' }}</div>
                            @php
                                $parts = [];
                                if($item->variant && $item->variant->size){ $parts[] = 'Size: '.$item->variant->size->name; }
                                if($item->variant && $item->variant->color){ $parts[] = 'Màu: '.$item->variant->color->name; }
                                if($item->variant && $item->variant->texture){ $parts[] = 'Chất liệu: '.$item->variant->texture->name; }
                            @endphp
                            @if(!empty($parts))
                                <div class="product-attrs">{{ implode(' • ', $parts) }}</div>
                            @endif
                            <div class="product-qty-price">
                                <span class="product-qty">x{{ $item->quantity }}</span>
                                <span class="product-price">{{ number_format($item->line_total, 0, ',', '.') }} ₫</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Summary -->
        <div class="card">
            <div class="card-title">
                <i class="ri-calculator-line"></i> Chi tiết thanh toán
            </div>
            <div class="summary-box">
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span>{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                </div>
                @if($order->discount > 0)
                    <div class="summary-row" style="color:#28a745;">
                        <span>Giảm giá</span>
                        <span>-{{ number_format($order->discount, 0, ',', '.') }} ₫</span>
                    </div>
                @endif
                @if($order->tax_amount > 0)
                    <div class="summary-row">
                        <span>Thuế</span>
                        <span>{{ number_format($order->tax_amount, 0, ',', '.') }} ₫</span>
                    </div>
                @endif
                @if($order->shipping_fee > 0)
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                    </div>
                @endif
                <div class="summary-row">
                    <span>Tổng cộng</span>
                    <span>{{ number_format($order->total, 0, ',', '.') }} ₫</span>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        @if($order && in_array($order->status, ['completed', 'delivered']))
            <div class="card">
                <div class="card-title">
                    <i class="ri-star-line"></i> Đánh giá sản phẩm
                </div>
                @foreach($order->items as $item)
                    @if(!isset($item->is_reviewed) || !$item->is_reviewed)
                        <div class="review-card" data-item-id="{{ $item->id }}">
                            <div class="review-header">
                                <img src="{{ $item->product->default_image_url ?? asset('client/images/product/product-01.jpg') }}" alt="{{ $item->product->name }}" class="review-img">
                                <div style="flex:1;">
                                    <div class="review-title">{{ $item->product->name }}</div>
                                    @if($item->variant)
                                        <div class="review-attrs">
                                            @if($item->variant->size)Size: {{ $item->variant->size->name }}@endif
                                            @if($item->variant->color) | Màu: {{ $item->variant->color->name }}@endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Đánh giá:</label>
                                <div class="star-group" data-item-id="{{ $item->id }}">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star" data-rating="{{ $i }}">★</span>
                                    @endfor
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nhận xét (tùy chọn):</label>
                                <textarea class="form-control review-content" data-item-id="{{ $item->id }}" placeholder="Chia sẻ cảm nhận..." style="min-height:70px;"></textarea>
                            </div>
                            <button type="button" class="btn-submit submit-review-btn" data-order-id="{{ $order->id }}" data-item-id="{{ $item->id }}" data-product-id="{{ $item->product_id }}" data-variant-id="{{ $item->variant_id }}">
                                <i class="ri-send-plane-fill"></i> Gửi đánh giá
                            </button>
                        </div>
                    @else
                        <div class="reviewed-badge">
                            <div class="reviewed-icon">✓</div>
                            <div class="reviewed-text">
                                <div class="reviewed-title">Đã đánh giá</div>
                                <div class="reviewed-name">{{ $item->product->name }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Actions -->
        <div class="btn-group">
            <a href="{{ route('client.order.list') }}" class="btn btn-secondary">
                <i class="ri-arrow-left-line"></i> Lịch sử đơn hàng
            </a>
            @if($order->status === 'pending')
                <form method="POST" action="{{ route('client.order.cancel', $order) }}" style="display:inline;" onsubmit="return confirm('Hủy đơn hàng này?');">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-close-line"></i> Hủy đơn
                    </button>
                </form>
            @endif
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="ri-shopping-cart-line"></i> Tiếp tục mua sắm
            </a>
        </div>
    @endif
</div>
</div>

@if($order && in_array($order->status, ['completed', 'delivered']))
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.star-group').forEach(function(group) {
        const stars = group.querySelectorAll('.star');
        let selectedRating = 0;
        
        stars.forEach(function(star) {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                group.dataset.selectedRating = selectedRating;
                stars.forEach((s, i) => s.classList.toggle('active', i < selectedRating));
            });
        });
    });
    
    document.querySelectorAll('.submit-review-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.getAttribute('data-item-id');
            const group = document.querySelector(`.star-group[data-item-id="${itemId}"]`);
            const content = document.querySelector(`.review-content[data-item-id="${itemId}"]`);
            const rating = group ? parseInt(group.dataset.selectedRating || 0) : 0;
            
            if (!rating) {
                alert('Vui lòng chọn số sao!');
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Đang gửi...';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            fetch("{{ url('/api/reviews') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                body: JSON.stringify({
                    order_id: parseInt(this.getAttribute('data-order-id')),
                    order_item_id: parseInt(itemId),
                    product_id: parseInt(this.getAttribute('data-product-id')),
                    variant_id: this.getAttribute('data-variant-id') !== 'null' ? parseInt(this.getAttribute('data-variant-id')) : null,
                    rating: rating,
                    content: content?.value.trim() || ''
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Cảm ơn bạn!');
                    location.reload();
                } else {
                    alert(data.message || 'Lỗi');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-send-plane-fill"></i> Gửi đánh giá';
                }
            })
            .catch(err => {
                alert('Lỗi: ' + err.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-send-plane-fill"></i> Gửi đánh giá';
            });
        });
    });
});
</script>
@endif

@if($order)
@push('scripts')
@vite(['resources/js/app.js'])
@php $currentUserId = Auth::check() ? Auth::id() : null; @endphp
<script>
(function(){
    const orderId = {{ $order->id }};
    const orderUserId = {{ $order->user_id ?? 'null' }};
    const currentUserId = {{ $currentUserId ?? 'null' }};
    
    function initRealtime() {
        if (typeof window.Echo === 'undefined') {
            setTimeout(initRealtime, 500);
            return;
        }
        if (orderUserId && currentUserId && currentUserId === orderUserId) {
            window.Echo.private(`user.${orderUserId}.orders`)
                .listen('.order.status.updated', (e) => {
                    if (e.id === orderId) location.reload();
                });
        }
    }
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
