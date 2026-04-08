@extends('layouts.app')

@section('title', 'Sản Phẩm - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/product-list.css') }}">
@endsection

@section('content')
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
                                {{ request('category') == $category->slug ? 'checked' : '' }}
                                onchange="this.form.submit()">
                            <label for="category_{{ $category->slug }}">{{ $category->name }}</label>
                        </div>
                    @endforeach
                </div>

                <div class="divider"></div>

                <div class="filter-section">
                    <h3>Khoảng Giá</h3>
                    <div class="price-input-group">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Từ" class="price-input">
                        <div class="price-separator">-</div>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Đến" class="price-input">
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="filter-btn filter-apply-btn">Áp dụng</button>
                    <a href="{{ route('products.index') }}" class="filter-btn filter-reset-btn text-center text-decoration-none">Đặt lại</a>
                </div>
            </form>
        </aside>

        <!-- PRODUCTS AREA -->
        <main class="products-area">
            <h1 class="page-title">Sản phẩm bánh Trung thu</h1>
            <p class="page-subtitle">Thưởng thức hương vị Tết Trung thu truyền thống với những chiếc bánh thơm ngon</p>
            
            @if(request('search'))
                <div class="search-results-info text-center mb-4">
                    Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm cho từ khóa "<strong>{{ request('search') }}</strong>"
                </div>
            @endif

            <div class="products-grid" id="product-list">
                @forelse($products as $product)
                    <div class="product-card">
                        @if($product->stock_quantity <= 0)
                            <div class="stock-badge">Hết hàng</div>
                        @endif

                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-img" onerror="this.src='{{ asset('assets/image/products/default.jpg') }}'">
                        
                        <h3>{{ $product->name }}</h3>
                        
                        <div class="description">
                            {!! Str::limit(strip_tags($product->description), 100) !!}
                        </div>
                        
                        <div class="price-container">
                            <span class="price">{{ number_format($product->sell_price, 0, ',', '.') }}&nbsp;₫</span>
                        </div>
                        
                        <div class="product-actions">
                            <a href="{{ route('products.show', $product->id) }}" class="action-icon">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <button class="action-icon border-0 bg-transparent btn-add-to-cart" 
                                    data-id="{{ $product->id }}"
                                    {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}
                                    style="{{ $product->stock_quantity <= 0 ? 'opacity:0.5; cursor:not-allowed;' : '' }}">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                        <p style="color: #5a2d0c; font-size: 18px; margin-bottom: 20px;">
                            Không tìm thấy sản phẩm nào phù hợp.
                        </p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary" style="background-color: #8B0000; border: none;">Hiển thị tất cả sản phẩm</a>
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
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                alert('Chức năng thêm vào giỏ hàng sẽ được hoàn thiện ở Batch 4. Sản phẩm ID: ' + id);
            });
        });
    });
</script>
@endsection
