<?php
/**
 * Lurnixe Health Card System - AJAX Register Member Backend
 * June 2026
 */

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

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
    $validity = intval($_POST['validity'] ?? 1);
    $payment_status = isset($_POST['payment_status']) ? 1 : 0;
    
    // 3. Validation
    if (empty($name) || empty($gender) || empty($dob) || empty($mobile) || empty($addr_line1) || empty($city) || empty($state) || empty($pincode) || empty($blood_group) || empty($emergency_name) || empty($emergency_mobile)) {
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
    
    // Calculate Age & Card Expiration dates
    $age = calculate_age($dob);
    $validity_date = date('Y-m-d', strtotime("+$validity year"));
    
    // 4. Handle Photo Upload
    $photo_relative_path = null;
    $photo_uploaded = false;
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];
        
        // Check upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Error uploading member photograph. Code: ' . $file['error']]);
            exit;
        }
        
        // Validate size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Photograph file size cannot exceed 2MB.']);
            exit;
        }
        
        // Validate MIME type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $file['tmp_name']);
        finfo_close($file_info);
        
        if (!in_array($mime_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file format. Only JPG, JPEG, and PNG images are allowed.']);
            exit;
        }
        
        // Determine file extension
        $ext = ($mime_type === 'image/png') ? 'png' : 'jpg';
        
        // Generate randomized filename to prevent directory traversal / overwrite
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest_path = __DIR__ . '/../../uploads/members/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $dest_path)) {
            $photo_relative_path = 'uploads/members/' . $filename;
            $photo_uploaded = true;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded photograph.']);
            exit;
        }
    }
    
    // 5. Database Transaction Operations
    try {
        $pdo->beginTransaction();
        
        // Generate unique Member ID
        $member_id = generate_member_id($pdo);
        
        // Insert member profile
        $sql = "INSERT INTO members (member_id, name, photo, mobile, alt_mobile, email, address, city, state, pincode, dob, age, gender, blood_group, allergies, health_info, emergency_name, emergency_mobile, validity_date, status, payment_status, created_by) 
                VALUES (:member_id, :name, :photo, :mobile, :alt_mobile, :email, :address, :city, :state, :pincode, :dob, :age, :gender, :blood_group, :allergies, :health_info, :emergency_name, :emergency_mobile, :validity_date, 'active', :payment_status, :created_by)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':member_id' => $member_id,
            ':name' => $name,
            ':photo' => $photo_relative_path,
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
            ':validity_date' => $validity_date,
            ':payment_status' => $payment_status,
            ':created_by' => $_SESSION['admin_id']
        ]);
        
        // Generate and save QR Code
        $qr_path = generate_qr_code($member_id);
        
        if ($qr_path) {
            // Update QR code path
            $update_qr_stmt = $pdo->prepare("UPDATE members SET qr_code = ? WHERE member_id = ?");
            $update_qr_stmt->execute([$qr_path, $member_id]);
        }
        
        // Save family members if provided
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
        
        // Log administrative action
        log_activity($pdo, $_SESSION['admin_id'], 'add_member', $member_id, "Registered new member profile: $name");
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Member registered successfully.',
            'member_id' => $member_id
        ]);
        exit;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        
        // Clean up uploaded image if transaction failed
        if ($photo_uploaded && file_exists(__DIR__ . '/../../' . $photo_relative_path)) {
            unlink(__DIR__ . '/../../' . $photo_relative_path);
        }
        
        error_log("Failed to register member: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database operation failed: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
