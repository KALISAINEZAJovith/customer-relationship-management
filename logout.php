<?php
session_start();
session_destroy();
header("Location: loginForm.php");
exit();
?>

// File: includes/db.php
<?php
$conn = new mysqli('localhost', 'root', '', 'crm');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>