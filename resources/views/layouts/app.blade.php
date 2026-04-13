<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Morico Bakery')</title>
    
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/user/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/user/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-free-7.2.0/css/all.min.css') }}">
    
    @yield('styles')
</head>
<body>
    @include('partials.header')

    <main>
        @yield('content')
    </main>

    <!-- Notification Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <!-- Toasts will be injected here by main.js -->
    </div>

    @include('partials.footer')

    <script src="{{ asset('assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/user/header.js') }}"></script>
    <script src="{{ asset('assets/js/user/search.js') }}"></script>
    <script src="{{ asset('assets/js/user/main.js') }}"></script>
    
    @yield('scripts')
</body>
</html>
