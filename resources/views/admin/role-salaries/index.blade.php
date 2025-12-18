@extends('admin.layouts.app')
@section('title', 'Quản lý lương theo role')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Quản lý lương theo role</h4>
                    <a href="{{ route('admin.role-salaries.create') }}" class="btn btn-primary">Thêm lương role</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Lương cơ bản</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roleSalaries as $roleSalary)
                                <tr>
                                    <td>
                                        @if($roleSalary->role == 1) Admin
                                        @elseif($roleSalary->role == 2) Staff
                                        @elseif($roleSalary->role == 3) Warehouse Manager
                                        @endif
                                    </td>
                                    <td>{{ number_format($roleSalary->base_salary) }} VND</td>
                                    <td>
                                        <a href="{{ route('admin.role-salaries.edit', $roleSalary->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteRoleSalary({{ $roleSalary->id }})">Xóa</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteRoleSalary(id) {
    if(confirm('Xác nhận xóa lương role?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("/admin/role-salaries") }}/' + id;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection