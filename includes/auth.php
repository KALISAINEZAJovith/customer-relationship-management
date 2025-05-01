<?php
session_start();

function checkAuth($role = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../views/manager/loginForm.php");
        exit();
    }

    if ($role && $_SESSION['user_role'] !== $role) {
        echo "Access Denied";
        exit();
    }
}
?>
