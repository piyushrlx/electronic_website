<?php
/**
 * TechFusion Slide-Out Cart Drawer Partial
 */
declare(strict_types=1);

if (!isset($cartService)) {
    $cartService = new \TechFusion\Services\CartService();
}
$cartItems = $cartService->getItems();
$cartSubtotal = $cartService->getSubtotal();
?>
<div class="tf-drawer-backdrop" id="cartDrawerBackdrop">
    <div class="tf-cart-drawer" id="cartDrawer">
        <div class="tf-cart-header">
            <h3 style="font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>Shopping Cart</span>
                <span class="tf-badge tf-badge-cyan" id="drawerItemCountBadge"><?= count($cartItems) ?> items</span>
            </h3>
            <button type="button" class="btn-ghost-glass btn-icon" id="cartDrawerClose" aria-label="Close Cart" style="width: 32px; height: 32px;">✕</button>
        </div>

        <div class="tf-cart-body" id="drawerCartBody">
            <?php if (empty($cartItems)): ?>
                <div style="text-align: center; padding: 3rem 1rem; color: var(--tf-text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.4;">🛒</div>
                    <h4 style="margin-bottom: 0.5rem; color: var(--tf-text-primary);">Your Cart is Empty</h4>
                    <p style="font-size: 0.875rem;">Explore our high-performance electronics and add items to your cart.</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="tf-card" style="padding: 0.75rem; display: flex; flex-direction: row; gap: 1rem; align-items: center;" id="cartItem-<?= $item['product_id'] ?>">
                            <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: var(--tf-radius-sm);">
                            <div style="flex: 1; min-width: 0;">
                                <h5 style="font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.25rem;">
                                    <?= e($item['name']) ?>
                                </h5>
                                <div style="font-family: var(--tf-font-headline); color: var(--tf-primary); font-size: 0.9375rem; font-weight: 700;">
                                    $<?= number_format($item['price'], 2) ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                                    <button type="button" class="btn-ghost-glass btn-sm" onclick="TechFusionCart.update(<?= $item['product_id'] ?>, <?= $item['quantity'] - 1 ?>)" style="padding: 0.1rem 0.5rem;">-</button>
                                    <span style="font-size: 0.875rem; font-weight: 600; min-width: 20px; text-align: center;"><?= $item['quantity'] ?></span>
                                    <button type="button" class="btn-ghost-glass btn-sm" onclick="TechFusionCart.update(<?= $item['product_id'] ?>, <?= $item['quantity'] + 1 ?>)" style="padding: 0.1rem 0.5rem;">+</button>
                                    <button type="button" class="btn-ghost-glass btn-sm" onclick="TechFusionCart.remove(<?= $item['product_id'] ?>)" style="margin-left: auto; color: var(--tf-error); padding: 0.1rem 0.5rem;" title="Remove Item">🗑️</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="tf-cart-footer">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <span style="color: var(--tf-text-muted); font-family: var(--tf-font-headline); text-transform: uppercase; font-size: 0.8125rem; font-weight: 600;">Estimated Subtotal</span>
                <span style="font-family: var(--tf-font-headline); font-size: 1.5rem; font-weight: 700; color: var(--tf-text-primary);" id="drawerSubtotal">$<?= number_format($cartSubtotal, 2) ?></span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="checkout.php" class="btn-cyber" style="width: 100%; text-align: center;">Proceed to Checkout ⚡</a>
                <a href="cart.php" class="btn-ghost-glass" style="width: 100%; text-align: center;">View Detailed Cart</a>
            </div>
        </div>
    </div>
</div>
