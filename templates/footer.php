<?php
/**
 * TechFusion Global Footer Partial
 */
declare(strict_types=1);
?>
    <!-- Global Cart Drawer Component -->
    <?php require_once __DIR__ . '/cart-drawer.php'; ?>

    <!-- Footer Section -->
    <footer style="background: var(--tf-surface); border-top: 1px solid var(--tf-border-glass); margin-top: 5rem; padding: 4rem 0 2rem 0;">
        <div class="tf-container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 3rem; margin-bottom: 3rem;">
                <!-- Column 1: Brand -->
                <div>
                    <div class="tf-logo" style="margin-bottom: 1rem;">
                        <div class="tf-logo-icon">⚡</div>
                        <div class="tf-logo-text">Tech<span>Fusion</span></div>
                    </div>
                    <p style="font-size: 0.9375rem; color: var(--tf-text-muted); line-height: 1.6;">
                        Next-generation electronics marketplace delivering flagship OLED displays, smart temperature control systems, and cutting-edge mobile technologies.
                    </p>
                    <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                        <span class="tf-badge tf-badge-cyan">● 100% Genuine</span>
                        <span class="tf-badge tf-badge-success">● Verified Warranty</span>
                    </div>
                </div>

                <!-- Column 2: Categories -->
                <div>
                    <h4 style="font-family: var(--tf-font-headline); text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.875rem; margin-bottom: 1.25rem; color: var(--tf-text-primary);">
                        Product Categories
                    </h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem;">
                        <li><a href="index.php?category=TV" style="color: var(--tf-text-muted); font-size: 0.9375rem;">OLED & 4K UHD Displays</a></li>
                        <li><a href="index.php?category=Refrigerator" style="color: var(--tf-text-muted); font-size: 0.9375rem;">Smart Inverter Refrigeration</a></li>
                        <li><a href="index.php?category=Mobile" style="color: var(--tf-text-muted); font-size: 0.9375rem;">Flagship 5G Smartphones</a></li>
                        <li><a href="index.php" style="color: var(--tf-text-muted); font-size: 0.9375rem;">Browse Complete Catalog</a></li>
                    </ul>
                </div>

                <!-- Column 3: Customer Care -->
                <div>
                    <h4 style="font-family: var(--tf-font-headline); text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.875rem; margin-bottom: 1.25rem; color: var(--tf-text-primary);">
                        Customer Support
                    </h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem;">
                        <li><a href="track-order.php" style="color: var(--tf-text-muted); font-size: 0.9375rem;">Live Order Tracking</a></li>
                        <li><a href="cart.php" style="color: var(--tf-text-muted); font-size: 0.9375rem;">Shopping Cart Review</a></li>
                        <li><a href="login.php" style="color: var(--tf-text-muted); font-size: 0.9375rem;">Staff & Admin Access</a></li>
                        <li><a href="mailto:support@techfusion.com" style="color: var(--tf-text-muted); font-size: 0.9375rem;">Email Support (support@techfusion.com)</a></li>
                    </ul>
                </div>

                <!-- Column 4: Security & Payment -->
                <div>
                    <h4 style="font-family: var(--tf-font-headline); text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.875rem; margin-bottom: 1.25rem; color: var(--tf-text-primary);">
                        Secure Checkout
                    </h4>
                    <p style="font-size: 0.875rem; color: var(--tf-text-muted); margin-bottom: 1rem;">
                        All transactions are encrypted with 256-bit TLS SSL protocols and verified via Razorpay / Stripe secure gateways.
                    </p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="tf-badge">Razorpay Test</span>
                        <span class="tf-badge">Cash on Delivery</span>
                        <span class="tf-badge">UPI / Cards</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Telemetry -->
            <div style="border-top: 1px solid var(--tf-border-subtle); padding-top: 1.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; font-size: 0.8125rem; color: var(--tf-text-dim);">
                <div>
                    &copy; <?= date('Y') ?> TechFusion Systems Inc. All rights reserved. Built with cyber-luxury architecture.
                </div>
                <div style="display: flex; gap: 1.5rem;">
                    <span>System Status: <strong style="color: var(--tf-success);">Nominal ●</strong></span>
                    <span>Database: <strong>MariaDB 10.4</strong></span>
                    <span>PHP: <strong><?= PHP_VERSION ?></strong></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Core Interactive Client Scripts -->
    <script src="assets/js/app.js"></script>
    <script src="assets/js/store.js"></script>
</body>
</html>
