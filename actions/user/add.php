<?php
include '../../includes/db.php';
include '../../includes/auth.php';
if ($_SESSION['role'] !== 'manager') { die("Access denied."); }
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $role);
$stmt->execute();
header("Location: ../../views/manager/dashboard.php");
?>