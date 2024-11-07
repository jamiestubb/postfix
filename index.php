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
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
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
      background-color: #fff;
      padding: 20px;
      width: 400px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }
    h1 {
      font-size: 1.5em;
      margin-bottom: 8px;
    }
    .description {
      font-size: 0.9em;
      color: #555;
      margin-bottom: 20px;
      font-style: italic;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
      margin-bottom: 5px;
    }
    .helper-text {
      font-size: 0.85em;
      color: #555;
      margin-top: 5px;
    }
    .captcha-image {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }
    .captcha-input, #email {
      font-size: 16px;
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      margin-top: 5px;
    }
    .error-message {
      color: red;
      font-size: 0.9em;
      margin-bottom: 10px;
    }
    .button-container {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }
    .next-button, .cancel-button {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1em;
    }
    .next-button {
      background-color: #0078d4;
      color: #fff;
    }
    .cancel-button {
      background-color: transparent;
      color: #0078d4;
      text-decoration: underline;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Who are you?</h1>
    <p class="description">To recover your account, begin by entering your email or username and the characters in the picture or audio below.</p>
    <?php
    session_start();
    if (isset($_SESSION['error_message'])) {
        echo "<p class='error-message'>" . $_SESSION['error_message'] . "</p>";
        unset($_SESSION['error_message']); // Clear the error message after displaying it
    }
    ?>
    <form id="captcha-form" action="validate_captcha.php" method="POST">
      <!-- Email input -->
      <label for="email">Email or Username: <span style="color: red;">*</span></label>
      <input type="email" id="email" name="email" required>
      <div class="helper-text">Example: user@contoso.onmicrosoft.com or user@contoso.com</div>

      <!-- Text-based CAPTCHA -->
      <label for="text-captcha">Enter the characters in the picture or the words in the audio: <span style="color: red;">*</span></label>
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

      <!-- Submit and Cancel buttons -->
      <div class="button-container">
        <button id="next-button" class="next-button" type="submit" disabled>Next</button>
        <button type="button" class="cancel-button" onclick="window.location.href='#'">Cancel</button>
      </div>
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
