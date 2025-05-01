<?php
$host = 'localhost';
$db   = 'crm';
$user = 'root';
$pass = ''; // change as needed
$charset = 'utf8mb4';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
