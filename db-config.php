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
        error_log('Database connection failed: ' . $conn->connect_error);
        return null;
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
?>
