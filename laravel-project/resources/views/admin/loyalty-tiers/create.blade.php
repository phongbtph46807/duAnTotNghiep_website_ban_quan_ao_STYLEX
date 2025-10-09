@extends('layouts.admin-layout')

@section('title', 'Tạo Hạng thành viên mới')

@section('content')
    <h2>Tạo Hạng thành viên mới</h2>
    <hr>

    <form action="{{ route('loyalty-tiers.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Tên Hạng</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="min_spend_required" class="form-label">Ngưỡng Chi tiêu Tối thiểu (VNĐ)</label>
            <input type="number" name="min_spend_required" id="min_spend_required" class="form-control @error('min_spend_required') is-invalid @enderror" value="{{ old('min_spend_required') }}" required>
            @error('min_spend_required')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="discount_rate" class="form-label">Tỷ lệ Giảm giá (%)</label>
            <input type="number" step="0.01" name="discount_rate" id="discount_rate" class="form-control @error('discount_rate') is-invalid @enderror" value="{{ old('discount_rate') }}" required>
            @error('discount_rate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Tạo Hạng</button>
        <a href="{{ route('loyalty-tiers.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection
