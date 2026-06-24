<?php
/**
 * Lurnixe Health Card System - User Profile Page
 * June 2026
 */
$page_title = "My Profile";
require_once __DIR__ . '/includes/header.php';

// Get member ID from session, parameter, or cookie
$member_id = null;
if (isset($_GET['id'])) {
    $member_id = sanitize_input($_GET['id']);
} elseif (isset($_SESSION['member_id'])) {
    $member_id = $_SESSION['member_id'];
}

$member = null;
$family_members = [];
$error = "";

// If no member ID, show a demo profile
if (empty($member_id)) {
    // Demo profile data for demonstration
    $member = [
        'id' => 1,
        'member_id' => 'LFC000521',
        'name' => 'John Alexander Doe',
        'email' => 'john.doe@example.com',
        'mobile' => '+91-98765-43210',
        'blood_group' => 'O+',
        'dob' => '1990-05-15',
        'age' => 34,
        'gender' => 'Male',
        'photo' => null,
        'health_info' => 'Hypertension - On medication',
        'allergies' => 'Penicillin',
        'validity_date' => '2027-06-15',
        'status' => 'active'
    ];
} else {
    // Fetch member from database
    try {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
        $stmt->execute([$member_id]);
        $member = $stmt->fetch();
        
        if ($member) {
            // Fetch family members
            $fam_stmt = $pdo->prepare("SELECT * FROM family_members WHERE member_id = ? ORDER BY id ASC");
            $fam_stmt->execute([$member_id]);
            $family_members = $fam_stmt->fetchAll();
        } else {
            $error = "Member profile not found.";
        }
    } catch (PDOException $e) {
        error_log("Failed to fetch member profile: " . $e->getMessage());
        $error = "An error occurred while loading your profile.";
    }
}

// Calculate health score (demo)
$health_score = 85;
?>

<style>
/* Profile Page Specific Styles */
.profile-page-container {
    background-color: #F8F9FA;
    min-height: 100vh;
    padding-bottom: 100px;
}

@media (max-width: 991.98px) {
    .profile-page-container {
        padding-bottom: 100px;
    }
}
</style>

<div class="profile-page-container">
    <!-- Profile Header -->
    <div class="profile-header py-5">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <!-- Avatar -->
                <div class="profile-avatar">
                    <?php if ($member && $member['photo'] && file_exists(__DIR__ . '/' . $member['photo'])): ?>
                        <img src="<?php echo BASE_URL . htmlspecialchars($member['photo']); ?>" alt="Profile">
                    <?php else: ?>
                        <i class="fa-solid fa-user"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Name -->
                <h1 class="profile-name">
                    <?php echo $member ? htmlspecialchars($member['name']) : 'User Profile'; ?>
                </h1>
                
                <!-- Verification Badge -->
                <div class="profile-badge">
                    <i class="fa-solid fa-circle-check me-1"></i>
                    <?php if ($member && $member['status'] === 'active'): ?>
                        Verified & Active
                    <?php else: ?>
                        Profile Status
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Health Information Section -->
        <div class="profile-section" data-aos="fade-up">
            <div class="profile-section-title">
                <i class="fa-solid fa-heart-pulse me-2"></i>
                Health Information
            </div>
            <div class="profile-section-content">
                <div class="profile-card-grid">
                    <div class="profile-info-card">
                        <div class="profile-info-label">Card ID</div>
                        <div class="profile-info-value"><?php echo $member ? htmlspecialchars($member['member_id']) : 'N/A'; ?></div>
                    </div>
                    <div class="profile-info-card health">
                        <div class="profile-info-label">Blood Group</div>
                        <div class="profile-info-value"><?php echo $member ? htmlspecialchars($member['blood_group']) : 'N/A'; ?></div>
                    </div>
                </div>
                <div style="padding: 16px;">
                    <div class="profile-item">
                        <span class="profile-item-label">Valid Upto</span>
                        <span class="profile-item-value">
                            <?php echo $member ? date('d M Y', strtotime($member['validity_date'])) : 'N/A'; ?>
                        </span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-item-label">Health Score</span>
                        <div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $health_score; ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo $health_score; ?>/100</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health Section -->
        <div class="profile-section" data-aos="fade-up" data-aos-delay="100">
            <div class="profile-section-title">
                <i class="fa-solid fa-stethoscope me-2"></i>
                Health Details
            </div>
            <div class="profile-section-content">
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #D5F5E3;">
                            <i class="fa-solid fa-file-medical text-success"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>My Health Records</h6>
                            <p>View medical documents</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #D6EAF8;">
                            <i class="fa-solid fa-calendar-check text-primary"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>My Appointments</h6>
                            <p>Schedule & history</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #FADBD8;">
                            <i class="fa-solid fa-users text-danger"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>My Family</h6>
                            <p><?php echo count($family_members); ?> family members</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #FDEBD0;">
                            <i class="fa-solid fa-prescription-bottle text-warning"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>My Prescriptions</h6>
                            <p>Current & past medications</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #E8DAEF;">
                            <i class="fa-solid fa-bell text-primary"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>Health Reminders</h6>
                            <p>Medications & checkups</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="profile-section" data-aos="fade-up" data-aos-delay="200">
            <div class="profile-section-title">
                <i class="fa-solid fa-user-circle me-2"></i>
                Personal Information
            </div>
            <div class="profile-section-content">
                <div class="profile-item">
                    <span class="profile-item-label">Email Address</span>
                    <span class="profile-item-value">
                        <?php echo $member && $member['email'] ? htmlspecialchars($member['email']) : '<span class="empty">Not provided</span>'; ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="profile-item-label">Mobile Number</span>
                    <span class="profile-item-value">
                        <?php echo $member ? htmlspecialchars($member['mobile']) : 'N/A'; ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="profile-item-label">Date of Birth</span>
                    <span class="profile-item-value">
                        <?php echo $member ? date('d M Y', strtotime($member['dob'])) : 'N/A'; ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="profile-item-label">Age</span>
                    <span class="profile-item-value">
                        <?php echo $member ? $member['age'] . ' years' : 'N/A'; ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="profile-item-label">Gender</span>
                    <span class="profile-item-value">
                        <?php echo $member ? htmlspecialchars($member['gender']) : 'N/A'; ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="profile-item-label">Allergies</span>
                    <span class="profile-item-value">
                        <?php echo $member && $member['allergies'] ? htmlspecialchars($member['allergies']) : '<span class="empty">None</span>'; ?>
                    </span>
                </div>
                <div class="profile-item">
                    <span class="profile-item-label">Health Info</span>
                    <span class="profile-item-value">
                        <?php echo $member && $member['health_info'] ? htmlspecialchars($member['health_info']) : '<span class="empty">None</span>'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Account Section -->
        <div class="profile-section" data-aos="fade-up" data-aos-delay="300">
            <div class="profile-section-title">
                <i class="fa-solid fa-sliders me-2"></i>
                Account Settings
            </div>
            <div class="profile-section-content">
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon">
                            <i class="fa-solid fa-user text-primary"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>Personal Information</h6>
                            <p>Edit profile details</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #D5F5E3;">
                            <i class="fa-solid fa-map-location-dot text-success"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>Address</h6>
                            <p>Update address information</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #D6EAF8;">
                            <i class="fa-solid fa-credit-card text-primary"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>Payment Methods</h6>
                            <p>Manage payment options</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #FDEBD0;">
                            <i class="fa-solid fa-gear text-warning"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>Settings</h6>
                            <p>App preferences & privacy</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
                <a href="#" class="account-menu-item">
                    <div class="account-menu-left">
                        <div class="account-menu-icon" style="background-color: #E8DAEF;">
                            <i class="fa-solid fa-circle-question text-primary"></i>
                        </div>
                        <div class="account-menu-text">
                            <h6>Help & Support</h6>
                            <p>FAQs & contact support</p>
                        </div>
                    </div>
                    <div class="account-menu-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="profile-section" data-aos="fade-up" data-aos-delay="400">
            <button class="profile-logout-btn w-100" data-logout-url="<?php echo BASE_URL; ?>admin/logout.php">
                <i class="fa-solid fa-right-from-bracket me-2"></i>
                Logout
            </button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
