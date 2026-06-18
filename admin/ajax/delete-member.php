<?php
/**
 * Lurnixe Health Card System - Super Admin Soft/Hard Delete
 * June 2026
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Enforce Super Admin authorization
try {
    require_super_admin();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = trim($_POST['member_id'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // 1. Verify CSRF Token
    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Security token mismatch. Please reload the page.']);
        exit;
    }
    
    if (empty($member_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid member ID.']);
        exit;
    }
    
    try {
        // Fetch member name for logging
        $stmt = $pdo->prepare("SELECT name, photo, qr_code FROM members WHERE member_id = ?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch();
        
        if (!$member) {
            echo json_encode(['success' => false, 'message' => 'Member not found.']);
            exit;
        }
        
        // Delete uploaded files
        if (!empty($member['photo']) && file_exists(__DIR__ . '/../../' . $member['photo'])) {
            unlink(__DIR__ . '/../../' . $member['photo']);
        }
        if (!empty($member['qr_code']) && file_exists(__DIR__ . '/../../' . $member['qr_code'])) {
            unlink(__DIR__ . '/../../' . $member['qr_code']);
        }
        
        // Delete member record (foreign key cascades will clear family members and renewals)
        $delete_stmt = $pdo->prepare("DELETE FROM members WHERE member_id = ?");
        $delete_stmt->execute([$member_id]);
        
        // Log action
        log_activity($pdo, $_SESSION['admin_id'], 'delete_member', $member_id, "Deleted member profile and files for " . $member['name']);
        
        echo json_encode(['success' => true, 'message' => 'Member and associated records deleted successfully.']);
        exit;
        
    } catch (PDOException $e) {
        error_log("Failed to delete member: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database operation failed: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
