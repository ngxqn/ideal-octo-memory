@extends('layouts.app')

@section('title', 'Giỏ hàng - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/cart.css') }}">
@endsection

@section('content')
    <h1 class="page-title">Giỏ hàng của bạn</h1>
    
    <div id="cart-container">
        @if($cart->cartItems->count() > 0)
            <div class="cart-header">
                <div>Sản phẩm</div>
                <div>Đơn giá</div>
                <div>Số lượng</div>
                <div>Tổng tiền</div>
                <div>Thao tác</div>
            </div>
        @endif

        <div id="cart-items-list">
            @forelse($cart->cartItems as $item)
                <div class="cart-item" data-id="{{ $item->id }}">
                    <div class="item-details">
                        <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" 
                             onerror="this.src='{{ asset('assets/image/products/default.png') }}'">
                        <span>{{ $item->product->name }}</span>
                    </div>

                    <div class="item-unit-price">
                        {{ number_format($item->product->sell_price, 0, ',', '.') }}&nbsp;₫
                    </div>

                    <div class="quantity-control">
                        <button class="btn-qty-minus" data-id="{{ $item->id }}"><i class="fa-solid fa-minus"></i></button>
                        <span class="qty-val">{{ $item->quantity }}</span>
                        <button class="btn-qty-plus" data-id="{{ $item->id }}"><i class="fa-solid fa-plus"></i></button>
                    </div>

                    <div class="item-total-price">
                        <span class="item-total-price">{{ number_format($item->product->sell_price * $item->quantity, 0, ',', '.') }}&nbsp;₫</span>
                    </div>

                    <div class="remove-btn-container">
                        <button class="remove-btn" data-id="{{ $item->id }}">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <p>Giỏ hàng của bạn đang trống</p>
                    <a href="{{ route('products.index') }}" 
                       style="display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: var(--primary-color); color: white; text-decoration: none; border-radius: 5px;">
                       Tiếp tục mua sắm
                    </a>
                </div>
            @endforelse
        </div>

        @if($cart->cartItems->count() > 0)
            <div class="summary-box" id="summary-box">
                <div class="summary-row">
                    <span class="total-label">Tổng cộng:</span>
                    <span class="total-amount" id="final-total">
                        {{ number_format($total, 0, ',', '.') }}&nbsp;₫
                    </span>
                </div>
                <div class="checkout-group">
                    <a href="{{ route('checkout.index') }}" class="order-btn" style="text-decoration: none; display: block; text-align: center;">
                        Thanh toán
                    </a>
                </div>
            </div>
        @endif

        <div>
            <a href="{{ route('products.index') }}" class="continue-shopping">
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
                        const unitPriceText = row.querySelector('.item-unit-price').textContent;
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
