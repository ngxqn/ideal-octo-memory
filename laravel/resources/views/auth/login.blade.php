<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập - Morico Bakery</title>
    
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/user/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/user/auth.css') }}">
</head>

<body class="auth-standalone">

    <div class="login-card d-flex flex-column flex-md-row mx-auto">
        <!-- Image Side -->
        <div class="image-section d-flex align-items-center justify-content-center p-4" style="flex: 1;">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/image/common/morico-black-emblem.png') }}" alt="MORICO" class="logo-img">
            </a>
        </div>

        <!-- Login Form Side -->
        <div class="login-section d-flex flex-column justify-content-center p-4 p-md-5" style="flex: 1;">
            <h1 class="page-title">Đăng nhập</h1>
            
            <form id="loginForm" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label fw-bold" style="color: var(--primary-color);">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="username" value="{{ old('username') }}" placeholder="Tên đăng nhập" required autofocus>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold" style="color: var(--primary-color);">Mật khẩu</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Nhập mật khẩu" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-2">
                    <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                    <label class="form-check-label fw-normal" for="rememberMe" style="color: var(--primary-color);">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="btn btn-morico w-100 py-2">Đăng nhập</button>
            </form>

            <div class="link text-center mt-4" style="font-size: 1rem; color: var(--primary-color);">
                Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
            </div>
            
            <div class="text-center mt-2">
                <a href="{{ route('home') }}" style="color: grey; font-size: 0.9rem;">Quay về trang chủ</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS bundle (not strictly needed here but kept for consistency) -->
    <script src="{{ asset('assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>
