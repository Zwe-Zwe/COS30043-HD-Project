<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// For debugging - log all inputs to a file
$inputLog = fopen("order_debug.log", "a");
fwrite($inputLog, "=== " . date('Y-m-d H:i:s') . " ===\n");
fwrite($inputLog, "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n");
fwrite($inputLog, "Raw input: " . file_get_contents("php://input") . "\n\n");
fclose($inputLog);

// Database connection
require_once 'db_connect.php';

// Error handling function
function returnError($message)
{
    echo json_encode([
        "status" => "error",
        "message" => $message
    ]);
    exit;
}

// Function to validate input data
function validateOrderData($data)
{
    if (!isset($data->userId) || !isset($data->items) || !isset($data->total)) {
        return false;
    }
    return true;
}

try {
    // Create a new order
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the posted data
        $data = json_decode(file_get_contents("php://input"));

        // Check if JSON was parsed correctly
        if (json_last_error() !== JSON_ERROR_NONE) {
            returnError("Invalid JSON: " . json_last_error_msg());
        }

        // Log the parsed data for debugging
        $debugLog = fopen("order_parsed.log", "a");
        fwrite($debugLog, "=== " . date('Y-m-d H:i:s') . " ===\n");
        fwrite($debugLog, print_r($data, true) . "\n\n");
        fclose($debugLog);

        // Validate data
        if (!validateOrderData($data)) {
            returnError("Invalid order data structure");
        }

        try {
            $conn->begin_transaction();

            // Create order in the orders table
            $sql = "INSERT INTO orders (user_id, total_amount, payment_method, payment_status, shipping_address, status) 
                    VALUES (?, ?, ?, ?, ?, ?)";

            // Format shipping address as text
            $shippingAddress = "Name: {$data->shipping->firstName} {$data->shipping->lastName}\n";
            $shippingAddress .= "Address: {$data->shipping->address}\n";
            $shippingAddress .= "City: {$data->shipping->city}, State: {$data->shipping->state}, Zip: {$data->shipping->zip}\n";
            $shippingAddress .= "Phone: {$data->shipping->phone}\n";
            $shippingAddress .= "Email: {$data->shipping->email}";

            // Extract and store all values before binding
            $userId = intval($data->userId);
            $totalAmount = floatval($data->total);
            $paymentMethod = $data->payment->method;
            $paymentStatus = "paid"; // For simplicity, we're assuming payment is always successful
            $orderStatus = "processing"; // Initial status

            // Prepare statement and bind parameters
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "idssss",
                $userId,
                $totalAmount,
                $paymentMethod,
                $paymentStatus,
                $shippingAddress,
                $orderStatus
            );

            if ($stmt->execute()) {
                $orderId = $conn->insert_id;

                // Insert order items
                foreach ($data->items as $item) {
                    $sql = "INSERT INTO order_items (order_id, book_json_id, quantity, price) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);

                    // Extract values before binding
                    $bookId = intval($item->bookId);
                    $quantity = intval($item->quantity);
                    $price = floatval($item->price);

                    $stmt->bind_param("iidd", $orderId, $bookId, $quantity, $price);

                    if (!$stmt->execute()) {
                        throw new Exception("Failed to insert order item: " . $stmt->error);
                    }
                }

                // Commit transaction
                $conn->commit();

                echo json_encode([
                    "status" => "success",
                    "message" => "Order created successfully",
                    "orderId" => $orderId
                ]);
            } else {
                throw new Exception("Failed to create order: " . $stmt->error);
            }
        } catch (Exception $e) {
            // Roll back transaction if an error occurred
            $conn->rollback();

            returnError("Database error: " . $e->getMessage());
        }
    }

    // Get order by ID
    else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $orderId = $_GET['id'];

        try {
            // Get order details
            $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email 
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    WHERE o.id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $orderResult = $stmt->get_result();

            if ($orderResult->num_rows > 0) {
                $order = $orderResult->fetch_assoc();

                // Get order items
                $sql = "SELECT * FROM order_items WHERE order_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $orderId);
                $stmt->execute();
                $itemsResult = $stmt->get_result();

                $items = [];
                while ($item = $itemsResult->fetch_assoc()) {
                    $items[] = $item;
                }

                $order['items'] = $items;

                echo json_encode([
                    "status" => "success",
                    "order" => $order
                ]);
            } else {
                returnError("Order not found");
            }
        } catch (Exception $e) {
            returnError("Database error: " . $e->getMessage());
        }
    }

    // Get orders for a specific user
    else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
        $userId = $_GET['user_id'];

        try {
            // Get all orders for user
            $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $orders = [];
            while ($order = $result->fetch_assoc()) {
                // For each order, get its items
                $sql = "SELECT * FROM order_items WHERE order_id = ?";
                $itemStmt = $conn->prepare($sql);
                $itemStmt->bind_param("i", $order['id']);
                $itemStmt->execute();
                $itemsResult = $itemStmt->get_result();

                $items = [];
                while ($item = $itemsResult->fetch_assoc()) {
                    $items[] = $item;
                }

                $order['items'] = $items;
                $orders[] = $order;
            }

            echo json_encode([
                "status" => "success",
                "orders" => $orders
            ]);
        } catch (Exception $e) {
            returnError("Database error: " . $e->getMessage());
        }
    } else {
        // Invalid request
        returnError("Invalid request method");
    }
} catch (Exception $e) {
    // Catch any uncaught exceptions
    returnError("Server error: " . $e->getMessage());
}

$conn->close();
?>