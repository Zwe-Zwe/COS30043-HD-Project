<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "booknest";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Log successful connection
file_put_contents('db_log.txt', date('[Y-m-d H:i:s] ') . "Database connection established\n", FILE_APPEND);
?>