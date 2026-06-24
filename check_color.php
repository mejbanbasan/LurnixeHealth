<?php
$im = imagecreatefromjpeg('assets/images/new_pvc_card.jpg');
$rgb = imagecolorat($im, 10, 10);
$r = ($rgb >> 16) & 0xFF;
$g = ($rgb >> 8) & 0xFF;
$b = $rgb & 0xFF;
printf("#%02x%02x%02x\n", $r, $g, $b);
