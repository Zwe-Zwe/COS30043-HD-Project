<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

// Debug log file
$logFile = 'api_log.txt';
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - Request received\n", FILE_APPEND);

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - Invalid method: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    exit;
}

// Define possible locations for db.json
$possibleLocations = [
    '../public/db.json',
    '../../public/db.json',
    '../data/db.json',
    '../../data/db.json',
    '../db.json',
    '../../db.json',
    'db.json'
];

$booksData = null;
$locationFound = null;

// Try each location until we find the file
foreach ($possibleLocations as $location) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - Checking location: $location\n", FILE_APPEND);
    if (file_exists($location)) {
        $booksData = file_get_contents($location);
        $locationFound = $location;
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - Found db.json at: $location\n", FILE_APPEND);
        break;
    }
}

if ($booksData) {
    // Parse the JSON data
    $dbJson = json_decode($booksData, true);

    // Check if 'books' key exists
    if (isset($dbJson['books']) && is_array($dbJson['books'])) {
        $books = $dbJson['books'];
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - Found " . count($books) . " books in 'books' key\n", FILE_APPEND);
    } else {
        // Try to find books key or use the whole JSON as books array
        $books = $dbJson;
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - Using full JSON as books array\n", FILE_APPEND);

        // Look for a books array in the JSON
        foreach ($dbJson as $key => $value) {
            if (is_array($value) && isset($value[0]) && isset($value[0]['title'])) {
                $books = $value;
                file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - Found books under key: $key\n", FILE_APPEND);
                break;
            }
        }
    }

    // Output the books array
    echo json_encode($books);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - Response sent\n", FILE_APPEND);
} else {
    // No books found
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "GET BOOKS - No db.json found\n", FILE_APPEND);

    // Return empty array instead of error
    echo json_encode([]);
}
?>