@extends('admin.layouts.app')

@section('title', 'Sửa lương role')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa lương cho role: {{ $roleSalary->role }}</h1>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.salaries.update', $roleSalary->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Lương cơ bản (VND)</label>
                    <input type="number" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror"
                           value="{{ old('base_salary', $roleSalary->base_salary) }}" required>
                    @error('base_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
