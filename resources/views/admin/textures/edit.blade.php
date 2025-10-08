@extends('layouts.admin-layout')

@section('content')
<div class="container">
    <h2>Sửa chất liệu: {{ $texture->name }}</h2>
    <form action="{{ route('admin.textures.update', $texture) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Tên chất liệu</label>
            <input type="text" name="name" class="form-control" value="{{ $texture->name }}" required>
        </div>
        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.textures.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
