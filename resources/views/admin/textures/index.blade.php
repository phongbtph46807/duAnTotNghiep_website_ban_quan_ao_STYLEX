@extends('layouts.admin-layout')

@section('content')
<div class="container">
    <h2>Danh sách Chất liệu</h2>
    <a href="{{ route('admin.textures.create') }}" class="btn btn-primary mb-3">+ Thêm chất liệu</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên chất liệu</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($textures as $texture)
            <tr>
                <td>{{ $texture->id }}</td>
                <td>{{ $texture->name }}</td>
                <td>
                    <a href="{{ route('admin.textures.edit', $texture) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.textures.destroy', $texture) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa chất liệu này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $textures->links() }}
</div>
@endsection
