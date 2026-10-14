<?php
/**
 * TechFusion Migration CLI Runner
 * Usage: php bin/migrate.php [--target=database_name]
 */

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Database/MigrationRunner.php';

use TechFusion\Database\Database;
use TechFusion\Database\MigrationRunner;

$options = getopt('', ['target:']);
$targetDb = $options['target'] ?? null;

echo "=== TechFusion Database Migration Runner ===\n";
try {
    $pdo = Database::getConnection($targetDb);
    $activeDb = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "Target Database: [{$activeDb}]\n";

    $runner = new MigrationRunner($pdo);
    $applied = $runner->getAppliedMigrations();
    echo "Previously Applied Migrations: " . count($applied) . "\n";
    foreach ($applied as $m) {
        echo "  - {$m}\n";
    }

    $pending = $runner->getPendingMigrations();
    echo "Pending Migrations: " . count($pending) . "\n";
    foreach ($pending as $p) {
        echo "  * " . basename($p) . "\n";
    }

    if (!empty($pending)) {
        echo "Applying pending migrations...\n";
        $result = $runner->migrate();
        echo "Result: {$result['status']}, Batch: {$result['batch']}\n";
        foreach ($result['applied'] as $appliedName) {
            echo "  + Applied: {$appliedName}\n";
        }
    } else {
        echo "Database schema is completely up to date.\n";
    }
    echo "=== Migration Complete ===\n";
} catch (Throwable $e) {
    echo "FATAL ERROR during migration: " . $e->getMessage() . "\n";
    exit(1);
}
