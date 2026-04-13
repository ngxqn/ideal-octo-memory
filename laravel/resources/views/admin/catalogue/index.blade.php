@extends('layouts.admin')

@section('title', 'Quản lý Danh mục - MORICO')
@section('page_title', 'Quản lý Danh mục')

@section('content')
<!-- Bootstrap Tabs -->
<ul class="nav nav-tabs mb-4" id="catalogueTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold px-4" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-pane" type="button" role="tab">
            <i class="fa-solid fa-box me-2"></i>Sản phẩm
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold px-4" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-pane" type="button" role="tab">
            <i class="fa-solid fa-tags me-2"></i>Loại sản phẩm
        </button>
    </li>
</ul>

<div class="tab-content" id="catalogueTabContent">
    <!-- Tab 1: Sản phẩm -->
    <div class="tab-pane fade show active" id="products-pane" role="tabpanel">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent py-3">
                <h3 class="h5 mb-0 fw-bold">Danh sách Sản phẩm</h3>
                <button class="btn btn-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#productModal" onclick="prepareProductModal('add')">
                    <i class="fa-solid fa-plus me-1"></i> Thêm Sản phẩm
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">Ảnh</th>
                                <th class="text-start" style="width: 120px;">SKU</th>
                                <th class="text-start">Thông tin sản phẩm</th>
                                <th class="text-start">Loại</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-end">Tồn kho</th>
                                <th class="text-center pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td class="ps-4">
                                    <div class="rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden" style="width: 48px; height: 48px;">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
                                        @else
                                            <img src="{{ asset('storage/products/default.png') }}" alt="Default" class="img-fluid opacity-50">
                                        @endif
                                    </div>
                                </td>
                                <td class="text-start">
                                    <code class="text-primary fw-bold">{{ $product->sku }}</code>
                                </td>
                                <td class="text-start">
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    @if($product->is_hidden)
                                        <span class="badge bg-secondary sx-small">Đã ẩn</span>
                                    @elseif($product->category && $product->category->is_hidden)
                                        <span class="badge bg-secondary sx-small">Đã ẩn (theo Loại)</span>
                                    @endif
                                </td>
                                <td class="text-start">
                                    <span class="badge bg-light text-dark border">{{ $product->category->name ?? 'N/A' }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold">{{ number_format($product->sell_price, 0, ',', '.') }}&nbsp;₫</div>
                                    <div class="text-muted sx-small" style="font-size: 0.7rem;">Gốc: {{ number_format($product->base_price, 0, ',', '.') }}&nbsp;₫</div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold {{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-danger' : 'text-success' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm rounded shadow-sm">
                                        <form action="{{ route('admin.catalogue.products.toggle', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-white border" title="{{ $product->is_hidden ? 'Hiện' : 'Ẩn' }}">
                                                <i class="fa-solid {{ $product->is_hidden ? 'fa-eye-slash text-secondary' : 'fa-eye text-success' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-white border" title="Sửa" 
                                                onclick='prepareProductModal("edit", @json($product))'>
                                            <i class="fa-solid fa-pen-to-square text-primary"></i>
                                        </button>
                                        @php
                                            $hasTransactions = ($product->order_details_count ?? 0) > 0 || ($product->goods_receipt_details_count ?? 0) > 0;
                                        @endphp
                                        @if($hasTransactions)
                                            <button type="button" class="btn btn-white border opacity-50" title="Đang có giao dịch"
                                                    onclick="showNotification('Không thể xóa sản phẩm này vì đã có giao dịch phát sinh (Đơn hàng/Phiếu nhập).', 'danger')">
                                                <i class="fa-solid fa-lock text-muted"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-white border" title="Xóa"
                                                    onclick="confirmDelete('{{ route('admin.catalogue.products.destroy', $product->id) }}', 'Sản phẩm')">
                                                <i class="fa-solid fa-trash text-danger"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fa-solid fa-box-open d-block mb-3 fs-1 opacity-25"></i>
                                    Chưa có sản phẩm nào
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Loại sản phẩm -->
    <div class="tab-pane fade" id="categories-pane" role="tabpanel">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent py-3">
                <h3 class="h5 mb-0 fw-bold">Quản lý Loại sản phẩm</h3>
                <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="prepareCategoryModal('add')">
                    <i class="fa-solid fa-plus me-1"></i> Thêm Loại
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">ID</th>
                                <th class="text-start">Tên loại & Slug</th>
                                <th class="text-end">Số lượng SP</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td class="ps-4 text-muted">#{{ $category->id }}</td>
                                <td class="text-start">
                                    <div class="fw-bold">{{ $category->name }}</div>
                                    <code class="small text-muted">{{ $category->slug }}</code>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold">{{ $category->products_count ?? $category->products()->count() }}</span>
                                </td>
                                <td class="text-center">
                                    @if($category->is_hidden)
                                        <span class="badge bg-secondary">Đã ẩn</span>
                                    @else
                                        <span class="badge bg-success">Hoạt động</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm rounded shadow-sm">
                                        <form action="{{ route('admin.catalogue.categories.toggle', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-white border" title="{{ $category->is_hidden ? 'Hiện' : 'Ẩn' }}">
                                                <i class="fa-solid {{ $category->is_hidden ? 'fa-eye-slash text-secondary' : 'fa-eye text-success' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-white border" title="Sửa"
                                                onclick='prepareCategoryModal("edit", @json($category))'>
                                            <i class="fa-solid fa-pen-to-square text-primary"></i>
                                        </button>
                                        @if(($category->products_count ?? 0) > 0)
                                            <button type="button" class="btn btn-white border opacity-50" title="Đang có sản phẩm"
                                                    onclick="showNotification('Không thể xóa loại này vì đang chứa sản phẩm.', 'danger')">
                                                <i class="fa-solid fa-lock text-muted"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-white border" title="Xóa"
                                                    onclick="confirmDelete('{{ route('admin.catalogue.categories.destroy', $category->id) }}', 'Loại sản phẩm')">
                                                <i class="fa-solid fa-trash text-danger"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">Chưa có loại sản phẩm nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sản phẩm -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="productForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="productFormMethod"></div>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="productModalTitle">Thêm Sản phẩm mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="p_name" class="form-control" required placeholder="Ví dụ: Bánh thập cẩm gà quay">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Mã SP (SKU) <span class="text-danger">*</span></label>
                            <input type="text" name="sku" id="p_sku" class="form-control" required placeholder="BC-THAP-CAM">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Loại sản phẩm <span class="text-danger">*</span></label>
                            <select name="category_id" id="p_category_id" class="form-select" required>
                                <option value="">-- Chọn loại --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Lợi nhuận (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="profit_margin" id="p_profit_margin" class="form-control" required min="0" step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Giá nhập (Base Price) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" id="p_base_price" class="form-control bg-light" readonly value="0">
                                <span class="input-group-text">₫</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tồn kho ban đầu</label>
                            <input type="number" id="p_stock_quantity" class="form-control bg-light" readonly value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Mô tả sản phẩm</label>
                            <textarea name="description" id="p_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Hình ảnh</label>
                            <input type="file" name="image" class="form-control">
                            <div id="p_image_preview" class="mt-2 small text-muted"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Trạng thái hiển thị</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_hidden" id="p_is_hidden" value="1">
                                <label class="form-check-label" for="p_is_hidden">Ẩn sản phẩm này</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Loại sản phẩm -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="categoryForm" method="POST">
                @csrf
                <div id="categoryFormMethod"></div>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="categoryModalTitle">Thêm Loại mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tên loại <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="c_name" class="form-control" required placeholder="Ví dụ: Bánh nướng truyền thống">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">SEO Slug</label>
                        <input type="text" name="slug" id="c_slug" class="form-control" placeholder="Để trống để tự tạo">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_hidden" id="c_is_hidden" value="1">
                        <label class="form-check-label" for="c_is_hidden">Ẩn loại này</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <i class="fa-solid fa-triangle-exclamation text-danger fs-1 mb-3"></i>
                <h5 class="fw-bold">Xác nhận xóa?</h5>
                <p class="text-muted small">Hành động này không thể hoàn tác.</p>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Bỏ qua</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Đồng ý</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function prepareProductModal(mode, data = null) {
        const form = document.getElementById('productForm');
        const methodDiv = document.getElementById('productFormMethod');
        const title = document.getElementById('productModalTitle');
        const imagePreview = document.getElementById('p_image_preview');
        
        // Reset form
        form.reset();
        methodDiv.innerHTML = '';
        imagePreview.innerHTML = '';
        
        if (mode === 'add') {
            title.textContent = 'Thêm Sản phẩm mới';
            form.action = "{{ route('admin.catalogue.products.store') }}";
            document.getElementById('p_base_price').value = 0;
            document.getElementById('p_stock_quantity').value = 0;
        } else {
            title.textContent = 'Chỉnh sửa Sản phẩm';
            form.action = "{{ route('admin.catalogue.products.update', ':id') }}".replace(':id', data.id);
            methodDiv.innerHTML = '@method("PUT")';
            
            // Populate data
            document.getElementById('p_name').value = data.name;
            document.getElementById('p_sku').value = data.sku;
            document.getElementById('p_category_id').value = data.category_id;
            document.getElementById('p_base_price').value = data.base_price;
            document.getElementById('p_profit_margin').value = data.profit_margin;
            document.getElementById('p_description').value = data.description || '';
            document.getElementById('p_is_hidden').checked = data.is_hidden;
            
            // Stock and base price are readonly views
            document.getElementById('p_stock_quantity').value = data.stock_quantity;
            
            if (data.image) {
                imagePreview.innerHTML = `Ảnh hiện tại: <span class="text-primary">${data.image.split('/').pop()}</span>`;
            }

            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        }
    }

    function prepareCategoryModal(mode, data = null) {
        const form = document.getElementById('categoryForm');
        const methodDiv = document.getElementById('categoryFormMethod');
        const title = document.getElementById('categoryModalTitle');
        
        form.reset();
        methodDiv.innerHTML = '';
        
        if (mode === 'add') {
            title.textContent = 'Thêm Loại mới';
            form.action = "{{ route('admin.catalogue.categories.store') }}";
        } else {
            title.textContent = 'Chỉnh sửa Loại';
            form.action = "{{ route('admin.catalogue.categories.update', ':id') }}".replace(':id', data.id);
            methodDiv.innerHTML = '@method("PUT")';
            
            document.getElementById('c_name').value = data.name;
            document.getElementById('c_slug').value = data.slug;
            document.getElementById('c_is_hidden').checked = data.is_hidden;

            const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        }
    }

    function confirmDelete(url, type) {
        const form = document.getElementById('deleteForm');
        form.action = url;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // --- Tab Persistence Logic ---
    document.addEventListener('DOMContentLoaded', function() {
        const STORAGE_KEY = 'admin_catalogue_active_tab';
        const tabList = [].slice.call(document.querySelectorAll('button[data-bs-toggle="tab"]'));
        
        // 1. Restore Tab on Load
        const activeTabId = localStorage.getItem(STORAGE_KEY);
        if (activeTabId) {
            const tabButton = document.getElementById(activeTabId);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }

        // 2. Track Tab Changes
        tabList.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem(STORAGE_KEY, event.target.id);
            });
        });
    });
</script>
@endsection
