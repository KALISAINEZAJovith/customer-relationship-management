<?php
include '../../includes/auth.php';
include '../../includes/db.php';

$user_id = $_SESSION['user_id'];
$query = "SELECT leads.id, clients.name AS client_name, leads.source 
          FROM leads JOIN clients ON leads.client_id = clients.id 
          WHERE clients.created_by = $user_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Leads</title>
    <link rel="stylesheet" href="../../style/navbar.css">
    <link rel="stylesheet" href="../../style/table.css">
</head>
<body>
    <div class="navbar">CRM - My Leads</div>
    <div class="container">
        <h2>Assigned Leads</h2>
        <form action="../../actions/lead/new.php" method="POST" class="form-box">
            <input type="number" name="client_id" placeholder="Client ID" required>
            <input type="text" name="source" placeholder="Source (e.g., email, call)" required>
            <button type="submit">Add Lead</button>
        </form>

        <table>
            <tr><th>ID</th><th>Client Name</th><th>Source</th></tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['client_name']) ?></td>
                    <td><?= htmlspecialchars($row['source']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
