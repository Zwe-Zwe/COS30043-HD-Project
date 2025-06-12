<?php
// CORS headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Load environment variables
$envFile = __DIR__ . '/.env.php';

// Default database configuration
$host = 'localhost';
$username = 'root';
$password = '';

// Try to load from environment if available
if (file_exists($envFile)) {
    $config = include($envFile);
    if (isset($config['host']) && isset($config['username'])) {
        $host = $config['host'];
        $username = $config['username'];
        $password = $config['password'] ?? '';
    }
}

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/../schema.sql');

    // Execute SQL script
    $pdo->exec($sql);

    // Response
    echo json_encode([
        'status' => 'success',
        'message' => 'Database initialized successfully'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database initialization failed: ' . $e->getMessage()
    ]);
}
