<?php
require_once '../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->execute([$name, $email, $role, $id]);
    header("Location: ../dashboard.php");
    exit;
}
?>

<!-- Show form prefilled with user data -->
