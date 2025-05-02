<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = intval($_POST['id']);
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = $_POST['role'];

    if (!$name || !$email || !$role) {
        die("All fields are required.");
    }

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);

    if ($stmt->execute()) {
        header("Location: ../../views/manager/register.php?update=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
