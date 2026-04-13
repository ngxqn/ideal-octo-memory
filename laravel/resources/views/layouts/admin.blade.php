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
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            showNotification("{{ session('success') }}", 'success');
                        });
                    </script>
                @endif

                @if($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            @php
                                $allErrors = implode(' ', $errors->all());
                            @endphp
                            showNotification("{{ $allErrors }}", 'danger');
                        });
                    </script>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Notification Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <!-- Toasts will be injected by JavaScript -->
    </div>

    <script src="{{ asset('assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        /**
         * Global Notification Helper using Bootstrap Toasts
         */
        function showNotification(message, type = 'success') {
            const container = document.querySelector('.toast-container');
            if (!container) return;

            const toastId = 'toast-' + Date.now();
            let bgClass = 'text-bg-success';
            if (type === 'error' || type === 'danger') bgClass = 'text-bg-danger';
            if (type === 'warning') bgClass = 'text-bg-warning';
            if (type === 'info') bgClass = 'text-bg-info';
            
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center border-0 ${bgClass}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', toastHtml);

            const toastEl = document.getElementById(toastId);
            if (!toastEl) return;

            const bs = window.bootstrap || bootstrap;
            if (bs && bs.Toast) {
                const toast = new bs.Toast(toastEl, { delay: 3000 });
                toast.show();
                toastEl.addEventListener('hidden.bs.toast', () => { toastEl.remove(); });
            }
        }

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
