<?php
/**
 * Lurnixe Health Card System - Premium Redesigned PDF Health Card Generator
 * June 2026
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Turn off error reporting to prevent deprecation warnings from corrupting the PDF stream
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce login validation
check_auth();

$member_id = sanitize_input($_GET['id'] ?? '');

if (empty($member_id)) {
    die("Error: No member ID provided.");
}

try {
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

// ----------------------------------------------------
// VECTOR ICON DRAWING HELPER
// ----------------------------------------------------
function DrawCardIcon($pdf, $type, $x, $y, $size = 3.0) {
    $pdf->SetDrawColor(15, 110, 86); // teal #0f6e56
    $pdf->SetLineWidth(0.2);
    $pdf->SetFillColor(15, 110, 86);
    
    if ($type === 'calendar') {
        // Calendar Grid
        $pdf->Rect($x, $y + 0.6, $size, $size - 0.6, 'D');
        $pdf->Line($x, $y + 1.3, $x + $size, $y + 1.3);
        // Binder rings
        $pdf->Line($x + 0.7, $y, $x + 0.7, $y + 0.9);
        $pdf->Line($x + $size - 0.7, $y, $x + $size - 0.7, $y + 0.9);
    } elseif ($type === 'phone') {
        // Telephone Handset outline
        $pdf->Line($x + 0.4, $y + 0.4, $x + 0.4, $y + $size - 0.4);
        $pdf->Line($x + 0.4, $y + 0.4, $x + 1.0, $y + 0.7);
        $pdf->Line($x + 0.4, $y + $size - 0.4, $x + 1.0, $y + $size - 0.7);
    } elseif ($type === 'envelope') {
        // Letter Envelope
        $pdf->Rect($x, $y + 0.5, $size, $size - 1.0, 'D');
        $pdf->Line($x, $y + 0.5, $x + $size / 2, $y + $size / 2 + 0.1);
        $pdf->Line($x + $size, $y + 0.5, $x + $size / 2, $y + $size / 2 + 0.1);
    } elseif ($type === 'pin') {
        // Teardrop location pin
        $pdf->Circle($x + $size/2, $y + $size/3 + 0.1, $size/3, 0, 360, 'D');
        $pdf->Polygon(array(
            $x + $size/2 - $size/3, $y + $size/3 + 0.1,
            $x + $size/2 + $size/3, $y + $size/3 + 0.1,
            $x + $size/2, $y + $size - 0.1
        ), 'D');
        $pdf->Circle($x + $size/2, $y + $size/3 + 0.1, 0.4, 0, 360, 'F');
    } elseif ($type === 'user') {
        // User profile silhouette
        $pdf->Circle($x + $size/2, $y + $size/3, $size/4, 0, 360, 'D');
        // Arc shoulders
        $pdf->StartTransform();
        $pdf->Circle($x + $size/2, $y + $size + 0.4, $size/2, 180, 360, 'D');
        $pdf->StopTransform();
    } elseif ($type === 'allergy') {
        // Prohibition/restriction circle line
        $pdf->Circle($x + $size/2, $y + $size/2, $size/2 - 0.1, 0, 360, 'D');
        $pdf->Line($x + 0.5, $y + 0.5, $x + $size - 0.5, $y + $size - 0.5);
    } elseif ($type === 'status') {
        // Card badge outline
        $pdf->Rect($x, $y + 0.5, $size, $size - 1.0, 'D');
        $pdf->Rect($x + 0.5, $y + 1.2, 0.7, 0.7);
        $pdf->Line($x + 1.5, $y + 1.0, $x + $size - 0.5, $y + 1.0);
        $pdf->Line($x + 1.5, $y + 1.6, $x + $size - 0.5, $y + 1.6);
    } elseif ($type === 'globe') {
        // Globe circle grid
        $pdf->Circle($x + $size/2, $y + $size/2, $size/2 - 0.1, 0, 360, 'D');
        $pdf->Line($x + 0.1, $y + $size/2, $x + $size - 0.1, $y + $size/2); // equator
        $pdf->Line($x + $size/2, $y + 0.1, $x + $size/2, $y + $size - 0.1); // meridian
    } elseif ($type === 'shield') {
        // Shield polygon for terms
        $pdf->Polygon(array(
            $x + $size/2, $y,
            $x + $size - 0.1, $y + $size/4,
            $x + $size - 0.1, $y + 2*$size/3,
            $x + $size/2, $y + $size,
            $x + 0.1, $y + 2*$size/3,
            $x + 0.1, $y + $size/4
        ), 'D');
    } elseif ($type === 'info') {
        // Info letter 'i' in circle
        $pdf->Circle($x + $size/2, $y + $size/2, $size/2 - 0.1, 0, 360, 'D');
        $pdf->Circle($x + $size/2, $y + $size/3, 0.3, 0, 360, 'F');
        $pdf->Line($x + $size/2, $y + $size/2 - 0.1, $x + $size/2, $y + 3*$size/4);
    }
}

// Initialize TCPDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFontSubsetting(true);
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// Card dimensions
$card_w = 160;
$card_h = 100;
$x = (210 - $card_w) / 2; // 25mm

// Y positions
$y_front = 30;
$y_back = 150;

// Border styling
$gray_border = array('width' => 0.4, 'color' => array(229, 232, 232));
$divider_line = array('width' => 0.3, 'color' => array(229, 232, 232));

// ==========================================
// FRONT SIDE CARD
// ==========================================
// 1. Draw rounded backgrounds
$pdf->RoundedRect($x, $y_front, $card_w, $card_h, 5, '1111', 'F', array(), array(255, 255, 255));
// Footer background block
$pdf->RoundedRect($x, $y_front + $card_h - 12, $card_w, 12, 5, '0011', 'F', array(), array(240, 244, 248));
// Card outline
$pdf->RoundedRect($x, $y_front, $card_w, $card_h, 5, '1111', 'D', $gray_border);

// 2. Dividers
$pdf->Line($x, $y_front + 14, $x + $card_w, $y_front + 14, $divider_line);
$pdf->Line($x, $y_front + $card_h - 12, $x + $card_w, $y_front + $card_h - 12, $divider_line);

// 3. Header Text
// Logo rounded icon box
$logo_x = $x + 5;
$logo_y = $y_front + 3.5;
$pdf->RoundedRect($logo_x, $logo_y, 7, 7, 1.5, '1111', 'F', array(), array(15, 110, 86));

// Draw white pulse wave inside logo icon
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetLineWidth(0.4);
$pdf->Line($logo_x + 1.0, $logo_y + 3.5, $logo_x + 2.3, $logo_y + 3.5);
$pdf->Line($logo_x + 2.3, $logo_y + 3.5, $logo_x + 2.8, $logo_y + 1.5);
$pdf->Line($logo_x + 2.8, $logo_y + 1.5, $logo_x + 3.6, $logo_y + 5.5);
$pdf->Line($logo_x + 3.6, $logo_y + 5.5, $logo_x + 4.4, $logo_y + 2.5);
$pdf->Line($logo_x + 4.4, $logo_y + 2.5, $logo_x + 4.9, $logo_y + 3.5);
$pdf->Line($logo_x + 4.9, $logo_y + 3.5, $logo_x + 6.0, $logo_y + 3.5);

// Brand name markup
$brand_html = '<span style="color:#2C3E50; font-weight:bold; font-size:13pt;">Lurnixe</span><span style="color:#0f6e56; font-weight:bold; font-size:13pt;">Health</span>';
$pdf->writeHTMLCell(50, 8, $x + 14, $logo_y - 0.5, $brand_html, 0, 0, false, true, 'L');

// Prominent FAMILY HEALTH CARD subtitle
$sub_html = '<span style="color:#0f6e56; font-weight:bold; font-size:9.5pt; letter-spacing: 0.5px;">FAMILY HEALTH CARD</span>';
$pdf->writeHTMLCell(60, 8, $x + $card_w - 65, $logo_y + 0.5, $sub_html, 0, 0, false, true, 'R');

// 4. Left Section (Circular Photo and QR Code)
$photo_x = $x + 20;
$photo_y = $y_front + 31.5;
$photo_r = 9.5;

$photo_file = $member['photo'];
$photo_success = false;

if (!empty($photo_file) && file_exists(__DIR__ . '/../' . $photo_file)) {
    $photo_path = __DIR__ . '/../' . $photo_file;
    $img_info = @getimagesize($photo_path);
    if ($img_info !== false) {
        $mime = $img_info['mime'];
        if ($mime === 'image/jpeg' || $mime === 'image/jpg' || extension_loaded('gd')) {
            try {
                // Circle clip region
                $pdf->StartTransform();
                $pdf->Circle($photo_x, $photo_y, $photo_r, 0, 360, 'CN');
                $pdf->Image($photo_path, $photo_x - $photo_r, $photo_y - $photo_r, $photo_r * 2, $photo_r * 2);
                $pdf->StopTransform();
                $photo_success = true;
            } catch (Exception $e) {
                $photo_success = false;
            }
        }
    }
}

// Circular Photo ring border (Teal)
$pdf->SetDrawColor(15, 110, 86);
$pdf->SetLineWidth(0.6);
$pdf->Circle($photo_x, $photo_y, $photo_r, 0, 360, 'D');

if (!$photo_success) {
    // Default circular placeholder
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Circle($photo_x, $photo_y, $photo_r - 0.2, 0, 360, 'F');
    $pdf->SetTextColor(100, 100, 100);
    $pdf->SetFont('helvetica', 'B', 5.5);
    $pdf->SetXY($photo_x - 5, $photo_y - 2);
    $pdf->Cell(10, 4, 'PHOTO', 0, 0, 'C');
}

// QR Code box with thin gray border
$qr_box_x = $x + 9.5;
$qr_box_y = $y_front + 45.5;
$qr_box_w = 21;
$qr_box_h = 22;
$pdf->RoundedRect($qr_box_x, $qr_box_y, $qr_box_w, $qr_box_h, 2, '1111', 'DF', array('width' => 0.2, 'color' => array(229, 232, 232)), array(255, 255, 255));

// Render QR Code matrix inside
$qr_x = $qr_box_x + 2;
$qr_y = $qr_box_y + 1.5;
$qr_size = 17;

$qr_url = BASE_URL . "member.php?id=" . $member['member_id'];
$old_err = error_reporting(0);
$matrix = \PHPQRCode\QRcode::text($qr_url, false, 'M', 3, 4);
error_reporting($old_err);

if (!empty($matrix)) {
    $num_rows = count($matrix);
    $num_cols = strlen($matrix[0]);
    $module_size = $qr_size / $num_cols;
    
    $pdf->SetFillColor(0, 0, 0);
    for ($r = 0; $r < $num_rows; $r++) {
        for ($c = 0; $c < $num_cols; $c++) {
            if ($matrix[$r][$c] === '1') {
                $pdf->Rect($qr_x + ($c * $module_size), $qr_y + ($r * $module_size), $module_size, $module_size, 'F');
            }
        }
    }
} else {
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Rect($qr_x, $qr_y, $qr_size, $qr_size);
}

// SCAN TO VERIFY text below QR
$pdf->SetTextColor(15, 110, 86);
$pdf->SetFont('helvetica', 'B', 5.2);
$pdf->SetXY($qr_box_x, $qr_box_y + 18.5);
$pdf->Cell($qr_box_w, 3, 'SCAN TO VERIFY', 0, 0, 'C');

// 5. Right Section Details
// Member Name (Large bold dark text)
$pdf->SetTextColor(44, 62, 80); // dark gray #2C3E50
$pdf->SetFont('helvetica', 'B', 13.5);
$pdf->SetXY($x + 43, $y_front + 17.5);
$display_name = strlen($member['name']) > 26 ? substr($member['name'], 0, 24) . '..' : $member['name'];
$pdf->Cell(70, 5, strtoupper($display_name), 0, 0, 'L');

// Badges
// Member ID Badge (Light blue bg, teal border and text)
$id_badge_x = $x + 43;
$id_badge_y = $y_front + 24.2;
$id_badge_w = 26;
$id_badge_h = 4.2;
$pdf->RoundedRect($id_badge_x, $id_badge_y, $id_badge_w, $id_badge_h, 1, '1111', 'DF', array('width' => 0.2, 'color' => array(15, 110, 86)), array(235, 245, 251));

$pdf->SetTextColor(15, 110, 86);
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->SetXY($id_badge_x, $id_badge_y);
$pdf->Cell($id_badge_w, $id_badge_h, $member['member_id'], 0, 0, 'C');

// Blood Group Badge (Pink/Red bg, red text)
$blood_badge_x = $x + 72;
$blood_badge_y = $y_front + 24.2;
$blood_badge_w = 20;
$blood_badge_h = 4.2;
$pdf->RoundedRect($blood_badge_x, $blood_badge_y, $blood_badge_w, $blood_badge_h, 1, '1111', 'DF', array('width' => 0.2, 'color' => array(229, 180, 180)), array(253, 237, 236));

$pdf->SetTextColor(192, 57, 43); // Crimson Red
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->SetXY($blood_badge_x, $blood_badge_y);
$pdf->Cell($blood_badge_w, $blood_badge_h, 'BLOOD ' . $member['blood_group'], 0, 0, 'C');

// Vertical line divider between left and right side of body
$pdf->Line($x + 38, $y_front + 14, $x + 38, $y_front + $card_h - 12, $divider_line);

// details coordinates mapping
$c1_icon_x = $x + 43;
$c1_lbl_x  = $x + 49;
$c1_val_x  = $x + 69;

$c2_icon_x = $x + 97;
$c2_lbl_x  = $x + 103;
$c2_val_x  = $x + 121;

// Row offsets
$y_row1 = $y_front + 32.5;
$y_row2 = $y_front + 40.5;
$y_row3 = $y_front + 48.5;
$y_row4 = $y_front + 56.5;
$y_row5 = $y_front + 64.5;

// Row 1: DOB and GENDER
DrawCardIcon($pdf, 'calendar', $c1_icon_x, $y_row1);
$pdf->SetTextColor(127, 140, 141); // light gray
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->Text($c1_lbl_x, $y_row1 - 0.5, 'DOB');
$pdf->SetTextColor(44, 62, 80);
$pdf->SetFont('helvetica', 'B', 7.5);
$pdf->Text($c1_val_x, $y_row1 - 0.5, format_date($member['dob']));

DrawCardIcon($pdf, 'user', $c2_icon_x, $y_row1);
$pdf->SetTextColor(127, 140, 141);
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->Text($c2_lbl_x, $y_row1 - 0.5, 'GENDER');
$pdf->SetTextColor(44, 62, 80);
$pdf->SetFont('helvetica', 'B', 7.5);
$pdf->Text($c2_val_x, $y_row1 - 0.5, $member['gender']);

// Row 2: MOBILE and EMAIL
DrawCardIcon($pdf, 'phone', $c1_icon_x, $y_row2);
$pdf->SetTextColor(127, 140, 141);
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->Text($c1_lbl_x, $y_row2 - 0.5, 'MOBILE');
$pdf->SetTextColor(44, 62, 80);
$pdf->SetFont('helvetica', 'B', 7.5);
$pdf->Text($c1_val_x, $y_row2 - 0.5, $member['mobile']);

DrawCardIcon($pdf, 'envelope', $c2_icon_x, $y_row2);
$pdf->SetTextColor(127, 140, 141);
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->Text($c2_lbl_x, $y_row2 - 0.5, 'EMAIL');
$pdf->SetTextColor(44, 62, 80);
$pdf->SetFont('helvetica', 'B', 7.5);
$display_email = !empty($member['email']) ? (strlen($member['email']) > 22 ? substr($member['email'], 0, 20) . '..' : $member['email']) : 'N/A';
$pdf->Text($c2_val_x, $y_row2 - 0.5, $display_email);

// Row 3: VAL TILL and STATUS
DrawCardIcon($pdf, 'calendar', $c1_icon_x, $y_row3);
$pdf->SetTextColor(127, 140, 141);
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->Text($c1_lbl_x, $y_row3 - 0.5, 'VAL TILL');
$pdf->SetTextColor(39, 174, 96); // Green valid till
$pdf->SetFont('helvetica', 'B', 7.5);
$pdf->Text($c1_val_x, $y_row3 - 0.5, format_date($member['validity_date']));

DrawCardIcon($pdf, 'status', $c2_icon_x, $y_row3);
$pdf->SetTextColor(127, 140, 141);
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->Text($c2_lbl_x, $y_row3 - 0.5, 'STATUS');
if (strtolower($member['status']) === 'active') {
    $pdf->SetTextColor(39, 174, 96);
    $status_text = 'ACTIVE';
} else {
    $pdf->SetTextColor(230, 126, 34); // Orange status
    $status_text = strtoupper($member['status']);
}
$pdf->SetFont('helvetica', 'B', 7.5);
$pdf->Text($c2_val_x, $y_row3 - 0.5, $status_text);

// Row 4: ALLERGY
DrawCardIcon($pdf, 'allergy', $c1_icon_x, $y_row4);
$pdf->SetTextColor(127, 140, 141);
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->Text($c1_lbl_x, $y_row4 - 0.5, 'ALLERGY');
$pdf->SetTextColor(44, 62, 80);
$pdf->SetFont('helvetica', 'B', 7.5);
$allergies = !empty($member['allergies']) ? (strlen($member['allergies']) > 32 ? substr($member['allergies'], 0, 30) . '..' : $member['allergies']) : 'None Declared';
$pdf->Text($c1_val_x, $y_row4 - 0.5, $allergies);

// Row 5: CITY
DrawCardIcon($pdf, 'pin', $c1_icon_x, $y_row5);
$pdf->SetTextColor(127, 140, 141);
$pdf->SetFont('helvetica', 'B', 6.5);
$pdf->Text($c1_lbl_x, $y_row5 - 0.5, 'CITY');
$pdf->SetTextColor(44, 62, 80);
$pdf->SetFont('helvetica', 'B', 7.5);
$pdf->Text($c1_val_x, $y_row5 - 0.5, $member['city'] . ' (' . $member['state'] . ')');

// 6. Footer (Emergency contact & website)
DrawCardIcon($pdf, 'phone', $x + 6, $y_front + $card_h - 7.5);

$em_text = '<span style="color:#0f6e56; font-weight:bold; font-size:7.5pt;">EMERGENCY CONTACT</span> &nbsp;&nbsp;<span style="color:#2C3E50; font-weight:bold; font-size:7.5pt;">' . htmlspecialchars($member['emergency_name']) . ' (Ph: ' . htmlspecialchars($member['emergency_mobile']) . ')</span>';
$pdf->writeHTMLCell(90, 6, $x + 11, $y_front + $card_h - 8.5, $em_text, 0, 0, false, true, 'L');

DrawCardIcon($pdf, 'globe', $x + $card_w - 46, $y_front + $card_h - 7.5);
$web_html = '<span style="color:#0f6e56; font-weight:bold; font-size:7.5pt; text-decoration:none;">www.lurnixehealth.com</span>';
$pdf->writeHTMLCell(40, 6, $x + $card_w - 41, $y_front + $card_h - 8.5, $web_html, 0, 0, false, true, 'L');


// ==========================================
// BACK SIDE CARD
// ==========================================
// 1. Draw rounded backgrounds
$pdf->RoundedRect($x, $y_back, $card_w, $card_h, 5, '1111', 'F', array(), array(255, 255, 255));
// Footer background block
$pdf->RoundedRect($x, $y_back + $card_h - 12, $card_w, 12, 5, '0011', 'F', array(), array(240, 244, 248));
// Card outline
$pdf->RoundedRect($x, $y_back, $card_w, $card_h, 5, '1111', 'D', $gray_border);

// 2. Footer divider and center vertical divider
$pdf->Line($x, $y_back + $card_h - 12, $x + $card_w, $y_back + $card_h - 12, $divider_line);
$pdf->Line($x + $card_w / 2, $y_back + 8, $x + $card_w / 2, $y_back + $card_h - 12 - 8, $divider_line);

// 3. Left Column: Terms & Conditions
DrawCardIcon($pdf, 'shield', $x + 8, $y_back + 10, 3.5);
$pdf->SetTextColor(15, 110, 86);
$pdf->SetFont('helvetica', 'B', 9.5);
$pdf->Text($x + 13, $y_back + 9.5, 'TERMS & CONDITIONS');

$terms_html = '
<table border="0" cellpadding="1.8" cellspacing="0" style="color:#2C3E50; font-size:7.2pt; line-height:1.25;">
  <tr><td width="7" style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td width="64">This card is non-transferable.</td></tr>
  <tr><td style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td>This card is valid till the mentioned date.</td></tr>
  <tr><td style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td>Please show this card during availing services.</td></tr>
  <tr><td style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td>LurnixeHealth is not liable for misuse of this card.</td></tr>
  <tr><td style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td>In case of loss, report immediately.</td></tr>
</table>';
$pdf->writeHTMLCell(74, 55, $x + 4, $y_back + 16.5, $terms_html, 0, 0, false, true, 'L');

// 4. Right Column: Instructions
DrawCardIcon($pdf, 'info', $x + $card_w/2 + 8, $y_back + 10, 3.5);
$pdf->SetTextColor(15, 110, 86);
$pdf->SetFont('helvetica', 'B', 9.5);
$pdf->Text($x + $card_w/2 + 13, $y_back + 9.5, 'INSTRUCTIONS');

$inst_html = '
<table border="0" cellpadding="1.8" cellspacing="0" style="color:#2C3E50; font-size:7.2pt; line-height:1.25;">
  <tr><td width="7" style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td width="64">Carry this card during hospital visits.</td></tr>
  <tr><td style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td>Use QR code to verify card authenticity.</td></tr>
  <tr><td style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td>Keep your details updated for seamless services.</td></tr>
  <tr><td style="color:#0f6e56; font-weight:bold; font-size:8pt;">&bull;</td><td>Contact support for any assistance.</td></tr>
</table>';
$pdf->writeHTMLCell(74, 55, $x + $card_w/2 + 4, $y_back + 16.5, $inst_html, 0, 0, false, true, 'L');

// 5. Back Side Footer: Helpline and Support email
DrawCardIcon($pdf, 'phone', $x + 6, $y_back + $card_h - 7.5);
$help_html = '<span style="color:#0f6e56; font-weight:bold; font-size:7.5pt;">HELPLINE</span> &nbsp;&nbsp;<span style="color:#2C3E50; font-weight:bold; font-size:7.5pt;">1800-123-4567</span>';
$pdf->writeHTMLCell(60, 6, $x + 11, $y_back + $card_h - 8.5, $help_html, 0, 0, false, true, 'L');

DrawCardIcon($pdf, 'envelope', $x + $card_w - 46, $y_back + $card_h - 7.5);
$email_html = '<span style="color:#0f6e56; font-weight:bold; font-size:7.5pt;">support@lurnixehealth.com</span>';
$pdf->writeHTMLCell(40, 6, $x + $card_w - 41, $y_back + $card_h - 8.5, $email_html, 0, 0, false, true, 'L');


// ==========================================
// CUTTING / CROP MARKS (High-quality cutout guidelines)
// ==========================================
$pdf->SetDrawColor(180, 180, 180);
$pdf->SetLineWidth(0.2);

$cards_positions = array($y_front, $y_back);

foreach ($cards_positions as $card_y) {
    // Top-Left corner crop marks
    $pdf->Line($x - 5, $card_y, $x - 1, $card_y);
    $pdf->Line($x, $card_y - 5, $x, $card_y - 1);

    // Top-Right corner crop marks
    $pdf->Line($x + $card_w + 1, $card_y, $x + $card_w + 5, $card_y);
    $pdf->Line($x + $card_w, $card_y - 5, $x + $card_w, $card_y - 1);

    // Bottom-Left corner crop marks
    $pdf->Line($x - 5, $card_y + $card_h, $x - 1, $card_y + $card_h);
    $pdf->Line($x, $card_y + $card_h + 1, $x, $card_y + $card_h + 5);

    // Bottom-Right corner crop marks
    $pdf->Line($x + $card_w + 1, $card_y + $card_h, $x + $card_w + 5, $card_y + $card_h);
    $pdf->Line($x + $card_w, $card_y + $card_h + 1, $x + $card_w, $card_y + $card_h + 5);
}

// Output PDF directly for download
$pdf_name = 'LurnixeCard_' . $member['member_id'] . '.pdf';
$pdf->Output($pdf_name, 'I');
exit;
