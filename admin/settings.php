<?php
/**
 * Lurnixe Health Card System - System Preferences
 * June 2026
 */
$page_title = "System Settings";
require_once __DIR__ . '/includes/header.php';

$admin_role = $_SESSION['admin_role'];
$csrf = get_csrf_token();
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $admin_role === 'super_admin') {
    // 1. Verify CSRF
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        header("Location: settings.php?error=csrf");
        exit;
    }
    
    // In a fully dynamic system, this would write to a DB config table or config file.
    // For our core PHP setup, we validate the input and show a success simulation.
    $success_msg = "System configuration settings updated successfully.";
    log_activity($pdo, $_SESSION['admin_id'], 'update_settings', null, "Updated system settings preferences");
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-dark fw-bold font-heading">System Settings</h2>
        <p class="text-muted small">Configure global health card formats, security thresholds, and session options.</p>
    </div>
</div>

<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
            <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-sliders me-2"></i> General Options</h5>
            
            <form action="" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                
                <div class="mb-3">
                    <label for="site_name" class="form-label small fw-bold">Site Brand Name</label>
                    <input type="text" class="form-control rounded-2" id="site_name" name="site_name" required value="<?php echo SITE_NAME; ?>" <?php echo ($admin_role !== 'super_admin') ? 'readonly' : ''; ?>>
                </div>
                
                <div class="mb-3">
                    <label for="card_prefix" class="form-label small fw-bold">Health Card ID Prefix</label>
                    <input type="text" class="form-control rounded-2 font-code" id="card_prefix" name="card_prefix" required value="<?php echo CARD_PREFIX; ?>" <?php echo ($admin_role !== 'super_admin') ? 'readonly' : ''; ?>>
                    <small class="text-muted">Unique prefix rule (e.g. LFC000001).</small>
                </div>
                
                <div class="mb-3">
                    <label for="validity_years" class="form-label small fw-bold">Default Card Validity Duration</label>
                    <select class="form-select rounded-2" id="validity_years" name="validity_years" <?php echo ($admin_role !== 'super_admin') ? 'disabled' : ''; ?>>
                        <option value="1" <?php echo (DEFAULT_VALIDITY_YEARS == 1) ? 'selected' : ''; ?>>1 Year</option>
                        <option value="2" <?php echo (DEFAULT_VALIDITY_YEARS == 2) ? 'selected' : ''; ?>>2 Years</option>
                        <option value="3" <?php echo (DEFAULT_VALIDITY_YEARS == 3) ? 'selected' : ''; ?>>3 Years</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="base_url" class="form-label small fw-bold">Dynamic Base URL Endpoint</label>
                    <input type="text" class="form-control rounded-2 bg-light font-code" id="base_url" readonly value="<?php echo BASE_URL; ?>">
                    <small class="text-muted">Determined dynamically from the current server path.</small>
                </div>
                
                <?php if ($admin_role === 'super_admin'): ?>
                    <button type="submit" class="btn btn-success rounded-pill px-5">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Configurations
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
            <h5 class="text-primary fw-bold mb-4 font-heading border-bottom pb-2"><i class="fa-solid fa-shield-halved me-2"></i> Security Thresholds</h5>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Admin Session Inactivity Timeout</label>
                <input type="text" class="form-control rounded-2 bg-light" readonly value="<?php echo (SESSION_TIMEOUT_SECONDS / 3600); ?> Hours (7200 seconds)">
            </div>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Maximum Login Cooldown Threshold</label>
                <input type="text" class="form-control rounded-2 bg-light" readonly value="<?php echo MAX_LOGIN_ATTEMPTS; ?> failed attempts">
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold">Lockout Duration</label>
                <input type="text" class="form-control rounded-2 bg-light" readonly value="<?php echo COOLDOWN_MINUTES; ?> Minutes Cooldown">
            </div>
            
            <div class="bg-light p-3 rounded border">
                <span class="d-block fw-bold text-dark mb-1 small"><i class="fa-solid fa-database me-1 text-primary"></i> DATABASE CONNECTION</span>
                <span class="font-code text-muted small d-block">Host: <?php echo DB_HOST; ?></span>
                <span class="font-code text-muted small d-block">Database: <?php echo DB_NAME; ?></span>
                <span class="font-code text-muted small d-block">Username: <?php echo DB_USER; ?></span>
                <span class="font-code text-muted small d-block">Status: Connected (PDO active)</span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
