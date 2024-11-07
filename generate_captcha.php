<?php
session_start();

// Generate random text for CAPTCHA
$captchaText = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 6);

// Store the CAPTCHA text in a session variable
$_SESSION['captcha_text'] = $captchaText;

// Create an image
$image = imagecreatetruecolor(100, 40);

// Set colors
$bgColor = imagecolorallocate($image, 255, 255, 255); // White background
$textColor = imagecolorallocate($image, 0, 0, 0); // Black text
$lineColor = imagecolorallocate($image, 64, 64, 64); // Gray lines

// Fill the background
imagefilledrectangle($image, 0, 0, 100, 40, $bgColor);

// Add some random lines for extra security
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand() % 40, 100, rand() % 40, $lineColor);
}

// Add the text to the image
imagestring($image, 5, 15, 10, $captchaText, $textColor);

// Output the image as a PNG
header("Content-type: image/png");
imagepng($image);

// Clean up
imagedestroy($image);
?>
