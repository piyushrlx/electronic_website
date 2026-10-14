<?php
/**
 * TechFusion Authentication & RBAC Manager
 * Handles admin/staff auth, session security, Bcrypt re-hashing, and audit logging.
 */

declare(strict_types=1);

namespace TechFusion\Auth;

use PDO;
use TechFusion\Database\Database;

class AuthManager {
    private PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Authenticate admin user
     */
    public function attempt(string $identifier, string $password): array {
        // Simple rate-limit check via session
        $failKey = 'login_fails_' . md5($identifier);
        $attempts = $_SESSION[$failKey] ?? 0;
        if ($attempts >= 5) {
            $lockoutTime = $_SESSION[$failKey . '_time'] ?? 0;
            if (time() - $lockoutTime < 300) {
                return ['success' => false, 'message' => 'Too many failed login attempts. Please wait 5 minutes.'];
            } else {
                $_SESSION[$failKey] = 0;
            }
        }

        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE (username = :ident OR email = :ident) AND is_active = 1 LIMIT 1");
        $stmt->execute([':ident' => trim($identifier)]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            $_SESSION[$failKey] = ($attempts + 1);
            $_SESSION[$failKey . '_time'] = time();
            return ['success' => false, 'message' => 'Invalid credentials or inactive account.'];
        }

        // Check if password needs rehash with updated cost
        if (password_needs_rehash($admin['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $rehashStmt = $this->pdo->prepare("UPDATE admins SET password_hash = :hash WHERE id = :id");
            $rehashStmt->execute([':hash' => $newHash, ':id' => $admin['id']]);
        }

        // Regenerate session to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['tf_admin_logged'] = true;
        $_SESSION['tf_admin_id'] = (int) $admin['id'];
        $_SESSION['tf_admin_username'] = $admin['username'];
        $_SESSION['tf_admin_email'] = $admin['email'];
        $_SESSION['tf_admin_role'] = $admin['role'];

        // Reset rate-limit tracker
        unset($_SESSION[$failKey], $_SESSION[$failKey . '_time']);

        // Update last login
        $upd = $this->pdo->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = :id");
        $upd->execute([':id' => $admin['id']]);

        // Audit log
        $this->logAudit((int)$admin['id'], 'LOGIN', 'admins', (int)$admin['id'], 'Admin logged in successfully.');

        return ['success' => true, 'role' => $admin['role']];
    }

    public function logout(): void {
        $adminId = $_SESSION['tf_admin_id'] ?? null;
        if ($adminId) {
            $this->logAudit((int)$adminId, 'LOGOUT', 'admins', (int)$adminId, 'Admin logged out.');
        }

        unset(
            $_SESSION['tf_admin_logged'],
            $_SESSION['tf_admin_id'],
            $_SESSION['tf_admin_username'],
            $_SESSION['tf_admin_email'],
            $_SESSION['tf_admin_role']
        );
        session_regenerate_id(true);
    }

    public function check(): bool {
        return !empty($_SESSION['tf_admin_logged']) && $_SESSION['tf_admin_logged'] === true;
    }

    public function isSuperAdmin(): bool {
        return $this->check() && ($_SESSION['tf_admin_role'] ?? '') === 'super_admin';
    }

    public function user(): ?array {
        if (!$this->check()) {
            return null;
        }
        return [
            'id' => $_SESSION['tf_admin_id'],
            'username' => $_SESSION['tf_admin_username'],
            'email' => $_SESSION['tf_admin_email'],
            'role' => $_SESSION['tf_admin_role'],
        ];
    }

    public function logAudit(?int $adminId, string $actionType, string $entity, ?int $targetId, ?string $details): void {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO audit_logs (admin_id, action_type, target_entity, target_id, details, ip_address, user_agent)
                VALUES (:admin_id, :action, :entity, :target_id, :details, :ip, :ua)
            ");
            $stmt->execute([
                ':admin_id' => $adminId,
                ':action' => $actionType,
                ':entity' => $entity,
                ':target_id' => $targetId,
                ':details' => $details,
                ':ip' => substr($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 0, 45),
                ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255),
            ]);
        } catch (\Throwable $e) {
            error_log('[TechFusion Audit Log Failure]: ' . $e->getMessage());
        }
    }
}
