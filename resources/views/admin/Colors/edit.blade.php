@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Sửa Màu: {{ $color->name }}</h2>
    <form action="{{ route('admin.colors.update', $color) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Tên màu</label>
            <input type="text" name="name" class="form-control" value="{{ $color->name }}" required>
        </div>
        <div class="mb-3">
            <label>Mã HEX_code</label>
            <input type="text" name="hex" class="form-control" value="{{ $color->hex }}">
        </div>
        
        <button class="btn btn-success">Cập nhật</button>
    </form>
</div>
@endsection
