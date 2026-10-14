<?php
/**
 * TechFusion Secure Checkout Flow
 * Atomic order creation, CSRF protection, and payment method selection.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Services\CartService;
use TechFusion\Models\Order;

$cartService = new CartService();
$items = $cartService->getItems();
$subtotal = $cartService->getSubtotal();

if (empty($items)) {
    header("Location: cart.php");
    exit;
}

// Promo calculation
$promoCode = trim($_GET['coupon'] ?? $_POST['coupon'] ?? '');
$discount = 0.0;
if (strtoupper($promoCode) === 'TECH10') {
    $discount = round($subtotal * 0.10, 2);
}
$shipping = ($subtotal > 500) ? 0.0 : 49.99;
$total = max(0.0, round($subtotal - $discount + $shipping, 2));

$errors = [];

// Handle Order Placement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. CSRF Verification
    $token = $_POST['_csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $errors[] = "Security session expired. Please refresh and try again.";
    }

    // 2. Validate Inputs
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'razorpay';

    if (empty($name) || strlen($name) < 2) {
        $errors[] = "Full legal name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required for shipment confirmation.";
    }
    if (empty($phone) || strlen($phone) < 7) {
        $errors[] = "A contact telephone number is required.";
    }
    if (empty($address) || strlen($address) < 10) {
        $errors[] = "Complete delivery address with ZIP/PIN code is required.";
    }

    // 3. Process Transaction if Valid
    if (empty($errors)) {
        try {
            $orderModel = new Order();
            $result = $orderModel->createOrder([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
            ], $items, $total);

            // Clear session cart upon confirmed creation
            $cartService->clear();

            // Store order confirmation info in session
            $_SESSION['latest_order_id'] = $result['order_id'];
            $_SESSION['latest_tracking'] = $result['tracking_number'];

            header("Location: order-success.php?id=" . $result['order_id']);
            exit;
        } catch (\Throwable $e) {
            $errors[] = "Transaction failed: " . $e->getMessage();
        }
    }
}

$pageTitle = 'Secure Checkout - TechFusion';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="tf-container" style="padding-top: 3rem; padding-bottom: 5rem;">
    <div style="margin-bottom: 2rem;">
        <span class="tf-badge tf-badge-cyan" style="margin-bottom: 0.5rem;">Encrypted Transaction</span>
        <h1>Complete Your Hardware Order</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="tf-card" style="padding: 1.25rem; border-color: var(--tf-error); margin-bottom: 2rem;">
            <div style="color: var(--tf-error); font-weight: 700; margin-bottom: 0.5rem;">⚠️ Please correct the following errors:</div>
            <ul style="color: var(--tf-text-primary); margin-left: 1.5rem; font-size: 0.9375rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="checkout.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 3rem; align-items: start;">
        <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="coupon" value="<?= e($promoCode) ?>">

        <!-- Left: Customer Information & Delivery -->
        <div class="tf-card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>1. Shipping & Contact Details</span>
            </h3>

            <div class="tf-form-group">
                <label for="cName" class="tf-label">Full Legal Name *</label>
                <input type="text" id="cName" name="customer_name" class="tf-input" required placeholder="e.g. Piyush Bhatt" value="<?= e($_POST['customer_name'] ?? '') ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="tf-form-group">
                    <label for="cEmail" class="tf-label">Email Confirmation *</label>
                    <input type="email" id="cEmail" name="customer_email" class="tf-input" required placeholder="name@example.com" value="<?= e($_POST['customer_email'] ?? '') ?>">
                </div>
                <div class="tf-form-group">
                    <label for="cPhone" class="tf-label">Telephone Contact *</label>
                    <input type="tel" id="cPhone" name="customer_phone" class="tf-input" required placeholder="+91 9876543210" value="<?= e($_POST['customer_phone'] ?? '') ?>">
                </div>
            </div>

            <div class="tf-form-group">
                <label for="cAddr" class="tf-label">Complete Street & Delivery Address *</label>
                <textarea id="cAddr" name="shipping_address" rows="3" class="tf-textarea" required placeholder="Street address, apartment or suite, city, postal code"><?= e($_POST['shipping_address'] ?? '') ?></textarea>
            </div>

            <h3 style="font-size: 1.25rem; margin: 2rem 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>2. Payment Methodology</span>
            </h3>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <label class="tf-card" style="padding: 1rem; cursor: pointer; display: flex; align-items: center; gap: 1rem;">
                    <input type="radio" name="payment_method" value="razorpay" checked style="accent-color: var(--tf-primary); width: 18px; height: 18px;">
                    <div>
                        <div style="font-weight: 700; color: var(--tf-text-primary);">Razorpay Gateway (Test Mode / Instant Dispatch)</div>
                        <div style="font-size: 0.8125rem; color: var(--tf-text-muted);">Supports Cards, UPI, Netbanking with 256-bit TLS encryption</div>
                    </div>
                </label>

                <label class="tf-card" style="padding: 1rem; cursor: pointer; display: flex; align-items: center; gap: 1rem;">
                    <input type="radio" name="payment_method" value="cod" style="accent-color: var(--tf-primary); width: 18px; height: 18px;">
                    <div>
                        <div style="font-weight: 700; color: var(--tf-text-primary);">Pay Upon Delivery (COD)</div>
                        <div style="font-size: 0.8125rem; color: var(--tf-text-muted);">Pay securely by card or cash upon hardware arrival</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Right: Order Summary Confirmation -->
        <div class="tf-card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--tf-border-subtle); padding-bottom: 0.75rem;">
                Order Summary (<?= count($items) ?> items)
            </h3>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; max-height: 240px; overflow-y: auto;">
                <?php foreach ($items as $item): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem;">
                        <span style="color: var(--tf-text-primary);"><?= e($item['name']) ?> &times; <?= $item['quantity'] ?></span>
                        <span style="color: var(--tf-primary); font-family: var(--tf-font-headline); font-weight: 700;">$<?= number_format($item['subtotal'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="border-top: 1px solid var(--tf-border-subtle); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9375rem;">
                <div style="display: flex; justify-content: space-between; color: var(--tf-text-muted);">
                    <span>Subtotal</span>
                    <span style="color: var(--tf-text-primary);">$<?= number_format($subtotal, 2) ?></span>
                </div>
                <?php if ($discount > 0): ?>
                    <div style="display: flex; justify-content: space-between; color: var(--tf-success);">
                        <span>Promo Discount</span>
                        <span>-$<?= number_format($discount, 2) ?></span>
                    </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; color: var(--tf-text-muted);">
                    <span>Express Dispatch</span>
                    <span><?= $shipping === 0.0 ? '<strong style="color: var(--tf-success);">FREE</strong>' : ('$' . number_format($shipping, 2)) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--tf-border-subtle); margin-top: 0.5rem; padding-top: 1rem;">
                    <span style="font-family: var(--tf-font-headline); font-size: 1.25rem; font-weight: 700;">Final Amount</span>
                    <span style="font-family: var(--tf-font-headline); font-size: 1.75rem; font-weight: 700; color: var(--tf-primary);">$<?= number_format($total, 2) ?></span>
                </div>
            </div>

            <button type="submit" class="btn-cyber" style="width: 100%; margin-top: 2rem; height: 50px;">
                Authorize & Place Order ⚡
            </button>
            <div style="text-align: center; margin-top: 1rem; font-size: 0.75rem; color: var(--tf-text-muted);">
                🔒 256-bit TLS encrypted transaction. Zero data retention of raw card numbers.
            </div>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
