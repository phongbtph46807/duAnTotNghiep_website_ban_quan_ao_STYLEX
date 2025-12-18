@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-plus"></i> Tạo Lương Mới</h4>
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
            <h6 class="mb-0">Thông Tin Lương</h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.salaries.store') }}">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nhân Viên <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Chọn Nhân Viên --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tháng <span class="text-danger">*</span></label>
                        <select name="month" class="form-control @error('month') is-invalid @enderror" required>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', $month) == $m ? 'selected' : '' }}>
                                    Tháng {{ $m }}
                                </option>
                            @endfor
                        </select>
                        @error('month') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Năm <span class="text-danger">*</span></label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                               value="{{ old('year', $year) }}" min="2020" max="2050" required>
                        @error('year') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Lương Cơ Bản <span class="text-danger">*</span></label>
                        <input type="number" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror"
                               value="{{ old('base_salary', 0) }}" min="0" step="1000" required>
                        @error('base_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Thưởng</label>
                        <input type="number" name="bonus" class="form-control @error('bonus') is-invalid @enderror"
                               value="{{ old('bonus', 0) }}" min="0" step="1000">
                        @error('bonus') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Khấu Trừ</label>
                        <input type="number" name="deduction" class="form-control @error('deduction') is-invalid @enderror"
                               value="{{ old('deduction', 0) }}" min="0" step="1000">
                        @error('deduction') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi Chú</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                              placeholder="Nhập ghi chú về lương (tùy chọn)">{{ old('notes') }}</textarea>
                    @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="alert alert-info">
                    <i class="bx bx-info-circle"></i>
                    <strong>Lưu ý:</strong> Lương sẽ được tạo với trạng thái "pending" (Chờ duyệt) và cần được Admin phê duyệt.
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> Tạo Lương
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