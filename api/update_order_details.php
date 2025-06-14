<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Create a log file for debugging
$logFile = 'order_details_log.txt';
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Update order details - Request received\n", FILE_APPEND);
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "REQUEST METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "POST DATA: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Invalid method\n", FILE_APPEND);
    exit;
}

// Required parameters
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
$payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : null;
$shipping_address = isset($_POST['shipping_address']) ? $_POST['shipping_address'] : null;
$customer_notes = isset($_POST['customer_notes']) ? $_POST['customer_notes'] : null;

// Validate order ID
if (!$order_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Missing order ID\n", FILE_APPEND);
    exit;
}

// Connect to database - using require to ensure it fails if file isn't found
require_once 'db_connection.php';

try {
    // Start building the SQL query
    $sql = "UPDATE orders SET ";
    $updates = [];
    $params = [];
    $types = "";

    // Add fields to update only if they are provided
    if ($payment_method !== null) {
        $updates[] = "payment_method = ?";
        $params[] = $payment_method;
        $types .= "s";
    }

    if ($payment_status !== null) {
        $updates[] = "payment_status = ?";
        $params[] = $payment_status;
        $types .= "s";
    }

    if ($shipping_address !== null) {
        $updates[] = "shipping_address = ?";
        $params[] = $shipping_address;
        $types .= "s";
    }

    if ($customer_notes !== null) {
        $updates[] = "customer_notes = ?";
        $params[] = $customer_notes;
        $types .= "s";
    }

    // If no fields to update, return success but note that nothing was updated
    if (empty($updates)) {
        echo json_encode(['success' => true, 'message' => 'No fields to update']);
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "No fields to update\n", FILE_APPEND);
        exit;
    }

    // Complete the SQL query
    $sql .= implode(", ", $updates) . " WHERE id = ?";
    $params[] = $order_id;
    $types .= "i";  // Order ID is an integer

    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "SQL: $sql\n", FILE_APPEND);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Parameters: " . print_r($params, true) . "\n", FILE_APPEND);

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $result = $stmt->execute();

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Order details updated successfully',
            'affected_rows' => $stmt->affected_rows
        ]);
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Update successful\n", FILE_APPEND);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

} catch (Exception $e) {
    // Log error for debugging
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);

    // Send error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating order details: ' . $e->getMessage()
    ]);
}

// Close connection
if (isset($conn)) {
    $conn->close();
}
?>