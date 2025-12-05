@extends('admin.layouts.app')

@section('title', 'Sửa tài khoản có quyền')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Sửa tài khoản có quyền</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Quản lý tài khoản có quyền</a></li>
                        <li class="breadcrumb-item active">Sửa tài khoản</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form sửa tài khoản -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thông tin tài khoản</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.roles.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Để trống nếu không muốn đổi mật khẩu">
                                    <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                    @php
                                        $adminCount = \App\Models\User::where('role', 1)->count();
                                        $isLastAdmin = $user->role == 1 && $adminCount <= 1;
                                    @endphp
                                    @if($isLastAdmin)
                                        <div class="alert alert-warning mb-2">
                                            <i class="ri-information-line me-2"></i>
                                            <strong>Lưu ý:</strong> Đây là admin cuối cùng, không thể thay đổi vai trò!
                                        </div>
                                    @endif
                                    @if($roles && $roles->count() > 0)
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($roles as $role)
                                                <div class="form-check">
                                                    <input class="form-check-input @error('role_ids') is-invalid @enderror" 
                                                           type="checkbox" 
                                                           name="role_ids[]" 
                                                           id="role_{{ $role->id }}" 
                                                           value="{{ $role->id }}"
                                                           {{ in_array($role->id, old('role_ids', $userRoles ?? [])) ? 'checked' : '' }}
                                                           {{ $isLastAdmin && strtolower($role->name) === 'admin' ? '' : ($isLastAdmin ? 'disabled' : '') }}>
                                                    <label class="form-check-label {{ $isLastAdmin && strtolower($role->name) !== 'admin' ? 'text-muted' : '' }}" for="role_{{ $role->id }}">
                                                        <i class="ri-shield-user-line me-1"></i>{{ $role->name }}
                                                        @if($role->description)
                                                            <small class="text-muted d-block">{{ $role->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text">Có thể chọn nhiều vai trò cho tài khoản này</div>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            <i class="ri-alert-line me-2"></i>
                                            Chưa có vai trò nào trong hệ thống. 
                                            <a href="{{ route('admin.rbac.roles.create') }}" class="alert-link">Tạo vai trò mới</a>
                                        </div>
                                    @endif
                                    @error('role_ids')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    @if($isLastAdmin)
                                        <div class="alert alert-warning mb-2">
                                            <i class="ri-information-line me-2"></i>
                                            <strong>Lưu ý:</strong> Admin cuối cùng phải ở trạng thái hoạt động!
                                        </div>
                                    @endif
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input @error('status') is-invalid @enderror" 
                                                   type="radio" name="status" id="status_active" value="active" 
                                                   {{ old('status', $user->status) == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label text-success" for="status_active">
                                                <i class="ri-checkbox-circle-line me-1"></i>Hoạt động
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input @error('status') is-invalid @enderror" 
                                                   type="radio" name="status" id="status_inactive" value="inactive" 
                                                   {{ old('status', $user->status) == 'inactive' ? 'checked' : '' }}
                                                   {{ $isLastAdmin ? 'disabled' : '' }}>
                                            <label class="form-check-label text-warning {{ $isLastAdmin ? 'text-muted' : '' }}" for="status_inactive">
                                                <i class="ri-pause-circle-line me-1"></i>Tạm dừng
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input @error('status') is-invalid @enderror" 
                                                   type="radio" name="status" id="status_blocked" value="blocked" 
                                                   {{ old('status', $user->status) == 'blocked' ? 'checked' : '' }}
                                                   {{ $isLastAdmin ? 'disabled' : '' }}>
                                            <label class="form-check-label text-danger {{ $isLastAdmin ? 'text-muted' : '' }}" for="status_blocked">
                                                <i class="ri-lock-line me-1"></i>Bị khóa
                                            </label>
                                        </div>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Phần chọn quyền -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="ri-shield-keyhole-line me-1"></i>Phân quyền <span class="text-muted">(Tùy chọn)</span>
                                    </label>
                                    <div class="alert alert-info mb-3">
                                        <i class="ri-information-line me-2"></i>
                                        <small>Chọn các quyền cụ thể cho tài khoản này. Nếu không chọn, tài khoản sẽ chỉ có quyền mặc định theo vai trò.</small>
                                    </div>
                                    @if($permissions && $permissions->count() > 0)
                                        <div class="row">
                                            @foreach($permissions as $permission)
                                                <div class="col-md-4 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               id="permission_{{ $permission->id }}" 
                                                               value="{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', $userPermissions ?? [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                            @if($permission->description)
                                                                <small class="text-muted d-block">{{ $permission->description }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermissions">
                                                <i class="ri-checkbox-multiple-line me-1"></i>Chọn tất cả
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllPermissions">
                                                <i class="ri-checkbox-blank-line me-1"></i>Bỏ chọn tất cả
                                            </button>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            <i class="ri-alert-line me-2"></i>
                                            Chưa có quyền nào trong hệ thống. Vui lòng tạo quyền trước.
                                        </div>
                                    @endif
                                    @error('permissions')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>Cập nhật tài khoản
                                    </button>
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line me-1"></i>Quay lại
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chọn tất cả permissions
    const selectAllBtn = document.getElementById('selectAllPermissions');
    const deselectAllBtn = document.getElementById('deselectAllPermissions');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
    }
    
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    }
});
</script>
@endpush

@endsection
