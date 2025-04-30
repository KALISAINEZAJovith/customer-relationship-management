<?php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

$email = sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (empty($email) || empty($password) || empty($role)) {
    header("Location: ../../loginForm.php?error=1");
    exit();
}

$query = "SELECT * FROM users WHERE email = ? AND role = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $email, $role);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    if ($role === 'admin' || $role === 'manager') {
        header("Location: ../../views/manager/client.php");
    } else {
        header("Location: ../../views/employee/leads.php");
    }
    exit();
} else {
    header("Location: ../../loginForm.php?error=invalid");
    exit();
}
?>
