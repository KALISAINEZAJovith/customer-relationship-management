<?php
session_start();
require_once '../../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$email || !$password) {
        header("Location: ../../views/manager/loginForm.php?error=Missing credentials");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $name, $email, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;

            // Redirect based on role
            if ($role === 'manager') {
                header("Location: ../../views/manager/index.php");
            } else {
                header("Location: ../../views/employee/leads.php");
            }
            exit();
        }
    }

    header("Location: ../../views/manager/loginForm.php?error=Invalid email or password");
    exit();
} else {
    header("Location: ../../views/manager/loginForm.php");
    exit();
}
?>
