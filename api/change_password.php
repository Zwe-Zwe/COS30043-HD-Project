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
    if (empty($data['userId']) || empty($data['currentPassword']) || empty($data['newPassword'])) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    $userId = $data['userId'];
    $currentPassword = $data['currentPassword'];
    $newPassword = $data['newPassword'];

    // First, verify the current password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit;
    }

    // Verify the current password
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
        exit;
    }

    // If verification passed, hash the new password and update it
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $success = $stmt->execute([$hashedPassword, $userId]);

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Password changed successfully'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to change password']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>