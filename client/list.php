<?php
session_start();
require_once '../includes/db.php';

// Check authentication
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../loginform.php");
//     exit;
// }

// Fetch clients from database
$sql = "SELECT * FROM clients ORDER BY name";
$result = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client List</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/table.css">
</head>
<body>
    <div class="container">
        <h1>Clients</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type']; ?>">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>
        
        <a href="create.php" class="btn btn-primary">Add New Client</a>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>created_at</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this client?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if ($result->rowCount() === 0): ?>
                <tr>
                    <td colspan="6" class="no-records">No clients found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>