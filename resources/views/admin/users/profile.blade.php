@extends('admin.layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Thông tin cá nhân</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : \App\Http\Controllers\Admin\UserController::URLIMAGEDEFAULT }}" 
                                 alt="Avatar" class="rounded-circle" width="150" height="150">
                        </div>
                        <h5>{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                        @if($user->role == 1)
                            <span class="badge bg-danger">Admin</span>
                        @elseif($user->role == 2)
                            <span class="badge bg-warning">Staff</span>
                        @else
                            <span class="badge bg-info">User</span>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số điện thoại:</strong></td>
                                    <td>{{ $user->phone_number ?? 'Chưa cập nhật' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Vai trò:</strong></td>
                                    <td>
                                        @if($user->role == 1)
                                            <span class="badge bg-danger">Admin</span>
                                        @elseif($user->role == 2)
                                            <span class="badge bg-warning">Staff</span>
                                        @else
                                            <span class="badge bg-info">User</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        @if($user->status == 'active')
                                            <span class="badge bg-success">Hoạt động</span>
                                        @elseif($user->status == 'inactive')
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Bị khóa</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật lần cuối:</strong></td>
                                    <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                                <i class="ri-edit-line me-1"></i> Chỉnh sửa thông tin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
