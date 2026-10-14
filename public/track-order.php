<?php
/**
 * TechFusion Order Tracking View
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Models\Order;

$orderModel = new Order();
$trackingQuery = trim($_GET['tracking'] ?? '');
$orderIdQuery = (int) ($_GET['order_id'] ?? 0);
$emailQuery = trim($_GET['email'] ?? '');

$foundOrder = null;
$searched = false;

if (!empty($trackingQuery)) {
    $searched = true;
    $foundOrder = $orderModel->getOrderByTracking($trackingQuery);
} elseif ($orderIdQuery > 0 && !empty($emailQuery)) {
    $searched = true;
    $foundOrder = $orderModel->getOrderWithItems($orderIdQuery, $emailQuery);
}

$pageTitle = 'Live Hardware Order Tracking - TechFusion';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="tf-container" style="padding-top: 3.5rem; padding-bottom: 5rem; max-width: 860px;">
    
    <div style="text-align: center; margin-bottom: 3rem;">
        <span class="tf-badge tf-badge-cyan" style="margin-bottom: 0.75rem;">Fulfillment Telemetry</span>
        <h1>Real-Time Order Tracking</h1>
        <p style="max-width: 540px; margin: 0 auto; color: var(--tf-text-muted);">
            Enter your unique shipment tracking reference (e.g. TF-2026-XXXX) or Order ID with email to monitor logistics status.
        </p>
    </div>

    <!-- Search Form Card -->
    <div class="tf-card" style="padding: 2rem; margin-bottom: 3rem;">
        <form method="GET" action="track-order.php" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="flex: 1 1 300px;">
                <input type="text" name="tracking" class="tf-input" placeholder="Tracking Reference (TF-2026-...)" value="<?= e($trackingQuery) ?>" required style="height: 48px; font-family: var(--tf-font-headline); font-weight: 600;">
            </div>
            <button type="submit" class="btn-cyber" style="height: 48px; min-width: 160px;">
                Track Status ⚡
            </button>
        </form>
    </div>

    <!-- Tracking Results -->
    <?php if ($searched): ?>
        <?php if ($foundOrder): ?>
            <?php
            $status = $foundOrder['order_status'];
            $steps = ['Pending', 'Processing', 'Shipped', 'Delivered'];
            $currentIdx = array_search($status, $steps, true);
            if ($currentIdx === false) $currentIdx = 1;
            ?>
            <div class="tf-card animate-fade-in-up" style="padding: 2.5rem; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.8125rem; color: var(--tf-text-muted); text-transform: uppercase;">Tracking Number</div>
                        <h2 style="font-size: 1.5rem; color: var(--tf-primary);"><?= e($foundOrder['tracking_number']) ?></h2>
                    </div>
                    <div style="text-align: right;">
                        <span class="tf-badge tf-badge-cyan" style="font-size: 0.8125rem;">Status: <?= e($status) ?></span>
                        <div style="font-size: 0.8125rem; color: var(--tf-text-muted); margin-top: 0.25rem;">
                            Updated: <?= date('M d, Y H:i', strtotime($foundOrder['updated_at'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Visual Progress Stepper -->
                <div style="position: relative; margin: 3rem 0; padding: 0 1rem;">
                    <div style="position: absolute; top: 16px; left: 5%; right: 5%; height: 3px; background: var(--tf-border-glass); z-index: 0;"></div>
                    <div style="position: absolute; top: 16px; left: 5%; width: <?= ($currentIdx / (count($steps) - 1)) * 90 ?>%; height: 3px; background: var(--tf-primary); box-shadow: 0 0 10px var(--tf-primary-glow); z-index: 0; transition: width 0.5s ease;"></div>
                    
                    <div style="display: flex; justify-content: space-between; position: relative; z-index: 1;">
                        <?php foreach ($steps as $idx => $stepName): ?>
                            <?php $isDone = $idx <= $currentIdx; ?>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                <div style="width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; background: <?= $isDone ? 'var(--tf-primary)' : 'var(--tf-surface-elevated)' ?>; color: <?= $isDone ? '#050811' : 'var(--tf-text-muted)' ?>; border: 2px solid <?= $isDone ? 'var(--tf-primary)' : 'var(--tf-border-glass)' ?>; box-shadow: <?= $isDone ? '0 0 12px var(--tf-primary-glow)' : 'none' ?>;">
                                    <?= $isDone ? '✓' : ($idx + 1) ?>
                                </div>
                                <span style="font-size: 0.8125rem; font-family: var(--tf-font-headline); font-weight: 600; color: <?= $isDone ? 'var(--tf-text-primary)' : 'var(--tf-text-dim)' ?>;">
                                    <?= $stepName ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Shipment Destination Info -->
                <div style="background: rgba(5, 8, 17, 0.6); padding: 1.5rem; border-radius: var(--tf-radius-md); border: 1px solid var(--tf-border-subtle); margin-bottom: 2rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; font-size: 0.875rem;">
                        <div>
                            <span style="color: var(--tf-text-muted); text-transform: uppercase; font-size: 0.75rem; font-weight: 700;">Recipient:</span>
                            <div style="font-weight: 600; color: var(--tf-text-primary); margin-top: 0.25rem;"><?= e($foundOrder['customer_name']) ?></div>
                        </div>
                        <div>
                            <span style="color: var(--tf-text-muted); text-transform: uppercase; font-size: 0.75rem; font-weight: 700;">Destination:</span>
                            <div style="color: var(--tf-text-primary); margin-top: 0.25rem;"><?= nl2br(e($foundOrder['shipping_address'])) ?></div>
                        </div>
                        <div>
                            <span style="color: var(--tf-text-muted); text-transform: uppercase; font-size: 0.75rem; font-weight: 700;">Carrier & Hub:</span>
                            <div style="color: var(--tf-primary); margin-top: 0.25rem;">Priority Logistics / Hub #4</div>
                        </div>
                    </div>
                </div>

                <!-- Products Summary -->
                <h4 style="font-size: 1rem; margin-bottom: 1rem;">Items In Consignment (<?= count($foundOrder['items']) ?>)</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach ($foundOrder['items'] as $it): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.875rem; padding: 0.5rem 0; border-bottom: 1px solid var(--tf-border-subtle);">
                            <span style="color: var(--tf-text-primary);"><?= e($it['product_name']) ?> &times; <?= $it['quantity'] ?></span>
                            <span style="color: var(--tf-primary); font-family: var(--tf-font-headline); font-weight: 700;">$<?= number_format((float)$it['unit_price'] * (int)$it['quantity'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        <?php else: ?>
            <div class="tf-card" style="text-align: center; padding: 4rem 1.5rem; border-color: var(--tf-error);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                <h3>Shipment Record Not Found</h3>
                <p style="color: var(--tf-text-muted); max-width: 480px; margin: 0 auto;">
                    We could not find an order with tracking number "<?= e($trackingQuery) ?>". Please double check your invoice email.
                </p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
