<?php
/**
 * Lurnixe Health Card System - Services & Partner Doctors Page
 * June 2026
 */
$page_title = "Services";
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* Service Cards Styling */
.service-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 35px 25px;
    height: 100%;
    position: relative;
    overflow: hidden;
}
.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(26, 188, 156, 0.08);
    border-color: rgba(26, 188, 156, 0.2);
}
.service-icon-box {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 25px;
    font-size: 1.6rem;
    transition: transform 0.3s ease;
}
.service-card:hover .service-icon-box {
    transform: scale(1.1) rotate(5deg);
}
.bg-soft-success { background-color: #E8F8F5; color: #1ABC9C; }
.bg-soft-info { background-color: #EBF5FB; color: #2980B9; }
.bg-soft-warning { background-color: #FEF9E7; color: #F39C12; }
.bg-soft-danger { background-color: #FDEDEC; color: #E74C3C; }
.bg-soft-primary { background-color: #EEF2FF; color: #4F46E5; }
.bg-soft-purple { background-color: #F5EEF8; color: #8E44AD; }

/* How It Works Section */
.step-card {
    background: #ffffff;
    border: 1px dashed rgba(26, 188, 156, 0.2);
    border-radius: 16px;
    padding: 30px 20px;
    height: 100%;
    position: relative;
}
.step-number {
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 40px;
    background-color: #1ABC9C;
    color: white;
    font-weight: 700;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #ffffff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>

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

<!-- Doctors Mobile Banner (Mobile Only) -->
<div class="d-block d-lg-none text-center">
    <img src="<?php echo BASE_URL; ?>assets/images/doctors_mobile_banner.jpg" alt="Our Partner Doctors & Services" class="img-fluid w-100 shadow-sm">
</div>

<!-- Core Services Grid Section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-dark fw-bold font-heading">Healthcare Services Ecosystem</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3px;"></div>
            <p class="text-muted mx-auto" style="max-width: 650px;">Explore the smart digital solutions and clinic partner networks integrated directly into your LurnixeHealth account.</p>
        </div>

        <div class="row g-4">
            <!-- Service 1: Digital & Physical Health Card -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="service-icon-box bg-soft-success">
                        <i class="fa-solid fa-address-card"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-3" style="font-size: 1.25rem;">Smart Health Cards</h4>
                    <p class="text-muted small mb-0">
                        Access your unified medical profile anytime from a browser or carry our premium, double-sided CR80 PVC card. Built to sit in your wallet with emergency tags, blood group indexes, and custom-styled verification QR codes.
                    </p>
                </div>
            </div>

            <!-- Service 2: Doctor Directory & Appointments -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="service-icon-box bg-soft-info">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-3" style="font-size: 1.25rem;">Doctor Directory & Booking</h4>
                    <p class="text-muted small mb-0">
                        Browse our extensive database of partner specialists and general practitioners. Search by location, qualification, or clinical department, and instantly submit consultation requests directly from your portal.
                    </p>
                </div>
            </div>

            <!-- Service 3: Unified Medical Registry -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card">
                    <div class="service-icon-box bg-soft-purple">
                        <i class="fa-solid fa-file-waveform"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-3" style="font-size: 1.25rem;">Electronic Health Records</h4>
                    <p class="text-muted small mb-0">
                        Consolidate all digital prescriptions, clinical laboratory test results, immunization histories, and doctor consult logs. Say goodbye to lost files and maintain a single secure source of medical truth.
                    </p>
                </div>
            </div>

            <!-- Service 4: Family Health Management -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                <div class="service-card">
                    <div class="service-icon-box bg-soft-danger">
                        <i class="fa-solid fa-people-roof"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-3" style="font-size: 1.25rem;">Family Card Management</h4>
                    <p class="text-muted small mb-0">
                        Link multiple dependent accounts for children, spouse, or elderly parents under a single primary health card profile. Manage schedules, verify IDs, and coordinate active care records for your whole household.
                    </p>
                </div>
            </div>

            <!-- Service 5: Secure QR Verification -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                <div class="service-card">
                    <div class="service-icon-box bg-soft-warning">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-3" style="font-size: 1.25rem;">Instant QR-Based Verify</h4>
                    <p class="text-muted small mb-0">
                        Allow emergency responders, laboratories, and clinic staff to securely authenticate your health status. Scanning the card's QR code instantly loads your verified status page and emergency medical profile.
                    </p>
                </div>
            </div>

            <!-- Service 6: Clinic Partner Portal -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="600">
                <div class="service-card">
                    <div class="service-icon-box bg-soft-primary">
                        <i class="fa-solid fa-circle-h"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-3" style="font-size: 1.25rem;">Partner Clinic Sync</h4>
                    <p class="text-muted small mb-0">
                        Our backend synchronizes consultation summaries, clinical reports, and prescriptions directly from authorized clinics to your profile, providing an automatically updated timeline of your medical visits.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light border-top border-bottom">
    <div class="container py-3">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-dark fw-bold font-heading">How the Ecosystem Works</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3px;"></div>
            <p class="text-muted mx-auto" style="max-width: 650px;">A seamless connection between you, your family health card, and our partner clinic registry.</p>
        </div>

        <div class="row g-4 text-center">
            <!-- Step 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="text-success fs-3 mb-3 mt-2"><i class="fa-solid fa-user-plus"></i></div>
                    <h5 class="text-dark fw-bold mb-2">Enroll & Set Profiles</h5>
                    <p class="text-muted small mb-0">Register your family account, upload basic medical details (allergies, blood group), and get your digital and physical health cards.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="text-success fs-3 mb-3 mt-2"><i class="fa-solid fa-qrcode"></i></div>
                    <h5 class="text-dark fw-bold mb-2">Present Card / Scan QR</h5>
                    <p class="text-muted small mb-0">At emergency setups or partner clinic visits, show the card. The clinician scans the QR code to verify details and pull current emergency files.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="text-success fs-3 mb-3 mt-2"><i class="fa-solid fa-arrows-spin"></i></div>
                    <h5 class="text-dark fw-bold mb-2">Auto-Sync Consults</h5>
                    <p class="text-muted small mb-0">Partner doctors document the checkup, and notes/prescriptions sync immediately back to your electronic medical record profile.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="zoom-in">
                <h3 class="text-dark fw-bold mb-3">Are you a Healthcare Provider or Clinic?</h3>
                <p class="text-muted mb-4">
                    Join LurnixeHealth's expanding partner network. Securely verify member profiles, sync prescriptions directly, and improve patient consult flows.
                </p>
                <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-success rounded-pill px-5 py-3 fw-bold text-white shadow">
                    <i class="fa-solid fa-handshake me-2"></i> Register Your Clinic
                </a>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
