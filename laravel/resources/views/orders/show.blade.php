@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng - MORICO')

@section('styles')
<style>
    .order-detail-container { background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 30px; margin-top: 30px; }
    .status-badge { padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #d1ecf1; color: #0c5460; }
    .status-delivered { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .info-box { background: #f8f9fa; border-radius: 10px; padding: 20px; height: 100%; }
    .info-box h3 { font-size: 1.1rem; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px; color: var(--primary-color); }
    .order-table th { border-top: none; background: #f8f9fa; }
    .item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
    .total-section { max-width: 400px; margin-left: auto; }
    .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-weight: bold; }
    .grand-total { font-size: 1.5rem; color: var(--primary-color); border-top: 2px solid #ddd; margin-top: 10px; padding-top: 15px; }
</style>
@endsection

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Lịch sử mua hàng</a></li>
            <li class="breadcrumb-item active">Chi tiết đơn hàng #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="order-detail-container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1">Chi tiết đơn hàng #{{ $order->id }}</h1>
                <span class="text-muted">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="text-end">
                <span class="status-badge status-{{ $order->status }} shadow-sm">
                    @php
                        $statusMap = [
                            'pending' => 'CHỜ XÁC NHẬN',
                            'confirmed' => 'ĐÃ XÁC NHẬN',
                            'delivered' => 'ĐÃ GIAO THÀNH CÔNG',
                            'cancelled' => 'ĐÃ HỦY',
                        ];
                    @endphp
                    {{ $statusMap[$order->status] ?? strtoupper($order->status) }}
                </span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="info-box border shadow-sm">
                    <h3><i class="fas fa-user me-2"></i> Thông tin khách hàng</h3>
                    <p class="mb-2"><strong>Họ tên:</strong> {{ $order->receiver_name }}</p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->receiver_phone }}</p>
                    <p class="mb-0"><strong>Ghi chú:</strong> <span class="fst-italic text-muted">{{ $order->note ?: '(Không có)' }}</span></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box border shadow-sm">
                    <h3><i class="fas fa-truck me-2"></i> Thông tin giao hàng & Thanh toán</h3>
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
        </div>

        <div class="table-responsive mb-4">
            <table class="table order-table align-middle">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderDetails as $detail)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($detail->product ? $detail->product->image : 'assets/image/products/default.jpg') }}" 
                                     class="item-img border shadow-sm"
                                     onerror="this.src='{{ asset('assets/image/products/default.jpg') }}'">
                                <span class="fw-bold">{{ $detail->product_name }}</span>
                            </div>
                        </td>
                        <td class="text-center">{{ number_format($detail->unit_price, 0, ',', '.') }}&nbsp;₫</td>
                        <td class="text-center">x{{ $detail->quantity }}</td>
                        <td class="text-end fw-bold">{{ number_format($detail->subtotal, 0, ',', '.') }}&nbsp;₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total-section ms-auto">
            <div class="total-row">
                <span class="text-muted">Tổng tiền hàng:</span>
                <span>{{ number_format($order->total_amount, 0, ',', '.') }}&nbsp;₫</span>
            </div>
            <div class="total-row">
                <span class="text-muted">Phí vận chuyển:</span>
                <span class="text-success fw-bold">Miễn phí</span>
            </div>
            <div class="total-row grand-total">
                <span>TỔNG THANH TOÁN:</span>
                <span>{{ number_format($order->total_amount, 0, ',', '.') }}&nbsp;₫</span>
            </div>
        </div>

        <div class="mt-5 pt-3 border-top text-center">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-morico px-5 py-2">
                <i class="fas fa-arrow-left me-2"></i> Quay lại lịch sử mua hàng
            </a>
        </div>
    </div>
</div>
@endsection
