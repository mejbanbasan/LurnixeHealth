<?php
/**
 * Lurnixe Health Card System - Public Footer Template
 * June 2026
 */
?>
<?php
// Retrieve database-driven settings if not already loaded in the parent scope
if (!isset($settings)) {
    $settings = [];
    try {
        $settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        while ($row = $settings_stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Exception $e) {
        error_log("Failed to fetch site settings in footer: " . $e->getMessage());
    }
}

$site_name = $settings['site_name'] ?? 'LurnixeHealth';
$logo_path = $settings['logo_path'] ?? '';
$footer_tagline = $settings['footer_tagline'] ?? 'Providing quality healthcare solutions for families.';
$footer_copyright = $settings['footer_copyright'] ?? ('&copy; ' . date('Y') . ' Lurnixe Health. All Rights Reserved.');
$footer_description = $settings['footer_description'] ?? 'Lurnixe Health is dedicated to making healthcare smarter, simpler, and more accessible. Our Digital Family Health Card integrates patient records with local medical providers for seamless access.';
$contact_phone = $settings['contact_phone'] ?? '+1 (800) 123-4567';
$contact_email = $settings['contact_email'] ?? 'support@lurnixehealth.com';
$contact_address = $settings['contact_address'] ?? '123 Healthcare Blvd, Suite 400, Medical District, NY 10016';
?>
    <!-- Footer Section -->
    <footer class="footer bg-dark text-white pt-5 pb-3 d-none d-lg-block">
        <div class="container">
            <div class="row g-4">
                <!-- Branding and info -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="footer-brand d-flex align-items-center mb-3">
                        <?php if (!empty($logo_path) && file_exists(__DIR__ . '/../' . $logo_path)): ?>
                            <img src="<?php echo BASE_URL . $logo_path; ?>" alt="<?php echo htmlspecialchars($site_name); ?>" style="max-height: 48px;" class="me-2">
                        <?php else: ?>
                            <div class="brand-logo-container bg-success me-2">
                                <span class="brand-icon text-white"><i class="fa-solid fa-heart-pulse"></i></span>
                            </div>
                            <span class="brand-name text-white fs-4 fw-bold"><?php echo htmlspecialchars($site_name); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-white small">
                        <?php echo htmlspecialchars($footer_description); ?>
                    </p>
                    <div class="footer-social-links mt-3">
                        <?php if (($settings['show_facebook'] ?? '0') === '1' && !empty($settings['facebook_url'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['facebook_url']); ?>" target="_blank" class="me-3 text-white hover-white"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (($settings['show_twitter'] ?? '0') === '1' && !empty($settings['twitter_url'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['twitter_url']); ?>" target="_blank" class="me-3 text-white hover-white"><i class="fab fa-x-twitter"></i></a>
                        <?php endif; ?>
                        <?php if (($settings['show_instagram'] ?? '0') === '1' && !empty($settings['instagram_url'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['instagram_url']); ?>" target="_blank" class="me-3 text-white hover-white"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (($settings['show_youtube'] ?? '0') === '1' && !empty($settings['youtube_url'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['youtube_url']); ?>" target="_blank" class="me-3 text-white hover-white"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        <?php if (($settings['show_linkedin'] ?? '0') === '1' && !empty($settings['linkedin_url'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['linkedin_url']); ?>" target="_blank" class="me-3 text-white hover-white"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <h5 class="footer-heading mb-3 pb-2 text-white border-bottom border-success border-2 d-inline-block">Quick Links</h5>
                    <ul class="list-unstyled footer-links">
                        <?php
                        // Fetch active top-level nav items from DB
                        $footer_navs = [];
                        try {
                            $fnav_stmt = $pdo->query("SELECT * FROM nav_items WHERE is_active = 1 AND parent_id IS NULL ORDER BY sort_order ASC, id ASC");
                            $footer_navs = $fnav_stmt->fetchAll();
                        } catch (Exception $e) {
                            error_log("Failed to load footer nav links: " . $e->getMessage());
                        }
                        
                        if (empty($footer_navs)) {
                            // Hardcoded fallback
                            echo '<li class="mb-2"><a href="' . BASE_URL . 'index.php" class="text-white text-decoration-none">Home</a></li>';
                            echo '<li class="mb-2"><a href="' . BASE_URL . 'about.php" class="text-white text-decoration-none">About Us</a></li>';
                            echo '<li class="mb-2"><a href="' . BASE_URL . 'health-card.php" class="text-white text-decoration-none">Health Card</a></li>';
                            echo '<li class="mb-2"><a href="' . BASE_URL . 'services.php" class="text-white text-decoration-none">Services</a></li>';
                            echo '<li class="mb-2"><a href="' . BASE_URL . 'contact.php" class="text-white text-decoration-none">Contact Us</a></li>';
                        } else {
                            foreach ($footer_navs as $nav) {
                                $nav_url = (strpos($nav['url'], 'http') === 0 || strpos($nav['url'], 'https') === 0) ? $nav['url'] : BASE_URL . $nav['url'];
                                echo '<li class="mb-2"><a href="' . $nav_url . '" class="text-white text-decoration-none">' . htmlspecialchars($nav['label']) . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>

                <!-- Core Modules -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <h5 class="footer-heading mb-3 pb-2 text-white border-bottom border-success border-2 d-inline-block">Core Modules</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>health-card.php" class="text-white text-decoration-none">Digital ID & QR Code</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>services.php" class="text-white text-decoration-none">Family Record Linking</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>about.php" class="text-white text-decoration-none">Emergency Info Portal</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>terms.php" class="text-white text-decoration-none">Terms & Conditions</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>admin/login.php" class="text-white text-decoration-none">Authorized Admin Login</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <h5 class="footer-heading mb-3 pb-2 text-white border-bottom border-success border-2 d-inline-block">Contact Info</h5>
                    <ul class="list-unstyled footer-contact text-white small">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fa-solid fa-location-dot mt-1 me-2 text-success"></i>
                            <span><?php echo nl2br(htmlspecialchars($contact_address)); ?></span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fa-solid fa-phone me-2 text-success"></i>
                            <span><?php echo htmlspecialchars($contact_phone); ?></span>
                        </li>
                        <li class="mb-3 d-flex align-items-center">
                            <i class="fa-solid fa-envelope me-2 text-success"></i>
                            <span><?php echo htmlspecialchars($contact_email); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white small"><?php echo htmlspecialchars($footer_copyright); ?></p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    <p class="mb-0 text-white small">
                        <a href="<?php echo BASE_URL; ?>terms.php" class="text-white text-decoration-none me-3">Terms of Service</a>
                        <a href="<?php echo BASE_URL; ?>terms.php" class="text-white text-decoration-none">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation Bar -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="d-flex justify-content-around align-items-center h-100">
            <a href="<?php echo BASE_URL; ?>index.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
            <a href="<?php echo BASE_URL; ?>services.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'services.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-doctor"></i>
                <span>Doctors</span>
            </a>
            <!-- Floating Center Scanner FAB -->
            <a href="<?php echo BASE_URL; ?>scanner.php" class="nav-item scan-fab-item">
                <div class="scan-fab-btn">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <span class="scan-label">Health Card</span>
            </a>
            <a href="<?php echo BASE_URL; ?>contact.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) === 'contact.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Appointments</span>
            </a>
            <?php if (isset($_SESSION['admin_id'])): ?>
                <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="nav-item">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>admin/login.php" class="nav-item">
                    <i class="fa-solid fa-user-lock"></i>
                    <span>Profile</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS (Animate on Scroll) JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <!-- Custom Main JS -->
    <script src="<?php echo BASE_URL; ?>assets/js/main.js?v=1.0"></script>
    
    <script>
        // Initialize AOS
        document.addEventListener("DOMContentLoaded", function() {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 50
            });
        });
    </script>
</body>
</html>
