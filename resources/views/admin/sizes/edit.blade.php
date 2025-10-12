@extends('layouts.admin-layout')

@section('content')
<div class="container">
    <h2>Sửa kích thước: {{ $size->name }}</h2>
    <form action="{{ route('admin.sizes.update', $size) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Tên kích thước</label>
            <input type="text" name="name" class="form-control" value="{{ $size->name }}" required>
        </div>
        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.sizes.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
