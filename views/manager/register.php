<?php
$conn = new mysqli("localhost", "root", "", "crm_portal");







 // Only manager can access

// Fetch users
$result = $conn->query("SELECT * FROM users");
?>

<h2>Create New User</h2>
<form method="POST" action="../../actions/user/create.php">
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <select name="role" required>
        <option value="employee">Employee</option>
        <option value="manager">Manager</option>
    </select><br>
    <button type="submit">Create User</button>
</form>

<hr>

<h2>All Users</h2>
<table border="1">
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= $row['role'] ?></td>
        <td>
            <a href="../../actions/user/delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
