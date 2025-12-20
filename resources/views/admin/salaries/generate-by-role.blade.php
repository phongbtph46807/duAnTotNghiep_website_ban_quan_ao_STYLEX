@extends('admin.layouts.app')

@section('title', 'Tạo lương theo role')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tạo lương tự động theo role</h1>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.salaries.generate-by-role') }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Chọn role</label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror" >
                            <option value="">-- Chọn role --</option>
                            @foreach ($roles  as $rs)
                                <option value="{{ $rs['id'] }}" {{ old('role') == $rs['id'] ? 'selected' : '' }}>
                                    {{ $rs['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tháng</label>
                        <select name="month" class="form-control @error('month') is-invalid @enderror" >
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', $month) == $m ? 'selected' : '' }}>
                                    Tháng {{ $m }}
                                </option>
                            @endfor
                        </select>
                        @error('month') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Năm</label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror"
                               value="{{ old('year', $year) }}" >
                        @error('year') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Lương cơ bản (theo cài đặt role)</label>
                    <input type="number" name="base_salary" class="form-control" value="Nhập lương cơ bảnVND" >
                </div>

                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    <strong>Lưu ý:</strong> Tạo lương tự động sẽ tạo lương cho tất cả nhân viên có role đã chọn với lương cơ bản theo cài đặt role
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Tạo lương</button>
                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
