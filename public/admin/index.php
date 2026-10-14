<?php
/**
 * TechFusion Admin Dashboard
 * Analytics, sales telemetry, and quick administration.
 */
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

use TechFusion\Auth\AuthManager;
use TechFusion\Database\Database;

$auth = new AuthManager();
if (!$auth->check()) {
    header("Location: ../login.php");
    exit;
}

$user = $auth->user();
$pdo = Database::getConnection();

// Aggregate KPIs
$totalOrders = (int) $pdo->query("SELECT count(*) FROM orders")->fetchColumn();
$totalRevenue = (float) $pdo->query("SELECT COALESCE(sum(total_amount), 0) FROM orders WHERE order_status != 'Cancelled'")->fetchColumn();
$totalProducts = (int) $pdo->query("SELECT count(*) FROM products")->fetchColumn();
$lowStockCount = (int) $pdo->query("SELECT count(*) FROM products WHERE stock <= 5")->fetchColumn();

// Recent Orders
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Low Stock Items
$lowStockItems = $pdo->query("SELECT id, name, category, stock, price FROM products WHERE stock <= 10 ORDER BY stock ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Control Center - TechFusion Admin';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/techfusion-core.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
</head>
<body>

    <!-- Admin Nav -->
    <header class="tf-navbar" style="border-bottom-color: var(--tf-border-glass-glow);">
        <div class="tf-container">
            <div class="tf-nav-inner">
                <a href="index.php" class="tf-logo">
                    <div class="tf-logo-icon">🛡️</div>
                    <div class="tf-logo-text">Tech<span>Fusion</span> <span style="font-size: 0.8125rem; color: var(--tf-text-muted); font-weight: 500;">Control</span></div>
                </a>

                <nav>
                    <ul class="tf-nav-links">
                        <li><a href="index.php" class="tf-nav-link active">Overview</a></li>
                        <li><a href="products.php" class="tf-nav-link">Inventory</a></li>
                        <li><a href="orders.php" class="tf-nav-link">Orders Fulfillment</a></li>
                        <li><a href="audit.php" class="tf-nav-link">Audit Trail</a></li>
                        <li><a href="../index.php" class="tf-nav-link" target="_blank">Storefront ↗</a></li>
                    </ul>
                </nav>

                <div class="tf-nav-actions">
                    <span class="tf-badge tf-badge-cyan">
                        <?= e($user['username']) ?> (<?= e($user['role']) ?>)
                    </span>
                    <a href="../logout.php" class="btn-ghost-glass btn-sm" style="color: var(--tf-error);">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="tf-container" style="padding-top: 3rem; padding-bottom: 5rem;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="tf-badge tf-badge-cyan" style="margin-bottom: 0.5rem;">Operations Hub</span>
                <h1>Executive Telemetry Overview</h1>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="products.php?action=new" class="btn-cyber btn-sm">+ Add Hardware SKU</a>
                <a href="orders.php" class="btn-ghost-glass btn-sm">Process Shipments</a>
            </div>
        </div>

        <!-- KPI Metric Cards Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            
            <div class="tf-card" style="padding: 1.5rem;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 700; margin-bottom: 0.5rem;">
                    Gross Sales Revenue
                </div>
                <div style="font-family: var(--tf-font-headline); font-size: 2rem; font-weight: 700; color: var(--tf-primary);">
                    $<?= number_format($totalRevenue, 2) ?>
                </div>
                <div style="font-size: 0.8125rem; color: var(--tf-success); margin-top: 0.5rem;">● Verified & Settled</div>
            </div>

            <div class="tf-card" style="padding: 1.5rem;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 700; margin-bottom: 0.5rem;">
                    Total Consignments
                </div>
                <div style="font-family: var(--tf-font-headline); font-size: 2rem; font-weight: 700; color: var(--tf-text-primary);">
                    <?= $totalOrders ?> Orders
                </div>
                <div style="font-size: 0.8125rem; color: var(--tf-text-muted); margin-top: 0.5rem;">Across all categories</div>
            </div>

            <div class="tf-card" style="padding: 1.5rem;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 700; margin-bottom: 0.5rem;">
                    Active Catalog
                </div>
                <div style="font-family: var(--tf-font-headline); font-size: 2rem; font-weight: 700; color: var(--tf-secondary-light);">
                    <?= $totalProducts ?> SKUs
                </div>
                <div style="font-size: 0.8125rem; color: var(--tf-text-muted); margin-top: 0.5rem;">TV, Fridge, Mobile</div>
            </div>

            <div class="tf-card" style="padding: 1.5rem; <?= $lowStockCount > 0 ? 'border-color: var(--tf-warning);' : '' ?>">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--tf-text-muted); font-family: var(--tf-font-headline); font-weight: 700; margin-bottom: 0.5rem;">
                    Critical Stock Alerts
                </div>
                <div style="font-family: var(--tf-font-headline); font-size: 2rem; font-weight: 700; color: <?= $lowStockCount > 0 ? 'var(--tf-warning)' : 'var(--tf-success)' ?>;">
                    <?= $lowStockCount ?> Models
                </div>
                <div style="font-size: 0.8125rem; color: <?= $lowStockCount > 0 ? 'var(--tf-warning)' : 'var(--tf-success)' ?>; margin-top: 0.5rem;">
                    <?= $lowStockCount > 0 ? '⚠️ Stock below 5 units' : '● Inventory Optimal' ?>
                </div>
            </div>

        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 2.5rem;">
            
            <!-- Recent Orders Queue -->
            <div class="tf-card" style="padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem;">Recent Order Intake</h3>
                    <a href="orders.php" class="btn-ghost-glass btn-sm">All Orders →</a>
                </div>

                <?php if (empty($recentOrders)): ?>
                    <p style="color: var(--tf-text-muted); font-size: 0.875rem;">No customer orders placed yet.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach ($recentOrders as $ro): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--tf-border-subtle); font-size: 0.875rem;">
                                <div>
                                    <strong style="color: var(--tf-text-primary);"><?= e($ro['customer_name']) ?></strong>
                                    <div style="color: var(--tf-text-muted); font-size: 0.75rem;"><?= e($ro['tracking_number']) ?></div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-family: var(--tf-font-headline); font-weight: 700; color: var(--tf-primary);">$<?= number_format((float)$ro['total_amount'], 2) ?></div>
                                    <span class="tf-badge tf-badge-cyan" style="font-size: 0.6875rem;"><?= e($ro['order_status']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Inventory Watchlist -->
            <div class="tf-card" style="padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem;">Inventory Watchlist</h3>
                    <a href="products.php" class="btn-ghost-glass btn-sm">Full Stock Sheet →</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($lowStockItems as $lsi): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--tf-border-subtle); font-size: 0.875rem;">
                            <div>
                                <strong style="color: var(--tf-text-primary);"><?= e($lsi['name']) ?></strong>
                                <div style="color: var(--tf-text-muted); font-size: 0.75rem;"><?= e($lsi['category']) ?> • $<?= number_format((float)$lsi['price'], 2) ?></div>
                            </div>
                            <div>
                                <span class="tf-badge <?= ($lsi['stock'] <= 5) ? 'tf-badge-warning' : 'tf-badge-success' ?>">
                                    <?= $lsi['stock'] ?> units left
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </main>

</body>
</html>
