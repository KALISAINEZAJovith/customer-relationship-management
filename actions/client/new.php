<?php
include '../../includes/db.php';
include '../../includes/auth.php';
$name = $_POST['name'];
$email = $_POST['email'];
$stmt = $conn->prepare("INSERT INTO clients (name, email, created_by) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $name, $email, $_SESSION['user_id']);
$stmt->execute();
header("Location: ../../views/" . $_SESSION['role'] . "/dashboard.php");
?>