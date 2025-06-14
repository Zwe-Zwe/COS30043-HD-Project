<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$status = [
    'status' => 'ok',
    'message' => 'API is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'
    ]
];

echo json_encode($status);
?>