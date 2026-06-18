<?php
/**
 * Lurnixe Health Card System - Admin Sidebar Layout
 * June 2026
 */
$current_page = basename($_SERVER['PHP_SELF']);
$admin_role = $_SESSION['admin_role'] ?? 'admin';

// Dynamic Active Class helper
function is_active($page, $current) {
    return $page === $current ? 'active-sidebar' : '';
}
?>
<div class="sidebar bg-dark text-white border-end" id="sidebar-wrapper">
    <!-- Brand Info -->
    <div class="sidebar-brand-box py-4 px-3 d-flex align-items-center">
        <div class="brand-logo-container bg-success me-2" style="width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
            <span class="brand-icon text-white" style="font-size: 1.1rem;"><i class="fa-solid fa-heart-pulse"></i></span>
        </div>
        <span class="brand-name text-white fs-5 fw-bold font-heading">Lurnixe<span class="text-success">Health</span></span>
    </div>
    
    <!-- Navigation List -->
    <div class="list-group list-group-flush px-2">
        <small class="text-uppercase text-muted fw-bold px-3 py-2 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Main Menu</small>
        
        <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent <?php echo is_active('dashboard.php', $current_page); ?>">
            <i class="fa-solid fa-chart-pie me-3"></i>Dashboard
        </a>
        
        <a href="<?php echo BASE_URL; ?>admin/members.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent <?php echo ($current_page == 'members.php' && !isset($_GET['action'])) ? 'active-sidebar' : ''; ?>">
            <i class="fa-solid fa-users me-3"></i>Members
        </a>
        
        <a href="<?php echo BASE_URL; ?>admin/add-member.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent <?php echo is_active('add-member.php', $current_page); ?>">
            <i class="fa-solid fa-user-plus me-3"></i>Add Member
        </a>
        
        <a href="<?php echo BASE_URL; ?>admin/members.php?action=generate" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent <?php echo ($current_page == 'members.php' && isset($_GET['action']) && $_GET['action'] == 'generate') ? 'active-sidebar' : ''; ?>">
            <i class="fa-solid fa-id-card me-3"></i>Generate Cards
        </a>
        
        <a href="<?php echo BASE_URL; ?>admin/reports.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent <?php echo is_active('reports.php', $current_page); ?>">
            <i class="fa-solid fa-file-invoice-dollar me-3"></i>Reports
        </a>
        
        <a href="<?php echo BASE_URL; ?>admin/contact-inquiries.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent d-flex align-items-center <?php echo is_active('contact-inquiries.php', $current_page); ?>">
            <i class="fa-solid fa-envelope-open-text me-3"></i>Contact Inquiries
            <?php if (isset($unread_inquiries) && $unread_inquiries > 0): ?>
                <span class="badge bg-danger ms-auto rounded-pill small" style="font-size: 0.75rem;"><?php echo $unread_inquiries; ?></span>
            <?php endif; ?>
        </a>

        <!-- Administration Header (Super Admin only) -->
        <?php if ($admin_role === 'super_admin'): ?>
            <small class="text-uppercase text-muted fw-bold px-3 py-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">System Admin</small>
            
            <a href="<?php echo BASE_URL; ?>admin/admins.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent <?php echo is_active('admins.php', $current_page); ?>">
                <i class="fa-solid fa-user-gear me-3"></i>System Admin
            </a>
            
            <a href="<?php echo BASE_URL; ?>admin/activity-logs.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent <?php echo is_active('activity-logs.php', $current_page); ?>">
                <i class="fa-solid fa-shield-halved me-3"></i>Activity Logs
            </a>
        <?php endif; ?>
        
        <small class="text-uppercase text-muted fw-bold px-3 py-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Preferences</small>
        
        <a href="<?php echo BASE_URL; ?>admin/preferences.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-white bg-transparent <?php echo is_active('preferences.php', $current_page); ?>">
            <i class="fa-solid fa-sliders me-3"></i>Preferences
        </a>
        
        <a href="<?php echo BASE_URL; ?>admin/logout.php" class="list-group-item list-group-item-action py-2.5 px-3 border-0 rounded-2 text-danger bg-transparent mt-4">
            <i class="fa-solid fa-power-off me-3"></i>Logout
        </a>
    </div>
</div>
