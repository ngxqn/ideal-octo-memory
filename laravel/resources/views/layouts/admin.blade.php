<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Admin - MORICO')</title>

    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin_style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-free-7.2.0/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    
    @yield('styles')
</head>
<body class="{{ (isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] == 'true') ? 'collapsed-sidebar' : '' }}">
        @include('partials.admin-sidebar')
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="main-wrapper">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left d-flex align-items-center">
                    <button id="sidebarToggle" class="btn btn-link link-dark p-0 me-3">
                        <i class="fa-solid fa-bars-staggered fs-4"></i>
                    </button>
                    <h1 class="h4 fw-bold mb-0" style="color: var(--brand-crimson);">
                        @yield('page_title')
                    </h1>
                </div>

                <div class="header-right">
                    <div class="admin-user-info d-flex align-items-center bg-white rounded-pill shadow-sm px-3 py-2 border">
                        <div class="me-3">
                            <i class="fa-solid fa-circle-user" style="font-size: 1.5rem; color: var(--brand-crimson);"></i>
                        </div>
                        <div class="me-3 d-none d-sm-block text-start" style="line-height: 1.2;">
                            <div class="fw-bold" style="color: var(--brand-crimson);">{{ auth()->user()->full_name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Quản trị viên</div>
                        </div>
                        <div class="vr me-3 d-none d-sm-block" style="height: 24px; opacity: 0.15;"></div>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                           class="logout-link text-muted hover-crimson" title="Đăng xuất">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <ul class="mb-0 d-inline-block">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebarToggle');
            const body = document.body;
            const backdrop = document.getElementById('sidebarBackdrop');

            function toggleSidebar() {
                if (window.innerWidth <= 768) {
                    body.classList.toggle('sidebar-open');
                } else {
                    body.classList.toggle('collapsed-sidebar');
                    // Save preference in cookie (more reliable for PHP SSR)
                    const isCollapsed = body.classList.contains('collapsed-sidebar');
                    document.cookie = "sidebar_collapsed=" + isCollapsed + "; path=/; max-age=" + (60*60*24*30);
                }
            }

            toggle.addEventListener('click', toggleSidebar);

            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    body.classList.remove('sidebar-open');
                });
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    body.classList.remove('sidebar-open');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
