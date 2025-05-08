<?php
require_once '../includes/db.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !hasRole('admin')) {
    header('Location: ../loginform.php?error=You must be logged in as an admin to view this page');
    exit;
}

$error = '';
$success = '';
$user = null;

// Get user ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate user ID
if ($id <= 0) {
    header('Location: users.php?error=Invalid user ID');
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($role)) {
        $error = "Name, email, and role are required";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (!empty($password) && strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        try {
            // Check if email already exists for other users
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            
            if ($checkStmt->fetch()) {
                $error = "Email already exists for another user";
            } else {
                // Update user
                if (!empty($password)) {
                    // Update with new password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, role = :role, password = :password, updated_at = NOW() WHERE id = :id");
                    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                } else {
                    // Update without changing password
                    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, role = :role, updated_at = NOW() WHERE id = :id");
                }
                
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':role', $role, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                
                // Log the activity
                $activity = "User {$_SESSION['name']} updated user: {$name}";
                $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, description, created_at) VALUES (:user_id, :description, NOW())");
                $logStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $logStmt->bindParam(':description', $activity, PDO::PARAM_STR);
                $logStmt->execute();
                
                $success = "User updated successfully";
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
} else {
    // Fetch user data
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if (!$user) {
            header('Location: users.php?error=User not found');
            exit;
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - CRM System</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/form.css">
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
        <h1>Edit User</h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($user): ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="post">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="manager" <?php echo ($user['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                        <option value="employee" <?php echo ($user['role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                    <small>Leave blank to keep current password. New password must be at least 8 characters long.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn">Update User</button>
                    <a href="users.php" class="btn cancel-btn">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>