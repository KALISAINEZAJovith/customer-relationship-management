<?php
require_once '../includes/db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../dashboard.php");
exit;
