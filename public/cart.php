<?php
/**
 * TechFusion Dedicated Cart Review Page
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Services\CartService;

$cartService = new CartService();
$items = $cartService->getItems();
$subtotal = $cartService->getSubtotal();

// Shipping & Promo calculation
$promoCode = trim($_GET['coupon'] ?? '');
$discount = 0.0;
$promoMessage = '';

if (!empty($promoCode)) {
    if (strtoupper($promoCode) === 'TECH10') {
        $discount = round($subtotal * 0.10, 2);
        $promoMessage = 'Promo TECH10 applied: 10% Discount!';
    } else {
        $promoMessage = 'Invalid promo code.';
    }
}

$shipping = ($subtotal > 500 || $subtotal === 0.0) ? 0.0 : 49.99;
$total = max(0.0, round($subtotal - $discount + $shipping, 2));

$pageTitle = 'Your Shopping Cart - TechFusion';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="tf-container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <span class="tf-badge tf-badge-cyan" style="margin-bottom: 0.5rem;">Cart Overview</span>
            <h1>Shopping Cart Review</h1>
        </div>
        <a href="index.php" class="btn-ghost-glass btn-sm">← Continue Shopping</a>
    </div>

    <?php if (empty($items)): ?>
        <div class="tf-card" style="text-align: center; padding: 5rem 1.5rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.4;">🛒</div>
            <h2 style="margin-bottom: 0.5rem;">Your Cart is Empty</h2>
            <p style="margin-bottom: 2rem;">You have not added any electronic hardware to your order yet.</p>
            <a href="index.php" class="btn-cyber">Browse Flagship Products ⚡</a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 3rem; align-items: start;">
            
            <!-- Items Table/List -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <?php foreach ($items as $item): ?>
                    <div class="tf-card" style="padding: 1.25rem; display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center;">
                        <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>" style="width: 90px; height: 90px; object-fit: cover; border-radius: var(--tf-radius-md);">
                        <div style="flex: 1 1 200px;">
                            <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--tf-primary); font-family: var(--tf-font-headline); font-weight: 700;">
                                <?= e($item['brand']) ?> • <?= e($item['category']) ?>
                            </div>
                            <h3 style="font-size: 1.125rem; margin-bottom: 0.25rem;">
                                <a href="product.php?id=<?= $item['product_id'] ?>"><?= e($item['name']) ?></a>
                            </h3>
                            <div style="font-family: var(--tf-font-headline); color: var(--tf-text-muted); font-size: 0.9375rem;">
                                Unit Price: <strong style="color: var(--tf-text-primary);">$<?= number_format($item['price'], 2) ?></strong>
                            </div>
                        </div>

                        <!-- Quantity Spinner -->
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <button type="button" class="btn-ghost-glass btn-sm" onclick="TechFusionCart.update(<?= $item['product_id'] ?>, <?= $item['quantity'] - 1 ?>); location.reload();">-</button>
                            <span style="font-size: 1rem; font-weight: 700; min-width: 28px; text-align: center;"><?= $item['quantity'] ?></span>
                            <button type="button" class="btn-ghost-glass btn-sm" onclick="TechFusionCart.update(<?= $item['product_id'] ?>, <?= $item['quantity'] + 1 ?>); location.reload();">+</button>
                        </div>

                        <!-- Line Total -->
                        <div style="font-family: var(--tf-font-headline); font-size: 1.25rem; font-weight: 700; color: var(--tf-primary); min-width: 90px; text-align: right;">
                            $<?= number_format($item['subtotal'], 2) ?>
                        </div>

                        <!-- Remove -->
                        <button type="button" class="btn-ghost-glass btn-icon btn-sm" onclick="TechFusionCart.remove(<?= $item['product_id'] ?>); location.reload();" style="color: var(--tf-error);" title="Remove Item">
                            🗑️
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary Card -->
            <div class="tf-card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--tf-border-subtle); padding-bottom: 0.75rem;">
                    Order Summary
                </h3>

                <!-- Subtotal -->
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; color: var(--tf-text-muted);">
                    <span>Gross Subtotal</span>
                    <span style="color: var(--tf-text-primary); font-weight: 600;">$<?= number_format($subtotal, 2) ?></span>
                </div>

                <!-- Discount -->
                <?php if ($discount > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; color: var(--tf-success);">
                        <span>Promo Discount (10%)</span>
                        <span style="font-weight: 700;">-$<?= number_format($discount, 2) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Shipping -->
                <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; color: var(--tf-text-muted);">
                    <span>Express Dispatch</span>
                    <span style="color: var(--tf-text-primary); font-weight: 600;">
                        <?= $shipping === 0.0 ? '<strong style="color: var(--tf-success);">FREE</strong>' : ('$' . number_format($shipping, 2)) ?>
                    </span>
                </div>

                <!-- Total -->
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--tf-border-subtle); padding-top: 1rem; margin-bottom: 2rem;">
                    <span style="font-family: var(--tf-font-headline); font-size: 1.125rem; font-weight: 700;">Total Payable</span>
                    <span style="font-family: var(--tf-font-headline); font-size: 1.75rem; font-weight: 700; color: var(--tf-primary);">$<?= number_format($total, 2) ?></span>
                </div>

                <!-- Promo Form -->
                <form method="GET" action="cart.php" style="margin-bottom: 2rem;">
                    <label for="couponInput" class="tf-label">Promo / Coupon Code</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" id="couponInput" name="coupon" class="tf-input" placeholder="e.g. TECH10" value="<?= e($promoCode) ?>">
                        <button type="submit" class="btn-ghost-glass btn-sm">Apply</button>
                    </div>
                    <?php if (!empty($promoMessage)): ?>
                        <div style="font-size: 0.8125rem; margin-top: 0.5rem; color: <?= $discount > 0 ? 'var(--tf-success)' : 'var(--tf-error)' ?>;">
                            <?= e($promoMessage) ?>
                        </div>
                    <?php endif; ?>
                </form>

                <!-- Proceed to Checkout -->
                <a href="checkout.php?coupon=<?= urlencode($promoCode) ?>" class="btn-cyber" style="width: 100%; text-align: center; height: 48px;">
                    Proceed to Checkout ⚡
                </a>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
