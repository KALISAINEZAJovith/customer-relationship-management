<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: views/" . ($_SESSION['role'] === 'manager' ? "manager" : "employee") . "/dashboard.php");
} else {
    header("Location: loginForm.php");
}
exit();
?>