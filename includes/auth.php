<?php
require_once '../includes/db.php';
session_start();

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isAuthenticated()) {
        header("Location: ../../loginForm.php");
        exit();
    }
}
?>
