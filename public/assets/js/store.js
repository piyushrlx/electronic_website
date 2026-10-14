/**
 * TechFusion Store Catalog Client Script
 * Handles real-time search, category switching, price filtering, and sorting
 */

const TechFusionStore = {
    init() {
        const searchInput = document.getElementById('catalogSearch');
        const priceRange = document.getElementById('priceRange');
        const priceLabel = document.getElementById('priceRangeVal');
        const sortSelect = document.getElementById('sortSelect');

        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => this.filterCatalog(), 300);
            });
        }

        if (priceRange && priceLabel) {
            priceRange.addEventListener('input', (e) => {
                priceLabel.textContent = `$${e.target.value}`;
                this.filterCatalog();
            });
        }

        if (sortSelect) {
            sortSelect.addEventListener('change', () => this.filterCatalog());
        }

        // Category filter chips
        document.querySelectorAll('.tf-chip[data-category]').forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.tf-chip[data-category]').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                this.filterCatalog();
            });
        });
    },

    async filterCatalog() {
        const activeChip = document.querySelector('.tf-chip[data-category].active');
        const category = activeChip ? activeChip.getAttribute('data-category') : 'All';
        const search = document.getElementById('catalogSearch')?.value || '';
        const maxPrice = document.getElementById('priceRange')?.value || '';
        const sort = document.getElementById('sortSelect')?.value || 'featured';

        const params = new URLSearchParams();
        if (category && category !== 'All') params.append('category', category);
        if (search.trim()) params.append('search', search.trim());
        if (maxPrice) params.append('max_price', maxPrice);
        if (sort) params.append('sort', sort);

        const grid = document.getElementById('productGrid');
        if (!grid) return;

        // Visual loading state
        grid.style.opacity = '0.5';

        try {
            const res = await fetch(`api/products.php?${params.toString()}`);
            const json = await res.json();
            grid.style.opacity = '1';

            if (!json.success || !json.data || json.data.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: var(--tf-text-muted);">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔍</div>
                        <h3>No Electronics Matched Your Criteria</h3>
                        <p style="font-size: 0.9375rem;">Try adjusting your search terms, price limit, or category filter.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            json.data.forEach(p => {
                html += `
                    <article class="tf-card animate-fade-in-up" id="product-${p.id}">
                        <div class="tf-card-media">
                            <span class="tf-badge tf-badge-cyan tf-card-badge">
                                <span class="tf-badge-pip"></span>
                                ${p.stock > 0 ? (p.stock + ' IN STOCK') : 'OUT OF STOCK'}
                            </span>
                            <img src="${p.image_url}" alt="${p.name}" loading="lazy">
                        </div>
                        <div class="tf-card-body">
                            <div class="tf-card-category">${p.brand} • ${p.category}</div>
                            <h3 class="tf-card-title">
                                <a href="product.php?id=${p.id}">${p.name}</a>
                            </h3>
                            <p class="tf-card-desc">${p.description || ''}</p>
                            <div class="tf-card-footer">
                                <div class="tf-card-price">$${parseFloat(p.price).toFixed(2)}</div>
                                <button type="button" class="btn-cyber btn-sm" onclick="TechFusionCart.add(${p.id}, 1)">
                                    Add ⚡
                                </button>
                            </div>
                        </div>
                    </article>
                `;
            });
            grid.innerHTML = html;
        } catch (err) {
            console.error('Filter Error:', err);
            grid.style.opacity = '1';
        }
    }
};

document.addEventListener('DOMContentLoaded', () => TechFusionStore.init());
