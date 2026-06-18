<?php
/**
 * Lurnixe Health Card System - Card Status Administration
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
    $new_status = trim($_POST['status'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // 1. Verify CSRF Token
    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Security token mismatch. Please reload the page.']);
        exit;
    }
    
    // Validate status values
    $allowed_statuses = ['active', 'expired', 'suspended', 'deactivated'];
    if (!in_array($new_status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status parameter.']);
        exit;
    }
    
    try {
        // Fetch current status of the member
        $stmt = $pdo->prepare("SELECT status, name FROM members WHERE member_id = ?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch();
        
        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'Member not found.']);
            exit;
        }
        
        $current_status = $member['status'];
        
        // 2. Role restriction: Only Super Admin can change status FROM deactivated
        if ($current_status === 'deactivated' && $_SESSION['admin_role'] !== 'super_admin') {
            echo json_encode(['success' => false, 'message' => 'Reactivating a deactivated profile requires Super Admin privileges.']);
            exit;
        }
        
        // 3. Update status in database
        $update_stmt = $pdo->prepare("UPDATE members SET status = ? WHERE member_id = ?");
        $update_stmt->execute([$new_status, $member_id]);
        
        // Log action
        log_activity($pdo, $_SESSION['admin_id'], 'update_status', $member_id, "Changed status from $current_status to $new_status for " . $member['name']);
        
        echo json_encode(['success' => true, 'message' => "Health Card status updated to " . strtoupper($new_status)]);
        exit;
        
    } catch (PDOException $e) {
        error_log("Failed to update card status: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database operation failed.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
