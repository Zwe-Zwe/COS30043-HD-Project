<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Debug log file
$logFile = 'api_log.txt';
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Request received\n", FILE_APPEND);

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Invalid method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    exit;
}

// Log all received data
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Get data from POST
$orderId = isset($_POST['order_id']) ? $_POST['order_id'] : null;

// Validate data
if (!$orderId) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Missing order ID\n", FILE_APPEND);
    exit;
}

// Database connection
require_once 'db_connection.php';

try {
    // Start transaction
    $conn->begin_transaction();
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Transaction started\n", FILE_APPEND);

    // First delete all order items
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $itemsDeleted = $stmt->affected_rows;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Items deleted: $itemsDeleted\n", FILE_APPEND);

    // Then delete the order
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $orderDeleted = $stmt->affected_rows;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Order deleted: $orderDeleted\n", FILE_APPEND);

    if ($orderDeleted > 0) {
        // Commit transaction
        $conn->commit();
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Transaction committed\n", FILE_APPEND);
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } else {
        // Rollback transaction
        $conn->rollback();
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Transaction rolled back (Order not found)\n", FILE_APPEND);
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
    }
} catch (Exception $e) {
    // Rollback on error
    if ($conn->connect_errno === 0) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "DELETE ORDER - Error: " . $e->getMessage() . "\n", FILE_APPEND);
}

$conn->close();
?>