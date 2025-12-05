@extends('admin.layouts.app')

@section('title', 'Tạo tài khoản có quyền')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Tạo tài khoản có quyền</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Quản lý tài khoản có quyền</a></li>
                        <li class="breadcrumb-item active">Tạo mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form tạo tài khoản -->
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

                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                    @if($roles && $roles->count() > 0)
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($roles as $role)
                                                <div class="form-check">
                                                    <input class="form-check-input @error('role_ids') is-invalid @enderror" 
                                                           type="checkbox" 
                                                           name="role_ids[]" 
                                                           id="role_{{ $role->id }}" 
                                                           value="{{ $role->id }}"
                                                           {{ in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
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
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input @error('status') is-invalid @enderror" 
                                                   type="radio" name="status" id="status_active" value="active" 
                                                   {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label text-success" for="status_active">
                                                <i class="ri-checkbox-circle-line me-1"></i>Hoạt động
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input @error('status') is-invalid @enderror" 
                                                   type="radio" name="status" id="status_inactive" value="inactive" 
                                                   {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                            <label class="form-check-label text-warning" for="status_inactive">
                                                <i class="ri-pause-circle-line me-1"></i>Tạm dừng
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input @error('status') is-invalid @enderror" 
                                                   type="radio" name="status" id="status_blocked" value="blocked" 
                                                   {{ old('status') == 'blocked' ? 'checked' : '' }}>
                                            <label class="form-check-label text-danger" for="status_blocked">
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
                                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
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
                                        <i class="ri-save-line me-1"></i>Tạo tài khoản
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

    <!-- Danh sách các tài khoản hiện có -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-user-list-line me-2"></i>Danh sách tài khoản có quyền hiện có
                    </h4>
                </div>
                <div class="card-body">
                    @if($existingUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-nowrap align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 25%;">Họ và tên</th>
                                        <th style="width: 25%;">Email</th>
                                        <th style="width: 15%;">Vai trò</th>
                                        <th style="width: 15%;">Trạng thái</th>
                                        <th style="width: 15%;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($existingUsers as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $user->name }}</strong>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->role == 1)
                                                    <span class="badge bg-danger">
                                                        <i class="ri-admin-line me-1"></i>Admin
                                                    </span>
                                                @elseif($user->role == 2)
                                                    <span class="badge bg-warning">
                                                        <i class="ri-team-line me-1"></i>Staff
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->status == 'active')
                                                    <span class="badge bg-success">
                                                        <i class="ri-checkbox-circle-line me-1"></i>Hoạt động
                                                    </span>
                                                @elseif($user->status == 'inactive')
                                                    <span class="badge bg-warning">
                                                        <i class="ri-pause-circle-line me-1"></i>Tạm dừng
                                                    </span>
                                                @elseif($user->status == 'blocked')
                                                    <span class="badge bg-danger">
                                                        <i class="ri-lock-line me-1"></i>Bị khóa
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.roles.edit', $user->id) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="Chỉnh sửa">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="ri-information-line me-2"></i>Chưa có tài khoản có quyền nào trong hệ thống.
                        </div>
                    @endif
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