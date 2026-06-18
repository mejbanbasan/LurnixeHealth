<?php
/**
 * Lurnixe Health Card System - Configuration File
 * June 2026
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Configure session cookie settings for security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    // If running on HTTPS, force secure cookies
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

// Database Credentials
if (php_sapi_name() === 'cli') {
    if (strpos(__DIR__, 'xampp') !== false) {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_NAME', 'lurnixe_health');
    } else {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'u894958506_admin');
        define('DB_PASS', 'LURNIX@313Office');
        define('DB_NAME', 'u894958506_lurnixehealth');
    }
} else {
    if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === 0)) {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_NAME', 'lurnixe_health');
    } else {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'u894958506_admin');
        define('DB_PASS', 'LURNIX@313Office');
        define('DB_NAME', 'u894958506_lurnixehealth');
    }
}

// Application Settings
define('SITE_NAME', 'LurnixeHealth');
define('CARD_PREFIX', 'LFC');
define('DEFAULT_VALIDITY_YEARS', 1); // Default validity if not specified

// Dynamic Base URL Detection
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? '') == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $physical_root = str_replace('\\', '/', dirname(__DIR__));
    $physical_script = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
    $url_script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    $relative_path = '';
    if (!empty($physical_script) && strpos($physical_script, $physical_root) === 0) {
        $relative_path = substr($physical_script, strlen($physical_root));
    }
    
    $url_root = $url_script;
    if (!empty($relative_path) && substr($url_script, -strlen($relative_path)) === $relative_path) {
        $url_root = substr($url_script, 0, -strlen($relative_path));
    }
    
    $url_root = rtrim($url_root, '/') . '/';
    define('BASE_URL', $protocol . $domainName . $url_root);
}

// Security Keys
define('SESSION_TIMEOUT_SECONDS', 7200); // 2 hours inactivity timeout
define('MAX_LOGIN_ATTEMPTS', 5);
define('COOLDOWN_MINUTES', 15);

// Error Reporting (Turn off display in production and AJAX to prevent JSON corruption)
if (
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
    (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/ajax/') !== false)
) {
    ini_set('display_errors', 0);
} else {
    ini_set('display_errors', 1);
}
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Create required upload folders if they don't exist
$upload_dirs = [
    __DIR__ . '/../uploads/',
    __DIR__ . '/../uploads/members/',
    __DIR__ . '/../uploads/qrcodes/',
    __DIR__ . '/../uploads/cards/'
];

foreach ($upload_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}
