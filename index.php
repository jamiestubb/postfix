<?php
// index.php - Central routing file

// Get the requested path (e.g., "/validate_captcha.php")
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Define a list of valid paths and their corresponding files
$routes = [
    "/" => "home.php",                   // Main page (formerly index.html)
    "/validate_captcha.php" => "validate_captcha.php",
    "/process_form.php" => "process_form.php",
    "/protected_form.php" => "protected_form.php",
    "/protected_redirect.php" => "protected_redirect.php",
    "/404" => "404.php"                   // Custom 404 page (optional direct access)
];

// Check if the requested path exists in the routes array
if (array_key_exists($requestPath, $routes)) {
    // Include the corresponding file
    include $routes[$requestPath];
} else {
    // If the path does not exist, set a 404 response code and show 404 page
    http_response_code(404);
    include "404.php";
}
