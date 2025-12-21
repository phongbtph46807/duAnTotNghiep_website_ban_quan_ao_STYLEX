@extends('admin.layouts.app')

@section('title', 'Quản lý tài khoản có quyền')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quản lý tài khoản có quyền</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Quản lý tài khoản có quyền</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Instructions -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info border-0" role="alert">
                <h5 class="alert-heading">
                    <i class="ri-information-line me-2"></i>Hướng dẫn quản lý tài khoản có quyền
                </h5>
                <p class="mb-2">
                    <strong>Quy trình quản lý tài khoản có quyền:</strong>
                </p>
                <ol class="mb-0">
                    <li><strong>Tạo tài khoản:</strong> Nhấn nút "Tạo tài khoản mới" để tạo tài khoản mới và gán vai trò (role), quyền cụ thể</li>
                    <li><strong>Gán vai trò:</strong> Chọn một hoặc nhiều vai trò (role) cho tài khoản trong form tạo mới</li>
                    <li><strong>Phân quyền:</strong> Gán các quyền (permissions) cụ thể cho tài khoản nếu cần</li>
                    <li><strong>Quản lý tài khoản:</strong> Sau khi tạo, bạn có thể chỉnh sửa thông tin và quyền của tài khoản từ trang quản lý người dùng</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Role Statistics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Tổng cộng</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-primary fs-14 mb-0">
                                <i class="ri-user-line fs-13 align-middle"></i>
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <div class="flex-grow-1">
                            <h4 class="fs-22 fw-semibold ff-secondary mb-0">{{ $totalUsers ?? 0 }}</h4>
                        </div>
                    </div>
                    <p class="text-muted mb-0 mt-1">
                        <small>Tài khoản có quyền</small>
                    </p>
                </div>
            </div>
        </div>

        @if(isset($roleStats) && count($roleStats) > 0)
            @foreach($roleStats as $stat)
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        {{ $stat['role']->name }}
                                    </p>
                        </div>
                        <div class="flex-shrink-0">
                                    <h5 class="text-info fs-14 mb-0">
                                        <i class="ri-shield-user-line fs-13 align-middle"></i>
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <div class="flex-grow-1">
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-0">{{ $stat['count'] }}</h4>
                                </div>
                            </div>
                            @if($stat['role']->description)
                                <p class="text-muted mb-0 mt-1">
                                    <small>{{ Str::limit($stat['role']->description, 30) }}</small>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-xl-9 col-md-6">
                <div class="card">
                <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <i class="ri-information-line me-2"></i>
                            Chưa có vai trò nào trong hệ thống. 
                            <a href="{{ route('admin.rbac.roles.create') }}" class="alert-link">Tạo vai trò mới</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Danh sách tài khoản theo từng Role -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                    <h5 class="card-title mb-0">
                        <i class="ri-group-line me-2"></i>Danh sách tài khoản theo từng vai trò
                    </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-success">
                                <i class="ri-add-line me-1"></i> Tạo tài khoản mới
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($usersByRole) && count($usersByRole) > 0)
                    <div class="accordion" id="roleUsersAccordion">
                        @foreach($usersByRole as $roleId => $roleData)
                            @if($roleData['count'] > 0)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $roleId }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $roleId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $roleId }}">
                                        <div class="d-flex align-items-center w-100">
                                            <span class="badge bg-{{ $roleData['role']->color ?? 'secondary' }}-subtle text-{{ $roleData['role']->color ?? 'secondary' }} me-2">{{ $roleData['count'] }}</span>
                                            <span class="badge bg-{{ $roleData['role']->color ?? 'secondary' }}-subtle text-{{ $roleData['role']->color ?? 'secondary' }} me-2">{{ $roleData['role']->name }}</span>
                                            @if($roleData['role']->description)
                                                <small class="text-muted ms-2">- {{ Str::limit($roleData['role']->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $roleId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $roleId }}" data-bs-parent="#roleUsersAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 50px;">STT</th>
                                                        <th>Tên</th>
                                                        <th>Email</th>
                                                        <th>Trạng thái</th>
                                                        <th>Ngày tạo</th>
                                                        <th style="width: 120px;">Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($roleData['users'] as $index => $user)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : \App\Http\Controllers\Admin\UserController::URLIMAGEDEFAULT }}" 
                                                                         alt="{{ $user->name }}" 
                                                                         class="rounded-circle" 
                                                                         style="width: 32px; height: 32px; object-fit: cover;">
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold">{{ $user->name }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'warning' : 'danger') }}-subtle text-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'warning' : 'danger') }}">
                                                                @if($user->status == 'active')
                                                                    Hoạt động
                                                                @elseif($user->status == 'inactive')
                                                                    Chưa kích hoạt
                                                                @else
                                                                    Đã khóa
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="ri-more-fill align-middle"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li>
                                                                        <a href="{{ route('admin.roles.edit', $user->id) }}" class="dropdown-item">
                                                                            <i class="ri-edit-line align-bottom me-2 text-primary"></i> Sửa thông tin
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <button class="dropdown-item change-role" data-user-id="{{ $user->id }}" data-current-role="{{ $user->role }}">
                                                                            <i class="ri-settings-3-line align-bottom me-2 text-muted"></i> Thay đổi quyền
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                                            @else
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="ri-inbox-line" style="font-size: 64px; opacity: 0.3;"></i>
                            <p class="mt-3 mb-0">Chưa có tài khoản nào</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1" aria-labelledby="changeRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeRoleModalLabel">Thay đổi quyền</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changeRoleForm">
                    <input type="hidden" id="userId" name="user_id">
                    <div class="mb-3">
                        <label for="newRole" class="form-label">Chọn quyền mới</label>
                        <select class="form-select" id="newRole" name="role" required>
                            <option value="1">Admin</option>
                            <option value="2">Staff</option>
                            <option value="3">Warehouse Manager</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmRoleChange">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Change role functionality
    $('.change-role').on('click', function() {
        const userId = $(this).data('user-id');
        const currentRole = $(this).data('current-role');
        
        $('#userId').val(userId);
        $('#newRole').val(currentRole);
        $('#changeRoleModal').modal('show');
    });

    $('#confirmRoleChange').on('click', function() {
        const userId = $('#userId').val();
        const newRole = $('#newRole').val();
        
        $.ajax({
            url: `/admin/roles/${userId}/update-role`,
            method: 'POST',
            data: {
                role: newRole,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#changeRoleModal').modal('hide');
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + response.message);
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Có lỗi xảy ra, vui lòng thử lại sau';
                alert(errorMsg);
            }
        });
    });
});
</script>
@endpush

@endsection

