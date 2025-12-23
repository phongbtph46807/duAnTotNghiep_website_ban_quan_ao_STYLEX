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
                    {{-- Thông tin người đặt (nếu khác người nhận) --}}
                    @if($order->buyer_name && ($order->buyer_name !== $order->full_name || $order->buyer_phone !== $order->phone))
                        <div style="margin-bottom:8px;padding:8px;background:#f8f9fa;border-radius:6px;">
                            <strong style="color:#6777ef;">Người đặt hàng:</strong><br>
                            {{ $order->buyer_name }}
                            @if($order->buyer_phone) - {{ $order->buyer_phone }} @endif
                            @if($order->buyer_email) <br><small style="color:#666;">{{ $order->buyer_email }}</small> @endif
                        </div>
                    @endif
                    
                    <strong>Người nhận:</strong> {{ $order->full_name }} - {{ $order->phone }}<br>
                    @if($order->email)
                        <strong>Email người nhận:</strong> {{ $order->email }}<br>
                    @endif
                    <strong>Địa chỉ:</strong> {{ $order->address }}, {{ $order->city }}<br>
                    
                    {{-- Đơn vị vận chuyển --}}
                    @if($order->shippingCarrier)
                        <strong>Đơn vị vận chuyển:</strong> {{ $order->shippingCarrier->name }}<br>
                    @endif
                    
                    {{-- Ghi chú đơn hàng --}}
                    @if($order->note)
                        <div style="margin-top:6px;padding:8px;background:#fff7e6;border-left:3px solid #ffc107;border-radius:4px;">
                            <strong>Ghi chú:</strong> {{ $order->note }}
                        </div>
                    @endif
                    
                    <div style="margin-top:8px;padding-top:8px;border-top:1px dashed #eee;">
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
                    
                    {{-- Lý do hủy/trả hàng --}}
                    @if($order->status === 'cancelled' && $order->cancel_reason)
                        <div style="margin-top:8px;padding:10px;background:#fff1f0;border-left:3px solid #ff4d4f;border-radius:4px;">
                            <strong style="color:#cf1322;">Lý do hủy đơn:</strong><br>
                            <div style="margin-top:4px;color:#666;">{{ $order->cancel_reason }}</div>
                            @if($order->cancel_images && count($order->cancel_images) > 0)
                                <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
                                    @foreach($order->cancel_images as $img)
                                        <a href="{{ asset('storage/' . $img) }}" target="_blank" style="display:block;">
                                            <img src="{{ asset('storage/' . $img) }}" alt="Ảnh hủy đơn" style="width:60px;height:60px;object-fit:cover;border-radius:4px;border:1px solid #ddd;">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    @if($order->status === 'returned' && $order->return_reason)
                        <div style="margin-top:8px;padding:10px;background:#fff7e6;border-left:3px solid #f59e0b;border-radius:4px;">
                            <strong style="color:#d48806;">Lý do trả hàng:</strong><br>
                            <div style="margin-top:4px;color:#666;">{{ $order->return_reason }}</div>
                            @if($order->return_images && count($order->return_images) > 0)
                                <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
                                    @foreach($order->return_images as $img)
                                        <a href="{{ asset('storage/' . $img) }}" target="_blank" style="display:block;">
                                            <img src="{{ asset('storage/' . $img) }}" alt="Ảnh trả hàng" style="width:60px;height:60px;object-fit:cover;border-radius:4px;border:1px solid #ddd;">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    {{-- Thông tin cập nhật --}}
                    @if($order->updated_at && $order->updated_at != $order->created_at)
                        <div style="margin-top:8px;font-size:12px;color:#999;">
                            <strong>Cập nhật lần cuối:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}
                            @if($order->updatedByUser)
                                <br><small>Bởi: {{ $order->updatedByUser->name }}</small>
                            @endif
                        </div>
                    @endif
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderId = {{ $order->id }};
    const orderCode = '{{ $order->code }}';
    @if(auth()->check())
    const userId = {{ auth()->id() }};
    @else
    const userId = null;
    @endif
    
    // Chỉ chạy khi window.Echo đã được load
    if (typeof window.Echo !== 'undefined') {
        // Listen trên public channel cho order tracking (không cần auth)
        window.Echo.channel(`order.${orderCode}.track`)
            .listen('.order.status.updated', (e) => {
                // Chỉ xử lý nếu là order hiện tại
                if (e.id === orderId || e.code === orderCode) {
                    updateOrderStatus(e);
                }
            });
        
        // Nếu user đã đăng nhập, cũng listen trên private channel để đảm bảo nhận được event
        if (userId !== null) {
            window.Echo.private(`user.${userId}.orders`)
                .listen('.order.status.updated', (e) => {
                    // Chỉ xử lý nếu là order hiện tại
                    if (e.id === orderId || e.code === orderCode) {
                        updateOrderStatus(e);
                    }
                });
        }
        
        console.log('✅ Realtime order tracking enabled for order:', orderCode);
    } else {
        console.warn('⚠️ Laravel Echo not loaded. Realtime updates disabled.');
        
        // Fallback: polling mỗi 10 giây để cập nhật status
        setInterval(function() {
            fetch('{{ route("client.order.track") }}?code={{ $order->code }}&ajax=1', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.status && data.status !== '{{ $order->status }}') {
                    location.reload(); // Reload nếu status thay đổi
                }
            })
            .catch(err => console.error('Polling error:', err));
        }, 10000);
    }
    
    function updateOrderStatus(orderData) {
        const newStatus = orderData.status;
        const currentStatus = '{{ $order->status }}';
        
        if (newStatus === currentStatus) {
            return; // Không cần cập nhật nếu status không đổi
        }
        
        // Cập nhật status badge
        const statusLabels = {
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
        
        const statusClasses = {
            'pending': 'status-pending',
            'processing': 'status-processing',
            'shipping': 'status-processing',
            'completed': 'status-completed',
            'delivered': 'status-completed',
            'cancelled': 'status-cancelled',
            'returned': 'status-cancelled',
            'cancel_request': 'status-cancelled',
            'return_request': 'status-cancelled',
        };
        
        // Cập nhật badge
        const statusBadge = document.querySelector('.order-badge');
        if (statusBadge) {
            statusBadge.textContent = statusLabels[newStatus] || newStatus;
            statusBadge.className = 'order-badge ' + (statusClasses[newStatus] || 'status-pending');
        }
        
        // Cập nhật timeline
        const statusRank = {
            'pending': 1,
            'processing': 2,
            'shipping': 3,
            'completed': 4,
            'delivered': 4,
        }[newStatus] || 0;
        
        const timelineItems = document.querySelectorAll('.timeline-item');
        timelineItems.forEach((item, index) => {
            if (index + 1 <= statusRank) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
        
        // Thêm timeline item cho cancelled/returned nếu cần
        if (['cancelled', 'returned'].includes(newStatus)) {
            const timeline = document.querySelector('.timeline');
            if (timeline && !timeline.querySelector('.timeline-item:last-child .timeline-title')?.textContent.includes('hủy') && 
                !timeline.querySelector('.timeline-item:last-child .timeline-title')?.textContent.includes('trả')) {
                const newItem = document.createElement('li');
                newItem.className = 'timeline-item active';
                newItem.innerHTML = `
                    <span class="timeline-dot"></span>
                    <div class="timeline-title">
                        ${newStatus === 'returned' ? 'Đơn hàng đã trả/hoàn tiền' : 'Đơn hàng đã hủy'}
                    </div>
                    <div class="timeline-time">Vui lòng liên hệ chăm sóc khách hàng nếu cần hỗ trợ.</div>
                `;
                timeline.appendChild(newItem);
            }
        }
        
        // Cập nhật payment status nếu có
        if (orderData.payment_status) {
            const paymentStatusEl = document.querySelector('[data-payment-status]');
            if (paymentStatusEl) {
                const paymentLabels = {
                    'paid': 'Đã thanh toán',
                    'refunded': 'Đã hoàn tiền',
                    'pending': 'Chưa thanh toán',
                };
                paymentStatusEl.textContent = paymentLabels[orderData.payment_status] || orderData.payment_status;
            }
        }
        
        // Hiển thị thông báo
        showStatusUpdateNotification(orderData.code, statusLabels[newStatus] || newStatus);
        
        // Cập nhật action buttons
        updateActionButtons(newStatus);
    }
    
    function showStatusUpdateNotification(orderCode, statusLabel) {
        // Tạo notification element
        const notification = document.createElement('div');
        notification.style.cssText = 'position:fixed;top:20px;right:20px;background:#10b981;color:#fff;padding:14px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:99999;font-weight:600;max-width:350px;animation:slideInRight 0.3s ease;';
        notification.innerHTML = `
            <div style="display:flex;align-items:center;gap:10px;">
                <i class="zmdi zmdi-check-circle" style="font-size:20px;"></i>
                <div>
                    <div style="font-size:14px;font-weight:700;">Đơn hàng đã được cập nhật</div>
                    <div style="font-size:12px;margin-top:4px;opacity:0.9;">Đơn #${orderCode}: ${statusLabel}</div>
                </div>
            </div>
        `;
        
        // Thêm animation CSS nếu chưa có
        if (!document.querySelector('#statusUpdateAnimation')) {
            const style = document.createElement('style');
            style.id = 'statusUpdateAnimation';
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
        
        document.body.appendChild(notification);
        
        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
    
    function updateActionButtons(newStatus) {
        const actionsContainer = document.querySelector('.d-flex.justify-content-between.flex-wrap');
        if (!actionsContainer) return;
        
        // Xóa button hủy đơn nếu không còn pending
        if (newStatus !== 'pending') {
            const cancelForm = actionsContainer.querySelector('.cancel-order-form');
            if (cancelForm) {
                cancelForm.remove();
            }
        }
        
        // Thêm button hủy đơn nếu chuyển về pending (ít khi xảy ra)
        if (newStatus === 'pending') {
            const existingCancelForm = actionsContainer.querySelector('.cancel-order-form');
            if (!existingCancelForm) {
                const cancelForm = document.createElement('form');
                cancelForm.method = 'POST';
                cancelForm.action = '{{ route("client.order.cancel", $order) }}';
                cancelForm.className = 'cancel-order-form';
                cancelForm.innerHTML = `
                    @csrf
                    <button type="submit" class="btn-outline cancel-order-btn" style="border:1px solid #ff4d4f;color:#ff4d4f;background:#fff;border-radius:8px;padding:10px 14px;font-weight:600;cursor:pointer;transition:all 0.3s;">
                        Hủy đơn hàng
                    </button>
                `;
                const buttonsDiv = actionsContainer.querySelector('.d-flex.gap-2');
                if (buttonsDiv) {
                    buttonsDiv.insertBefore(cancelForm, buttonsDiv.firstChild);
                }
            }
        }
    }
});
</script>
@endpush
@endif
@endsection
