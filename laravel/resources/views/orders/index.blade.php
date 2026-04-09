@extends('layouts.app')

@section('title', 'Lịch Sử Mua Hàng - MORICO')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/history.css') }}">
<style>
    .order-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 30px; border-left: 5px solid var(--primary-color); }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #d1ecf1; color: #0c5460; }
    .status-delivered { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .item-list { list-style: none; padding: 0; margin: 15px 0; border-top: 1px solid #eee; border-bottom: 1px solid #eee; }
    .item-list li { display: flex; justify-content: space-between; padding: 10px 0; }
    .view-detail-btn { color: var(--primary-color); text-decoration: none; font-weight: bold; }
    .view-detail-btn:hover { text-decoration: underline; }
</style>
@endsection

@section('content')
<div class="container py-5">
    <h1 class="page-title text-center mb-5">Lịch sử mua hàng của bạn</h1>

    <div id="history-container" class="mx-auto" style="max-width: 900px;">
        @forelse($orders as $order)
            <div class="order-card">
                <div class="order-header d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h2 class="h4 mb-1">Mã đơn hàng: #{{ $order->id }}</h2>
                        <span class="text-muted small">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <span class="status-badge status-{{ $order->status }}">
                        @php
                            $statusMap = [
                                'pending' => 'Chờ xác nhận',
                                'confirmed' => 'Đã xác nhận',
                                'delivered' => 'Đã giao thành công',
                                'cancelled' => 'Đã hủy',
                            ];
                        @endphp
                        {{ $statusMap[$order->status] ?? $order->status }}
                    </span>
                </div>

                <div class="order-details mb-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Địa chỉ: <strong>{{ "{$order->shipping_address}, {$order->shipping_commune}, {$order->shipping_city}" }}</strong></p>
                            <p class="mb-0"><i class="fas fa-credit-card me-2 text-primary"></i> Thanh toán: 
                                <strong>
                                    @if($order->payment_method == 'cod') Tiền mặt (COD) 
                                    @elseif($order->payment_method == 'transfer') Chuyển khoản 
                                    @else Trực tuyến @endif
                                </strong>
                            </p>
                        </div>
                        <div class="col-md-5">
                            <p class="mb-1"><i class="fas fa-user me-2 text-primary"></i> Người nhận: <strong>{{ $order->receiver_name }}</strong></p>
                            <p class="mb-0"><i class="fas fa-phone me-2 text-primary"></i> SĐT: <strong>{{ $order->receiver_phone }}</strong></p>
                        </div>
                    </div>
                </div>

                <h3 class="h6 fw-bold">Sản phẩm tóm tắt:</h3>
                <ul class="item-list">
                    @foreach($order->orderDetails->take(3) as $detail)
                    <li>
                        <span>{{ $detail->product_name }}</span>
                        <span class="text-muted">x{{ $detail->quantity }}</span>
                        <span class="fw-bold">{{ number_format($detail->subtotal, 0, ',', '.') }}&nbsp;₫</span>
                    </li>
                    @endforeach
                    @if($order->orderDetails->count() > 3)
                        <li class="text-center text-muted small italic">... và {{ $order->orderDetails->count() - 3 }} sản phẩm khác</li>
                    @endif
                </ul>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="total-info h5 mb-0 fw-bold text-primary">
                        TỔNG TIỀN: {{ number_format($order->total_amount, 0, ',', '.') }}&nbsp;₫
                    </div>
                    <a href="{{ route('orders.show', $order->id) }}" class="view-detail-btn">
                        Xem chi tiết <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                <p class="fs-5 text-muted">Bạn chưa có đơn hàng nào.</p>
                <a href="{{ route('products.index') }}" class="btn btn-morico mt-2 px-4">Mua sắm ngay</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
