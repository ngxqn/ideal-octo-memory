@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng - MORICO')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/orders.css') }}">
@endsection

@section('content')
<div class="app-container py-5">
    <nav class="m-breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="separator">/</span>
        <a href="{{ route('orders.index') }}">Lịch sử mua hàng</a>
        <span class="separator">/</span>
        <span class="current">Chi tiết đơn hàng #{{ $order->id }}</span>
    </nav>

    <div class="mt-4 pt-0 text-start">
        <a href="{{ route('orders.index') }}" class="m-btn-link">
            <i class="fas fa-arrow-left me-2"></i> Quay lại lịch sử mua hàng
        </a>
    </div>  

    <div class="m-card mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="m-card-title border-0 mb-0">Chi tiết đơn hàng #{{ $order->id }}</h2>
                <span class="text-muted"><i class="far fa-calendar-alt me-1"></i> Ngày đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}</span>
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
                    <h3><i class="fas fa-user text-primary"></i> Thông tin người nhận</h3>
                    <p class="mb-2"><strong>Họ tên:</strong> {{ $order->receiver_name }}</p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->receiver_phone }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="m-info-card">
                    <h3><i class="fas fa-truck text-primary"></i> Giao hàng & thanh toán</h3>
                    <p class="mb-2"><strong>Địa chỉ:</strong> {{ "{$order->shipping_address}, {$order->shipping_commune}, {$order->shipping_city}" }}</p>
                    <p class="mb-0"><strong>Thanh toán:</strong> 
                        <strong>
                            @if($order->payment_method == 'cod') Tiền mặt (COD) 
                            @elseif($order->payment_method == 'transfer') Chuyển khoản ngân hàng 
                            @else Thanh toán trực tuyến 
                            @endif
                        </strong>
                    </p>
                </div>
            </div>
            <div class="col-md-12">
                <div class="m-info-card">
                    <p class="mb-0"><strong>Ghi chú:</strong> <span class="fst-italic text-muted">{{ $order->note ?: '(Không có)' }}</span></p>
                </div>
            </div>
        </div>
        

        <div class="table-responsive mb-0">
            <table class="m-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 50%;">Sản phẩm</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderDetails as $detail)
                    <tr>
                        <td>
                            @if($detail->product)
                                <a href="{{ route('products.show', $detail->product->id) }}" class="text-decoration-none color-inherit">
                                    <div class="item-details">
                                        <img src="{{ asset('storage/' . $detail->product->image) }}" 
                                             class="item-img"
                                             onerror="this.src='{{ asset('storage/products/default.png') }}'">
                                        <span class="fw-bold text-dark">{{ $detail->product_name }}</span>
                                    </div>
                                </a>
                            @else
                                <div class="item-details">
                                    <img src="{{ asset('storage/products/default.png') }}" class="item-img">
                                    <span class="fw-bold text-dark">{{ $detail->product_name }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-muted">{{ number_format($detail->unit_price, 0, ',', '.') }}&nbsp;₫</td>
                        <td class="text-center fw-bold">×{{ $detail->quantity }}</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($detail->subtotal, 0, ',', '.') }}&nbsp;₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="m-total-bar mt-0">
            <div class="ms-auto d-flex flex-column align-items-end gap-2" style="min-width: 300px;">
                <div class="d-flex justify-content-between w-100 mb-2">
                    <span class="text-muted">Tổng tiền hàng:</span>
                    <span class="fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}&nbsp;₫</span>
                </div>
                <div class="d-flex justify-content-between w-100 mb-3">
                    <span class="text-muted">Phí vận chuyển:</span>
                    <span class="text-success fw-bold">Miễn phí</span>
                </div>
                <div class="m-grand-total border-top pt-4 w-100">
                    <small>TỔNG THANH TOÁN:</small>
                    {{ number_format($order->total_amount, 0, ',', '.') }}&nbsp;₫
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
