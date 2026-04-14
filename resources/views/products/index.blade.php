@extends('layouts.app')

@section('title', 'Sản Phẩm - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/product-list.css') }}">
@endsection

@section('content')
    <div class="app-container py-5">
        <h1 class="page-title">Sản phẩm bánh Trung thu</h1>
        <p class="page-subtitle mb-5">Thưởng thức hương vị Tết Trung thu truyền thống với những chiếc bánh thơm ngon</p>

        <div class="main-content">
        <!-- SIDEBAR FILTER -->
        <aside class="filter-sidebar">
            <form action="{{ route('products.index') }}" method="GET">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <div class="filter-section">
                    <h3>Loại bánh</h3>
                    @foreach($categories as $category)
                        <div class="filter-option">
                            <input type="checkbox" id="category_{{ $category->slug }}" name="category" value="{{ $category->slug }}" 
                                {{ request('category') == $category->slug ? 'checked' : '' }}>
                            <label for="category_{{ $category->slug }}">{{ $category->name }}</label>
                        </div>
                    @endforeach
                </div>

                <div class="divider"></div>

                <div class="filter-section">
                    <h3>Khoảng Giá</h3>
                    <div class="price-input-group">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Từ" class="price-input">
                        <div class="price-separator">|</div>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Đến" class="price-input">
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="m-btn m-btn-primary w-100">Áp dụng</button>
                    <a href="{{ route('products.index') }}" class="m-btn m-btn-outline w-100">Đặt lại</a>
                </div>
            </form>
        </aside>

        <!-- PRODUCTS AREA -->
        <main class="products-area">
            
            @if(request('search'))
                <div class="search-results-info text-center mb-4">
                    Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm cho từ khóa "<strong>{{ request('search') }}</strong>"
                </div>
            @endif

            <div class="products-grid" id="product-list">
                @forelse($products as $product)
                    <div class="product-card">
                        <div class="m-badge m-badge-category category-badge">{{ $product->category->name }}</div>
                        @if($product->stock_quantity <= 0)
                            <div class="m-badge m-badge-out stock-badge">Hết hàng</div>
                        @endif

                        <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none color-inherit">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-img" onerror="this.onerror=null;this.src='{{ asset('storage/products/default.png') }}'">
                            <h3>{{ $product->name }}</h3>
                        </a>
                        
                        <div class="description">
                            {!! Str::limit(strip_tags($product->description), 100) !!}
                        </div>
                        
                        <div class="product-actions mt-auto">
                            <span class="price">{{ number_format($product->sell_price, 0, ',', '.') }}&nbsp;₫</span>
                            <button class="m-btn-cart btn-add-to-cart" 
                                    data-id="{{ $product->id }}"
                                    title="Thêm vào giỏ hàng"
                                    {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                <i class="fa-solid fa-cart-shopping"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                        <p style="color: #5a2d0c; font-size: 18px; margin-bottom: 20px;">
                            Không tìm thấy sản phẩm nào phù hợp.
                        </p>
                        <a href="{{ route('products.index') }}" class="m-btn m-btn-primary">Hiển thị tất cả sản phẩm</a>
                    </div>
                @endforelse
            </div>

            <!-- PAGINATION -->
            {{ $products->links('vendor.pagination.morico') }}
        </main>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Placeholder for addToCart (will be fully implemented in Batch 4/Cart)
        const addToCartBtns = document.querySelectorAll('.btn-add-to-cart');
        
        addToCartBtns.forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.getAttribute('data-id');
                
                try {
                    const response = await fetch('{{ route("cart.items.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: id, quantity: 1 })
                    });

                    const data = await response.json();
                    if (data.success) {
                        showNotification(data.message);
                        if (typeof adjustCartCountLocally === 'function') {
                            adjustCartCountLocally(1);
                        }
                    } else {
                        // Nếu chưa đăng nhập, redirect về login
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
        });
    });
</script>
@endsection
