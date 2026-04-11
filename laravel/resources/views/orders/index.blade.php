@extends('layouts.app')

@section('title', 'Lịch Sử Mua Hàng - MORICO')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/orders.css') }}">
@endsection

@section('content')
<div class="app-container py-5">    
    <nav class="m-breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="separator">/</span>
        <span class="current">Lịch sử mua hàng</span>
    </nav>

    <h1 class="page-title">Lịch sử mua hàng</h1>
    <p class="page-subtitle">Theo dõi trạng thái và chi tiết các đơn hàng của bạn</p>

    <div id="history-container" class="mx-auto">
        @forelse($orders as $order)
            <div class="m-card">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h2 class="m-card-title border-0 mb-0">Đơn hàng #{{ $order->id }}</h2>
                        <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="text-end">
                        <span class="m-badge m-badge-{{ $order->status }}">
                            @php
                                $statusMap = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'delivered' => 'Đã giao thành công',
                                    'cancelled' => 'Đã hủy',
                                ];
                            @endphp
                            {{ $statusMap[$order->status] ?? strtoupper($order->status) }}
                        </span>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="m-info-card">
                            <h3><i class="fas fa-user text-primary"></i> Người nhận</h3>
                            <p><strong>Họ tên:</strong> {{ $order->receiver_name }}</p>
                            <p class="mb-0"><strong>Số điện thoại:</strong> {{ $order->receiver_phone }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="m-info-card">
                            <h3><i class="fas fa-truck text-primary"></i> Giao hàng</h3>
                            <p class="mb-0 text-truncate" title="{{ "{$order->shipping_address}, {$order->shipping_commune}, {$order->shipping_city}" }}">
                                <strong>Địa chỉ:</strong> {{ "{$order->shipping_address}, {$order->shipping_commune}, {$order->shipping_city}" }}
                            </p>
                            <p class="mt-2 mb-0"><strong>Thanh toán:</strong> 
                                @if($order->payment_method == 'cod') Tiền mặt (COD) 
                                @else Chuyển khoản @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="m-info-card">
                            <p class="mb-0"><strong>Ghi chú:</strong> <span class="fst-italic text-muted">{{ $order->note ?: '(Không có)' }}</span></p>
                        </div>
                    </div>
                </div>

                <h3 class="h6 fw-bold text-muted text-uppercase mb-3"><i class="fas fa-shopping-basket me-2"></i>Sản phẩm</h3>
                <ul class="order-item-summary">
                    @foreach($order->orderDetails->take(2) as $detail)
                    <li>
                        <span class="item-name">{{ $detail->product_name }}</span>
                        <div class="text-end">
                            <span class="text-muted small me-3">×{{ $detail->quantity }}</span>
                            <span class="fw-bold text-primary">{{ number_format($detail->subtotal, 0, ',', '.') }}&nbsp;₫</span>
                        </div>
                    </li>
                    @endforeach
                    @if($order->orderDetails->count() > 2)
                        <li class="text-center py-2"><span class="text-muted italic small">... và {{ $order->orderDetails->count() - 2 }} sản phẩm khác</span></li>
                    @endif
                </ul>

                <div class="m-total-bar border-top-0 pt-0">
                    <div class="m-grand-total text-start">
                        <small>Tổng thanh toán:</small>
                        {{ number_format($order->total_amount, 0, ',', '.') }}&nbsp;₫
                    </div>
                    <a href="{{ route('orders.show', $order->id) }}" class="m-btn m-btn-primary px-4">
                        Xem chi tiết <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-bag fa-4x text-muted opacity-25"></i>
                </div>
                <h3 class="h4 text-muted">Bạn chưa có đơn hàng nào</h3>
                <p class="text-muted mb-4">Hãy khám phá bộ sưu tập bánh ngọt thơm ngon của Morico nhé!</p>
                <a href="{{ route('products.index') }}" class="m-btn m-btn-primary px-5">Mua sắm ngay</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
