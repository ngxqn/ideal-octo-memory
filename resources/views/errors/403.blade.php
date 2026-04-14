@php
    $isAdmin = request()->is('admin/*');
@endphp

@extends($isAdmin ? 'layouts.admin' : 'layouts.app')

@section('title', '403 - Truy cập bị từ chối')

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
</style>
@endsection

@section('page_title', 'Lỗi 403') <!-- For Admin Layout -->

@section('content')
<div class="app-container">
    <div class="error-container">
        <div class="error-code">403</div>
        <h1 class="error-message">Xin lỗi! Bạn không có quyền truy cập trang này.</h1>
        <p class="error-description">
            Đây là khu vực hạn chế. Vui lòng liên hệ quản trị viên nếu bạn tin rằng đây là một sự nhầm lẫn.
        </p>
        <div class="error-actions">
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary px-4 py-2 shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i> Quay về Bảng điều khiển
                </a>
            @else
                <a href="{{ route('home') }}" class="m-btn m-btn-primary px-5 py-3 shadow">
                    <i class="fas fa-home me-2"></i> Quay về trang chủ
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
