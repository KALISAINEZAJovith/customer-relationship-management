<?php
session_start();
require_once '../includes/db.php';

// Check authentication and authorization
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employee') {
    header("Location: ../loginform.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch assigned leads count
$active_leads_count = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM leads WHERE assigned_to = ? AND status NOT IN ('closed', 'lost')");
    $stmt->execute([$user_id]);
    $active_leads_count = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching active leads: " . $e->getMessage());
}

// Fetch recent activities
$activities = [];
try {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as client_name 
        FROM activities a 
        JOIN clients c ON a.client_id = c.id 
        WHERE a.user_id = ? 
        ORDER BY a.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $activities = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching activities: " . $e->getMessage());
}

// Fetch upcoming tasks
$tasks = [];
try {
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as client_name 
        FROM tasks t 
        LEFT JOIN clients c ON t.client_id = c.id 
        WHERE t.assigned_to = ? AND t.status = 'pending' 
        ORDER BY t.due_date ASC 
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $tasks = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching tasks: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/table.css">
    <style>
        .dashboard-overview {
            display: flex;
            margin-bottom: 30px;
            gap: 20px;
        }
        
        .overview-card {
            flex: 1;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .overview-card h3 {
            margin-top: 0;
            color: #555;
        }
        
        .overview-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #3498db;
            margin: 10px 0;
        }
        
        .section {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .section h2 {
            margin-top: 0;
            color: #333;
            margin-bottom: 15px;
        }
        
        .task-item, .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .task-item:last-child, .activity-item:last-child {
            border-bottom: none;
        }
        
        .task-item .due-date {
            color: #e74c3c;
            font-size: 13px;
        }
        
        .activity-item .time {
            color: #777;
            font-size: 12px;
        }
        
        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Employee Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</p>
        
        <div class="dashboard-overview">
            <div class="overview-card">
                <h3>Active Leads</h3>
                <div class="number"><?= $active_leads_count ?></div>
                <a href="leads.php" class="btn btn-primary">View All Leads</a>
            </div>
        </div>
        
        <div class="section">
            <h2>Upcoming Tasks</h2>
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task-item">
                        <div>
                            <strong><?= htmlspecialchars($task['title']) ?></strong>
                            <?php if ($task['client_id']): ?>
                                - Client: <?= htmlspecialchars($task['client_name']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="due-date">Due: <?= date("M j, Y", strtotime($task['due_date'])) ?></div>
                        <div class="actions">
                            <a href="complete_task.php?id=<?= $task['id'] ?>" class="btn-view">Mark Complete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No pending tasks.</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h2>Recent Activities</h2>
            <?php if (!empty($activities)): ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-item">
                        <div><?= htmlspecialchars($activity['action']) ?> for client <strong><?= htmlspecialchars($activity['client_name']) ?></strong></div>
                        <div class="time"><?= date("M j, Y, g:i a", strtotime($activity['created_at'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No recent activities found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>