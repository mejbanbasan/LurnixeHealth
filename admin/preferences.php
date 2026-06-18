<?php
/**
 * Lurnixe Health Card System - System Preferences & Site Customizer
 * June 2026
 */
$page_title = "Preferences";
require_once __DIR__ . '/includes/header.php';

$admin_id = $_SESSION['admin_id'];
$admin_role = $_SESSION['admin_role'];
$csrf = get_csrf_token();
$success_msg = "";
$error_msg = "";

// Helper function to save setting
function save_setting($pdo, $key, $value) {
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$key, $value, $value]);
}

// 1. Process Actions (Delete Stat, Delete Nav)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $csrf_token = $_GET['csrf_token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        $error_msg = "Security token mismatch. Please try again.";
    } else {
        if ($action === 'delete_stat') {
            $stat_id = intval($_GET['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM stat_counters WHERE id = ?");
            $stmt->execute([$stat_id]);
            log_activity($pdo, $admin_id, 'delete_stat', null, "Deleted stats counter ID: $stat_id");
            $success_msg = "Stats counter removed successfully.";
        } elseif ($action === 'delete_nav') {
            $nav_id = intval($_GET['id'] ?? 0);
            // Delete child items first or set their parent to null
            $stmt = $pdo->prepare("UPDATE nav_items SET parent_id = NULL WHERE parent_id = ?");
            $stmt->execute([$nav_id]);
            
            $stmt = $pdo->prepare("DELETE FROM nav_items WHERE id = ?");
            $stmt->execute([$nav_id]);
            log_activity($pdo, $admin_id, 'delete_nav', null, "Deleted navbar item ID: $nav_id");
            $success_msg = "Navbar menu item removed successfully.";
        }
    }
}

// 2. Process POST Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        $error_msg = "Security token mismatch. Please reload the page.";
    } else {
        $section = $_POST['section'] ?? '';
        
        // --- BRANDING ---
        if ($section === 'branding') {
            $site_name = trim($_POST['site_name'] ?? '');
            $site_tagline = trim($_POST['site_tagline'] ?? '');
            
            save_setting($pdo, 'site_name', $site_name);
            save_setting($pdo, 'site_tagline', $site_tagline);
            
            $branding_dir = __DIR__ . '/../uploads/branding/';
            if (!file_exists($branding_dir)) {
                mkdir($branding_dir, 0755, true);
            }
            
            // Upload Site Logo
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['logo'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['png', 'jpg', 'jpeg', 'svg'])) {
                    $logo_filename = 'logo_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $branding_dir . $logo_filename)) {
                        save_setting($pdo, 'logo_path', 'uploads/branding/' . $logo_filename);
                    }
                }
            }
            
            // Upload Favicon
            if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['favicon'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['ico', 'png', 'gif'])) {
                    $fav_filename = 'favicon_' . time() . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $branding_dir . $fav_filename)) {
                        save_setting($pdo, 'favicon_path', 'uploads/branding/' . $fav_filename);
                    }
                }
            }
            
            log_activity($pdo, $admin_id, 'update_branding', null, "Updated branding configurations");
            $success_msg = "Logo & Branding preferences saved successfully.";
        }
        
        // --- STATS COUNTERS (EDIT LIST) ---
        elseif ($section === 'stats_list') {
            if (isset($_POST['stats']) && is_array($_POST['stats'])) {
                $stmt = $pdo->prepare("UPDATE stat_counters SET number = ?, label = ?, sort_order = ?, is_active = ? WHERE id = ?");
                foreach ($_POST['stats'] as $stat_id => $data) {
                    $num = trim($data['number'] ?? '');
                    $label = trim($data['label'] ?? '');
                    $order = intval($data['sort_order'] ?? 0);
                    $active = isset($data['is_active']) ? 1 : 0;
                    
                    if (!empty($num) && !empty($label)) {
                        $stmt->execute([$num, $label, $order, $active, $stat_id]);
                    }
                }
            }
            log_activity($pdo, $admin_id, 'update_stats_list', null, "Updated stats counter records");
            $success_msg = "Stats counters updated successfully.";
        }
        
        // --- ADD STAT COUNTER ---
        elseif ($section === 'add_stat') {
            $number = trim($_POST['number'] ?? '');
            $label = trim($_POST['label'] ?? '');
            $order = intval($_POST['sort_order'] ?? 0);
            
            if (empty($number) || empty($label)) {
                $error_msg = "Please enter both a number (e.g. 100+) and a label.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO stat_counters (number, label, sort_order, is_active) VALUES (?, ?, ?, 1)");
                $stmt->execute([$number, $label, $order]);
                log_activity($pdo, $admin_id, 'add_stat', null, "Created new stats counter: $number $label");
                $success_msg = "New stats counter added successfully.";
            }
        }
        
        // --- NAVBAR ITEMS (EDIT LIST) ---
        elseif ($section === 'navbar_list') {
            if (isset($_POST['navs']) && is_array($_POST['navs'])) {
                $stmt = $pdo->prepare("UPDATE nav_items SET label = ?, url = ?, parent_id = ?, sort_order = ?, is_active = ? WHERE id = ?");
                foreach ($_POST['navs'] as $nav_id => $data) {
                    $lbl = trim($data['label'] ?? '');
                    $url = trim($data['url'] ?? '');
                    $parent = (!empty($data['parent_id'])) ? intval($data['parent_id']) : null;
                    $order = intval($data['sort_order'] ?? 0);
                    $active = isset($data['is_active']) ? 1 : 0;
                    
                    // Prevent circular parenting
                    if ($parent == $nav_id) {
                        $parent = null;
                    }
                    
                    if (!empty($lbl) && !empty($url)) {
                        $stmt->execute([$lbl, $url, $parent, $order, $active, $nav_id]);
                    }
                }
            }
            log_activity($pdo, $admin_id, 'update_navbar_list', null, "Updated navbar links schema");
            $success_msg = "Navbar menu configuration saved successfully.";
        }
        
        // --- ADD NAVBAR ITEM ---
        elseif ($section === 'add_nav') {
            $label = trim($_POST['label'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $parent = (!empty($_POST['parent_id'])) ? intval($_POST['parent_id']) : null;
            $order = intval($_POST['sort_order'] ?? 0);
            
            if (empty($label) || empty($url)) {
                $error_msg = "Please fill in both the Menu Label and URL fields.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO nav_items (label, url, parent_id, sort_order, is_active) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$label, $url, $parent, $order]);
                log_activity($pdo, $admin_id, 'add_nav', null, "Created navbar link: $label ($url)");
                $success_msg = "New navbar item added successfully.";
            }
        }
        
        // --- SOCIAL LINKS ---
        elseif ($section === 'social') {
            $platforms = ['facebook', 'instagram', 'twitter', 'youtube', 'linkedin'];
            foreach ($platforms as $platform) {
                $url_val = trim($_POST[$platform . '_url'] ?? '');
                $show_val = isset($_POST['show_' . $platform]) ? '1' : '0';
                
                save_setting($pdo, $platform . '_url', $url_val);
                save_setting($pdo, 'show_' . $platform, $show_val);
            }
            log_activity($pdo, $admin_id, 'update_social_links', null, "Updated dynamic social links config");
            $success_msg = "Social media links updated successfully.";
        }
        
        // --- CONTACT INFO ---
        elseif ($section === 'contact') {
            $phone = trim($_POST['contact_phone'] ?? '');
            $email = trim($_POST['contact_email'] ?? '');
            $address = trim($_POST['contact_address'] ?? '');
            $whatsapp = trim($_POST['contact_whatsapp'] ?? '');
            
            // Clean whatsapp to only contain numeric characters
            $whatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
            
            save_setting($pdo, 'contact_phone', $phone);
            save_setting($pdo, 'contact_email', $email);
            save_setting($pdo, 'contact_address', $address);
            save_setting($pdo, 'contact_whatsapp', $whatsapp);
            
            log_activity($pdo, $admin_id, 'update_contact_info', null, "Updated dynamic contact numbers");
            $success_msg = "Contact details updated successfully.";
        }
        
        // --- FOOTER CONFIG ---
        elseif ($section === 'footer') {
            $tagline = trim($_POST['footer_tagline'] ?? '');
            $copyright = trim($_POST['footer_copyright'] ?? '');
            $desc = trim($_POST['footer_description'] ?? '');
            
            save_setting($pdo, 'footer_tagline', $tagline);
            save_setting($pdo, 'footer_copyright', $copyright);
            save_setting($pdo, 'footer_description', $desc);
            
            log_activity($pdo, $admin_id, 'update_footer_settings', null, "Updated footer layouts");
            $success_msg = "Footer layout configurations saved successfully.";
        }
        
        // --- SEO SETTINGS ---
        elseif ($section === 'seo') {
            $title = trim($_POST['meta_title'] ?? '');
            $desc = trim($_POST['meta_description'] ?? '');
            $keywords = trim($_POST['meta_keywords'] ?? '');
            
            save_setting($pdo, 'meta_title', $title);
            save_setting($pdo, 'meta_description', $desc);
            save_setting($pdo, 'meta_keywords', $keywords);
            
            log_activity($pdo, $admin_id, 'update_seo_settings', null, "Updated dynamic SEO tags");
            $success_msg = "SEO settings saved successfully.";
        }
    }
}

// 3. Fetch Settings & Data for rendering
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Fetch stats counters
$stats_counters = $pdo->query("SELECT * FROM stat_counters ORDER BY sort_order ASC, id ASC")->fetchAll();

// Fetch navbar items
$nav_items = $pdo->query("SELECT * FROM nav_items ORDER BY sort_order ASC, id ASC")->fetchAll();
// Map navbar parent items for drop-down (items where parent_id is null)
$parent_navs = array_filter($nav_items, function($item) {
    return is_null($item['parent_id']);
});

// Determine active tab after POST redirect simulation
$active_tab = $_POST['section'] ?? ($_GET['section'] ?? 'branding');
if ($active_tab === 'stats_list' || $active_tab === 'add_stat') $active_tab = 'stats';
if ($active_tab === 'navbar_list' || $active_tab === 'add_nav') $active_tab = 'navbar-menu';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark fw-bold font-heading">System Preferences</h2>
        <p class="text-muted small">Configure global branding logos, stats counters, header menus, social media portals, and metadata options dynamically.</p>
    </div>
</div>

<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Tabs navigation -->
<ul class="nav nav-pills mb-4 gap-2 flex-wrap" id="preferencesTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 <?php echo ($active_tab === 'branding') ? 'active' : ''; ?>" id="branding-tab" data-bs-toggle="pill" data-bs-target="#branding" type="button" role="tab" aria-controls="branding" aria-selected="true"><i class="fa-solid fa-copyright me-2"></i>Logo & Branding</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 <?php echo ($active_tab === 'stats') ? 'active' : ''; ?>" id="stats-tab" data-bs-toggle="pill" data-bs-target="#stats" type="button" role="tab" aria-controls="stats" aria-selected="false"><i class="fa-solid fa-chart-simple me-2"></i>Stats Counter</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 <?php echo ($active_tab === 'navbar-menu') ? 'active' : ''; ?>" id="navbar-tab" data-bs-toggle="pill" data-bs-target="#navbar-menu" type="button" role="tab" aria-controls="navbar-menu" aria-selected="false"><i class="fa-solid fa-bars me-2"></i>Navbar Menu</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 <?php echo ($active_tab === 'social') ? 'active' : ''; ?>" id="social-tab" data-bs-toggle="pill" data-bs-target="#social" type="button" role="tab" aria-controls="social" aria-selected="false"><i class="fa-solid fa-share-nodes me-2"></i>Social Links</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 <?php echo ($active_tab === 'contact') ? 'active' : ''; ?>" id="contact-tab" data-bs-toggle="pill" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false"><i class="fa-solid fa-phone me-2"></i>Contact Info</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 <?php echo ($active_tab === 'footer') ? 'active' : ''; ?>" id="footer-tab" data-bs-toggle="pill" data-bs-target="#footer" type="button" role="tab" aria-controls="footer" aria-selected="false"><i class="fa-solid fa-rectangle-list me-2"></i>Footer Options</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 <?php echo ($active_tab === 'seo') ? 'active' : ''; ?>" id="seo-tab" data-bs-toggle="pill" data-bs-target="#seo" type="button" role="tab" aria-controls="seo" aria-selected="false"><i class="fa-solid fa-magnifying-glass me-2"></i>SEO Settings</button>
    </li>
</ul>

<!-- Tabs content -->
<div class="tab-content bg-white p-4 rounded-3 shadow-sm border mb-5" id="preferencesTabsContent">
    
    <!-- 1. LOGO & BRANDING -->
    <div class="tab-pane fade <?php echo ($active_tab === 'branding') ? 'show active' : ''; ?>" id="branding" role="tabpanel" aria-labelledby="branding-tab">
        <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2">Logo & Branding Details</h5>
        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="section" value="branding">
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="site_name" class="form-label small fw-bold">Site Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="site_name" name="site_name" required value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>">
                        <div class="invalid-feedback">Please enter the Site Brand Name.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="site_tagline" class="form-label small fw-bold">Site Tagline</label>
                        <input type="text" class="form-control rounded-2" id="site_tagline" name="site_tagline" value="<?php echo htmlspecialchars($settings['site_tagline'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="col-md-6 text-center text-md-start">
                    <div class="row">
                        <!-- Logo Upload -->
                        <div class="col-sm-6 mb-3">
                            <label class="form-label small fw-bold d-block">Website Logo</label>
                            <div class="border rounded-3 p-3 bg-light mb-2 d-flex align-items-center justify-content-center" style="height: 100px; overflow: hidden;">
                                <?php if (!empty($settings['logo_path']) && file_exists(__DIR__ . '/../' . $settings['logo_path'])): ?>
                                    <img src="<?php echo BASE_URL . $settings['logo_path']; ?>" class="img-fluid" style="max-height: 80px;" alt="Logo">
                                <?php else: ?>
                                    <span class="text-muted small">No Logo Uploaded</span>
                                <?php endif; ?>
                            </div>
                            <input type="file" class="form-control form-control-sm" name="logo" accept="image/png, image/jpeg, image/svg+xml">
                            <span class="text-muted small" style="font-size: 0.7rem;">PNG, JPG or SVG formats only.</span>
                        </div>
                        
                        <!-- Favicon Upload -->
                        <div class="col-sm-6 mb-3">
                            <label class="form-label small fw-bold d-block">Favicon</label>
                            <div class="border rounded-3 p-3 bg-light mb-2 d-flex align-items-center justify-content-center" style="height: 100px; overflow: hidden;">
                                <?php if (!empty($settings['favicon_path']) && file_exists(__DIR__ . '/../' . $settings['favicon_path'])): ?>
                                    <img src="<?php echo BASE_URL . $settings['favicon_path']; ?>" style="width: 32px; height: 32px;" alt="Favicon">
                                <?php else: ?>
                                    <span class="text-muted small">No Favicon</span>
                                <?php endif; ?>
                            </div>
                            <input type="file" class="form-control form-control-sm" name="favicon" accept="image/x-icon, image/png">
                            <span class="text-muted small" style="font-size: 0.7rem;">ICO or PNG formats only.</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-success rounded-pill px-5">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Branding Preferences
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- 2. STATS COUNTER -->
    <div class="tab-pane fade <?php echo ($active_tab === 'stats') ? 'show active' : ''; ?>" id="stats" role="tabpanel" aria-labelledby="stats-tab">
        <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2">Stats Counter Directory</h5>
        
        <!-- List / Edit Counters Form -->
        <form action="" method="POST" class="needs-validation mb-5" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="section" value="stats_list">
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle small">
                    <thead class="bg-light">
                        <tr class="fw-bold">
                            <th style="width: 150px;">Number (e.g. 500+)</th>
                            <th>Label Title (e.g. Doctors)</th>
                            <th style="width: 100px;">Sort Order</th>
                            <th style="width: 100px;" class="text-center">Status</th>
                            <th style="width: 80px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats_counters)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No stats counters defined yet. Add one below.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats_counters as $stat): ?>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm rounded-2" name="stats[<?php echo $stat['id']; ?>][number]" required value="<?php echo htmlspecialchars($stat['number']); ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm rounded-2" name="stats[<?php echo $stat['id']; ?>][label]" required value="<?php echo htmlspecialchars($stat['label']); ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm rounded-2" name="stats[<?php echo $stat['id']; ?>][sort_order]" required value="<?php echo htmlspecialchars($stat['sort_order']); ?>">
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="stats[<?php echo $stat['id']; ?>][is_active]" value="1" <?php echo ($stat['is_active'] == 1) ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="preferences.php?action=delete_stat&id=<?php echo $stat['id']; ?>&csrf_token=<?php echo $csrf; ?>" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('Are you sure you want to delete this stats counter?');">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($stats_counters)): ?>
                <button type="submit" class="btn btn-success rounded-pill px-4 mt-2">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save All Counter Changes
                </button>
            <?php endif; ?>
        </form>
        
        <!-- Add Counter Box -->
        <div class="card border rounded-3 p-4 bg-light">
            <h6 class="text-dark fw-bold mb-3"><i class="fa-solid fa-square-plus text-primary me-2"></i>Add New Stats Counter</h6>
            <form action="" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="section" value="add_stat">
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="add_stat_number" class="form-label small fw-bold">Number Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm rounded-2" id="add_stat_number" name="number" required placeholder="e.g. 500+, 10k+">
                        <div class="invalid-feedback">Please enter the counter number.</div>
                    </div>
                    
                    <div class="col-md-5">
                        <label for="add_stat_label" class="form-label small fw-bold">Label Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm rounded-2" id="add_stat_label" name="label" required placeholder="e.g. Happy Users, Specialists">
                        <div class="invalid-feedback">Please enter the label title.</div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="add_stat_order" class="form-label small fw-bold">Sort Order</label>
                        <input type="number" class="form-control form-control-sm rounded-2" id="add_stat_order" name="sort_order" value="0">
                    </div>
                    
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                            <i class="fa-solid fa-plus me-1"></i> Add Counter Row
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- 3. NAVBAR MENU -->
    <div class="tab-pane fade <?php echo ($active_tab === 'navbar-menu') ? 'show active' : ''; ?>" id="navbar-menu" role="tabpanel" aria-labelledby="navbar-tab">
        <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2">Navbar Navigation Layout</h5>
        
        <!-- List / Edit Navbar Form -->
        <form action="" method="POST" class="needs-validation mb-5" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="section" value="navbar_list">
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle small">
                    <thead class="bg-light">
                        <tr class="fw-bold">
                            <th>Menu Label Title</th>
                            <th>Page Link / URL Destination</th>
                            <th style="width: 200px;">Parent Menu (For Dropdowns)</th>
                            <th style="width: 100px;">Sort Order</th>
                            <th style="width: 100px;" class="text-center">Status</th>
                            <th style="width: 80px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($nav_items)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No navbar menu items configured. Add one below.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($nav_items as $nav): ?>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm rounded-2 fw-semibold" name="navs[<?php echo $nav['id']; ?>][label]" required value="<?php echo htmlspecialchars($nav['label']); ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm rounded-2 font-code" name="navs[<?php echo $nav['id']; ?>][url]" required value="<?php echo htmlspecialchars($nav['url']); ?>">
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm rounded-2" name="navs[<?php echo $nav['id']; ?>][parent_id]">
                                            <option value="">None (Top Level)</option>
                                            <?php foreach ($parent_navs as $pn): ?>
                                                <?php if ($pn['id'] != $nav['id']): ?>
                                                    <option value="<?php echo $pn['id']; ?>" <?php echo ($pn['id'] == $nav['parent_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($pn['label']); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm rounded-2" name="navs[<?php echo $nav['id']; ?>][sort_order]" required value="<?php echo htmlspecialchars($nav['sort_order']); ?>">
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" name="navs[<?php echo $nav['id']; ?>][is_active]" value="1" <?php echo ($nav['is_active'] == 1) ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="preferences.php?action=delete_nav&id=<?php echo $nav['id']; ?>&csrf_token=<?php echo $csrf; ?>" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="return confirm('Are you sure you want to delete this navbar menu item? Parent items will set child items to Top Level.');">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($nav_items)): ?>
                <button type="submit" class="btn btn-success rounded-pill px-4 mt-2">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Menu Changes
                </button>
            <?php endif; ?>
        </form>
        
        <!-- Add Navbar Item Box -->
        <div class="card border rounded-3 p-4 bg-light">
            <h6 class="text-dark fw-bold mb-3"><i class="fa-solid fa-square-plus text-primary me-2"></i>Add New Navbar Link</h6>
            <form action="" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="section" value="add_nav">
                
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="add_nav_label" class="form-label small fw-bold">Menu Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm rounded-2" id="add_nav_label" name="label" required placeholder="e.g. Services, About">
                        <div class="invalid-feedback">Please enter the label name.</div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="add_nav_url" class="form-label small fw-bold">Page Link / URL <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm rounded-2 font-code" id="add_nav_url" name="url" required placeholder="e.g. services.php, #about">
                        <div class="invalid-feedback">Please enter the URL destination.</div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="add_nav_parent" class="form-label small fw-bold">Parent Category</label>
                        <select class="form-select form-select-sm rounded-2" id="add_nav_parent" name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($parent_navs as $pn): ?>
                                <option value="<?php echo $pn['id']; ?>"><?php echo htmlspecialchars($pn['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="add_nav_order" class="form-label small fw-bold">Sort Order</label>
                        <input type="number" class="form-control form-control-sm rounded-2" id="add_nav_order" name="sort_order" value="0">
                    </div>
                    
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                            <i class="fa-solid fa-plus me-1"></i> Add Navbar Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- 4. SOCIAL MEDIA LINKS -->
    <div class="tab-pane fade <?php echo ($active_tab === 'social') ? 'show active' : ''; ?>" id="social" role="tabpanel" aria-labelledby="social-tab">
        <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2">Social Media Portals</h5>
        <form action="" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="section" value="social">
            
            <div class="row g-3">
                <!-- Facebook -->
                <div class="col-md-8">
                    <label for="facebook_url" class="form-label small fw-bold"><i class="fa-brands fa-facebook text-primary me-2"></i>Facebook URL</label>
                    <input type="url" class="form-control rounded-2" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>" placeholder="https://facebook.com/yourpage">
                </div>
                <div class="col-md-4 d-flex align-items-end mb-1">
                    <div class="form-check form-switch p-3 border rounded-3 bg-light w-100">
                        <input class="form-check-input ms-0 me-3" type="checkbox" id="show_facebook" name="show_facebook" value="1" <?php echo (($settings['show_facebook'] ?? '0') === '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label small fw-bold text-dark" for="show_facebook">Display in Headers & Footers</label>
                    </div>
                </div>
                
                <!-- Instagram -->
                <div class="col-md-8">
                    <label for="instagram_url" class="form-label small fw-bold"><i class="fa-brands fa-instagram text-danger me-2"></i>Instagram URL</label>
                    <input type="url" class="form-control rounded-2" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/yourhandle">
                </div>
                <div class="col-md-4 d-flex align-items-end mb-1">
                    <div class="form-check form-switch p-3 border rounded-3 bg-light w-100">
                        <input class="form-check-input ms-0 me-3" type="checkbox" id="show_instagram" name="show_instagram" value="1" <?php echo (($settings['show_instagram'] ?? '0') === '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label small fw-bold text-dark" for="show_instagram">Display in Headers & Footers</label>
                    </div>
                </div>
                
                <!-- Twitter -->
                <div class="col-md-8">
                    <label for="twitter_url" class="form-label small fw-bold"><i class="fa-brands fa-x-twitter text-dark me-2"></i>Twitter / X URL</label>
                    <input type="url" class="form-control rounded-2" id="twitter_url" name="twitter_url" value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>" placeholder="https://twitter.com/yourhandle">
                </div>
                <div class="col-md-4 d-flex align-items-end mb-1">
                    <div class="form-check form-switch p-3 border rounded-3 bg-light w-100">
                        <input class="form-check-input ms-0 me-3" type="checkbox" id="show_twitter" name="show_twitter" value="1" <?php echo (($settings['show_twitter'] ?? '0') === '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label small fw-bold text-dark" for="show_twitter">Display in Headers & Footers</label>
                    </div>
                </div>
                
                <!-- Youtube -->
                <div class="col-md-8">
                    <label for="youtube_url" class="form-label small fw-bold"><i class="fa-brands fa-youtube text-danger me-2"></i>YouTube Channel URL</label>
                    <input type="url" class="form-control rounded-2" id="youtube_url" name="youtube_url" value="<?php echo htmlspecialchars($settings['youtube_url'] ?? ''); ?>" placeholder="https://youtube.com/c/yourchannel">
                </div>
                <div class="col-md-4 d-flex align-items-end mb-1">
                    <div class="form-check form-switch p-3 border rounded-3 bg-light w-100">
                        <input class="form-check-input ms-0 me-3" type="checkbox" id="show_youtube" name="show_youtube" value="1" <?php echo (($settings['show_youtube'] ?? '0') === '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label small fw-bold text-dark" for="show_youtube">Display in Headers & Footers</label>
                    </div>
                </div>
                
                <!-- Linkedin -->
                <div class="col-md-8">
                    <label for="linkedin_url" class="form-label small fw-bold"><i class="fa-brands fa-linkedin text-primary me-2"></i>LinkedIn Company URL</label>
                    <input type="url" class="form-control rounded-2" id="linkedin_url" name="linkedin_url" value="<?php echo htmlspecialchars($settings['linkedin_url'] ?? ''); ?>" placeholder="https://linkedin.com/company/yourpage">
                </div>
                <div class="col-md-4 d-flex align-items-end mb-1">
                    <div class="form-check form-switch p-3 border rounded-3 bg-light w-100">
                        <input class="form-check-input ms-0 me-3" type="checkbox" id="show_linkedin" name="show_linkedin" value="1" <?php echo (($settings['show_linkedin'] ?? '0') === '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label small fw-bold text-dark" for="show_linkedin">Display in Headers & Footers</label>
                    </div>
                </div>
                
                <div class="col-12 mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-success rounded-pill px-5">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Social Portals
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- 5. CONTACT INFO -->
    <div class="tab-pane fade <?php echo ($active_tab === 'contact') ? 'show active' : ''; ?>" id="contact" role="tabpanel" aria-labelledby="contact-tab">
        <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2">Public Contact Information</h5>
        <form action="" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="section" value="contact">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="contact_phone" class="form-label small fw-bold">Support Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-2" id="contact_phone" name="contact_phone" required value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>" placeholder="+1 (555) 000-0000">
                    <div class="invalid-feedback">Please enter a phone number.</div>
                </div>
                
                <div class="col-md-6">
                    <label for="contact_email" class="form-label small fw-bold">Support Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control rounded-2" id="contact_email" name="contact_email" required value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" placeholder="support@lurnixehealth.com">
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                
                <div class="col-md-6">
                    <label for="contact_whatsapp" class="form-label small fw-bold">WhatsApp Business Number (Numeric only with country code)</label>
                    <input type="text" class="form-control rounded-2" id="contact_whatsapp" name="contact_whatsapp" value="<?php echo htmlspecialchars($settings['contact_whatsapp'] ?? ''); ?>" placeholder="e.g. 15551234567">
                    <small class="text-muted">No spaces, plus signs, or hyphens (used for dynamic chat routing).</small>
                </div>
                
                <div class="col-md-6">
                    <label for="contact_address" class="form-label small fw-bold">Office Headquarters Address <span class="text-danger">*</span></label>
                    <textarea class="form-control rounded-2" id="contact_address" name="contact_address" required rows="2" placeholder="Full street address, city, state and zip..."><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></textarea>
                    <div class="invalid-feedback">Please enter the headquarters address.</div>
                </div>
                
                <div class="col-12 mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-success rounded-pill px-5">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Contact Configurations
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- 6. FOOTER -->
    <div class="tab-pane fade <?php echo ($active_tab === 'footer') ? 'show active' : ''; ?>" id="footer" role="tabpanel" aria-labelledby="footer-tab">
        <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2">Footer layout configurations</h5>
        <form action="" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="section" value="footer">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="footer_tagline" class="form-label small fw-bold">Footer Brand Sub-tagline</label>
                        <input type="text" class="form-control rounded-2" id="footer_tagline" name="footer_tagline" value="<?php echo htmlspecialchars($settings['footer_tagline'] ?? ''); ?>" placeholder="e.g. Providing quality healthcare solutions.">
                    </div>
                    
                    <div class="mb-3">
                        <label for="footer_copyright" class="form-label small fw-bold">Copyright String</label>
                        <input type="text" class="form-control rounded-2" id="footer_copyright" name="footer_copyright" value="<?php echo htmlspecialchars($settings['footer_copyright'] ?? ''); ?>" placeholder="e.g. © 2026 LurnixeHealth. All rights reserved.">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="footer_description" class="form-label small fw-bold">Footer Overview / Short Bio</label>
                        <textarea class="form-control rounded-2" id="footer_description" name="footer_description" rows="4" placeholder="Brief paragraph about company goals, certifications or licensing..."><?php echo htmlspecialchars($settings['footer_description'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div class="col-12 mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-success rounded-pill px-5">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Footer Layouts
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- 7. SEO SETTINGS -->
    <div class="tab-pane fade <?php echo ($active_tab === 'seo') ? 'show active' : ''; ?>" id="seo" role="tabpanel" aria-labelledby="seo-tab">
        <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2">Global Search Engine Optimization (SEO) Metadata</h5>
        <form action="" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="section" value="seo">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="meta_title" class="form-label small fw-bold">Default SEO Meta Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-2" id="meta_title" name="meta_title" required value="<?php echo htmlspecialchars($settings['meta_title'] ?? ''); ?>" placeholder="e.g. LurnixeHealth - Secure Family Portal">
                        <div class="invalid-feedback">Please enter the Meta Title.</div>
                        <small class="text-muted">Recommended length: 50-60 characters.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_keywords" class="form-label small fw-bold">Global SEO Meta Keywords</label>
                        <textarea class="form-control rounded-2" id="meta_keywords" name="meta_keywords" rows="3" placeholder="Comma separated terms (e.g. health card, medical records, doctors)..."><?php echo htmlspecialchars($settings['meta_keywords'] ?? ''); ?></textarea>
                        <small class="text-muted">Provide comma-separated keywords.</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="meta_description" class="form-label small fw-bold">Default SEO Meta Description <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-2" id="meta_description" name="meta_description" required rows="6" placeholder="Provide a summary sentence to appear in search engine listings..."><?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?></textarea>
                        <div class="invalid-feedback">Please enter the Meta Description.</div>
                        <small class="text-muted">Recommended length: 150-160 characters.</small>
                    </div>
                </div>
                
                <div class="col-12 mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-success rounded-pill px-5">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Metadata Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
    
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
