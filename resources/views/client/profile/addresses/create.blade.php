@extends('client.layouts.app')

@section('title', 'Thêm địa chỉ mới - ' . env('APP_NAME'))

@section('content')
@include('client.partials.profile-styles')

<div class="container p-t-40 p-b-60">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card profile-card profile-sidebar">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="profile-avatar">
                        @else
                            <div class="profile-avatar-placeholder">
                                <span style="font-size: 48px; color: white; font-weight: bold;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-2" style="font-weight: 600; color: #333;">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-4" style="font-size: 14px; word-break: break-word;">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <!-- Settings Menu -->
            <div class="card profile-card mt-3">
                <div class="card-body p-0">
                    <div style="padding: 16px; border-bottom: 1px solid #e0e0e0;">
                        <h6 class="mb-0" style="font-weight: 600; color: #333; font-size: 15px;">
                            <i class="ri-settings-3-line me-2" style="color: #6777ef;"></i>Cài đặt & Quản lý
                        </h6>
                    </div>
                    <div style="padding: 8px;">
                        <a href="{{ route('client.profile.index') }}" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-user-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Hồ sơ cá nhân</span>
                        </a>
                        <a href="{{ route('client.profile.index', ['tab' => 'orders']) }}" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-shopping-bag-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Đơn hàng của tôi</span>
                        </a>
                        <a href="{{ route('client.profile.addresses.index') }}" class="settings-menu-item-sidebar active" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-map-pin-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Địa chỉ giao hàng</span>
                        </a>
                        <a href="{{ route('client.profile.index', ['tab' => 'card']) }}" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-wallet-3-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Ví của tôi</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <div class="card profile-card">
                <div class="profile-card-header">
                    <h4 class="mb-0" style="font-weight: 600; color: #333;">
                        <i class="ri-add-line me-2" style="color: #6777ef;"></i>Thêm địa chỉ mới
                    </h4>
                </div>
                <div class="profile-card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('client.profile.addresses.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="full_name" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Họ và tên người nhận <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                       id="full_name" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" 
                                       required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="phone" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', auth()->user()->phone_number) }}" 
                                       required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Email
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                       style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="address_type" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Loại địa chỉ <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('address_type') is-invalid @enderror" 
                                        id="address_type" name="address_type" required
                                        style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    <option value="home" {{ old('address_type') == 'home' ? 'selected' : '' }}>Nhà riêng</option>
                                    <option value="office" {{ old('address_type') == 'office' ? 'selected' : '' }}>Văn phòng</option>
                                    <option value="other" {{ old('address_type') == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('address_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="province" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Tỉnh/Thành phố <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('city') is-invalid @enderror" 
                                        id="province" name="city"
                                        style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;"
                                        data-old-value="{{ old('city') }}">
                                    <option value="">Đang tải dữ liệu...</option>
                                </select>
                                <small id="province-status" style="color:#666; font-size:12px; margin-top:4px; display:block;"></small>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="commune" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Phường/Xã <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('district') is-invalid @enderror" 
                                        id="commune" name="district" disabled
                                        style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;"
                                        data-old-value="{{ old('district') }}">
                                    <option value="">Chọn phường/xã</option>
                                </select>
                                <small id="commune-status" style="color:#666; font-size:12px; margin-top:4px; display:block;"></small>
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="address" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Địa chỉ chi tiết <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" required
                                          style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4 d-flex justify-content-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1"
                                           {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default" style="font-weight: 500; margin-left: 5px;">
                                        Đặt làm địa chỉ mặc định
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4" style="border-top: 2px solid #f0f0f0;">
                            <a href="{{ route('client.profile.addresses.index') }}" class="btn btn-secondary" style=" margin-right: 5px; border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                                <i class="ri-arrow-left-line me-1"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="ri-save-line me-1"></i> Lưu địa chỉ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const communeSelect = document.getElementById('commune');
    const addressInput = document.getElementById('address');

    if (!provinceSelect) return;

    let selectedProvinceName = '';
    let selectedCommuneName = '';

    function normalizeData(response) {
        if (response.data && Array.isArray(response.data)) {
            return response.data;
        } else if (Array.isArray(response)) {
            return response;
        }
        return [];
    }

    function updateAddressField() {
        if (selectedProvinceName && selectedCommuneName && addressInput) {
            addressInput.value = `${selectedCommuneName}, ${selectedProvinceName}`;
        }
    }

    async function loadProvinces() {
        try {
            provinceSelect.innerHTML = '<option value="">Đang tải tỉnh/thành phố...</option>';
            const response = await fetch('/api/provinces');
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const json = await response.json();
            if (json.error) throw new Error(json.error);
            
            const provinces = normalizeData(json);
            if (!provinces || provinces.length === 0) throw new Error('Không có dữ liệu tỉnh/thành phố');

            provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành phố --</option>';
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code || province.id;
                option.textContent = province.name;
                option.dataset.name = province.name;
                provinceSelect.appendChild(option);
            });

            const oldValue = provinceSelect.dataset.oldValue;
            if (oldValue) {
                const matching = Array.from(provinceSelect.options).find(opt => 
                    opt.dataset.name === oldValue
                );
                if (matching) {
                    provinceSelect.value = matching.value;
                    provinceSelect.dispatchEvent(new Event('change'));
                }
            }

            document.getElementById('province-status').textContent = `✓ ${provinces.length} tỉnh/thành phố`;
        } catch (error) {
            console.error('Error loading provinces:', error);
            provinceSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            document.getElementById('province-status').textContent = '❌ ' + error.message;
        }
    }

    async function loadCommunes(provinceID) {
        try {
            communeSelect.innerHTML = '<option value="">Đang tải phường/xã...</option>';
            communeSelect.disabled = true;
            
            const response = await fetch(`/api/communes?provinceID=${provinceID}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const json = await response.json();
            if (json.error) throw new Error(json.error);
            
            const communes = normalizeData(json);
            if (!communes || communes.length === 0) throw new Error('Không có dữ liệu phường/xã');

            communeSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            communes.forEach(commune => {
                const option = document.createElement('option');
                option.value = commune.code || commune.id;
                option.textContent = commune.name;
                option.dataset.name = commune.name;
                communeSelect.appendChild(option);
            });
            
            communeSelect.disabled = false;
            document.getElementById('commune-status').textContent = `✓ ${communes.length} phường/xã`;

            const oldValue = communeSelect.dataset.oldValue;
            if (oldValue) {
                const matching = Array.from(communeSelect.options).find(opt => 
                    opt.dataset.name === oldValue
                );
                if (matching) {
                    communeSelect.value = matching.value;
                    communeSelect.dispatchEvent(new Event('change'));
                }
            }
        } catch (error) {
            console.error('Error loading communes:', error);
            communeSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            document.getElementById('commune-status').textContent = '❌ ' + error.message;
        }
    }

    provinceSelect.addEventListener('change', function() {
        if (!this.value) {
            communeSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            communeSelect.disabled = true;
            selectedProvinceName = '';
            selectedCommuneName = '';
            if (addressInput) addressInput.value = '';
            document.getElementById('commune-status').textContent = '';
            return;
        }

        selectedProvinceName = this.options[this.selectedIndex].dataset.name;
        selectedCommuneName = '';
        
        communeSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        communeSelect.disabled = true;
        if (addressInput) addressInput.value = '';
        loadCommunes(this.value);
    });

    communeSelect.addEventListener('change', function() {
        if (!this.value) {
            selectedCommuneName = '';
            if (addressInput) addressInput.value = '';
            return;
        }
        selectedCommuneName = this.options[this.selectedIndex].dataset.name;
        updateAddressField();
    });

    loadProvinces();
});
</script>
@endpush

