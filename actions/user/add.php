<?php
require_once '../../includes/db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {
    echo "User added successfully.";
} else {
    echo "Error: " . $stmt->error;
}
?>
