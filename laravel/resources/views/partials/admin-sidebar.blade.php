<aside class="col-auto col-md-3 col-lg-2 sidebar d-flex flex-column">
    <div class="sidebar-header">
        <div class="logo-wrap">
            <img src="{{ asset('assets/image/common/morico-black-emblem.png') }}" alt="MORICO" class="logo-img">
            <div class="d-none d-md-block text-start">
                <div class="logo-title">MORICO</div>
                <div class="logo-subtitle">Nền tảng quản lí</div>
            </div>
        </div>
    </div>
    <nav class="nav-menu flex-grow-1">
        <div class="nav-section">
            <div class="nav-title d-none d-md-block">Bảng điều khiển</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-home"></i>
                        <span class="d-none d-md-inline">Trang chủ</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="nav-section mt-3">
            <div class="nav-title d-none d-md-block">Quản lý</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fa-solid fa-users"></i>
                        <span class="d-none d-md-inline">Người dùng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.catalogue.*') ? 'active' : '' }}" href="{{ route('admin.catalogue.index') }}">
                        <i class="fa-solid fa-box"></i>
                        <span class="d-none d-md-inline">Sản phẩm & Loại</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.goods-receipts.*') ? 'active' : '' }}" href="{{ route('admin.goods-receipts.index') }}">
                        <i class="fa-solid fa-truck-loading"></i>
                        <span class="d-none d-md-inline">Nhập hàng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}" href="{{ route('admin.pricing.index') }}">
                        <i class="fa-solid fa-dollar-sign"></i>
                        <span class="d-none d-md-inline">Quản lý giá bán</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                        <i class="fa-solid fa-shopping-cart"></i>
                        <span class="d-none d-md-inline">Đơn hàng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                        <i class="fa-solid fa-warehouse"></i>
                        <span class="d-none d-md-inline">Tồn kho</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="sidebar-footer mt-auto p-3">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="fa-solid fa-sign-out-alt"></i> Đăng xuất
            </button>
        </form>
    </div>
</aside>
