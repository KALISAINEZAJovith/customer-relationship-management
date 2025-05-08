<?php
session_start();
require_once '../includes/db.php';

// Check authentication and authorization
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: ../loginform.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build the query with filters
$sql = "SELECT l.*, c.name as client_name, c.email as client_email, c.phone as client_phone
        FROM leads l
        LEFT JOIN clients c ON l.client_id = c.id
        WHERE l.assigned_to = ?";

if (!empty($status_filter)) {
    $sql .= " AND l.status = ?";
}

$sql .= " ORDER BY l.created_at DESC";

$stmt = $pdo->prepare($sql); // Replace $conn with $pdo

if (!empty($status_filter)) {
    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $status_filter, PDO::PARAM_STR);
} else {
    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
}

$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll for PDO
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leads</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/table.css">
    <style>
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: white;
        }
        
        .status-new {
            background-color: #3498db;
        }
        
        .status-active {
            background-color: #2ecc71;
        }
        
        .status-closed {
            background-color: #95a5a6;
        }
        
        .status-lost {
            background-color: #e74c3c;
        }
        
        .filters {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Leads</h1>
        
        <div class="filters">
            <form method="get" action="">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="new" <?= $status_filter == 'new' ? 'selected' : '' ?>>New</option>
                    <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="closed" <?= $status_filter == 'closed' ? 'selected' : '' ?>>Closed</option>
                    <option value="lost" <?= $status_filter == 'lost' ? 'selected' : '' ?>>Lost</option>
                </select>
            </form>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th>Value</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($leads->num_rows > 0): ?>
                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td><?= htmlspecialchars($lead['title']) ?></td>
                            <td>
                                <?= htmlspecialchars($lead['client_name']) ?><br>
                                <small><?= htmlspecialchars($lead['client_email']) ?></small><br>
                                <small><?= htmlspecialchars($lead['client_phone']) ?></small>
                            </td>
                            <td>
                                <span class="status-badge status-<?= strtolower($lead['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($lead['status'])) ?>
                                </span>
                            </td>
                            <td>$<?= number_format($lead['value'], 2) ?></td>
                            <td><?= date("M j, Y", strtotime($lead['created_at'])) ?></td>
                            <td class="actions">
                                <a href="view_lead.php?id=<?= $lead['id'] ?>" class="btn-view">View</a>
                                <a href="update_lead.php?id=<?= $lead['id'] ?>" class="btn-edit">Update</a>
                                <a href="add_note.php?lead_id=<?= $lead['id'] ?>" class="btn-edit">Add Note</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-records">No leads found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="controls">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>