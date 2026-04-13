@extends('layouts.admin')

@section('title', 'Quản lý Giá bán - MORICO')
@section('page_title')
    <i class="fa-solid fa-dollar-sign me-2" style="color: var(--brand-gold);"></i> Quản lý giá bán
@endsection

@section('content')

<div class="card shadow-sm border mb-4">
    <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
        <h3 class="h5 mb-0 fw-bold text-secondary"><i class="fa-solid fa-calculator me-2"></i> Tra cứu và tinh chỉnh giá bán</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Loại SP</th>
                        <th class="text-end">Giá vốn (Base)</th>
                        <th class="text-center" style="width: 15%">% Lợi nhuận</th>
                        <th class="text-end">Giá bán (Sell)</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody id="pricing-table-body">
                    @forelse($products as $product)
                    <tr id="row-{{ $product->id }}">
                        <td>
                            <div class="fw-bold">{{ $product->name }}</div>
                            <div class="text-muted small">
                                <code>{{ $product->sku }}</code>
                                @if($product->is_hidden)
                                    <span class="badge bg-secondary sx-small ms-1">Đã ẩn</span>
                                @elseif($product->category && $product->category->is_hidden)
                                    <span class="badge bg-secondary sx-small ms-1">Đã ẩn (theo Loại)</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $product->category->name ?? 'N/A' }}</span>
                        </td>
                        <td class="text-end text-danger fw-bold" id="base_price_{{ $product->id }}" data-value="{{ $product->base_price }}">
                            {{ number_format($product->base_price, 0, ',', '.') }} ₫
                        </td>
                        <td class="text-center">
                            <div class="input-group input-group-sm justify-content-center">
                                <input type="number" 
                                       class="form-control text-center profit-input" 
                                       style="max-width: 80px;" 
                                       id="profit_margin_{{ $product->id }}"
                                       value="{{ number_format($product->profit_margin, 2, '.', '') }}" 
                                       data-original="{{ number_format($product->profit_margin, 2, '.', '') }}"
                                       min="0" max="999" step="0.01"
                                       oninput="previewPrice({{ $product->id }})"
                                       onkeydown="return !['+', '-', 'e', 'E'].includes(event.key);">
                                <span class="input-group-text">%</span>
                            </div>
                        </td>
                        <td class="text-end fw-bold" id="sell_price_{{ $product->id }}">
                            {{ number_format($product->sell_price, 0, ',', '.') }} ₫
                        </td>
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm me-1" onclick="savePrice({{ $product->id }})">
                                <i class="fa-solid fa-save"></i> Lưu
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="resetPrice({{ $product->id }})">
                                <i class="fa-solid fa-undo"></i> Đặt lại
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-box-open fs-3 mb-2 opacity-50"></i>
                            <p class="mb-0">Chưa có sản phẩm nào để quản lý giá.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Toast Container for Notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="pricingToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                <!-- Message goes here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Config CSRF Token for Fetch API
    const csrfToken = '{{ csrf_token() }}';
    const formatter = new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 });

    // Mutiplier representation of Database calculation: sell = base * (1 + margin / 100)
    function calculateSellingPrice(basePrice, profitMargin) {
        return Math.round(basePrice * (1 + profitMargin / 100));
    }

    function formatCurrency(amount) {
        return formatter.format(amount) + ' ₫';
    }

    function previewPrice(productId) {
        const basePriceElem = document.getElementById('base_price_' + productId);
        const marginInput = document.getElementById('profit_margin_' + productId);
        const sellPriceElem = document.getElementById('sell_price_' + productId);
        
        const basePrice = parseFloat(basePriceElem.getAttribute('data-value')) || 0;
        const margin = parseFloat(marginInput.value) || 0;
        
        const newSellPrice = calculateSellingPrice(basePrice, margin);
        sellPriceElem.textContent = formatCurrency(newSellPrice);
        
        // Add a visual indicator that it's unsaved
        sellPriceElem.classList.add('text-warning');
    }

    function resetPrice(productId) {
        const marginInput = document.getElementById('profit_margin_' + productId);
        marginInput.value = marginInput.getAttribute('data-original');
        
        // Trigger recalculation to reset view
        previewPrice(productId);
        
        // Remove visual indicator
        const sellPriceElem = document.getElementById('sell_price_' + productId);
        sellPriceElem.classList.remove('text-warning');
    }

    async function savePrice(productId) {
        const marginInput = document.getElementById('profit_margin_' + productId);
        const sellPriceElem = document.getElementById('sell_price_' + productId);
        const marginValue = marginInput.value;
        const row = document.getElementById('row-' + productId);
        
        // Optional: Simple visual loading state
        const saveBtn = row.querySelector('.btn-primary');
        const originalBtnHtml = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        saveBtn.disabled = true;

        try {
            const url = "{{ route('admin.pricing.update', ['product' => ':id']) }}".replace(':id', productId);
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    profit_margin: marginValue
                })
            });

            const data = await response.json();
            
            const toastEl = document.getElementById('pricingToast');
            const toast = new bootstrap.Toast(toastEl);
            const toastMessage = document.getElementById('toastMessage');

            if (response.ok && data.success) {
                // Update the DOM to perfectly reflect the database newly generated price
                sellPriceElem.textContent = formatCurrency(data.product.sell_price);
                sellPriceElem.classList.remove('text-warning');
                
                // Update original data attribute so "Reset" works correctly
                marginInput.setAttribute('data-original', data.product.profit_margin);
                
                // Show toast
                toastEl.className = 'toast align-items-center text-bg-success border-0';
                toastMessage.textContent = data.message;
                toast.show();
            } else {
                toastEl.className = 'toast align-items-center text-bg-danger border-0';
                toastMessage.textContent = 'Có lỗi xảy ra: ' + (data.message || 'Lỗi server');
                toast.show();
            }
        } catch (error) {
            console.error('Error saving price:', error);
            const toastEl = document.getElementById('pricingToast');
            const toast = new bootstrap.Toast(toastEl);
            const toastMessage = document.getElementById('toastMessage');
            
            toastEl.className = 'toast align-items-center text-bg-danger border-0';
            toastMessage.textContent = 'Lỗi kết nối. Vui lòng thử lại sau.';
            toast.show();
        } finally {
            // Restore button
            saveBtn.innerHTML = originalBtnHtml;
            saveBtn.disabled = false;
        }
    }
</script>
@endsection
