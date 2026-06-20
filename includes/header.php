<?php
/**
 * Lurnixe Health Card System - Public Header Template
 * June 2026
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

// Fetch site settings from DB
$settings = [];
try {
    $settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    while ($row = $settings_stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    error_log("Failed to fetch site settings in header: " . $e->getMessage());
}

$site_name = $settings['site_name'] ?? 'LurnixeHealth';
$site_tagline = $settings['site_tagline'] ?? 'Caring for Your Health';
$logo_path = $settings['logo_path'] ?? '';
$favicon_path = $settings['favicon_path'] ?? '';
$meta_title = $settings['meta_title'] ?? ($site_name . ' - ' . $site_tagline);
$meta_description = $settings['meta_description'] ?? 'Lurnixe Health Family Health Card Management System - Smart. Simple. Secure.';
$meta_keywords = $settings['meta_keywords'] ?? 'health card, medical card';
$contact_phone = $settings['contact_phone'] ?? '+1 (800) 123-4567';
$contact_email = $settings['contact_email'] ?? 'support@lurnixehealth.com';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " | " . htmlspecialchars($site_name) : htmlspecialchars($meta_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    
    <!-- Favicon -->
    <?php if (!empty($favicon_path) && file_exists(__DIR__ . '/../' . $favicon_path)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL . $favicon_path; ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>favicon.ico">
    <?php endif; ?>
    
    <!-- Google Fonts (Poppins & Open Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- FontAwesome 6 (for rich iconography) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- AOS (Animate on Scroll) CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <!-- Custom Style CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css?v=1.0" rel="stylesheet">
</head>
<body class="<?php echo isset($body_class) ? htmlspecialchars($body_class) : ''; ?>">



    <!-- Mobile Top Header -->
    <div class="mobile-header d-lg-none d-flex justify-content-between align-items-center px-3 py-2 bg-white border-bottom sticky-top shadow-sm" style="height: 56px; z-index: 1020;">
        <div class="d-flex align-items-center">
            <?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
                <a href="javascript:history.back()" class="text-dark me-3" style="font-size: 1.2rem;">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            <?php else: ?>
                <div class="brand-logo-container bg-success me-2" style="width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fa-solid fa-heart-pulse" style="font-size: 0.9rem;"></i>
                </div>
            <?php endif; ?>
            <span class="fw-bold text-dark font-heading" style="font-size: 1.05rem;">
                <?php 
                if (basename($_SERVER['PHP_SELF']) === 'index.php') {
                    echo htmlspecialchars($site_name);
                } else {
                    echo isset($page_title) ? htmlspecialchars($page_title) : 'Lurnixe Health';
                }
                ?>
            </span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <?php if (basename($_SERVER['PHP_SELF']) !== 'scanner.php'): ?>
                <a href="#" class="position-relative text-dark" style="font-size: 1.15rem;">
                    <i class="fa-regular fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 6px; height: 6px;"></span>
                </a>
                <a href="#" class="text-dark" style="font-size: 1.15rem;" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <i class="fa-solid fa-bars"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Offcanvas Menu -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel" style="width: 280px; z-index: 1050;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold font-heading text-primary" id="mobileMenuLabel">
                <i class="fa-solid fa-heart-pulse me-2"></i>Lurnixe Health
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush">
                <a href="<?php echo BASE_URL; ?>index.php" class="list-group-item list-group-item-action py-3 border-0 <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'active bg-light text-primary fw-bold' : ''; ?>">
                    <i class="fa-solid fa-house me-3 text-muted"></i>Home
                </a>
                <a href="<?php echo BASE_URL; ?>about.php" class="list-group-item list-group-item-action py-3 border-0 <?php echo (basename($_SERVER['PHP_SELF']) === 'about.php') ? 'active bg-light text-primary fw-bold' : ''; ?>">
                    <i class="fa-solid fa-circle-info me-3 text-muted"></i>About Us
                </a>
                <a href="<?php echo BASE_URL; ?>health-card.php" class="list-group-item list-group-item-action py-3 border-0 <?php echo (basename($_SERVER['PHP_SELF']) === 'health-card.php') ? 'active bg-light text-primary fw-bold' : ''; ?>">
                    <i class="fa-solid fa-address-card me-3 text-muted"></i>Health Card
                </a>
                <a href="<?php echo BASE_URL; ?>services.php" class="list-group-item list-group-item-action py-3 border-0 <?php echo (basename($_SERVER['PHP_SELF']) === 'services.php') ? 'active bg-light text-primary fw-bold' : ''; ?>">
                    <i class="fa-solid fa-user-doctor me-3 text-muted"></i>Services & Doctors
                </a>
                <a href="<?php echo BASE_URL; ?>contact.php" class="list-group-item list-group-item-action py-3 border-0 <?php echo (basename($_SERVER['PHP_SELF']) === 'contact.php') ? 'active bg-light text-primary fw-bold' : ''; ?>">
                    <i class="fa-solid fa-calendar-check me-3 text-muted"></i>Book Appointment
                </a>
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <hr class="my-2 text-muted opacity-25">
                    <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="list-group-item list-group-item-action py-3 border-0">
                        <i class="fa-solid fa-chart-line me-3 text-primary"></i>Admin Dashboard
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/logout.php" class="list-group-item list-group-item-action py-3 border-0">
                        <i class="fa-solid fa-right-from-bracket me-3 text-danger"></i>Logout
                    </a>
                <?php else: ?>
                    <hr class="my-2 text-muted opacity-25">
                    <a href="<?php echo BASE_URL; ?>admin/login.php" class="list-group-item list-group-item-action py-3 border-0">
                        <i class="fa-solid fa-lock me-3 text-muted"></i>Admin Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg sticky-top navbar-light bg-white shadow-sm d-none d-lg-flex">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>index.php">
                <!-- Branding Icon/Logo representation -->
                <?php if (!empty($logo_path) && file_exists(__DIR__ . '/../' . $logo_path)): ?>
                    <img src="<?php echo BASE_URL . $logo_path; ?>" alt="<?php echo htmlspecialchars($site_name); ?>" style="max-height: 45px;" class="me-2">
                <?php else: ?>
                    <div class="brand-logo-container me-2">
                        <span class="brand-icon"><i class="fa-solid fa-heart-pulse"></i></span>
                    </div>
                <?php endif; ?>
                <div class="brand-text d-flex flex-column">
                    <span class="brand-name"><?php echo htmlspecialchars($site_name); ?></span>
                    <span class="brand-tagline"><?php echo htmlspecialchars($site_tagline); ?></span>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <?php
                    // Fetch dynamic navigation links from database
                    $header_navs = [];
                    try {
                        $nav_stmt = $pdo->query("SELECT * FROM nav_items WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
                        $header_navs = $nav_stmt->fetchAll();
                    } catch (Exception $e) {
                        error_log("Failed to load nav_items: " . $e->getMessage());
                    }
                    
                    $top_navs = [];
                    $sub_navs = [];
                    foreach ($header_navs as $nav) {
                        if (is_null($nav['parent_id']) || $nav['parent_id'] === '') {
                            $top_navs[] = $nav;
                        } else {
                            $sub_navs[$nav['parent_id']][] = $nav;
                        }
                    }
                    
                    foreach ($top_navs as $nav) {
                        $has_children = isset($sub_navs[$nav['id']]);
                        if ($has_children) {
                            echo '<li class="nav-item dropdown">';
                            echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown' . $nav['id'] . '" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . htmlspecialchars($nav['label']) . '</a>';
                            echo '<ul class="dropdown-menu border-0 shadow-sm" aria-labelledby="navbarDropdown' . $nav['id'] . '">';
                            foreach ($sub_navs[$nav['id']] as $child) {
                                $child_url = (strpos($child['url'], 'http') === 0 || strpos($child['url'], 'https') === 0) ? $child['url'] : BASE_URL . $child['url'];
                                echo '<li><a class="dropdown-item" href="' . $child_url . '">' . htmlspecialchars($child['label']) . '</a></li>';
                            }
                            echo '</ul>';
                            echo '</li>';
                        } else {
                            $nav_url = (strpos($nav['url'], 'http') === 0 || strpos($nav['url'], 'https') === 0) ? $nav['url'] : BASE_URL . $nav['url'];
                            $is_active = (basename($_SERVER['PHP_SELF']) === $nav['url']) ? 'active' : '';
                            echo '<li class="nav-item">';
                            echo '<a class="nav-link ' . $is_active . '" href="' . $nav_url . '">' . htmlspecialchars($nav['label']) . '</a>';
                            echo '</li>';
                        }
                    }
                    ?>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <?php if (isset($_SESSION['admin_id'])): ?>
                            <a class="btn btn-primary btn-nav px-4 rounded-pill" href="<?php echo BASE_URL; ?>admin/dashboard.php">
                                <i class="fa-solid fa-chart-line me-2"></i>Dashboard
                            </a>
                        <?php else: ?>
                            <a class="btn btn-outline-primary btn-nav px-4 rounded-pill me-2 mb-2 mb-lg-0" href="<?php echo BASE_URL; ?>admin/login.php">
                                <i class="fa-solid fa-lock me-2"></i>Login
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
