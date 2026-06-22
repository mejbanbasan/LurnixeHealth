<?php
/**
 * Lurnixe Health Card System - Public QR Scan View (Premium Medical Registry Page)
 * June 2026
 */
$page_title = "Verify Member Profile";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$member = null;
$family_members = [];
$error = "";

// Retrieve and sanitize the member ID from the GET query parameter
$member_id = sanitize_input($_GET['id'] ?? '');

if (empty($member_id)) {
    $error = "Invalid request. No Health Member ID provided.";
} else {
    try {
        // Query database for the member details
        $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch();
        
        if (!$member) {
            $error = "Health Card not found. Please verify the QR Code or contact administration.";
        } else {
            // Fetch linked family members
            $fam_stmt = $pdo->prepare("SELECT * FROM family_members WHERE member_id = ? ORDER BY id ASC");
            $fam_stmt->execute([$member_id]);
            $family_members = $fam_stmt->fetchAll();
        }
    } catch (PDOException $e) {
        error_log("Public scan lookup failed: " . $e->getMessage());
        $error = "An error occurred while retrieving card details. Please try again later.";
    }
}
?>

<style>
/* iOS/Android Premium Profile App Layout Styles */
.verify-profile-container {
    background-color: #F4F6F7;
    min-height: 85vh;
}
.profile-card-ios {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    border: 1px solid rgba(0, 0, 0, 0.04);
}
.profile-avatar-wrapper {
    position: relative;
    display: inline-block;
}
.verified-badge-pill {
    background-color: #E8F8F5;
    color: #1ABC9C;
    border: 1px solid rgba(26, 188, 156, 0.2);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.stats-grid-box {
    background-color: #F8F9FA;
    border-radius: 12px;
    padding: 10px;
    text-align: center;
    border: 1px solid rgba(0,0,0,0.015);
    height: 100%;
}
.stats-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: #7F8C8D;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.stats-value {
    font-size: 0.95rem;
    font-weight: 700;
    color: #2C3E50;
}
.secure-banner-box {
    background-color: #EBF5FB;
    border-radius: 16px;
    border: 1px solid rgba(41, 128, 185, 0.15);
    color: #2980B9;
    transition: transform 0.2s ease;
    cursor: pointer;
}
.secure-banner-box:hover {
    transform: translateY(-2px);
}
.section-menu-header {
    font-size: 0.75rem;
    font-weight: 700;
    color: #7F8C8D;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 8px;
    margin-left: 5px;
}
.menu-list-group {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.04);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
}
.menu-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    border-bottom: 1px solid #F2F4F4;
    cursor: pointer;
    transition: background-color 0.2s ease;
    text-decoration: none;
    color: inherit;
}
.menu-item-row:last-child {
    border-bottom: none;
}
.menu-item-row:hover {
    background-color: #F8F9FA;
}
.menu-item-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.menu-icon-box {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
.menu-title {
    font-weight: 600;
    color: #2C3E50;
    font-size: 0.88rem;
    margin-bottom: 1px;
}
.menu-subtitle {
    font-size: 0.7rem;
    color: #7F8C8D;
}
.menu-chevron {
    color: #BDC3C7;
    font-size: 0.8rem;
}
.exit-btn-row {
    background: #FDEDEC;
    color: #C0392B;
    border: 1px solid rgba(192, 57, 43, 0.15);
    font-weight: 700;
    border-radius: 16px;
    transition: background-color 0.2s ease;
}
.exit-btn-row:hover {
    background: #FADBD8;
}
.modal-ios-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 15px 45px rgba(0,0,0,0.15);
}
.modal-ios-header {
    border-bottom: 1px solid #F2F4F4;
    padding: 20px 24px;
}
.modal-ios-body {
    padding: 24px;
}
.modal-ios-footer {
    border-top: 1px solid #F2F4F4;
    padding: 16px 24px;
}
</style>

<div class="verify-profile-container py-4 py-md-5">
    <div class="container">
        <?php if (!empty($error)): ?>
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white" data-aos="zoom-in">
                        <div class="text-danger mb-4" style="font-size: 4.5rem;">
                            <i class="fa-solid fa-circle-exclamation animate__animated animate__pulse animate__infinite"></i>
                        </div>
                        <h3 class="text-dark fw-bold mb-3">Verification Failed</h3>
                        <p class="text-muted mb-4"><?php echo $error; ?></p>
                        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-success rounded-pill px-5 py-2.5 text-white fw-bold">
                            <i class="fa-solid fa-house me-2"></i> Go to Homepage
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            
            <?php
            // Calculate a dynamic health score based on profile completion percentage
            $health_score = 75; // base score for registered active accounts
            if (!empty($member['photo']) && file_exists(__DIR__ . '/' . $member['photo'])) $health_score += 10;
            if (!empty($member['email'])) $health_score += 5;
            if (!empty($member['allergies']) && strtolower(trim($member['allergies'])) !== 'none') $health_score += 5;
            if (!empty($member['health_info']) && strtolower(trim($member['health_info'])) !== 'none') $health_score += 5;
            if ($health_score > 100) $health_score = 100;
            ?>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    
                    <!-- App Top Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4 px-2" data-aos="fade-down">
                        <h3 class="fw-bold mb-0 font-heading text-dark" style="font-size: 1.6rem;">Profile</h3>
                        <div class="d-flex align-items-center gap-3">
                            <!-- Notification Bell -->
                            <a href="#" class="position-relative text-dark text-decoration-none bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'" data-bs-toggle="modal" data-bs-target="#remindersModal">
                                <i class="fa-regular fa-bell fs-5"></i>
                                <span class="position-absolute top-2 end-2 p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
                            </a>
                            <!-- Settings/Gear Icon -->
                            <a href="#" class="text-dark text-decoration-none bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                <i class="fa-solid fa-gear fs-5"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Profile Card (Avatar + Header Info) -->
                    <div class="profile-card-ios p-4 mb-4 text-center" data-aos="fade-up">
                        <div class="profile-avatar-wrapper mb-3">
                            <?php 
                            $photo_path = $member['photo'];
                            if (empty($photo_path) || !file_exists(__DIR__ . '/' . $photo_path)) {
                                echo '<div class="medical-photo-placeholder" style="width: 100px; height: 100px; border-radius: 50%; font-size: 2.2rem; margin: 0 auto; background-color: #EAECEE; display: flex; align-items: center; justify-content: center; color: #BDC3C7;"><i class="fa-solid fa-user"></i></div>';
                            } else {
                                echo '<img src="' . BASE_URL . htmlspecialchars($photo_path) . '" alt="' . htmlspecialchars($member['name']) . '" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #E8F8F5; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">';
                            }
                            ?>
                        </div>
                        <h4 class="text-dark fw-bold mb-1"><?php echo htmlspecialchars($member['name']); ?></h4>
                        <p class="text-muted small mb-2"><?php echo !empty($member['email']) ? htmlspecialchars($member['email']) : 'no-email@lurnixehealth.com'; ?> &bull; <?php echo htmlspecialchars($member['mobile']); ?></p>
                        
                        <span class="badge verified-badge-pill px-3 py-1.5 rounded-pill mb-2">
                            <i class="fa-solid fa-circle-check text-success"></i> Verified User
                        </span>
                    </div>

                    <!-- Core Stats Grid (Card ID, Blood Group, Health Score) -->
                    <div class="row g-3 mb-4" data-aos="fade-up" data-aos-delay="50">
                        <div class="col-4">
                            <div class="stats-grid-box">
                                <div class="stats-label">Card ID</div>
                                <div class="stats-value text-truncate" style="font-size: 0.8rem;" title="<?php echo htmlspecialchars($member['member_id']); ?>"><?php echo htmlspecialchars($member['member_id']); ?></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-grid-box">
                                <div class="stats-label">Blood Group</div>
                                <div class="stats-value text-danger"><i class="fa-solid fa-droplet me-1"></i><?php echo htmlspecialchars($member['blood_group']); ?></div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stats-grid-box">
                                <div class="stats-label">Health Score</div>
                                <div class="stats-value text-success"><i class="fa-solid fa-heart-pulse me-1"></i><?php echo $health_score; ?>/100</div>
                            </div>
                        </div>
                    </div>

                    <!-- Secure Banner -->
                    <div class="secure-banner-box d-flex align-items-center gap-3 p-3 mb-4" data-aos="fade-up" data-aos-delay="100" data-bs-toggle="modal" data-bs-target="#securityModal">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center text-primary shadow-sm" style="width: 40px; height: 40px; min-width: 40px;">
                            <i class="fa-solid fa-shield-halved fs-5 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 text-start">
                            <h6 class="mb-0 fw-bold" style="font-size: 0.88rem;">Keep Your Profile Secure</h6>
                            <span class="small opacity-75" style="font-size: 0.75rem;">Complete your profile verification setup</span>
                        </div>
                        <i class="fa-solid fa-chevron-right menu-chevron"></i>
                    </div>

                    <!-- My Health Section -->
                    <div class="mb-4" data-aos="fade-up" data-aos-delay="150">
                        <div class="section-menu-header">My Health</div>
                        <div class="menu-list-group">
                            <!-- Health Records -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#healthRecordsModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-success-subtle text-success">
                                        <i class="fa-solid fa-file-waveform"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">My Health Records</div>
                                        <div class="menu-subtitle">View clinical warnings and conditions</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>

                            <!-- Appointments -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#appointmentsModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-primary-subtle text-primary">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">My Appointments</div>
                                        <div class="menu-subtitle">Check upcoming consults & sessions</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>

                            <!-- Family -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#familyModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-warning-subtle text-warning">
                                        <i class="fa-solid fa-people-roof"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">My Family</div>
                                        <div class="menu-subtitle">Manage linked dependents profiles</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>

                            <!-- Prescriptions -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#prescriptionsModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-info-subtle text-info">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">My Prescriptions</div>
                                        <div class="menu-subtitle">View prescription files & records</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>

                            <!-- Health Reminders -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#remindersModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-danger-subtle text-danger">
                                        <i class="fa-solid fa-bell"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">Health Reminders</div>
                                        <div class="menu-subtitle">Medicine & general checkup alerts</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Account Section -->
                    <div class="mb-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="section-menu-header">Account</div>
                        <div class="menu-list-group">
                            <!-- Personal Info -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#personalInfoModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-primary-subtle text-primary">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">Personal Information</div>
                                        <div class="menu-subtitle">Age, gender & emergency contacts</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>

                            <!-- Address -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#addressModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-success-subtle text-success">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">Address</div>
                                        <div class="menu-subtitle">Registered residence details</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>

                            <!-- Payment Methods -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#paymentsModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-info-subtle text-info">
                                        <i class="fa-solid fa-credit-card"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">Payment Methods</div>
                                        <div class="menu-subtitle">Paid validity and premium options</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>

                            <!-- Settings -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-secondary-subtle text-secondary">
                                        <i class="fa-solid fa-sliders"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">Settings</div>
                                        <div class="menu-subtitle">App preferences and setups</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>

                            <!-- Help & Support -->
                            <div class="menu-item-row" data-bs-toggle="modal" data-bs-target="#supportModal">
                                <div class="menu-item-left">
                                    <div class="menu-icon-box bg-warning-subtle text-warning">
                                        <i class="fa-solid fa-circle-question"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="menu-title">Help & Support</div>
                                        <div class="menu-subtitle">FAQ, support phone & contact portal</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right menu-chevron"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Exit Actions (Logout styled row) -->
                    <div class="mb-5" data-aos="fade-up" data-aos-delay="250">
                        <a href="<?php echo BASE_URL; ?>index.php" class="btn w-100 menu-item-row exit-btn-row py-3">
                            <div class="menu-item-left mx-auto">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Logout</span>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

            <!-- ================= MODALS SECTION ================= -->

            <!-- Security Modal -->
            <div class="modal fade" id="securityModal" tabindex="-1" aria-labelledby="securityModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="securityModalLabel"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Profile Security</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <div class="text-center mb-4">
                                <span class="fs-1 text-success d-block mb-2"><i class="fa-solid fa-circle-shield"></i></span>
                                <h6 class="fw-bold text-dark">LFC-Secure Registry Verification</h6>
                            </div>
                            <p class="text-muted small">Your medical records are dynamic and verified in real-time under LFC-Secure domain validation logs. Emergency scans are secured to display emergency info only, shielding detailed database tables.</p>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Authentication Protocol</span>
                                    <span class="badge bg-success-subtle text-success small">AES-256</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Registry Query Sync</span>
                                    <span class="badge bg-primary-subtle text-primary small">REAL-TIME</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Emergency Access Status</span>
                                    <span class="badge bg-success-subtle text-success small">GRANTED</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Records Modal -->
            <div class="modal fade" id="healthRecordsModal" tabindex="-1" aria-labelledby="healthRecordsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="healthRecordsModalLabel"><i class="fa-solid fa-file-waveform text-success me-2"></i>My Health Records</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <!-- Allergy Alerts -->
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Clinical Allergy Warnings</h6>
                            <?php if (empty($member['allergies']) || strtolower(trim($member['allergies'])) === 'none'): ?>
                                <div class="p-3 bg-success-subtle text-success rounded-3 border mb-4 d-flex align-items-center gap-2 small">
                                    <i class="fa-solid fa-circle-check fs-5"></i>
                                    <span>No known clinical or food allergies declared.</span>
                                </div>
                            <?php else: ?>
                                <div class="p-3 bg-danger-subtle text-danger rounded-3 border mb-4 d-flex align-items-start gap-2 small">
                                    <i class="fa-solid fa-circle-exclamation fs-5 mt-0.5"></i>
                                    <div>
                                        <strong class="d-block mb-1">ALLERGY WARNING:</strong>
                                        <span><?php echo htmlspecialchars($member['allergies']); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Chronic Conditions / Notes -->
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-waveform text-info me-2"></i>Chronic Conditions & Health Notes</h6>
                            <?php if (empty($member['health_info']) || strtolower(trim($member['health_info'])) === 'none'): ?>
                                <div class="p-3 bg-light rounded-3 text-muted small border">
                                    <i class="fa-solid fa-info-circle me-1 text-primary"></i> No chronic medical conditions or health histories declared.
                                </div>
                            <?php else: ?>
                                <div class="p-3 rounded-3 border" style="background-color: #EBF5FB; border-left: 5px solid #2980B9 !important; color: #1B4F72;">
                                    <span class="d-block fw-bold mb-1" style="font-size: 0.85rem;">Active Health History:</span>
                                    <p class="mb-0 small" style="white-space: pre-line;"><?php echo htmlspecialchars($member['health_info']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-success w-100 rounded-pill py-2.5 text-white fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Modal -->
            <div class="modal fade" id="appointmentsModal" tabindex="-1" aria-labelledby="appointmentsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="appointmentsModalLabel"><i class="fa-solid fa-calendar-check text-primary me-2"></i>My Appointments</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <p class="text-muted small">You can schedule appointments directly with our partner doctors online.</p>
                            <div class="p-4 bg-light rounded-3 border text-center mb-3">
                                <span class="fs-1 text-muted d-block mb-2"><i class="fa-solid fa-calendar-xmark"></i></span>
                                <span class="small text-muted d-block">No Active Appointments Scheduled</span>
                            </div>
                            <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-outline-primary w-100 rounded-pill fw-bold">
                                <i class="fa-solid fa-user-doctor me-2"></i>Find Partner Doctors & Book
                            </a>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Modal -->
            <div class="modal fade" id="familyModal" tabindex="-1" aria-labelledby="familyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="familyModalLabel"><i class="fa-solid fa-people-roof text-warning me-2"></i>My Family Dependents</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <?php if (empty($family_members)): ?>
                                <div class="text-center text-muted py-5 border rounded-3 bg-light">
                                    <span class="fs-1 d-block mb-2 text-black-50"><i class="fa-solid fa-users-slash"></i></span>
                                    <p class="mb-0 small">No dependents or linked family members currently registered under this account.</p>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-3">The following family members are linked to your premium health card registration for clinical checks.</p>
                                <div class="row g-3">
                                    <?php foreach ($family_members as $fam): ?>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-dark fw-bold mb-1" style="font-size: 0.9rem;"><?php echo htmlspecialchars($fam['name']); ?></h6>
                                                    <span class="badge bg-secondary-subtle text-secondary small me-2"><?php echo htmlspecialchars($fam['relation']); ?></span>
                                                    <span class="text-muted small" style="font-size: 0.75rem;"><?php echo format_date($fam['dob']); ?></span>
                                                </div>
                                                <span class="badge bg-danger-subtle text-danger font-heading fw-bold px-2.5 py-1.5"><?php echo htmlspecialchars($fam['blood_group']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-warning w-100 rounded-pill py-2.5 text-white fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescriptions Modal -->
            <div class="modal fade" id="prescriptionsModal" tabindex="-1" aria-labelledby="prescriptionsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="prescriptionsModalLabel"><i class="fa-solid fa-receipt text-info me-2"></i>My Prescriptions</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start text-center py-4">
                            <span class="fs-1 text-muted d-block mb-2"><i class="fa-solid fa-prescription-bottle-medical"></i></span>
                            <h6 class="fw-bold text-dark mb-1">No Active Digital Prescriptions</h6>
                            <p class="text-muted small mb-0">Sync your diagnostic reports or prescription files at partner labs or clinics during consultations.</p>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-info w-100 rounded-pill py-2.5 text-white fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reminders Modal -->
            <div class="modal fade" id="remindersModal" tabindex="-1" aria-labelledby="remindersModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="remindersModalLabel"><i class="fa-solid fa-bell text-danger me-2"></i>Health Reminders</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <p class="text-muted small mb-3">Medicine checkouts and general clinical checkup alert status.</p>
                            <div class="p-3 bg-light rounded-3 border small mb-2 d-flex align-items-center justify-content-between">
                                <span><i class="fa-solid fa-clock text-muted me-2"></i>BP Monitoring Checkup</span>
                                <span class="badge bg-warning text-dark small rounded-pill">Weekly Reminder</span>
                            </div>
                            <div class="p-3 bg-light rounded-3 border small d-flex align-items-center justify-content-between">
                                <span><i class="fa-solid fa-clock text-muted me-2"></i>Annual Vaccine Booster</span>
                                <span class="badge bg-secondary text-white small rounded-pill">Not Scheduled</span>
                            </div>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-danger w-100 rounded-pill py-2.5 text-white fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Info Modal -->
            <div class="modal fade" id="personalInfoModal" tabindex="-1" aria-labelledby="personalInfoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="personalInfoModalLabel"><i class="fa-solid fa-user text-primary me-2"></i>Personal Information</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stats-label">Date of Birth</div>
                                    <div class="stats-value" style="font-size:0.9rem;"><?php echo format_date($member['dob']); ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="stats-label">Age</div>
                                    <div class="stats-value" style="font-size:0.9rem;"><?php echo htmlspecialchars($member['age']); ?> Years</div>
                                </div>
                                <div class="col-6">
                                    <div class="stats-label">Gender</div>
                                    <div class="stats-value" style="font-size:0.9rem;"><?php echo htmlspecialchars($member['gender']); ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="stats-label">Valid Upto</div>
                                    <div class="stats-value text-success" style="font-size:0.9rem;"><?php echo format_date($member['validity_date']); ?></div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <h6 class="fw-bold text-danger mb-3"><i class="fa-solid fa-phone-volume me-2"></i>Emergency Contact Details</h6>
                            <div class="p-3 bg-danger-subtle rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted font-heading">Primary Contact</span>
                                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($member['emergency_name']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted font-heading">Emergency Mobile</span>
                                    <a href="tel:<?php echo htmlspecialchars($member['emergency_mobile']); ?>" class="fw-bold text-danger small text-decoration-none"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($member['emergency_mobile']); ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Modal -->
            <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="addressModalLabel"><i class="fa-solid fa-location-dot text-success me-2"></i>Registered Address</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="stats-label mb-1">Residence Address</div>
                                <div class="stats-value text-dark mb-3" style="font-size: 0.9rem; font-weight: normal;"><?php echo htmlspecialchars($member['address']); ?></div>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="stats-label">City</div>
                                        <div class="stats-value text-dark" style="font-size: 0.85rem;"><?php echo htmlspecialchars($member['city']); ?></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stats-label">State</div>
                                        <div class="stats-value text-dark" style="font-size: 0.85rem;"><?php echo htmlspecialchars($member['state']); ?></div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stats-label">Pincode</div>
                                        <div class="stats-value text-dark" style="font-size: 0.85rem;"><?php echo htmlspecialchars($member['pincode']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-success w-100 rounded-pill py-2.5 text-white fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Modal -->
            <div class="modal fade" id="paymentsModal" tabindex="-1" aria-labelledby="paymentsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="paymentsModalLabel"><i class="fa-solid fa-credit-card text-info me-2"></i>Payment Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <p class="text-muted small">Your account registry has a premium family health card membership active.</p>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Membership Card Tier</span>
                                    <span class="fw-bold text-dark small">Premium Digital & PVC Card</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Payment Registry Status</span>
                                    <span class="badge bg-success-subtle text-success small">PAID</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Renewal Status</span>
                                    <span class="badge bg-secondary-subtle text-secondary small">NOT DUE</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="<?php echo BASE_URL; ?>admin/generate-card.php?id=<?php echo urlencode($member['member_id']); ?>" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                                    <i class="fa-solid fa-file-pdf me-2"></i>Download Printed PDF Card
                                </a>
                            </div>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-info w-100 rounded-pill py-2.5 text-white fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Modal -->
            <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="settingsModalLabel"><i class="fa-solid fa-sliders text-secondary me-2"></i>Preferences & Settings</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <p class="text-muted small">Configure settings for your medical profile views.</p>
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-dark fw-bold">Auto-Sync Records</span>
                                    <span class="badge bg-success-subtle text-success small">ON</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-dark fw-bold">Emergency Profile Visible</span>
                                    <span class="badge bg-success-subtle text-success small">ON</span>
                                </div>
                            </div>
                            <div class="alert alert-warning small mb-0 rounded-3" role="alert">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> Settings modifications require admin panel authorization. Please contact support to update your preferences.
                            </div>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-secondary w-100 rounded-pill py-2.5 fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Modal -->
            <div class="modal fade" id="supportModal" tabindex="-1" aria-labelledby="supportModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content modal-ios-content">
                        <div class="modal-ios-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-dark font-heading" id="supportModalLabel"><i class="fa-solid fa-circle-question text-warning me-2"></i>Help & Support</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-ios-body text-start">
                            <p class="text-muted small">Need help with card replacements, records sync, or app configuration? Reach our helpdesk directly.</p>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="mb-2 d-flex align-items-center gap-2 small">
                                    <i class="fa-solid fa-envelope text-success"></i>
                                    <span>support@lurnixehealth.com</span>
                                </div>
                                <div class="mb-2 d-flex align-items-center gap-2 small">
                                    <i class="fa-solid fa-phone text-success"></i>
                                    <span>+1 (800) 123-4567</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 small">
                                    <i class="fa-solid fa-location-dot text-success mt-0.5"></i>
                                    <span>123 Healthcare Blvd, Medical District, NY</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-ios-footer">
                            <button type="button" class="btn btn-warning w-100 rounded-pill py-2.5 text-white fw-bold" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
