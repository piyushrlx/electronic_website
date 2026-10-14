<?php
/**
 * TechFusion Portal Authentication
 * Secure login with CSRF defense, rate-limiting, and Bcrypt validation.
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Auth\AuthManager;

$auth = new AuthManager();

// If already logged in, redirect to admin
if ($auth->check()) {
    header("Location: admin/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF
    $token = $_POST['_csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = "Session expired. Please reload and try again.";
    } else {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if (empty($identifier) || empty($password)) {
            $error = "Username/Email and Password are required.";
        } else {
            $result = $auth->attempt($identifier, $password);
            if ($result['success']) {
                header("Location: admin/index.php");
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

$pageTitle = 'Security Portal Login - TechFusion';
require_once __DIR__ . '/../templates/header.php';
?>

<div class="tf-container" style="padding-top: 5rem; padding-bottom: 6rem; max-width: 480px;">
    
    <div class="tf-card animate-fade-in-up" style="padding: 2.5rem;">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="tf-logo-icon" style="margin: 0 auto 1rem auto; width: 48px; height: 48px; font-size: 1.5rem;">⚡</div>
            <h2>TechFusion Portal</h2>
            <p style="font-size: 0.875rem; color: var(--tf-text-muted); margin-top: 0.25rem;">
                Authorized Staff & System Administration
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="tf-badge tf-badge-error" style="width: 100%; padding: 0.75rem 1rem; justify-content: center; margin-bottom: 1.5rem; font-size: 0.875rem;">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">

            <div class="tf-form-group">
                <label for="identInput" class="tf-label">Username or Staff Email</label>
                <input type="text" id="identInput" name="identifier" class="tf-input" required placeholder="manager or dev_lead" autofocus value="<?= e($_POST['identifier'] ?? '') ?>">
            </div>

            <div class="tf-form-group">
                <label for="passInput" class="tf-label">Master Password</label>
                <input type="password" id="passInput" name="password" class="tf-input" required placeholder="••••••••••••">
            </div>

            <button type="submit" class="btn-cyber" style="width: 100%; height: 46px; margin-top: 1rem;">
                Authenticate Access ⚡
            </button>
        </form>

        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--tf-border-subtle); text-align: center; font-size: 0.8125rem; color: var(--tf-text-muted);">
            <div>Default Dev Accounts in Database:</div>
            <code style="color: var(--tf-primary); font-size: 0.75rem;">manager (admin) / dev_lead (super_admin)</code>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
