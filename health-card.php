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

<!-- Professional Health Card Design Section -->
<section class="py-5 py-md-8">
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
            
            <div class="col-lg-6 text-center" data-aos="fade-left">
                <!-- Professional PVC Card Design (HTML + CSS) -->
                <div class="pvc-card-wrapper mb-4">
                    <!-- Card Front -->
                    <div class="pvc-card pvc-front">
                        <div class="pvc-shape-1"></div>
                        <div class="pvc-shape-2"></div>
                        
                        <div class="pvc-header">
                            <div class="pvc-logo">
                                <div class="pvc-logo-icon">
                                    <i class="fa-solid fa-heart-pulse"></i>
                                </div>
                                <div class="pvc-logo-text">
                                    <h2 class="pvc-brand">LURNIXE</h2>
                                    <p class="pvc-brand-sub">HEALTHCARE</p>
                                </div>
                            </div>
                            <div class="pvc-member-id-badge">ID: LFC000521</div>
                        </div>

                        <div class="pvc-body">
                            <div class="pvc-info">
                                <h1 class="pvc-user-name">JOHN ALEXANDER DOE</h1>
                                <div class="pvc-attributes">
                                    <div class="pvc-attr">
                                        <p class="pvc-attr-label">Blood Group</p>
                                        <p class="pvc-attr-val">O+</p>
                                    </div>
                                    <div class="pvc-attr">
                                        <p class="pvc-attr-label">Gender</p>
                                        <p class="pvc-attr-val">Male</p>
                                    </div>
                                    <div class="pvc-attr">
                                        <p class="pvc-attr-label">DOB</p>
                                        <p class="pvc-attr-val">15 May 1990</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pvc-qr-container">
                                <!-- Temporary static SVG for preview -->
                                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="80" height="80" fill="white"/>
                                    <rect width="16" height="16" fill="#1A5276"/>
                                    <rect x="64" width="16" height="16" fill="#1A5276"/>
                                    <rect y="64" width="16" height="16" fill="#1A5276"/>
                                    <rect x="8" y="8" width="8" height="8" fill="white" opacity="0.5"/>
                                    <rect x="72" y="8" width="8" height="8" fill="white" opacity="0.5"/>
                                    <rect x="8" y="72" width="8" height="8" fill="white" opacity="0.5"/>
                                </svg>
                            </div>
                        </div>

                        <div class="pvc-footer">
                            <span class="pvc-footer-text">www.lurnixehealth.com</span>
                            <span class="pvc-validity">Valid Upto: 06/2027</span>
                        </div>
                    </div>
                </div>
                
                <!-- Card Back Preview -->
                <div class="pvc-card-wrapper">
                    <div class="pvc-card pvc-back">
                        <div class="pvc-shape-1" style="background: linear-gradient(135deg, rgba(52, 152, 219, 0.2) 0%, rgba(41, 128, 185, 0.05) 100%); left: -50px; right: auto; filter: blur(40px);"></div>
                        
                        <div class="pvc-mag-stripe"></div>
                        
                        <div class="pvc-back-content">
                            <div class="pvc-back-info">
                                <div class="pvc-terms">
                                    <strong>Terms & Conditions</strong><br>
                                    This card remains the property of Lurnixe Health and must be returned upon request. It is non-transferable and can only be used by the registered member. Use of this card is governed by the prevailing terms and conditions of the Lurnixe Healthcare network. In case of emergency, please present this card to authorized medical personnel.<br><br>
                                    If found, please return to:<br>
                                    Lurnixe Health Headquarters, 123 Healthcare Ave, Medical District.
                                </div>
                                <div class="pvc-support">
                                    <h4>Emergency Info</h4>
                                    <div class="pvc-support-row">
                                        <i class="fa-solid fa-phone"></i>
                                        <div><strong>24/7 Helpline:</strong><br>+91-9876543210</div>
                                    </div>
                                    <div class="pvc-support-row">
                                        <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                        <div><strong>Allergies:</strong><br>None Known</div>
                                    </div>
                                    <div class="pvc-support-row mt-3 text-center d-block">
                                        <span style="font-family: 'Courier New', monospace; font-size: 16px; letter-spacing: 2px; color: #fff;">LFC000521</span>
                                    </div>
                                </div>
                            </div>
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
