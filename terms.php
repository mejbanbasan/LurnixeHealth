<?php
/**
 * Lurnixe Health Card System - Terms & Conditions
 * June 2026
 */
$page_title = "Terms & Conditions";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light border-bottom">
    <div class="container text-center py-4" data-aos="fade-up">
        <h1 class="display-5 text-dark fw-bold mb-3">Terms & Conditions</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none text-success">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Terms & Conditions</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container py-3" style="max-width: 850px;" data-aos="fade-up">
        <div class="bg-white p-5 rounded border shadow-sm">
            <h2 class="text-dark mb-4 border-bottom pb-2">Platform Use Terms</h2>
            
            <p class="text-muted">
                Welcome to the Lurnixe Family Health Card System. By enrolling, utilizing, or scanning our health cards, you agree to comply with the terms and guidelines detailed below.
            </p>
            
            <h5 class="text-dark mt-4 mb-3 fw-bold">1. General Information Disclaimer</h5>
            <p class="text-muted small">
                All health profiles and records stored on the platform are provided for general health awareness and informational purposes only. The digital health card is not intended as a substitute for professional medical advice, clinical diagnosis, or immediate emergency treatment. Always seek the advice of a qualified physician with any questions regarding a medical condition.
            </p>
            
            <h5 class="text-dark mt-4 mb-3 fw-bold">2. System Modifications</h5>
            <p class="text-muted small">
                Lurnixe Health reserves the right to modify, upgrade, suspend, or discontinue any feature, service, pricing tier, or database access channel at any time without prior notification.
            </p>
            
            <h5 class="text-dark mt-4 mb-3 fw-bold">3. User Responsibility & Privacy</h5>
            <p class="text-muted small">
                Registered members are solely responsible for maintaining the confidentiality of their medical profile credentials. You authorize administrators to update your health card details and declare that all details provided during offline registration are accurate.
            </p>
            
            <h5 class="text-dark mt-4 mb-3 fw-bold">4. Card Property & Ownership</h5>
            <p class="text-muted small">
                Printed physical PVC cards and downloadable digital PDF cards are the sole property of Lurnixe Health. Cards are subject to suspension or deactivation if payment verification fails, or if administrative policies are breached.
            </p>
            
            <h5 class="text-dark mt-4 mb-3 fw-bold">5. QR Scan Data Limitation</h5>
            <p class="text-muted small">
                To protect user privacy, the public QR scan view shows a restricted set of information (Name, Photo, Member ID, Status, Valid Till, Emergency Contact) and masks sensitive clinical notes, address details, and allergies. Authorized medical partners may view additional details via secured dashboard channels.
            </p>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
