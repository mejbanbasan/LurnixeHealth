<?php
$inputFile = 'assets/images/new_pvc_card_v2.png';
$outputFile = 'assets/images/new_pvc_card_v3.png';

$im = imagecreatefrompng($inputFile);
imagealphablending($im, true);

$w = imagesx($im);
$h = imagesy($im);

$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);

$bgColor = imagecolorat($im, 10, 10);
$colors = imagecolorsforindex($im, $bgColor);
$bgR = $colors['red'];
$bgG = $colors['green'];
$bgB = $colors['blue'];
$bgA = $colors['alpha']; // 0 is opaque, 127 is transparent in GD

// If it's already transparent, we just use it
if ($bgA >= 120) {
    echo "Already transparent.\n";
    exit;
}

$fuzz = 15;

for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $rgb = imagecolorat($im, $x, $y);
        $c = imagecolorsforindex($im, $rgb);
        $r = $c['red'];
        $g = $c['green'];
        $b = $c['blue'];
        
        $diff = abs($r - $bgR) + abs($g - $bgG) + abs($b - $bgB);
        if ($diff < $fuzz) {
            // make transparent
            $alpha = 127;
        } else {
            // solid
            $alpha = 0;
            if ($diff < $fuzz * 2) {
                // antialias/blend
                $alpha = 127 - (127 * ($diff - $fuzz) / $fuzz);
            }
        }
        $color = imagecolorallocatealpha($out, $r, $g, $b, (int)$alpha);
        imagesetpixel($out, $x, $y, $color);
    }
}

imagepng($out, $outputFile);
echo "Background removed.\n";
