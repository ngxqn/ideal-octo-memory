@extends('layouts.admin')

@section('title', 'Bảng điều khiển - MORICO Admin')

@section('page_title')
    <i class="fa-solid fa-home me-2" style="color: var(--brand-gold);"></i> Bảng điều khiển
@endsection

@section('styles')
<style>
    .stat-card h2 {
        font-size: 2.5rem;
        margin-bottom: 0.2rem;
        color: var(--brand-crimson);
        font-weight: bold;
    }

    .stat-card span {
        color: var(--muted-brown);
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-sm border p-4">
            <h2>{{ $totalProducts }}</h2><span>Sản phẩm</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-sm border p-4">
            <h2>{{ $totalOrders }}</h2><span>Đơn hàng</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-sm border p-4">
            <h2>{{ $pendingOrders }}</h2><span>Chờ xử lý</span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-sm border p-4">
            <h2>{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h2><span>Doanh thu</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Low Stock -->
    <div class="col-lg-6">
        <div class="card shadow-sm border p-4 h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom-0 pt-0 pb-3">
                <h3 class="h6 mb-0 fw-bold">
                    <i class="fa-solid fa-warehouse text-warning me-2"></i>
                    Tồn kho thấp (< {{ $lowStockThreshold }})
                </h3>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    Xem tất cả
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 mt-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Còn lại</th>
                                <th class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $product)
                                <tr>
                                    <td><div class="fw-bold">{{ $product->name }}</div></td>
                                    <td class="text-center fw-bold text-danger">{{ $product->stock_quantity }}</td>
                                    <td class="text-center">
                                        @if($product->stock_quantity == 0)
                                            <span class="badge rounded-pill bg-danger">HẾT HÀNG</span>
                                        @else
                                            <span class="badge rounded-pill bg-warning text-dark">SẮP HẾT!</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Không có sản phẩm sắp hết hàng</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Zero/Low Profit -->
    <div class="col-lg-6">
        <div class="card shadow-sm border p-4 h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom-0 pt-0 pb-3">
                <h3 class="h6 mb-0 fw-bold">
                    <i class="fa-solid fa-dollar-sign text-success me-2"></i>
                    Sản phẩm lợi nhuận thấp
                </h3>
                <a href="{{ route('admin.pricing.index') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    Xem tất cả
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 mt-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-end">Giá vốn</th>
                                <th class="text-end">Giá bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowProfitProducts as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-end text-danger fw-bold">{{ number_format($product->base_price, 0, ',', '.') }} ₫</td>
                                    <td class="text-end">{{ number_format($product->sell_price, 0, ',', '.') }} ₫</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Không có sản phẩm lợi nhuận thấp</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Recent Orders -->
    <div class="col-lg-12">
        <div class="card shadow-sm border p-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom-0 pt-0 pb-3">
                <h3 class="h6 mb-0 fw-bold">
                    <i class="fa-solid fa-shopping-cart text-info me-2"></i>
                    Đơn hàng gần đây
                </h3>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    Xem tất cả
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 mt-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td><span class="fw-bold">#{{ $order->id }}</span></td>
                                    <td>{{ $order->receiver_name }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = match($order->status) {
                                                'pending' => 'bg-warning text-dark',
                                                'confirmed' => 'bg-primary',
                                                'delivered' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            $statusText = match($order->status) {
                                                'pending' => 'Chờ xử lý',
                                                'confirmed' => 'Đã xác nhận',
                                                'delivered' => 'Đã giao',
                                                'cancelled' => 'Đã hủy',
                                                default => $order->status
                                            };
                                        @endphp
                                        <span class="badge rounded-pill {{ $badgeClass }}">{{ $statusText }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Chưa có đơn hàng nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-auto text-center py-3 text-muted small border-top">
    <p>© {{ date('Y') }} Hệ thống Quản lý Bánh Trung Thu MORICO | Phiên bản 2.1.0</p>
</div>
@endsection
