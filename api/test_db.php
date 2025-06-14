<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Try to connect to the database
try {
    require_once 'db_connection.php';

    // Check if connection is valid
    if ($conn->connect_error) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database connection failed: ' . $conn->connect_error
        ]);
        exit;
    }

    // Test query - get tables
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    // Test query - count orders
    $orderCount = 0;
    $result = $conn->query("SELECT COUNT(*) as count FROM orders");
    if ($result) {
        $row = $result->fetch_assoc();
        $orderCount = $row['count'];
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection successful',
        'database' => [
            'name' => $database ?? 'booknest',
            'tables' => $tables,
            'orders_count' => $orderCount,
        ],
        'server' => [
            'php_version' => phpversion(),
            'mysql_version' => $conn->server_info,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ]
    ]);

    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Exception: ' . $e->getMessage()
    ]);
}
?>