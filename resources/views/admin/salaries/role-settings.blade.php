@extends('admin.layouts.app')

@section('title', 'Cài đặt lương theo role')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cài đặt lương theo role/Vải trò</h1>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách lương theo role</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Role ID</th>
                            <th>Lương cơ bản (VND)</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roleSalaries as $rs)
                            <tr>
                                <td>{{ $rs->role }}</td>
                                <td>{{ number_format($rs->base_salary) }} VND</td>
                                <td>
                                    <a href="{{ route('admin.salaries.edit', $rs->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
