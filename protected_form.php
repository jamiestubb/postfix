<?php
// Display errors for troubleshooting (optional)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Decode the JSON data from the fetch request
$data = json_decode(file_get_contents('php://input'), true);
$turnstileResponse = $data['token'];
$email = $data['email'];
$secretKey = '0x4AAAAAAAzbaFyF5jnLHaBSyZ5AuNHu098'; // Replace with your actual secret key from Cloudflare

// Prepare data for validation
$validationData = [
    'secret' => $secretKey,
    'response' => $turnstileResponse,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

// Set up HTTP request options
$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($validationData)
    ]
];

// Validate the CAPTCHA response with Cloudflare
$context  = stream_context_create($options);
$verify = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
$captchaSuccess = json_decode($verify);

// Check if the CAPTCHA validation was successful
if ($captchaSuccess->success) {
    // If CAPTCHA validation passed, output the form HTML
    echo '
    <form action="process_form.php" method="POST">
        <input type="hidden" name="user_email" value="' . htmlspecialchars($email) . '">

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" required></textarea>

        <!-- Hidden Turnstile response input -->
        <input type="hidden" name="cf-turnstile-response" value="' . htmlspecialchars($turnstileResponse) . '">

        <button type="submit">Submit</button>
    </form>';
} else {
    // If CAPTCHA validation fails, do not show the form
    echo '<p>CAPTCHA validation failed. Please try again.</p>';
}
?>
