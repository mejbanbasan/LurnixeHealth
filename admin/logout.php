<?php
/**
 * Lurnixe Health Card System - Sign Out Handling
 * June 2026
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log activity before logging out
if (isset($_SESSION['admin_id'])) {
    log_activity($pdo, $_SESSION['admin_id'], 'logout', null, 'Admin signed out');
}

// Clear Remember Me Cookie by setting expiry in the past
if (isset($_COOKIE['remember_admin'])) {
    setcookie('remember_admin', '', time() - 3600, '/');
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to login page
header("Location: " . BASE_URL . "admin/login.php");
exit;
