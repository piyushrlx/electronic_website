<?php
/**
 * TechFusion Product Catalog API Endpoint
 * Provides JSON product queries with filtering and sorting.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Models\Product;

$productModel = new Product();

$category = $_GET['category'] ?? null;
$search = $_GET['search'] ?? null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;
$sort = $_GET['sort'] ?? 'featured';

$products = $productModel->getFilteredProducts($category, $search, $maxPrice, $sort);

echo json_encode([
    'success' => true,
    'count' => count($products),
    'data' => $products,
]);
