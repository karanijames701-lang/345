<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db-config.php';

echo "<h1>Database Connection Test</h1>";

// Test connection
$conn = getDbConnection();

if (!$conn) {
    echo "<p style='color: red;'>❌ Database connection FAILED</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>MySQL is running</li>";
    echo "<li>Database 'user_credentials_db' exists</li>";
    echo "<li>Credentials in db-config.php are correct</li>";
    echo "</ul>";
} else {
    echo "<p style='color: green;'>✓ Database connection successful</p>";

    // Test if table exists
    $result = $conn->query("SHOW TABLES LIKE 'user_credentials'");

    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Table 'user_credentials' exists</p>";

        // Count records
        $count = $conn->query("SELECT COUNT(*) as count FROM user_credentials");
        $row = $count->fetch_assoc();
        echo "<p>Records in database: " . $row['count'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Table 'user_credentials' does NOT exist</p>";
        echo "<p>Please import database.sql into phpMyAdmin</p>";
    }

    closeDbConnection($conn);
}
?>
