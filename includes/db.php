<?php
// Database connection settings
$host = 'localhost';
$dbname = 'crm_system';
$username = 'root';
$password = '';

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] == $role;
}

// Function to redirect users based on role
function redirectToUserDashboard() {
    if (isset($_SESSION['role'])) {
        switch($_SESSION['role']) {
            case 'admin':
                header('Location: admin/dashboard.php');
                break;
            case 'manager':
                header('Location: manager/dashboard.php');
                break;
            case 'employee':
                header('Location: employee/dashboard.php');
                break;
            default:
                header('Location: loginform.php');
        }
        exit;
    }
}

// Function to secure against XSS
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>