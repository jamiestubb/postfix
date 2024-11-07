<?php
// PHP file to handle the form action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $text_captcha = $_POST['text_captcha'];
    $turnstile_response = $_POST['cf-turnstile-response']; // Cloudflare Turnstile response

    // Validate the text CAPTCHA by comparing with a stored answer (for example purposes)
    session_start();
    if ($text_captcha !== $_SESSION['captcha_text']) {
        echo "Text CAPTCHA verification failed. Please try again.";
        exit;
    }

    // Validate Cloudflare Turnstile response
    $secret_key = '0x4AAAAAAAzbaFyF5jnLHaBSyZ5AuNHu098';
    $verify_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $post_data = http_build_query([
        'secret' => $secret_key,
        'response' => $turnstile_response,
    ]);

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => $post_data,
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($verify_url, false, $context);
    $verification = json_decode($result);

    if (!$verification || !$verification->success) {
        echo "Cloudflare CAPTCHA verification failed. Please try again.";
        exit;
    }

    // Proceed if both CAPTCHAs are verified
    echo "CAPTCHA verified successfully! Proceeding with email: $email";
    // Process the form data here
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dual CAPTCHA Verification</title>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  <style>
    body, html {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }
    .container {
      text-align: left;
      background-color: #fff;
      padding: 20px;
      width: 320px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }
    .captcha-image {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .captcha-input {
      width: 100%;
      padding: 8px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    button {
      width: 100%;
      padding: 10px;
      background-color: #0078d4;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1em;
    }
  </style>
</head>
<body>
  <div class="container">
    <form id="captcha-form" action="validate_captcha.php" method="POST">
      <!-- Email input -->
      <label for="email">Email or Username:</label>
      <input type="email" id="email" name="email" required>
      <div class="helper-text">Example: user@contoso.onmicrosoft.com or user@contoso.com</div>

      <!-- Text-based CAPTCHA -->
      <label for="text-captcha">Enter the characters in the image:</label>
      <div class="captcha-image">
        <img src="generate_captcha.php" alt="CAPTCHA Image" id="captcha-image">
        <button type="button" onclick="refreshCaptcha()">↻</button>
      </div>
      <input type="text" id="text-captcha" name="text_captcha" class="captcha-input" required>

      <!-- Cloudflare Turnstile CAPTCHA -->
      <div class="cf-turnstile" 
           data-sitekey="0x4AAAAAAAzbaCIIxhpKU4HJ" 
           data-callback="onTurnstileVerified">
      </div>

      <!-- Submit button, enabled only after CAPTCHA verification -->
      <button id="next-button" type="submit" disabled>Next</button>
    </form>
  </div>

  <script>
    // Refresh the text CAPTCHA image
    function refreshCaptcha() {
      document.getElementById("captcha-image").src = "generate_captcha.php?" + Date.now();
    }

    // Enable the submit button only after Turnstile CAPTCHA is verified
    function onTurnstileVerified(token) {
      document.getElementById('next-button').disabled = false;
    }
  </script>
</body>
</html>

