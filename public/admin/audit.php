<?php
/**
 * TechFusion Security Audit Trail View
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

$pdo = Database::getConnection();

$stmt = $pdo->query("
    SELECT al.*, a.username as admin_username
    FROM audit_logs al
    LEFT JOIN admins a ON al.admin_id = a.id
    ORDER BY al.id DESC
    LIMIT 100
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Audit Trail - TechFusion Admin</title>
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
                        <li><a href="orders.php" class="tf-nav-link">Orders Fulfillment</a></li>
                        <li><a href="audit.php" class="tf-nav-link active">Audit Trail</a></li>
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
                <span class="tf-badge tf-badge-cyan" style="margin-bottom: 0.5rem;">Security Telemetry</span>
                <h1>Administrative Audit Logs (<?= count($logs) ?> Events)</h1>
            </div>
            <span class="tf-badge tf-badge-success">● Immutable Log Active</span>
        </div>

        <div class="tf-card" style="padding: 0; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--tf-border-glass); text-align: left; font-family: var(--tf-font-headline); color: var(--tf-text-muted); font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 1rem 1.25rem;">Timestamp</th>
                        <th style="padding: 1rem 1.25rem;">Staff Member</th>
                        <th style="padding: 1rem 1.25rem;">Action</th>
                        <th style="padding: 1rem 1.25rem;">Entity / Target</th>
                        <th style="padding: 1rem 1.25rem;">Details</th>
                        <th style="padding: 1rem 1.25rem;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" style="padding: 3rem; text-align: center; color: var(--tf-text-muted);">
                                No security audit events logged yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                                <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-dim); white-space: nowrap;">
                                    <?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td style="padding: 0.85rem 1.25rem; font-weight: 600; color: var(--tf-text-primary);">
                                    <?= e($log['admin_username'] ?? 'System/Guest') ?>
                                </td>
                                <td style="padding: 0.85rem 1.25rem;">
                                    <span class="tf-badge tf-badge-cyan"><?= e($log['action_type']) ?></span>
                                </td>
                                <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-muted);">
                                    <?= e($log['target_entity']) ?> <?= $log['target_id'] ? ('#' . $log['target_id']) : '' ?>
                                </td>
                                <td style="padding: 0.85rem 1.25rem; color: var(--tf-text-primary);">
                                    <?= e($log['details']) ?>
                                </td>
                                <td style="padding: 0.85rem 1.25rem; font-family: monospace; color: var(--tf-text-dim);">
                                    <?= e($log['ip_address']) ?>
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
