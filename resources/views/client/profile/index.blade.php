@extends('client.layouts.app')

@section('title', 'Hồ sơ cá nhân - ' . env('APP_NAME'))

@section('content')
@include('client.partials.profile-styles')

<div class="container p-t-40 p-b-60">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card profile-card profile-sidebar">
                <div class="card-body text-center p-4">
                    <div class="mb-4">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="profile-avatar">
                        @else
                            <div class="profile-avatar-placeholder">
                                <span style="font-size: 48px; color: white; font-weight: bold;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-2" style="font-weight: 600; color: #333;">{{ $user->name }}</h5>
                    <p class="text-muted mb-4" style="font-size: 14px; word-break: break-word;">{{ $user->email }}</p>
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
                        <a href="{{ route('client.profile.index') }}" class="settings-menu-item-sidebar active" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-user-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Hồ sơ cá nhân</span>
                        </a>
                        <a href="{{ route('client.order.list') }}" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-shopping-bag-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Đơn hàng của tôi</span>
                        </a>
                        <a href="{{ route('client.profile.addresses.index') }}" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-map-pin-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Địa chỉ giao hàng</span>
                        </a>
                        <a href="#" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-bank-card-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Phương thức thanh toán</span>
                        </a>
                        <a href="#" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-notification-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Cài đặt thông báo</span>
                        </a>
                        <a href="#" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-shield-check-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Bảo mật tài khoản</span>
                        </a>
                        <a href="#" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-star-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Đánh giá của tôi</span>
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
                        <i class="ri-user-settings-line me-2" style="color: #6777ef;"></i>Thông tin cá nhân
                    </h4>
                </div>
                <div class="profile-card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Section -->
                        <div class="form-section">
                            <div class="avatar-upload-section">
                                <label class="form-label mb-3" style="font-weight: 600; font-size: 16px; color: #333;">
                                    <i class="ri-image-line me-2" style="color: #6777ef;"></i>Ảnh đại diện
                                </label>
                                <div>
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" 
                                             id="avatarPreview"
                                             class="avatar-preview-large"
                                             onclick="document.getElementById('avatar').click();">
                                    @else
                                        <div id="avatarPreview" 
                                             class="avatar-placeholder-large"
                                             onclick="document.getElementById('avatar').click();">
                                            <span style="font-size: 60px; color: white; font-weight: bold;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="avatar" id="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
                                <button type="button" class="btn btn-outline-primary" style="border-radius: 8px; padding: 8px 20px;" onclick="document.getElementById('avatar').click();">
                                    <i class="ri-camera-line me-1"></i> Chọn ảnh mới
                                </button>
                            </div>
                        </div>

                        {{-- Loyalty Tier Card --}}
                        @if($currentTier)
                        <div class="card profile-card mb-4"
                            id="tierCard"
                            data-current-tier="{{ $currentTier->id }}">

                            <div class="card-body text-center"
                                id="tierContent"
                                style="
                                    background: {{ $currentTier->color }};
                                    color: {{ $currentTier->text_color }};
                                    border-radius: 16px;
                                    padding: 24px;
                                ">

                                {{-- Navigation --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <button type="button"
                                            class="tier-nav-btn"
                                            onclick="prevTier()">
                                        <i class="ri-arrow-left-s-line"></i>
                                    </button>

                                    <h4 id="tierName"
                                        class="tier-name fw-bold mb-0">
                                        Hạng {{ $currentTier->name }}
                                        <span class="badge bg-dark ms-2">Đang sở hữu</span>
                                    </h4>

                                    <button type="button"
                                            class="tier-nav-btn"
                                            onclick="nextTier()">
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>

                                {{-- Discount --}}
                                <div class="tier-benefit fw-semibold mb-2"
                                    style="font-size: 16px;">
                                    🎁 Giảm <strong>{{ $currentTier->discount_rate }}%</strong>
                                    cho mọi đơn hàng
                                </div>

                                {{-- Condition --}}
                                <div class="small fw-semibold mb-3"
                                    id="tierCondition">
                                    Tổng chi tiêu từ
                                    {{ number_format($currentTier->min_spend_required) }} đ
                                </div>

                                {{-- Progress Bar --}}
                                @php
                                    $progress = $nextTierData['progress'] ?? 100;
                                @endphp

                                <div class="progress mb-3"
                                    style="height: 10px; border-radius: 20px; background: rgba(255,255,255,0.3);">
                                    <div id="tierProgress"
                                        class="progress-bar"
                                        role="progressbar"
                                        style="
                                            width: {{ $progress }}%;
                                            background: rgba(0,0,0,0.35);
                                            border-radius: 20px;
                                        ">
                                    </div>
                                </div>

                                {{-- Progress Text --}}
                                <div class="small fw-bold"
                                    id="tierNote"
                                    style="text-shadow: 0 1px 2px rgba(0,0,0,0.35);">
                                    @if($nextTierData)
                                        Còn
                                        <strong>{{ number_format($nextTierData['remaining']) }} đ</strong>
                                        để lên hạng
                                        <strong>{{ $nextTierData['next_tier']->name }}</strong>
                                    @else
                                        🎉 Bạn đang ở hạng cao nhất
                                    @endif
                                </div>

                            </div>
                        </div>
                        @endif




                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="ri-user-line me-2" style="color: #6777ef;"></i>Thông tin cá nhân
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="name" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" 
                                           required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" 
                                           required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="phone_number" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Số điện thoại
                                    </label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                           style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="ri-lock-password-line me-2" style="color: #6777ef;"></i>Đổi mật khẩu
                            </h5>
                            <p class="text-muted mb-4" style="font-size: 14px; margin-bottom: 20px;">
                                <i class="ri-information-line me-1"></i>Để trống nếu không muốn đổi mật khẩu
                            </p>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="current_password" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Mật khẩu hiện tại
                                    </label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password"
                                           style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Mật khẩu mới
                                    </label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password"
                                           style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="password_confirmation" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                        Xác nhận mật khẩu mới
                                    </label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation"
                                           style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4" style="border-top: 2px solid #f0f0f0;">
                            <a href="{{ route('home') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600; margin-right: 10px;">
                                <i class="ri-arrow-left-line me-1"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="ri-save-line me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Nếu là div, thay thế bằng img
                const img = document.createElement('img');
                img.id = 'avatarPreview';
                img.src = e.target.result;
                img.className = 'avatar-preview-large';
                img.onclick = function() { document.getElementById('avatar').click(); };
                preview.parentNode.replaceChild(img, preview);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<script>
    const tiers = @json($allTiers);
    const totalSpent = {{ $user->loyalty->total_spent ?? 0 }};
    const ownedTierId = {{ $currentTier->id }};
    let currentIndex = tiers.findIndex(t => t.id === ownedTierId);

    function renderTier(index) {
        const tier = tiers[index];
        const nextTier = tiers[index + 1] ?? null;

        const card = document.getElementById('tierContent');
        card.style.background = tier.color;
        card.style.color = tier.text_color;

        document.getElementById('tierName').innerHTML =
            `Hạng ${tier.name}` +
            (tier.id === {{ $currentTier->id }}
                ? `<span class="badge bg-dark ms-2">Đang sở hữu</span>`
                : ``);

        document.querySelector('.tier-benefit').innerHTML =
            `🎁 Giảm <strong>${tier.discount_rate}%</strong> cho mọi đơn hàng`;

        document.getElementById('tierCondition').innerText =
            `Tổng chi tiêu từ ${Number(tier.min_spend_required).toLocaleString()} đ`;

        let progress = 100;
        let note = '🎉 Hạng cao nhất';

        if (nextTier) {
            const needed = nextTier.min_spend_required - totalSpent;
            progress = Math.min(
                100,
                Math.max(
                    0,
                    ((totalSpent - tier.min_spend_required) /
                    (nextTier.min_spend_required - tier.min_spend_required)) * 100
                )
            );

            note = needed > 0
                ? `Còn <strong>${needed.toLocaleString()} đ</strong> để lên hạng <strong>${nextTier.name}</strong>`
                : `🎉 Đã đủ điều kiện lên hạng`;
        }

        document.getElementById('tierProgress').style.width = progress + '%';
        document.getElementById('tierNote').innerHTML = note;
    }

    function prevTier() {
        if (currentIndex > 0) {
            currentIndex--;
            renderTier(currentIndex);
        }
    }

    function nextTier() {
        if (currentIndex < tiers.length - 1) {
            currentIndex++;
            renderTier(currentIndex);
        }
    }
</script>

<script>
    const card = document.querySelector('.loyalty-card');
    const bg = card.style.backgroundColor;
    if (bg === '#e5e4e2' || bg === '#ffd700' || bg === '#b9f2ff') {
        card.classList.add('light');
    }
</script>


@endsection

