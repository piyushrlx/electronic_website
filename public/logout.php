<?php
/**
 * TechFusion Logout Action
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/database.php';

use TechFusion\Auth\AuthManager;

$auth = new AuthManager();
$auth->logout();

header("Location: login.php");
exit;
