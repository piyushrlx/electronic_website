<?php
/**
 * TechFusion Cart API Endpoint
 * Handles dynamic AJAX requests for cart modifications.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Services\CartService;

$cart = new CartService();
$method = $_SERVER['REQUEST_METHOD'];

// Handle POST actions
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? 'add';
    $productId = (int) ($input['product_id'] ?? 0);
    $quantity = (int) ($input['quantity'] ?? 1);

    if ($action === 'add') {
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
            exit;
        }
        $result = $cart->addItem($productId, $quantity);
        $result['items'] = $cart->getItems();
        echo json_encode($result);
        exit;
    }

    if ($action === 'update') {
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
            exit;
        }
        $result = $cart->updateQuantity($productId, $quantity);
        $result['items'] = $cart->getItems();
        echo json_encode($result);
        exit;
    }

    if ($action === 'remove') {
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
            exit;
        }
        $result = $cart->removeItem($productId);
        $result['items'] = $cart->getItems();
        echo json_encode($result);
        exit;
    }

    if ($action === 'clear') {
        $cart->clear();
        echo json_encode([
            'success' => true,
            'cartCount' => 0,
            'subtotal' => 0.0,
            'items' => [],
        ]);
        exit;
    }
}

// GET action: return cart details
echo json_encode([
    'success' => true,
    'cartCount' => $cart->getItemCount(),
    'subtotal' => $cart->getSubtotal(),
    'items' => $cart->getItems(),
]);
