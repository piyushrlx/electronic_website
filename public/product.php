<?php
/**
 * TechFusion Product Detail View with Normalized Specs
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Models\Product;

$productModel = new Product();
$productId = (int) ($_GET['id'] ?? 0);

$product = $productModel->getProductWithSpecs($productId);

if (!$product) {
    header("Location: index.php");
    exit;
}

$pageTitle = $product['name'] . ' - TechFusion Specifications';
$pageDesc = $product['description'] ?? 'Detailed specifications and ordering for ' . $product['name'];

// JSON-LD Schema.org Structured Data
$jsonLd = [
    "@context" => "https://schema.org/",
    "@type" => "Product",
    "name" => $product['name'],
    "image" => [$product['image_url']],
    "description" => $product['description'] ?? '',
    "brand" => [
        "@type" => "Brand",
        "name" => $product['brand'],
    ],
    "offers" => [
        "@type" => "Offer",
        "priceCurrency" => "USD",
        "price" => (string)$product['price'],
        "availability" => ($product['stock'] > 0) ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
    ],
];

require_once __DIR__ . '/../templates/header.php';
?>
<script type="application/ld+json">
<?= json_encode($jsonLd, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?>
</script>
<?php

<div class="tf-container" style="padding-top: 2.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumb Navigation -->
    <nav style="display: flex; gap: 0.5rem; font-size: 0.875rem; color: var(--tf-text-muted); margin-bottom: 2rem;">
        <a href="index.php">Home</a>
        <span>/</span>
        <a href="index.php?category=<?= urlencode($product['category']) ?>"><?= e($product['category']) ?>s</a>
        <span>/</span>
        <span style="color: var(--tf-text-primary);"><?= e($product['name']) ?></span>
    </nav>

    <!-- Main Product Showcase Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 3.5rem; align-items: start;">
        
        <!-- Product Media Preview -->
        <div class="tf-card" style="padding: 1rem; overflow: hidden;">
            <div style="width: 100%; aspect-ratio: 4/3; border-radius: var(--tf-radius-md); overflow: hidden; background: #060a14;">
                <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1rem; padding: 0.5rem 0.25rem;">
                <span class="tf-badge tf-badge-cyan">● Authentic Factory Sealed</span>
                <span class="tf-badge tf-badge-success">● Free Express Delivery</span>
            </div>
        </div>

        <!-- Product Details & Ordering Form -->
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <span class="tf-badge tf-badge-cyan"><?= e($product['brand']) ?></span>
                <span class="tf-badge"><?= e($product['category']) ?></span>
                <span class="tf-badge <?= ($product['stock'] > 0) ? 'tf-badge-success' : 'tf-badge-error' ?>">
                    <span class="tf-badge-pip"></span>
                    <?= ($product['stock'] > 0) ? ($product['stock'] . ' Units in Stock') : 'Currently Out of Stock' ?>
                </span>
            </div>

            <h1 style="font-size: 2.25rem; margin-bottom: 1rem;"><?= e($product['name']) ?></h1>
            
            <div style="font-family: var(--tf-font-headline); font-size: 2.25rem; font-weight: 700; color: var(--tf-primary); margin-bottom: 1.5rem;">
                $<?= number_format((float)$product['price'], 2) ?>
            </div>

            <p style="font-size: 1.0625rem; line-height: 1.7; margin-bottom: 2rem;">
                <?= e($product['description']) ?>
            </p>

            <!-- Quantity & Add to Cart -->
            <?php if ($product['stock'] > 0): ?>
                <div style="background: var(--tf-surface-glass); border: 1px solid var(--tf-border-glass); border-radius: var(--tf-radius-lg); padding: 1.5rem; margin-bottom: 2.5rem;">
                    <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                        <div>
                            <label for="orderQty" class="tf-label">Select Quantity</label>
                            <input type="number" id="orderQty" value="1" min="1" max="<?= $product['stock'] ?>" class="tf-input" style="width: 100px; text-align: center;">
                        </div>
                        <div style="flex: 1; min-width: 200px; display: flex; align-items: flex-end;">
                            <button type="button" class="btn-cyber" style="width: 100%; height: 46px;" onclick="TechFusionCart.add(<?= $product['id'] ?>, parseInt(document.getElementById('orderQty').value || 1))">
                                Add to Cart ⚡
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="tf-badge tf-badge-error" style="padding: 1rem; width: 100%; justify-content: center; font-size: 0.9375rem; margin-bottom: 2.5rem;">
                    Product Temporarily Sold Out. Check back soon.
                </div>
            <?php endif; ?>

            <!-- Technical Specifications Table -->
            <div>
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>Hardware Specifications</span>
                    <span class="tf-badge tf-badge-cyan">Telemetry Matrix</span>
                </h3>
                
                <div class="tf-card" style="padding: 0; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9375rem;">
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600; width: 40%;">Category</td>
                                <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary); font-weight: 600;"><?= e($product['category']) ?></td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Brand / Manufacturer</td>
                                <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['brand']) ?></td>
                            </tr>

                            <!-- Category Specific Spec Rows -->
                            <?php if ($product['category'] === 'TV' && !empty($product['specs'])): ?>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Display Panel Size</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['screen_size'] ?? 'N/A') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Native Resolution</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['resolution'] ?? 'N/A') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Display Technology</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['display_tech'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Smart Platform OS</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['smart_os'] ?? 'N/A') ?></td>
                                </tr>
                            <?php elseif ($product['category'] === 'Refrigerator' && !empty($product['specs'])): ?>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Gross Capacity</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['capacity_liters'] ?? 'N/A') ?> Liters</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Door Architecture</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['door_type'] ?? 'N/A') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Defrosting Mechanism</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['defrost_type'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Energy Star Efficiency</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= str_repeat('★ ', (int)($product['specs']['energy_rating'] ?? 3)) ?></td>
                                </tr>
                            <?php elseif ($product['category'] === 'Mobile' && !empty($product['specs'])): ?>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">RAM & Storage</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['ram_storage'] ?? 'N/A') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Battery Cell Capacity</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['battery_mah'] ?? 'N/A') ?> mAh</td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Optics Array</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['camera_mp'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 600;">Cellular Telemetry</td>
                                    <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);"><?= e($product['specs']['network_type'] ?? '5G') ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
