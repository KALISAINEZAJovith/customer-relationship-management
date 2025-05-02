<?php
require_once '../../includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../../views/manager/register.php?deleted=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
