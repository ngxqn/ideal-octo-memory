// --- CONSTANTS ---
const cartCountBadge = document.getElementById("cartCountBadge");
const cartLink = document.getElementById("cartLink");

/**
 * Update cart count display (Reads from Server API)
 */
async function updateCartCount() {
    if (!cartLink || !cartCountBadge) return;

    try {
        const url = cartCountBadge.getAttribute('data-url');
        if (!url) return;

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const totalCount = data.count || 0;

            if (totalCount > 0) {
                cartCountBadge.textContent = totalCount;
                cartCountBadge.classList.add("visible");
            } else {
                cartCountBadge.textContent = "0";
                cartCountBadge.classList.remove("visible");
            }
        }
    } catch (error) {
        console.error("Lỗi khi cập nhật số lượng giỏ hàng:", error);
    }
}

/**
 * Optimistically adjust cart count locally
 */
function adjustCartCountLocally(delta) {
    if (!cartCountBadge) return;
    let currentCount = parseInt(cartCountBadge.textContent) || 0;
    let newCount = currentCount + delta;
    if (newCount < 0) newCount = 0;

    cartCountBadge.textContent = newCount;
    if (newCount > 0) {
        cartCountBadge.classList.add("visible");
    } else {
        cartCountBadge.classList.remove("visible");
    }
}

// --- DOM CONTENT LOADED ---
document.addEventListener("DOMContentLoaded", () => {
    // Sync cart count on load
    updateCartCount();

    // --- SCROLL EFFECT ---
    (function () {
        const navBar = document.querySelector('.nav-bar');
        if (!navBar) return;

        let lastScrollY = window.scrollY;

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;

            if (currentScrollY > lastScrollY && currentScrollY > 50) {
                navBar.classList.add('nav-hidden');
            } else {
                navBar.classList.remove('nav-hidden');
            }
            lastScrollY = currentScrollY;
        }, { passive: true });
    })();
});

// --- DROPDOWN TOGGLE ---
document.addEventListener("click", e => {
    const userIcon = document.getElementById("userIcon");
    const dropdown = document.getElementById("userDropdown");

    if (!userIcon || !dropdown) return;

    if (userIcon.contains(e.target)) {
        e.stopPropagation();
        dropdown.classList.toggle("active");
    } else if (!dropdown.contains(e.target)) {
        dropdown.classList.remove("active");
    }
});

// --- STORAGE LISTENER ---
window.addEventListener('storage', (e) => {
    if (e.key === 'cart') {
        updateCartCount();
    }
});
