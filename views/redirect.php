<?php
require_once '../includes/auth.php';
checkAuth();

if ($_SESSION['role'] === 'manager') {
    header("Location: manager/register.php");
} elseif ($_SESSION['role'] === 'employee') {
    header("Location: employee/leads.php");
} else {
    echo "Unknown role.";
}
exit;
