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
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Request received\n", FILE_APPEND);

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Invalid method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    exit;
}

// Log all received data
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Get data from POST
$itemId = isset($_POST['item_id']) ? $_POST['item_id'] : null;

// Validate data
if (!$itemId) {
    http_response_code(400);
    echo json_encode(['error' => 'Item ID is required']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Missing item ID\n", FILE_APPEND);
    exit;
}

// Database connection
require_once 'db_connection.php';

try {
    // Start transaction
    $conn->begin_transaction();
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Transaction started\n", FILE_APPEND);

    // Get the order ID, quantity, and price for the item
    $stmt = $conn->prepare("SELECT order_id, quantity, price FROM order_items WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $conn->rollback();
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Item not found, transaction rolled back\n", FILE_APPEND);
        http_response_code(404);
        echo json_encode(['error' => 'Item not found']);
        exit;
    }

    $item = $result->fetch_assoc();
    $orderId = $item['order_id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $totalItemPrice = $quantity * $price;

    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Item found: Order ID: $orderId, Qty: $quantity, Price: $price\n", FILE_APPEND);

    // Delete the order item
    $stmt = $conn->prepare("DELETE FROM order_items WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Update order total amount
        $stmt = $conn->prepare("UPDATE orders SET total_amount = total_amount - ? WHERE id = ?");
        $stmt->bind_param("di", $totalItemPrice, $orderId);
        $stmt->execute();

        if ($stmt->affected_rows >= 0) {
            // Commit transaction
            $conn->commit();
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Transaction committed\n", FILE_APPEND);

            echo json_encode([
                'success' => true,
                'message' => 'Item removed successfully'
            ]);
        } else {
            // Rollback transaction
            $conn->rollback();
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Could not update order total, transaction rolled back\n", FILE_APPEND);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update order total']);
        }
    } else {
        // Rollback transaction
        $conn->rollback();
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Failed to remove item, transaction rolled back\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to remove item']);
    }
} catch (Exception $e) {
    // Rollback on error
    if ($conn->connect_errno === 0) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REMOVE ITEM - Error: " . $e->getMessage() . "\n", FILE_APPEND);
}

$conn->close();
?>