// --- CONSTANTS ---
const cartCountBadge = document.getElementById("cartCountBadge");
const cartLink = document.getElementById("cartLink");

/**
 * Update cart count display (Reads from localStorage)
 * In Batch 4, this might move to a server-side session or API.
 */
function updateCartCount() {
    if (!cartLink || !cartCountBadge) return;

    // 1. Get cart from localStorage
    const cart = JSON.parse(localStorage.getItem("cart")) || [];

    // 2. Calculate total quantity
    const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);

    // 3. Update UI
    if (totalCount > 0) {
        cartCountBadge.textContent = totalCount;
        cartCountBadge.classList.add("visible");
    } else {
        cartCountBadge.textContent = "0";
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
