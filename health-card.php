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
                <style>
                .ref-card-wrapper {
                    width: 100%;
                    max-width: 450px;
                    margin: 0 auto 30px auto;
                    position: relative;
                    aspect-ratio: 856 / 540;
                    container-type: inline-size;
                }
                .ref-card {
                    width: 856px;
                    height: 540px;
                    background: #fdfdfd;
                    border-radius: 24px;
                    position: absolute;
                    top: 0; left: 0;
                    overflow: hidden;
                    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
                    box-sizing: border-box;
                    color: #1e293b;
                    border: 1px solid rgba(0, 0, 0, 0.05);
                    transform: scale(calc(100cqw / 856));
                    transform-origin: top left;
                    font-family: 'Outfit', sans-serif;
                    text-align: left;
                }
                
                /* Front Side Styling */
                .ref-front {
                    background: #ffffff;
                }
                
                .ref-svg-bg {
                    position: absolute;
                    right: 0; top: 0;
                    height: 100%; width: 450px;
                    z-index: 1;
                }
                
                .ref-header {
                    position: absolute;
                    top: 50px; left: 50px;
                    z-index: 2;
                    display: flex;
                    flex-direction: column;
                }
                .ref-header img {
                    width: 320px;
                    height: auto;
                    object-fit: contain;
                }
                .ref-header-tagline {
                    font-size: 16px;
                    font-weight: 600;
                    color: #1e293b;
                    margin-top: -5px;
                    letter-spacing: 0.5px;
                    padding-left: 75px;
                }
                
                .ref-info-container {
                    position: absolute;
                    top: 200px; left: 50px;
                    z-index: 2;
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                }
                .ref-info-row {
                    display: flex;
                    align-items: center;
                }
                .ref-info-icon {
                    width: 32px; height: 32px;
                    background: #0d6efd;
                    color: white;
                    border-radius: 50%;
                    display: flex; align-items: center; justify-content: center;
                    font-size: 14px;
                    margin-right: 15px;
                }
                .ref-info-label {
                    width: 100px;
                    font-size: 18px;
                    color: #475569;
                    font-weight: 500;
                }
                .ref-info-value {
                    font-size: 26px;
                    font-weight: 700;
                    color: #0f172a;
                }
                
                .ref-footer-icons {
                    position: absolute;
                    bottom: 40px; left: 50px;
                    z-index: 2;
                    display: flex;
                    gap: 30px;
                }
                .ref-footer-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 8px;
                }
                .ref-footer-item i {
                    font-size: 28px;
                    color: #1e293b;
                }
                .ref-footer-item span {
                    font-size: 14px;
                    font-weight: 600;
                    color: #1e293b;
                }
                
                .ref-right-content {
                    position: absolute;
                    top: 0; right: 0;
                    width: 380px; height: 100%;
                    z-index: 2;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
                
                .ref-tap-care {
                    margin-top: 40px;
                    margin-left: 150px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    color: white;
                }
                .ref-tap-care i {
                    font-size: 32px;
                    margin-bottom: 5px;
                    transform: rotate(45deg);
                }
                .ref-tap-care span {
                    font-size: 14px;
                    font-weight: 600;
                    letter-spacing: 1px;
                }
                
                .ref-qr-container {
                    margin-top: 70px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
                .ref-qr-box {
                    background: white;
                    padding: 15px;
                    border-radius: 20px;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                    margin-bottom: 15px;
                }
                .ref-qr-text {
                    color: white;
                    font-size: 14px;
                    font-weight: 600;
                    text-align: center;
                    line-height: 1.4;
                    letter-spacing: 0.5px;
                }
                
                .ref-website {
                    position: absolute;
                    bottom: 45px; right: 40px;
                    z-index: 3;
                    font-size: 16px;
                    font-weight: 700;
                    color: #1e293b;
                }
                
                /* Back Side Styling */
                .ref-back {
                    background: #ffffff;
                    background-image: 
                        radial-gradient(#f1f5f9 1px, transparent 1px),
                        radial-gradient(#f1f5f9 1px, transparent 1px);
                    background-position: 0 0, 10px 10px;
                    background-size: 20px 20px;
                }
        
                .ref-mag-stripe {
                    width: 100%;
                    height: 70px;
                    background: linear-gradient(to bottom, #1a1a1a, #0a0a0a);
                    position: absolute;
                    top: 45px;
                    left: 0;
                    z-index: 10;
                }
        
                .ref-back-header {
                    position: absolute;
                    top: 140px;
                    left: 0;
                    width: 100%;
                    padding: 0 45px;
                    box-sizing: border-box;
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                }
                
                .ref-back-header-title {
                    color: #0d6efd;
                    font-size: 15px;
                    font-weight: 800;
                    letter-spacing: 2px;
                    text-transform: uppercase;
                }
        
                .ref-back-content {
                    position: absolute;
                    top: 180px;
                    left: 45px;
                    right: 45px;
                    display: flex;
                    gap: 40px;
                }
        
                .ref-terms-container {
                    flex: 1.6;
                    background: #ffffff;
                    padding: 24px;
                    border-radius: 16px;
                    border-left: 5px solid #0d6efd;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
                }
        
                .ref-terms {
                    font-size: 13px;
                    line-height: 1.8;
                    color: #475569;
                    margin: 0;
                }
        
                .ref-terms h4 {
                    margin: 0 0 12px 0;
                    color: #0f172a;
                    font-size: 16px;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
        
                .ref-company-info {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                }
        
                .ref-info-block {
                    background: #ffffff;
                    padding: 24px;
                    border-radius: 16px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
                    border-top: 5px solid #20c997;
                    height: 100%;
                    box-sizing: border-box;
                }
        
                .ref-info-block h4 {
                    margin: 0 0 20px 0;
                    color: #0f172a;
                    font-size: 16px;
                    font-weight: 800;
                    text-transform: uppercase;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    letter-spacing: 0.5px;
                }
                
                .ref-info-block h4 i {
                    color: #20c997;
                    font-size: 18px;
                }
        
                .ref-info-row {
                    display: flex;
                    gap: 15px;
                    margin-bottom: 18px;
                    font-size: 13px;
                    color: #334155;
                    align-items: center;
                }
        
                .ref-info-row i {
                    color: #0d6efd;
                    font-size: 16px;
                    width: 20px;
                    text-align: center;
                }
                
                .ref-info-row strong {
                    color: #0f172a;
                    font-size: 12px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    display: block;
                    margin-bottom: 2px;
                }
        
                .ref-back-footer {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    background: #f8fafc;
                    padding: 20px 45px;
                    box-sizing: border-box;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-top: 1px solid #e2e8f0;
                }
                
                .ref-back-footer-text {
                    font-size: 12px;
                    color: #64748b;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                </style>
                
                <div class="ref-card-wrapper mb-4">
                    <div class="ref-card ref-front">
                        <svg class="ref-svg-bg" viewBox="0 0 450 540" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="mainGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#0dcaf0" />
                                    <stop offset="100%" stop-color="#20c997" />
                                </linearGradient>
                                <linearGradient id="backGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#0dcaf0" stop-opacity="0.5" />
                                    <stop offset="100%" stop-color="#0d6efd" stop-opacity="0.2" />
                                </linearGradient>
                            </defs>
                            <path d="M450,0 L200,0 C300,100 150,250 80,540 L450,540 Z" fill="url(#backGrad)" />
                            <path d="M450,0 L250,0 C380,180 80,300 0,540 L450,540 Z" fill="url(#mainGrad)" />
                        </svg>
                        
                        <div class="ref-header">
                            <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Company Logo" style="background: transparent;">
                            <div class="ref-header-tagline">Trusted Health Management</div>
                        </div>

                        <div class="ref-info-container">
                            <div class="ref-info-row">
                                <div class="ref-info-icon"><i class="fa-solid fa-user"></i></div>
                                <div class="ref-info-label">Name</div>
                                <div class="ref-info-value" style="text-transform: uppercase;">ABU LAHAB</div>
                            </div>
                            <div class="ref-info-row">
                                <div class="ref-info-icon"><i class="fa-solid fa-users"></i></div>
                                <div class="ref-info-label">Member ID</div>
                                <div class="ref-info-value">LXH-2026-0001</div>
                            </div>
                            <div class="ref-info-row">
                                <div class="ref-info-icon"><i class="fa-solid fa-shield-check"></i></div>
                                <div class="ref-info-label">Valid Upto</div>
                                <div class="ref-info-value">May 2026</div>
                            </div>
                        </div>

                        <div class="ref-footer-icons">
                            <div class="ref-footer-item">
                                <i class="fa-solid fa-stethoscope"></i>
                                <span>Stethoscope</span>
                            </div>
                            <div class="ref-footer-item">
                                <i class="fa-solid fa-user-doctor"></i>
                                <span>Doctor</span>
                            </div>
                            <div class="ref-footer-item">
                                <i class="fa-solid fa-people-group"></i>
                                <span>Family</span>
                            </div>
                            <div class="ref-footer-item">
                                <i class="fa-solid fa-compact-disc"></i>
                                <span>Disc</span>
                            </div>
                        </div>

                        <div class="ref-right-content">
                            <div class="ref-tap-care">
                                <i class="fa-solid fa-rss"></i>
                                <span>TAP TO CARE</span>
                            </div>
                            <div class="ref-qr-container">
                                <div class="ref-qr-box" id="qrcodePreview">
                                    <!-- Dummy QR Code SVG -->
                                    <svg viewBox="0 0 100 100" fill="#20c997" xmlns="http://www.w3.org/2000/svg" width="130" height="130">
                                        <rect width="100" height="100" fill="white"/>
                                        <rect width="20" height="20" fill="#20c997"/>
                                        <rect x="80" width="20" height="20" fill="#20c997"/>
                                        <rect y="80" width="20" height="20" fill="#20c997"/>
                                        <rect x="10" y="10" width="10" height="10" fill="white"/>
                                        <rect x="90" y="10" width="10" height="10" fill="white"/>
                                        <rect x="10" y="90" width="10" height="10" fill="white"/>
                                        <path d="M40 0 h20 v20 h-20 z M0 40 h20 v20 h-20 z M80 40 h20 v20 h-20 z M40 80 h20 v20 h-20 z M40 40 h20 v20 h-20 z"/>
                                    </svg>
                                </div>
                                <div class="ref-qr-text">
                                    SCAN FOR INSTANT<br>CARE DETAILS
                                </div>
                            </div>
                        </div>
                        
                        <div class="ref-website">www.lurnixehealth.com</div>
                    </div>
                </div>
                
                <div class="ref-card-wrapper">
                    <div class="ref-card ref-back">
                        <div class="ref-mag-stripe"></div>
                        
                        <div class="ref-back-header">
                            <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Company Logo" style="width: 160px; background: transparent;">
                            <div class="ref-back-header-title">Important Information</div>
                        </div>

                        <div class="ref-back-content">
                            <div class="ref-terms-container">
                                <div class="ref-terms">
                                    <h4>Terms & Conditions</h4>
                                    <p>This health card is the property of Lurnixe Health and must be returned upon request. It is strictly non-transferable and can only be used by the registered member whose details appear on the front. Use of this card is governed by the prevailing terms and conditions of the Lurnixe Healthcare network.</p>
                                    <p>Present this card at any partner hospital, clinic, or pharmacy to access your medical records instantly.</p>
                                    <p style="margin-top: 15px;"><strong>If found, please return to:</strong><br>Lurnixe Health Headquarters, 123 Healthcare Ave, Medical District, City.</p>
                                </div>
                            </div>
                            
                            <div class="ref-company-info">
                                <div class="ref-info-block">
                                    <h4><i class="fa-solid fa-headset"></i> Contact Support</h4>
                                    <div class="ref-info-row">
                                        <i class="fa-solid fa-phone"></i>
                                        <div>
                                            <strong>24/7 Helpline</strong>
                                            +1 (800) 123-4567
                                        </div>
                                    </div>
                                    <div class="ref-info-row">
                                        <i class="fa-solid fa-envelope"></i>
                                        <div>
                                            <strong>Support Email</strong>
                                            support@lurnixehealth.com
                                        </div>
                                    </div>
                                    <div class="ref-info-row" style="margin-bottom: 0;">
                                        <i class="fa-solid fa-globe"></i>
                                        <div>
                                            <strong>Website</strong>
                                            www.lurnixehealth.com
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ref-back-footer">
                            <div class="ref-back-footer-text">Property of Lurnixe Health</div>
                            <div class="ref-back-footer-text">Not valid as a national ID</div>
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
