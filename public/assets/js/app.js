/**
 * TechFusion Global Client Engine
 * Handles Cart Drawer, Toast Notifications, Theme Toggling, and Telemetry
 */

const TechFusion = {
    init() {
        this.initTheme();
        this.initCartDrawer();
    },

    // 1. Theme Management (Dark / Light)
    initTheme() {
        const toggleBtn = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const savedTheme = localStorage.getItem('tf_theme') || 'dark';

        document.documentElement.setAttribute('data-theme', savedTheme);
        if (themeIcon) {
            themeIcon.textContent = savedTheme === 'dark' ? '☀️' : '🌙';
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const current = document.documentElement.getAttribute('data-theme') || 'dark';
                const next = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('tf_theme', next);
                if (themeIcon) {
                    themeIcon.textContent = next === 'dark' ? '☀️' : '🌙';
                }
            });
        }
    },

    // 2. Slide-Out Cart Drawer Controls
    initCartDrawer() {
        const openBtn = document.getElementById('cartDrawerBtn');
        const closeBtn = document.getElementById('cartDrawerClose');
        const backdrop = document.getElementById('cartDrawerBackdrop');

        if (openBtn && backdrop) {
            openBtn.addEventListener('click', () => backdrop.classList.add('open'));
        }

        if (closeBtn && backdrop) {
            closeBtn.addEventListener('click', () => backdrop.classList.remove('open'));
        }

        if (backdrop) {
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    backdrop.classList.remove('open');
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && backdrop && backdrop.classList.contains('open')) {
                backdrop.classList.remove('open');
            }
        });
    },

    // 3. Global Toast Notifications
    toast(message, type = 'info') {
        const container = document.getElementById('tfToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'tf-card animate-fade-in-up';
        toast.style.padding = '0.75rem 1.25rem';
        toast.style.minWidth = '260px';
        toast.style.fontSize = '0.875rem';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '0.75rem';
        toast.style.border = type === 'error' ? '1px solid var(--tf-error)' : '1px solid var(--tf-primary)';
        toast.style.boxShadow = '0 8px 30px rgba(0, 0, 0, 0.5)';

        const icon = type === 'error' ? '⚠️' : (type === 'success' ? '✅' : '⚡');
        toast.innerHTML = `<span>${icon}</span><span style="color: var(--tf-text-primary);">${message}</span>`;

        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }
};

// 4. Cart Operations API Helper
const TechFusionCart = {
    async add(productId, quantity = 1) {
        try {
            const res = await fetch('api/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'add', product_id: productId, quantity: quantity })
            });
            const data = await res.json();
            if (data.success) {
                this.updateUI(data);
                TechFusion.toast(data.message || 'Item added to cart.', 'success');
                // Open drawer on add
                const backdrop = document.getElementById('cartDrawerBackdrop');
                if (backdrop) backdrop.classList.add('open');
            } else {
                TechFusion.toast(data.message || 'Could not add item.', 'error');
            }
        } catch (err) {
            console.error('Cart Error:', err);
            TechFusion.toast('Network error updating cart.', 'error');
        }
    },

    async update(productId, quantity) {
        try {
            const res = await fetch('api/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update', product_id: productId, quantity: quantity })
            });
            const data = await res.json();
            if (data.success) {
                this.updateUI(data);
            }
        } catch (err) {
            console.error('Cart Update Error:', err);
        }
    },

    async remove(productId) {
        try {
            const res = await fetch('api/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'remove', product_id: productId })
            });
            const data = await res.json();
            if (data.success) {
                this.updateUI(data);
                TechFusion.toast('Item removed from cart.', 'info');
            }
        } catch (err) {
            console.error('Cart Remove Error:', err);
        }
    },

    updateUI(data) {
        // 1. Update Navbar Counter
        const navCount = document.getElementById('navCartCount');
        if (navCount) {
            navCount.textContent = data.cartCount;
            navCount.classList.remove('cart-badge-bounce');
            void navCount.offsetWidth; // trigger reflow
            navCount.classList.add('cart-badge-bounce');
        }

        // 2. Update Drawer Badges & Subtotal
        const badge = document.getElementById('drawerItemCountBadge');
        if (badge) badge.textContent = `${data.items ? data.items.length : 0} items`;

        const subtotal = document.getElementById('drawerSubtotal');
        if (subtotal) subtotal.textContent = `$${parseFloat(data.subtotal || 0).toFixed(2)}`;

        // 3. Render Drawer Items
        const body = document.getElementById('drawerCartBody');
        if (!body) return;

        if (!data.items || data.items.length === 0) {
            body.innerHTML = `
                <div style="text-align: center; padding: 3rem 1rem; color: var(--tf-text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.4;">🛒</div>
                    <h4 style="margin-bottom: 0.5rem; color: var(--tf-text-primary);">Your Cart is Empty</h4>
                    <p style="font-size: 0.875rem;">Explore our high-performance electronics and add items to your cart.</p>
                </div>
            `;
            return;
        }

        let html = '<div style="display: flex; flex-direction: column; gap: 1rem;">';
        data.items.forEach(item => {
            html += `
                <div class="tf-card" style="padding: 0.75rem; display: flex; flex-direction: row; gap: 1rem; align-items: center;" id="cartItem-${item.product_id}">
                    <img src="${item.image_url}" alt="${item.name}" style="width: 70px; height: 70px; object-fit: cover; border-radius: var(--tf-radius-sm);">
                    <div style="flex: 1; min-width: 0;">
                        <h5 style="font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.25rem;">
                            ${item.name}
                        </h5>
                        <div style="font-family: var(--tf-font-headline); color: var(--tf-primary); font-size: 0.9375rem; font-weight: 700;">
                            $${parseFloat(item.price).toFixed(2)}
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                            <button type="button" class="btn-ghost-glass btn-sm" onclick="TechFusionCart.update(${item.product_id}, ${item.quantity - 1})" style="padding: 0.1rem 0.5rem;">-</button>
                            <span style="font-size: 0.875rem; font-weight: 600; min-width: 20px; text-align: center;">${item.quantity}</span>
                            <button type="button" class="btn-ghost-glass btn-sm" onclick="TechFusionCart.update(${item.product_id}, ${item.quantity + 1})" style="padding: 0.1rem 0.5rem;">+</button>
                            <button type="button" class="btn-ghost-glass btn-sm" onclick="TechFusionCart.remove(${item.product_id})" style="margin-left: auto; color: var(--tf-error); padding: 0.1rem 0.5rem;" title="Remove Item">🗑️</button>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        body.innerHTML = html;
    }
};

document.addEventListener('DOMContentLoaded', () => TechFusion.init());
