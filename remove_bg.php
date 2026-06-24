<?php
$inputFile = 'assets/images/new_pvc_card.jpg';
$outputFile = 'assets/images/new_pvc_card.png';

$im = imagecreatefromjpeg($inputFile);
$w = imagesx($im);
$h = imagesy($im);

$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);

$bgColor = imagecolorat($im, 10, 10);
$bgR = ($bgColor >> 16) & 0xFF;
$bgG = ($bgColor >> 8) & 0xFF;
$bgB = $bgColor & 0xFF;

$fuzz = 20;

for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $rgb = imagecolorat($im, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        $diff = abs($r - $bgR) + abs($g - $bgG) + abs($b - $bgB);
        if ($diff < $fuzz) {
            // make transparent
            $alpha = 127;
        } else {
            // keep pixel but maybe add semi-transparency for shadow?
            $alpha = 0;
            if ($diff < $fuzz * 2) {
                $alpha = 127 - (127 * ($diff - $fuzz) / $fuzz);
            }
        }
        $color = imagecolorallocatealpha($out, $r, $g, $b, (int)$alpha);
        imagesetpixel($out, $x, $y, $color);
    }
}

imagepng($out, $outputFile);
echo "Saved transparent PNG.\n";
