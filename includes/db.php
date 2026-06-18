<?php
/**
 * Lurnixe Health Card System - Database Connection
 * June 2026
 */

require_once __DIR__ . '/../config/config.php';

try {
    // Construct DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    // Set PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
    ];
    
    // Instantiate PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // In production, do not print full error message for security.
    error_log("Database Connection Failure: " . $e->getMessage());
    die("A connection error occurred. Please try again later or contact the administrator.");
}
