<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Morico Bakery</title>

    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/user/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/user/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-free-7.2.0/css/all.min.css') }}">
    <style>
        .error-text { color: red; font-size: 0.85rem; margin-top: 5px; display: none; }
        .is-invalid-client { border-color: red !important; }
    </style>
</head>

<body class="auth-standalone">
    <div class="login-card d-flex flex-column flex-md-row mx-auto">
        <!-- Image Side -->
        <div class="image-section d-flex align-items-center justify-content-center p-4" style="flex: 1;">
            <a href="{{ route('home') }}"><img src="{{ asset('assets/image/common/morico-black-emblem.png') }}" alt="MORICO" class="logo-img"></a>
        </div>

        <!-- Register Form Side -->
        <div class="login-section d-flex flex-column justify-content-center p-4 p-md-5" style="flex: 1;">
            <h1 class="page-title">Đăng ký thành viên</h1>

            <form id="registerForm" action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="full_name" class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" id="full_name" value="{{ old('full_name') }}" placeholder="Nhập họ và tên" required>
                    <div class="error-text" id="full_name_error">Vui lòng nhập họ tên hợp lệ.</div>
                    @error('full_name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label fw-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="username" value="{{ old('username') }}" placeholder="Nhập tên đăng nhập" required>
                    <div class="error-text" id="username_error">Tên đăng nhập tối thiểu 3 ký tự.</div>
                    @error('username') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại" required>
                    <div class="error-text" id="phone_error">Số điện thoại không hợp lệ (10-11 chữ số).</div>
                    @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" placeholder="Nhập địa chỉ email" required>
                    <div class="error-text" id="email_error">Vui lòng nhập email hợp lệ.</div>
                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                    <input type="text" name="address" class="form-control mb-2 @error('address') is-invalid @enderror" id="address" value="{{ old('address') }}" placeholder="Số nhà, tên đường/phố" required>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="text" name="commune" class="form-control @error('commune') is-invalid @enderror" id="commune" value="{{ old('commune') }}" placeholder="Phường/xã/đặc khu" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" id="city" value="{{ old('city') }}" placeholder="Tỉnh/thành" required>
                        </div>
                    </div>
                    @error('address') <div class="text-danger small">{{ $message }}</div> @enderror
                    @error('commune') <div class="text-danger small">{{ $message }}</div> @enderror
                    @error('city') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Nhập mật khẩu" required>
                    @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-bold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                    <div class="error-text" id="password_error">Mật khẩu xác nhận không khớp hoặc quá ngắn (mẫu 8 kí tự).</div>
                </div>

                <button type="submit" class="btn btn-morico w-100 py-2 mt-2">Đăng ký</button>
                <p class="login-link text-center mt-3 mb-0" style="font-size: 0.9rem; color: var(--primary-color);">
                    Bạn đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập tại đây</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let hasError = false;
            
            // Client-side Validation logic
            const fullName = document.getElementById('full_name');
            const username = document.getElementById('username');
            const phone = document.getElementById('phone');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordConf = document.getElementById('password_confirmation');

            // Reset errors
            document.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid-client'));

            if (fullName.value.trim().length < 2) {
                fullName.classList.add('is-invalid-client');
                document.getElementById('full_name_error').style.display = 'block';
                hasError = true;
            }

            if (username.value.trim().length < 3) {
                username.classList.add('is-invalid-client');
                document.getElementById('username_error').style.display = 'block';
                hasError = true;
            }

            if (!/^\d{10,11}$/.test(phone.value.trim())) {
                phone.classList.add('is-invalid-client');
                document.getElementById('phone_error').style.display = 'block';
                hasError = true;
            }

            if (password.value.length < 8 || password.value !== passwordConf.value) {
                password.classList.add('is-invalid-client');
                passwordConf.classList.add('is-invalid-client');
                document.getElementById('password_error').style.display = 'block';
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
