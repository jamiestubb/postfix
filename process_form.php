<?php
// Display errors for troubleshooting (optional)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the Turnstile response and user email from the form submission
    $turnstileResponse = $_POST['cf-turnstile-response'];
    $userEmail = $_POST['user_email']; // This was set in the hidden field
    $secretKey = '0x4AAAAAAAzbaFyF5jnLHaBSyZ5AuNHu098';

    // Prepare data for CAPTCHA validation
    $data = [
        'secret' => $secretKey,
        'response' => $turnstileResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    // Set up HTTP request options
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    // Create the context for the HTTP request
    $context  = stream_context_create($options);
    $verify = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
    $captchaSuccess = json_decode($verify);

    // Check if the CAPTCHA validation was successful
    if ($captchaSuccess->success) {
        // CAPTCHA validated successfully - proceed with form processing
        echo "Form submitted successfully! Email: " . htmlspecialchars($userEmail);
        // Here you can add more processing, like saving form data to a database
    } else {
        // CAPTCHA validation failed
        echo "CAPTCHA validation failed. Please try again.";
    }
}
?>
