<?php
require_once '../includes/db.php';

// Check if user is logged in and is a manager
if (!isLoggedIn() || !hasRole('manager')) {
    header('Location: ../loginform.php?error=You must be logged in as a manager to view this page');
    exit;
}

// Process any messages
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Handle lead assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_lead'])) {
    $lead_id = filter_input(INPUT_POST, 'lead_id', FILTER_VALIDATE_INT);
    $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
    
    if ($lead_id && $employee_id) {
        try {
            // Update lead assignment
            $stmt = $pdo->prepare("UPDATE leads SET assigned_to = :employee_id, updated_at = NOW() WHERE id = :lead_id");
            $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt->bindParam(':lead_id', $lead_id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Get employee name for the log
            $empStmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
            $empStmt->bindParam(':id', $employee_id, PDO::PARAM_INT);
            $empStmt->execute();
            $employee = $empStmt->fetch();
            
            // Get lead info for the log
            $leadStmt = $pdo->prepare("SELECT name FROM leads WHERE id = :id");
            $leadStmt->bindParam(':id', $lead_id, PDO::PARAM_INT);
            $leadStmt->execute();
            $lead = $leadStmt->fetch();
            
            // Log the activity
            $activity = "Manager {$_SESSION['name']} assigned lead '{$lead['name']}' to {$employee['name']}";
            $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, description, created_at) VALUES (:user_id, :description, NOW())");
            $logStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $logStmt->bindParam(':description', $activity, PDO::PARAM_STR);
            $logStmt->execute();
            
            $success = "Lead successfully assigned";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Invalid lead or employee selection";
    }
}

// Fetch all leads with client and employee information
try {
    $stmt = $pdo->prepare("
        SELECT l.*, c.name as client_name, u.name as assigned_to_name 
        FROM leads l
        LEFT JOIN clients c ON l.client_id = c.id
        LEFT JOIN users u ON l.assigned_to = u.id
        ORDER BY l.status, l.created_at DESC
    ");
    $stmt->execute();
    $leads = $stmt->fetchAll();
    
    // Fetch available employees for assignment
    $empStmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'employee' ORDER BY name");
    $empStmt->execute();
    $employees = $empStmt->fetchAll();
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
    <title>Manage Leads - CRM System</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
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
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover { color: black; }
        
        .lead-details {
            display: flex;
            margin-bottom: 20px;
        }
        .lead-info {
            flex: 1;
        }
        .lead-actions {
            flex: 1;
            padding-left: 20px;
            border-left: 1px solid #eee;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-assign {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-assign:hover {
            background-color: #2980b9;
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
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="active"><a href="leads.php"><i class="fas fa-chart-line"></i> Leads</a></li>
            <li><a href="../client/list.php"><i class="fas fa-building"></i> Clients</a></li>
            <li><a href="../user/change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    
    <!-- Page Content -->
    <div class="container">
        <h1>Manage Leads</h1>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="../client/list.php" class="btn"><i class="fas fa-building"></i> View Clients</a>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Lead Name</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th>Value</th>
                    <th>Assigned To</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($leads) && count($leads) > 0): ?>
                    <?php foreach($leads as $lead): ?>
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
                            <td class="actions">
                                <button class="btn view-btn" onclick="viewLead(<?php echo $lead['id']; ?>)" title="View/Assign"><i class="fas fa-user-plus"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-data">No leads found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Lead Assignment Modal -->
    <div id="leadModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Assign Lead</h2>
            
            <div id="lead-details-container"></div>
            
            <div class="lead-actions">
                <h3>Assign to Employee</h3>
                <form action="" method="post">
                    <input type="hidden" id="lead_id" name="lead_id" value="">
                    
                    <div class="form-group">
                        <label for="employee_id">Select Employee:</label>
                        <select id="employee_id" name="employee_id" required>
                            <option value="">-- Select Employee --</option>
                            <?php foreach($employees as $employee): ?>
                                <option value="<?php echo $employee['id']; ?>"><?php echo sanitize($employee['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="assign_lead" class="btn-assign">Assign Lead</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Get the modal
        const modal = document.getElementById("leadModal");
        
        // Function to display lead details and open modal
        function viewLead(leadId) {
            // In a real application, you would fetch lead details via AJAX
            // For this example, we'll just set the lead ID for the form
            document.getElementById("lead_id").value = leadId;
            
            // Find the lead in the table to display details
            const leadRow = document.querySelector(`tr td button[onclick*="${leadId}"]`).parentNode.parentNode;
            const leadName = leadRow.cells[0].textContent;
            const clientName = leadRow.cells[1].textContent;
            const status = leadRow.cells[2].textContent.trim();
            const value = leadRow.cells[3].textContent;
            const currentAssignee = leadRow.cells[4].textContent.trim();
            
            // Prepare HTML for lead details
            let detailsHtml = `
                <div class="lead-info">
                    <h3>Lead Information</h3>
                    <p><strong>Lead Name:</strong> ${leadName}</p>
                    <p><strong>Client:</strong> ${clientName}</p>
                    <p><strong>Status:</strong> ${status}</p>
                    <p><strong>Value:</strong> ${value}</p>
                    <p><strong>Currently Assigned To:</strong> ${currentAssignee === 'Not Assigned' ? 'Unassigned' : currentAssignee}</p>
                </div>
            `;
            
            // Update the container with lead details
            document.getElementById("lead-details-container").innerHTML = detailsHtml;
            
            // Open the modal
            modal.style.display = "block";
        }
        
        // Function to close the modal
        function closeModal() {
            modal.style.display = "none";
        }
        
        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>