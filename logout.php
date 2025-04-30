<?php
/**
 * Logout script for CRM system
 * Destroys user session and redirects to login
 */

// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Set logout message
session_start();
$_SESSION['message'] = "You have been successfully logged out.";
$_SESSION['message_type'] = "success";

// Redirect to login page
header("Location: loginForm.php");
exit();
?>