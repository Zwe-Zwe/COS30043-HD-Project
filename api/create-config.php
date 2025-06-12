<?php
// This script helps set up initial configuration
// Only run this manually when setting up a new environment

// Prevent web access in production
if (!isset($_GET['setup']) || $_GET['setup'] !== 'true') {
    http_response_code(403);
    echo "Access denied. This utility is for initial setup only.";
    exit;
}

// Define the .env.php file path
$envFile = __DIR__ . '/.env.php';

// Check if file already exists
if (file_exists($envFile)) {
    echo "Configuration file already exists. Delete it first if you want to create a new one.<br>";
    echo "Located at: " . $envFile;
    exit;
}

// Default configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'booknest';

// Create configuration content
$content = <<<EOT
<?php
// Database configuration
return [
    'host' => '$host',
    'username' => '$username',
    'password' => '$password',
    'dbname' => '$dbname'
];
EOT;

// Write to file
if (file_put_contents($envFile, $content)) {
    // Initialize database
    try {
        // Try to create database
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Read SQL file
        $sql = file_get_contents(__DIR__ . '/../schema.sql');

        // Execute SQL script
        $pdo->exec($sql);

        echo "<h2>Success!</h2>";
        echo "Configuration file created and database initialized successfully.<br>";
        echo "You can now use the application.";
    } catch (PDOException $e) {
        echo "<h2>Partial Success</h2>";
        echo "Configuration file created successfully, but database initialization failed.<br>";
        echo "Error: " . $e->getMessage() . "<br>";
        echo "You may need to create the database manually using the schema.sql file.";
    }
} else {
    echo "<h2>Error</h2>";
    echo "Failed to create configuration file. Check permissions on the directory.";
}
?>