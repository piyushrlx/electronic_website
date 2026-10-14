<?php
/**
 * TechFusion Storefront Homepage & Catalog
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Models\Product;

$productModel = new Product();

$category = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;
$products = $productModel->getFilteredProducts($category, $search);
$categories = $productModel->getCategories();

$pageTitle = 'TechFusion - Cyber-Luxury Electronics & Spatial Computing';
require_once __DIR__ . '/../templates/header.php';
?>

<!-- Hero Banner Section -->
<section style="position: relative; padding: 4.5rem 0 3.5rem 0; overflow: hidden; border-bottom: 1px solid var(--tf-border-glass);">
    <div style="position: absolute; top: -120px; left: 50%; transform: translateX(-50%); width: 700px; height: 350px; background: radial-gradient(ellipse at center, rgba(0, 240, 255, 0.18) 0%, rgba(5, 8, 17, 0) 70%); pointer-events: none; z-index: 0;"></div>
    
    <div class="tf-container" style="position: relative; z-index: 1;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 2.5rem;">
            <!-- Hero Copy -->
            <div style="flex: 1 1 540px;">
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                    <span class="tf-badge tf-badge-cyan">
                        <span class="tf-badge-pip"></span>
                        Next-Generation Hardware 2026
                    </span>
                    <span class="tf-badge tf-badge-success">● In-Stock & Ready to Ship</span>
                </div>
                <h1 style="margin-bottom: 1.25rem;">
                    Pure Sensory Fidelity. <br>
                    <span style="color: var(--tf-primary);">Engineered Without Compromise.</span>
                </h1>
                <p style="font-size: 1.125rem; max-width: 580px; margin-bottom: 2rem;">
                    Experience flagship OLED cinema displays, intelligent frost-free refrigeration, and high-performance titanium smartphones with guaranteed manufacturer warranties.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="#catalogSection" class="btn-cyber">Explore Catalog ⚡</a>
                    <a href="track-order.php" class="btn-ghost-glass">Track Shipment</a>
                </div>
            </div>

            <!-- Hero Telemetry Card -->
            <div style="flex: 0 1 420px; width: 100%;">
                <div class="tf-card animate-pulse-glow" style="padding: 1.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                        <span style="font-family: var(--tf-font-headline); font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--tf-primary);">System Telemetry</span>
                        <span class="tf-badge tf-badge-cyan">Active Node</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        <div style="background: rgba(5, 8, 17, 0.6); padding: 1rem; border-radius: var(--tf-radius-md); border: 1px solid var(--tf-border-subtle);">
                            <div style="font-size: 0.75rem; color: var(--tf-text-muted); text-transform: uppercase;">Active Catalog</div>
                            <div style="font-family: var(--tf-font-headline); font-size: 1.75rem; font-weight: 700; color: var(--tf-text-primary);"><?= count($products) ?> SKUs</div>
                        </div>
                        <div style="background: rgba(5, 8, 17, 0.6); padding: 1rem; border-radius: var(--tf-radius-md); border: 1px solid var(--tf-border-subtle);">
                            <div style="font-size: 0.75rem; color: var(--tf-text-muted); text-transform: uppercase;">Dispatch Time</div>
                            <div style="font-family: var(--tf-font-headline); font-size: 1.75rem; font-weight: 700; color: var(--tf-success);">&lt; 24 Hrs</div>
                        </div>
                        <div style="background: rgba(5, 8, 17, 0.6); padding: 1rem; border-radius: var(--tf-radius-md); border: 1px solid var(--tf-border-subtle);">
                            <div style="font-size: 0.75rem; color: var(--tf-text-muted); text-transform: uppercase;">Security Tier</div>
                            <div style="font-family: var(--tf-font-headline); font-size: 1.75rem; font-weight: 700; color: var(--tf-secondary-light);">256-Bit</div>
                        </div>
                        <div style="background: rgba(5, 8, 17, 0.6); padding: 1rem; border-radius: var(--tf-radius-md); border: 1px solid var(--tf-border-subtle);">
                            <div style="font-size: 0.75rem; color: var(--tf-text-muted); text-transform: uppercase;">Warranty</div>
                            <div style="font-family: var(--tf-font-headline); font-size: 1.75rem; font-weight: 700; color: var(--tf-primary);">3 Years</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Catalog Section -->
<section id="catalogSection" style="padding: 4rem 0;">
    <div class="tf-container">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="tf-badge tf-badge-cyan" style="margin-bottom: 0.5rem;">Flagship Electronics</span>
                <h2>Browse Equipment</h2>
            </div>
            <div style="color: var(--tf-text-muted); font-size: 0.9375rem;">
                Showing <strong style="color: var(--tf-primary);" id="catalogCount"><?= count($products) ?></strong> available models
            </div>
        </div>

        <!-- Filter HUD Bar -->
        <div class="tf-filter-hud">
            <!-- Category Chips -->
            <div class="tf-chip-group">
                <button type="button" class="tf-chip <?= empty($category) ? 'active' : '' ?>" data-category="All">All Categories</button>
                <?php foreach ($categories as $cat): ?>
                    <button type="button" class="tf-chip <?= ($category === $cat) ? 'active' : '' ?>" data-category="<?= e($cat) ?>"><?= e($cat) ?>s</button>
                <?php endforeach; ?>
            </div>

            <!-- Search & Controls -->
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <!-- Live Search -->
                <div style="position: relative; width: 220px;">
                    <input type="text" id="catalogSearch" class="tf-input" placeholder="Search models or brands..." value="<?= e($search) ?>" style="padding-left: 2rem;">
                    <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); opacity: 0.5;">🔍</span>
                </div>

                <!-- Price Slider -->
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: var(--tf-text-muted);">
                    <span>Max:</span>
                    <input type="range" id="priceRange" min="500" max="2000" step="50" value="2000" style="accent-color: var(--tf-primary); width: 100px;">
                    <span id="priceRangeVal" style="color: var(--tf-primary); font-weight: 700; min-width: 50px;">$2000</span>
                </div>

                <!-- Sort Order -->
                <select id="sortSelect" class="tf-select" style="width: auto; padding: 0.5rem 0.85rem;">
                    <option value="featured">Featured First</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="name_asc">Alphabetical</option>
                </select>
            </div>
        </div>

        <!-- Dynamic Product Grid -->
        <div id="productGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
            <?php if (empty($products)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: var(--tf-text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                    <h3>No Products Found</h3>
                    <p>There are no products matching your selected criteria.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <article class="tf-card animate-fade-in-up" id="product-<?= $p['id'] ?>">
                        <div class="tf-card-media">
                            <span class="tf-badge tf-badge-cyan tf-card-badge">
                                <span class="tf-badge-pip"></span>
                                <?= $p['stock'] > 0 ? ($p['stock'] . ' IN STOCK') : 'OUT OF STOCK' ?>
                            </span>
                            <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                        </div>
                        <div class="tf-card-body">
                            <div class="tf-card-category"><?= e($p['brand']) ?> • <?= e($p['category']) ?></div>
                            <h3 class="tf-card-title">
                                <a href="product.php?id=<?= $p['id'] ?>"><?= e($p['name']) ?></a>
                            </h3>
                            <p class="tf-card-desc"><?= e($p['description']) ?></p>
                            <div class="tf-card-footer">
                                <div class="tf-card-price">$<?= number_format((float)$p['price'], 2) ?></div>
                                <button type="button" class="btn-cyber btn-sm" onclick="TechFusionCart.add(<?= $p['id'] ?>, 1)">
                                    Add ⚡
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
