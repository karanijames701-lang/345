<?php
// ========================================
// User Credentials API
// Handles save and verify operations
// ========================================

// Enable CORS for API requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db-config.php';

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Route requests
switch ($action) {
    case 'save':
        if ($method === 'POST') {
            saveCredentials($input);
        } else {
            sendError('Method not allowed', 405);
        }
        break;

    case 'verify':
        if ($method === 'POST') {
            verifyCredentials($input);
        } else {
            sendError('Method not allowed', 405);
        }
        break;

    case 'list':
        if ($method === 'GET') {
            listCredentials();
        } else {
            sendError('Method not allowed', 405);
        }
        break;

    default:
        sendError('Invalid action', 400);
}

// ========================================
// Save user credentials
// ========================================
function saveCredentials($input) {
    // Validate input
    if (!isset($input['username']) || !isset($input['password'])) {
        sendError('Username and password are required');
        return;
    }

    $username = trim($input['username']);
    $password = $input['password'];
    $accountType = isset($input['accountType']) ? $input['accountType'] : 'personal';

    if (empty($username) || empty($password)) {
        sendError('Username and password cannot be empty');
        return;
    }

    // Hash password using SHA-256
    $passwordHash = hash('sha256', $password);

    // Connect to database
    $conn = getDbConnection();

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO user_credentials (username, password_hash, account_type) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $passwordHash, $accountType);

    // Execute
    if ($stmt->execute()) {
        sendSuccess([
            'id' => $conn->insert_id,
            'username' => $username,
            'account_type' => $accountType
        ], 'Credentials saved successfully');
    } else {
        if ($conn->errno === 1062) {
            sendError('Username already exists');
        } else {
            sendError('Failed to save credentials: ' . $stmt->error);
        }
    }

    $stmt->close();
    closeDbConnection($conn);
}

// ========================================
// Verify user credentials
// ========================================
function verifyCredentials($input) {
    // Validate input
    if (!isset($input['username']) || !isset($input['password'])) {
        sendError('Username and password are required');
        return;
    }

    $username = trim($input['username']);
    $password = $input['password'];

    // Hash password using SHA-256
    $passwordHash = hash('sha256', $password);

    // Connect to database
    $conn = getDbConnection();

    // Prepare statement
    $stmt = $conn->prepare("SELECT id, username, account_type, created_at, last_login FROM user_credentials WHERE username = ? AND password_hash = ?");
    $stmt->bind_param("ss", $username, $passwordHash);

    // Execute
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Update last login
        $updateStmt = $conn->prepare("UPDATE user_credentials SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $updateStmt->bind_param("s", $user['id']);
        $updateStmt->execute();
        $updateStmt->close();

        sendSuccess([
            'user' => $user
        ], 'Login successful');
    } else {
        sendError('Invalid username or password', 401);
    }

    $stmt->close();
    closeDbConnection($conn);
}

// ========================================
// List all credentials (for admin/debug)
// ========================================
function listCredentials() {
    $conn = getDbConnection();

    $result = $conn->query("SELECT id, username, account_type, created_at, last_login FROM user_credentials ORDER BY created_at DESC");

    $credentials = [];
    while ($row = $result->fetch_assoc()) {
        $credentials[] = $row;
    }

    sendSuccess(['credentials' => $credentials]);

    closeDbConnection($conn);
}

// ========================================
// Helper functions
// ========================================
function sendSuccess($data = [], $message = 'Success') {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit();
}
?>
