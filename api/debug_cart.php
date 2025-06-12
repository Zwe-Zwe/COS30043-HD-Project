<?php
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
$envFile = __DIR__ . '/.env.php';
if (!file_exists($envFile)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Configuration file not found']);
    exit;
}

$config = include($envFile);

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get table information
    $stmt = $pdo->query("DESCRIBE carts");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get a sample of cart data
    $stmtData = $pdo->query("SELECT * FROM carts LIMIT 10");
    $cartData = $stmtData->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'tableInfo' => $columns,
        'sampleData' => $cartData
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>