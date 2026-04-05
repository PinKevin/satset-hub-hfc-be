<?php

echo "Setting up PHP API with Authentication and CRUD...\n\n";

if (!file_exists('composer.phar') && !shell_exec('which composer')) {
    echo "Error: Composer is not installed. Please install Composer first.\n";
    echo "Visit: https://getcomposer.org/download/\n";
    exit(1);
}

echo "Installing dependencies...\n";
exec('composer install', $output, $return_var);

if ($return_var !== 0) {
    echo "Error installing dependencies.\n";
    exit(1);
}

echo "Dependencies installed successfully.\n\n";

if (!file_exists('.env')) {
    echo "Creating .env file...\n";
    copy('.env.example', '.env') ?: file_put_contents('.env', "DB_HOST=localhost\nDB_DATABASE=example\nDB_USERNAME=root\nDB_PASSWORD=\nJWT_SECRET=your-secret-key-change-this-in-production\nJWT_EXPIRE=86400\nAPP_DEBUG=true\nAPP_URL=http://localhost:8000\n");
}

require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Setting up database...\n";

try {
    $pdo = new PDO("mysql:host=" . $_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $_ENV['DB_DATABASE'] . "`");
    echo "Database '" . $_ENV['DB_DATABASE'] . "' created/verified.\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database credentials in .env file.\n";
    exit(1);
}

echo "\nRunning database migrations...\n";

echo "Creating users table...\n";
require_once __DIR__ . '/database/migrations/create_users_table.php';

echo "Creating posts table...\n";
require_once __DIR__ . '/database/migrations/create_posts_table.php';

echo "\nSetup completed successfully!\n";
echo "\nNext steps:\n";
echo "1. Configure your web server to point to this directory\n";
echo "2. Update .env file with your database credentials if needed\n";
echo "3. Test the API endpoints\n";
echo "\nAPI Documentation:\n";
echo "- Register: POST /api/auth/register\n";
echo "- Login: POST /api/auth/login\n";
echo "- Get Posts: GET /api/posts (requires auth)\n";
echo "- Create Post: POST /api/posts (requires auth)\n";
echo "- Update Post: PUT /api/posts/{id} (requires auth)\n";
echo "- Delete Post: DELETE /api/posts/{id} (requires auth)\n";
echo "\nExample usage:\n";
echo "curl -X POST http://localhost/api/auth/register -H \"Content-Type: application/json\" -d '{\"name\":\"John Doe\",\"email\":\"john@example.com\",\"password\":\"password123\"}'\n";
?>
