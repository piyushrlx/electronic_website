<?php
/**
 * TechFusion Cart Service
 * Manages shopping cart items in session with database validation.
 */

declare(strict_types=1);

namespace TechFusion\Services;

use TechFusion\Models\Product;

class CartService {
    private Product $productModel;

    public function __construct(?Product $productModel = null) {
        $this->productModel = $productModel ?? new Product();
        if (!isset($_SESSION['tf_cart']) || !is_array($_SESSION['tf_cart'])) {
            $_SESSION['tf_cart'] = [];
        }
    }

    public function addItem(int $productId, int $quantity = 1): array {
        if ($quantity <= 0) {
            $quantity = 1;
        }

        $product = $this->productModel->getProductWithSpecs($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        $currentQty = $_SESSION['tf_cart'][$productId] ?? 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product['stock']) {
            $newQty = $product['stock'];
            $_SESSION['tf_cart'][$productId] = $newQty;
            return [
                'success' => true,
                'message' => "Maximum available stock ({$product['stock']}) added to cart.",
                'cartCount' => $this->getItemCount(),
                'subtotal' => $this->getSubtotal(),
            ];
        }

        $_SESSION['tf_cart'][$productId] = $newQty;
        return [
            'success' => true,
            'message' => "{$product['name']} added to cart.",
            'cartCount' => $this->getItemCount(),
            'subtotal' => $this->getSubtotal(),
        ];
    }

    public function updateQuantity(int $productId, int $quantity): array {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }

        $product = $this->productModel->getProductWithSpecs($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        if ($quantity > $product['stock']) {
            $quantity = $product['stock'];
        }

        $_SESSION['tf_cart'][$productId] = $quantity;
        return [
            'success' => true,
            'cartCount' => $this->getItemCount(),
            'subtotal' => $this->getSubtotal(),
        ];
    }

    public function removeItem(int $productId): array {
        unset($_SESSION['tf_cart'][$productId]);
        return [
            'success' => true,
            'message' => 'Item removed from cart.',
            'cartCount' => $this->getItemCount(),
            'subtotal' => $this->getSubtotal(),
        ];
    }

    public function clear(): void {
        $_SESSION['tf_cart'] = [];
    }

    public function getItems(): array {
        $cart = $_SESSION['tf_cart'] ?? [];
        if (empty($cart)) {
            return [];
        }

        $items = [];
        foreach ($cart as $productId => $qty) {
            $product = $this->productModel->getProductWithSpecs((int)$productId);
            if ($product) {
                $subtotal = round((float)$product['price'] * $qty, 2);
                $items[] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'brand' => $product['brand'],
                    'category' => $product['category'],
                    'price' => (float)$product['price'],
                    'stock' => (int)$product['stock'],
                    'image_url' => $product['image_url'],
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }
        return $items;
    }

    public function getItemCount(): int {
        $cart = $_SESSION['tf_cart'] ?? [];
        return array_sum($cart);
    }

    public function getSubtotal(): float {
        $items = $this->getItems();
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['subtotal'];
        }
        return round($total, 2);
    }
}
