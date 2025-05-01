<?php
require_once '../includes/db.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($newPass && $newPass === $confirm) {
        $hashed = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $id]);
        header("Location: ../dashboard.php");
        exit;
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!-- Show password + confirm fields only -->
