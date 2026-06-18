<?php
/**
 * Lurnixe Health Card System - Services Page
 * June 2026
 */
$page_title = "Services";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light border-bottom">
    <div class="container text-center py-4" data-aos="fade-up">
        <h1 class="display-5 text-dark fw-bold mb-3">Our Services & Pricing</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none text-success">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Pricing Plans Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-dark">Family Health Card Options</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3px;"></div>
            <p class="text-muted">Choose the validity duration that best fits your family's health registry requirements.</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <!-- 1 Year -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="custom-card text-center p-5 border-top border-5 border-primary">
                    <h4 class="text-primary mb-3">1 Year Plan</h4>
                    <h2 class="display-6 text-dark fw-bold mb-4">$49<span class="fs-6 text-muted fw-normal">/year</span></h2>
                    <ul class="list-unstyled mb-5 text-muted small">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> 1 Family Member Card</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> CR80 PVC Printable Card</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> 24/7 QR Scan Profile View</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Medical Record Attachment</li>
                        <li class="mb-2 text-decoration-line-through"><i class="fa-solid fa-xmark text-danger me-2"></i> Multi-Year Renewal Discount</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline-primary w-100 rounded-pill">Choose Plan</a>
                </div>
            </div>
            
            <!-- 2 Year -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="custom-card text-center p-5 border-top border-5 border-success position-relative" style="transform: scale(1.03); z-index: 2;">
                    <div class="position-absolute top-0 start-50 translate-middle bg-success text-white px-3 py-1 rounded-pill small fw-bold" style="font-size: 0.75rem;">MOST POPULAR</div>
                    <h4 class="text-success mb-3">2 Year Plan</h4>
                    <h2 class="display-6 text-dark fw-bold mb-4">$89<span class="fs-6 text-muted fw-normal">/2 years</span></h2>
                    <ul class="list-unstyled mb-5 text-muted small">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> 1 Family Member Card</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> CR80 PVC Printable Card</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> 24/7 QR Scan Profile View</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Medical Record Attachment</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Save 10% on Validity extension</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-success w-100 rounded-pill">Choose Plan</a>
                </div>
            </div>
            
            <!-- 3 Year -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="custom-card text-center p-5 border-top border-5 border-primary">
                    <h4 class="text-primary mb-3">3 Year Plan</h4>
                    <h2 class="display-6 text-dark fw-bold mb-4">$119<span class="fs-6 text-muted fw-normal">/3 years</span></h2>
                    <ul class="list-unstyled mb-5 text-muted small">
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> 1 Family Member Card</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> CR80 PVC Printable Card</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> 24/7 QR Scan Profile View</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Medical Record Attachment</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i> Save 20% on Validity extension</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline-primary w-100 rounded-pill">Choose Plan</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Future / Phase 2 Services -->
<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-dark">Expansion Services (Phase 2 & 3)</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3px;"></div>
            <p class="text-muted">Sneak peek into our upcoming clinical integration modules.</p>
        </div>
        
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <!-- Mockup from assets -->
                <img src="<?php echo BASE_URL; ?>assets/images/doctor_list.jpg" alt="Lurnixe Health Partner Doctor List" class="img-fluid rounded-4 shadow-lg" style="max-height: 480px; border: 8px solid white;">
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-light-success p-3 rounded-3 text-success fs-4">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                        <h4 class="text-dark mb-1">Clinic Partner Portal</h4>
                        <p class="text-muted small">Connects health card holders directly with local clinics to automatically log medical checkups, appointments, and general consulting visits.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-light-blue p-3 rounded-3 text-primary fs-4">
                        <i class="fa-solid fa-hospital"></i>
                    </div>
                    <div>
                        <h4 class="text-dark mb-1">Doctor Directory & Search</h4>
                        <p class="text-muted small">Search registered doctors by location, specialization, and availability. Filter results and initiate easy appointment requests directly.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-light-danger p-3 rounded-3 text-danger fs-4">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h4 class="text-dark mb-1">Digital Medical Prescriptions</h4>
                        <p class="text-muted small">Store clinical laboratory reports and doctor prescriptions right on your portal profile, eliminating missing paper files completely.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
