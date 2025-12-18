@extends('admin.layouts.app')
@section('title', 'Sửa lương role')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sửa lương role</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.role-salaries.update', $roleSalary->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="
                                    @if($roleSalary->role == 1) Admin
                                    @elseif($roleSalary->role == 2) Staff  
                                    @elseif($roleSalary->role == 3) Warehouse Manager
                                    @endif
                                " readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lương cơ bản</label>
                                <input type="number" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror" 
                                       value="{{ old('base_salary', $roleSalary->base_salary) }}" min="0" required>
                                @error('base_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
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