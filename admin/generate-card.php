<?php
/**
 * Lurnixe Health Card System - Premium PVC Health Card Generator
 * June 2026
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce login validation (basic check)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id']) && !isset($_SESSION['member_id'])) {
    die("Error: Unauthorized access.");
}

$member_id = sanitize_input($_GET['id'] ?? '');

if (empty($member_id)) {
    die("Error: No member ID provided.");
}

try {
    // Fetch site settings from DB for logo
    $settings = [];
    $settings_stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    while ($row = $settings_stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    $logo_path = $settings['logo_path'] ?? '';

    // Fetch member profile details
    $stmt = $pdo->prepare("SELECT * FROM members WHERE member_id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch();
    
    if (!$member) {
        die("Error: Member not found.");
    }
} catch (PDOException $e) {
    error_log("Failed to load member for PDF card: " . $e->getMessage());
    die("Error: Database operation failed.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lurnixe Card - <?php echo htmlspecialchars($member['member_id']); ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
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
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            color: #1e293b;
            border: 1px solid rgba(0, 0, 0, 0.05);
            font-family: 'Outfit', sans-serif;
            text-align: left;
            margin-bottom: 30px;
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

        @media print {
            .no-print { display: none !important; }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page { margin: 10mm; }
            .ref-card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-bottom: 20px !important;
                transform: scale(0.9);
                transform-origin: top left;
            }
        }
    </style>
</head>
<body>

<div class="card-container">
    <!-- FRONT SIDE -->
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
                <div class="ref-info-value" style="text-transform: uppercase;"><?php echo htmlspecialchars($member['name']); ?></div>
            </div>
            <div class="ref-info-row">
                <div class="ref-info-icon"><i class="fa-solid fa-users"></i></div>
                <div class="ref-info-label">Member ID</div>
                <div class="ref-info-value"><?php echo htmlspecialchars($member['member_id']); ?></div>
            </div>
            <div class="ref-info-row">
                <div class="ref-info-icon"><i class="fa-solid fa-shield-check"></i></div>
                <div class="ref-info-label">Valid Upto</div>
                <div class="ref-info-value"><?php echo date('M Y', strtotime($member['validity_date'])); ?></div>
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
                <div class="ref-qr-box" id="qrcode"></div>
                <div class="ref-qr-text">
                    SCAN FOR INSTANT<br>CARE DETAILS
                </div>
            </div>
        </div>
        
        <div class="ref-website">www.lurnixehealth.com</div>
    </div>
    
    <!-- BACK SIDE -->
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

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate QR Code
            const qrUrl = "<?php echo BASE_URL; ?>member.php?id=<?php echo urlencode($member['member_id']); ?>";
            new QRCode(document.getElementById("qrcode"), {
                text: qrUrl,
                width: 140,
                height: 140,
                colorDark : "#0f172a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
            
            // Trigger browser's native print dialog automatically
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>