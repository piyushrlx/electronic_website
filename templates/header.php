<?php
/**
 * TechFusion Global Header Partial
 */
declare(strict_types=1);

if (!defined('TECHFUSION_BOOTSTRAPPED')) {
    require_once __DIR__ . '/../config/bootstrap.php';
}

use TechFusion\Services\CartService;
use TechFusion\Auth\AuthManager;

$cartService = new CartService();
$authManager = new AuthManager();

$cartCount = $cartService->getItemCount();
$pageTitle = $pageTitle ?? 'TechFusion - Next-Gen Electronics Marketplace';
$pageDesc = $pageDesc ?? 'Explore high-performance OLED TVs, smart refrigeration, and flagship smartphones at TechFusion.';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDesc) ?>">
    
    <!-- Open Graph Metadata -->
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDesc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="TechFusion">
    
    <!-- Design System Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@300;400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Core CSS Architecture -->
    <link rel="stylesheet" href="assets/css/techfusion-core.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>

    <!-- Sticky Glassmorphic Navbar -->
    <header class="tf-navbar">
        <div class="tf-container">
            <div class="tf-nav-inner">
                <!-- Brand Logo -->
                <a href="index.php" class="tf-logo">
                    <div class="tf-logo-icon">⚡</div>
                    <div class="tf-logo-text">Tech<span>Fusion</span></div>
                </a>

                <!-- Navigation Links -->
                <nav>
                    <ul class="tf-nav-links">
                        <li><a href="index.php" class="tf-nav-link <?= (basename($_SERVER['PHP_SELF']) === 'index.php' && empty($_GET['category'])) ? 'active' : '' ?>">Home</a></li>
                        <li><a href="index.php?category=TV" class="tf-nav-link <?= (($_GET['category'] ?? '') === 'TV') ? 'active' : '' ?>">TVs</a></li>
                        <li><a href="index.php?category=Refrigerator" class="tf-nav-link <?= (($_GET['category'] ?? '') === 'Refrigerator') ? 'active' : '' ?>">Refrigerators</a></li>
                        <li><a href="index.php?category=Mobile" class="tf-nav-link <?= (($_GET['category'] ?? '') === 'Mobile') ? 'active' : '' ?>">Mobiles</a></li>
                        <li><a href="track-order.php" class="tf-nav-link <?= (basename($_SERVER['PHP_SELF']) === 'track-order.php') ? 'active' : '' ?>">Track Order</a></li>
                    </ul>
                </nav>

                <!-- Actions: Theme Toggle, Admin / Auth, Cart Drawer Trigger -->
                <div class="tf-nav-actions">
                    <!-- Theme Toggle -->
                    <button type="button" class="btn-ghost-glass btn-icon" id="themeToggle" title="Toggle Dark/Light Mode" aria-label="Toggle Theme">
                        <span id="themeIcon">☀️</span>
                    </button>

                    <!-- Admin Portal or Login -->
                    <?php if ($authManager->check()): ?>
                        <a href="admin/index.php" class="btn-ghost-glass btn-sm" title="Admin Dashboard">
                            🛡️ Staff Panel
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn-ghost-glass btn-sm">
                            Portal Login
                        </a>
                    <?php endif; ?>

                    <!-- Cart Drawer Trigger -->
                    <button type="button" class="btn-cyber btn-sm" id="cartDrawerBtn" aria-label="Open Shopping Cart">
                        <span>Cart</span>
                        <span class="tf-cart-counter" id="navCartCount"><?= $cartCount ?></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Global Notification Banner / Toast Container -->
    <div id="tfToastContainer" style="position: fixed; top: 84px; right: 24px; z-index: 3000; display: flex; flex-direction: column; gap: 8px;"></div>
