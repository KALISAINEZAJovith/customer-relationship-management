<?php
require_once '../includes/db.php';

$stmt = $pdo->query("SELECT * FROM clients ORDER BY id DESC");
$clients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><title>Clients List</title></head>
<body>
    <h2>Client List</h2>
    <a href="create.php">Add New Client</a><br><br>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Actions</th>
        </tr>
        <?php foreach ($clients as $client): ?>
        <tr>
            <td><?= htmlspecialchars($client['name']) ?></td>
            <td><?= htmlspecialchars($client['email']) ?></td>
            <td><?= htmlspecialchars($client['phone']) ?></td>
            <td><?= htmlspecialchars($client['company']) ?></td>
            <td>
                <a href="edit.php?id=<?= $client['id'] ?>">Edit</a> |
                <a href="delete.php?id=<?= $client['id'] ?>" onclick="return confirm('Delete this client?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
