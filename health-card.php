<?php
/**
 * Lurnixe Health Card System - Health Card Information
 * June 2026
 */
$page_title = "Family Health Card";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light border-bottom">
    <div class="container text-center py-4" data-aos="fade-up">
        <h1 class="display-5 text-dark fw-bold mb-3">Family Health Card System</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none text-success">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Health Card</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container py-3">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="text-dark mb-4">A Credit Card Sized Lifeline for Your Family</h2>
                <p class="lead text-muted">
                    Lurnixe Family Health Card is designed to simplify and protect your life. Printed on standard CR80 format, it sits in your wallet and contains instant emergency links.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-6 col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <span class="text-success fs-5"><i class="fa-solid fa-circle-check"></i></span>
                            <div>
                                <h6 class="text-dark fw-bold mb-1">Standard Size</h6>
                                <p class="text-muted small">85.6mm x 53.98mm credit card format.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <span class="text-success fs-5"><i class="fa-solid fa-circle-check"></i></span>
                            <div>
                                <h6 class="text-dark fw-bold mb-1">Double Sided</h6>
                                <p class="text-muted small">QR code, photo front; medical history back.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <span class="text-success fs-5"><i class="fa-solid fa-circle-check"></i></span>
                            <div>
                                <h6 class="text-dark fw-bold mb-1">QR Enabled</h6>
                                <p class="text-muted small">Instant scan lookup to check verification live.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="d-flex align-items-start gap-2">
                            <span class="text-success fs-5"><i class="fa-solid fa-circle-check"></i></span>
                            <div>
                                <h6 class="text-dark fw-bold mb-1">Family Support</h6>
                                <p class="text-muted small">Link dependent children and elderly relatives.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <!-- Card design preview -->
                <div class="card p-4 bg-primary text-white border-0 shadow-lg rounded-4 text-start position-relative overflow-hidden mb-3" style="min-height: 250px; background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-hover) 100%);">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="fw-bold mb-0 text-white d-flex align-items-center"><img src="<?php echo BASE_URL; ?>assets/images/qr_logo.png" alt="LurnixeHealth Logo" style="max-height: 22px;" class="me-2">LurnixeHealth</h5>
                            <small class="opacity-75" style="font-size: 0.7rem; letter-spacing: 1px;">FAMILY HEALTH CARD</small>
                        </div>
                        <span class="badge bg-success px-3 py-2 rounded-pill small">ACTIVE</span>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-white rounded p-1" style="width: 70px; height: 80px;">
                            <!-- Placeholder avatar -->
                            <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                <i class="fa-solid fa-user fs-3"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0 text-white font-heading fw-bold">John Doe</h4>
                            <span class="font-code text-white-50 d-block fs-6" style="letter-spacing: 1px;">ID: LFC000124</span>
                            <span class="badge bg-light text-primary mt-1">O+ Positive</span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-end mt-auto">
                        <div>
                            <small class="d-block opacity-50" style="font-size: 0.65rem;">VALID TILL</small>
                            <span class="fw-semibold small">15 Jun 2027</span>
                        </div>
                        <div class="bg-white bg-opacity-75 p-2 rounded-3 shadow-sm border border-white-50 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; backdrop-filter: blur(4px);">
                            <i class="fa-solid fa-qrcode text-dark fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Card Status Details -->
<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-dark">Card Status Lifecycle</h2>
            <div class="mx-auto bg-success mt-2 mb-3" style="width: 60px; height: 3px;"></div>
            <p class="text-muted">Cards follow a strict status cycle maintained by authorized administrators.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="p-4 rounded-3 border text-center h-100">
                    <span class="badge bg-success px-3 py-2 rounded-pill fs-6 mb-3">ACTIVE</span>
                    <h5 class="text-dark mb-2">Active State</h5>
                    <p class="text-muted small mb-0">The health card is valid, within its validity dates, and is accepted at partner clinics.</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="p-4 rounded-3 border text-center h-100">
                    <span class="badge bg-warning px-3 py-2 rounded-pill fs-6 mb-3">EXPIRED</span>
                    <h5 class="text-dark mb-2">Expired State</h5>
                    <p class="text-muted small mb-0">The card's validity duration (1/2/3 years) has lapsed. Renewal is required by admin.</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="p-4 rounded-3 border text-center h-100">
                    <span class="badge bg-warning px-3 py-2 rounded-pill fs-6 mb-3 text-dark">SUSPENDED</span>
                    <h5 class="text-dark mb-2">Suspended State</h5>
                    <p class="text-muted small mb-0">Temporarily suspended by administration due to payment issues or request. Can reactivate.</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="p-4 rounded-3 border text-center h-100">
                    <span class="badge bg-danger px-3 py-2 rounded-pill fs-6 mb-3">DEACTIVATED</span>
                    <h5 class="text-dark mb-2">Deactivated State</h5>
                    <p class="text-muted small mb-0">Permanently disabled by administrative command. Requires Super Admin to re-register.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
