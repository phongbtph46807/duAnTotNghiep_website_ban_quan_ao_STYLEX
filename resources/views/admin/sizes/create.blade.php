@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Thêm Kích thước mới</h2>
    <form action="{{ route('admin.sizes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tên kích thước</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.sizes.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
