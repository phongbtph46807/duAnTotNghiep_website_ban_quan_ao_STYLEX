@extends('layouts.admin-layout')

@section('content')
<div class="container">
    <h2>Thêm Chất liệu mới</h2>
    <form action="{{ route('admin.textures.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tên chất liệu</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.textures.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
