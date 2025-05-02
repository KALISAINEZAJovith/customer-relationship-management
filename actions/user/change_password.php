<?php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

checkAuth(); // Ensure user is logged in

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        die("All fields are required.");
    }

    if ($newPassword !== $confirmPassword) {
        die("New passwords do not match.");
    }

    $userId = $_SESSION['user_id'];

    // Fetch current password hash from DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($storedHash);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (!password_verify($currentPassword, $storedHash)) {
        die("Current password is incorrect.");
    }

    // Update to new password
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->bind_param("si", $newHash, $userId);

    if ($updateStmt->execute()) {
        echo "Password changed successfully.";
    } else {
        echo "Error updating password.";
    }

    $updateStmt->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: ../../views/loginform.php");
    exit();
}
?>
