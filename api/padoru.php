<?php
$now = new DateTime();
$xmas = new DateTime();
if ($now->format("m") == 12)
    if ($now->format("d") > 25)
        $xmas->setDate(intval($now->format("Y")) + 1, 12, 25);
    else
        $xmas->setDate(intval($now->format("Y")), 12, 25);
else
    $xmas->setDate(intval($now->format("Y")), 12, 25);
$days = $xmas->diff($now)->days;
if ($days == 0)
    $image = imagecreatefromjpeg("./padorus/12.jpg");
else if ($days <= 30)
    $image = imagecreatefromjpeg("./padorus/11.jpg");
else if ($days <= 60)
    $image = imagecreatefromjpeg("./padorus/10.jpg");
else if ($days <= 90)
    $image = imagecreatefromjpeg("./padorus/9.jpg");
else if ($days <= 120)
    $image = imagecreatefromjpeg("./padorus/8.jpg");
else if ($days <= 150)
    $image = imagecreatefromjpeg("./padorus/7.jpg");
else if ($days <= 180)
    $image = imagecreatefromjpeg("./padorus/6.jpg");
else if ($days <= 210)
    $image = imagecreatefromjpeg("./padorus/5.jpg");
else if ($days <= 240)
    $image = imagecreatefromjpeg("./padorus/4.jpg");
else if ($days <= 270)
    $image = imagecreatefromjpeg("./padorus/3.jpg");
else if ($days <= 300)
    $image = imagecreatefromjpeg("./padorus/2.jpg");
else
    $image = imagecreatefromjpeg("./padorus/1.jpg");
$size = imagettfbbox(16, 0, dirname(__FILE__) . "/fonts/trebuchet.ttf", $days);
imagettftext($image, 16, 0, 272 - ($size[2] - $size[0]) / 2, 270, imagecolorallocate($image, 50, 50, 80), dirname(__FILE__) . "/fonts/trebuchet.ttf", $days);
header("Content-type: image/jpeg");
imagejpeg($image, null, 100);
