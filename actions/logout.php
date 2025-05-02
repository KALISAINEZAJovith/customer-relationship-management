<?php
$conn = new mysqli("localhost", "root", "", "crm_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
session_unset();  // Clear session variables
session_destroy(); // Destroy the session
header("Location: ../views/loginform.php "); // Redirect to login page
exit;
