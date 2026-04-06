window.onload = function () {
    let users = JSON.parse(localStorage.getItem("users")) || [];
    let addUserModal, resetPassModal;

    try {
        addUserModal = new bootstrap.Modal(document.getElementById('add-user-modal'));
        resetPassModal = new bootstrap.Modal(document.getElementById('reset-modal'));
    } catch (e) {
        console.warn("Bootstrap modals not initialized", e);
    }

    function renderUsers() {
        const tbody = document.getElementById("users-table-body");
        tbody.innerHTML = "";
        users.forEach(user => {
            const tr = document.createElement("tr");
            const badgeClass = user.status === "active" ? "bg-success" : "bg-danger";
            const statusText = user.status === "active" ? "Đang hoạt động" : "Đã khóa";
            const roleMap = {
                'admin': { text: 'Quản trị viên', class: 'text-danger' },
                'staff': { text: 'Nhân viên', class: 'text-primary' },
                'customer': { text: 'Khách hàng', class: 'text-secondary' }
            };
            const roleInfo = roleMap[user.role] || roleMap['customer'];
            const loggedInUser = localStorage.getItem("loggedInUser");
            const isSelf = user.username === loggedInUser;

            tr.innerHTML = `
                <td><span class="fw-bold text-primary">#${user.id}</span></td>
                <td><span class="fw-bold">${user.fullname}</span></td>
                <td><span class="fw-bold ${roleInfo.class}">${roleInfo.text}</span></td>
                <td>${user.username}</td>
                <td><span class="text-muted" style="letter-spacing: 2px;">••••••</span></td>
                <td>${user.phone}</td>
                <td>${user.date}</td>
                <td class="text-center"><span class="badge rounded-pill ${badgeClass}">${statusText}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-warning btn-reset" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;" data-username="${user.username}">
                        <i class="fas fa-key me-1"></i> Đặt lại
                    </button>
                    <button class="btn btn-sm ${user.status === "active" ? "btn-outline-danger btn-lock" : "btn-outline-success btn-unlock"}" 
                        style="padding: 0.2rem 0.5rem; font-size: 0.8rem;" 
                        data-id="${user.id}" 
                        ${isSelf ? 'disabled title="Không thể tự khóa tài khoản của mình"' : ''}>
                        ${user.status === "active" ? '<i class="fas fa-lock me-1"></i> Khóa' : '<i class="fas fa-unlock me-1"></i> Mở khóa'}
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById("total-users").textContent = users.length;
        document.getElementById("active-users").textContent = users.filter(u => u.status === "active").length;
        document.getElementById("locked-users").textContent = users.filter(u => u.status === "locked").length;

        attachResetEvents();
        attachLockEvents();
    }

    // Thêm khách hàng
    document.getElementById("add-user-btn").addEventListener("click", () => {
        if (addUserModal) addUserModal.show();
    });

    document.getElementById("confirm-add-user").addEventListener("click", () => {
        const fullname = document.getElementById("new-fullname").value.trim();
        const username = document.getElementById("new-username").value.trim();
        const password = document.getElementById("new-user-password").value.trim();
        const phone = document.getElementById("new-phone").value.trim();
        const status = document.getElementById("new-status").value;
        const role = document.getElementById("new-role").value;
        const errorBox = document.getElementById("add-user-error");

        errorBox.style.display = "none";
        errorBox.textContent = "";

        if (!fullname || !username || !password || !phone) {
            errorBox.textContent = "Vui lòng nhập đầy đủ thông tin.";
            errorBox.style.display = "block";
            return;
        }

        if (password.length < 6) {
            errorBox.textContent = "Mật khẩu phải ít nhất 6 ký tự.";
            errorBox.style.display = "block";
            return;
        }

        if (users.some(u => u.username === username)) {
            errorBox.textContent = "Tên đăng nhập đã tồn tại.";
            errorBox.style.display = "block";
            return;
        }

        const newUser = {
            id: Date.now(),
            fullname,
            username,
            password,
            phone,
            date: new Date().toLocaleDateString("vi-VN"),
            status,
            role
        };

        users.push(newUser);
        localStorage.setItem("users", JSON.stringify(users));
        renderUsers();
        if (addUserModal) addUserModal.hide();

        document.getElementById("new-fullname").value = "";
        document.getElementById("new-username").value = "";
        document.getElementById("new-user-password").value = "";
        document.getElementById("new-phone").value = "";
        document.getElementById("new-status").value = "active";
    });

    // Đặt lại mật khẩu
    function attachResetEvents() {
        document.querySelectorAll(".btn-reset").forEach(btn => {
            btn.addEventListener("click", () => {
                const username = btn.getAttribute("data-username");
                const user = users.find(u => u.username === username);
                if (!user) return;

                document.getElementById("reset-username").textContent = user.username;
                document.getElementById("current-password").textContent = user.password;
                if (resetPassModal) resetPassModal.show();

                document.getElementById("confirm-reset").onclick = () => {
                    const newPass = document.getElementById("new-password").value.trim();
                    const confirmPass = document.getElementById("confirm-password").value.trim();
                    const errorBox = document.getElementById("password-error");

                    errorBox.style.display = "none";
                    errorBox.textContent = "";

                    if (newPass.length < 6) {
                        errorBox.textContent = "Mật khẩu phải ít nhất 6 ký tự.";
                        errorBox.style.display = "block";
                        return;
                    }

                    if (newPass !== confirmPass) {
                        errorBox.textContent = "Mật khẩu xác nhận không khớp.";
                        errorBox.style.display = "block";
                        return;
                    }

                    user.password = newPass;
                    user.password = newPass;
                    localStorage.setItem("users", JSON.stringify(users));
                    alert("Đặt lại mật khẩu thành công!");
                    renderUsers();
                    if (resetPassModal) resetPassModal.hide();

                    document.getElementById("new-password").value = "";
                    document.getElementById("confirm-password").value = "";
                };
            });
        });
    }

    // Logic Khởi tạo mẫu (nếu cần)
    const initBtn = document.getElementById("init-sample-btn");
    if (initBtn) {
        initBtn.addEventListener("click", () => {
            if (confirm("Bạn có muốn khởi tạo dữ liệu mẫu (ghi đè dữ liệu hiện tại)?")) {
                const sampleUsers = [
                    { id: 1, fullname: "Quản trị viên", username: "admin", password: "123", phone: "0123456789", date: "01/01/2023", status: "active", role: "admin" },
                    { id: 2, fullname: "Nhân viên Kho", username: "staff", password: "123", phone: "0987654321", date: "02/01/2023", status: "active", role: "staff" },
                    { id: 3, fullname: "Nguyễn Văn Khách", username: "customer1", password: "123", phone: "0909123456", date: "05/01/2023", status: "active", role: "customer" }
                ];
                localStorage.setItem("users", JSON.stringify(sampleUsers));
                users = sampleUsers;
                renderUsers();
            }
        });
    }

    const refreshBtn = document.getElementById("refresh-btn");
    if (refreshBtn) {
        refreshBtn.addEventListener("click", () => {
            users = JSON.parse(localStorage.getItem("users")) || [];
            renderUsers();
        });
    }

    // Khóa/Mở khóa tài khoản
    function attachLockEvents() {
        document.querySelectorAll(".btn-lock, .btn-unlock").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = parseInt(btn.getAttribute("data-id"));
                const user = users.find(u => u.id === id);
                if (!user) return;

                user.status = user.status === "active" ? "locked" : "active";
                localStorage.setItem("users", JSON.stringify(users));
                renderUsers();
            });
        });
    }

    renderUsers();
};
