@extends('layouts.app')

@section('title', 'Giỏ hàng - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/cart.css') }}">
<style>
    .quantity-control button {
        border: 1px solid #ddd;
        background: white;
        padding: 5px 10px;
        cursor: pointer;
    }
    .quantity-control span {
        padding: 0 15px;
        font-weight: bold;
    }
    /* Thêm style cho nút xóa */
    .remove-btn {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.2rem;
        cursor: pointer;
    }
    .remove-btn:hover { color: #a71d2a; }
</style>
@endsection

@section('content')
    <h1 class="page-title">Giỏ hàng của bạn</h1>
    
    <div id="cart-container" class="container mb-5">
        <div class="cart-header d-none d-md-grid" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr; padding: 15px; background: #f8f9fa; font-weight: bold; border-bottom: 2px solid #ddd;">
            <div>Sản phẩm</div>
            <div class="text-center">Đơn giá</div>
            <div class="text-center">Số lượng</div>
            <div class="text-center">Tổng tiền</div>
            <div class="text-center">Thao tác</div>
        </div>

        <div id="cart-items-list" class="mt-3">
            @forelse($cart->cartItems as $item)
                <div class="cart-item py-3 border-bottom d-flex flex-column d-md-grid" 
                     data-id="{{ $item->id }}"
                     style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 0.5fr; align-items: center;">
                    
                    <div class="item-details d-flex align-items-center gap-3">
                        <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" 
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                             onerror="this.src='{{ asset('assets/image/products/default.png') }}'">
                        <span class="fw-bold">{{ $item->product->name }}</span>
                    </div>

                    <div class="text-center">
                        <span class="d-md-none fw-bold">Đơn giá: </span>
                        {{ number_format($item->product->sell_price, 0, ',', '.') }}&nbsp;₫
                    </div>

                    <div class="quantity-control d-flex justify-content-center align-items-center">
                        <button class="btn-qty-minus" data-id="{{ $item->id }}"><i class="fa-solid fa-minus"></i></button>
                        <span class="qty-val">{{ $item->quantity }}</span>
                        <button class="btn-qty-plus" data-id="{{ $item->id }}"><i class="fa-solid fa-plus"></i></button>
                    </div>

                    <div class="text-center">
                        <span class="d-md-none fw-bold">Tổng tiền: </span>
                        <span class="item-total-price">{{ number_format($item->product->sell_price * $item->quantity, 0, ',', '.') }}&nbsp;₫</span>
                    </div>

                    <div class="text-center">
                        <button class="remove-btn" data-id="{{ $item->id }}">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-cart text-center py-5">
                    <i class="fa-solid fa-cart-shopping fa-3x mb-3" style="color: #ddd;"></i>
                    <p>Giỏ hàng của bạn đang trống</p>
                    <a href="{{ route('products.index') }}" class="btn btn-morico mt-3">Tiếp tục mua sắm</a>
                </div>
            @endforelse
        </div>

        @if($cart->cartItems->count() > 0)
            <div class="summary-box mt-4 p-4 border rounded shadow-sm d-flex flex-column align-items-end" id="summary-box">
                <div class="summary-row mb-3">
                    <span class="total-label fs-5 fw-bold">Tổng cộng:</span>
                    <span class="total-amount fs-4 fw-bold text-danger ms-3" id="final-total">
                        {{ number_format($total, 0, ',', '.') }}&nbsp;₫
                    </span>
                </div>
                <div class="checkout-group">
                    <a href="{{ route('checkout.index') }}" class="btn btn-morico btn-lg px-5 py-2">Thanh toán</a>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('products.index') }}" class="continue-shopping text-decoration-none text-muted">
                <i class="fa-solid fa-arrow-left me-2"></i>Tiếp tục mua hàng
            </a>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartList = document.getElementById('cart-items-list');
        const finalTotalSpan = document.getElementById('final-total');

        // Hàm định dạng tiền tệ Việt Nam
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + '\u00A0₫';
        }

        // Cập nhật tổng giỏ hàng
        function updateSummary() {
            let total = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const priceText = item.querySelector('.item-total-price').textContent;
                const price = parseInt(priceText.replace(/[^\d]/g, ''));
                total += price;
            });
            if (finalTotalSpan) {
                finalTotalSpan.textContent = formatCurrency(total);
            }
            if (total === 0) {
                window.location.reload(); // Tải lại để hiện UI trống
            }
        }

        // Xử lý Thay đổi số lượng
        cartList.addEventListener('click', async function(e) {
            const btnMinus = e.target.closest('.btn-qty-minus');
            const btnPlus = e.target.closest('.btn-qty-plus');
            const btnRemove = e.target.closest('.remove-btn');

            if (btnMinus || btnPlus) {
                const btn = btnMinus || btnPlus;
                const id = btn.getAttribute('data-id');
                const row = btn.closest('.cart-item');
                const qtySpan = row.querySelector('.qty-val');
                let currentQty = parseInt(qtySpan.textContent);
                const newQty = btnMinus ? currentQty - 1 : currentQty + 1;

                if (newQty < 1) return;

                try {
                    const response = await fetch(`{{ url('/cart/items') }}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ quantity: newQty })
                    });
                    
                    const res = await response.json();
                    if (res.success) {
                        qtySpan.textContent = newQty;
                        // Cập nhật thành tiền của dòng này
                        const unitPriceText = row.querySelector('.text-center').textContent;
                        const unitPrice = parseInt(unitPriceText.replace(/[^\d]/g, ''));
                        row.querySelector('.item-total-price').textContent = formatCurrency(unitPrice * newQty);
                        updateSummary();
                        updateCartCount(); // Hàm từ header.js
                    } else {
                        alert(res.message);
                    }
                } catch (error) {
                    console.error('Lỗi cập nhật giỏ hàng:', error);
                }
            }

            // Xử lý Xóa sản phẩm
            if (btnRemove) {
                const id = btnRemove.getAttribute('data-id');
                const row = btnRemove.closest('.cart-item');

                if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    try {
                        const response = await fetch(`{{ url('/cart/items') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const res = await response.json();
                        if (res.success) {
                            row.remove();
                            updateSummary();
                            updateCartCount();
                        } else {
                            alert(res.message);
                        }
                    } catch (error) {
                        console.error('Lỗi xóa sản phẩm:', error);
                    }
                }
            }
        });
    });
</script>
@endsection
