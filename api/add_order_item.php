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
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Request received\n", FILE_APPEND);

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Invalid method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    exit;
}

// Log all received data
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Get data from POST
$orderId = isset($_POST['order_id']) ? $_POST['order_id'] : null;
$bookJsonId = isset($_POST['book_json_id']) ? $_POST['book_json_id'] : null;
$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
$price = isset($_POST['price']) ? $_POST['price'] : null;

// Validate data
if (!$orderId || !$bookJsonId || !$price) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID, book ID, and price are required']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Missing required fields\n", FILE_APPEND);
    exit;
}

// Database connection
require_once 'db_connection.php';

try {
    // Start transaction
    $conn->begin_transaction();
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Transaction started\n", FILE_APPEND);

    // Insert order item
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_json_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $orderId, $bookJsonId, $quantity, $price);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $newItemId = $conn->insert_id;
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Item added with ID: $newItemId\n", FILE_APPEND);

        // Calculate total item price
        $totalItemPrice = $quantity * $price;

        // Update order total amount
        $stmt = $conn->prepare("UPDATE orders SET total_amount = total_amount + ? WHERE id = ?");
        $stmt->bind_param("di", $totalItemPrice, $orderId);
        $stmt->execute();

        if ($stmt->affected_rows >= 0) {
            // Commit transaction
            $conn->commit();
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Transaction committed\n", FILE_APPEND);

            echo json_encode([
                'success' => true,
                'message' => 'Item added successfully',
                'item_id' => $newItemId
            ]);
        } else {
            // Rollback transaction
            $conn->rollback();
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Could not update order total, transaction rolled back\n", FILE_APPEND);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update order total']);
        }
    } else {
        // Rollback transaction
        $conn->rollback();
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Failed to add item, transaction rolled back\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add item to order']);
    }
} catch (Exception $e) {
    // Rollback on error
    if ($conn->connect_errno === 0) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ADD ITEM - Error: " . $e->getMessage() . "\n", FILE_APPEND);
}

$conn->close();
?>