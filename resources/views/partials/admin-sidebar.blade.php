<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-wrap">
            <img src="{{ asset('assets/image/common/morico-black-emblem.png') }}" alt="MORICO" class="logo-img">
            <div class="logo-text-wrapper ms-2">
                <div class="logo-title">MORICO</div>
                <div class="logo-subtitle">Nền tảng quản lí</div>
            </div>
        </div>
    </div>
    
    <nav class="nav-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-home"></i>
                    <span>Bảng điều khiển</span>
                </a>
            </li>
        </ul>

        <div class="nav-title mt-4 mb-2 px-4 small text-uppercase opacity-50 fw-bold">Quản lý</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Người dùng</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.catalogue.*') ? 'active' : '' }}" href="{{ route('admin.catalogue.index') }}">
                    <i class="fa-solid fa-box"></i>
                    <span>Danh mục sản phẩm</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.goods-receipts.*') ? 'active' : '' }}" href="{{ route('admin.goods-receipts.index') }}">
                    <i class="fa-solid fa-truck-loading"></i>
                    <span>Nhập hàng</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}" href="{{ route('admin.pricing.index') }}">
                    <i class="fa-solid fa-dollar-sign"></i>
                    <span>Quản lý giá bán</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                    <i class="fa-solid fa-shopping-cart"></i>
                    <span>Đơn hàng</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                    <i class="fa-solid fa-warehouse"></i>
                    <span>Tồn kho</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
