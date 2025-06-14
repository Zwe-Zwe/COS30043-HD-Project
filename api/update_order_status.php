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
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE STATUS - Request received\n", FILE_APPEND);
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get order ID and status from POST data
$orderId = isset($_POST['order_id']) ? $_POST['order_id'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;

file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Extracted data - Order ID: $orderId, Status: $status\n", FILE_APPEND);

// Validate input
if (!$orderId || !$status) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID and status are required']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Error: Missing required fields\n", FILE_APPEND);
    exit;
}

// Connect to database
require_once 'db_connection.php';

try {
    // Update the order status in the database
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    $stmt->execute();

    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Query executed. Affected rows: " . $stmt->affected_rows . "\n", FILE_APPEND);

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Success response sent\n", FILE_APPEND);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made to order status']);
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "No changes response sent\n", FILE_APPEND);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Error: " . $e->getMessage() . "\n", FILE_APPEND);
}

$conn->close();
?>