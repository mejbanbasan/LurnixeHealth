<?php
/**
 * Lurnixe Health Card System - Helper Functions
 * June 2026
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Class alias for FPDF since the modern Composer package uses namespace Fpdf\Fpdf
if (!class_exists('FPDF') && class_exists('Fpdf\Fpdf')) {
    class_alias('Fpdf\Fpdf', 'FPDF');
}

require_once __DIR__ . '/db.php';

/**
 * Sanitize user input for XSS prevention
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Calculate age based on Date of Birth
 */
function calculate_age($dob) {
    try {
        $birthDate = new DateTime($dob);
        $today = new DateTime('today');
        return $birthDate->diff($today)->y;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Generate Next Unique Member ID
 */
function generate_member_id($pdo) {
    try {
        $stmt = $pdo->query("SELECT MAX(id) as max_id FROM members");
        $row = $stmt->fetch();
        $next_id = ($row['max_id'] ?? 0) + 1;
        return CARD_PREFIX . str_pad($next_id, 6, '0', STR_PAD_LEFT);
    } catch (PDOException $e) {
        error_log("Error generating member ID: " . $e->getMessage());
        return CARD_PREFIX . '000001'; // Fallback
    }
}

/**
 * Generate and save QR Code locally
 */
function generate_qr_code($member_id) {
    $qr_data = BASE_URL . "member.php?id=" . $member_id;
    $file_name = $member_id . '.svg';
    $file_path = __DIR__ . '/../uploads/qrcodes/' . $file_name;
    
    // Ensure qrcodes directory exists
    if (!file_exists(dirname($file_path))) {
        mkdir(dirname($file_path), 0755, true);
    }
    
    try {
        // Generate QR code text matrix using PHPQRCode without GD dependency
        ob_start();
        $matrix = \PHPQRCode\QRcode::text($qr_data, false, 'M', 3, 4);
        ob_end_clean();
        
        if (empty($matrix)) {
            return null;
        }
        
        $num_rows = count($matrix);
        $num_cols = strlen($matrix[0]);
        
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 ' . $num_cols . ' ' . $num_rows . '">';
        $svg .= '<rect width="100%" height="100%" fill="#ffffff"/>';
        $svg .= '<path d="';
        for ($r = 0; $r < $num_rows; $r++) {
            for ($c = 0; $c < $num_cols; $c++) {
                if ($matrix[$r][$c] === '1') {
                    $svg .= 'M' . $c . ',' . $r . 'h1v1h-1z ';
                }
            }
        }
        $svg .= '" fill="#000000"/>';
        $svg .= '</svg>';
        
        if (file_put_contents($file_path, $svg) !== false) {
            return 'uploads/qrcodes/' . $file_name;
        }
    } catch (Exception $e) {
        error_log("QR Code SVG Generation failed for $member_id: " . $e->getMessage());
    }
    
    return null;
}

/**
 * Log Admin activities
 */
function log_activity($pdo, $admin_id, $action, $target_member_id = null, $details = null) {
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        // Handle proxy forwarding IP if applicable
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, action, target_member_id, details, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$admin_id, $action, $target_member_id, $details, $ip_address]);
    } catch (PDOException $e) {
        error_log("Failed to log activity: " . $e->getMessage());
    }
}

/**
 * Generate CSRF Token for Forms
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format date helper
 */
function format_date($date_str, $format = 'd M Y') {
    if (empty($date_str)) return 'N/A';
    try {
        $date = new DateTime($date_str);
        return $date->format($format);
    } catch (Exception $e) {
        return 'N/A';
    }
}
