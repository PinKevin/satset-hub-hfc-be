<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/config/eloquent.php';

// Test autoloader
echo "Testing autoloader...\n";
echo "PostController class exists: " . (class_exists('PostController') ? 'YES' : 'NO') . "\n";
echo "AuthController class exists: " . (class_exists('AuthController') ? 'YES' : 'NO') . "\n";

// Simulate the request
$request_method = 'GET';
$request_uri = '/api/posts';
$base_path = '/api';
$path = str_replace($base_path, '', $request_uri);
$path = trim($path, '/');

echo "\nRequest URI: $request_uri\n";
echo "Request Method: $request_method\n";
echo "Path after removing base: '$path'\n";

// Load routes
Route::loadRoutes(__DIR__ . '/routes/web.php');

// Test dispatch
try {
    $response = Route::dispatch($request_method, $path);
    echo "Response: " . json_encode($response) . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
