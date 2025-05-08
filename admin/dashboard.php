<?php
require_once '../includes/db.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !hasRole('admin')) {
    header('Location: ../loginform.php?error=You must be logged in as an admin to view this page');
    exit;
}

// Fetch some statistics for the dashboard
try {
    // Count total users
    $userStmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
    $userCount = $userStmt->fetch();
    
    // Count total clients
    $clientStmt = $pdo->query("SELECT COUNT(*) as total_clients FROM clients");
    $clientCount = $clientStmt->fetch();
    
    // Count total leads
    $leadStmt = $pdo->query("SELECT COUNT(*) as total_leads FROM leads");
    $leadCount = $leadStmt->fetch();
    
    // Get recent activity
    $activityStmt = $pdo->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10");
    $recentActivity = $activityStmt->fetchAll();
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CRM System</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .dashboard-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-box {
            width: 30%;
            padding: 20px;
            border-radius: 5px;
            background-color: #f5f5f5;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-box h3 {
            font-size: 2.5rem;
            margin: 10px 0;
            color: #4a6fdc;
        }
        .stat-box p {
            color: #666;
            margin: 0;
        }
        .activity-container {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .activity-list {
            list-style: none;
            padding: 0;
        }
        .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-time {
            color: #999;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <h1>CRM System</h1>
        </div>
        <ul class="nav-links">
            <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="../client/list.php"><i class="fas fa-building"></i> Clients</a></li>
            <li><a href="../user/change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    
    <!-- Page Content -->
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
        
        <?php if(isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="dashboard-stats">
            <div class="stat-box">
                <i class="fas fa-users fa-2x"></i>
                <h3><?php echo $userCount['total_users'] ?? 0; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-building fa-2x"></i>
                <h3><?php echo $clientCount['total_clients'] ?? 0; ?></h3>
                <p>Total Clients</p>
            </div>
            <div class="stat-box">
                <i class="fas fa-chart-line fa-2x"></i>
                <h3><?php echo $leadCount['total_leads'] ?? 0; ?></h3>
                <p>Total Leads</p>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="activity-container">
            <h2>Recent Activity</h2>
            <?php if(isset($recentActivity) && count($recentActivity) > 0): ?>
                <ul class="activity-list">
                    <?php foreach($recentActivity as $activity): ?>
                        <li class="activity-item">
                            <div><?php echo sanitize($activity['description']); ?></div>
                            <span class="activity-time"><?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No recent activity found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>