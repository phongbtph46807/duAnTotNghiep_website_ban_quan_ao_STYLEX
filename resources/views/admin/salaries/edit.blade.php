@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-edit"></i> Chỉnh Sửa Lương</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Quay Lại
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Thông Tin Lương - {{ $salary->user->name }}</h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.salaries.update', $salary) }}">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nhân Viên</label>
                        <input type="text" class="form-control" value="{{ $salary->user->name }} ({{ $salary->user->email }})" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tháng</label>
                        <input type="text" class="form-control" value="Tháng {{ $salary->month }}" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Năm</label>
                        <input type="text" class="form-control" value="{{ $salary->year }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Lương Cơ Bản <span class="text-danger">*</span></label>
                        <input type="number" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror"
                               value="{{ old('base_salary', $salary->base_salary) }}" min="0" step="1000" required>
                        @error('base_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Thưởng</label>
                        <input type="number" name="bonus" class="form-control @error('bonus') is-invalid @enderror"
                               value="{{ old('bonus', $salary->bonus) }}" min="0" step="1000">
                        @error('bonus') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Khấu Trừ</label>
                        <input type="number" name="deduction" class="form-control @error('deduction') is-invalid @enderror"
                               value="{{ old('deduction', $salary->deduction) }}" min="0" step="1000">
                        @error('deduction') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi Chú</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                              placeholder="Nhập ghi chú về lương (tùy chọn)">{{ old('notes', $salary->notes) }}</textarea>
                    @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="alert alert-warning">
                    <i class="bx bx-info-circle"></i>
                    <strong>Lưu ý:</strong> Chỉ có thể chỉnh sửa lương ở trạng thái "pending" (Chờ duyệt).
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> Cập Nhật
                    </button>
                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
                        <i class="bx bx-x"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection