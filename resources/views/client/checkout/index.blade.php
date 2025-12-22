@extends('client.layouts.app')

@section('title', 'Thanh toán - ' . env('APP_NAME'))

@section('content')
@php
    // Helper function để format số tiền: làm tròn về số nguyên (giống phí vận chuyển)
    function formatMoney($amount) {
        $amount = (float) $amount;
        $rounded = round($amount); // Làm tròn về số nguyên
        return number_format($rounded, 0, ',', '.'); // Luôn hiển thị 0 chữ số thập phân
    }
@endphp

<div class="container p-t-40 p-b-60">
    <style>
        /* Checkout polish */
        .co-card { background:#fff; border:1px solid #eee; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.05); }
        .co-card__body { padding:24px; }
        .co-title { display:flex; align-items:center; gap:10px; font-weight:800; font-size:20px; margin:0 0 18px; }
        .co-title:before { content:""; display:inline-block; width:6px; height:22px; background:#6777ef; border-radius:6px; }
        .co-label { font-weight:600; margin-bottom:8px; display:block; }
        .co-input, .co-select, .co-textarea { width:100%; border-radius:10px; border:1px solid #e6e6e6; padding:10px 14px; transition:border-color .2s ease, box-shadow .2s ease; }
        .co-input:focus, .co-select:focus, .co-textarea:focus { outline:none; border-color:#6777ef; box-shadow:0 0 0 3px rgba(103,119,239,.15); }
        .co-grid { display:grid; grid-template-columns:repeat(12,1fr); gap:16px; }
        .co-col-6 { grid-column: span 6; }
        .co-col-12 { grid-column: span 12; }
        @media (max-width: 991px){ .co-col-6 { grid-column: span 12; } }
        .co-summary { position:sticky; top:90px; }
        .co-line { display:flex; gap:12px; align-items:flex-start; padding:10px 0; border-bottom:1px dashed #eee; }
        .co-line:last-child { border-bottom:none; }
        .co-line img { width:58px; height:58px; border-radius:8px; object-fit:cover; flex-shrink:0; }
        .co-line__name { font-weight:700; color:#222; max-width:100%; word-wrap:break-word; }
        .co-info { flex:1; min-width:0; overflow:hidden; }
        .co-qty { width:64px; text-align:center; color:#333; flex-shrink:0; }
        .co-price { min-width:120px; text-align:right; font-weight:700; flex-shrink:0; }
        .co-actions { display:flex; gap:12px; }
        .btn-primary-x { background:#6777ef; color:#fff; border:none; border-radius:10px; padding:12px 16px; font-weight:700; }
        .btn-primary-x:hover { filter:brightness(.95); }
        .co-hint { background:#f8f9ff; border:1px solid #e3e7ff; padding:10px 12px; border-radius:8px; color:#556; }
        .co-error { color:#d33; font-size:13px; margin-top:4px; }
    </style>

    <div class="row">
        <div class="col-lg-7 m-b-30">
            <div class="co-card">
            <div class="co-card__body">
            <h4 class="co-title">Thông tin thanh toán</h4>
            @if (!auth()->check())
                <div class="co-hint" style="margin-bottom:14px; background:#fff3cd; border-color:#ffc107; color:#856404;">
                    <i class="zmdi zmdi-info-outline" style="margin-right:6px;"></i>
                    Bạn đang mua hàng với tư cách khách. <a href="{{ route('loginView') }}" style="color:#6777ef; text-decoration:underline;">Đăng nhập</a> để tích điểm và theo dõi đơn hàng dễ dàng hơn.
                </div>
            @endif
            @if ($errors->any())
                <div class="co-hint" style="margin-bottom:14px;">Vui lòng kiểm tra lại các trường bắt buộc.</div>
            @endif
            <form method="POST" action="{{ route('client.checkout.place') }}" id="checkout-form">
                @csrf
                <div class="p-b-10">
                    <h5 class="co-title" style="font-size:18px;">Thông tin người đặt</h5>
                </div>
                <div class="co-grid">
                    <div class="co-col-6">
                        <label class="co-label">Họ và tên *</label>
                        <input name="buyer_full_name" class="co-input" value="{{ old('buyer_full_name', optional(auth()->user())->name) }}" >
                        @error('buyer_full_name')<div class="co-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="co-col-6">
                        <label class="co-label">Số điện thoại *</label>
                        <input name="buyer_phone" class="co-input" value="{{ old('buyer_phone', optional(auth()->user())->phone) }}">
                        @error('buyer_phone')<div class="co-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="co-col-6">
                        <label class="co-label">Email</label>
                        <input name="buyer_email" type="email" class="co-input" value="{{ old('buyer_email', optional(auth()->user())->email) }}">
                        @error('buyer_email')<div class="co-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="p-t-20 p-b-10 d-flex align-items-center justify-content-between">
                    <h5 class="co-title" style="font-size:18px; margin-bottom:0;">Thông tin người nhận</h5>
                    <label style="font-size:13px; color:#555; display:flex; align-items:center; gap:6px;">
                        <input type="checkbox" id="copy-buyer-info" style="width:16px; height:16px;">
                        Giống người đặt
                    </label>
                </div>

                @if(auth()->check() && isset($addresses) && $addresses->count() > 0)
                <div class="co-grid" style="margin-bottom:16px;">
                    <div class="co-col-12">
                        <label class="co-label">
                            <i class="ri-map-pin-line me-1"></i>Chọn địa chỉ đã lưu
                        </label>
                        <select id="select-saved-address" class="co-select">
                            <option value="">-- Chọn địa chỉ hoặc nhập mới --</option>
                            @foreach($addresses as $addr)
                                @php
                                    // Lấy phường/xã: ưu tiên ward, nếu không có thì dùng district
                                    $commune = $addr->ward ?: $addr->district;
                                @endphp
                                <option value="{{ $addr->id }}" 
                                        data-full-name="{{ $addr->full_name }}"
                                        data-phone="{{ $addr->phone }}"
                                        data-email="{{ $addr->email ?? '' }}"
                                        data-city="{{ $addr->city }}"
                                        data-district="{{ $commune }}"
                                        data-address="{{ $addr->address }}">
                                    {{ $addr->full_name }} - {{ $addr->phone }} 
                                    @if($addr->is_default)
                                        <span style="color:#6777ef;">(Mặc định)</span>
                                    @endif
                                    - {{ $addr->full_address }}
                                </option>
                            @endforeach
                        </select>
                        <small style="color:#666; font-size:12px; margin-top:4px; display:block;">
                            <a href="{{ route('client.profile.addresses.index') }}" target="_blank" style="color:#6777ef; text-decoration:none;">
                                <i class="ri-add-line me-1"></i>Quản lý địa chỉ
                            </a>
                        </small>
                    </div>
                </div>
                @endif

                <div class="co-grid">
                    <div class="co-col-6">
                        <label class="co-label">Họ và tên người nhận *</label>
                        <input name="full_name" class="co-input" value="{{ old('full_name', optional(auth()->user())->name) }}" >
                        @error('full_name')<div class="co-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="co-col-6">
                        <label class="co-label">Số điện thoại người nhận *</label>
                        <input name="phone" class="co-input" value="{{ old('phone') }}" >
                        @error('phone')<div class="co-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="co-col-6">
                        <label class="co-label">Email người nhận</label>
                        <input name="email" type="email" class="co-input" value="{{ old('email', optional(auth()->user())->email) }}">
                        @error('email')<div class="co-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="co-col-6">
                        <label class="co-label">Tỉnh/Thành phố *</label>
                        <select id="province" class="co-select">
                            <option value="">Đang tải dữ liệu...</option>
                        </select>
                        <input type="hidden" name="city" value="{{ old('city') }}">
                        <small id="province-status" style="color:#666; font-size:12px; margin-top:4px; display:block;"></small>
                    </div>
                    <div class="co-col-6">
                        <label class="co-label">Phường/Xã *</label>
                        <select id="commune" class="co-select" required disabled>
                            <option value="">Chọn phường/xã</option>
                        </select>
                        <input type="hidden" name="district" value="{{ old('district') }}">
                        <small id="commune-status" style="color:#666; font-size:12px; margin-top:4px; display:block;"></small>
                    </div>
                    <div class="co-col-12">
                        <label class="co-label">Địa chỉ nhận hàng *</label>
                        <input name="address" class="co-input" value="{{ old('address') }}" placeholder="Ví dụ: 123 Nguyễn Trãi, Số nhà, Tòa nhà, Chi tiết đường phố..." required>
                        @error('address')<div class="co-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="co-col-12">
                        <label class="co-label">Ghi chú (tuỳ chọn)</label>
                        <textarea name="note" class="co-textarea" rows="3">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="p-t-10">
                    <h5 class="co-title" style="font-size:18px;">Phương thức thanh toán</h5>
                    <style>
                        .pay-group { display:flex; gap:12px; flex-wrap:wrap; }
                        .pay-option { position:relative; flex:1 1 260px; display:flex; align-items:center; gap:12px; border:1px solid #e6e6e6; border-radius:12px; padding:12px 14px; cursor:pointer; transition:border-color .2s ease, box-shadow .2s ease, background .2s ease; background:#fff; }
                        .pay-option input { position:absolute; inset:0; opacity:0; cursor:pointer; }
                        .pay-option__icon { width:36px; height:36px; border-radius:8px; background:#f2f3ff; display:flex; align-items:center; justify-content:center; font-size:18px; }
                        .pay-option__title { font-weight:700; color:#222; }
                        .pay-option__desc { font-size:12px; color:#666; margin-top:2px; }
                        .pay-option.active { border-color:#6777ef; box-shadow:0 0 0 3px rgba(103,119,239,.15); background:#f8f9ff; }
                        #payment-logos img { pointer-events: none; user-select: none; }
                    </style>
                    <div class="pay-group">
                        <label class="pay-option" data-method="cod">
                            <input type="radio" name="payment_method" value="cod" {{ old('payment_method','cod')=='cod' ? 'checked' : '' }}>
                            <div class="pay-option__icon">🚚</div>
                            <div>
                                <div class="pay-option__title">Thanh toán khi nhận hàng (COD)</div>
                                <div class="pay-option__desc">Kiểm tra hàng rồi thanh toán</div>
                            </div>
                        </label>

                        <label class="pay-option" data-method="online">
                            <input type="radio" name="payment_method" value="online" {{ old('payment_method')=='online' ? 'checked' : '' }}>
                            <div class="pay-option__icon" style="background:#fff;">
                                <img src="https://sandbox.vnpayment.vn/apis/assets/images/logo-icon/logo-primary.svg"
                                     alt="VNPAY"
                                     style="height:20px"
                                     onerror="this.onerror=null;this.src='https://vinadesign.vn/uploads/thumbnails/800/2023/05/vnpay-logo-vinadesign-25-12-59-16.jpg';">
                            </div>
                            <div>
                                <div class="pay-option__title">Thanh toán Online (VNPAY)</div>
                                <div class="pay-option__desc">Hỗ trợ thẻ ngân hàng, QR, ví điện tử</div>
                            </div>
                        </label>
                    </div>

                    <div id="online-hint" class="co-hint" style="display:none; margin-top:10px;">Bạn sẽ được chuyển tới cổng thanh toán an toàn để hoàn tất.</div>
                    <!-- <div id="payment-logos" class="p-t-10" style="display:none;">
                        <img src="https://vinadesign.vn/uploads/thumbnails/800/2023/05/vnpay-logo-vinadesign-25-12-59-16.jpg" alt="VNPAY" style="height:30px; margin-right:12px;" onerror="this.onerror=null;this.src='https://vnpay.vn/assets/front/images/logo.svg';">
                        <img src="https://static.mservice.io/img/logo-momo.png" alt="MoMo" style="height:28px; margin-right:10px; background:#fff; border-radius:4px; padding:2px;" onerror="this.onerror=null;this.src='https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png';">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa" style="height:22px; margin-right:8px; opacity:.9;" onerror="this.style.display='none';">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height:22px; opacity:.9;" onerror="this.style.display='none';">
                    </div> -->
                </div>

                <div class="p-t-20">
                    <div class="co-actions">
                        <a href="{{ route('client.cart.index') }}" class="co-hint" style="text-decoration:none;">← Quay lại giỏ hàng</a>
                        <button class="btn-primary-x">Đặt hàng</button>
                    </div>
                </div>
            </form>
            </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="co-card co-summary">
                <div class="co-card__body">
                <h4 class="co-title">Đơn hàng</h4>
                <ul class="p-l-0" style="list-style:none; margin:0;">
                    @foreach($cartData as $it)
                    <li class="co-line">
                        <img src="{{ $it['product']->default_image_url }}" alt="IMG">
                        <div class="co-info">
                            <div class="co-line__name">{{ $it['product']->name }}</div>
                            <div class="stext-110 cl6" style="font-size:14px; margin-top:4px; white-space:normal; word-break:break-word; word-wrap:break-word; overflow-wrap:break-word; line-height:1.6;">
                                @php
                                    $variantParts = [];
                                    if ($it['variant'] && $it['variant']->size) {
                                        $variantParts[] = '<span style="font-weight: 700; color: #666;">Size:</span> <span style="font-weight: 600;">' . $it['variant']->size->name . '</span>';
                                    }
                                    if ($it['variant'] && $it['variant']->color) {
                                        $variantParts[] = '<span style="font-weight: 700; color: #666;">Màu:</span> <span style="font-weight: 600;">' . $it['variant']->color->name . '</span>';
                                    }
                                    $variantDisplay = !empty($variantParts) ? implode(' &nbsp;&nbsp;|&nbsp;&nbsp; ', $variantParts) : '';
                                @endphp
                                @if($variantDisplay)
                                    <div style="display:block; width:100%;">{!! $variantDisplay !!}</div>
                                @endif
                            </div>
                        </div>
                        <div class="co-qty">x {{ $it['quantity'] }}</div>
                        <div class="co-price">{{ formatMoney($it['line_total']) }} ₫</div>
                    </li>
                    @endforeach
                </ul>

                {{-- Thông tin thêm về vận chuyển & thuế --}}
                <div class="co-hint m-t-15" style="font-size:13px;">
                    <div><strong>Đơn vị vận chuyển:</strong>
                        @if(isset($shippingCarrier) && $shippingCarrier)
                            {{ $shippingCarrier->name }} @if(isset($shippingFee)) - {{ formatMoney($shippingFee) }} ₫ @endif
                        @else
                            Chưa chọn
                        @endif
                    </div>
                    <div><strong>Mức thuế áp dụng:</strong>
                        @if(isset($taxRate) && $taxRate)
                            {{ $taxRate->name }} ({{ number_format($taxRate->rate * 100, 2, ',', '.') }} %)
                        @else
                            Không áp dụng
                        @endif
                    </div>
                    @if(isset($voucher) && $voucher)
                        <div><strong>Voucher:</strong> {{ $voucher['code'] }}</div>
                    @endif
                </div>

                <div style="padding-top:10px; border-top:1px solid #eee; margin-top:10px;">
                    <div class="flex-w flex-sb-m m-t-10">
                        <span class="mtext-101 cl2">Tạm tính</span>
                        <span class="mtext-101 cl2">{{ formatMoney($subtotal) }} ₫</span>
                    </div>
                    @if(isset($voucherDiscount) && $voucherDiscount > 0 && $voucher)
                    <div class="flex-w flex-sb-m m-t-10" style="color:#28a745;">
                        <span class="mtext-101 cl2">
                            Giảm giá Voucher
                            <small style="font-size:11px; color:#666;">({{ $voucher['code'] }})</small>
                        </span>
                        <span class="mtext-101 cl2" style="color:#28a745; font-weight:700;">-{{ formatMoney($voucherDiscount) }} ₫</span>
                    </div>
                    @endif

                    @if(isset($loyaltyDiscount) && $loyaltyDiscount > 0 && isset($currentTier) && $currentTier)
                    <div class="flex-w flex-sb-m m-t-10" style="color:#6777ef;">
                        <span class="mtext-101 cl2">
                            Giảm giá thành viên
                            <small style="font-size:11px; color:#666;">({{ $currentTier->name }} -{{ number_format($currentTier->discount_rate, 0) }}%)</small>
                        </span>
                        <span class="mtext-101 cl2" style="color:#6777ef; font-weight:700;">-{{ formatMoney($loyaltyDiscount) }} ₫</span>
                    </div>
                    @endif

                    @if(isset($taxAmount) && $taxAmount > 0 && isset($taxRate))
                    <div class="flex-w flex-sb-m m-t-10">
                        <span class="mtext-101 cl2">
                            Thuế ({{ $taxRate->name }})
                        </span>
                        <span class="mtext-101 cl2">{{ formatMoney($taxAmount) }} ₫</span>
                    </div>
                    @endif

                    @if(isset($shippingFee) && $shippingFee > 0)
                    <div class="flex-w flex-sb-m m-t-10">
                        <span class="mtext-101 cl2">
                            Phí vận chuyển
                            @if(isset($shippingCarrier) && $shippingCarrier)
                                <small style="font-size:11px; color:#666;">({{ $shippingCarrier->name }})</small>
                            @endif
                        </span>
                        <span class="mtext-101 cl2">{{ formatMoney($shippingFee) }} ₫</span>
                    </div>
                    @endif

                    <div class="flex-w flex-sb-m m-t-10" style="padding-top:10px; border-top:1px solid #eee;">
                        <span class="mtext-101 cl2" style="font-weight:700; font-size:16px;">Tổng cộng</span>
                        <span class="mtext-101 cl2 co-price" style="font-size:18px; color:#6777ef;">{{ formatMoney($total) }} ₫</span>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('client/js/provinces-api.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var radios = document.querySelectorAll('input[name="payment_method"]');
    var payOptions = document.querySelectorAll('.pay-option');
    var buyerFields = {
        name: document.querySelector('input[name="buyer_full_name"]'),
        phone: document.querySelector('input[name="buyer_phone"]'),
        email: document.querySelector('input[name="buyer_email"]')
    };
    var receiverFields = {
        name: document.querySelector('input[name="full_name"]'),
        phone: document.querySelector('input[name="phone"]'),
        email: document.querySelector('input[name="email"]')
    };
    var copyCheckbox = document.getElementById('copy-buyer-info');

    function toggle(){
        var val = document.querySelector('input[name="payment_method"]:checked').value;
        var show = (val === 'online');
        document.getElementById('online-hint').style.display = show ? 'block' : 'none';
        var logos = document.getElementById('payment-logos'); if (logos) logos.style.display = show ? 'block' : 'none';
        payOptions.forEach(function(el){ el.classList.toggle('active', el.querySelector('input').checked); });
    }

    function copyBuyerInfo(){
        if (!buyerFields.name || !receiverFields.name) return;
        receiverFields.name.value = buyerFields.name.value || '';
        receiverFields.phone.value = buyerFields.phone ? buyerFields.phone.value || '' : receiverFields.phone.value;
        if (receiverFields.email && buyerFields.email) {
            receiverFields.email.value = buyerFields.email.value || '';
        }
    }

    payOptions.forEach(function(el){ el.addEventListener('click', function(){ var inp = el.querySelector('input'); if (inp) { inp.checked = true; toggle(); } }); });
    radios.forEach(function(r){ r.addEventListener('change', toggle); });

    if (copyCheckbox) {
        copyCheckbox.addEventListener('change', function(){
            if (this.checked) {
                copyBuyerInfo();
            }
        });
    }
    Object.values(buyerFields).forEach(function(field){
        if (!field || !copyCheckbox) return;
        field.addEventListener('input', function(){
            if (copyCheckbox.checked) {
                copyBuyerInfo();
            }
        });
    });

    toggle();

    // Xử lý chọn địa chỉ đã lưu
    var selectAddress = document.getElementById('select-saved-address');
    var provinceSelect = document.getElementById('province');
    var communeSelect = document.getElementById('commune');
    var cityHidden = document.querySelector('input[name="city"]');
    var districtHidden = document.querySelector('input[name="district"]');
    var addressInput = document.querySelector('input[name="address"]');

    if (selectAddress) {
        selectAddress.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                return; // Không chọn gì hoặc chọn "-- Chọn địa chỉ hoặc nhập mới --"
            }

            // Lấy dữ liệu từ data attributes
            // data-district đã được set thành commune (phường/xã) từ PHP
            var fullName = selectedOption.getAttribute('data-full-name') || '';
            var phone = selectedOption.getAttribute('data-phone') || '';
            var email = selectedOption.getAttribute('data-email') || '';
            var city = selectedOption.getAttribute('data-city') || '';
            var commune = selectedOption.getAttribute('data-district') || ''; // Phường/Xã
            var address = selectedOption.getAttribute('data-address') || '';

            // Điền thông tin vào form
            if (receiverFields.name) receiverFields.name.value = fullName;
            if (receiverFields.phone) receiverFields.phone.value = phone;
            if (receiverFields.email) receiverFields.email.value = email;
            if (addressInput) addressInput.value = address;

            // Set giá trị vào hidden fields trước (provinces-api.js sẽ tự động restore)
            if (cityHidden) cityHidden.value = city;
            if (districtHidden) {
                districtHidden.value = commune; // commune là phường/xã
                // Đảm bảo giá trị được set trước khi provinces load
                districtHidden.setAttribute('value', commune);
            }

            // Hàm chọn phường/xã sau khi communes đã load
            function selectCommune() {
                if (!commune || !communeSelect) return;
                
                // Đợi communes load xong (không disabled và có options)
                var checkCommuneInterval = setInterval(function() {
                    if (!communeSelect.disabled && communeSelect.options.length > 1) {
                        clearInterval(checkCommuneInterval);
                        
                        // Đảm bảo hidden field vẫn có giá trị
                        if (districtHidden && !districtHidden.value) {
                            districtHidden.value = commune;
                        }
                        
                        // Tìm và chọn phường/xã
                        var foundCommune = false;
                        for (var k = 0; k < communeSelect.options.length; k++) {
                            var commOpt = communeSelect.options[k];
                            if (!commOpt.value) continue; // Bỏ qua option rỗng
                            
                            var commText = commOpt.textContent || commOpt.text || '';
                            var commName = commOpt.dataset.name || '';
                            
                            // So sánh chính xác hoặc chứa (bỏ qua "Xã", "Phường", "Thị trấn")
                            var communeClean = commune.replace(/^(Xã|Phường|Thị trấn)\s*/i, '').trim();
                            var commTextClean = commText.replace(/^(Xã|Phường|Thị trấn)\s*/i, '').trim();
                            var commNameClean = commName.replace(/^(Xã|Phường|Thị trấn)\s*/i, '').trim();
                            
                            if (commText === commune || commName === commune || 
                                commText.includes(commune) || commName.includes(commune) ||
                                commTextClean === communeClean || commNameClean === communeClean ||
                                commOpt.value === commune) {
                                communeSelect.value = commOpt.value;
                                communeSelect.dispatchEvent(new Event('change'));
                                foundCommune = true;
                                console.log('Đã chọn phường/xã:', commune, '->', commText);
                                break;
                            }
                        }
                        
                        if (!foundCommune) {
                            console.warn('Không tìm thấy phường/xã:', commune, 'Có', communeSelect.options.length, 'options');
                            // Thử lại với provinces-api.js restore logic
                            if (districtHidden && districtHidden.value) {
                                var matching = Array.from(communeSelect.options).find(function(opt) {
                                    return opt.dataset.name === districtHidden.value || 
                                           opt.textContent === districtHidden.value;
                                });
                                if (matching) {
                                    communeSelect.value = matching.value;
                                    communeSelect.dispatchEvent(new Event('change'));
                                    console.log('Đã chọn phường/xã bằng restore logic:', districtHidden.value);
                                }
                            }
                        }
                    }
                }, 100);
                
                // Timeout sau 5 giây
                setTimeout(function() {
                    clearInterval(checkCommuneInterval);
                }, 5000);
            }

            // Tìm và chọn tỉnh/thành phố từ dropdown
            if (city && provinceSelect) {
                // Hàm tìm và chọn tỉnh
                function findAndSelectProvince() {
                    if (provinceSelect.options.length <= 1) return false; // Chưa load xong
                    
                    for (var i = 0; i < provinceSelect.options.length; i++) {
                        var opt = provinceSelect.options[i];
                        if (!opt.value) continue; // Bỏ qua option rỗng
                        
                        var optText = opt.textContent || opt.text || '';
                        var optName = opt.dataset.name || '';
                        
                        // So sánh chính xác hoặc chứa
                        if (optText === city || optName === city || 
                            optText.includes(city) || optName.includes(city) ||
                            opt.value === city) {
                            provinceSelect.value = opt.value;
                            provinceSelect.dispatchEvent(new Event('change'));
                            console.log('Đã chọn tỉnh/thành phố:', city);
                            
                            // Sau khi chọn tỉnh, đợi communes load xong rồi chọn phường/xã
                            selectCommune();
                            return true;
                        }
                    }
                    return false;
                }
                
                // Thử tìm ngay lập tức
                var foundProvince = findAndSelectProvince();
                
                // Nếu không tìm thấy, đợi provinces load xong rồi thử lại
                if (!foundProvince) {
                    var checkProvinceInterval = setInterval(function() {
                        if (findAndSelectProvince()) {
                            clearInterval(checkProvinceInterval);
                        }
                    }, 200);
                    
                    // Timeout sau 5 giây
                    setTimeout(function() {
                        clearInterval(checkProvinceInterval);
                    }, 5000);
                }
            }
        });
    }
});
</script>
@endpush

@endsection



