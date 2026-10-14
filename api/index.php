<?php
/**
 * TechFusion Serverless Front Controller for Vercel
 * Dispatches requests to the appropriate public page or API endpoint.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = trim($uri, '/');

// Map clean URLs
if ($uri === '' || $uri === 'index' || $uri === 'index.php') {
    require __DIR__ . '/../public/index.php';
    exit;
}

if ($uri === 'cart' || $uri === 'cart.php') {
    require __DIR__ . '/../public/cart.php';
    exit;
}

if ($uri === 'checkout' || $uri === 'checkout.php') {
    require __DIR__ . '/../public/checkout.php';
    exit;
}

if ($uri === 'product' || $uri === 'product.php') {
    require __DIR__ . '/../public/product.php';
    exit;
}

if ($uri === 'track-order' || $uri === 'track-order.php') {
    require __DIR__ . '/../public/track-order.php';
    exit;
}

if ($uri === 'login' || $uri === 'login.php') {
    require __DIR__ . '/../public/login.php';
    exit;
}

if ($uri === 'admin' || $uri === 'admin/index.php') {
    require __DIR__ . '/../public/admin/index.php';
    exit;
}

// Default fallback to index
require __DIR__ . '/../public/index.php';
