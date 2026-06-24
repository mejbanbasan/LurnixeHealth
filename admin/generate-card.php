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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            background-color: #f0f2f5;
            margin: 0;
            padding: 40px 0;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .card-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            width: 100%;
        }

        @media screen and (max-width: 900px) {
            body {
                padding: 20px 0;
            }
            .card-container {
                zoom: calc(100vw / 900);
            }
        }

        .ref-card {
            width: 856px;
            height: 540px;
            background: #ffffff;
            border-radius: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            box-sizing: border-box;
            user-select: none;
            -webkit-user-select: none;
            color: #1a1a1a;
            margin: 0 auto;
        }

        /* Front Side Styling */
        .ref-front .right-shape {
            position: absolute;
            top: 0;
            right: 0;
            width: 45%;
            height: 100%;
            background: linear-gradient(135deg, #0dcaf0 0%, #20c997 100%);
            clip-path: polygon(25% 0, 100% 0, 100% 100%, 0% 100%, 15% 65%, 5% 35%);
            z-index: 1;
        }

        .ref-front .right-shape-overlay {
            position: absolute;
            top: 0;
            right: 0;
            width: 45%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            clip-path: polygon(35% 0, 100% 0, 100% 100%, 10% 100%, 25% 65%, 15% 35%);
            z-index: 0;
        }

        .ref-header {
            position: absolute;
            top: 40px;
            left: 45px;
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 2;
        }

        .ref-logo-icon {
            height: 120px;
            display: flex;
            align-items: center;
            max-width: 350px;
        }
        
        .ref-logo-icon img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .ref-details {
            position: absolute;
            top: 170px;
            left: 45px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            z-index: 2;
            width: 50%;
        }

        .ref-row {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .ref-icon-box {
            width: 40px;
            height: 40px;
            background: #0dcaf0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            flex-shrink: 0;
        }

        .ref-label {
            font-size: 16px;
            color: #555;
            width: 100px;
            font-weight: 500;
        }

        .ref-value {
            font-size: 22px;
            font-weight: 700;
            color: #111;
        }
        
        .ref-value.name {
            font-size: 28px;
            text-transform: uppercase;
        }

        .ref-bottom-icons {
            position: absolute;
            bottom: 40px;
            left: 45px;
            display: flex;
            gap: 35px;
            z-index: 2;
        }

        .ref-bottom-icon {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .ref-bottom-icon i {
            font-size: 28px;
            color: #333;
        }

        .ref-bottom-icon span {
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }

        .ref-right-content {
            position: absolute;
            top: 0;
            right: 0;
            width: 350px;
            height: 100%;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .ref-contactless {
            position: absolute;
            top: 40px;
            right: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
            gap: 5px;
        }

        .ref-contactless i {
            font-size: 32px;
            transform: rotate(90deg);
        }

        .ref-contactless span {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .ref-qr-wrapper {
            background: #fff;
            padding: 12px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .ref-qr-text {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            margin-top: 15px;
            line-height: 1.4;
        }

        .ref-website {
            position: absolute;
            bottom: 40px;
            right: 40px;
            font-size: 18px;
            font-weight: 600;
            color: #111;
            z-index: 3;
        }

        /* Back Side Styling */
        .ref-back {
            background: #fdfdfd;
            background-image: radial-gradient(#e5e5e5 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .ref-mag-stripe {
            width: 100%;
            height: 65px;
            background: #111;
            position: absolute;
            top: 40px;
            left: 0;
            z-index: 10;
        }

        .ref-back-header {
            position: absolute;
            top: 120px;
            left: 0;
            width: 100%;
            padding: 0 45px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .ref-back-header-title {
            color: #0dcaf0;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .ref-back-content {
            position: absolute;
            top: 150px;
            left: 45px;
            right: 45px;
            display: flex;
            gap: 50px;
        }

        .ref-terms-container {
            flex: 1.5;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #20c997;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .ref-terms {
            font-size: 13px;
            line-height: 1.7;
            color: #555;
            margin: 0;
        }

        .ref-terms h4 {
            margin: 0 0 10px 0;
            color: #111;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .ref-company-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .ref-info-block {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border-top: 4px solid #0dcaf0;
        }

        .ref-info-block h4 {
            margin: 0 0 15px 0;
            color: #111;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .ref-info-block h4 i {
            color: #0dcaf0;
        }

        .ref-info-row {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 14px;
            color: #444;
            align-items: flex-start;
        }

        .ref-info-row i {
            color: #20c997;
            font-size: 16px;
            margin-top: 3px;
            width: 16px;
            text-align: center;
        }

        .ref-back-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #f1f3f5;
            padding: 15px 45px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e9ecef;
        }
        
        .ref-back-footer-text {
            font-size: 12px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page {
                margin: 10mm;
            }
            .ref-card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-bottom: 20px !important;
                /* Optional scaling to ensure it fits nicely on paper */
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
        <div class="right-shape-overlay"></div>
        <div class="right-shape"></div>
        
        <div class="ref-header">
            <div class="ref-logo-icon">
                <?php if (!empty($logo_path) && file_exists(__DIR__ . '/../' . $logo_path)): ?>
                    <img src="<?php echo BASE_URL . $logo_path; ?>" alt="Company Logo">
                <?php else: ?>
                    <h2 class="ref-brand-title" style="margin:0;">Lurnixe<span>Health</span></h2>
                <?php endif; ?>
            </div>
        </div>

        <div class="ref-details">
            <div class="ref-row">
                <div class="ref-icon-box"><i class="fa-solid fa-user"></i></div>
                <div class="ref-label">Name</div>
                <div class="ref-value name"><?php echo htmlspecialchars($member['name']); ?></div>
            </div>
            <div class="ref-row">
                <div class="ref-icon-box"><i class="fa-solid fa-id-card"></i></div>
                <div class="ref-label">Member ID</div>
                <div class="ref-value"><?php echo htmlspecialchars($member['member_id']); ?></div>
            </div>
            <div class="ref-row">
                <div class="ref-icon-box"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="ref-label">Valid Upto</div>
                <div class="ref-value"><?php echo date('M Y', strtotime($member['validity_date'])); ?></div>
            </div>
        </div>

        <div class="ref-bottom-icons">
            <div class="ref-bottom-icon">
                <i class="fa-solid fa-stethoscope"></i>
                <span>Stethoscope</span>
            </div>
            <div class="ref-bottom-icon">
                <i class="fa-solid fa-user-doctor"></i>
                <span>Doctor</span>
            </div>
            <div class="ref-bottom-icon">
                <i class="fa-solid fa-users"></i>
                <span>Family</span>
            </div>
            <div class="ref-bottom-icon">
                <i class="fa-solid fa-compact-disc"></i>
                <span>Disc</span>
            </div>
        </div>

        <div class="ref-right-content">
            <div class="ref-contactless">
                <i class="fa-solid fa-wifi"></i>
                <span>TAP TO CARE</span>
            </div>
            
            <div class="ref-qr-wrapper" id="qrcode"></div>
            
            <div class="ref-qr-text">
                SCAN FOR INSTANT<br>CARE DETAILS
            </div>
        </div>

        <div class="ref-website">www.lurnixehealth.com</div>
    </div>
    
    <!-- BACK SIDE -->
    <div class="ref-card ref-back">
        <div class="ref-mag-stripe"></div>
        
        <div class="ref-back-header">
            <div class="ref-back-header-title">Important Information</div>
        </div>

        <div class="ref-back-content">
            <div class="ref-terms-container">
                <div class="ref-terms">
                    <h4>Terms & Conditions</h4>
                    <p>This health card is the property of Lurnixe Health and must be returned upon request. It is strictly non-transferable and can only be used by the registered member whose details appear on the front. Use of this card is governed by the prevailing terms and conditions of the Lurnixe Healthcare network.</p>
                    <p>Present this card at any partner hospital or clinic to access your medical records instantly.</p>
                    <p><strong>If found, please return to:</strong><br>Lurnixe Health Headquarters, 123 Healthcare Ave, Medical District, City.</p>
                </div>
            </div>
            
            <div class="ref-company-info">
                <div class="ref-info-block">
                    <h4><i class="fa-solid fa-headset"></i> Contact Us</h4>
                    <div class="ref-info-row">
                        <i class="fa-solid fa-phone"></i>
                        <div>
                            <strong>24/7 Helpline:</strong><br>
                            +1 (800) 123-4567
                        </div>
                    </div>
                    <div class="ref-info-row">
                        <i class="fa-solid fa-envelope"></i>
                        <div>
                            <strong>Support Email:</strong><br>
                            support@lurnixehealth.com
                        </div>
                    </div>
                    <div class="ref-info-row" style="margin-bottom: 0;">
                        <i class="fa-solid fa-globe"></i>
                        <div>
                            <strong>Website:</strong><br>
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
                colorDark : "#20c997",
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