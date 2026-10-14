<?php
/**
 * TechFusion Order Success & Invoice Receipt
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Models\Order;

$orderId = (int) ($_GET['id'] ?? 0);
$orderModel = new Order();
$order = $orderModel->getOrderWithItems($orderId);

if (!$order) {
    header("Location: index.php");
    exit;
}

$pageTitle = 'Order Confirmed #' . $order['id'] . ' - TechFusion';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="tf-container" style="padding-top: 3.5rem; padding-bottom: 5rem; max-width: 860px;">
    
    <!-- Success Banner -->
    <div class="tf-card animate-pulse-glow" style="text-align: center; padding: 3rem 2rem; margin-bottom: 2.5rem; border-color: var(--tf-success);">
        <div style="font-size: 3.5rem; margin-bottom: 1rem;">⚡</div>
        <span class="tf-badge tf-badge-success" style="margin-bottom: 1rem;">Payment & Order Verified</span>
        <h1 style="font-size: 2.25rem; margin-bottom: 0.75rem;">Hardware Order Confirmed!</h1>
        <p style="font-size: 1.125rem; max-width: 600px; margin: 0 auto 1.5rem auto;">
            Thank you, <strong style="color: var(--tf-text-primary);"><?= e($order['customer_name']) ?></strong>. Your shipment has been queued for warehouse staging and dispatch.
        </p>
        <div style="background: rgba(5, 8, 17, 0.75); display: inline-flex; align-items: center; gap: 1rem; padding: 0.75rem 1.5rem; border-radius: var(--tf-radius-full); border: 1px solid var(--tf-border-glass);">
            <span style="font-size: 0.8125rem; color: var(--tf-text-muted); text-transform: uppercase;">Tracking Reference:</span>
            <strong style="color: var(--tf-primary); font-family: var(--tf-font-headline); font-size: 1.125rem; letter-spacing: 0.05em;"><?= e($order['tracking_number']) ?></strong>
        </div>
    </div>

    <!-- Official Invoice Card -->
    <div class="tf-card" style="padding: 2.5rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; border-bottom: 1px solid var(--tf-border-glass); padding-bottom: 1.5rem;">
            <div>
                <div class="tf-logo" style="margin-bottom: 0.5rem;">
                    <div class="tf-logo-icon">⚡</div>
                    <div class="tf-logo-text">Tech<span>Fusion</span></div>
                </div>
                <div style="font-size: 0.8125rem; color: var(--tf-text-muted);">
                    Official Invoice & Dispatch Bill<br>
                    Date: <?= date('F d, Y - H:i:s', strtotime($order['created_at'])) ?>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-family: var(--tf-font-headline); font-size: 1.25rem; font-weight: 700; color: var(--tf-primary);">
                    Invoice #TF-<?= str_pad((string)$order['id'], 6, '0', STR_PAD_LEFT) ?>
                </div>
                <div class="tf-badge tf-badge-cyan" style="margin-top: 0.5rem;">Status: <?= e($order['order_status']) ?></div>
            </div>
        </div>

        <!-- Customer & Shipping Telemetry -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; font-size: 0.9375rem;">
            <div>
                <div style="font-family: var(--tf-font-headline); text-transform: uppercase; font-size: 0.75rem; color: var(--tf-text-muted); font-weight: 700; margin-bottom: 0.5rem;">
                    Billed & Shipped To:
                </div>
                <strong style="color: var(--tf-text-primary);"><?= e($order['customer_name']) ?></strong><br>
                <span><?= nl2br(e($order['shipping_address'])) ?></span><br>
                <span>Phone: <?= e($order['customer_phone']) ?></span><br>
                <span>Email: <?= e($order['customer_email']) ?></span>
            </div>
            <div>
                <div style="font-family: var(--tf-font-headline); text-transform: uppercase; font-size: 0.75rem; color: var(--tf-text-muted); font-weight: 700; margin-bottom: 0.5rem;">
                    Fulfillment Node:
                </div>
                <span>TechFusion Distribution Center #4</span><br>
                <span>Carrier: Priority Cyber Express</span><br>
                <span>Tracking ID: <strong style="color: var(--tf-primary);"><?= e($order['tracking_number']) ?></strong></span>
            </div>
        </div>

        <!-- Line Items Table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem; font-size: 0.9375rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--tf-border-glass); text-align: left; font-family: var(--tf-font-headline); color: var(--tf-text-muted); font-size: 0.8125rem;">
                    <th style="padding: 0.75rem 0;">Hardware Model</th>
                    <th style="padding: 0.75rem; text-align: center;">Qty</th>
                    <th style="padding: 0.75rem; text-align: right;">Unit Price</th>
                    <th style="padding: 0.75rem 0; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                        <td style="padding: 1rem 0; color: var(--tf-text-primary); font-weight: 600;">
                            <?= e($item['product_name']) ?>
                        </td>
                        <td style="padding: 1rem; text-align: center; color: var(--tf-text-muted);"><?= $item['quantity'] ?></td>
                        <td style="padding: 1rem; text-align: right; color: var(--tf-text-muted);">$<?= number_format((float)$item['unit_price'], 2) ?></td>
                        <td style="padding: 1rem 0; text-align: right; font-family: var(--tf-font-headline); color: var(--tf-primary); font-weight: 700;">
                            $<?= number_format((float)$item['unit_price'] * (int)$item['quantity'], 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="padding-top: 1.5rem; text-align: right; font-family: var(--tf-font-headline); font-size: 1.125rem; font-weight: 700;">Grand Total Paid:</td>
                    <td style="padding-top: 1.5rem; text-align: right; font-family: var(--tf-font-headline); font-size: 1.75rem; font-weight: 700; color: var(--tf-primary);">
                        $<?= number_format((float)$order['total_amount'], 2) ?>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Actions -->
        <div style="display: flex; gap: 1rem; justify-content: flex-end; flex-wrap: wrap;">
            <button type="button" class="btn-ghost-glass" onclick="window.print()">🖨️ Print Invoice</button>
            <a href="track-order.php?tracking=<?= urlencode($order['tracking_number']) ?>" class="btn-cyber">Live Tracking Status ⚡</a>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
