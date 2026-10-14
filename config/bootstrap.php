<?php
/**
 * TechFusion Core Bootstrap File
 * Handles environment loading, error reporting, session security, and core helpers.
 */

// 1. Prevent multiple initialization
if (defined('TECHFUSION_BOOTSTRAPPED')) {
    return;
}
define('TECHFUSION_BOOTSTRAPPED', true);

// 2. Define directory constants
define('TECHFUSION_ROOT', dirname(__DIR__));
define('TECHFUSION_CONFIG', TECHFUSION_ROOT . '/config');
define('TECHFUSION_SRC', TECHFUSION_ROOT . '/src');
define('TECHFUSION_PUBLIC', TECHFUSION_ROOT . '/public');

// 3. Robust Native Environment Loader
function load_env_file(string $path): void {
    if (!file_exists($path) || !is_readable($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Strip enclosing quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Load .env from root
load_env_file(TECHFUSION_ROOT . '/.env');

// 4. Environment Access Helper
function env(string $key, mixed $default = null): mixed {
    $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($val === false || $val === null) {
        return $default;
    }
    return match (strtolower((string)$val)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'empty', '(empty)' => '',
        'null', '(null)' => null,
        default => $val,
    };
}

// 5. Error Reporting & Debug Configuration
$isDebug = (bool) env('APP_DEBUG', true);
if ($isDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
}

// 6. Security & Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    $secureCookie = (bool) env('SESSION_SECURE_COOKIE', false) || 
                   (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                   (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

    session_set_cookie_params([
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
        'path' => '/',
        'domain' => '',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// 7. Security Helpers: HTML Escaping & CSRF Token Protection
function e(?string $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    if (empty($_SESSION['_csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['_csrf_token'], $token);
}

// 8. PSR-4 Autoloader for TechFusion Namespace
spl_autoload_register(function (string $class): void {
    $prefix = 'TechFusion\\';
    $baseDir = TECHFUSION_SRC . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
