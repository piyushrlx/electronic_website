<?php
/**
 * TechFusion Order Model
 * Handles atomic order placement, stock deduction, line items, and tracking.
 */

declare(strict_types=1);

namespace TechFusion\Models;

use PDO;
use RuntimeException;
use Throwable;
use TechFusion\Database\Database;

class Order {
    private PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Create an order transactionally with stock deduction
     */
    public function createOrder(array $customerData, array $cartItems, float $totalAmount): array {
        if (empty($cartItems)) {
            throw new RuntimeException("Cannot place an order with an empty cart.");
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Generate unique tracking number (e.g., TF-2026-XXXX)
            $trackingNumber = 'TF-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));

            // 2. Insert into `orders`
            $sql = "INSERT INTO orders (customer_name, customer_email, customer_phone, shipping_address, total_amount, order_status, tracking_number)
                    VALUES (:name, :email, :phone, :address, :total, 'Processing', :tracking)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name' => $customerData['name'],
                ':email' => $customerData['email'],
                ':phone' => $customerData['phone'],
                ':address' => $customerData['address'],
                ':total' => $totalAmount,
                ':tracking' => $trackingNumber,
            ]);

            $orderId = (int) $this->pdo->lastInsertId();

            // 3. Insert line items & deduct stock
            $itemStmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)");
            $stockCheckStmt = $this->pdo->prepare("SELECT stock, name FROM products WHERE id = :id FOR UPDATE");
            $stockUpdateStmt = $this->pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :qty");

            foreach ($cartItems as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['price'];

                // Verify stock lock
                $stockCheckStmt->execute([':id' => $productId]);
                $prod = $stockCheckStmt->fetch(PDO::FETCH_ASSOC);

                if (!$prod || (int)$prod['stock'] < $quantity) {
                    throw new RuntimeException("Insufficient stock for product: " . ($prod['name'] ?? "ID #{$productId}"));
                }

                // Deduct stock
                $stockUpdateStmt->execute([':qty' => $quantity, ':id' => $productId]);

                // Record item
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $productId,
                    ':quantity' => $quantity,
                    ':unit_price' => $unitPrice,
                ]);
            }

            $this->pdo->commit();

            return [
                'order_id' => $orderId,
                'tracking_number' => $trackingNumber,
                'total_amount' => $totalAmount,
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Get order details by ID and optional customer email (for tracking portal)
     */
    public function getOrderWithItems(int $orderId, ?string $customerEmail = null): ?array {
        $sql = "SELECT * FROM orders WHERE id = :id";
        $params = [':id' => $orderId];

        if (!empty($customerEmail)) {
            $sql .= " AND customer_email = :email";
            $params[':email'] = $customerEmail;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $itemStmt = $this->pdo->prepare("
            SELECT oi.*, p.name as product_name, p.image_url, p.category, p.brand
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $itemStmt->execute([':order_id' => $orderId]);
        $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    /**
     * Get order by tracking number
     */
    public function getOrderByTracking(string $trackingNumber): ?array {
        $stmt = $this->pdo->prepare("SELECT id FROM orders WHERE tracking_number = :tracking LIMIT 1");
        $stmt->execute([':tracking' => trim($trackingNumber)]);
        $id = $stmt->fetchColumn();

        return $id ? $this->getOrderWithItems((int)$id) : null;
    }

    /**
     * Get all orders with limit for admin
     */
    public function getAllOrders(int $limit = 50, ?string $status = null): array {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];

        if (!empty($status)) {
            $sql .= " AND order_status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY id DESC LIMIT " . (int)$limit;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $newStatus): bool {
        $stmt = $this->pdo->prepare("UPDATE orders SET order_status = :status WHERE id = :id");
        return $stmt->execute([':status' => $newStatus, ':id' => $orderId]);
    }
}
