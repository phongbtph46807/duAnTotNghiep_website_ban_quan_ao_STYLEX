@extends('auth.layout')

@section('title', 'Đăng nhập')
@section('page-title', 'Đăng nhập')
@section('page-subtitle', 'Chào mừng bạn đến với STYLEX')

@section('content')
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

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">
                Ghi nhớ đăng nhập
            </label>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
                Đăng nhập
            </button>
        </div>

        {{-- Divider --}}
        <div class="text-center my-3">
            <span class="text-muted">hoặc</span>
        </div>

        {{-- Google Login Button --}}
        <div class="d-grid">
            <a href="{{ route('google.login') }}" class="btn btn-outline-danger">
                <svg width="18" height="18" viewBox="0 0 18 18" class="me-2">
                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.964-2.184l-2.908-2.258c-.806.54-1.837.86-3.056.86-2.35 0-4.34-1.587-5.052-3.72H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                    <path fill="#FBBC05" d="M3.948 10.718c-.18-.54-.282-1.117-.282-1.718s.102-1.178.282-1.718V4.95H.957C.348 6.173 0 7.55 0 9s.348 2.827.957 4.05l2.991-2.332z"/>
                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.95L3.948 7.282C4.66 5.147 6.65 3.58 9 3.58z"/>
                </svg>
                Đăng nhập bằng Google
            </a>
        </div>
    </form>
@endsection

@section('footer')
    <p class="mb-0">Chưa có tài khoản? <a href="{{ route('registerView') }}">Đăng ký ngay</a></p>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-target'));
            const icon = this.querySelector('i');
            
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
@endpush