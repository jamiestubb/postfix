<?php
session_start();

// Generate random text for CAPTCHA
$captchaText = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 6);

// Store the CAPTCHA text in a session variable
$_SESSION['captcha_text'] = $captchaText;

// Create a base image
$imageWidth = 200;
$imageHeight = 80;
$image = imagecreatetruecolor($imageWidth, $imageHeight);

// Set colors
$bgColor = imagecolorallocate($image, 255, 255, 255); // White background
$textColor = imagecolorallocate($image, 0, 0, 0); // Black text
$lineColor = imagecolorallocate($image, 64, 64, 64); // Gray lines

// Fill the background
imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $bgColor);

// Add random lines for extra security
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand(0, $imageHeight), $imageWidth, rand(0, $imageHeight), $lineColor);
}

// Use the largest built-in font size
$fontSize = 5; // Largest available font size with imagestring()

// Calculate the text dimensions for centering
$textWidth = imagefontwidth($fontSize) * strlen($captchaText);
$textHeight = imagefontheight($fontSize);

// Center the text in the image
$textX = ($imageWidth - $textWidth) / 2;
$textY = ($imageHeight - $textHeight) / 2;

// Add the text to the image
imagestring($image, $fontSize, $textX, $textY, $captchaText, $textColor);

// Optionally, scale up the image if you want the text to appear larger
$scaledWidth = 300;  // New width for scaling
$scaledHeight = 100; // New height for scaling
$scaledImage = imagescale($image, $scaledWidth, $scaledHeight);

// Output the scaled image as a PNG
header("Content-type: image/png");
imagepng($scaledImage);

// Clean up
imagedestroy($image);
imagedestroy($scaledImage);
?>
