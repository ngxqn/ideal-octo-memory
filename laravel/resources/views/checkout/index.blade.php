@extends('layouts.app')

@section('title', 'Thanh Toán Đơn Hàng - MORICO')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/checkout.css') }}">
<style>
    .payment-option { margin-bottom: 10px; padding: 10px; border: 1px solid #eee; border-radius: 5px; cursor: pointer; }
    .payment-option:hover { background: #f9f9f9; }
    .payment-option input { margin-right: 10px; }
    .section h2 { border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; margin-bottom: 20px; font-size: 1.5rem; }
    .order-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #eee; }
    .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-weight: bold; }
    .total-amount { color: var(--primary-color); font-size: 1.2rem; }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="header text-center mb-5">
        <h1 class="page-title">Thanh toán đơn hàng</h1>
        <div class="page-subtitle text-muted">Hoàn tất đơn hàng của bạn một cách dễ dàng và an toàn</div>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row g-4">
            <!-- Thông tin nhận hàng -->
            <div class="col-lg-7">
                <div id="shipping-info" class="section p-4 border rounded shadow-sm bg-white">
                    <h2><i class="fas fa-truck me-2"></i> Thông tin nhận hàng</h2>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_name" class="form-control" value="{{ old('receiver_name', $user->full_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_phone" class="form-control" value="{{ old('receiver_phone', $user->phone) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Số nhà, tên đường/phố <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_address" class="form-control" value="{{ old('shipping_address', $user->address_detail) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phường/xã/đặc khu <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_commune" class="form-control" value="{{ old('shipping_commune', $user->commune) }}" required placeholder="Ví dụ: Phường 1, Quận 1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tỉnh/thành phố <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_city" class="form-control" value="{{ old('shipping_city', $user->city) }}" required>
                        </div>
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold"><i class="fas fa-pen-to-square me-2"></i>Ghi chú đơn hàng</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Ví dụ: Giao giờ hành chính, lời nhắn cho cửa hàng...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chi tiết đơn hàng & Thanh toán -->
            <div class="col-lg-5">
                <div id="order-summary" class="section p-4 border rounded shadow-sm bg-white">
                    <h2><i class="fas fa-receipt me-2"></i> Chi tiết Đơn hàng</h2>
                    
                    <div class="items-list mb-4">
                        @foreach($cart->cartItems as $item)
                        <div class="order-item">
                            <div class="item-info">
                                <div class="fw-bold">{{ $item->product->name }}</div>
                                <div class="text-muted small">Số lượng: {{ $item->quantity }}</div>
                            </div>
                            <span class="fw-bold text-primary">
                                {{ number_format($item->product->sell_price * $item->quantity, 0, ',', '.') }}&nbsp;₫
                            </span>
                        </div>
                        @endforeach
                    </div>

                    <div class="total-row">
                        <span>Tổng tiền hàng:</span>
                        <span id="sub-total">{{ number_format($total, 0, ',', '.') }}&nbsp;₫</span>
                    </div>

                    <div class="total-row border-0">
                        <span>Phí vận chuyển:</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>

                    <div class="total-row mt-3 pt-3 border-top border-2 border-primary">
                        <span class="fs-5">TỔNG THANH TOÁN:</span>
                        <span class="total-amount fs-4" id="final-total">{{ number_format($total, 0, ',', '.') }}&nbsp;₫</span>
                    </div>

                    <div class="mt-5">
                        <h2 class="fs-5 mb-3">Hình thức Thanh toán</h2>
                        
                        <div class="payment-option border p-3 rounded mb-2">
                            <input type="radio" name="payment_method" id="cod" value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                            <label for="cod" class="m-0 cursor-pointer">Thanh toán khi nhận hàng (COD)</label>
                        </div>
                        
                        <div class="payment-option border p-3 rounded mb-2">
                            <input type="radio" name="payment_method" id="transfer" value="transfer" {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                            <label for="transfer" class="m-0 cursor-pointer">Chuyển khoản ngân hàng</label>
                        </div>

                        <div class="payment-option border p-3 rounded mb-4">
                            <input type="radio" name="payment_method" id="online" value="online" {{ old('payment_method') == 'online' ? 'checked' : '' }}>
                            <label for="online" class="m-0 cursor-pointer">Thanh toán trực tuyến (VNPay/ZaloPay)</label>
                        </div>
                    </div>

                    @if($errors->has('checkout'))
                        <div class="alert alert-danger">{{ $errors->first('checkout') }}</div>
                    @endif

                    <button type="submit" class="btn btn-morico btn-lg w-100 py-3 mt-3 fw-bold shadow">
                        <i class="fas fa-check-circle me-2"></i> XÁC NHẬN ĐẶT HÀNG
                    </button>

                    <div class="text-center mt-4">
                        <a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('checkout-form');
        
        form.addEventListener('submit', function() {
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> ĐANG XỬ LÝ...';
        });
    });
</script>
@endsection
