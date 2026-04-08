<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Morico Bakery</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-free-7.2.0/css/all.min.css') }}">
    <style>
        body { display: flex; min-height: 100vh; background-color: #f8f9fa; }
        .sidebar { width: 250px; background-color: #343a40; color: #fff; padding: 20px; }
        .main-content { flex: 1; padding: 40px; }
        .nav-link { color: #adb5bd; margin-bottom: 10px; display: block; text-decoration: none; }
        .nav-link:hover, .nav-link.active { color: #fff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="mb-4">Morico Admin</h3>
        <nav>
            <a href="#" class="nav-link active"><i class="fas fa-home me-2"></i> Tổng quan</a>
            <a href="#" class="nav-link"><i class="fas fa-box me-2"></i> Sản phẩm</a>
            <a href="#" class="nav-link"><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</a>
            <a href="#" class="nav-link"><i class="fas fa-users me-2"></i> Người dùng</a>
            <hr>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </div>
    <div class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h2>Chào mừng, {{ Auth::user()->full_name }}</h2>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Xem Trang chủ</a>
        </header>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text">Đây là khu vực quản trị. Các chức năng quản lý sản phẩm, đơn hàng và kho sẽ được triển khai chi tiết ở các giai đoạn tiếp theo.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
