@extends('admin.layouts.app')
@section('title', 'Thêm lương role')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thêm lương role</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.role-salaries.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                    <option value="">-- Chọn role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role['id'] }}" {{ old('role') == $role['id'] ? 'selected' : '' }}>
                                            {{ $role['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lương cơ bản</label>
                                <input type="number" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror" 
                                       value="{{ old('base_salary') }}" min="0" required>
                                @error('base_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Thêm</button>
                                <a href="{{ route('admin.role-salaries.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection