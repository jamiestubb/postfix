<?php
session_start();

// Generate random text for CAPTCHA
$captchaText = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 6);

// Store the CAPTCHA text in a session variable
$_SESSION['captcha_text'] = $captchaText;

// Create a larger image
$imageWidth = 150;
$imageHeight = 50;
$image = imagecreatetruecolor($imageWidth, $imageHeight);

// Set colors
$bgColor = imagecolorallocate($image, 255, 255, 255); // White background
$textColor = imagecolorallocate($image, 0, 0, 0); // Black text
$lineColor = imagecolorallocate($image, 64, 64, 64); // Gray lines

// Fill the background
imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $bgColor);

// Add random lines for extra security
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand() % $imageHeight, $imageWidth, rand() % $imageHeight, $lineColor);
}

// Add the text to the image with a larger font
$fontSize = 7; // Scale the font size up to improve readability
$textX = 20; // Adjust text position if necessary
$textY = 15; // Adjust text position if necessary
imagestring($image, $fontSize, $textX, $textY, $captchaText, $textColor);

// Output the image as a PNG
header("Content-type: image/png");
imagepng($image);

// Clean up
imagedestroy($image);
?>
