@php
    $isAdmin = request()->is('admin/*');
@endphp

@extends($isAdmin ? 'layouts.admin' : 'layouts.app')

@section('title', '404 - Trang không tìm thấy')

@section('styles')
<style>
    .error-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 50px 20px;
        text-align: center;
    }
    .error-code {
        font-size: 120px;
        font-weight: 900;
        color: var(--brand-crimson, #8b0000);
        line-height: 1;
        margin-bottom: 20px;
        text-shadow: 4px 4px 0px rgba(0,0,0,0.05);
    }
    .error-message {
        font-size: 24px;
        color: #333;
        margin-bottom: 30px;
    }
    .error-description {
        color: #666;
        max-width: 500px;
        margin-bottom: 40px;
    }
    .error-actions {
        display: flex;
        gap: 15px;
    }
    
    /* Admin specific adjustments */
    body.collapsed-sidebar .error-container {
        padding-left: 0;
    }
</style>
@endsection

@section('page_title', 'Lỗi 404') <!-- For Admin Layout -->

@section('content')
<div class="app-container">
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-message">Oops! Trang bạn tìm kiếm không tồn tại.</h1>
        <p class="error-description">
            Có vẻ như liên kết đã bị hỏng hoặc trang đã được di chuyển. 
            Đừng lo lắng, hương vị bánh Morico vẫn luôn chờ đón bạn ở trang chủ!
        </p>
        <div class="error-actions">
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary px-4 py-2 shadow-sm">
                    <i class="fas fa-chart-line me-2"></i> Quay về Bảng điều khiển
                </a>
            @else
                <a href="{{ route('home') }}" class="m-btn m-btn-primary px-5 py-3 shadow">
                    <i class="fas fa-home me-2"></i> Quay về trang chủ
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-5 py-3">
                    Tiếp tục mua sắm
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
