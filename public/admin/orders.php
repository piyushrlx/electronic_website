<?php
/**
 * TechFusion Admin Order Fulfillment Manager
 */
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

use TechFusion\Auth\AuthManager;
use TechFusion\Models\Order;

$auth = new AuthManager();
if (!$auth->check()) {
    header("Location: ../login.php");
    exit;
}

$orderModel = new Order();
$statusFilter = $_GET['status'] ?? null;
$message = '';

// Handle Status Change POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['_csrf_token'] ?? '')) {
        $message = "Session expired. Please try again.";
    } else {
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $newStatus = trim($_POST['order_status'] ?? '');
        $validStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

        if ($orderId > 0 && in_array($newStatus, $validStatuses, true)) {
            $orderModel->updateStatus($orderId, $newStatus);
            $auth->logAudit($auth->user()['id'], 'UPDATE_STATUS', 'orders', $orderId, "Order #{$orderId} status changed to {$newStatus}");
            $message = "Order #{$orderId} updated to {$newStatus}.";
        }
    }
}

$orders = $orderModel->getAllOrders(50, $statusFilter);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Fulfillment - TechFusion Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/techfusion-core.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
</head>
<body>

    <header class="tf-navbar">
        <div class="tf-container">
            <div class="tf-nav-inner">
                <a href="index.php" class="tf-logo">
                    <div class="tf-logo-icon">🛡️</div>
                    <div class="tf-logo-text">Tech<span>Fusion</span> Control</div>
                </a>
                <nav>
                    <ul class="tf-nav-links">
                        <li><a href="index.php" class="tf-nav-link">Overview</a></li>
                        <li><a href="products.php" class="tf-nav-link">Inventory</a></li>
                        <li><a href="orders.php" class="tf-nav-link active">Orders Fulfillment</a></li>
                        <li><a href="audit.php" class="tf-nav-link">Audit Trail</a></li>
                    </ul>
                </nav>
                <div class="tf-nav-actions">
                    <a href="../logout.php" class="btn-ghost-glass btn-sm" style="color: var(--tf-error);">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="tf-container" style="padding-top: 3rem; padding-bottom: 5rem;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="tf-badge tf-badge-cyan" style="margin-bottom: 0.5rem;">Fulfillment Hub</span>
                <h1>Customer Order Intake (<?= count($orders) ?>)</h1>
            </div>

            <!-- Filter Status Links -->
            <div class="tf-chip-group">
                <a href="orders.php" class="tf-chip <?= empty($statusFilter) ? 'active' : '' ?>">All</a>
                <a href="orders.php?status=Processing" class="tf-chip <?= $statusFilter === 'Processing' ? 'active' : '' ?>">Processing</a>
                <a href="orders.php?status=Shipped" class="tf-chip <?= $statusFilter === 'Shipped' ? 'active' : '' ?>">Shipped</a>
                <a href="orders.php?status=Delivered" class="tf-chip <?= $statusFilter === 'Delivered' ? 'active' : '' ?>">Delivered</a>
                <a href="orders.php?status=Cancelled" class="tf-chip <?= $statusFilter === 'Cancelled' ? 'active' : '' ?>">Cancelled</a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="tf-badge tf-badge-success" style="padding: 0.75rem 1.25rem; width: 100%; margin-bottom: 1.5rem; font-size: 0.875rem;">
                ✓ <?= e($message) ?>
            </div>
        <?php endif; ?>

        <div class="tf-card" style="padding: 0; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--tf-border-glass); text-align: left; font-family: var(--tf-font-headline); color: var(--tf-text-muted); font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 1rem 1.25rem;">ID & Tracking</th>
                        <th style="padding: 1rem 1.25rem;">Customer Details</th>
                        <th style="padding: 1rem 1.25rem;">Amount</th>
                        <th style="padding: 1rem 1.25rem;">Status & Transition</th>
                        <th style="padding: 1rem 1.25rem;">Placed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" style="padding: 3rem; text-align: center; color: var(--tf-text-muted);">
                                No orders currently found matching this filter criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                <td style="padding: 1rem 1.25rem;">
                                    <strong style="color: var(--tf-text-primary);">#<?= $o['id'] ?></strong><br>
                                    <a href="../track-order.php?tracking=<?= urlencode($o['tracking_number']) ?>" target="_blank" style="font-family: var(--tf-font-headline); color: var(--tf-primary); font-size: 0.8125rem;">
                                        <?= e($o['tracking_number']) ?> ↗
                                    </a>
                                </td>
                                <td style="padding: 1rem 1.25rem;">
                                    <strong style="color: var(--tf-text-primary);"><?= e($o['customer_name']) ?></strong><br>
                                    <span style="color: var(--tf-text-muted); font-size: 0.8125rem;"><?= e($o['customer_email']) ?> • <?= e($o['customer_phone']) ?></span>
                                </td>
                                <td style="padding: 1rem 1.25rem; font-family: var(--tf-font-headline); font-weight: 700; color: var(--tf-primary); font-size: 1rem;">
                                    $<?= number_format((float)$o['total_amount'], 2) ?>
                                </td>
                                <td style="padding: 1rem 1.25rem;">
                                    <form method="POST" action="orders.php" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="order_status" class="tf-select" style="padding: 0.35rem 0.65rem; font-size: 0.8125rem; width: auto;">
                                            <?php foreach (['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'] as $st): ?>
                                                <option value="<?= $st ?>" <?= $o['order_status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn-ghost-glass btn-sm">Update</button>
                                    </form>
                                </td>
                                <td style="padding: 1rem 1.25rem; color: var(--tf-text-muted); font-size: 0.8125rem;">
                                    <?= date('M d, Y H:i', strtotime($o['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>
