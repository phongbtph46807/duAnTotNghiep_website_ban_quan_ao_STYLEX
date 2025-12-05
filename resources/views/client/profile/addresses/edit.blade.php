@extends('client.layouts.app')

@section('title', 'Sửa địa chỉ - ' . env('APP_NAME'))

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
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="profile-avatar">
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
                        <a href="{{ route('client.order.list') }}" class="settings-menu-item-sidebar" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
                            <i class="ri-shopping-bag-line" style="font-size: 20px; color: #6777ef; margin-right: 12px; width: 24px;"></i>
                            <span style="font-size: 14px; font-weight: 500;">Đơn hàng của tôi</span>
                        </a>
                        <a href="{{ route('client.profile.addresses.index') }}" class="settings-menu-item-sidebar active" style="display: flex; align-items: center; padding: 12px; text-decoration: none; color: #333; transition: all 0.3s; border-radius: 8px; margin-bottom: 4px;">
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
                        <i class="ri-edit-line me-2" style="color: #6777ef;"></i>Sửa địa chỉ
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

                    <form action="{{ route('client.profile.addresses.update', $address) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="full_name" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Họ và tên người nhận <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                       id="full_name" name="full_name" value="{{ old('full_name', $address->full_name) }}" 
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
                                       id="phone" name="phone" value="{{ old('phone', $address->phone) }}" 
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
                                       id="email" name="email" value="{{ old('email', $address->email) }}"
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
                                    <option value="home" {{ old('address_type', $address->address_type) == 'home' ? 'selected' : '' }}>Nhà riêng</option>
                                    <option value="office" {{ old('address_type', $address->address_type) == 'office' ? 'selected' : '' }}>Văn phòng</option>
                                    <option value="other" {{ old('address_type', $address->address_type) == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('address_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="city" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Tỉnh/Thành phố <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', $address->city) }}" 
                                       required style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="district" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Quận/Huyện
                                </label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                       id="district" name="district" value="{{ old('district', $address->district) }}"
                                       style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="ward" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    Phường/Xã
                                </label>
                                <input type="text" class="form-control @error('ward') is-invalid @enderror" 
                                       id="ward" name="ward" value="{{ old('ward', $address->ward) }}"
                                       style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
                                @error('ward')
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
                                          style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">{{ old('address', $address->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1"
                                           {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default" style="font-weight: 500;">
                                        Đặt làm địa chỉ mặc định
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4" style="border-top: 2px solid #f0f0f0;">
                            <a href="{{ route('client.profile.addresses.index') }}" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 24px; font-weight: 600;">
                                <i class="ri-arrow-left-line me-1"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="ri-save-line me-1"></i> Cập nhật địa chỉ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

