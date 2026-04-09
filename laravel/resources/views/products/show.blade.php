@extends('layouts.app')

@section('title', $product->name . ' - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/product-detail.css') }}">
@endsection

@section('content')
    <div class="product-detail-container">
        <div id="product-content">
            <div class="product-detail">
                <div class="product-image">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" onerror="this.src='{{ asset('assets/image/products/default.png') }}'">
                </div>

                <div class="product-info">
                    <h1>{{ $product->name }}</h1>
                    <div class="product-tag">Trọng lượng: 150g</div>
                                  
                    <div class="price-section">
                        <span class="original-price">{{ number_format($product->sell_price, 0, ',', '.') }}&nbsp;₫</span>
                    </div>

                    <div class="quantity-section">
                        <span class="quantity-label">Số Lượng</span>
                        <div class="quantity-controls">
                            <input type="number" class="quantity-input" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                            <span class="stock-info"> {{ $product->stock_quantity }} sản phẩm có sẵn</span>
                        </div>
                        <div class="stock-warning" style="display: none; color: #B22222; margin-top: 10px;"></div>
                    </div>

                    <div class="action-buttons">
                        <button class="btn-add-cart" id="btn-add-cart" data-id="{{ $product->id }}" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                            Thêm Vào Giỏ Hàng
                        </button>
                        <button class="btn-buy-now" id="btn-buy-now" data-id="{{ $product->id }}" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                            Mua Ngay
                        </button>
                    </div>
                </div>
            </div>

            <div class="product-details-section">
                <h2 class="section-title">Mô Tả</h2>
                <div class="description-label">
                    {!! $product->description !!}
                </div>

                <h2 class="section-title">Thông Tin Sản Phẩm</h2>
                <div class="details-grid" id="product-details">
                    <div class="detail-label">Mã sản phẩm:</div>
                    <div class="detail-value">{{ $product->sku }}</div>
                    
                    <div class="detail-label">Loại bánh:</div>
                    <div class="detail-value">{{ $product->category->name }}</div>
                    
                    <div class="detail-label">Nhà cung cấp:</div>
                    <div class="detail-value">{{ $product->supplier ?? 'Morico Bakery' }}</div>
                    
                    <div class="detail-label">Hạn sử dụng:</div>
                    <div class="detail-value">Xem trên bao bì sản phẩm</div>
                </div>

                <h2 class="section-title">Hướng dẫn sử dụng</h2>
                <p>Thưởng thức trực tiếp hoặc kèm trà nhài để tăng hương vị.</p>
              
                <h2 class="section-title">Hướng dẫn bảo quản</h2>
                <p>Giữ bánh trong hộp kín, tránh ánh nắng trực tiếp.</p>
            </div>

            @if($relatedProducts->count() > 0)
                <div class="related-products">
                    <h2>CÓ THỂ BẠN QUAN TÂM</h2>
                    <div class="related-grid" id="related-products">
                        @foreach($relatedProducts as $related)
                            <div class="related-product-card">
                                <img src="{{ asset($related->image) }}" alt="{{ $related->name }}" onerror="this.src='{{ asset('assets/image/products/default.png') }}'">
                                <h4>{{ $related->name }}</h4>
                                <div class="price">{{ number_format($related->sell_price, 0, ',', '.') }}&nbsp;₫</div>
                                <a href="{{ route('products.show', $related->id) }}" class="view-detail-btn">Xem Chi Tiết</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAddCart = document.getElementById('btn-add-cart');
        const btnBuyNow = document.getElementById('btn-buy-now');

        if (btnAddCart) {
            btnAddCart.addEventListener('click', async function() {
                const id = this.getAttribute('data-id');
                const quantity = document.getElementById('quantity').value;
                
                try {
                    const response = await fetch('{{ route("cart.items.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: id, quantity: quantity })
                    });

                    const data = await response.json();
                    if (data.success) {
                        showNotification(data.message);
                        if (typeof adjustCartCountLocally === 'function') {
                            adjustCartCountLocally(parseInt(quantity));
                        }
                    } else {
                        if (response.status === 401) {
                            window.location.href = '{{ route("login") }}';
                        } else {
                            showNotification(data.message || 'Có lỗi xảy ra.', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Error adding to cart:', error);
                    showNotification('Không thể thêm sản phẩm vào giỏ hàng.', 'error');
                }
            });
        }

        if (btnBuyNow) {
            btnBuyNow.addEventListener('click', async function() {
                const id = this.getAttribute('data-id');
                const quantity = document.getElementById('quantity').value;
                
                try {
                    const response = await fetch('{{ route("cart.items.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: id, quantity: quantity })
                    });

                    const data = await response.json();
                    if (data.success) {
                        // Redirect to checkout after successful add
                        window.location.href = '{{ route("checkout.index") }}';
                    } else {
                        if (response.status === 401) {
                            window.location.href = '{{ route("login") }}';
                        } else {
                            alert(data.message || 'Có lỗi xảy ra.');
                        }
                    }
                } catch (error) {
                    console.error('Error in buy now:', error);
                    alert('Không thể thực hiện mua ngay.');
                }
            });
        }
    });
</script>
@endsection
