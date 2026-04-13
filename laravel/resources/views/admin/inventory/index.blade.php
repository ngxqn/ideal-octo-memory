@extends('layouts.admin')

@section('title', 'Quản lý tồn kho - MORICO')
@section('page_title')
    <i class="fa-solid fa-warehouse me-2" style="color: var(--brand-gold);"></i> Quản lý tồn kho
@endsection

@section('content')

<!-- Báo Cáo Tồn Kho Hiện Tại -->
<div class="accordion mb-4 shadow-sm border-0" id="accordionStockStatus">
    <div class="accordion-item border">
        <h2 class="accordion-header">
            <button class="accordion-button fw-bold text-secondary text-uppercase small letter-spacing-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStockStatus" aria-expanded="true">
                <i class="fa-solid fa-box-open me-2 text-primary"></i> Báo Cáo Tồn Kho Hiện Tại
            </button>
        </h2>
        <div id="collapseStockStatus" class="accordion-collapse collapse show">
            <div class="accordion-body bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top mt-2">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">Mã SP</th>
                                <th style="width: 25%;">Tên Bánh</th>
                                <th style="width: 15%;">Loại</th>
                                <th class="text-center" style="width: 10%;">Tồn kho</th>
                                <th class="text-center" style="width: 15%;">Trạng thái</th>
                                <th class="text-center" style="width: 15%;">Cập nhật cuối</th>
                                <th class="text-center" style="width: 10%;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $isOutOfStock = $product->stock_quantity <= 0;
                                    $isLowStock = $product->stock_quantity <= $product->low_stock_threshold;
                                    
                                    $rowClass = '';
                                    if ($isOutOfStock) $rowClass = 'table-danger';
                                    elseif ($isLowStock) $rowClass = 'table-warning';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="fw-bold text-primary small">{{ $product->sku }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $product->name }}</span>
                                        @if($product->is_hidden)
                                            <span class="badge bg-secondary ms-1 small">Ẩn</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $product->category->name }}</td>
                                    <td class="text-center fw-bold {{ $isLowStock ? 'text-danger' : '' }}">
                                        {{ number_format($product->stock_quantity) }}
                                    </td>
                                    <td class="text-center">
                                        @if($isOutOfStock)
                                            <span class="badge bg-danger rounded-pill">HẾT HÀNG</span>
                                        @elseif($isLowStock)
                                            <span class="badge bg-warning text-dark rounded-pill">SẮP HẾT ( < {{ $product->low_stock_threshold }})</span>
                                        @else
                                            <span class="badge bg-success rounded-pill">Bình thường</span>
                                        @endif
                                    </td>
                                    <td class="text-center small text-muted">
                                        {{ $product->updated_at ? $product->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-outline-primary btn-sm px-2 py-0" title="Nhập hàng qua PO">
                                            <i class="fa-solid fa-truck-loading small"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Không có dữ liệu sản phẩm.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Phần Tra Cứu & Báo Cáo (Accordion) -->
<div class="accordion mb-4 shadow-sm border-0" id="accordionInventory">
    <div class="accordion-item border">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAnalytics">
                <i class="fa-solid fa-chart-pie me-2 text-primary"></i> Tra cứu & Báo cáo Nhập - Xuất - Tồn (Phát sinh)
            </button>
        </h2>
        <div id="collapseAnalytics" class="accordion-collapse collapse" data-bs-parent="#accordionInventory">
            <div class="accordion-body bg-light">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="small fw-bold mb-1">Từ ngày</label>
                        <input type="date" id="reportStartDate" class="form-control" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold mb-1">Đến ngày</label>
                        <input type="date" id="reportEndDate" class="form-control" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100 fw-bold" id="generateReportBtn">
                            <i class="fa-solid fa-play me-1"></i> Tạo Báo Cáo
                        </button>
                    </div>
                </div>
                
                <div id="analyticsReportContainer" class="mt-4 d-none">
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded bg-white text-center shadow-sm">
                                <div class="text-muted small mb-1">Tổng nhập phát sinh</div>
                                <h4 class="text-success mb-0 fw-bold" id="stat-total-in">0</h4>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 border rounded bg-white text-center shadow-sm">
                                <div class="text-muted small mb-1">Tổng xuất phát sinh</div>
                                <h4 class="text-danger mb-0 fw-bold" id="stat-total-out">0</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive bg-white rounded border shadow-sm" style="max-height: 400px;">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Mã SP</th>
                                    <th>Tên Sản phẩm</th>
                                    <th class="text-center">Tổng Nhập (+)</th>
                                    <th class="text-center">Tổng Xuất (-)</th>
                                    <th class="text-center">Tồn Hiện Tại</th>
                                </tr>
                            </thead>
                            <tbody id="reportResultsBody"></tbody>
                        </table>
                    </div>
                </div>
                
                <div id="reportLoader" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2 text-muted small">Đang tổng hợp dữ liệu...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Nhật Ký Biến Động Kho (Ledger) -->
<div class="accordion mb-4 shadow-sm border-0" id="accordionLedger">
    <div class="accordion-item border">
        <h2 class="accordion-header">
            <button class="accordion-button fw-bold text-secondary text-uppercase small letter-spacing-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLedger" aria-expanded="true">
                <i class="fa-solid fa-history me-2 text-primary"></i> Nhật Ký Biến Động Kho (Ledger)
            </button>
        </h2>
        <div id="collapseLedger" class="accordion-collapse collapse show">
            <div class="accordion-body bg-white">
                <!-- Bộ lọc Ledger -->
                <form method="GET" action="{{ route('admin.inventory.index') }}" class="row g-2 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Sản phẩm:</label>
                        <select name="product_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Loại biến động:</label>
                        <select name="ref_type" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="product_init" {{ request('ref_type') == 'product_init' ? 'selected' : '' }}>Khởi tạo</option>
                            <option value="goods_receipt" {{ request('ref_type') == 'goods_receipt' ? 'selected' : '' }}>Nhập hàng (PO)</option>
                            <option value="order_placed" {{ request('ref_type') == 'order_placed' ? 'selected' : '' }}>Xuất hàng (Order)</option>
                            <option value="order_cancelled" {{ request('ref_type') == 'order_cancelled' ? 'selected' : '' }}>Hoàn kho (Hủy đơn)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Từ ngày:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Đến ngày:</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-1 mt-auto">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold">Tìm kiếm</button>
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm flex-grow-1 fw-bold">Đặt lại</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle border-top">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Thời gian</th>
                                <th style="width: 20%;">Sản phẩm</th>
                                <th class="text-center" style="width: 15%;">Loại</th>
                                <th class="text-center" style="width: 15%;">Biến động</th>
                                <th class="text-end" style="width: 15%;">Đơn giá (Log)</th>
                                <th style="width: 20%;">Tham chiếu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                @php
                                    $refText = [
                                        'product_init' => ['text' => 'Khởi tạo', 'class' => 'bg-secondary'],
                                        'goods_receipt' => ['text' => 'Nhập hàng', 'class' => 'bg-success'],
                                        'order_placed' => ['text' => 'Xuất hàng', 'class' => 'bg-danger'],
                                        'order_cancelled' => ['text' => 'Hoàn kho', 'class' => 'bg-info text-dark'],
                                    ];
                                    $r = $refText[$log->reference_type] ?? ['text' => $log->reference_type, 'class' => 'bg-light text-dark'];
                                    
                                    $isPositive = $log->change_amount > 0;
                                @endphp
                                <tr>
                                    <td class="small text-muted">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td class="fw-bold">{{ $log->product->name }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $r['class'] }} small">{{ $r['text'] }}</span>
                                    </td>
                                    <td class="text-center fw-bold {{ $isPositive ? 'text-success' : 'text-danger' }}">
                                        {{ $isPositive ? '+' : '' }}{{ $log->change_amount }}
                                    </td>
                                    <td class="text-end small">
                                        @if($log->unit_price > 0)
                                            {{ number_format($log->unit_price, 0, ',', '.') }}&nbsp;₫
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="small text-muted">
                                        @if($log->reference_type == 'goods_receipt')
                                            PN{{ str_pad($log->reference_id, 5, '0', STR_PAD_LEFT) }}
                                        @elseif(in_array($log->reference_type, ['order_placed', 'order_cancelled']))
                                            MD{{ str_pad($log->reference_id, 5, '0', STR_PAD_LEFT) }}
                                        @else
                                            #{{ $log->reference_id }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Chưa có nhật ký biến động kho.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1060;">
    <div id="liveToast" class="toast hide border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-medium" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const ROUTES = {
        report: "{{ route('admin.inventory.report') }}"
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('liveToast');
        const toastBody = document.getElementById('toastMessage');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        
        toastEl.className = 'toast show border-0 shadow-lg';
        if (type === 'success') toastEl.classList.add('text-bg-success');
        else if (type === 'error' || type === 'danger') toastEl.classList.add('text-bg-danger');
        else toastEl.classList.add('text-bg-info');
        
        toastBody.textContent = message;
        toast.show();
    }

    const formatter = new Intl.NumberFormat('vi-VN');

    document.getElementById('generateReportBtn').addEventListener('click', async function() {
        const startDate = document.getElementById('reportStartDate').value;
        const endDate = document.getElementById('reportEndDate').value;
        
        if (!startDate || !endDate) {
            showToast("Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.", "warning");
            return;
        }

        const btn = this;
        const loader = document.getElementById('reportLoader');
        const container = document.getElementById('analyticsReportContainer');
        const body = document.getElementById('reportResultsBody');

        btn.disabled = true;
        loader.classList.remove('d-none');
        container.classList.add('d-none');
        body.innerHTML = '';

        try {
            const url = new URL(ROUTES.report, window.location.origin);
            url.searchParams.append('start_date', startDate);
            url.searchParams.append('end_date', endDate);

            const res = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (res.ok && data.success) {
                document.getElementById('stat-total-in').textContent = '+' + data.summary.total_in;
                document.getElementById('stat-total-out').textContent = '-' + data.summary.total_out;

                if (data.data.length === 0) {
                    body.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Không có biến động kho nào trong khoảng thời gian này.</td></tr>';
                } else {
                    data.data.forEach(item => {
                        body.innerHTML += `
                            <tr>
                                <td class="small fw-bold text-primary">${item.product.sku}</td>
                                <td>${item.product.name}</td>
                                <td class="text-center text-success fw-bold">+${item.total_in}</td>
                                <td class="text-center text-danger fw-bold">-${item.total_out}</td>
                                <td class="text-center fw-medium">${item.product.stock_quantity}</td>
                            </tr>
                        `;
                    });
                }
                container.classList.remove('d-none');
            } else {
                showToast(data.message || "Lỗi khi tạo báo cáo.", "danger");
            }
        } catch (e) {
            console.error(e);
            showToast("Lỗi kết nối hoặc hệ thống.", "danger");
        } finally {
            btn.disabled = false;
            loader.classList.add('d-none');
        }
    });
</script>
@endsection
