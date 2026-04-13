@extends('layouts.app')

@section('title', 'Giỏ hàng - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/cart.css') }}">
@endsection

@section('content')
    <div class="app-container py-5">
        <nav class="m-breadcrumb">
            <a href="{{ route('home') }}">Trang chủ</a>
            <span class="separator">/</span>
            <span class="current">Giỏ hàng</span>
        </nav>

        <h1 class="page-title">Giỏ hàng của bạn</h1>
        <p class="page-subtitle">Kiểm tra lại các sản phẩm trước khi thanh toán</p>

        @if($cart->cartItems->count() > 0)
            <div class="m-card p-0 overflow-hidden" style="border: none; background: transparent; box-shadow: none;">
                <div class="table-responsive">
                    <table class="m-table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Sản phẩm</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Số tiền</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items-list">
                            @foreach($cart->cartItems as $item)
                                <tr class="cart-item" data-id="{{ $item->id }}" data-stock="{{ $item->product->stock_quantity }}" data-price="{{ $item->product->sell_price }}">
                                    <td>
                                        <a href="{{ route('products.show', $item->product->id) }}" class="text-decoration-none color-inherit">
                                            <div class="item-details">
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="item-img"
                                                     onerror="this.src='{{ asset('storage/products/default.png') }}'">
                                                <span>{{ $item->product->name }}</span>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="text-end fw-bold text-muted">
                                        {{ number_format($item->product->sell_price, 0, ',', '.') }}&nbsp;₫
                                    </td>
                                    <td>
                                        <div class="quantity-control justify-content-center">
                                            <button class="m-btn-soft btn-qty-minus {{ $item->quantity <= 1 ? 'btn-disabled' : '' }}" data-id="{{ $item->id }}"><i class="fa-solid fa-minus"></i></button>
                                            <input type="number" class="qty-input" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" data-id="{{ $item->id }}">
                                            <button class="m-btn-soft btn-qty-plus {{ $item->quantity >= $item->product->stock_quantity ? 'btn-disabled' : '' }}" data-id="{{ $item->id }}"><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-primary">
                                        <span class="item-total-price">{{ number_format($item->product->sell_price * $item->quantity, 0, ',', '.') }}&nbsp;₫</span>
                                    </td>
                                    <td class="text-end">
                                        <button class="remove-btn" data-id="{{ $item->id }}" title="Xóa khỏi giỏ hàng">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="m-total-bar">
                    <div>
                        <a href="{{ route('products.index') }}" class="m-btn-link">
                            <i class="fa-solid fa-arrow-left me-2"></i>Tiếp tục mua hàng
                        </a>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-3">
                        <div class="m-grand-total">
                            <small>TỔNG CỘNG:</small>
                            <span id="final-total">{{ number_format($total, 0, ',', '.') }}&nbsp;₫</span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="m-btn m-btn-primary px-5 py-3">
                            <i class="fa-solid fa-check-circle me-2"></i>Tiến hành thanh toán
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="m-card text-center py-5">
                <div class="mb-4">
                    <i class="fa-solid fa-cart-shopping fa-4x text-muted opacity-25"></i>
                </div>
                <h3 class="h4 text-muted mb-3">Giỏ hàng của bạn đang trống</h3>
                <p class="text-muted mb-4">Hãy khám phá những chiếc bánh thơm ngon của Morico nhé!</p>
                <a href="{{ route('products.index') }}" class="m-btn m-btn-primary px-5">
                   Bắt đầu mua sắm
                </a>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartList = document.getElementById('cart-items-list');
        const finalTotalSpan = document.getElementById('final-total');
        const syncTimeouts = {};
        window.isCartDirty = false;

        // Safety Net: Warning when leaving with unsaved changes
        window.addEventListener('beforeunload', function (e) {
            if (window.isCartDirty) {
                e.preventDefault();
                e.returnValue = ''; // Standard way to trigger browser prompt
            }
        });

        // Cập nhật hiển thị dòng và trạng thái nút
        function updateRowDisplay(row, qty, price, stock) {
            const totalPriceSpan = row.querySelector('.item-total-price span');
            if (totalPriceSpan) totalPriceSpan.textContent = formatCurrency(price * qty);
            
            const plusBtn = row.querySelector('.btn-qty-plus');
            const minusBtn = row.querySelector('.btn-qty-minus');
            
            if (plusBtn) {
                if (qty >= stock) plusBtn.classList.add('btn-disabled');
                else plusBtn.classList.remove('btn-disabled');
            }
            if (minusBtn) {
                if (qty <= 1) minusBtn.classList.add('btn-disabled');
                else minusBtn.classList.remove('btn-disabled');
            }
            
            updateSummary();
        }

        // Kích hoạt đồng bộ hóa sau 500ms
        function triggerSync(itemId, quantity) {
            window.isCartDirty = true;
            if (syncTimeouts[itemId]) clearTimeout(syncTimeouts[itemId]);
            syncTimeouts[itemId] = setTimeout(() => {
                syncWithServer(itemId, quantity);
            }, 500);
        }

        // Hàm định dạng tiền tệ Việt Nam
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 }).format(amount) + '\u00A0₫';
        }

        // Cập nhật tổng giỏ hàng locally
        function updateSummary() {
            let total = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const qtyInput = item.querySelector('.qty-input');
                const qty = parseInt(qtyInput ? qtyInput.value : 0) || 0;
                const price = parseInt(item.getAttribute('data-price'));
                total += qty * price;
            });
            if (finalTotalSpan) {
                finalTotalSpan.textContent = formatCurrency(total);
            }
        }

        // Đồng bộ với server (Debounced)
        async function syncWithServer(itemId, quantity) {
            try {
                const response = await fetch(`{{ url('/cart/items') }}/${itemId}`, {
                    method: 'PUT',
                    keepalive: true, // Giữ request ngay cả khi đóng tab
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: quantity })
                });
                
                const res = await response.json();
                if (!res.success) {
                    showNotification(res.message, 'error');
                    window.location.reload(); // Reload để sync lại trạng thái đúng từ server
                }
            } catch (error) {
                console.error('Lỗi cập nhật giỏ hàng:', error);
            } finally {
                delete syncTimeouts[itemId];
                if (Object.keys(syncTimeouts).length === 0) {
                    window.isCartDirty = false;
                }
                if (typeof updateCartCount === 'function') updateCartCount();
            }
        }

        // Event Delegation cho +/- và Xóa
        if (cartList) {
            cartList.addEventListener('click', function(e) {
                const btnMinus = e.target.closest('.btn-qty-minus');
                const btnPlus = e.target.closest('.btn-qty-plus');
                const btnRemove = e.target.closest('.remove-btn');

                // Thay đổi số lượng qua nút bấm
                if (btnMinus || btnPlus) {
                    const row = (btnMinus || btnPlus).closest('.cart-item');
                    const id = row.getAttribute('data-id');
                    const stock = parseInt(row.getAttribute('data-stock'));
                    const price = parseInt(row.getAttribute('data-price'));
                    const qtyInput = row.querySelector('.qty-input');
                    const plusBtn = row.querySelector('.btn-qty-plus');
                    const minusBtn = row.querySelector('.btn-qty-minus');
                    
                    let currentQty = parseInt(qtyInput.value);

                    // Xử lý khi nút đang ở trạng thái 'Vô hiệu hóa logic'
                    if (btnPlus && btnPlus.classList.contains('btn-disabled')) {
                        showNotification(`Chỉ còn ${stock} sản phẩm trong kho!`, 'warning');
                        return;
                    }

                    if (btnMinus && btnMinus.classList.contains('btn-disabled')) {
                        showNotification(`Số lượng tối thiểu là 1.
                        Nếu muốn xoá sản phẩm, hãy nhấn nút <i class="fa-regular fa-trash-can"></i>`, 'info');
                        return;
                    }

                    let newQty = btnMinus ? currentQty - 1 : currentQty + 1;

                    // Fallback validation
                    if (newQty < 1 || newQty > stock) return;

                    // Cập nhật UI
                    qtyInput.value = newQty;
                    updateRowDisplay(row, newQty, price, stock);
                    
                    // Cập nhật header badge
                    if (typeof adjustCartCountLocally === 'function') {
                        adjustCartCountLocally(btnPlus ? 1 : -1);
                    }

                    // Sync với server
                    triggerSync(id, newQty);
                }

                // Xóa sản phẩm... (biến qtyToRemove được đổi tên bên dưới)

                // Xóa sản phẩm
                if (btnRemove) {
                    const id = btnRemove.getAttribute('data-id');
                    const row = btnRemove.closest('.cart-item');
                    const qtyToRemove = parseInt(row.querySelector('.qty-input').value) || 0;

                    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                        fetch(`{{ url('/cart/items') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                row.remove();
                                updateSummary();
                                // Cập nhật số lượng trên header
                                if (typeof adjustCartCountLocally === 'function') {
                                    adjustCartCountLocally(-qtyToRemove);
                                }
                                if (document.querySelectorAll('.cart-item').length === 0) {
                                    window.location.reload();
                                }
                            } else {
                                showNotification(res.message, 'error');
                            }
                        })
                        .catch(err => console.error('Lỗi xóa sản phẩm:', err));
                    }
                }
            });
        }

        // Xử lý nhập liệu trực tiếp
        if (cartList) {
            let lastValue = 0;

            cartList.addEventListener('focus', function(e) {
                if (e.target.classList.contains('qty-input')) {
                    lastValue = parseInt(e.target.value) || 0;
                }
            }, true);

            // Chặn các phím không phải số ngay từ đầu
            cartList.addEventListener('keydown', function(e) {
                if (e.target.classList.contains('qty-input')) {
                    const row = e.target.closest('.cart-item');
                    const stock = parseInt(row.getAttribute('data-stock'));
                    const qty = parseInt(e.target.value) || 0;

                    // Xử lý Up/Down arrow với thông báo
                    if (e.keyCode === 38) { // Up Arrow
                        if (qty >= stock) {
                            e.preventDefault();
                            showNotification(`Chỉ còn ${stock} sản phẩm trong kho!`, 'warning');
                        }
                    } else if (e.keyCode === 40) { // Down Arrow
                        if (qty <= 1) {
                            e.preventDefault();
                            showNotification(`Số lượng tối thiểu là 1. Nếu muốn xoá sản phẩm, hãy nhấn nút <i class="fa-regular fa-trash-can"></i>`, 'info');
                        }
                    }

                    // Cho phép: backspace, delete, tab, escape, enter
                    if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                        // Cho phép: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                        (e.keyCode === 65 && e.ctrlKey === true) ||
                        (e.keyCode === 67 && e.ctrlKey === true) ||
                        (e.keyCode === 86 && e.ctrlKey === true) ||
                        (e.keyCode === 88 && e.ctrlKey === true) ||
                        // Cho phép: home, end, left, up, right, down
                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                        return;
                    }
                    // Chặn các phím không phải số (bao gồm cả dấu chấm, phẩy, e, -, +)
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                }
            });

            cartList.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input')) {
                    const row = e.target.closest('.cart-item');
                    const id = row.getAttribute('data-id');
                    const stock = parseInt(row.getAttribute('data-stock'));
                    const price = parseInt(row.getAttribute('data-price'));
                    
                    // 1. Xóa ký tự lạ
                    let val = e.target.value.replace(/[^0-9]/g, '');
                    
                    // 2. Xử lý số 0 hoặc trống ngay lập tức (Auto-correct)
                    if (val === '0') {
                        showNotification(`Số lượng tối thiểu là 1. Nếu muốn xoá sản phẩm, hãy nhấn nút <i class="fa-regular fa-trash-can"></i>`, 'info');
                        val = '1';
                    }
                    
                    // 3. Xóa số 0 ở đầu (002 -> 2)
                    if (val.length > 1 && val.startsWith('0')) {
                        val = parseInt(val).toString();
                    }
                    
                    e.target.value = val;
                    if (val === '') return;

                    let qty = parseInt(val);
                    if (isNaN(qty)) return;

                    // 4. Validation giới hạn trên
                    if (qty > stock) {
                        showNotification(`Chỉ còn ${stock} sản phẩm trong kho!`, 'warning');
                        qty = stock;
                        e.target.value = qty;
                    }

                    // Cập nhật UI
                    updateRowDisplay(row, qty, price, stock);
                    
                    // Cập nhật Badge (Optimistic)
                    if (typeof adjustCartCountLocally === 'function') {
                        let delta = qty - lastValue;
                        adjustCartCountLocally(delta);
                        lastValue = qty;
                    }

                    triggerSync(id, qty);
                }
            });

            cartList.addEventListener('blur', function(e) {
                if (e.target.classList.contains('qty-input')) {
                    let qty = parseInt(e.target.value);
                    if (isNaN(qty) || qty < 1) {
                        const row = e.target.closest('.cart-item');
                        const id = row.getAttribute('data-id');
                        const stock = parseInt(row.getAttribute('data-stock'));
                        const price = parseInt(row.getAttribute('data-price'));
                        
                        // Nếu reset về 1, cần tính lại delta cho badge
                        if (typeof adjustCartCountLocally === 'function') {
                            adjustCartCountLocally(1 - qty);
                        }

                        e.target.value = 1;
                        updateRowDisplay(row, 1, price, stock);
                        triggerSync(id, 1);
                    }
                }
            }, true);
        }
    });
</script>
@endsection
