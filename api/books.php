<?php
// CORS headers
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load the books JSON file
$booksFile = __DIR__ . '/../db.json';
if (!file_exists($booksFile)) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Books data not found']);
    exit;
}

// Read and output the JSON content
$jsonContent = file_get_contents($booksFile);
echo $jsonContent;
?>