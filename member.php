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
/* Premium Medical Registry Custom CSS */
.verify-profile-container {
    background-color: #F8F9FA;
}
.medical-profile-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.035);
    overflow: hidden;
    margin-bottom: 24px;
}
.medical-header-banner {
    background: linear-gradient(135deg, #1A5276 0%, #117864 100%);
    padding: 30px 24px;
    text-align: center;
    position: relative;
    border-bottom: 4px solid #27AE60;
}
.medical-photo-ring {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid #ffffff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    object-fit: cover;
    background-color: #EAECEE;
}
.medical-photo-placeholder {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 4px solid #ffffff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    background-color: #D5DBDB;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #7F8C8D;
    font-size: 2.8rem;
    margin: 0 auto;
}
.info-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #7F8C8D;
    letter-spacing: 0.8px;
    margin-bottom: 3px;
    text-transform: uppercase;
}
.info-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2C3E50;
    word-break: break-all;
}
.allergy-alert-box {
    border-radius: 10px;
    padding: 16px;
    border-left: 5px solid;
}
.allergy-alert-box.has-allergies {
    background-color: #FDF2E9;
    border-color: #E67E22;
    color: #A04000;
}
.allergy-alert-box.no-allergies {
    background-color: #E8F8F5;
    border-color: #1ABC9C;
    color: #0E6251;
}
.emergency-card {
    background: #FDEDEC;
    border-radius: 12px;
    border: 1px dashed #EC7063;
    padding: 20px;
}
.emergency-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background-color: #FADBD8;
    color: #C0392B;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.dependent-row {
    border-bottom: 1px solid #F2F4F4;
    padding: 12px 8px;
}
.dependent-row:last-child {
    border-bottom: none;
}
</style>

<div class="verify-profile-container py-5">
    <div class="container py-3">
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
            <!-- Main Title -->
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold mb-2">
                    <i class="fa-solid fa-circle-check me-1"></i> SECURE DATABASE QUERY
                </span>
                <h1 class="text-dark fw-bold">Member Registry Profile</h1>
                <p class="text-muted small mx-auto" style="max-width: 500px;">
                    This profile is dynamic and verified in real-time under domain authorization LFC-Secure.
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Left Column: Quick Profile & Emergency -->
                <div class="col-lg-4" data-aos="fade-right">
                    <!-- Profile Card -->
                    <div class="medical-profile-card">
                        <div class="medical-header-banner">
                            <!-- Photo Container -->
                            <?php 
                            $photo_path = $member['photo'];
                            if (empty($photo_path) || !file_exists(__DIR__ . '/' . $photo_path)) {
                                echo '<div class="medical-photo-placeholder"><i class="fa-solid fa-user-doctor"></i></div>';
                            } else {
                                echo '<img src="' . BASE_URL . htmlspecialchars($photo_path) . '" alt="' . htmlspecialchars($member['name']) . '" class="medical-photo-ring">';
                            }
                            ?>
                            <h4 class="text-white fw-bold mt-3 mb-1"><?php echo htmlspecialchars($member['name']); ?></h4>
                            <span class="text-white-50 small font-heading fw-bold">MEMBER ID: <?php echo htmlspecialchars($member['member_id']); ?></span>
                        </div>

                        <!-- Card Body Statuses -->
                        <div class="p-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="info-label mb-0">System Status</span>
                                <?php 
                                $status = strtoupper($member['status']);
                                if ($status === 'ACTIVE') {
                                    echo '<span class="badge bg-success text-white px-3 py-1.5 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i> ACTIVE</span>';
                                } elseif ($status === 'EXPIRED') {
                                    echo '<span class="badge bg-danger text-white px-3 py-1.5 rounded-pill"><i class="fa-solid fa-clock-rotate-left me-1"></i> EXPIRED</span>';
                                } elseif ($status === 'SUSPENDED') {
                                    echo '<span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill"><i class="fa-solid fa-triangle-exclamation me-1"></i> SUSPENDED</span>';
                                } else {
                                    echo '<span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill"><i class="fa-solid fa-ban me-1"></i> DEACTIVATED</span>';
                                }
                                ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="info-label mb-0">Expiry Date</span>
                                <span class="fw-bold text-dark font-heading small"><?php echo format_date($member['validity_date']); ?></span>
                            </div>
                        </div>

                        <!-- Card Footer actions -->
                        <div class="p-4 bg-light text-center">
                            <a href="<?php echo BASE_URL; ?>admin/generate-card.php?id=<?php echo urlencode($member['member_id']); ?>&t=<?php echo time(); ?>" class="btn btn-outline-primary btn-sm w-100 py-2 fw-bold rounded-3">
                                <i class="fa-solid fa-file-pdf me-2"></i>Download Printed PDF Card
                            </a>
                        </div>
                    </div>

                    <!-- Emergency Card -->
                    <div class="emergency-card shadow-sm" data-aos="zoom-in" data-aos-delay="100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="emergency-icon"><i class="fa-solid fa-phone-volume animate__animated animate__shakeY animate__infinite animate__slower"></i></div>
                            <div>
                                <h5 class="text-danger fw-bold mb-0">Emergency Contact</h5>
                                <span class="text-muted small">Available during primary validation</span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="info-label">Contact Person</div>
                            <div class="info-value text-dark" style="font-size: 1.05rem;"><?php echo htmlspecialchars($member['emergency_name']); ?></div>
                        </div>
                        <div>
                            <div class="info-label">Emergency Phone</div>
                            <div class="info-value">
                                <a href="tel:<?php echo htmlspecialchars($member['emergency_mobile']); ?>" class="text-danger fw-bold fs-5 text-decoration-none d-inline-flex align-items-center">
                                    <i class="fa-solid fa-phone me-2"></i><?php echo htmlspecialchars($member['emergency_mobile']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Medical Ledger & Dependents -->
                <div class="col-lg-8" data-aos="fade-left">
                    <!-- Personal Profile Card -->
                    <div class="medical-profile-card p-4">
                        <h5 class="text-dark fw-bold mb-4 font-heading border-bottom pb-2">
                            <i class="fa-solid fa-folder-open text-success me-2"></i>Personal & Contact Ledger
                        </h5>
                        <div class="row g-4">
                            <div class="col-sm-4 col-6">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value"><?php echo format_date($member['dob']); ?></div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <div class="info-label">Age</div>
                                <div class="info-value"><?php echo htmlspecialchars($member['age']); ?> Years</div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <div class="info-label">Gender</div>
                                <div class="info-value"><?php echo htmlspecialchars($member['gender']); ?></div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <div class="info-label">Blood Group</div>
                                <div class="info-value">
                                    <span class="badge bg-danger text-white px-3 py-1 font-heading fw-bold"><?php echo htmlspecialchars($member['blood_group']); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <div class="info-label">Mobile Number</div>
                                <div class="info-value">
                                    <a href="tel:<?php echo htmlspecialchars($member['mobile']); ?>" class="text-success text-decoration-none"><?php echo htmlspecialchars($member['mobile']); ?></a>
                                </div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <div class="info-label">Email Address</div>
                                <div class="info-value text-truncate"><?php echo !empty($member['email']) ? htmlspecialchars($member['email']) : 'N/A'; ?></div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Registered Residence Address</div>
                                <div class="info-value text-dark" style="word-break: normal;">
                                    <?php 
                                    $address = [];
                                    if(!empty($member['address'])) $address[] = $member['address'];
                                    if(!empty($member['city'])) $address[] = $member['city'];
                                    if(!empty($member['state'])) $address[] = $member['state'];
                                    if(!empty($member['pincode'])) $address[] = $member['pincode'];
                                    echo htmlspecialchars(implode(', ', $address));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Warnings & Allergies -->
                    <div class="medical-profile-card p-4">
                        <h5 class="text-dark fw-bold mb-3 font-heading border-bottom pb-2">
                            <i class="fa-solid fa-notes-medical text-danger me-2"></i>Clinical Warnings / Allergies
                        </h5>
                        <?php if (empty($member['allergies']) || strtolower(trim($member['allergies'])) === 'none'): ?>
                            <div class="allergy-alert-box no-allergies d-flex gap-3 align-items-center">
                                <span class="fs-2"><i class="fa-solid fa-circle-check"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-1">No Known Allergies Declared</h6>
                                    <p class="mb-0 small opacity-85">This member has no severe clinical drug or food hypersensitivity warnings on file.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="allergy-alert-box has-allergies d-flex gap-3 align-items-start">
                                <span class="fs-2 mt-1"><i class="fa-solid fa-triangle-exclamation animate__animated animate__flash animate__infinite"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-1">Active Medical Allergy Warnings</h6>
                                    <p class="mb-1 text-dark fw-bold small" style="letter-spacing: 0.5px;"><?php echo htmlspecialchars($member['allergies']); ?></p>
                                    <p class="mb-0 small opacity-85">Take necessary precautions. Verify clinical history in case of anesthesia or drug administration.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Medical History / Clinical Conditions -->
                    <div class="medical-profile-card p-4">
                        <h5 class="text-dark fw-bold mb-3 font-heading border-bottom pb-2">
                            <i class="fa-solid fa-file-waveform text-info me-2"></i>Medical History & Chronic Conditions
                        </h5>
                        <?php if (empty($member['health_info']) || strtolower(trim($member['health_info'])) === 'none'): ?>
                            <div class="p-3 bg-light rounded-3 text-muted small">
                                <i class="fa-solid fa-info-circle me-1 text-primary"></i> No chronic medical conditions or health histories declared.
                            </div>
                        <?php else: ?>
                            <div class="p-3 rounded-3" style="background-color: #EBF5FB; border-left: 5px solid #2980B9; color: #1B4F72;">
                                <h6 class="fw-bold mb-1 text-dark">Active Chronic Conditions / Health Notes</h6>
                                <p class="mb-0 small" style="white-space: pre-line;"><?php echo htmlspecialchars($member['health_info']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Dependents / Family Members Card -->
                    <div class="medical-profile-card p-4">
                        <h5 class="text-dark fw-bold mb-3 font-heading border-bottom pb-2">
                            <i class="fa-solid fa-people-roof text-primary me-2"></i>Linked Dependents / Family Members
                        </h5>
                        
                        <?php if (empty($family_members)): ?>
                            <div class="text-center text-muted py-4">
                                <span class="fs-1 d-block mb-2 text-black-50"><i class="fa-solid fa-users-slash"></i></span>
                                <p class="mb-0 small">No dependents or linked family members currently registered under this account.</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-2">
                                <?php foreach ($family_members as $fam): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-dark fw-bold mb-1" style="font-size: 0.95rem;"><?php echo htmlspecialchars($fam['name']); ?></h6>
                                                <span class="badge bg-secondary-subtle text-secondary small"><?php echo htmlspecialchars($fam['relation']); ?></span>
                                                <span class="text-muted small ms-2"><?php echo format_date($fam['dob']); ?></span>
                                            </div>
                                            <span class="badge bg-danger-subtle text-danger font-heading fw-bold px-2.5 py-1.5"><?php echo htmlspecialchars($fam['blood_group']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
