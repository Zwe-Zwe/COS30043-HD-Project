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
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Request received\n", FILE_APPEND);

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Invalid method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    exit;
}

// Log all received data
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Get data from POST
$itemId = isset($_POST['item_id']) ? $_POST['item_id'] : null;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : null;

// Validate data
if (!$itemId || $quantity === null || $quantity < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Valid item ID and quantity are required']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Invalid data: Item ID=$itemId, Quantity=$quantity\n", FILE_APPEND);
    exit;
}

// Database connection
require_once 'db_connection.php';

try {
    // Start transaction
    $conn->begin_transaction();
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Transaction started\n", FILE_APPEND);

    // Get the current item details
    $stmt = $conn->prepare("SELECT order_id, quantity, price FROM order_items WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $conn->rollback();
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Item not found, transaction rolled back\n", FILE_APPEND);
        http_response_code(404);
        echo json_encode(['error' => 'Item not found']);
        exit;
    }

    $item = $result->fetch_assoc();
    $orderId = $item['order_id'];
    $oldQuantity = $item['quantity'];
    $price = $item['price'];

    // Calculate price difference
    $priceDifference = ($quantity - $oldQuantity) * $price;

    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Current: Qty=$oldQuantity, New: Qty=$quantity, Price=$price, Diff=$priceDifference\n", FILE_APPEND);

    // Update the item quantity
    $stmt = $conn->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $itemId);
    $stmt->execute();

    // Check if update was successful
    if ($stmt->affected_rows >= 0) {
        // If price difference exists, update the order total
        if ($priceDifference != 0) {
            $stmt = $conn->prepare("UPDATE orders SET total_amount = total_amount + ? WHERE id = ?");
            $stmt->bind_param("di", $priceDifference, $orderId);
            $stmt->execute();
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Order total updated\n", FILE_APPEND);
        }

        // Commit transaction
        $conn->commit();
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Transaction committed\n", FILE_APPEND);

        echo json_encode([
            'success' => true,
            'message' => 'Item quantity updated successfully',
            'old_quantity' => $oldQuantity,
            'new_quantity' => $quantity
        ]);
    } else {
        // Rollback transaction
        $conn->rollback();
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Failed to update item quantity, transaction rolled back\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update item quantity']);
    }
} catch (Exception $e) {
    // Rollback on error
    if ($conn->connect_errno === 0) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "UPDATE ITEM - Error: " . $e->getMessage() . "\n", FILE_APPEND);
}

$conn->close();
?>