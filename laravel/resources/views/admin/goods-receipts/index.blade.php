@extends('layouts.admin')

@section('title', 'Quản lý Nhập hàng - MORICO')
@section('page_title')
    <i class="fa-solid fa-truck-loading me-2" style="color: var(--brand-gold);"></i> Quản lý nhập hàng
@endsection

@section('content')
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center pt-3 pb-0">
        <h3 class="h5 mb-0 fw-bold text-secondary">Danh sách phiếu nhập</h3>
        <button class="btn btn-primary d-flex align-items-center gap-2" onclick="openReceiptModal()">
            <i class="fa-solid fa-plus"></i> Thêm phiếu mới
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã Phiếu</th>
                        <th>Ngày tạo</th>
                        <th>Mặt hàng</th>
                        <th>Ghi chú</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td class="fw-bold text-primary">PN{{ str_pad($receipt->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($receipt->details->count() > 0)
                                {{ $receipt->details->first()->product->name ?? 'N/A' }} 
                                <span class="text-muted">(&times;{{ $receipt->details->first()->quantity }})</span>
                                @if($receipt->details->count() > 1)
                                    <span class="badge bg-info text-dark rounded-pill ms-1">+{{ $receipt->details->count() - 1 }}</span>
                                @endif
                            @else
                                <span class="text-muted">Chưa có sản phẩm</span>
                            @endif
                        </td>
                        <td class="text-muted small text-truncate" style="max-width: 150px;">{{ $receipt->note ?? '-' }}</td>
                        <td class="text-end fw-bold text-danger">
                            @php
                                $total = $receipt->details->sum(function($item) {
                                    return $item->quantity * $item->import_price;
                                });
                            @endphp
                            {{ number_format($total, 0, ',', '.') }} ₫
                        </td>
                        <td class="text-center">
                            @if($receipt->status === 'completed')
                                <span class="badge bg-success rounded-pill">Đã hoàn thành</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Bản nháp</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm rounded shadow-sm">
                                <button class="btn btn-white border text-info" title="Chi tiết" onclick="viewReceipt({{ $receipt->id }})">
                                    <i class="fa-solid fa-list"></i>
                                </button>
                                @if($receipt->status === 'draft')
                                <button class="btn btn-white border text-warning" title="Chỉnh sửa (Nháp)" onclick="editReceipt({{ $receipt->id }})">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-white border text-success" title="Xác nhận Hoàn thành" onclick="completeReceipt({{ $receipt->id }})">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <button class="btn btn-white border text-danger" title="Xóa phiếu nháp" onclick="deleteReceipt({{ $receipt->id }})">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open fs-1 opacity-25 mb-3 d-block"></i>
                            Chưa có phiếu nhập hàng nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Create/Edit Receipt -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary" id="receiptModalTitle">Tạo phiếu nhập hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white" id="receiptModalBody">
                <input type="hidden" id="form-receipt-id" value="">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Trạng thái lưu</label>
                        <select class="form-select" id="form-status">
                            <option value="draft">Bản nháp (Chưa cộng tồn kho)</option>
                            <option value="completed">Đã hoàn thành (Cộng Tồn kho & Tính lại Giá vốn)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Ghi chú</label>
                        <textarea class="form-control" id="form-note" rows="2" placeholder="Thông tin thêm..."></textarea>
                    </div>
                </div>

                <div id="item-input-section" class="card shadow-sm border mb-4">
                    <div class="card-header bg-white py-2"><h6 class="mb-0 fw-bold text-primary small">Thêm sản phẩm</h6></div>
                    <div class="card-body p-3 bg-light">
                        <div class="row g-2 align-items-end">
                            <div class="col-lg-5">
                                <label class="small text-muted fw-bold">Chọn sản phẩm</label>
                                <select class="form-select border-primary" id="form-product-select" onchange="autoFillPrice()">
                                    <option value="" data-base="0">-- Tìm & chọn sản phẩm --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-name="{{ htmlspecialchars($product->name) }}" data-base="{{ $product->base_price }}">
                                            {{ $product->sku }} - {{ $product->name }} | Giá nhập hiện tại: {{ number_format($product->base_price, 0, ',', '.') }} ₫
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="small text-muted fw-bold">Giá nhập mới (VND)</label>
                                <input type="number" class="form-control border-primary" id="form-product-price" placeholder="Nhập đơn giá">
                            </div>
                            <div class="col-lg-2">
                                <label class="small text-muted fw-bold">Số lượng</label>
                                <input type="number" class="form-control border-primary" id="form-product-qty" value="1" min="1">
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-primary w-100 fw-bold" onclick="addItem()"><i class="fa-solid fa-plus me-1"></i> Thêm</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Sản phẩm</th>
                                <th class="text-center" style="width:100px;">S.Lượng</th>
                                <th class="text-end" style="width:150px;">Giá nhập</th>
                                <th class="text-end" style="width:150px;">Thành tiền</th>
                                <th class="text-center" style="width:60px;" id="th-action">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="items-tbody">
                            <!-- Items auto injected here -->
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end fs-6">Tổng cộng phiếu:</td>
                                <td class="text-end text-danger fs-5" id="form-total-cost">0 ₫</td>
                                <td id="tf-action"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
            <div class="modal-footer bg-light" id="receiptModalFooter">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success px-4 fw-bold shadow-sm" id="btn-save-receipt" onclick="saveReceipt()"><i class="fa-solid fa-save me-1"></i> Lưu Phiếu Nhập</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1060;">
    <div id="liveToast" class="toast hide border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-medium" id="toastMessage">
                <!-- Message injected here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const formatter = new Intl.NumberFormat('vi-VN');
    
    const ROUTES = {
        store: "{{ route('admin.goods-receipts.store') }}",
        show: "{{ route('admin.goods-receipts.show', ':id') }}",
        update: "{{ route('admin.goods-receipts.update', ':id') }}",
        delete: "{{ route('admin.goods-receipts.destroy', ':id') }}",
        complete: "{{ route('admin.goods-receipts.complete', ':id') }}"
    };

    let tempItems = [];
    let isReadOnly = false;
    let modalMode = 'view'; // Modes: 'create', 'view', 'edit'

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('liveToast');
        const toastBody = document.getElementById('toastMessage');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        
        // Reset classes
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

    // Input masking to prevent non-numeric chars like +, -, e
    ['form-product-price', 'form-product-qty'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('keydown', function(e) {
                if (['e', 'E', '+', '-'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        }
    });

    function updateModalFooter(status, id) {
        const footer = document.getElementById('receiptModalFooter');
        
        if (modalMode === 'view') {
            if (status === 'completed') {
                footer.innerHTML = ''; // No buttons for completed view
            } else {
                // Draft View: Delete and Edit only
                footer.innerHTML = `
                    <button type="button" class="btn btn-outline-danger px-4" onclick="deleteReceipt(${id}, true)"><i class="fa-solid fa-trash me-1"></i> Xóa</button>
                    <button type="button" class="btn btn-outline-warning px-4" onclick="editReceipt(${id})"><i class="fa-solid fa-pen-to-square me-1"></i> Sửa</button>
                `;
            }
        } else if (modalMode === 'edit') {
            // Draft Edit: Delete and Save only
            footer.innerHTML = `
                <button type="button" class="btn btn-outline-danger px-4" onclick="deleteReceipt(${id}, true)"><i class="fa-solid fa-trash me-1"></i> Xóa</button>
                <button type="button" class="btn btn-success px-4 fw-bold shadow-sm" id="btn-save-receipt" onclick="saveReceipt()"><i class="fa-solid fa-save me-1"></i> Lưu Phiếu Nhập</button>
            `;
        } else {
            // mode === 'create'
            footer.innerHTML = `
                <button type="button" class="btn btn-success px-4 fw-bold shadow-sm" id="btn-save-receipt" onclick="saveReceipt()"><i class="fa-solid fa-save me-1"></i> Tạo phiếu</button>
            `;
        }
    }

    function formatVND(amount) {
        return formatter.format(amount) + ' ₫';
    }

    function autoFillPrice() {
        const select = document.getElementById('form-product-select');
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;
        
        const basePrice = selectedOption.getAttribute('data-base');
        document.getElementById('form-product-price').value = basePrice;
    }

    function addItem() {
        if (isReadOnly) return;
        const select = document.getElementById('form-product-select');
        const opt = select.options[select.selectedIndex];
        const priceInput = document.getElementById('form-product-price');
        const qtyInput = document.getElementById('form-product-qty');

        if (!opt.value) { showToast("Vui lòng chọn sản phẩm.", "warning"); return; }
        
        const productId = parseInt(opt.value);
        const name = opt.getAttribute('data-name');
        let qty = parseInt(qtyInput.value) || 0;
        let price = parseFloat(priceInput.value) || 0;

        // Force integer for qty and 2 decimals for price
        qty = Math.floor(qty);
        price = parseFloat(price.toFixed(2));

        if (qty <= 0) { showToast("Số lượng phải > 0", "warning"); return; }
        if (price < 0) { showToast("Giá nhập không hợp lệ", "warning"); return; }

        // Check if exists
        const existsLine = tempItems.find(i => i.product_id === productId);
        if (existsLine) {
            existsLine.quantity += qty;
            existsLine.import_price = price; // overwrite price
        } else {
            tempItems.push({
                product_id: productId,
                name: name,
                quantity: qty,
                import_price: price
            });
        }

        renderItems();
        select.value = "";
        priceInput.value = "";
        qtyInput.value = "1";
    }

    function removeItem(index) {
        if (isReadOnly) return;
        tempItems.splice(index, 1);
        renderItems();
    }

    function renderItems() {
        const tbody = document.getElementById('items-tbody');
        tbody.innerHTML = '';
        let total = 0;

        tempItems.forEach((item, index) => {
            const lineTotal = item.quantity * item.import_price;
            total += lineTotal;
            tbody.innerHTML += `
                <tr>
                    <td class="text-muted">${index + 1}</td>
                    <td class="fw-bold">${item.name}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-end">${formatVND(item.import_price)}</td>
                    <td class="text-end fw-bold text-danger">${formatVND(lineTotal)}</td>
                    ${!isReadOnly ? `<td class="text-center"><button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="removeItem(${index})"><i class="fa-solid fa-times"></i></button></td>` : ''}
                </tr>
            `;
        });

        if (tempItems.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${!isReadOnly ? '6' : '5'}" class="text-center text-muted py-4">Chưa có sản phẩm nào.</td></tr>`;
        }

        document.getElementById('form-total-cost').textContent = formatVND(total);
        
        document.getElementById('th-action').style.display = isReadOnly ? 'none' : 'table-cell';
        document.getElementById('tf-action').style.display = isReadOnly ? 'none' : 'table-cell';
    }

    function resetForm() {
        document.getElementById('form-receipt-id').value = '';
        document.getElementById('form-note').value = '';
        document.getElementById('form-status').value = 'draft';
        tempItems = [];
        isReadOnly = false;
        
        document.getElementById('form-note').disabled = false;
        document.getElementById('form-status').disabled = false;
        document.getElementById('item-input-section').style.display = 'block';
        
        document.getElementById('receiptModalTitle').innerHTML = 'Tạo phiếu nhập hàng';
        renderItems();
    }

    function openReceiptModal() {
        resetForm();
        modalMode = 'create';
        updateModalFooter('draft', null);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('receiptModal')).show();
    }

    async function viewReceipt(id) {
        resetForm();
        isReadOnly = true;
        modalMode = 'view';
        document.getElementById('receiptModalTitle').innerHTML = `Chi tiết phiếu nhập PN${String(id).padStart(3, '0')}`;
        
        document.getElementById('form-note').disabled = true;
        document.getElementById('form-status').disabled = true;
        document.getElementById('item-input-section').style.display = 'none';

        await fetchReceiptData(id);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('receiptModal')).show();
    }

    async function editReceipt(id) {
        resetForm();
        modalMode = 'edit';
        document.getElementById('receiptModalTitle').innerHTML = `Chỉnh sửa Phiếu Nhập PN${String(id).padStart(3, '0')}`;
        document.getElementById('form-receipt-id').value = id;
        
        // Cannot edit completed status directly back to draft
        document.getElementById('form-status').disabled = false;

        await fetchReceiptData(id);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('receiptModal')).show();
    }

    async function deleteReceipt(id, fromModal = false) {
        if (!confirm("Bạn có chắc chắn muốn xóa phiếu nhập này? Hành động này không thể hoàn tác.")) return;

        try {
            const url = ROUTES.delete.replace(':id', id);
            const res = await fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (res.ok && data.success) {
                showToast(data.message, "success");
                if (fromModal) {
                    bootstrap.Modal.getInstance(document.getElementById('receiptModal')).hide();
                }
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || "Xóa thất bại.", "danger");
            }
        } catch (e) {
            showToast("Lỗi kết nối máy chủ.", "danger");
        }
    }

    async function fetchReceiptData(id) {
        try {
            const url = ROUTES.show.replace(':id', id);
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            
            if (!res.ok) {
                if (res.status === 404) {
                    showToast("Không tìm thấy dữ liệu phiếu nhập (404).", "danger");
                } else {
                    showToast("Lỗi hệ thống khi tải dữ liệu.", "danger");
                }
                return;
            }

            const data = await res.json();
            if (data.success) {
                const r = data.receipt;
                document.getElementById('form-note').value = r.note || '';
                document.getElementById('form-status').value = r.status;
                
                tempItems = r.details.map(d => ({
                    product_id: d.product_id,
                    name: d.product ? d.product.name : 'N/A',
                    quantity: d.quantity,
                    import_price: d.import_price
                }));
                renderItems();

                if (r.status === 'completed') {
                    isReadOnly = true;
                    document.getElementById('form-note').disabled = true;
                    document.getElementById('form-status').disabled = true;
                    document.getElementById('item-input-section').style.display = 'none';
                    renderItems();
                }
                
                // If viewing from viewReceipt, we set the footer based on status
                updateModalFooter(r.status, id);

            } else {
                showToast(data.message || "Không thể tải chi tiết phiếu nhập.", "danger");
            }
        } catch (e) {
            console.error(e);
            showToast("Lỗi kết nối mạng.", "danger");
        }
    }

    async function saveReceipt() {
        const id = document.getElementById('form-receipt-id').value;
        const note = document.getElementById('form-note').value;
        const status = document.getElementById('form-status').value;
        
        if (tempItems.length === 0) { showToast("Phải có ít nhất 1 sản phẩm trong phiếu nhập.", "warning"); return; }
        
        if (status === 'completed') {
            if (!confirm("CẢNH BÁO: Đánh dấu 'Hoàn thành' sẽ cập nhật TỒN KHO và GIÁ VỐN ngay lập tức. Hành động này là BẤT BIẾN (không thể sửa hay xóa). Bạn chắc chắn chứ?")) {
                return;
            }
        }

        const payload = {
            note: note,
            status: status,
            items: tempItems.map(i => ({
                product_id: i.product_id,
                quantity: i.quantity,
                import_price: i.import_price
            }))
        };

        const method = id ? 'PUT' : 'POST';
        const url = id ? ROUTES.update.replace(':id', id) : ROUTES.store;

        document.getElementById('btn-save-receipt').disabled = true;
        document.getElementById('btn-save-receipt').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';

        try {
            const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (res.ok && data.success) {
                showToast(data.message, "success");
                setTimeout(() => window.location.reload(), 1000); 
            } else {
                showToast(data.message || "Kiểm tra lại dữ liệu nhập.", "danger");
            }
        } catch(e) {
            showToast("Lỗi kết nối máy chủ.", "danger");
        } finally {
            document.getElementById('btn-save-receipt').disabled = false;
            document.getElementById('btn-save-receipt').innerHTML = '<i class="fa-solid fa-save me-1"></i> Lưu Phiếu Nhập';
        }
    }

    async function completeReceipt(id) {
        if (!confirm("XÁC NHẬN: Bạn có chắc muốn chốt phiếu nhập này? Tồn kho và giá vốn (WAC) sẽ được tính toán ngay lập tức và phiếu này không thể sửa được nữa!")) return;
        
        try {
            const url = ROUTES.complete.replace(':id', id);
            const res = await fetch(url, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await res.json();
            
            if (res.ok && data.success) {
                showToast(data.message, "success");
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || "Xử lý thất bại.", "danger");
            }
        } catch(e) {
            showToast("Lỗi kết nối máy chủ.", "danger");
        }
    }
</script>
@endsection
