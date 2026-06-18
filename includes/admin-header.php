<?php
/**
 * Lurnixe Health Card System - Admin Header Template
 * June 2026
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Enforce authentication check
check_auth();

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];
$admin_email = $_SESSION['admin_email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " | " . SITE_NAME . " Admin" : SITE_NAME . " Admin Portal"; ?></title>
    
    <!-- Google Fonts (Poppins & Open Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    <!-- Custom Admin Style CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/admin.css?v=1.0" rel="stylesheet">
</head>
<body>

<div class="d-flex" id="wrapper">
    
    <!-- Sidebar Navigation -->
    <div class="bg-dark text-white border-end" id="sidebar-wrapper">
        <div class="sidebar-brand-box py-4 px-3 d-flex align-items-center">
            <div class="brand-logo-container bg-success me-2" style="width: 35px; height: 35px;">
                <span class="brand-icon text-white" style="font-size: 1.1rem;"><i class="fa-solid fa-heart-pulse"></i></span>
            </div>
            <span class="brand-name text-white fs-5 fw-bold">Lurnixe<span class="text-success">Health</span></span>
        </div>
        
        <div class="list-group list-group-flush px-2">
            <!-- Section Title -->
            <small class="text-uppercase text-muted fw-bold px-3 py-2 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Main Menu</small>
            
            <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-white bg-transparent <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active-sidebar' : ''; ?>">
                <i class="fa-solid fa-chart-pie me-3"></i>Dashboard
            </a>
            
            <a href="<?php echo BASE_URL; ?>admin/members.php" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-white bg-transparent <?php echo (basename($_SERVER['PHP_SELF']) == 'members.php' || basename($_SERVER['PHP_SELF']) == 'view-member.php' || basename($_SERVER['PHP_SELF']) == 'edit-member.php') ? 'active-sidebar' : ''; ?>">
                <i class="fa-solid fa-users me-3"></i>Members List
            </a>
            
            <a href="<?php echo BASE_URL; ?>admin/add-member.php" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-white bg-transparent <?php echo basename($_SERVER['PHP_SELF']) == 'add-member.php' ? 'active-sidebar' : ''; ?>">
                <i class="fa-solid fa-user-plus me-3"></i>Add Member
            </a>
            
            <a href="<?php echo BASE_URL; ?>admin/reports.php" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-white bg-transparent <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active-sidebar' : ''; ?>">
                <i class="fa-solid fa-file-invoice-dollar me-3"></i>Reports & Analytics
            </a>

            <!-- Administration Header (Super Admin only) -->
            <?php if ($admin_role === 'super_admin'): ?>
                <small class="text-uppercase text-muted fw-bold px-3 py-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">System Admin</small>
                
                <a href="<?php echo BASE_URL; ?>admin/admins.php" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-white bg-transparent <?php echo basename($_SERVER['PHP_SELF']) == 'admins.php' ? 'active-sidebar' : ''; ?>">
                    <i class="fa-solid fa-user-gear me-3"></i>Manage Admins
                </a>
                
                <a href="<?php echo BASE_URL; ?>admin/activity-logs.php" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-white bg-transparent <?php echo basename($_SERVER['PHP_SELF']) == 'activity-logs.php' ? 'active-sidebar' : ''; ?>">
                    <i class="fa-solid fa-shield-halved me-3"></i>Activity Logs
                </a>
            <?php endif; ?>
            
            <small class="text-uppercase text-muted fw-bold px-3 py-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Preference</small>
            
            <a href="<?php echo BASE_URL; ?>admin/preferences.php" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-white bg-transparent <?php echo basename($_SERVER['PHP_SELF']) == 'preferences.php' ? 'active-sidebar' : ''; ?>">
                <i class="fa-solid fa-sliders me-3"></i>Settings
            </a>
            
            <a href="<?php echo BASE_URL; ?>index.php" target="_blank" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-white bg-transparent">
                <i class="fa-solid fa-globe me-3"></i>Public Website
            </a>
            
            <a href="<?php echo BASE_URL; ?>admin/logout.php" class="list-group-item list-group-item-action py-3 px-3 border-0 rounded-2 text-danger bg-transparent mt-4">
                <i class="fa-solid fa-power-off me-3"></i>Sign Out
            </a>
        </div>
    </div>
    
    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper" class="w-100 bg-light">
        
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3 px-4 shadow-sm">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <!-- Hamburger menu button -->
                <button class="btn btn-outline-secondary btn-sm rounded" id="menu-toggle">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                
                <!-- Admin Info Badge -->
                <div class="d-flex align-items-center gap-3">
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
            
            <!-- Notification messages -->
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
                    } else {
                        echo "An error occurred. Please verify input and retry.";
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
