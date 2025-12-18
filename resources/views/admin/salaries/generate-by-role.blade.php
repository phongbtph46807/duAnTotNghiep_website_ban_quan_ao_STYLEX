@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="mb-0"><i class="bx bx-cog"></i> Tạo Lương Tự Động Theo Role</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Quay Lại
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-x-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex gap-2">
            <i class="bx bx-info-circle fs-5 mt-1"></i>
            <div>
                <strong>Hướng dẫn:</strong>
                <ul class="mb-0 mt-2 ps-3 small">
                    <li>Chọn role để tạo lương tự động cho tất cả nhân viên thuộc role đó</li>
                    <li>Lương cơ bản sẽ được lấy từ cấu hình role salary</li>
                    <li>Nếu chưa cấu hình role salary, vui lòng vào <a href="{{ route('admin.role-salaries.index') }}">Quản lý lương role</a></li>
                    <li>Hệ thống sẽ tạo mới hoặc cập nhật lương cho kỳ đã chọn</li>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Thông Tin Tạo Lương</h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.salaries.store-generate-by-role') }}">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Chọn Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Chọn Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role['id'] }}" {{ old('role') == $role['id'] ? 'selected' : '' }}>
                                    {{ $role['name'] }} 
                                    @if($role['salary'] > 0)
                                        ({{ number_format($role['salary']) }} VND)
                                    @else
                                        (Chưa cấu hình)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
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

                    <div class="col-md-4">
                        <label class="form-label">Năm <span class="text-danger">*</span></label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                               value="{{ old('year', $year) }}" min="2020" max="2050" required>
                        @error('year') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bx bx-info-circle"></i>
                    <strong>Lưu ý:</strong> 
                    <ul class="mb-0 mt-2">
                        <li>Tạo lương tự động sẽ tạo lương cho tất cả nhân viên có role đã chọn</li>
                        <li>Lương cơ bản sẽ được lấy từ cấu hình role salary</li>
                        <li>Thưởng và khấu trừ sẽ được đặt về 0, có thể chỉnh sửa sau</li>
                        <li>Trạng thái mặc định: pending (Chờ duyệt)</li>
                    </ul>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> Tạo Lương Tự Động
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