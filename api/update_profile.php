<?php
// CORS headers
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

    // Get the data from request
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (empty($data['id']) || empty($data['name']) || empty($data['email']) || empty($data['address'])) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    $userId = $data['id'];
    $name = $data['name'];
    $email = $data['email'];
    $address = $data['address'];

    // Check if email already exists (except for current user)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $userId]);

    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already in use by another account']);
        exit;
    }

    // Update the user profile
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, address = ? WHERE id = ?");
    $success = $stmt->execute([$name, $email, $address, $userId]);

    if ($success) {
        // Get updated user data
        $stmt = $pdo->prepare("SELECT id, name, email, address, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>