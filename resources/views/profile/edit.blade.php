@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân - Morico Bakery')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user/auth.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/user/profile.css') }}">
<style>
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px; color: var(--primary-color); font-weight: bold; }
    .btn-morico-outline { border: 1px solid var(--primary-color); color: var(--primary-color); background: transparent; transition: all 0.3s; }
    .btn-morico-outline:hover { background: var(--primary-color); color: white; }
</style>
@endsection

@section('content')
    <div class="profile-container">
        <div class="profile-card mx-auto">
            <h1 class="page-title text-center">Hồ sơ cá nhân</h1>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- THÔNG TIN ĐĂNG NHẬP (PASSWORD CHANGE) -->
            <div class="section-header">
                <div><i class="fa-solid fa-shield-halved"></i> Thông tin đăng nhập</div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" value="{{ $user->username }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mật khẩu</label>
                    <button class="btn btn-morico-outline w-100" type="button" data-bs-toggle="collapse" data-bs-target="#changePwSection">
                        Đổi mật khẩu
                    </button>
                </div>
                
                <div class="col-12">
                    <div id="changePwSection" class="collapse mt-2 p-3 border rounded shadow-sm {{ ($errors->has('current_password') || $errors->has('password')) ? 'show' : '' }}">
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Nhập MK cũ" required>
                                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Mật khẩu mới</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nhập MK mới" required>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại MK mới" required>
                                </div>
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-morico px-5 py-2">Xác nhận đổi mật khẩu</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- THÔNG TIN CÁ NHÂN -->
            <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                @csrf
                @method('PUT')
                <div class="section-header">
                    <div><i class="fa-solid fa-user-circle"></i> Thông tin cá nhân</div>
                    <button type="button" id="editBtn" class="btn btn-morico btn-sm px-3 py-1">Sửa</button>
                </div>
                
                <div class="row g-3" id="personalInfoFields">
                    <div class="col-12">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" id="fullname" value="{{ old('full_name', $user->full_name) }}" disabled required>
                        @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $user->email) }}" disabled required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone', $user->phone) }}" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" disabled required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Số nhà, tên đường/phố <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" id="address" value="{{ old('address', $user->address) }}" disabled required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phường/xã/đặc khu <span class="text-danger">*</span></label>
                        <input type="text" name="commune" class="form-control @error('commune') is-invalid @enderror" id="commune" value="{{ old('commune', $user->commune) }}" disabled required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tỉnh/thành phố <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" id="city" value="{{ old('city', $user->city) }}" disabled required>
                    </div>
                </div>

                <div class="profile-actions pt-4">
                    <a href="{{ route('home') }}" class="btn btn-morico-outline px-4 text-decoration-none">Quay về</a>
                    <button type="submit" id="saveBtn" class="btn btn-morico px-5" style="display:none;">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBtn = document.getElementById('editBtn');
        const saveBtn = document.getElementById('saveBtn');
        const personalInputs = document.querySelectorAll('#personalInfoFields input');

        editBtn.addEventListener('click', function() {
            const isEditing = editBtn.textContent.trim() === 'Hủy';
            
            if (!isEditing) {
                personalInputs.forEach(input => input.disabled = false);
                editBtn.textContent = 'Hủy';
                editBtn.classList.replace('btn-morico', 'btn-morico-outline');
                saveBtn.style.display = 'inline-block';
                document.getElementById('fullname').focus();
            } else {
                // To properly cancel, we would need to revert the values, 
                // but a simple approach is to just redirect back to the page to reset.
                window.location.reload();
            }
        });
        
        // Show editing state if there are errors (form was submitted and failed)
        const hasErrors = {{ $errors->hasAny(['full_name', 'email', 'phone', 'address', 'commune', 'city']) ? 'true' : 'false' }};
        if (hasErrors) {
             personalInputs.forEach(input => input.disabled = false);
             editBtn.textContent = 'Hủy';
             editBtn.classList.replace('btn-morico', 'btn-morico-outline');
             saveBtn.style.display = 'inline-block';
        }
    });
</script>
@endsection
