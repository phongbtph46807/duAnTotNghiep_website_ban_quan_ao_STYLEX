@extends('layouts.layout')

@section('content')

<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-5 col-lg-4">
        <div class="p-4 shadow-sm" style=" max-width: 550px; background: #fff; border: 1px solid #e5e5e5; border-radius: 10px;">
            <h4 class="text-center mb-4" style="font-weight: 600; color: #333;">ĐĂNG NHẬP</h4>

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

            @if(Session::has('error'))
            <script>
                window.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: "{{ Session::get('error') }}",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                });
            </script>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf

                

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" autofocus>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
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


                <button type="submit" class="btn w-100 py-2 btn btn-primary"
                    style="background-color: #111; color: #fff; border-radius: 6px; font-weight: 500; letter-spacing: 0.5px;">
                    Đăng nhập
                </button>

                <p class="text-center mt-3" style="font-size: 14px; color: #666;"> Chưa có tài khoản? <a href="{{ route('register') }}"
                        style="color: #111; font-weight: 500;">Đăng kí</a> </p>
            </form>
        </div>
    </div>
</div>
@endsection