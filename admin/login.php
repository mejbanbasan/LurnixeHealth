<?php
/**
 * Lurnixe Health Card System - Secure Admin Login
 * June 2026
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    header("Location: " . BASE_URL . "admin/dashboard.php");
    exit;
}

// Remember Me Cookie Autologin check
if (!empty($_COOKIE['remember_admin']) && !isset($_SESSION['admin_id'])) {
    $parts = explode('|', $_COOKIE['remember_admin']);
    if (count($parts) === 2) {
        $cookie_admin_id = $parts[0];
        $cookie_signature = $parts[1];
        
        // Validate signature
        $expected_signature = hash_hmac('sha256', $cookie_admin_id, 'LURNIXE_COOKIE_KEY_2026');
        if (hash_equals($expected_signature, $cookie_signature)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ? AND status = 'active'");
                $stmt->execute([$cookie_admin_id]);
                $admin = $stmt->fetch();
                
                if ($admin) {
                    // Log in the user
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_role'] = $admin['role'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['last_activity'] = time();
                    
                    log_activity($pdo, $admin['id'], 'login_success_remember', null, 'Autologin via Remember Me cookie');
                    
                    header("Location: " . BASE_URL . "admin/dashboard.php");
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Remember me autologin error: " . $e->getMessage());
            }
        }
    }
}

$error_msg = "";
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Form Submit Handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // 1. Verify CSRF Token
    if (!verify_csrf_token($csrf_token)) {
        $error_msg = "Security token mismatch. Please reload the page and try again.";
    } else {
        try {
            // 2. Lockout Check: Check failed attempts from this IP in the last 15 minutes
            $stmt = $pdo->prepare("SELECT COUNT(*) as failed_count FROM activity_logs WHERE action = 'login_failed' AND ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)");
            $stmt->bindValue(1, $client_ip, PDO::PARAM_STR);
            $stmt->bindValue(2, COOLDOWN_MINUTES, PDO::PARAM_INT);
            $stmt->execute();
            $lockout = $stmt->fetch();
            
            if ($lockout && $lockout['failed_count'] >= MAX_LOGIN_ATTEMPTS) {
                $error_msg = "Too many failed attempts. Login is locked for this device. Please try again in 15 minutes.";
            } else {
                // 3. Query admin record
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();
                
                if ($admin && password_verify($password, $admin['password'])) {
                    // Check if account is active
                    if ($admin['status'] !== 'active') {
                        $error_msg = "Your account is currently inactive. Please contact Super Admin.";
                    } else {
                        // Success: Regenerate session ID to prevent session fixation
                        session_regenerate_id(true);
                        
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_name'] = $admin['name'];
                        $_SESSION['admin_role'] = $admin['role'];
                        $_SESSION['admin_email'] = $admin['email'];
                        $_SESSION['last_activity'] = time();
                        
                        log_activity($pdo, $admin['id'], 'login_success', null, "Admin logged in");
                        
                        // Handle Remember Me Cookie
                        if ($remember) {
                            $signature = hash_hmac('sha256', $admin['id'], 'LURNIXE_COOKIE_KEY_2026');
                            $cookie_val = $admin['id'] . '|' . $signature;
                            setcookie('remember_admin', $cookie_val, time() + (30 * 24 * 3600), '/', '', false, true); // 30 Days
                        }
                        
                        header("Location: " . BASE_URL . "admin/dashboard.php");
                        exit;
                    }
                } else {
                    // Failed attempt
                    $error_msg = "Invalid email address or password.";
                    $admin_id_log = $admin ? $admin['id'] : 1; // Log under target admin or default admin 1
                    
                    // Log failed attempt
                    log_activity($pdo, $admin_id_log, 'login_failed', null, "Failed login attempt for email: $email");
                }
            }
        } catch (PDOException $e) {
            error_log("Login database error: " . $e->getMessage());
            $error_msg = "A database error occurred. Please try again later.";
        }
    }
}

// Generate CSRF token for the login form
$csrf = get_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorized Admin Login | LurnixeHealth</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Style CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css?v=1.0" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #1A5276 0%, #111c24 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            border: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <!-- Brand Logo Header -->
            <div class="text-center mb-4">
                <a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none d-inline-flex align-items-center gap-2">
                    <div class="brand-logo-container bg-success" style="width: 45px; height: 45px;">
                        <span class="brand-icon text-white"><i class="fa-solid fa-heart-pulse"></i></span>
                    </div>
                    <span class="brand-name text-white fs-3">Lurnixe<span class="text-success">Health</span></span>
                </a>
            </div>
            
            <div class="card login-card bg-white p-4 p-sm-5">
                <div class="text-center mb-4">
                    <h3 class="text-dark fw-bold mb-1">Admin Sign In</h3>
                    <p class="text-muted small">Access dashboard using authorized credentials.</p>
                </div>
                
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo $error_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error']) && $_GET['error'] === 'timeout'): ?>
                    <div class="alert alert-warning alert-dismissible fade show small" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> Session expired due to inactivity. Please sign in again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-dark">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input type="email" class="form-control bg-light border-start-0 rounded-end-2" id="email" name="email" required placeholder="name@lurnixehealth.com">
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold text-dark">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-key text-muted"></i></span>
                            <input type="password" class="form-control bg-light border-start-0 rounded-end-2" id="password" name="password" required placeholder="••••••••">
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label text-muted" for="remember">
                                Remember this device (30 days)
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success rounded-pill w-100 py-3 mb-2">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Log In
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none text-success small">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back to Homepage
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Enable Bootstrap form verification
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
</body>
</html>
