<?php
/**
 * Lurnixe Health Card System - Contact Us Page
 * June 2026
 */
$page_title = "Contact Us";
require_once __DIR__ . '/includes/header.php';

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message) || empty($phone)) {
        $error_msg = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Please enter a valid email address.";
    } else {
        try {
            require_once __DIR__ . '/includes/db.php';
            $stmt = $pdo->prepare("INSERT INTO contact_inquiries (full_name, email, phone, message, status) VALUES (?, ?, ?, ?, 'new')");
            $stmt->execute([$name, $email, $phone, $message]);
            $success_msg = "Thank you, $name! Your inquiry has been submitted successfully. We will get back to you shortly.";
        } catch (PDOException $e) {
            error_log("Failed to save contact inquiry: " . $e->getMessage());
            $error_msg = "A database error occurred. Please try again later.";
        }
    }
}
?>

<section class="py-5 bg-light border-bottom">
    <div class="container text-center py-4" data-aos="fade-up">
        <h1 class="display-5 text-dark fw-bold mb-3">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none text-success">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container py-3">
        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-5" data-aos="fade-right">
                <h2 class="text-dark mb-4">Get In Touch</h2>
                <p class="text-muted mb-4">
                    Have questions about the Family Health Card or how to enroll your family members? Get in touch with our support desk or representative office.
                </p>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-light-success text-success rounded-3 p-3 fs-5">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h6 class="text-dark fw-bold mb-1">Our Location</h6>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($settings['contact_address'] ?? '123 Healthcare Blvd, Suite 400, NY 10016'); ?></p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-light-success text-success rounded-3 p-3 fs-5">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div>
                        <h6 class="text-dark fw-bold mb-1">Call Us</h6>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($settings['contact_phone'] ?? '+1 (800) 123-4567'); ?></p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="bg-light-success text-success rounded-3 p-3 fs-5">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <h6 class="text-dark fw-bold mb-1">Email Support</h6>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($settings['contact_email'] ?? 'support@lurnixehealth.com'); ?></p>
                    </div>
                </div>

                <?php if (!empty($settings['contact_whatsapp'])): ?>
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-light-success text-success rounded-3 p-3 fs-5">
                        <i class="fa-brands fa-whatsapp text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-dark fw-bold mb-1">WhatsApp Chat</h6>
                        <p class="text-muted small mb-0">
                            <a href="https://wa.me/<?php echo htmlspecialchars($settings['contact_whatsapp']); ?>" target="_blank" class="text-decoration-none text-success fw-bold">Chat Live with representative</a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-7" data-aos="fade-left">
                <div class="card p-4 border-0 shadow-sm rounded-3 bg-white">
                    <h3 class="text-dark mb-3">Send a Message</h3>
                    
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="" method="POST" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label small fw-bold text-dark">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-2" id="name" name="name" required placeholder="Enter full name">
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold text-dark">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-2" id="email" name="email" required placeholder="Enter email address">
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label small fw-bold text-dark">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control rounded-2" id="phone" name="phone" required placeholder="Enter 10-digit number" pattern="[0-9]{10}">
                                <div class="invalid-feedback">Please enter a valid 10-digit phone number.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="subject" class="form-label small fw-bold text-dark">Subject</label>
                                <input type="text" class="form-control rounded-2" id="subject" name="subject" placeholder="Enter message subject">
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label small fw-bold text-dark">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control rounded-2" id="message" name="message" rows="5" required placeholder="Type your inquiry here..."></textarea>
                                <div class="invalid-feedback">Please enter your message.</div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 w-100">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
