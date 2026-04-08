<header>
    <div class="header-container">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('assets/image/common/morico-black-emblem.png') }}" alt="MORICO" class="logo-img">
            <span>MORICO</span>
        </a>

        <div class="search-bar">
            <input type="text" placeholder="Bạn muốn tìm bánh gì...">
            <button>
                <i class="fa-solid fa-magnifying-glass" style="color: white;"></i>
            </button>
        </div>
        <div class="search-results"></div>

        <div class="icon-group">
            @auth
            <a href="{{ route('cart.index') }}" class="icon-link" id="cartLink">
                <i class="fa-solid fa-cart-shopping"></i>
                <span id="cartCountBadge" class="cart-count">0</span>
            </a>
            @endauth

            <div class="user-menu">
                @auth
                    <i class="fa-solid fa-user user-icon" id="userIcon"></i>
                    <div class="dropdown" id="userDropdown">
                        <a href="{{ route('profile.edit') }}">Tài khoản</a>
                        <a href="{{ route('orders.index') }}">Lịch sử mua hàng</a>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
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
