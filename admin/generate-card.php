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
    $font_size = 7.5;
} elseif ($name_len > 22) {
    $font_size = 8.5;
} elseif ($name_len > 16) {
    $font_size = 9.5;
} else {
    $font_size = 11.5;
}

$pdf->SetTextColor(33, 47, 61); // Dark blue-grey text matching template styling
$pdf->SetFont('helvetica', 'B', $font_size);
// Align to name position next to user circular icon (X = 19.0mm, Y = 22.0mm relative to card)
$pdf->SetXY($x + 19.0, $y_back + 22.0);
$pdf->Cell(38, 5, $name_text, 0, 0, 'L');

// 2. Overlay Member ID
$pdf->SetTextColor(33, 47, 61);
$pdf->SetFont('helvetica', 'B', 8.5);
// Align to member ID position next to card circular icon (X = 19.0mm, Y = 27.0mm relative to card)
$pdf->SetXY($x + 19.0, $y_back + 27.0);
$pdf->Cell(38, 4, $member['member_id'], 0, 0, 'L');

// 3. Overlay Valid Upto Date
$valid_date = date('m/Y', strtotime($member['validity_date']));
$pdf->SetTextColor(33, 47, 61);
$pdf->SetFont('helvetica', 'B', 8.5);
// Align to validity position next to calendar circular icon (X = 19.0mm, Y = 31.8mm relative to card)
$pdf->SetXY($x + 19.0, $y_back + 31.8);
$pdf->Cell(38, 4, $valid_date, 0, 0, 'L');

// 4. Overlay Dynamic QR Code with Center Logo
$qr_url = BASE_URL . "member.php?id=" . $member['member_id'];
$qr_size = 11.0;
$qr_box_x = $x + 58.11;     // Align with QR box left edge
$qr_box_y = $y_back + 20.34; // Align with QR box top edge
$qr_box_w = 12.96;
$qr_box_h = 11.96;

$qr_x_abs = $qr_box_x + ($qr_box_w - $qr_size) / 2;
$qr_y_abs = $qr_box_y + ($qr_box_h - $qr_size) / 2;

$style = array(
    'border' => 0,
    'vpadding' => 0,
    'hpadding' => 0,
    'fgcolor' => array(0, 0, 0), // Black QR modules
    'bgcolor' => array(255, 255, 255), // Solid white background (quiet zone)
    'module_width' => 1,
    'module_height' => 1
);
// Write dynamic QR code
$pdf->write2DBarcode($qr_url, 'QRCODE,H', $qr_x_abs, $qr_y_abs, $qr_size, $qr_size, $style, 'N');

// Center logo overlay inside the barcode
$logo_size = 2.2;
$logo_x = $qr_x_abs + ($qr_size - $logo_size) / 2;
$logo_y = $qr_y_abs + ($qr_size - $logo_size) / 2;
$logo_path = __DIR__ . '/../assets/images/qr_logo.png';

if (file_exists($logo_path)) {
    // Draw white background mask square under logo
    $pdf->Rect($logo_x - 0.2, $logo_y - 0.2, $logo_size + 0.4, $logo_size + 0.4, 'F', array(), array(255, 255, 255));
    // Overlay logo image
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