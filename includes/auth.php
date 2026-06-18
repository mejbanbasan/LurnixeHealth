<?php
/**
 * Lurnixe Health Card System - Authentication Middleware
 * June 2026
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Check if the admin is logged in and handles session timeouts
 */
function check_auth() {
    // Check if the session contains the admin identifier
    if (!isset($_SESSION['admin_id'])) {
        // Redirect to login page
        header("Location: " . BASE_URL . "admin/login.php");
        exit;
    }

    // Session inactivity timeout check (2 hours)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT_SECONDS)) {
        // Clear and destroy the session
        session_unset();
        session_destroy();
        
        // Redirect to login page with timeout message
        header("Location: " . BASE_URL . "admin/login.php?error=timeout");
        exit;
    }
    
    // Update the last activity timestamp
    $_SESSION['last_activity'] = time();
}

/**
 * Enforce Super Admin only authorization
 */
function require_super_admin() {
    // First verify they are authenticated
    check_auth();
    
    // Check if the role is Super Admin
    if ($_SESSION['admin_role'] !== 'super_admin') {
        // Redirect to dashboard with unauthorized flag
        header("Location: " . BASE_URL . "admin/dashboard.php?error=unauthorized");
        exit;
    }
}

/**
 * Utility to check if a user is currently logged in (for login page redirects)
 */
function is_logged_in() {
    return isset($_SESSION['admin_id']);
}
