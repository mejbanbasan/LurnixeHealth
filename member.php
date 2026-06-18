<?php
/**
 * Lurnixe Health Card System - Public QR Scan View
 * June 2026
 */
$page_title = "Verify Health Card";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$member = null;
$error = "";

// Retrieve and sanitize the member ID from the GET query parameter
$member_id = sanitize_input($_GET['id'] ?? '');

if (empty($member_id)) {
    $error = "Invalid request. No Health Card ID provided.";
} else {
    try {
        // Query database for the member details
        $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch();
        
        if (!$member) {
            $error = "Health Card not found. Please verify the QR Code or contact support.";
        }
    } catch (PDOException $e) {
        error_log("Public scan lookup failed: " . $e->getMessage());
        $error = "An error occurred while retrieving card details. Please try again later.";
    }
}
?>

<section class="py-5 bg-light border-bottom">
    <div class="container text-center py-2" data-aos="fade-up">
        <h1 class="text-dark fw-bold mb-1">Health Card Verification Portal</h1>
        <p class="text-muted small">Live validation query from Lurnixe Health secure database.</p>
    </div>
</section>

<section class="py-5">
    <div class="container" style="max-width: 550px;" data-aos="zoom-in">
        <?php if (!empty($error)): ?>
            <div class="card border-0 shadow-sm rounded-3 p-5 text-center">
                <div class="text-danger mb-4" style="font-size: 4rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-dark mb-3">Verification Failed</h3>
                <p class="text-muted mb-4"><?php echo $error; ?></p>
                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-house me-2"></i> Go to Homepage
                </a>
            </div>
        <?php else: ?>
            <!-- Card Verification Success Box -->
            <div class="qr-profile-card">
                <!-- Header Status Gradient -->
                <div class="qr-profile-header">
                    <span class="text-white-50 small font-heading fw-semibold mb-1 d-block" style="letter-spacing: 1px;">VERIFICATION STATUS</span>
                    
                    <?php 
                    $status = strtoupper($member['status']);
                    if ($status === 'ACTIVE') {
                        echo '<span class="badge badge-active px-4 py-2 rounded-pill fs-6"><i class="fa-solid fa-circle-check me-1"></i> ACTIVE & VERIFIED</span>';
                    } elseif ($status === 'EXPIRED') {
                        echo '<span class="badge badge-expired px-4 py-2 rounded-pill fs-6"><i class="fa-solid fa-clock-rotate-left me-1"></i> CARD EXPIRED</span>';
                    } elseif ($status === 'SUSPENDED') {
                        echo '<span class="badge badge-suspended px-4 py-2 rounded-pill fs-6 text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i> CARD SUSPENDED</span>';
                    } else {
                        echo '<span class="badge badge-deactivated px-4 py-2 rounded-pill fs-6"><i class="fa-solid fa-ban me-1"></i> DEACTIVATED</span>';
                    }
                    ?>
                </div>
                
                <div class="text-center p-4">
                    <!-- Photo container -->
                    <?php 
                    $photo_path = $member['photo'];
                    if (empty($photo_path) || !file_exists(__DIR__ . '/' . $photo_path)) {
                        $photo_src = BASE_URL . "assets/images/default_avatar.jpg";
                        // Fallback check
                        $photo_elem = '<div class="qr-profile-photo mx-auto d-flex align-items-center justify-content-center text-muted fs-1 border border-3 border-white"><i class="fa-solid fa-user"></i></div>';
                    } else {
                        $photo_src = BASE_URL . $photo_path;
                        $photo_elem = '<img src="' . $photo_src . '" alt="' . htmlspecialchars($member['name']) . '" class="qr-profile-photo mx-auto d-block">';
                    }
                    echo $photo_elem;
                    ?>
                    
                    <h3 class="text-dark fw-bold mt-3 mb-1"><?php echo htmlspecialchars($member['name']); ?></h3>
                    <p class="font-code text-muted mb-4 fs-6" style="letter-spacing: 1px;">ID: <?php echo htmlspecialchars($member['member_id']); ?></p>
                    
                    <hr class="border-secondary opacity-25">
                    
                    <!-- Medical details table (restricted view) -->
                    <div class="row text-start g-3 my-2 px-2">
                        <div class="col-6">
                            <span class="text-muted d-block small">GENDER</span>
                            <span class="text-dark fw-semibold"><?php echo htmlspecialchars($member['gender']); ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block small">BLOOD GROUP</span>
                            <span class="badge bg-light-blue text-primary px-3 py-1 fs-6 font-heading fw-bold mt-1"><?php echo htmlspecialchars($member['blood_group']); ?></span>
                        </div>
                        
                        <div class="col-6">
                            <span class="text-muted d-block small">AGE</span>
                            <span class="text-dark fw-semibold"><?php echo htmlspecialchars($member['age']); ?> Yrs</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block small">VALID UNTIL</span>
                            <span class="text-dark fw-semibold"><?php echo format_date($member['validity_date']); ?></span>
                        </div>
                    </div>
                    
                    <hr class="border-secondary opacity-25">
                    
                    <!-- Emergency Contact Details -->
                    <div class="bg-light p-3 rounded text-start mt-3 border">
                        <span class="text-danger fw-bold small d-block mb-2"><i class="fa-solid fa-circle-exclamation me-1"></i> EMERGENCY CONTACT</span>
                        <div class="row g-2">
                            <div class="col-12">
                                <span class="text-muted small">NAME: </span>
                                <span class="text-dark fw-semibold"><?php echo htmlspecialchars($member['emergency_name']); ?></span>
                            </div>
                            <div class="col-12">
                                <span class="text-muted small">PHONE: </span>
                                <span class="text-success fw-bold"><i class="fa-solid fa-phone me-1"></i> <?php echo htmlspecialchars($member['emergency_mobile']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Privacy Note -->
                    <p class="text-muted mt-4 mb-0" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-shield-halved me-1 text-success"></i> Privacy Mode Active. Personal address and clinical allergy details are hidden for security reasons.
                    </p>
                </div>
                
                <div class="card-footer bg-light text-center py-3 border-top">
                    <span class="text-muted small" style="font-size: 0.8rem;">Powered by <strong class="text-primary">Lurnixe Health</strong></span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
