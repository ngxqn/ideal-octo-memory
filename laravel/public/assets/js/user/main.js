/**
 * main.js - File chứa các hàm utility chung cho Literary Haven Admin
 * (Thay thế cho admin_actions.js cũ)
 * Lưu ý: Dữ liệu sản phẩm được lưu trữ trong key 'morico_products'
 */

// Định nghĩa key lưu trữ chính
const DATA_KEY = 'morico_products';

/**
 * Lấy dữ liệu sản phẩm từ LocalStorage.
 * Trả về đối tượng { products: [...] }
 */
function getData() {
    try {
        const productsJSON = localStorage.getItem(DATA_KEY);
        const productsArray = productsJSON ? JSON.parse(productsJSON) : [];
        // Trả về định dạng mà script trong inventory.html mong muốn
        return { products: productsArray };
    } catch (error) {
        console.error(`Error getting data from localStorage (${DATA_KEY}):`, error);
        return { products: [] };
    }
}

/**
 * Lưu dữ liệu sản phẩm vào LocalStorage.
 * Yêu cầu đầu vào là đối tượng { products: [...] }
 */
function saveData(dataObject) {
    if (dataObject && Array.isArray(dataObject.products)) {
        try {
            localStorage.setItem(DATA_KEY, JSON.stringify(dataObject.products));
            return true;
        } catch (error) {
            console.error(`Error saving data to localStorage (${DATA_KEY}):`, error);
            return false;
        }
    }
    return false;
}

// initializeSampleUser removed as it conflicts with Laravel Auth

// [Optional: Thêm các hàm utility khác như formatCurrency, formatDate nếu cần]

// Ví dụ: format ngày tháng (dùng trong script cũ)
function formatDateTime(dateString) {
    if (!dateString) return "";
    try {
        const date = new Date(dateString);
        if (isNaN(date)) return "Lỗi định dạng ngày";
        const options = {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: false,
        };
        return date.toLocaleDateString("vi-VN", options).replace(',', '');
    } catch (e) {
        return "";
    }
}

/**
 * Global Notification Helper using Bootstrap Toasts
 * Creates and stacks toasts in the .toast-container
 */
function showNotification(message, type = 'success') {
    const container = document.querySelector('.toast-container');
    if (!container) return;

    // Create toast element
    const toastId = 'toast-' + Date.now();
    let bgClass = 'text-bg-success';
    if (type === 'error' || type === 'danger') bgClass = 'text-bg-danger';
    if (type === 'warning') bgClass = 'text-bg-warning';
    if (type === 'info') bgClass = 'text-bg-info';
    
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center border-0 ${bgClass}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    // Append to container
    container.insertAdjacentHTML('beforeend', toastHtml);

    // Initialize and show
    const toastEl = document.getElementById(toastId);
    if (!toastEl) return;

    // Use window.bootstrap if bootstrap is not directly available
    const bs = window.bootstrap || bootstrap;
    if (bs && bs.Toast) {
        const toast = new bs.Toast(toastEl, { delay: 3000 });
        toast.show();

        // Remove from DOM after hide
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    } else {
        console.error('Bootstrap Toast API not found.');
        // Fallback to alert if toast fails
        if (type === 'error') alert(message);
    }
}