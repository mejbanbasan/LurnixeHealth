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
        body {
            background-color: #f0f2f5;
            margin: 0;
            padding: 40px 0;
            font-family: 'Outfit', sans-serif;
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
            body { padding: 20px 0; }
            .card-container { zoom: calc(100vw / 900); }
        }

        .ref-card {
            width: 856px;
            height: 540px;
            background: #ffffff;
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            user-select: none;
            -webkit-user-select: none;
            color: #1a202c;
            margin: 0 auto;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Front Side Styling */
        .ref-front {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .ref-front::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: 
                radial-gradient(circle at 100% 0%, rgba(32, 201, 151, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 0% 100%, rgba(13, 202, 240, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        .ref-front .right-shape {
            position: absolute;
            top: -20%;
            right: -10%;
            width: 45%;
            height: 140%;
            background: linear-gradient(135deg, #0d6efd 0%, #20c997 100%);
            transform: rotate(-10deg);
            border-radius: 60px;
            z-index: 1;
            box-shadow: -15px 0 40px rgba(32, 201, 151, 0.2);
        }

        .ref-front .right-shape-overlay {
            position: absolute;
            top: -10%;
            right: -15%;
            width: 45%;
            height: 120%;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%);
            transform: rotate(-10deg);
            border-radius: 60px;
            z-index: 2;
        }

        .ref-header {
            position: absolute;
            top: 40px;
            left: 45px;
            z-index: 3;
        }

        .ref-logo-icon {
            width: 250px;
            display: flex;
            align-items: center;
        }
        
        .ref-logo-icon img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .ref-badge {
            position: absolute;
            top: 45px;
            right: 45px;
            z-index: 3;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ref-details {
            position: absolute;
            top: 150px;
            left: 45px;
            z-index: 3;
            width: 50%;
        }

        .ref-name-block {
            margin-bottom: 35px;
        }

        .ref-label {
            font-size: 13px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .ref-value.name {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .ref-info-grid {
            display: flex;
            gap: 20px;
        }

        .ref-info-box {
            background: #ffffff;
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            min-width: 140px;
        }

        .ref-info-box .ref-label {
            font-size: 11px;
            margin-bottom: 4px;
        }

        .ref-info-box .ref-value {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .ref-bottom-strip {
            position: absolute;
            bottom: 40px;
            left: 45px;
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f1f5f9;
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .ref-bottom-strip i {
            color: #0d6efd;
            font-size: 16px;
        }

        .ref-bottom-strip span {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .ref-right-content {
            position: absolute;
            top: 0;
            right: 0;
            width: 350px;
            height: 100%;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .ref-qr-wrapper {
            background: #ffffff;
            padding: 16px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 4px solid rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ref-qr-text {
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            line-height: 1.5;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        <div class="right-shape"></div>
        <div class="right-shape-overlay"></div>
        
        <div class="ref-header">
            <div class="ref-logo-icon">
                <?php if (!empty($logo_path) && file_exists(__DIR__ . '/../' . $logo_path)): ?>
                    <img src="<?php echo BASE_URL . $logo_path; ?>" alt="Company Logo">
                <?php else: ?>
                    <h2 class="ref-brand-title" style="margin:0; font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; font-size: 28px;">Lurnixe<span style="color: #0d6efd;">Health</span></h2>
                <?php endif; ?>
            </div>
        </div>

        <div class="ref-badge">
            <i class="fa-solid fa-notes-medical"></i> Health Membership
        </div>

        <div class="ref-details">
            <div class="ref-name-block">
                <div class="ref-label">Member Name</div>
                <div class="ref-value name"><?php echo htmlspecialchars($member['name']); ?></div>
            </div>
            
            <div class="ref-info-grid">
                <div class="ref-info-box">
                    <div class="ref-label">Member ID</div>
                    <div class="ref-value"><?php echo htmlspecialchars($member['member_id']); ?></div>
                </div>
                <div class="ref-info-box">
                    <div class="ref-label">Valid Upto</div>
                    <div class="ref-value"><?php echo date('M Y', strtotime($member['validity_date'])); ?></div>
                </div>
            </div>
        </div>

        <div class="ref-bottom-strip">
            <i class="fa-solid fa-circle-check"></i>
            <span>Active Healthcare Network Member</span>
        </div>

        <div class="ref-right-content">
            <div class="ref-qr-wrapper" id="qrcode"></div>
            <div class="ref-qr-text">
                SCAN FOR INSTANT<br>CARE DETAILS
            </div>
        </div>
    </div>
    
    <!-- BACK SIDE -->
    <div class="ref-card ref-back">
        <div class="ref-mag-stripe"></div>
        
        <div class="ref-back-header">
            <div class="ref-logo-icon" style="width: 160px;">
                <?php if (!empty($logo_path) && file_exists(__DIR__ . '/../' . $logo_path)): ?>
                    <img src="<?php echo BASE_URL . $logo_path; ?>" alt="Company Logo" style="width: 100%; height: auto; object-fit: contain;">
                <?php else: ?>
                    <h2 class="ref-brand-title" style="margin:0; font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; font-size: 20px;">Lurnixe<span style="color: #0d6efd;">Health</span></h2>
                <?php endif; ?>
            </div>
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