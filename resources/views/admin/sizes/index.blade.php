@extends('layouts.admin-layout')

@section('content')
<div class="container">
    <h2>Danh sách Kích thước</h2>
    <a href="{{ route('admin.sizes.create') }}" class="btn btn-primary mb-3">+ Thêm kích thước</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên kích thước</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sizes as $size)
            <tr>
                <td>{{ $size->id }}</td>
                <td>{{ $size->name }}</td>
                <td>
                    <a href="{{ route('admin.sizes.edit', $size) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.sizes.destroy', $size) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa kích thước này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $sizes->links() }}
</div>
@endsection
