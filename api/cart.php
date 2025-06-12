<?php
// CORS headers
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load environment variables
$envFile = __DIR__ . '/.env.php';
if (!file_exists($envFile)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Configuration file not found']);
    exit;
}

$config = include($envFile);

// Verify configuration
if (!isset($config['host']) || !isset($config['dbname']) || !isset($config['username']) || !isset($config['password'])) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Invalid configuration file']);
    exit;
}

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']}", $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle different HTTP methods
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Get cart items for a user
            if (!isset($_GET['user_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
                exit;
            }

            $userId = $_GET['user_id'];

            // Debug: Log the userID being queried
            error_log("Fetching cart for user ID: " . $userId);

            // No need to check for column name since we know it's book_json_id
            $bookIdColumn = 'book_json_id';

            // Get cart items from database using book_json_id
            $stmt = $pdo->prepare("SELECT $bookIdColumn as book_json_id, quantity FROM carts WHERE user_id = ?");
            $stmt->execute([$userId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug: Log the items found
            error_log("Found " . count($items) . " cart items for user " . $userId);
            error_log("Items: " . json_encode($items));

            echo json_encode(['status' => 'success', 'items' => $items]);
            break;

        case 'POST':
            // Update cart items
            $data = json_decode(file_get_contents('php://input'), true);

            // Debug: Log the received data
            error_log("POST data received: " . json_encode($data));

            if (!isset($data['user_id']) || !isset($data['items'])) {
                echo json_encode(['status' => 'error', 'message' => 'User ID and items are required']);
                exit;
            }

            $userId = $data['user_id'];
            $items = $data['items'];

            // Debug: Log the items to be inserted
            error_log("Items to insert: " . json_encode($items));

            // Begin transaction
            $pdo->beginTransaction();

            try {
                // Clear existing cart for this user
                $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
                $stmt->execute([$userId]);

                // Debug: Log deletion
                error_log("Deleted existing cart items for user: " . $userId);

                // Insert new cart items
                if (!empty($items)) {
                    $stmt = $pdo->prepare("INSERT INTO carts (user_id, book_json_id, quantity) VALUES (?, ?, ?)");

                    foreach ($items as $item) {
                        // Make sure we have the right field
                        $bookId = isset($item['book_json_id']) ? $item['book_json_id'] : $item['book_id'];
                        $quantity = isset($item['quantity']) ? $item['quantity'] : 1;

                        error_log("Inserting: user_id={$userId}, book_json_id={$bookId}, quantity={$quantity}");
                        $stmt->execute([$userId, $bookId, $quantity]);
                    }
                }

                // Commit transaction
                $pdo->commit();
                echo json_encode(['status' => 'success', 'message' => 'Cart updated successfully']);
            } catch (Exception $e) {
                // Rollback on error
                $pdo->rollBack();
                error_log("Cart update error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Error updating cart: ' . $e->getMessage()]);
                exit;
            }
            break;

        case 'DELETE':
            // Remove an item from cart
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['user_id']) || !isset($data['book_id'])) {
                echo json_encode(['status' => 'error', 'message' => 'User ID and Book ID are required']);
                exit;
            }

            $userId = $data['user_id'];
            $bookId = $data['book_id'];

            $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ? AND book_json_id = ?");
            $stmt->execute([$userId, $bookId]);

            echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>