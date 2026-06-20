<?php
/**
 * Lurnixe Health Card System - Premium PVC Health Card Generator
 * June 2026
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Turn off error reporting to prevent warnings from corrupting the PDF stream
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce login validation
check_auth();

// Set HTTP cache-control headers to prevent browser caching of generated cards
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

// Initialize TCPDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFontSubsetting(true);
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage();
$pdf->SetAutoPageBreak(false);

// Card dimensions (Standard CR80 size)
$card_w = 85.6;
$card_h = 54.0;
$x = (210 - $card_w) / 2; // Centered horizontally: 62.2 mm

// Y positions (Perfect spacing on A4 page)
$y_front = 63.0;
$y_back = 180.0;

// Background Image Paths
$front_img = __DIR__ . '/../assets/images/pvc_front.jpg';
$back_img = __DIR__ . '/../assets/images/pvc_back.jpg';

// Verify background images exist
if (!file_exists($front_img) || !file_exists($back_img)) {
    die("Error: PVC card background templates are missing. Please run background preparation script.");
}

// ==========================================
// FRONT SIDE CARD
// ==========================================
$pdf->Image($front_img, $x, $y_front, $card_w, $card_h, 'JPG', '', '', false, 300, '', false, false, 0);

// ==========================================
// BACK SIDE CARD
// ==========================================
$pdf->Image($back_img, $x, $y_back, $card_w, $card_h, 'JPG', '', '', false, 300, '', false, false, 0);

// 1. Overlay Member Name
$name_text = strtoupper($member['name']);
$name_len = strlen($name_text);
if ($name_len > 28) {
    $font_size = 8.5;
} elseif ($name_len > 22) {
    $font_size = 10;
} elseif ($name_len > 16) {
    $font_size = 12;
} else {
    $font_size = 14.5;
}

$pdf->SetTextColor(255, 255, 255); // White text
$pdf->SetFont('helvetica', 'B', $font_size);
// Align to name position (X = 5.2mm, Y = 29.5mm relative to card)
$pdf->SetXY($x + 5.2, $y_back + 29.5);
$pdf->Cell(52, 6, $name_text, 0, 0, 'L');

// 2. Overlay Member ID
$pdf->SetTextColor(255, 255, 255); // White text
$pdf->SetFont('helvetica', 'B', 8);
// Align to member ID position (X = 5.2mm, Y = 36.2mm relative to card)
$pdf->SetXY($x + 5.2, $y_back + 36.2);
$pdf->Cell(52, 4, 'MEMBER ID: ' . $member['member_id'], 0, 0, 'L');

// 3. Overlay Support Email (next to the envelope icon)
$pdf->SetTextColor(255, 255, 255); // White text
$pdf->SetFont('helvetica', 'B', 7.5);
// Align to email position (X = 10.5mm, Y = 44.0mm relative to card)
$pdf->SetXY($x + 10.5, $y_back + 44.0);
$pdf->Cell(45, 4, 'support@lurnixehealth.com', 0, 0, 'L');

// 4. Overlay Dynamic QR Code with Premium Presentation & Center Logo
$qr_url = BASE_URL . "member.php?id=" . $member['member_id'];
$qr_x = 60.6;
$qr_y = 28.1;
$qr_size = 14.4;

// Draw a beautiful white rounded rectangle container with a primary green border
$border_style = array('width' => 0.5, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => array(39, 174, 96)); // #27AE60
$pdf->RoundedRect($x + 58.8, $y_back + 26.3, 18.0, 18.0, 2.0, '1111', 'DF', $border_style, array(255, 255, 255));

$style = array(
    'border' => 0,
    'vpadding' => 0,
    'hpadding' => 0,
    'fgcolor' => array(0, 0, 0), // black
    'bgcolor' => array(255, 255, 255), // white
    'module_width' => 1,
    'module_height' => 1
);
// Write barcode using Level H error correction (to tolerate logo occlusion)
$pdf->write2DBarcode($qr_url, 'QRCODE,H', $x + $qr_x, $y_back + $qr_y, $qr_size, $qr_size, $style, 'N');

// Center logo overlay calculations
$logo_size = 3.2;
$logo_x = $x + $qr_x + ($qr_size - $logo_size) / 2;
$logo_y = $y_back + $qr_y + ($qr_size - $logo_size) / 2;
$logo_path = __DIR__ . '/../assets/images/qr_logo.png';

if (file_exists($logo_path)) {
    // Draw white background square under logo to mask QR code pixels
    $pdf->Rect($logo_x - 0.2, $logo_y - 0.2, $logo_size + 0.4, $logo_size + 0.4, 'F', array(), array(255, 255, 255));
    // Overlay the heart logo
    $pdf->Image($logo_path, $logo_x, $logo_y, $logo_size, $logo_size, 'PNG', '', '', false, 300, '', false, false, 0);
}


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
