<?php
// CORS headers
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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

// Get environment configuration
$envFile = __DIR__ . '/.env.php';
if (!file_exists($envFile)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Configuration file not found']);
    exit;
}

$config = include($envFile);

// Validate configuration
if (
    !isset($config['host']) || !isset($config['dbname']) ||
    !isset($config['username']) || !isset($config['password'])
) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Invalid configuration file']);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Parse request data
    $data = json_decode(file_get_contents("php://input"), true);
    $userId = isset($data['userId']) ? $data['userId'] : null;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Validate session logic would go here
        echo json_encode(['status' => 'success', 'valid' => true]);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // If user ID is provided, validate and update the session
        if ($userId) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                unset($user['password']); // Don't return password
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Session validated',
                    'user' => $user
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid session'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No user ID provided'
            ]);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
