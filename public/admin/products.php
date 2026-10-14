<?php
/**
 * TechFusion Admin Products & Inventory Manager
 */
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

use TechFusion\Auth\AuthManager;
use TechFusion\Database\Database;
use TechFusion\Models\Product;

$auth = new AuthManager();
if (!$auth->check()) {
    header("Location: ../login.php");
    exit;
}

$pdo = Database::getConnection();
$productModel = new Product();
$message = '';
$error = '';

// Handle Stock Update or Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['_csrf_token'] ?? '')) {
        $error = "Session expired. Please try again.";
    } else {
        $action = $_POST['action'] ?? '';

        // 1. Stock Quick Update
        if ($action === 'update_stock') {
            $pId = (int) ($_POST['product_id'] ?? 0);
            $newStock = (int) ($_POST['stock'] ?? 0);
            if ($pId > 0 && $newStock >= 0) {
                $upd = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
                $upd->execute([':stock' => $newStock, ':id' => $pId]);
                $auth->logAudit($auth->user()['id'], 'UPDATE_STOCK', 'products', $pId, "Stock set to {$newStock}");
                $message = "Stock updated successfully for SKU #{$pId}.";
            }
        }

        // 2. Add New Product SKU
        if ($action === 'create_product') {
            $name = trim($_POST['name'] ?? '');
            $brand = trim($_POST['brand'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $imgUrl = trim($_POST['image_url'] ?? '');
            $desc = trim($_POST['description'] ?? '');

            if (empty($name) || empty($brand) || empty($category) || $price <= 0) {
                $error = "Name, Brand, Category, and valid Price are required.";
            } else {
                try {
                    $pdo->beginTransaction();
                    $stmt = $pdo->prepare("INSERT INTO products (name, brand, category, price, stock, image_url, description) VALUES (:name, :brand, :category, :price, :stock, :img, :desc)");
                    $stmt->execute([
                        ':name' => $name,
                        ':brand' => $brand,
                        ':category' => $category,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':img' => $imgUrl ?: 'https://images.unsplash.com/photo-1593784991095-a205069470b6?auto=format&fit=crop&w=600&q=80',
                        ':desc' => $desc,
                    ]);
                    $newId = (int)$pdo->lastInsertId();

                    // Optional specs insertion based on category
                    if ($category === 'TV') {
                        $s = $pdo->prepare("INSERT INTO tv_specs (product_id, screen_size, resolution, display_tech, smart_os) VALUES (:id, :ss, :res, :tech, :os)");
                        $s->execute([
                            ':id' => $newId,
                            ':ss' => trim($_POST['tv_screen_size'] ?? '55 Inch'),
                            ':res' => trim($_POST['tv_resolution'] ?? '4K UHD'),
                            ':tech' => trim($_POST['tv_display_tech'] ?? 'OLED'),
                            ':os' => trim($_POST['tv_smart_os'] ?? 'Google TV'),
                        ]);
                    } elseif ($category === 'Refrigerator') {
                        $s = $pdo->prepare("INSERT INTO fridge_specs (product_id, capacity_liters, door_type, defrost_type, energy_rating) VALUES (:id, :cap, :dt, :def, :er)");
                        $s->execute([
                            ':id' => $newId,
                            ':cap' => (int)($_POST['fridge_capacity'] ?? 350),
                            ':dt' => trim($_POST['fridge_door_type'] ?? 'Double Door'),
                            ':def' => trim($_POST['fridge_defrost_type'] ?? 'Frost Free'),
                            ':er' => (int)($_POST['fridge_energy_rating'] ?? 4),
                        ]);
                    } elseif ($category === 'Mobile') {
                        $s = $pdo->prepare("INSERT INTO mobile_specs (product_id, ram_storage, battery_mah, camera_mp, network_type) VALUES (:id, :rs, :bat, :cam, :net)");
                        $s->execute([
                            ':id' => $newId,
                            ':rs' => trim($_POST['mobile_ram_storage'] ?? '8GB / 256GB'),
                            ':bat' => (int)($_POST['mobile_battery_mah'] ?? 5000),
                            ':cam' => trim($_POST['mobile_camera_mp'] ?? '50MP + 12MP'),
                            ':net' => trim($_POST['mobile_network_type'] ?? '5G'),
                        ]);
                    }

                    $pdo->commit();
                    $auth->logAudit($auth->user()['id'], 'CREATE_PRODUCT', 'products', $newId, "Created product {$name}");
                    $message = "New SKU #{$newId} ({$name}) added to catalog successfully.";
                } catch (\Throwable $e) {
                    if ($pdo->inTransaction()) $pdo->rollBack();
                    $error = "Failed to add product: " . $e->getMessage();
                }
            }
        }
    }
}

$allProducts = $productModel->getFilteredProducts();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - TechFusion Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/techfusion-core.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/animations.css">
</head>
<body>

    <header class="tf-navbar">
        <div class="tf-container">
            <div class="tf-nav-inner">
                <a href="index.php" class="tf-logo">
                    <div class="tf-logo-icon">🛡️</div>
                    <div class="tf-logo-text">Tech<span>Fusion</span> Control</div>
                </a>
                <nav>
                    <ul class="tf-nav-links">
                        <li><a href="index.php" class="tf-nav-link">Overview</a></li>
                        <li><a href="products.php" class="tf-nav-link active">Inventory</a></li>
                        <li><a href="orders.php" class="tf-nav-link">Orders Fulfillment</a></li>
                        <li><a href="audit.php" class="tf-nav-link">Audit Trail</a></li>
                    </ul>
                </nav>
                <div class="tf-nav-actions">
                    <a href="../logout.php" class="btn-ghost-glass btn-sm" style="color: var(--tf-error);">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="tf-container" style="padding-top: 3rem; padding-bottom: 5rem;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="tf-badge tf-badge-cyan" style="margin-bottom: 0.5rem;">Hardware Catalog</span>
                <h1>Warehouse Stock & Inventory (<?= count($allProducts) ?>)</h1>
            </div>
            <a href="#newProductCard" class="btn-cyber btn-sm">+ Add Product SKU</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="tf-badge tf-badge-success" style="padding: 0.75rem 1.25rem; width: 100%; margin-bottom: 1.5rem; font-size: 0.875rem;">
                ✓ <?= e($message) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="tf-badge tf-badge-error" style="padding: 0.75rem 1.25rem; width: 100%; margin-bottom: 1.5rem; font-size: 0.875rem;">
                ⚠️ <?= e($error) ?>
            </div>
        <?php endif; ?>

        <!-- Products Table -->
        <div class="tf-card" style="padding: 0; overflow-x: auto; margin-bottom: 3.5rem;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--tf-border-glass); text-align: left; font-family: var(--tf-font-headline); color: var(--tf-text-muted); font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 1rem 1.25rem;">SKU</th>
                        <th style="padding: 1rem 1.25rem;">Hardware Model</th>
                        <th style="padding: 1rem 1.25rem;">Category</th>
                        <th style="padding: 1rem 1.25rem;">Price</th>
                        <th style="padding: 1rem 1.25rem;">Stock Count & Adjust</th>
                        <th style="padding: 1rem 1.25rem;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allProducts as $p): ?>
                        <tr style="border-bottom: 1px solid var(--tf-border-subtle);">
                            <td style="padding: 1rem 1.25rem; font-family: var(--tf-font-headline); font-weight: 700; color: var(--tf-text-dim);">
                                #<?= $p['id'] ?>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="<?= e($p['image_url']) ?>" alt="" style="width: 44px; height: 44px; object-fit: cover; border-radius: var(--tf-radius-sm);">
                                    <div>
                                        <strong style="color: var(--tf-text-primary);"><?= e($p['name']) ?></strong>
                                        <div style="font-size: 0.75rem; color: var(--tf-text-muted);"><?= e($p['brand']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <span class="tf-badge tf-badge-cyan"><?= e($p['category']) ?></span>
                            </td>
                            <td style="padding: 1rem 1.25rem; font-family: var(--tf-font-headline); font-weight: 700; color: var(--tf-primary);">
                                $<?= number_format((float)$p['price'], 2) ?>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <form method="POST" action="products.php" style="display: flex; gap: 0.5rem; align-items: center;">
                                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="update_stock">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <input type="number" name="stock" value="<?= $p['stock'] ?>" min="0" class="tf-input" style="width: 80px; padding: 0.35rem 0.5rem; text-align: center;">
                                    <button type="submit" class="btn-ghost-glass btn-sm">Set</button>
                                </form>
                            </td>
                            <td style="padding: 1rem 1.25rem;">
                                <a href="../product.php?id=<?= $p['id'] ?>" target="_blank" class="btn-ghost-glass btn-sm">View ↗</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add New Product Form Card -->
        <div id="newProductCard" class="tf-card" style="padding: 2.5rem; max-width: 800px; margin: 0 auto;">
            <h3 style="font-size: 1.35rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>+ Enlist New Hardware SKU</span>
                <span class="tf-badge tf-badge-cyan">Database Addition</span>
            </h3>

            <form method="POST" action="products.php">
                <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="create_product">

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                    <div class="tf-form-group">
                        <label class="tf-label">Product Name / Title *</label>
                        <input type="text" name="name" class="tf-input" required placeholder="e.g. Sony Bravia 75 Inch Mini-LED 4K">
                    </div>
                    <div class="tf-form-group">
                        <label class="tf-label">Brand *</label>
                        <input type="text" name="brand" class="tf-input" required placeholder="e.g. Sony, Apple, Samsung">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="tf-form-group">
                        <label class="tf-label">Category *</label>
                        <select name="category" id="catSelector" class="tf-select" required>
                            <option value="TV">TV</option>
                            <option value="Refrigerator">Refrigerator</option>
                            <option value="Mobile">Mobile</option>
                        </select>
                    </div>
                    <div class="tf-form-group">
                        <label class="tf-label">Unit Price ($) *</label>
                        <input type="number" step="0.01" name="price" class="tf-input" required placeholder="1299.99">
                    </div>
                    <div class="tf-form-group">
                        <label class="tf-label">Initial Stock Units *</label>
                        <input type="number" name="stock" value="10" min="0" class="tf-input" required>
                    </div>
                </div>

                <div class="tf-form-group">
                    <label class="tf-label">Image URL</label>
                    <input type="url" name="image_url" class="tf-input" placeholder="https://images.unsplash.com/photo-...">
                </div>

                <div class="tf-form-group">
                    <label class="tf-label">Technical Description</label>
                    <textarea name="description" rows="3" class="tf-textarea" placeholder="Detailed engineering and hardware summary..."></textarea>
                </div>

                <button type="submit" class="btn-cyber" style="width: 100%; height: 48px; margin-top: 1rem;">
                    Commit New Product to Database ⚡
                </button>
            </form>
        </div>

    </main>

</body>
</html>
