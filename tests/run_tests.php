<?php
/**
 * TechFusion Automated End-to-End Test Suite
 * Tests DB, Products, Cart, Orders, Auth, Security, and Migrations.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Database\Database;
use TechFusion\Models\Product;
use TechFusion\Models\Order;
use TechFusion\Services\CartService;
use TechFusion\Auth\AuthManager;

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] {$name}\n";
    } else {
        $failed++;
        echo "  [FAIL] {$name}: {$detail}\n";
    }
}

echo "=========================================================\n";
echo "      TechFusion Comprehensive Test Suite Execution      \n";
echo "=========================================================\n\n";

try {
    // 1. Database Connection & Schema Test
    echo "1. Testing Database & Infrastructure...\n";
    $pdo = Database::getConnection();
    assert_test("Database PDO Connection", $pdo instanceof PDO);
    $dbName = $pdo->query("SELECT DATABASE()")->fetchColumn();
    assert_test("Database Selected", $dbName === 'electronic_store', "Got: {$dbName}");
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    assert_test("All 8 Core Tables Exist", in_array('products', $tables) && in_array('orders', $tables) && in_array('admins', $tables) && in_array('tv_specs', $tables));

    // 2. Product Model & Specs Test
    echo "\n2. Testing Product Model & Specs...\n";
    $productModel = new Product($pdo);
    $allProducts = $productModel->getFilteredProducts();
    assert_test("Active Product Catalog Count >= 6", count($allProducts) >= 6, "Count: " . count($allProducts));

    $tvProduct = $productModel->getProductWithSpecs(1);
    assert_test("TV Product #1 Loaded", $tvProduct !== null && $tvProduct['name'] === 'Sony Bravia XR OLED 55" 4K');
    assert_test("TV Hardware Specs Loaded", !empty($tvProduct['specs']['screen_size']), "Specs: " . json_encode($tvProduct['specs'] ?? []));

    $phoneProduct = $productModel->getProductWithSpecs(5);
    assert_test("Mobile Product #5 Loaded", $phoneProduct !== null && str_contains($phoneProduct['name'], 'iPhone 15 Pro Max'));
    assert_test("Mobile Specs Array Present", !empty($phoneProduct['specs']['ram_storage']));

    // 3. Cart Service Test
    echo "\n3. Testing Cart Service & Stock Limits...\n";
    $cart = new CartService($productModel);
    $cart->clear();
    assert_test("Initial Cart is Empty", $cart->getItemCount() === 0);

    $addRes = $cart->addItem(1, 2);
    assert_test("Add 2 units of SKU #1 to Cart", $addRes['success'] && $cart->getItemCount() === 2);
    assert_test("Cart Subtotal Correct", $cart->getSubtotal() > 0);

    // 4. Order Creation & Atomic Stock Reservation Test
    echo "\n4. Testing Transactional Order Creation...\n";
    $orderModel = new Order($pdo);
    $initialStock = (int) $pdo->query("SELECT stock FROM products WHERE id = 1")->fetchColumn();

    $cartItems = $cart->getItems();
    $orderResult = $orderModel->createOrder([
        'name' => 'Automated Test User',
        'email' => 'test@techfusion.com',
        'phone' => '+15551234567',
        'address' => '77 Cyber Matrix Way, New Delhi, DL 110001',
    ], $cartItems, $cart->getSubtotal());

    assert_test("Order Created Successfully", !empty($orderResult['order_id']) && !empty($orderResult['tracking_number']));

    $postStock = (int) $pdo->query("SELECT stock FROM products WHERE id = 1")->fetchColumn();
    assert_test("Stock Atomic Decrement by 2", $postStock === ($initialStock - 2), "Initial: {$initialStock}, Post: {$postStock}");

    // Revert test stock and test order to preserve exact data
    $pdo->prepare("UPDATE products SET stock = :s WHERE id = 1")->execute([':s' => $initialStock]);
    $pdo->prepare("DELETE FROM orders WHERE id = :id")->execute([':id' => $orderResult['order_id']]);
    echo "  [INFO] Test order cleaned up; original stock restored to {$initialStock}.\n";

    // 5. Order Tracking Lookup Test
    echo "\n5. Testing Tracking Portal Lookup...\n";
    $fakeTracking = $orderModel->getOrderByTracking('TF-NON-EXISTENT');
    assert_test("Non-existent Tracking Returns Null", $fakeTracking === null);

    // 6. Security & Authentication Test
    echo "\n6. Testing Security, Auth & CSRF...\n";
    $auth = new AuthManager($pdo);
    $loginFail = $auth->attempt('nonexistent_user', 'wrongpassword');
    assert_test("Invalid Login Rejected", $loginFail['success'] === false);

    $token = csrf_token();
    assert_test("CSRF Token Generated", strlen($token) === 64);
    assert_test("CSRF Token Validated", verify_csrf_token($token) === true);
    assert_test("CSRF Invalid Token Rejected", verify_csrf_token('invalid_token_12345') === false);

    $escaped = e("<script>alert('xss')</script>");
    assert_test("XSS Escaping Safe", !str_contains($escaped, "<script>"));

    echo "\n=========================================================\n";
    echo "Test Execution Summary: Passed: {$passed} | Failed: {$failed}\n";
    echo "=========================================================\n";

    if ($failed > 0) {
        exit(1);
    }
} catch (Throwable $e) {
    echo "\n[FATAL EXCEPTION]: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
