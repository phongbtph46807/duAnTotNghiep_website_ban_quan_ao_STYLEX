@extends('admin.layouts.app')
@section('body-class', '')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-5 col-lg-4">
        <div class="p-4 shadow-sm" style=" max-width: 550px; background: #fff; border: 1px solid #e5e5e5; border-radius: 10px;">
            <h4 class="text-center mb-4" style="font-weight: 600; color: #333;">ĐĂNG KÝ</h4>

            @if(Session::has('success'))
            <script>
                window.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: "{{ Session::get('success') }}",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
            </script>
            @endif
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        value="{{ old('name') }}" placeholder="Nhập tên của bạn" autofocus>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">
                        Số điện thoại
                        <i class="fa fa-question-circle" data-bs-toggle="tooltip" title="Nhập số mà có số 0 ở đầu. Ví dụ: 012345678"></i>
                    </label>

                    <input id="phone"
                        type="text"
                        class="form-control @error('phone_number') is-invalid @enderror"
                        name="phone_number"
                        value="{{ old('phone_number') }}"  placeholder="Nhập số điện thoại">

                    @error('phone_number')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                    @enderror
                </div>



                <!-- Mật khẩu -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Nhập mật khẩu">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#password">
                            <i class="fa fa-eye"></i>
                        </button>

                    </div>
                    @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Xác nhận mật khẩu -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <input id="password_confirmation" type="password" class="form-control"
                            name="password_confirmation" placeholder="Xác nhận mật khẩu">
                        <button type="button" class="btn btn-outline-secondary toggle-password"
                            data-target="#password_confirmation">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn w-100 py-2 btn btn-primary"
                    style="background-color: #111; color: #fff; border-radius: 6px; font-weight: 500; letter-spacing: 0.5px;">
                    Đăng ký
                </button>

                <p class="text-center mt-3" style="font-size: 14px; color: #666;"> Đã có tài khoản? <a href="{{ route('login') }}"
                        style="color: #111; font-weight: 500;">Đăng nhập</a> </p>
            </form>
        </div>
    </div>
</div>
@endsection