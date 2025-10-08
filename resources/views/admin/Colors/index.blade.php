@extends('layouts.admin-layout')

@section('content')
<div class="container">
    <h2>Danh sách Màu sắc</h2>
    <a href="{{ route('admin.colors.create') }}" class="btn btn-primary mb-3">+ Thêm Màu</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên màu</th>
                <th>Mã Hex</th>
                
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colors as $color)
            <tr>
                <td>{{ $color->id }}</td>
                <td>{{ $color->name }}</td>
                <td><span style="background:{{ $color->hex }}; padding:5px 20px; border:1px solid #ccc;"></span> {{ $color->hex }}</td>
                
                <td>
                    <a href="{{ route('admin.colors.edit', $color) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.colors.destroy', $color) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa màu này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $colors->links() }}
</div>
@endsection
