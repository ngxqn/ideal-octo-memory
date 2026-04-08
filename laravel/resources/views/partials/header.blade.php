<header>
    <div class="header-container">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('assets/image/common/morico-black-emblem.png') }}" alt="MORICO" class="logo-img">
            <span>MORICO</span>
        </a>

        <form action="{{ route('products.index') }}" method="GET" class="search-bar">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('min_price'))
                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
            @endif
            @if(request('max_price'))
                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Bạn muốn tìm bánh gì...">
            <button type="submit">
                <i class="fa-solid fa-magnifying-glass" style="color: white;"></i>
            </button>
        </form>

        <div class="icon-group">
            @auth
            <a href="{{ route('cart.index') }}" class="icon-link" id="cartLink">
                <i class="fa-solid fa-cart-shopping"></i>
                <span id="cartCountBadge" class="cart-count">0</span>
            </a>
            @endauth

            <div class="user-menu">
                @auth
                    <div class="d-flex align-items-center gap-2" id="userIcon" style="cursor: pointer;">
                        <div class="user-avatar-circle">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <span class="d-none d-md-inline fw-bold" style="color: var(--primary-color);">{{ Auth::user()->full_name }}</span>
                    </div>
                    <div class="dropdown" id="userDropdown">
                        <a href="{{ route('profile.edit') }}">Tài khoản</a>
                        @if(Auth::user()->role === 'customer')
                            <a href="{{ route('orders.index') }}">Lịch sử mua hàng</a>
                        @else
                            <a href="{{ route('admin.dashboard') }}">Quản trị Admin</a>
                        @endif
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #8B0000;">Đăng xuất</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                @else
                    <div class="auth-buttons" style="display: flex; gap: 10px;">
                        <a href="{{ route('login') }}" class="auth-btn sign-in-btn">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="auth-btn sign-up-btn">Đăng ký</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>

<!-- NAVIGATION -->
<nav class="nav-bar">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">TRANG CHỦ</a>
    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">GIỚI THIỆU</a>
    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">SẢN PHẨM</a>
</nav>
