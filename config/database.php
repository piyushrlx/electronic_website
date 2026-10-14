<?php
/**
 * TechFusion Database Connection Manager
 * Manages PDO instance with strict error handling, connection pooling, and SSL options.
 */

declare(strict_types=1);

namespace TechFusion\Database;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(?string $databaseOverride = null): PDO {
        if (self::$instance !== null && $databaseOverride === null) {
            return self::$instance;
        }

        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $dbName = $databaseOverride ?? env('DB_DATABASE', 'electronic_store');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci",
        ];

        // SSL support for hosted/cloud databases (PlanetScale, Aiven, Supabase)
        $sslMode = env('DB_SSL_MODE', 'disabled');
        if ($sslMode === 'required') {
            $options[PDO::MYSQL_ATTR_SSL_CA] = env('DB_SSL_CA_PATH', '');
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }

        try {
            $pdo = new PDO($dsn, $username, $password, $options);
            if ($databaseOverride === null) {
                self::$instance = $pdo;
            }
            return $pdo;
        } catch (PDOException $e) {
            // Never leak credentials or raw traces to the client
            error_log('[TechFusion Database Connection Error]: ' . $e->getMessage());
            throw new RuntimeException("Database connection failure. Check environment configuration.");
        }
    }

    public static function resetConnection(): void {
        self::$instance = null;
    }
}
