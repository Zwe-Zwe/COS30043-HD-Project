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

// Fix the path to the environment file
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

  $data = json_decode(file_get_contents('php://input'), true);

  // Validate input
  if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['address'])) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
  }

  $name = $data['name'];
  $email = $data['email'];
  $password = password_hash($data['password'], PASSWORD_BCRYPT);
  $address = $data['address'];

  // Check for duplicate email
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
  $stmt->execute([$email]);
  if ($stmt->fetchColumn() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO users (name, email, password, address) VALUES (?, ?, ?, ?)");
  $stmt->execute([$name, $email, $password, $address]);

  echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $e->getMessage()]);
}
?>