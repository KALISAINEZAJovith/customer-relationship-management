<?php
require_once '../includes/db.php';

// Check if user is logged in and is a manager
if (!isLoggedIn() || !hasRole('manager')) {
    header('Location: ../loginform.php?error=You must be logged in as a manager to view this page');
    exit;
}

// Fetch some statistics for the dashboard
try {
    // Count total clients
    $clientStmt = $pdo->query("SELECT COUNT(*) as total_clients FROM clients");
    $clientCount = $clientStmt->fetch();
    
    // Count total leads
    $leadStmt = $pdo->query("SELECT COUNT(*) as total_leads FROM leads");
    $leadCount = $leadStmt->fetch();
    
    // Count unassigned leads
    $unassignedStmt = $pdo->query("SELECT COUNT(*) as unassigned_leads FROM leads WHERE assigned_to IS NULL");
    $unassignedCount = $unassignedStmt->fetch();
    
    // Get leads by status
    $statusStmt = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM leads 
        GROUP BY status 
        ORDER BY FIELD(status, 'new', 'contacted', 'qualified', 'proposal', 'won', 'lost')
    ");
    $leadsByStatus = $statusStmt->fetchAll();
    
    // Get recent leads
    $recentLeadsStmt = $pdo->query("
        SELECT l.*, c.name as client_name, u.name as assigned_to_name 
        FROM leads l
        LEFT JOIN clients c ON l.client_id = c.id
        LEFT JOIN users u ON l.assigned_to = u.id
        ORDER BY l.created_at DESC 
        LIMIT 5
    ");
    $recentLeads = $recentLeadsStmt->fetchAll();
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'new':
            return 'badge-new';
        case 'contacted':
            return 'badge-contacted';
        case 'qualified':
            return 'badge-qualified';
        case 'proposal':
            return 'badge-proposal';
        case 'won':
            return 'badge-won';
        case 'lost':
            return 'badge-lost';
        default:
            return 'badge-default';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - CRM System</title>
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
        .panel {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .panel h2 {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .status-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .status-item {
            flex: 1;
            min-width: 120px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .status-count {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 5px 0;
        }
        .status-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            color: white;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }
        .badge-new { background-color: #3498db; }
        .badge-contacted { background-color: #9b59b6; }
        .badge-qualified { background-color: #2ecc71; }
        .badge-proposal { background-color: #f39c12; }
        .badge-won { background-color: #27ae60; }
        .badge-lost { background-color: #e74c3c; }
        .badge-default { background-color: #95a5a6; }
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
            <li><a href="leads.php"><i class="fas fa-chart-line"></i> Leads</a></li>
            <li><a href="../client/list.php"><i class="fas fa-building"></i> Clients</a></li>
            <li><a href="../user/change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    
    <!-- Page Content -->
    <div class="container">
        <h1>Manager Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
        
        <?php if(isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="dashboard-stats">
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
            <div class="stat-box">
                <i class="fas fa-user-plus fa-2x"></i>
                <h3><?php echo $unassignedCount['unassigned_leads'] ?? 0; ?></h3>
                <p>Unassigned Leads</p>
            </div>
        </div>
        
        <!-- Lead Status Summary -->
        <div class="panel">
            <h2>Leads by Status</h2>
            <div class="status-summary">
                <?php if(isset($leadsByStatus) && count($leadsByStatus) > 0): ?>
                    <?php foreach($leadsByStatus as $status): ?>
                        <div class="status-item <?php echo getStatusBadgeClass($status['status']); ?>">
                            <div class="status-count"><?php echo $status['count']; ?></div>
                            <div class="status-label"><?php echo ucfirst($status['status']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No lead status data available.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Leads -->
        <div class="panel">
            <h2>Recent Leads</h2>
            <?php if(isset($recentLeads) && count($recentLeads) > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Lead Name</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Value</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentLeads as $lead): ?>
                            <tr>
                                <td><?php echo sanitize($lead['name']); ?></td>
                                <td><?php echo sanitize($lead['client_name']); ?></td>
                                <td>
                                    <span class="badge <?php echo getStatusBadgeClass($lead['status']); ?>">
                                        <?php echo sanitize($lead['status']); ?>
                                    </span>
                                </td>
                                <td>$<?php echo number_format($lead['value'], 2); ?></td>
                                <td>
                                    <?php if($lead['assigned_to']): ?>
                                        <?php echo sanitize($lead['assigned_to_name']); ?>
                                    <?php else: ?>
                                        <span class="not-assigned">Not Assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($lead['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="view-all">
                    <a href="leads.php" class="btn">View All Leads</a>
                </div>
            <?php else: ?>
                <p>No recent leads found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>