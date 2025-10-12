@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Thêm Màu Mới</h2>
    <form action="{{ route('admin.colors.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tên màu</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Mã HEX</label>
            <input type="text" name="hex" class="form-control">
        </div>
       
        <button class="btn btn-success">Lưu</button>
    </form>
</div>
@endsection
