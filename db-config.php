<?php
// ========================================
// MySQL Database Configuration
// ========================================

// Database connection settings
define('DB_HOST', 'localhost');        // Database host (usually 'localhost')
define('DB_USER', 'root');             // Your MySQL username
define('DB_PASS', '');                 // Your MySQL password
define('DB_NAME', 'user_credentials_db'); // Database name

// Create database connection
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode([
            'success' => false,
            'error' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }

    // Set charset to utf8mb4 for full Unicode support
    $conn->set_charset('utf8mb4');

    return $conn;
}

// Close database connection
function closeDbConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Enable CORS for local development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
