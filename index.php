<?php
/**
 * Main entry point for the Satset Hub HFC API.
 * This file handles routing and CORS for both development (php -S) 
 * and production environments.
 */

// Handle CORS
// We allow all origins for now as it's a local development setup.
// In production, this should be more restrictive.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Support for PHP Built-in Server (php -S)
// This part ensures that static files are served correctly if they exist.
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path !== '/' && file_exists(__DIR__ . $path)) {
        return false; // serve the requested resource as-is
    }
}

// Forward all other requests to the API handler
require_once __DIR__ . '/api/index.php';
