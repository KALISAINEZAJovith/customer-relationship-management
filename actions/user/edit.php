<?php
require_once '../../includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        echo json_encode($user); // Return JSON for frontend to populate form
    } else {
        echo json_encode(["error" => "User not found"]);
    }

    $stmt->close();
}
?>
