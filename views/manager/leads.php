<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

checkAuth();
checkRole('manager');

// Fetch clients and users
$clients = $conn->query("SELECT id, name FROM clients");
$users   = $conn->query("SELECT id, name FROM users WHERE role = 'employee'");
?>

<h2>Assign New Lead</h2>
<form method="POST" action="../../actions/lead/new.php">
    <label>Client:</label>
    <select name="client_id" required>
        <?php while ($c = $clients->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Assign To:</label>
    <select name="assigned_to" required>
        <?php while ($u = $users->fetch_assoc()): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <label>Status:</label>
    <input type="text" name="status" required><br>

    <label>Notes:</label>
    <textarea name="notes"></textarea><br>

    <button type="submit">Create Lead</button>
</form>
