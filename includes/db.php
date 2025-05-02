<?php
$host = 'localhost';
$dbname = 'crm_portal';  // Use the name you created
$username = 'root';      // Replace with your DB username
$password = '';          // Replace with your DB password if any

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
