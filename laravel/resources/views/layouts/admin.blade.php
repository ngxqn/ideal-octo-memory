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
<body>
    <div class="container-fluid overflow-hidden p-0">
        <div class="row g-0 flex-nowrap min-vh-100">
            @include('partials.admin-sidebar')

            <!-- Main content -->
            <main class="col col-md-9 col-lg-10 main-content d-flex flex-column p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h1 class="h3 fw-bold mb-3 mb-md-0" style="color: var(--brand-crimson);">
                        @yield('page_title')
                    </h1>
                    <div class="admin-user-info d-flex align-items-center bg-white rounded-pill shadow-sm px-2 py-2 border">
                        <div class="ms-2 me-3">
                            <i class="fa-solid fa-user" style="font-size: 1.2rem; color: var(--brand-crimson);"></i>
                        </div>
                        <div class="me-2 d-none d-sm-block text-start" style="line-height: 1.2;">
                            <div class="fw-bold" style="color: var(--brand-crimson);">{{ auth()->user()->full_name }}</div>
                            <div class="text-muted" style="font-size: 0.78rem;">Quản trị viên</div>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
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
    @yield('scripts')
</body>
</html>
