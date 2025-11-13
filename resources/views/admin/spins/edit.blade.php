@extends('admin.layouts.app')
@section('title', 'Cập nhật phần thưởng Spin')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Cập nhật phần thưởng Spin</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.spins.index') }}">Quản lí Spin</a></li>
                    <li class="breadcrumb-item active">Cập nhật</li>
                </ol>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.spins.update', $spin->id) }}">
        @csrf
        @method('PUT')
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Thông tin phần thưởng</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên phần thưởng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name', $spin->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Voucher</label>
                        <select class="form-select @error('voucher_id') is-invalid @enderror" name="voucher_id">
                            <option value="">-- Không có --</option>
                            @foreach ($vouchers as $voucher)
                                <option value="{{ $voucher->id }}"
                                    {{ old('voucher_id', $spin->voucher_id) == $voucher->id ? 'selected' : '' }}>
                                    {{ $voucher->code }} - {{ $voucher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('voucher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Xác suất trúng (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('probability') is-invalid @enderror"
                               name="probability" value="{{ old('probability', $spin->probability) }}" min="1" max="100" required>
                        @error('probability')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label d-block">Trạng thái</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $spin->is_active) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label">Hoạt động</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <a href="{{ route('admin.spins.index') }}" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </div>
    </form>
@endsection
