<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

checkAuth();
checkRole('manager');

// Fetch all clients
$result = $conn->query("SELECT * FROM clients");
?>

<h2>Add New Client</h2>
<form method="POST" action="../../actions/client/create.php">
    <input type="text" name="name" placeholder="Client Name" required><br>
    <input type="email" name="email" placeholder="Client Email" required><br>
    <input type="text" name="phone" placeholder="Phone Number" required><br>
    <input type="text" name="address" placeholder="Address" required><br>
    <button type="submit">Add Client</button>
</form>

<hr>

<h2>Client List</h2>
<table border="1">
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Actions</th></tr>
    <?php while ($client = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $client['id'] ?></td>
        <td><?= htmlspecialchars($client['name']) ?></td>
        <td><?= htmlspecialchars($client['email']) ?></td>
        <td><?= htmlspecialchars($client['phone']) ?></td>
        <td><?= htmlspecialchars($client['address']) ?></td>
        <td>
            <a href="../../actions/client/delete.php?id=<?= $client['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
