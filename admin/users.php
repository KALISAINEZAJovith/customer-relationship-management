<?php
require_once '../includes/db.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !hasRole('admin')) {
    header('Location: ../loginform.php?error=You must be logged in as an admin to view this page');
    exit;
}

// Process any messages
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Fetch all users
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY role, name");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - CRM System</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <h1>CRM System</h1>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="active"><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="../client/list.php"><i class="fas fa-building"></i> Clients</a></li>
            <li><a href="../user/change_password.php"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    
    <!-- Page Content -->
    <div class="container">
        <h1>Manage Users</h1>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="create.php" class="btn create-btn"><i class="fas fa-plus"></i> Add New User</a>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($users) && count($users) > 0): ?>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo sanitize($user['name']); ?></td>
                            <td><?php echo sanitize($user['email']); ?></td>
                            <td><?php echo ucfirst(sanitize($user['role'])); ?></td>
                            <td><?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn edit-btn" title="Edit"><i class="fas fa-edit"></i></a>
                                <?php if($user['id'] != $_SESSION['user_id']): // Prevent deleting themselves ?>
                                    <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-data">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>