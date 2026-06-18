<?php
/**
 * Lurnixe Health Card System - AJAX Edit Member Backend
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
    // 1. Verify CSRF Token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Security token mismatch. Please reload the page.']);
        exit;
    }
    
    // 2. Retrieve & Sanitize Inputs
    $member_id = trim($_POST['member_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $alt_mobile = trim($_POST['alt_mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Address combination
    $addr_line1 = trim($_POST['address_line1'] ?? '');
    $addr_line2 = trim($_POST['address_line2'] ?? '');
    $address = $addr_line1 . (!empty($addr_line2) ? ', ' . $addr_line2 : '');
    
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    
    $blood_group = trim($_POST['blood_group'] ?? '');
    $emergency_name = trim($_POST['emergency_name'] ?? '');
    $emergency_mobile = trim($_POST['emergency_mobile'] ?? '');
    $allergies = trim($_POST['allergies'] ?? '');
    $health_info = trim($_POST['health_info'] ?? '');
    
    // Card Settings
    $payment_status = isset($_POST['payment_status']) ? 1 : 0;
    
    // 3. Validation
    if (empty($member_id) || empty($name) || empty($gender) || empty($dob) || empty($mobile) || empty($addr_line1) || empty($city) || empty($state) || empty($pincode) || empty($blood_group) || empty($emergency_name) || empty($emergency_mobile)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
    
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo json_encode(['success' => false, 'message' => 'Mobile number must be a valid 10-digit number.']);
        exit;
    }
    
    if (!preg_match('/^[0-9]{10}$/', $emergency_mobile)) {
        echo json_encode(['success' => false, 'message' => 'Emergency contact number must be a valid 10-digit number.']);
        exit;
    }
    
    if (!empty($alt_mobile) && !preg_match('/^[0-9]{10}$/', $alt_mobile)) {
        echo json_encode(['success' => false, 'message' => 'Alternate mobile number must be a valid 10-digit number.']);
        exit;
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    
    // Calculate Age
    $age = calculate_age($dob);
    
    try {
        // Fetch current member details
        $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
        $stmt->execute([$member_id]);
        $current_member = $stmt->fetch();
        
        if (!$current_member) {
            echo json_encode(['success' => false, 'message' => 'Member not found.']);
            exit;
        }
        
        // Handle Status logic (only Admin status change checks)
        $new_status = trim($_POST['status'] ?? $current_member['status']);
        $allowed_statuses = ['active', 'expired', 'suspended', 'deactivated'];
        if (!in_array($new_status, $allowed_statuses)) {
            $new_status = $current_member['status'];
        }
        
        // Role check for deactivated state reactivation
        if ($current_member['status'] === 'deactivated' && $new_status !== 'deactivated' && $_SESSION['admin_role'] !== 'super_admin') {
            echo json_encode(['success' => false, 'message' => 'Reactivating a deactivated profile requires Super Admin privileges.']);
            exit;
        }
        
        // 4. Handle Photo Upload
        $photo_path = $current_member['photo'];
        $new_photo_uploaded = false;
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['photo'];
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Error uploading member photograph.']);
                exit;
            }
            
            if ($file['size'] > 2 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'Photograph file size cannot exceed 2MB.']);
                exit;
            }
            
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($file_info, $file['tmp_name']);
            finfo_close($file_info);
            
            if (!in_array($mime_type, $allowed_types)) {
                echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, and PNG images are allowed.']);
                exit;
            }
            
            $ext = ($mime_type === 'image/png') ? 'png' : 'jpg';
            $filename = bin2hex(random_bytes(16)) . '.' . $ext;
            $dest_path = __DIR__ . '/../../uploads/members/' . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                $photo_path = 'uploads/members/' . $filename;
                $new_photo_uploaded = true;
                
                // Delete old photo if it exists to free up space
                if (!empty($current_member['photo']) && file_exists(__DIR__ . '/../../' . $current_member['photo'])) {
                    unlink(__DIR__ . '/../../' . $current_member['photo']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save uploaded photograph.']);
                exit;
            }
        }
        
        // 5. Database Transaction Updates
        $pdo->beginTransaction();
        
        // Update member details
        $sql = "UPDATE members SET 
                    name = :name, 
                    photo = :photo, 
                    mobile = :mobile, 
                    alt_mobile = :alt_mobile, 
                    email = :email, 
                    address = :address, 
                    city = :city, 
                    state = :state, 
                    pincode = :pincode, 
                    dob = :dob, 
                    age = :age, 
                    gender = :gender, 
                    blood_group = :blood_group, 
                    allergies = :allergies, 
                    health_info = :health_info, 
                    emergency_name = :emergency_name, 
                    emergency_mobile = :emergency_mobile, 
                    status = :status, 
                    payment_status = :payment_status
                WHERE member_id = :member_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':photo' => $photo_path,
            ':mobile' => $mobile,
            ':alt_mobile' => !empty($alt_mobile) ? $alt_mobile : null,
            ':email' => !empty($email) ? $email : null,
            ':address' => $address,
            ':city' => $city,
            ':state' => $state,
            ':pincode' => $pincode,
            ':dob' => $dob,
            ':age' => $age,
            ':gender' => $gender,
            ':blood_group' => $blood_group,
            ':allergies' => !empty($allergies) ? $allergies : null,
            ':health_info' => !empty($health_info) ? $health_info : null,
            ':emergency_name' => $emergency_name,
            ':emergency_mobile' => $emergency_mobile,
            ':status' => $new_status,
            ':payment_status' => $payment_status,
            ':member_id' => $member_id
        ]);
        
        // Regenerate QR Code if base settings change or if missing
        if (empty($current_member['qr_code']) || !file_exists(__DIR__ . '/../../' . $current_member['qr_code'])) {
            $qr_path = generate_qr_code($member_id);
            if ($qr_path) {
                $update_qr_stmt = $pdo->prepare("UPDATE members SET qr_code = ? WHERE member_id = ?");
                $update_qr_stmt->execute([$qr_path, $member_id]);
            }
        }
        
        // Update Family Members (Atomic delete and re-insert)
        $delete_fam_stmt = $pdo->prepare("DELETE FROM family_members WHERE member_id = ?");
        $delete_fam_stmt->execute([$member_id]);
        
        if (isset($_POST['family']) && is_array($_POST['family'])) {
            $fam_sql = "INSERT INTO family_members (member_id, name, relation, dob, blood_group) VALUES (?, ?, ?, ?, ?)";
            $fam_stmt = $pdo->prepare($fam_sql);
            
            foreach ($_POST['family'] as $dep) {
                $dep_name = trim($dep['name'] ?? '');
                $dep_relation = trim($dep['relation'] ?? '');
                $dep_dob = trim($dep['dob'] ?? '');
                $dep_blood = trim($dep['blood_group'] ?? '');
                
                if (!empty($dep_name) && !empty($dep_relation) && !empty($dep_dob) && !empty($dep_blood)) {
                    $fam_stmt->execute([$member_id, $dep_name, $dep_relation, $dep_dob, $dep_blood]);
                }
            }
        }
        
        // Log action
        log_activity($pdo, $_SESSION['admin_id'], 'edit_member', $member_id, "Updated member profile for $name");
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Member profile updated successfully.',
            'member_id' => $member_id
        ]);
        exit;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        
        // Clean up newly uploaded image if transaction failed
        if ($new_photo_uploaded && file_exists(__DIR__ . '/../../' . $photo_path)) {
            unlink(__DIR__ . '/../../' . $photo_path);
        }
        
        error_log("Failed to update member details: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database operation failed: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
