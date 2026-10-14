<?php
/**
 * TechFusion Product Model
 * Interacts with `products`, `tv_specs`, `fridge_specs`, `mobile_specs`.
 */

declare(strict_types=1);

namespace TechFusion\Models;

use PDO;
use TechFusion\Database\Database;

class Product {
    private PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Get all active products with optional filters
     */
    public function getFilteredProducts(?string $category = null, ?string $search = null, ?float $maxPrice = null, string $sort = 'featured'): array {
        $sql = "SELECT p.* FROM products p WHERE 1=1";
        $params = [];

        if (!empty($category) && $category !== 'All') {
            $sql .= " AND p.category = :category";
            $params[':category'] = $category;
        }

        if (!empty($search)) {
            $sql .= " AND (p.name LIKE :search OR p.brand LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $sql .= " AND p.price <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        // Sorting
        $sql .= match ($sort) {
            'price_asc' => " ORDER BY p.price ASC",
            'price_desc' => " ORDER BY p.price DESC",
            'name_asc' => " ORDER BY p.name ASC",
            default => " ORDER BY p.id ASC",
        };

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single product with its category-specific technical specifications
     */
    public function getProductWithSpecs(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        $product['specs'] = [];
        $category = $product['category'];

        if ($category === 'TV') {
            $specStmt = $this->pdo->prepare("SELECT * FROM tv_specs WHERE product_id = :id");
            $specStmt->execute([':id' => $id]);
            $product['specs'] = $specStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } elseif ($category === 'Refrigerator') {
            $specStmt = $this->pdo->prepare("SELECT * FROM fridge_specs WHERE product_id = :id");
            $specStmt->execute([':id' => $id]);
            $product['specs'] = $specStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } elseif ($category === 'Mobile') {
            $specStmt = $this->pdo->prepare("SELECT * FROM mobile_specs WHERE product_id = :id");
            $specStmt->execute([':id' => $id]);
            $product['specs'] = $specStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        return $product;
    }

    /**
     * Get distinct product categories
     */
    public function getCategories(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get distinct brands
     */
    public function getBrands(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT brand FROM products ORDER BY brand ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
