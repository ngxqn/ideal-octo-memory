<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Morico Bakery</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    <style>
        body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { width: 400px; padding: 40px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-admin { background-color: #343a40; color: #fff; }
        .btn-admin:hover { background-color: #23272b; color: #fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="text-center mb-4">MORICO ADMIN</h2>
        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required autofocus>
                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-admin w-100 py-2">Đăng nhập Quản trị</button>
        </form>
        <div class="text-center mt-3">
            <a href="{{ route('home') }}" class="small text-muted text-decoration-none">Quay về Trang chủ</a>
        </div>
    </div>
</body>
</html>
