@extends('admin.layouts.app')
@section('title','Sửa hãng vận chuyển')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Sửa hãng vận chuyển #{{ $shipping_carrier->id }}</h4>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.shipping_carriers.update', $shipping_carrier) }}" method="post">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Tên hãng vận chuyển <span class="text-danger">*</span></label>
                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $shipping_carrier->name) }}"
                           placeholder="VD: GHTK, GHN, VNPost...">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.shipping_carriers.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
