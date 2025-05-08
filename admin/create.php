<?php
require_once '../includes/db.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !hasRole('admin')) {
    header('Location: ../loginform.php?error=You must be logged in as an admin to view this page');
    exit;
}

$error = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($role) || empty($password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        try {
            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
            $checkStmt->execute();
            
            if ($checkStmt->fetch()) {
                $error = "Email already exists";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Create user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, NOW())");
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                $stmt->bindParam(':role', $role, PDO::PARAM_STR);
                $stmt->execute();
                
                // Log the activity
                $activity = "User {$_SESSION['name']} created a new user: {$name}";
                $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, description, created_at) VALUES (:user_id, :description, NOW())");
                $logStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $logStmt->bindParam(':description', $activity, PDO::PARAM_STR);
                $logStmt->execute();
                
                // Redirect to users page with success message
                header("Location: users.php?success=User created successfully");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - CRM System</title>
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
        <h1>Create New User</h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="manager" <?php echo (isset($_POST['role']) && $_POST['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                    <option value="employee" <?php echo (isset($_POST['role']) && $_POST['role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <small>Password must be at least 8 characters long.</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn">Create User</button>
                <a href="users.php" class="btn cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>