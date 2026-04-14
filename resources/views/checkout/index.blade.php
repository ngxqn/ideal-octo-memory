@extends('layouts.app')

@section('title', 'Thanh Toán Đơn Hàng - MORICO')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/checkout.css') }}">
@endsection

@section('content')
<div class="app-container py-5">
    <nav class="m-breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="separator">/</span>
        <a href="{{ route('cart.index') }}">Giỏ hàng</a>
        <span class="separator">/</span>
        <span class="current">Thanh toán</span>
    </nav>

    <h1 class="page-title">Thanh toán đơn hàng</h1>
    <p class="page-subtitle">Hoàn tất đơn hàng của bạn một cách dễ dàng và an toàn</p>    

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row g-4">
            <!-- Thông tin nhận hàng -->
            <div class="col-lg-7">
                <div id="shipping-info" class="m-card">
                    <h2 class="m-card-title"><i class="fas fa-truck text-primary"></i> Thông tin nhận hàng</h2>
                    
                    <div class="address-section mb-4">
                        @if($addresses->count() > 0)
                            <div id="address-options">
                                @foreach($addresses as $address)
                                    <div class="address-option {{ $address->is_default ? 'selected' : '' }}" 
                                         data-id="{{ $address->id }}"
                                         data-name="{{ $address->receiver_name }}"
                                         data-phone="{{ $address->receiver_phone }}"
                                         data-address="{{ $address->address }}"
                                         data-commune="{{ $address->commune }}"
                                         data-city="{{ $address->city }}">
                                        <div class="d-flex align-items-start gap-2">
                                            <input type="radio" name="selected_address_id" value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }} class="mt-1">
                                            <div class="flex-grow-1">
                                                <div class="address-label">
                                                    {{ $address->receiver_name }}
                                                    @if($address->is_default)
                                                        <span class="badge bg-primary ms-2" style="font-size: 0.7rem;">Mặc định</span>
                                                    @endif
                                                </div>
                                                <div class="address-contact">{{ $address->receiver_phone }}</div>
                                                <div class="address-details">
                                                    {{ $address->address }}, {{ $address->commune }}, {{ $address->city }}
                                                </div>
                                            </div>
                                            @if(!$address->is_default)
                                            <button type="button" class="btn btn-link text-danger p-0 ms-auto btn-delete-address" 
                                                    data-id="{{ $address->id }}" title="Xóa địa chỉ">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Bạn chưa có địa chỉ lưu sẵn.</p>
                        @endif

                        <button type="button" class="add-address-btn" id="show-add-form">
                            <i class="fas fa-plus"></i> Thêm địa chỉ mới
                        </button>

                        <!-- Hidden fields for standard PlaceOrderRequest -->
                        <input type="hidden" name="receiver_name" id="hidden_receiver_name" value="">
                        <input type="hidden" name="receiver_phone" id="hidden_receiver_phone" value="">
                        <input type="hidden" name="shipping_address" id="hidden_shipping_address" value="">
                        <input type="hidden" name="shipping_commune" id="hidden_shipping_commune" value="">
                        <input type="hidden" name="shipping_city" id="hidden_shipping_city" value="">

                        <!-- Inline New Address Form -->
                        <div class="new-address-form mt-3 p-3 border rounded shadow-sm" id="new-address-inline-form" style="display: none;">
                            <h3 class="fs-5 mb-3 text-primary"><i class="fas fa-house-chimney me-2"></i>Nhập địa chỉ mới</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Họ tên người nhận</label>
                                    <input type="text" id="new_receiver_name" class="form-control" placeholder="Nhập họ tên">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="new_receiver_phone" class="form-label fw-bold">Số điện thoại</label>
                                    <input type="tel" id="new_receiver_phone" class="form-control" 
                                           placeholder="Ví dụ: 0912345678"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Số nhà, tên đường</label>
                                    <input type="text" id="new_shipping_address" class="form-control" placeholder="Ví dụ: 123 Đường ABC">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Phường/Xã</label>
                                    <input type="text" id="new_shipping_commune" class="form-control" placeholder="Ví dụ: Phường 1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tỉnh/Thành phố</label>
                                    <input type="text" id="new_shipping_city" class="form-control" placeholder="Ví dụ: TP. Hồ Chí Minh">
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="save_for_future" id="save_for_future" value="1">
                                        <label class="form-check-label" for="save_for_future">
                                            Lưu địa chỉ này cho lần mua sau
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="button" class="btn btn-secondary flex-grow-1" id="cancel-add">Hủy</button>
                                    <button type="button" class="btn btn-primary flex-grow-1" id="confirm-new-address">Sử dụng địa chỉ này</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-bold"><i class="fas fa-pen-to-square me-2"></i>Ghi chú đơn hàng</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Ví dụ: Giao giờ hành chính, lời nhắn cho cửa hàng...">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Chi tiết đơn hàng & Thanh toán -->
            <div class="col-lg-5">
                <div id="order-summary" class="m-card">
                    <h2 class="m-card-title"><i class="fas fa-receipt text-primary"></i> Chi tiết Đơn hàng</h2>
                    
                    <div class="items-list mb-4">
                        @foreach($cart->cartItems as $item)
                        <div class="m-card p-3 mb-2 d-flex align-items-center gap-3" style="border-width: 1px; box-shadow: none;">
                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                 class="item-img" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                 onerror="this.onerror=null;this.src='{{ asset('storage/products/default.png') }}'">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                <div class="text-muted small">Số lượng: {{ $item->quantity }}</div>
                            </div>
                            <span class="fw-bold text-primary">
                                {{ number_format($item->product->sell_price * $item->quantity, 0, ',', '.') }}&nbsp;₫
                            </span>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tổng tiền hàng:</span>
                        <span id="sub-total" class="fw-bold">{{ number_format($total, 0, ',', '.') }}&nbsp;₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>

                    <div class="m-total-bar mt-0 pt-4">
                        <div class="m-grand-total w-100">
                            <small>TỔNG THANH TOÁN:</small>
                            <span id="final-total" class="fs-3">{{ number_format($total, 0, ',', '.') }}&nbsp;₫</span>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h2 class="fs-5 mb-3">Hình thức Thanh toán</h2>
                        
                        <div class="payment-option border p-3 rounded mb-2">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" id="cod" value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                <label for="cod" class="m-0 ms-2 cursor-pointer w-100">Thanh toán khi nhận hàng (COD)</label>
                            </div>
                        </div>
                        
                        <div class="payment-option border p-3 rounded mb-2">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" id="transfer" value="transfer" {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                <label for="transfer" class="m-0 ms-2 cursor-pointer d-flex align-items-center justify-content-between flex-grow-1">
                                    <span>Chuyển khoản ngân hàng</span>
                                    <i class="fas fa-chevron-down toggle-bank-icon" style="display: none; transition: transform 0.3s;"></i>
                                </label>
                            </div>

                            <!-- Bank details with Carousel -->
                            <div id="bank-transfer-details" class="mt-3 border-top pt-3" style="display: none;">
                                <div id="bankCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner bg-light rounded p-3">
                                        <!-- Slide 1: Bank Info -->
                                        <div class="carousel-item active text-center">
                                            <h6 class="fw-bold mb-3">Thông tin chuyển khoản</h6>
                                            <p class="mb-1">Ngân hàng: <strong>KIENLONGBANK</strong></p>
                                            <p class="mb-1">Số tài khoản: <strong>1234 5678 9999</strong></p>
                                            <p class="mb-1">Chủ tài khoản: <strong>MORICO BAKERY</strong></p>
                                            <p class="mb-0 small text-muted">Nội dung: [Mã đơn hàng]</p>
                                        </div>
                                        <!-- Slide 2: QR Code -->
                                        <div class="carousel-item text-center">
                                            <h6 class="fw-bold mb-2">Quét mã QR để thanh toán</h6>
                                            <img src="{{ asset('assets/image/qr-merchant-kienlongbank.png') }}" alt="QR Kienlongbank" class="img-fluid" style="max-height: 200px;">
                                        </div>
                                    </div>
                                    <!-- Controls -->
                                    <div class="d-flex justify-content-center mt-2 gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-target="#bankCarousel" data-bs-slide="prev">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-target="#bankCarousel" data-bs-slide="next">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="payment-option border p-3 rounded mb-4">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" id="online" value="online" {{ old('payment_method') == 'online' ? 'checked' : '' }}>
                                <label for="online" class="m-0 ms-2 cursor-pointer w-100">Thanh toán trực tuyến (VNPay/ZaloPay)</label>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="submit" class="m-btn m-btn-primary w-100 py-3 mt-3 shadow" id="confirm-order-btn">
                        <i class="fas fa-check-circle me-2"></i> XÁC NHẬN ĐẶT HÀNG
                    </button>

                    <div class="text-center mt-4">
                        <a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addressOptions = document.querySelectorAll('.address-option');
        const showAddFormBtn = document.getElementById('show-add-form');
        const addForm = document.getElementById('new-address-inline-form');
        const cancelAddBtn = document.getElementById('cancel-add');
        const confirmNewAddrBtn = document.getElementById('confirm-new-address');
        const confirmOrderBtn = document.getElementById('confirm-order-btn');
        const checkoutForm = document.getElementById('checkout-form');

        // Hidden fields
        const hiddenName = document.getElementById('hidden_receiver_name');
        const hiddenPhone = document.getElementById('hidden_receiver_phone');
        const hiddenAddr = document.getElementById('hidden_shipping_address');
        const hiddenCommune = document.getElementById('hidden_shipping_commune');
        const hiddenCity = document.getElementById('hidden_shipping_city');

        function syncSelectedAddress() {
            const selected = document.querySelector('.address-option.selected');
            if (selected) {
                hiddenName.value = selected.dataset.name;
                hiddenPhone.value = selected.dataset.phone;
                hiddenAddr.value = selected.dataset.address;
                hiddenCommune.value = selected.dataset.commune;
                hiddenCity.value = selected.dataset.city;
            }
        }

        // Initialize with default or first address
        syncSelectedAddress();

        if (confirmNewAddrBtn) {
            confirmNewAddrBtn.addEventListener('click', async function() {
                // Quick client-side validation for the sub-form
                const name = document.getElementById('new_receiver_name').value;
                const phone = document.getElementById('new_receiver_phone').value;
                const addr = document.getElementById('new_shipping_address').value;
                const comm = document.getElementById('new_shipping_commune').value;
                const city = document.getElementById('new_shipping_city').value;
                const save = document.getElementById('save_for_future').checked;

                if (!name || !phone || !addr || !comm || !city) {
                    alert('Vui lòng điền đầy đủ thông tin địa chỉ mới.');
                    return;
                }

                // If save is checked, save it to DB immediately via AJAX
                if (save) {
                    confirmNewAddrBtn.disabled = true;
                    confirmNewAddrBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang lưu...';

                    try {
                        const response = await fetch('{{ route("addresses.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                receiver_name: name,
                                receiver_phone: phone,
                                address: addr,
                                commune: comm,
                                city: city,
                                is_default: false
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            // Add new card dynamically
                            const addrObj = data.address;
                            const optionsContainer = document.getElementById('address-options') || document.querySelector('.address-section');
                            
                            const newCardHtml = `
                                <div class="address-option selected" 
                                     data-name="${addrObj.receiver_name}"
                                     data-phone="${addrObj.receiver_phone}"
                                     data-address="${addrObj.address}"
                                     data-commune="${addrObj.commune}"
                                     data-city="${addrObj.city}">
                                    <div class="d-flex align-items-start gap-2 w-100">
                                        <input type="radio" name="selected_address_id" value="${addrObj.id}" checked class="mt-1">
                                        <div class="flex-grow-1">
                                            <div class="address-label">${addrObj.receiver_name}</div>
                                            <div class="address-contact">${addrObj.receiver_phone}</div>
                                            <div class="address-details">
                                                ${addrObj.address}, ${addrObj.commune}, ${addrObj.city}
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-link text-danger p-0 ms-auto btn-delete-address" 
                                                data-id="${addrObj.id}" title="Xóa địa chỉ">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            `;

                            // If total addresses was 0, replace the <p> tag
                            if (!document.getElementById('address-options')) {
                                optionsContainer.innerHTML = `<div id="address-options">${newCardHtml}</div>`;
                            } else {
                                document.getElementById('address-options').insertAdjacentHTML('afterbegin', newCardHtml);
                            }

                            // Re-bind click event to new card (or use delegation)
                            // For simplicity, let's refresh the binding or use delegation at the container
                            
                            addForm.style.display = 'none';
                            showAddFormBtn.style.display = 'flex';
                            alert('Địa chỉ đã được lưu và chọn.');
                        } else {
                            alert('Lỗi: ' + (data.message || 'Không thể lưu địa chỉ.'));
                        }
                    } catch (error) {
                        console.error('Error saving address:', error);
                        alert('Có lỗi mạng xảy ra khi lưu địa chỉ.');
                    } finally {
                        confirmNewAddrBtn.disabled = false;
                        confirmNewAddrBtn.innerHTML = 'Sử dụng địa chỉ này';
                    }
                }

                // Sync to hidden fields (regardless of save to DB)
                hiddenName.value = name;
                hiddenPhone.value = phone;
                hiddenAddr.value = addr;
                hiddenCommune.value = comm;
                hiddenCity.value = city;

                if (!save) {
                    alert('Đã chọn địa chỉ mới cho đơn hàng này.');
                    addForm.classList.add('border-primary'); // Highlight
                }
                
                syncSelectedAddress();
            });
        }

        // Use event delegation for address selection and deletion
        document.addEventListener('click', async function(e) {
            // Handle Selection
            const option = e.target.closest('.address-option');
            const deleteBtn = e.target.closest('.btn-delete-address');

            if (deleteBtn) {
                e.stopPropagation();
                const addressId = deleteBtn.getAttribute('data-id');
                if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
                    try {
                        const response = await fetch(`{{ url('/addresses') }}/${addressId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            deleteBtn.closest('.address-option').remove();
                            // If deleted was selected, re-select default
                            syncSelectedAddress();
                        }
                    } catch (error) {
                        console.error('Error deleting address:', error);
                    }
                }
                return;
            }

            if (option) {
                const options = document.querySelectorAll('.address-option');
                options.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                option.querySelector('input[type="radio"]').checked = true;
                
                // Close add form if open
                if (addForm) addForm.style.display = 'none';
                if (showAddFormBtn) showAddFormBtn.style.display = 'flex';
                
                syncSelectedAddress();
            }
        });

        if (showAddFormBtn) {
            showAddFormBtn.addEventListener('click', function() {
                this.style.display = 'none';
                addForm.style.display = 'block';
                // Unselect all saved addresses
                const options = document.querySelectorAll('.address-option');
                options.forEach(opt => {
                    opt.classList.remove('selected');
                    const radio = opt.querySelector('input[type="radio"]');
                    if (radio) radio.checked = false;
                });
                
                // Clear hidden fields to force validation on form if submitted
                hiddenName.value = '';
                hiddenPhone.value = '';
                hiddenAddr.value = '';
                hiddenCommune.value = '';
                hiddenCity.value = '';

                document.getElementById('new_receiver_name').focus();
            });
        }

        if (cancelAddBtn) {
            cancelAddBtn.addEventListener('click', function() {
                addForm.style.display = 'none';
                showAddFormBtn.style.display = 'flex';
                // Re-select default if exists
                const def = document.querySelector('input[name="selected_address_id"][checked]') || document.querySelector('input[name="selected_address_id"]');
                if (def) {
                    def.checked = true;
                    def.closest('.address-option').classList.add('selected');
                    syncSelectedAddress();
                }
            });
        }

        // Payment method toggle logic
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const bankDetails = document.getElementById('bank-transfer-details');
        const bankToggleIcon = document.querySelector('.toggle-bank-icon');

        function togglePaymentDetails() {
            const selected = document.querySelector('input[name="payment_method"]:checked');
            const isTransfer = (selected && selected.value === 'transfer');
            
            if (bankDetails) {
                bankDetails.style.display = isTransfer ? 'block' : 'none';
            }
            if (bankToggleIcon) {
                bankToggleIcon.style.display = isTransfer ? 'inline-block' : 'none';
                bankToggleIcon.style.transform = 'rotate(0deg)'; // Reset
            }
        }

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', togglePaymentDetails);
        });

        if (bankToggleIcon) {
            bankToggleIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (bankDetails) {
                    const isHidden = bankDetails.style.display === 'none';
                    bankDetails.style.display = isHidden ? 'block' : 'none';
                    this.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(-90deg)';
                }
            });
        }

        // Initial check
        togglePaymentDetails();

        checkoutForm.addEventListener('submit', function(e) {
            // Final check: if add form is open, sync those fields before submitting
            if (addForm.style.display !== 'none') {
                hiddenName.value = document.getElementById('new_receiver_name').value;
                hiddenPhone.value = document.getElementById('new_receiver_phone').value;
                hiddenAddr.value = document.getElementById('new_shipping_address').value;
                hiddenCommune.value = document.getElementById('new_shipping_commune').value;
                hiddenCity.value = document.getElementById('new_shipping_city').value;
            }

            if (!hiddenName.value) {
                e.preventDefault();
                alert('Vui lòng chọn hoặc nhập địa chỉ giao hàng.');
                return;
            }

            confirmOrderBtn.disabled = true;
            confirmOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> ĐANG XỬ LÝ...';
        });
    });
</script>
@endsection
