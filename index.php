<?php
/**
 * Lurnixe Health Card System - Home Page
 * June 2026
 */
$page_title = "Home";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section py-5 mb-5 shadow-sm">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left Column: Premium HTML Typography & Action Buttons -->
            <div class="col-lg-6 text-start" data-aos="fade-right">
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold mb-3 font-heading" style="letter-spacing: 0.5px;">
                    <i class="fa-solid fa-shield-halved me-1 text-success"></i> Premium Family Health Card
                </span>
                <h1 class="hero-title text-dark mb-3" style="font-size: 2.8rem; font-weight: 800; line-height: 1.2;">
                    Your Health. <br>
                    <span style="color: var(--primary-green);">Our Priority. Always.</span>
                </h1>
                <p class="hero-subtitle text-muted mb-4 fs-5" style="max-width: 530px; line-height: 1.6; font-size: 1.05rem !important;">
                    Lurnixe Health is your secure digital healthcare companion. Manage your family health records, book doctor appointments, and verify profiles instantly with one simple scan.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo BASE_URL; ?>health-card.php" class="btn btn-success text-white px-4 py-3 rounded-3 shadow d-inline-flex align-items-center">
                        <i class="fa-solid fa-address-card me-2 fs-5"></i>
                        <span class="fw-bold">Apply for Health Card</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-outline-primary px-4 py-3 rounded-3 d-inline-flex align-items-center">
                        <i class="fa-solid fa-calendar-check me-2 fs-5"></i>
                        <span class="fw-bold">Book Appointment</span>
                    </a>
                </div>
            </div>
            <!-- Right Column: Crop Graphic with dynamic verification label -->
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <div class="position-relative d-inline-block">
                    <img src="<?php echo BASE_URL; ?>assets/images/hero_family_cropped.jpg?v=2.0" alt="Lurnixe Family Health" class="img-fluid rounded-4 shadow-lg border" style="max-width: 100%; transition: transform 0.3s ease; border-color: rgba(0,0,0,0.08);" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="position-absolute bottom-0 start-0 p-3 bg-white rounded-3 shadow-sm border m-3 d-none d-sm-flex align-items-center gap-2" style="max-width: 280px; z-index: 10; border-color: rgba(0,0,0,0.05);">
                        <i class="fa-solid fa-circle-check text-success fs-4 animate__animated animate__pulse animate__infinite"></i>
                        <div class="text-start">
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">Dynamic Registry Verified</h6>
                            <span class="text-muted small" style="font-size: 0.75rem;">Real-time database validation</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Query active stats counters from DB
$stats_counters = [];
try {
    $stat_stmt = $pdo->query("SELECT * FROM stat_counters WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
    $stats_counters = $stat_stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to load stat_counters in index: " . $e->getMessage());
}
?>
<!-- Stats Section -->
<section class="container mb-5">
    <div class="stats-section p-4 bg-white shadow-sm" data-aos="fade-up" data-aos-offset="0">
        <div class="row text-center g-4 justify-content-center">
            <?php if (empty($stats_counters)): ?>
                <div class="col-lg-3 col-md-6 stat-item">
                    <span class="stat-number">10,000+</span>
                    <p class="text-muted mb-0 small font-heading fw-medium">Happy Users</p>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <span class="stat-number">500+</span>
                    <p class="text-muted mb-0 small font-heading fw-medium">Doctors</p>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <span class="stat-number">200+</span>
                    <p class="text-muted mb-0 small font-heading fw-medium">Clinics</p>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <span class="stat-number">50+</span>
                    <p class="text-muted mb-0 small font-heading fw-medium">Hospitals</p>
                </div>
            <?php else: ?>
                <?php 
                $col_class = 'col-lg-3';
                $cnt = count($stats_counters);
                if ($cnt === 1) $col_class = 'col-md-6 col-lg-12';
                elseif ($cnt === 2) $col_class = 'col-md-6 col-lg-6';
                elseif ($cnt === 3) $col_class = 'col-md-4 col-lg-4';
                ?>
                <?php foreach ($stats_counters as $stat): ?>
                    <div class="<?php echo $col_class; ?> col-md-6 stat-item">
                        <span class="stat-number"><?php echo htmlspecialchars($stat['number']); ?></span>
                        <p class="text-muted mb-0 small font-heading fw-medium"><?php echo htmlspecialchars($stat['label']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title text-dark">Everything You Need for Better Healthcare</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3px;"></div>
            <p class="text-muted mx-auto" style="max-width: 600px;">Our platform is engineered to give you and your family a secure, centralized health registry with easy access.</p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="custom-card h-100 p-4">
                    <div class="feature-icon-box bg-light-green">
                        <i class="fa-solid fa-shield-virus"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3">Secure & Private</h5>
                    <p class="text-muted small mb-0">Your medical data is encrypted and secure. Privacy is our top priority, restricting detail views on public QR scans.</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="custom-card h-100 p-4">
                    <div class="feature-icon-box bg-light-blue">
                        <i class="fa-solid fa-file-medical"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3">Health Records</h5>
                    <p class="text-muted small mb-0">Store and instantly retrieve your critical health records, emergency contacts, and blood groups with one click.</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="custom-card h-100 p-4">
                    <div class="feature-icon-box bg-light-warning">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3">Book Appointments</h5>
                    <p class="text-muted small mb-0">Phase 2 integration will connect you directly with over 500 partner doctors to request appointments seamlessly.</p>
                </div>
            </div>
            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="custom-card h-100 p-4">
                    <div class="feature-icon-box bg-light-danger">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3">Stay Healthy</h5>
                    <p class="text-muted small mb-0">Receive automated health tips, expiration warnings, renewal options, and active status checks on your health card.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it Works Section -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title text-dark">How Lurnixe Health Works</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3px;"></div>
            <p class="text-muted">Five simple steps to secure your family's smart digital healthcare access.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="p-3 text-center">
                    <div class="step-number mx-auto">01</div>
                    <h6 class="text-dark fw-bold mb-2">Create Account</h6>
                    <p class="text-muted small mb-0">Contact our representative or administration desk to enroll.</p>
                </div>
            </div>
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="p-3 text-center">
                    <div class="step-number mx-auto">02</div>
                    <h6 class="text-dark fw-bold mb-2">Get Health Card</h6>
                    <p class="text-muted small mb-0">The admin generates your unique LFC Member ID and printed card.</p>
                </div>
            </div>
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="p-3 text-center">
                    <div class="step-number mx-auto">03</div>
                    <h6 class="text-dark fw-bold mb-2">Book Appointment</h6>
                    <p class="text-muted small mb-0">Scan your QR code or access the portal to connect to partner doctors.</p>
                </div>
            </div>
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="p-3 text-center">
                    <div class="step-number mx-auto">04</div>
                    <h6 class="text-dark fw-bold mb-2">Manage Health</h6>
                    <p class="text-muted small mb-0">Instantly view validation, allergies, and emergency details.</p>
                </div>
            </div>
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="p-3 text-center">
                    <div class="step-number mx-auto">05</div>
                    <h6 class="text-dark fw-bold mb-2">Stay Healthy</h6>
                    <p class="text-muted small mb-0">Get reminders, digital prescriptions, and updates on the go.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container text-center py-4" data-aos="zoom-in">
        <h2 class="text-dark mb-3">Ready to Protect Your Family?</h2>
        <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
            Apply for your digital Lurnixe Family Health Card today. Store your data securely and ensure your information is ready for emergency scans when needed most.
        </p>
        <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-success btn-lg rounded-pill px-5 py-3">
            <i class="fa-solid fa-file-signature me-2"></i> Enroll Offline Now
        </a>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
