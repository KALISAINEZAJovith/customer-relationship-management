<?php
include '../../includes/db.php';
include '../../includes/auth.php';
$client_id = $_POST['client_id'];
$source = $_POST['source'];
$stmt = $conn->prepare("INSERT INTO leads (client_id, source) VALUES (?, ?)");
$stmt->bind_param("is", $client_id, $source);
$stmt->execute();
header("Location: ../../views/" . $_SESSION['role'] . "/dashboard.php");
?>