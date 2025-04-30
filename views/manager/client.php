<?php
include '../../includes/auth.php';
include '../../includes/db.php';

$result = $conn->query("SELECT * FROM clients ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Clients</title>
    <link rel="stylesheet" href="../../style/navbar.css">
    <link rel="stylesheet" href="../../style/table.css">
</head>
<body>
    <div class="navbar">CRM - All Clients</div>
    <div class="container">
        <h2>Client List</h2>
        <form action="../../actions/client/new.php" method="POST" class="form-box">
            <input type="text" name="name" placeholder="Client Name" required>
            <input type="email" name="email" placeholder="Client Email" required>
            <button type="submit">Add Client</button>
        </form>

        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Created At</th></tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
