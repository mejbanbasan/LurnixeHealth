<?php
/**
 * Lurnixe Health Card System - Services Page
 * June 2026
 */
$page_title = "Services";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Doctors Mobile Banner (Mobile Only) -->
<div class="d-block d-lg-none text-center">
    <img src="<?php echo BASE_URL; ?>assets/images/doctors_mobile_banner.jpg" alt="Our Partner Doctors & Services" class="img-fluid w-100 shadow-sm">
</div>

<section class="py-5 bg-light border-bottom">
    <div class="container text-center py-4" data-aos="fade-up">
        <h1 class="display-5 text-dark fw-bold mb-3">Our Partner Doctors & Services</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none text-success">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Doctors</li>
            </ol>
        </nav>
    </div>
</section>



<!-- Partner Doctors & Services -->
<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-dark">Our Partner Doctors & Services</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3px;"></div>
            <p class="text-muted">Explore our medical integrations, partner clinic lookups, and digital prescriptions.</p>
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
