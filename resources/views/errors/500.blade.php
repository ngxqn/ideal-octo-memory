@php
    $isAdmin = request()->is('admin/*');
@endphp

@extends($isAdmin ? 'layouts.admin' : 'layouts.app')

@section('title', '500 - Lỗi máy chủ')

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

@section('page_title', 'Lỗi 500') <!-- For Admin Layout -->

@section('content')
<div class="app-container">
    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-message">Oops! Đã có lỗi xảy ra phía máy chủ.</h1>
        <p class="error-description">
            Chúng tôi đang gặp phải một số vấn đề kỹ thuật. Đội ngũ kỹ thuật của Morico đã được thông báo và đang nỗ lực khắc phục. 
            Vui lòng thử lại sau ít phút.
        </p>
        <div class="error-actions">
            <a href="javascript:location.reload()" class="btn btn-secondary px-4 py-2">
                <i class="fas fa-sync me-2"></i> Thử lại
            </a>
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary px-4 py-2 shadow-sm">
                    <i class="fas fa-home me-2"></i> Quay về Bảng điều khiển
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
