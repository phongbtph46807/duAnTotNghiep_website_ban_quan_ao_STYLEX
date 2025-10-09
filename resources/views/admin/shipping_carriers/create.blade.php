@extends('admin.layouts.app')
@section('title','Thêm hãng vận chuyển')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Thêm hãng vận chuyển</h4>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.shipping_carriers.store') }}" method="post">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tên hãng vận chuyển <span class="text-danger">*</span></label>
                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="VD: GHTK, GHN, VNPost...">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.shipping_carriers.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
