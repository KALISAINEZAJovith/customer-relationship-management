<?php
require_once '../../includes/db.php';

$id = $_POST['id'];
$name = $_POST['name'];
$email = $_POST['email'];
$role = $_POST['role'];

$stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
$stmt->bind_param("sssi", $name, $email, $role, $id);

if ($stmt->execute()) {
    echo "User updated successfully.";
} else {
    echo "Error: " . $stmt->error;
}
?>
