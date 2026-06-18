<?php
/**
 * Lurnixe Health Card System - AJAX Card Renewal API
 * June 2026
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Enforce login validation
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = trim($_POST['member_id'] ?? '');
    $duration = intval($_POST['renewal_duration'] ?? 1);
    $new_expiry = trim($_POST['new_expiry'] ?? '');
    $payment_confirmed = isset($_POST['renewal_payment']) ? 1 : 0;
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // 1. Verify CSRF
    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Security token mismatch. Please reload the page.']);
        exit;
    }
    
    if (empty($member_id) || empty($new_expiry) || !$payment_confirmed) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields and verify payment.']);
        exit;
    }
    
    try {
        // Fetch current member details
        $stmt = $pdo->prepare("SELECT validity_date, name, status FROM members WHERE member_id = ?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch();
        
        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'Member profile not found.']);
            exit;
        }
        
        $current_expiry = $member['validity_date'];
        
        // 2. Transaction execution
        $pdo->beginTransaction();
        
        // Insert renewal logs
        $renewal_stmt = $pdo->prepare("INSERT INTO renewals (member_id, renewed_by, previous_validity, new_validity, renewal_duration_years) VALUES (?, ?, ?, ?, ?)");
        $renewal_stmt->execute([$member_id, $_SESSION['admin_id'], $current_expiry, $new_expiry, $duration]);
        
        // Update member validity date, payment status, and reset card status to 'active'
        $update_stmt = $pdo->prepare("UPDATE members SET validity_date = ?, status = 'active', payment_status = 1 WHERE member_id = ?");
        $update_stmt->execute([$new_expiry, $member_id]);
        
        // Log action
        log_activity($pdo, $_SESSION['admin_id'], 'renew_card', $member_id, "Renewed card validity for $duration year(s). New expiry: $new_expiry. (Previous expiry was $current_expiry)");
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Health Card validity renewed successfully. Card status reset to ACTIVE.'
        ]);
        exit;
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Failed to renew member card: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database operation failed: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
