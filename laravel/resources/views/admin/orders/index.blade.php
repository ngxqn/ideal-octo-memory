@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng - MORICO')
@section('page_title')
    <i class="fa-solid fa-shopping-cart me-2" style="color: var(--brand-gold);"></i> Quản lý đơn hàng
@endsection

@section('content')

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
        <h3 class="h5 mb-0 fw-bold"><i class="fa-solid fa-search text-primary me-2"></i> Tìm kiếm đơn hàng</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}" id="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Tìm kiếm từ khóa:</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Mã đơn, tên, sđt...">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Trạng thái:</label>
                    <select class="form-select" name="status">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Đã giao thành công</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Từ ngày:</label>
                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Đến ngày:</label>
                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" title="Tìm kiếm"><i class="fa-solid fa-search me-1"></i> Tìm kiếm</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100 fw-bold" title="Đặt lại"><i class="fa-solid fa-redo"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
        <h3 class="h5 mb-0 fw-bold"><i class="fa-solid fa-table text-primary me-2"></i> Danh sách Đơn hàng</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-top mt-2">
                <thead class="table-light">
                    @php
                        $sort = request('sort', 'created_at');
                        $dir = request('direction', 'desc');
                        $columns = [
                            'id' => ['label' => 'Mã Đơn', 'width' => '7%'],
                            'receiver_name' => ['label' => 'Khách hàng', 'width' => '13%'],
                            'receiver_phone' => ['label' => 'SĐT', 'width' => '9%'],
                            'shipping_address' => ['label' => 'Đ/c', 'width' => '12%'],
                            'shipping_commune' => ['label' => 'Phường/Xã/Đặc khu', 'width' => '12%'],
                            'shipping_city' => ['label' => 'Tỉnh/TP', 'width' => '9%'],
                            'total_amount' => ['label' => 'Tổng tiền', 'width' => '9%'],
                            'created_at' => ['label' => 'Ngày đặt', 'width' => '10%'],
                            'status' => ['label' => 'Trạng thái', 'width' => '12%'],
                        ];
                    @endphp
                    <tr>
                        @foreach($columns as $col => $info)
                            <th style="width: {{ $info['width'] }};">
                                @php
                                    $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                                    $icon = 'fa-sort text-muted opacity-50';
                                    if ($sort === $col) {
                                        $icon = $dir === 'asc' ? 'fa-sort-up text-primary' : 'fa-sort-down text-primary';
                                    }
                                @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $col, 'direction' => $nextDir, 'page' => 1]) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                    <span class="small fw-bold">{{ $info['label'] }}</span>
                                    <i class="fa-solid {{ $icon }} ms-1"></i>
                                </a>
                            </th>
                        @endforeach
                        <th style="width: 7%;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $statusMap = [
                                'pending' => ['text' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
                                'confirmed' => ['text' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
                                'delivered' => ['text' => 'Đã giao', 'class' => 'bg-success'],
                                'cancelled' => ['text' => 'Đã hủy', 'class' => 'bg-danger'],
                            ];
                            $s = $statusMap[$order->status] ?? $statusMap['pending'];
                        @endphp
                        <tr>
                            <td class="fw-bold text-primary small">MD{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="small">{{ $order->receiver_name }}</td>
                            <td class="small">{{ $order->receiver_phone }}</td>
                            <td class="small text-truncate" title="{{ $order->shipping_address }}" style="max-width: 120px;">{{ $order->shipping_address }}</td>
                            <td class="small text-truncate" title="{{ $order->shipping_commune }}" style="max-width: 120px;">{{ $order->shipping_commune }}</td>
                            <td class="small">{{ $order->shipping_city }}</td>
                            <td class="fw-bold text-danger small">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                            <td class="small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center"><span class="badge {{ $s['class'] }} small">{{ $s['text'] }}</span></td>
                            <td class="text-end">
                                <button class="btn btn-info btn-sm text-white py-0 px-2" onclick="viewOrderDetails({{ $order->id }})" title="Chi tiết">
                                    <i class="fa-solid fa-list small"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-1 opacity-25 mb-3 d-block"></i>
                                Không tìm thấy đơn hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="order-detail-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary" id="detail-modal-title">Chi tiết Đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-info-circle text-primary me-2"></i> Thông tin tổng quan</h6>
                    <div class="row g-3 small">
                        <div class="col-sm-6 border-bottom pb-2">
                            <span class="text-muted d-block">Mã đơn:</span>
                            <span class="fw-bold fs-6" id="modal-order-id"></span>
                        </div>
                        <div class="col-sm-6 border-bottom pb-2">
                            <span class="text-muted d-block">Ngày đặt:</span>
                            <span class="fw-bold fs-6" id="modal-order-date"></span>
                        </div>
                        <div class="col-sm-6 border-bottom pb-2">
                            <span class="text-muted d-block">Tổng tiền:</span>
                            <span class="fw-bold fs-6 text-danger" id="modal-total"></span>
                        </div>
                        <div class="col-sm-6 border-bottom pb-2">
                            <span class="text-muted d-block">Trạng thái:</span>
                            <div id="modal-status-container" class="mt-1"></div>
                        </div>
                        <div class="col-12 border-bottom pb-2">
                            <span class="text-muted d-block">Phương thức thanh toán:</span>
                            <span class="fw-bold" id="modal-payment"></span>
                        </div>
                        <div class="col-12 border-bottom pb-2">
                            <span class="text-muted d-block">Ghi chú từ khách:</span>
                            <span class="fw-medium text-warning-emphasis" id="modal-note"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-truck text-primary me-2"></i> Thông tin giao hàng</h6>
                    <div class="row g-3 small">
                        <div class="col-sm-6 border-bottom pb-2">
                            <span class="text-muted d-block">Người nhận:</span>
                            <span class="fw-bold" id="modal-customer"></span>
                        </div>
                        <div class="col-sm-6 border-bottom pb-2">
                            <span class="text-muted d-block">Điện thoại:</span>
                            <span class="fw-bold" id="modal-phone"></span>
                        </div>
                        <div class="col-12 mb-2 pb-2">
                            <span class="text-muted d-block">Địa chỉ đầy đủ:</span>
                            <span class="fw-bold" id="modal-address"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-box text-primary me-2"></i> Sản phẩm</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody id="modal-products"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success d-none fw-bold" id="btn-update-status" onclick="confirmStatus()">Lưu Trạng Thái Mới</button>
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
    const csrfToken = '{{ csrf_token() }}';
    const formatter = new Intl.NumberFormat('vi-VN');
    
    let currentOrder = null;
    let detailModal = null;

    const MAP_STATUS = {
        'pending': { text: 'Chờ xác nhận', class: 'bg-warning text-dark' },
        'confirmed': { text: 'Đã xác nhận', class: 'bg-info text-dark' },
        'delivered': { text: 'Đã giao thành công', class: 'bg-success text-white' },
        'cancelled': { text: 'Đã hủy', class: 'bg-danger text-white' }
    };

    // Strict 1-Way Machine
    const VALID_TRANSITIONS = {
        'pending': ['confirmed', 'cancelled'],
        'confirmed': ['delivered', 'cancelled'],
        'delivered': [],
        'cancelled': []
    };

    document.addEventListener('DOMContentLoaded', function () {
        detailModal = new bootstrap.Modal(document.getElementById('order-detail-modal'));
    });

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('liveToast');
        const toastBody = document.getElementById('toastMessage');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        
        toastEl.className = 'toast show border-0 shadow-lg';
        if (type === 'success') {
            toastEl.classList.add('text-bg-success');
        } else if (type === 'danger' || type === 'error') {
            toastEl.classList.add('text-bg-danger');
        } else if (type === 'warning') {
            toastEl.classList.add('text-bg-warning');
        } else {
            toastEl.classList.add('text-bg-info');
        }
        
        toastBody.textContent = message;
        toast.show();
    }

    const ROUTES = {
        show: "{{ route('admin.orders.show', ':id') }}",
        updateStatus: "{{ route('admin.orders.update-status', ':id') }}"
    };

    async function viewOrderDetails(orderId) {
        try {
            const url = ROUTES.show.replace(':id', orderId);
            const res = await fetch(url);
            const data = await res.json();

            if (res.ok && data.success) {
                currentOrder = data.order;
                renderModal();
                detailModal.show();
            } else {
                showToast("Lỗi tải đơn hàng.", "danger");
            }
        } catch (e) {
            showToast("Lỗi kết nối.", "danger");
        }
    }

    function renderModal() {
        const o = currentOrder;
        
        document.getElementById('detail-modal-title').textContent = `Chi tiết Đơn hàng MD${String(o.id).padStart(5, '0')}`;
        document.getElementById('modal-order-id').textContent = `MD${String(o.id).padStart(5, '0')}`;
        document.getElementById('modal-order-date').textContent = new Date(o.created_at).toLocaleString('vi-VN');
        document.getElementById('modal-total').textContent = formatter.format(o.total_amount) + ' ₫';
        document.getElementById('modal-payment').textContent = o.payment_method;
        document.getElementById('modal-note').textContent = o.note || '(Không có ghi chú)';
        
        document.getElementById('modal-customer').textContent = o.receiver_name;
        document.getElementById('modal-phone').textContent = o.receiver_phone;
        document.getElementById('modal-address').textContent = `${o.shipping_address}, ${o.shipping_commune}, ${o.shipping_city}`;

        renderStatusDropdown();

        // Products
        const tbody = document.getElementById('modal-products');
        tbody.innerHTML = '';
        const items = o.order_details || o.details || [];
        items.forEach((d, index) => {
            tbody.innerHTML += `
                <tr>
                    <td class="text-muted">${index + 1}</td>
                    <td class="fw-bold text-primary">${d.product_name}</td>
                    <td class="text-center">${d.quantity}</td>
                    <td class="text-end">${formatter.format(d.unit_price)} ₫</td>
                    <td class="text-end fw-bold">${formatter.format(d.subtotal)} ₫</td>
                </tr>
            `;
        });
    }

    function renderStatusDropdown() {
        const o = currentOrder;
        const container = document.getElementById('modal-status-container');
        const btnUpdate = document.getElementById('btn-update-status');
        const allowedNext = VALID_TRANSITIONS[o.status] || [];

        if (allowedNext.length === 0) {
            container.innerHTML = `<span class="badge ${MAP_STATUS[o.status].class} fs-6">${MAP_STATUS[o.status].text} (Chốt)</span>`;
            btnUpdate.classList.add('d-none');
            return;
        }

        let html = `<select class="form-select form-select-sm fw-bold border-primary shadow-sm" id="modal-status-select" onchange="checkStatusChange()">`;
        html += `<option value="${o.status}" selected>${MAP_STATUS[o.status].text} (Hiện tại)</option>`;
        
        allowedNext.forEach(s => {
            html += `<option value="${s}">${MAP_STATUS[s].text}</option>`;
        });
        
        html += `</select>`;
        container.innerHTML = html;
        btnUpdate.classList.add('d-none'); // Hide initially until changed
    }

    function checkStatusChange() {
        const select = document.getElementById('modal-status-select');
        const btnUpdate = document.getElementById('btn-update-status');
        
        if (select.value !== currentOrder.status) {
            btnUpdate.classList.remove('d-none');
        } else {
            btnUpdate.classList.add('d-none');
        }
    }

    async function confirmStatus() {
        const newStatus = document.getElementById('modal-status-select').value;
        const oldStatusObj = MAP_STATUS[currentOrder.status];
        const newStatusObj = MAP_STATUS[newStatus];

        let msg = `Cập nhật trạng thái đổi từ [${oldStatusObj.text}] sang [${newStatusObj.text}].`;
        if (newStatus === 'cancelled') {
            msg += `\nCẢNH BÁO: Hành động này sẽ Hủy Đơn, trả lại Hàng vào Kho. Bạn chắc chứ?`;
        } else if (newStatus === 'delivered') {
            msg += `\nCẢNH BÁO: Chốt đơn thành công! Sau hành động này sẽ không thể Đổi hay Hủy đơn.`;
        }

        if (!confirm(msg)) return;

        const btn = document.getElementById('btn-update-status');
        btn.disabled = true;
        btn.innerHTML = 'Đang xử lý...';

        try {
            const url = ROUTES.updateStatus.replace(':id', currentOrder.id);
            const res = await fetch(url, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken 
                },
                body: JSON.stringify({ status: newStatus })
            });

            const data = await res.json();
            
            if (res.ok && data.success) {
                showToast(data.message, "success");
                detailModal.hide();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || "Xảy ra lỗi khi cập nhật.", "danger");
            }
        } catch (e) {
            showToast("Lỗi kết nối.", "danger");
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Lưu Trạng Thái Mới';
        }
    }
</script>
@endsection
