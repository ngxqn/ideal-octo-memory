@extends('layouts.admin')

@section('title', 'Quản lý người dùng - MORICO')
@section('page_title')
    <i class="fa-solid fa-users me-2" style="color: var(--brand-gold);"></i> Quản lý người dùng
@endsection

@section('content')

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="h6 text-muted mb-1">Tổng cộng</h4>
                    <p class="h3 fw-bold mb-0 text-primary">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                    <i class="fa-solid fa-users fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="h6 text-muted mb-1">Đang hoạt động</h4>
                    <p class="h3 fw-bold mb-0 text-success">{{ number_format($activeUsers) }}</p>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                    <i class="fa-solid fa-user-check fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="h6 text-muted mb-1">Đã khóa</h4>
                    <p class="h3 fw-bold mb-0 text-danger">{{ number_format($lockedUsers) }}</p>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                    <i class="fa-solid fa-user-lock fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Table Area -->
<div class="card shadow-sm border p-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-transparent border-bottom-0 pt-3 pb-0">
        <h3 class="h5 mb-0 fw-bold"><i class="fa-solid fa-table text-primary me-2"></i> Danh sách Người dùng</h3>
        <div class="d-flex gap-2 flex-wrap mt-2 mt-sm-0">
            <button class="btn btn-sm rounded-pill text-white shadow-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#addUserModal"
                style="background-color: #ad21f3;">
                <i class="fa-solid fa-user-plus me-1"></i> Thêm Người dùng
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                <i class="fa-solid fa-refresh me-1"></i> Làm mới
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-4 align-items-end border rounded p-3 bg-light">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Tìm kiếm:</label>
                <input type="text" name="search" class="form-control form-control-sm shadow-sm" placeholder="Tên, username, email, SĐT..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Vai trò:</label>
                <select name="role" class="form-select form-select-sm shadow-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Trạng thái:</label>
                <select name="is_active" class="form-select form-select-sm shadow-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Đã khóa</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold shadow-sm">
                    <i class="fa-solid fa-filter me-1"></i> Lọc dữ liệu
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm flex-grow-1 fw-bold shadow-sm">Xóa lọc</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-top mt-2">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 20%">Họ tên / Tên đăng nhập</th>
                        <th style="width: 15%">Email / SĐT</th>
                        <th class="text-center" style="width: 10%">Vai trò</th>
                        <th class="text-center" style="width: 15%">Ngày đăng ký</th>
                        <th class="text-center" style="width: 15%">Trạng thái</th>
                        <th class="text-end" style="width: 20%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="small text-muted">#{{ $user->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $user->full_name }}</div>
                                <div class="small text-primary">@ {{ $user->username }}</div>
                            </td>
                            <td>
                                <div class="small fw-medium">{{ $user->email }}</div>
                                <div class="small text-muted">{{ $user->phone }}</div>
                            </td>
                            <td class="text-center">
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger rounded-pill px-3" style="font-size: 0.75rem;">ADMIN</span>
                                @else
                                    <span class="badge bg-info text-dark rounded-pill px-3" style="font-size: 0.75rem;">KHÁCH</span>
                                @endif
                            </td>
                            <td class="text-center small text-muted">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                @if($user->is_active)
                                    <span class="badge bg-success rounded-pill px-3 status-badge" data-id="{{ $user->id }}">Hoạt động</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3 status-badge" data-id="{{ $user->id }}">Đã khóa</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-warning fw-bold px-2 py-1" onclick="openResetPasswordModal({{ $user->id }}, '{{ $user->username }}')" title="Đặt lại mật khẩu">
                                    <i class="fa-solid fa-key small"></i>
                                </button>
                                
                                @if($user->id !== Auth::id())
                                    <button class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} fw-bold px-3 py-1 ms-1 toggle-lock-btn" 
                                            data-id="{{ $user->id }}" 
                                            onclick="toggleUserLock({{ $user->id }}, {{ $user->is_active ? 1 : 0 }})">
                                        <i class="fa-solid {{ $user->is_active ? 'fa-lock' : 'fa-unlock' }} me-1"></i>
                                        {{ $user->is_active ? 'Khóa' : 'Mở' }}
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-light disabled fw-bold px-3 py-1 ms-1 border" title="Không thể tự khóa mình">
                                        <i class="fa-solid fa-user-shield me-1"></i> Tôi
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted italic">Không tìm thấy người dùng nào phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal: Add User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg border-0 shadow-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="addUserModalLabel">
                    <i class="fa-solid fa-user-plus me-2"></i> Thêm người dùng mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addUserForm" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control shadow-sm" required placeholder="Nguyễn Văn A">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control shadow-sm" required placeholder="user123">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control shadow-sm" required placeholder="email@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control shadow-sm" required placeholder="0901234567">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Mật khẩu khởi tạo <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control shadow-sm" required minlength="6" placeholder="******">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select shadow-sm" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Kích hoạt</label>
                        <select name="is_active" class="form-select shadow-sm">
                            <option value="1">Có</option>
                            <option value="0">Tạm khóa</option>
                        </select>
                    </div>

                    <hr class="mt-4 mb-2">
                    <p class="small text-muted fw-bold mb-0 text-uppercase"><i class="fa-solid fa-map-marker-alt me-1"></i> Thông tin địa chỉ</p>
                    
                    <div class="col-12">
                        <label class="form-label small fw-bold">Địa chỉ <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control shadow-sm" placeholder="Số nhà, tên đường..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Phường/Xã/Đặc khu <span class="text-danger">*</span></label>
                        <input type="text" name="commune" class="form-control shadow-sm" placeholder="Phường 1..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control shadow-sm" placeholder="Hồ Chí Minh...">
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-top-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold" id="submitAddUser">Tạo người dùng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog border-0">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">
                    <i class="fa-solid fa-key me-2"></i> Đặt lại mật khẩu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small mb-3">Đang đặt lại mật khẩu cho tài khoản: <strong id="resetTargetUsername" class="text-primary font-monospace"></strong></p>
                
                <form id="resetPasswordForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="new_pw" class="form-control shadow-sm" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" id="confirm_pw" class="form-control shadow-sm" required placeholder="Nhập lại mật khẩu mới">
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-top-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning rounded-pill px-5 fw-bold" id="submitResetPassword">Cập nhật mật khẩu</button>
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
        store: "{{ route('admin.users.store') }}",
        toggleActive: "{{ route('admin.users.toggle-active', ':id') }}",
        resetPassword: "{{ route('admin.users.reset-password', ':id') }}"
    };

    const MODALS = {
        add: new bootstrap.Modal(document.getElementById('addUserModal')),
        reset: new bootstrap.Modal(document.getElementById('resetPasswordModal'))
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

    // Toggle Lock Action
    async function toggleUserLock(id, currentStatus) {
        if (!confirm('Bạn có chắc chắn muốn ' + (currentStatus ? 'khóa' : 'mở khóa') + ' tài khoản này?')) return;

        try {
            const url = ROUTES.toggleActive.replace(':id', id);
            const res = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();

            if (res.ok && data.success) {
                showToast(data.message);
                setTimeout(() => window.location.reload(), 500); // Reload to reflect stats and table changes
            } else {
                showToast(data.message || 'Lỗi xảy ra.', 'error');
            }
        } catch (e) {
            console.error(e);
            showToast('Lỗi kết nối.', 'error');
        }
    }

    // Reset Password Action
    let currentResetId = null;
    function openResetPasswordModal(id, username) {
        currentResetId = id;
        document.getElementById('resetTargetUsername').textContent = username;
        document.getElementById('resetPasswordForm').reset();
        MODALS.reset.show();
    }

    document.getElementById('submitResetPassword').addEventListener('click', async function() {
        const newPw = document.getElementById('new_pw').value;
        const confirmPw = document.getElementById('confirm_pw').value;

        if (newPw.length < 6) {
            showToast('Mật khẩu phải có ít nhất 6 ký tự.', 'error');
            return;
        }

        if (newPw !== confirmPw) {
            showToast('Mật khẩu xác nhận không khớp.', 'error');
            return;
        }

        const btn = this;
        btn.disabled = true;

        try {
            const url = ROUTES.resetPassword.replace(':id', currentResetId);
            const res = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password: newPw })
            });
            const data = await res.json();

            if (res.ok && data.success) {
                showToast(data.message);
                MODALS.reset.hide();
            } else {
                showToast(data.message || 'Lỗi xảy ra.', 'error');
            }
        } catch (e) {
            showToast('Lỗi kết nối.', 'error');
        } finally {
            btn.disabled = false;
        }
    });

    // Add User Action
    document.getElementById('submitAddUser').addEventListener('click', async function() {
        const form = document.getElementById('addUserForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const btn = this;
        btn.disabled = true;

        try {
            const res = await fetch(ROUTES.store, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });
            const result = await res.json();

            if (res.ok && result.success) {
                showToast(result.message);
                MODALS.add.hide();
                setTimeout(() => window.location.reload(), 500);
            } else {
                if (res.status === 422) {
                    const errors = Object.values(result.errors).flat().join('\n');
                    showToast(errors, 'error');
                } else {
                    // Prevent leaking raw SQL or system errors
                    showToast('Đã có lỗi hệ thống xảy ra. Vui lòng thử lại sau hoặc liên hệ kỹ thuật.', 'error');
                    console.error('System Error:', result.message || 'Unknown');
                }
            }
        } catch (e) {
            showToast('Lỗi kết nối.', 'error');
        } finally {
            btn.disabled = false;
        }
    });
</script>
@endsection
