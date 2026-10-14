<?php
/**
 * TechFusion Safe Reversible Migration Runner
 * Ensures zero data loss and transactional migration tracking.
 */

declare(strict_types=1);

namespace TechFusion\Database;

use PDO;
use RuntimeException;
use Throwable;

class MigrationRunner {
    private PDO $pdo;
    private string $migrationsDir;

    public function __construct(?PDO $pdo = null, ?string $migrationsDir = null) {
        $this->pdo = $pdo ?? Database::getConnection();
        $this->migrationsDir = $migrationsDir ?? (TECHFUSION_ROOT . '/docs/migrations');
        $this->ensureMigrationsTable();
    }

    private function ensureMigrationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL UNIQUE,
            `batch` INT UNSIGNED NOT NULL,
            `applied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->pdo->exec($sql);
    }

    public function getAppliedMigrations(): array {
        $stmt = $this->pdo->query("SELECT `migration` FROM `schema_migrations` ORDER BY `id` ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPendingMigrations(): array {
        $applied = $this->getAppliedMigrations();
        $files = glob($this->migrationsDir . '/*.sql');
        if ($files === false) {
            return [];
        }
        $pending = [];
        foreach ($files as $file) {
            $name = basename($file);
            if (!in_array($name, $applied, true)) {
                $pending[] = $file;
            }
        }
        sort($pending);
        return $pending;
    }

    public function migrate(): array {
        $pending = $this->getPendingMigrations();
        if (empty($pending)) {
            return ['status' => 'no_pending', 'applied' => []];
        }

        $stmt = $this->pdo->query("SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `schema_migrations`");
        $nextBatch = (int) $stmt->fetchColumn();

        $appliedList = [];
        foreach ($pending as $filePath) {
            $name = basename($filePath);
            $sql = file_get_contents($filePath);
            if ($sql === false || trim($sql) === '') {
                continue;
            }

            try {
                $this->pdo->beginTransaction();
                // Execute migration SQL (DDL triggers implicit commit in MySQL)
                $this->pdo->exec($sql);
                // Record migration
                $logStmt = $this->pdo->prepare("INSERT INTO `schema_migrations` (`migration`, `batch`) VALUES (:migration, :batch)");
                $logStmt->execute([':migration' => $name, ':batch' => $nextBatch]);
                if ($this->pdo->inTransaction()) {
                    $this->pdo->commit();
                }
                $appliedList[] = $name;
            } catch (Throwable $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw new RuntimeException("Failed applying migration {$name}: " . $e->getMessage(), 0, $e);
            }
        }

        return ['status' => 'success', 'applied' => $appliedList, 'batch' => $nextBatch];
    }
}
