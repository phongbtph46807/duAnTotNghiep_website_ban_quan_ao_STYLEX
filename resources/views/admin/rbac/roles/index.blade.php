@extends('admin.layouts.app')
@section('title', 'Quản lý Role')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách Role</h4>
    <a href="{{ route('admin.rbac.roles.create') }}" class="btn btn-primary">Tạo Role</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          @foreach($roles as $role)
          @php
            $isAdmin = strtolower($role->name) === 'admin';
            $userCount = $roleUserCounts[$role->id] ?? 0;
            $canDelete = !$isAdmin && $userCount == 0;
          @endphp
          <tr>
            <td>{{ $role->id }}</td>
            <td>
              <div>
                <span class="badge bg-{{ $role->color ?? 'secondary' }}-subtle text-{{ $role->color ?? 'secondary' }} me-2">
                  {{ $role->name }}
                </span>
                @if($isAdmin)
                  <span class="badge bg-danger ms-2">Bắt buộc</span>
                @endif
              </div>
              <small class="text-muted">({{ $userCount }} tài khoản)</small>
            </td>
            <td>{{ $role->description }}</td>
            <td>
              <div class="d-flex gap-2 align-items-center">
              <a class="btn btn-sm btn-warning" href="{{ route('admin.rbac.roles.edit', $role) }}">Sửa</a>
                @if($canDelete)
              <form class="d-inline" method="POST" action="{{ route('admin.rbac.roles.destroy', $role) }}">
                @csrf
                @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa role này?')">Xóa</button>
              </form>
                @else
                  <button class="btn btn-sm btn-danger" disabled title="{{ $isAdmin ? 'Không thể xóa role Admin' : 'Role đang được sử dụng bởi ' . $userCount . ' tài khoản' }}">
                    Xóa
                  </button>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div>
        {{ $roles->links() }}
      </div>
    </div>
  </div>
</div>
@endsection


