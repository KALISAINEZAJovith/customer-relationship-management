<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';

checkAuth();
checkRole('employee'); // Only employees can access

$user_id = $_SESSION['user_id'];

// Fetch leads assigned to this employee
$stmt = $conn->prepare("
    SELECT leads.id AS lead_id, clients.name AS client_name, clients.email, clients.phone, leads.status, leads.notes
    FROM leads
    JOIN clients ON leads.client_id = clients.id
    WHERE leads.assigned_to = ?
    ORDER BY leads.id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Assigned Leads</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <form method="POST" action="../../actions/lead/update.php">
        <input type="hidden" name="lead_id" value="<?= $row['lead_id'] ?>">
        <strong>Client:</strong> <?= htmlspecialchars($row['client_name']) ?> <br>
        <strong>Email:</strong> <?= htmlspecialchars($row['email']) ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?><br>

        <label>Status:</label>
        <input type="text" name="status" value="<?= htmlspecialchars
