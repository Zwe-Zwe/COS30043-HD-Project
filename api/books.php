<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

// Log file for debugging
$logFile = 'api_log.txt';
file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "BOOKS API - Request received\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Define possible locations for db.json
$possibleLocations = [
    '../public/db.json',
    '../db.json',
    '../../db.json',
    'db.json',
    '../src/assets/db.json',
    '../assets/db.json'
];

$foundLocation = null;
$jsonData = null;

// Try each location until we find the file
foreach ($possibleLocations as $location) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Checking for db.json at: $location\n", FILE_APPEND);
    if (file_exists($location)) {
        $jsonData = file_get_contents($location);
        $foundLocation = $location;
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Found db.json at: $location\n", FILE_APPEND);
        break;
    }
}

if ($jsonData) {
    $dbData = json_decode($jsonData, true);
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "JSON data structure: " . print_r(array_keys($dbData), true) . "\n", FILE_APPEND);

    // Check if 'books' key exists directly in the data
    if (isset($dbData['books']) && is_array($dbData['books'])) {
        echo json_encode($dbData);  // Return the full structure
        exit;
    }

    // Try to find books array
    foreach ($dbData as $key => $value) {
        if (is_array($value) && !empty($value) && isset($value[0]['title'])) {
            // Create a wrapper structure with 'books' key
            echo json_encode(['books' => $value]);
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Found books under key: $key, wrapped and returned\n", FILE_APPEND);
            exit;
        }
    }

    // If no books array was found, return the whole data structure
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Returning full JSON structure\n", FILE_APPEND);
    echo json_encode($dbData);
} else {
    // Return empty books array if db.json not found
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "db.json not found, returning empty books array\n", FILE_APPEND);
    echo json_encode(['books' => []]);
}
?>