<?php
/**
 * Lurnixe Health Card System - Secure Admin Header Template
 * June 2026
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Enforce admin authorization check
check_auth();

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];
$admin_email = $_SESSION['admin_email'];

// Count unread contact inquiries for the sidebar badge
$unread_inquiries = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'new'");
    $unread_inquiries = (int)$stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Failed to count unread contact inquiries: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " | " . SITE_NAME . " Admin" : SITE_NAME . " Admin Portal"; ?></title>
    
    <!-- Google Fonts (Poppins & Open Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom Admin Style CSS (Absolute path as requested) -->
    <link href="<?php echo BASE_URL; ?>assets/css/admin.css?v=1.2" rel="stylesheet">
</head>
<body>

<div class="d-flex">
    
    <!-- Sidebar Navigation -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>
    
    <!-- Main Content Wrapper -->
    <div class="main-content w-100 bg-light d-flex flex-column">
        
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3 px-4 shadow-sm">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <!-- Hamburger menu button (Visible only on Mobile) -->
                <button class="btn btn-outline-secondary btn-sm rounded d-md-none" id="menu-toggle">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                
                <!-- Spacer on Desktop, Toggle on Mobile -->
                <div class="d-none d-md-block">
                    <h5 class="m-0 fw-semibold text-dark font-heading"><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Control Panel"; ?></h5>
                </div>
                
                <!-- Admin Info Badge -->
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="text-end">
                        <span class="d-block fw-bold text-dark font-heading small"><?php echo htmlspecialchars($admin_name); ?></span>
                        <span class="badge bg-success-subtle text-success small rounded-pill" style="font-size: 0.65rem;"><?php echo htmlspecialchars(strtoupper($admin_role)); ?></span>
                    </div>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px;">
                        <?php echo strtoupper(substr($admin_name, 0, 1)); ?>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Content Body Container -->
        <div class="container-fluid py-4 px-4">
            
            <!-- Global Notification messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Action completed successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> 
                    <?php 
                    $err_type = $_GET['error'];
                    if ($err_type === 'unauthorized') {
                        echo "Access Denied: You do not have permissions to access that module.";
                    } elseif ($err_type === 'self_status') {
                        echo "Operation Blocked: You cannot deactivate your own account.";
                    } elseif ($err_type === 'self_delete') {
                        echo "Operation Blocked: You cannot delete your own account.";
                    } else {
                        echo "An error occurred. Please verify inputs and retry.";
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
