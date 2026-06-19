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
<section class="container mb-5 pb-3">
    <div class="stats-section p-4 bg-white shadow-lg" data-aos="fade-up" data-aos-offset="0">
        <div class="row text-center g-4 justify-content-center">
            <?php if (empty($stats_counters)): ?>
                <div class="col-lg-3 col-md-6 stat-item">
                    <div class="px-2">
                        <span class="stat-number mb-1 d-block">10,000+</span>
                        <p class="text-muted mb-0 small font-heading fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Happy Users</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <div class="px-2">
                        <span class="stat-number mb-1 d-block">500+</span>
                        <p class="text-muted mb-0 small font-heading fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Doctors</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <div class="px-2">
                        <span class="stat-number mb-1 d-block">200+</span>
                        <p class="text-muted mb-0 small font-heading fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Clinics</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 stat-item">
                    <div class="px-2">
                        <span class="stat-number mb-1 d-block">50+</span>
                        <p class="text-muted mb-0 small font-heading fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Hospitals</p>
                    </div>
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
                        <div class="px-2">
                            <span class="stat-number mb-1 d-block"><?php echo htmlspecialchars($stat['number']); ?></span>
                            <p class="text-muted mb-0 small font-heading fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;"><?php echo htmlspecialchars($stat['label']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-transparent">
    <div class="container py-3">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold mb-2 font-heading" style="font-size: 0.8rem;">
                <i class="fa-solid fa-star me-1 text-success"></i> PLATFORM CAPABILITIES
            </span>
            <h2 class="section-title text-dark fw-bold" style="font-size: 2.2rem;">Everything You Need for Smart Healthcare</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3.5px; border-radius: 2px;"></div>
            <p class="text-muted mx-auto" style="max-width: 600px; font-size: 1.02rem;">Our platform is engineered to give families secure, centralized medical registries with instant verification.</p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="custom-card h-100 p-4 border border-light-subtle shadow-sm bg-white rounded-4">
                    <div class="feature-icon-box bg-light-green mb-4 shadow-sm" style="width: 55px; height: 55px; border-radius: 12px;">
                        <i class="fa-solid fa-shield-halved text-success fs-4"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3 fw-bold" style="font-size: 1.15rem;">Secure Vault Privacy</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6; font-size: 0.88rem;">Your medical registries are fully encrypted. Public QR code verification displays profile validation logs without exposing personal clinical files.</p>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="custom-card h-100 p-4 border border-light-subtle shadow-sm bg-white rounded-4">
                    <div class="feature-icon-box bg-light-blue mb-4 shadow-sm" style="width: 55px; height: 55px; border-radius: 12px;">
                        <i class="fa-solid fa-file-medical text-primary fs-4"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3 fw-bold" style="font-size: 1.15rem;">Smart Medical Records</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6; font-size: 0.88rem;">Store and instantly retrieve your critical health records, emergency contacts, blood group registries, and chronic profiles with one scan.</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="custom-card h-100 p-4 border border-light-subtle shadow-sm bg-white rounded-4">
                    <div class="feature-icon-box bg-light-warning mb-4 shadow-sm" style="width: 55px; height: 55px; border-radius: 12px;">
                        <i class="fa-solid fa-calendar-check text-warning fs-4"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3 fw-bold" style="font-size: 1.15rem;">Consultation Registry</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6; font-size: 0.88rem;">Phase 2 integration connects members directly to partner doctor clinics for secure consult verification and electronic prescriptions.</p>
                </div>
            </div>
            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="custom-card h-100 p-4 border border-light-subtle shadow-sm bg-white rounded-4">
                    <div class="feature-icon-box bg-light-danger mb-4 shadow-sm" style="width: 55px; height: 55px; border-radius: 12px;">
                        <i class="fa-solid fa-heart-pulse text-danger fs-4"></i>
                    </div>
                    <h5 class="card-title text-dark mb-3 fw-bold" style="font-size: 1.15rem;">Automated Care Tips</h5>
                    <p class="text-muted small mb-0" style="line-height: 1.6; font-size: 0.88rem;">Receive automated validation indicators, renewal warnings, healthy lifestyle tips, and immediate updates on card status logs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust, Privacy & Security Protocols Section -->
<section class="py-5 bg-light border-top border-bottom">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <!-- Text Content -->
            <div class="col-lg-6 text-start" data-aos="fade-right">
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-3 font-heading" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-lock me-1"></i> DATA PROTECTION COMPLIANCE
                </span>
                <h2 class="text-dark fw-bold mb-3 font-heading" style="font-size: 2.2rem; line-height: 1.3;">
                    Bank-Grade Security for Your Family's Health Data
                </h2>
                <p class="text-muted mb-4" style="line-height: 1.7; font-size: 1.02rem;">
                    Lurnixe Health utilizes standard encryption and network architecture to safeguard family health registries. We ensure complete confidentiality, giving members full control over their profile validation logs.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-4 text-success"><i class="fa-solid fa-circle-check"></i></span>
                            <span class="fw-bold text-dark small">AES-256 Bit Encryption</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-4 text-success"><i class="fa-solid fa-circle-check"></i></span>
                            <span class="fw-bold text-dark small">GDPR & HIPAA Guidelines</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-4 text-success"><i class="fa-solid fa-circle-check"></i></span>
                            <span class="fw-bold text-dark small">Secure QR Matrix Card</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-4 text-success"><i class="fa-solid fa-circle-check"></i></span>
                            <span class="fw-bold text-dark small">24/7 Scan Audit Records</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Security Badge Graphic -->
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <div class="p-5 bg-white rounded-4 shadow-lg border border-light-subtle position-relative overflow-hidden" style="max-width: 460px; margin: 0 auto;">
                    <div class="position-absolute top-0 end-0 bg-success text-white px-4 py-2 small fw-bold font-heading rounded-bottom-start" style="font-size: 0.75rem;">SECURED</div>
                    <span class="fs-1 text-success mb-3 d-block"><i class="fa-solid fa-shield-halved animate__animated animate__pulse animate__infinite text-success" style="font-size: 4rem;"></i></span>
                    <h4 class="text-dark fw-bold mb-2 font-heading" style="font-size: 1.3rem;">Encrypted Medical Vault</h4>
                    <p class="text-muted small mb-4" style="line-height: 1.5;">Verification scans are validated in real-time under domain authorization LFC-Secure.</p>
                    <div class="d-flex justify-content-center gap-3 border-top pt-4">
                        <div class="text-center px-2">
                            <span class="d-block fw-bold text-dark mb-0" style="font-size: 0.95rem;">SSL</span>
                            <span class="text-muted small" style="font-size: 0.7rem; text-transform: uppercase;">Encryption</span>
                        </div>
                        <div class="vr text-muted"></div>
                        <div class="text-center px-2">
                            <span class="d-block fw-bold text-dark mb-0" style="font-size: 0.95rem;">2FA</span>
                            <span class="text-muted small" style="font-size: 0.7rem; text-transform: uppercase;">Admin Gate</span>
                        </div>
                        <div class="vr text-muted"></div>
                        <div class="text-center px-2">
                            <span class="d-block fw-bold text-dark mb-0" style="font-size: 0.95rem;">AES</span>
                            <span class="text-muted small" style="font-size: 0.7rem; text-transform: uppercase;">Database</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it Works Section -->
<section class="py-5 bg-white border-bottom">
    <div class="container py-3">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold mb-2 font-heading" style="font-size: 0.8rem;">
                <i class="fa-solid fa-route me-1 text-success"></i> TIMELINE STEPPER
            </span>
            <h2 class="section-title text-dark fw-bold" style="font-size: 2.2rem;">How Lurnixe Health Works</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3.5px; border-radius: 2px;"></div>
            <p class="text-muted">Five simple steps to secure your family's smart digital healthcare access.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="p-4 text-center bg-light rounded-4 h-100 border border-light-subtle shadow-sm">
                    <div class="step-number mx-auto mb-3">01</div>
                    <h6 class="text-dark fw-bold mb-2">Create Account</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">Contact our desk or representative office to enroll your family members in the program.</p>
                </div>
            </div>
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="p-4 text-center bg-light rounded-4 h-100 border border-light-subtle shadow-sm">
                    <div class="step-number mx-auto mb-3">02</div>
                    <h6 class="text-dark fw-bold mb-2">Get Health Card</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">The admin desk generates your unique LFC Member ID and printed CR80 PVC card.</p>
                </div>
            </div>
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="p-4 text-center bg-light rounded-4 h-100 border border-light-subtle shadow-sm">
                    <div class="step-number mx-auto mb-3">03</div>
                    <h6 class="text-dark fw-bold mb-2">Book Appointment</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">Scan your QR code or access the portal online to connect with our partner clinics.</p>
                </div>
            </div>
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="p-4 text-center bg-light rounded-4 h-100 border border-light-subtle shadow-sm">
                    <div class="step-number mx-auto mb-3">04</div>
                    <h6 class="text-dark fw-bold mb-2">Manage Health</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">Instantly view real-time profile validity, allergy warnings, and emergency details.</p>
                </div>
            </div>
            <div class="col-lg col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="p-4 text-center bg-light rounded-4 h-100 border border-light-subtle shadow-sm">
                    <div class="step-number mx-auto mb-3">05</div>
                    <h6 class="text-dark fw-bold mb-2">Stay Healthy</h6>
                    <p class="text-muted small mb-0" style="line-height: 1.5;">Receive check-up reminders, electronic recipes, and health alerts on your phone.</p>
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
