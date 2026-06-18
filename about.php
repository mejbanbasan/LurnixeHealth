<?php
/**
 * Lurnixe Health Card System - About Us
 * June 2026
 */
$page_title = "About Us";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light border-bottom">
    <div class="container text-center py-4" data-aos="fade-up">
        <h1 class="display-5 text-dark fw-bold mb-3">About Lurnixe Health</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none text-success">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="text-dark mb-4">Caring for Your Health — Smart. Simple. Secure.</h2>
                <p class="lead text-muted mb-4">
                    Lurnixe Health is a modern healthcare platform dedicated to making healthcare services smarter, simpler, and more accessible for everyone.
                </p>
                <p class="text-muted">
                    Our mission is to connect patients, doctors, clinics, hospitals, and healthcare providers through a secure and innovative digital ecosystem. Through our Digital Health Card system, online appointment services, health record management, and healthcare technology solutions, we aim to provide a seamless experience for both patients and medical professionals.
                </p>
                <p class="text-muted mb-0">
                    Our platform is designed to help individuals securely store and access their health information anytime, anywhere. By combining technology with healthcare, Lurnixe Health strives to improve efficiency, reduce paperwork, and ensure that quality healthcare is just a scan away.
                </p>
            </div>
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <!-- Display mockup image from assets -->
                <img src="<?php echo BASE_URL; ?>assets/images/portal_welcome.jpg" alt="Lurnixe Health Welcome Portal" class="img-fluid rounded-4 shadow-lg" style="max-height: 480px; border: 8px solid white;">
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="p-4 border rounded-3 h-100">
                    <div class="bg-light-green text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <h4 class="text-dark mb-3">Our Vision</h4>
                    <p class="text-muted small mb-0">To establish a global network of digitized health files, bridging the gap between clinical settings and active emergencies.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="p-4 border rounded-3 h-100">
                    <div class="bg-light-blue text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h4 class="text-dark mb-3">Our Mission</h4>
                    <p class="text-muted small mb-0">To eliminate paper medical history, provide instant access to life-saving profiles, and help clinics connect with patients.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="p-4 border rounded-3 h-100">
                    <div class="bg-light-danger text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <h4 class="text-dark mb-3">Core Values</h4>
                    <p class="text-muted small mb-0">Data Security first, Patient Privacy by Default, Scalable Engineering, and User-Friendly Design for all age groups.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
