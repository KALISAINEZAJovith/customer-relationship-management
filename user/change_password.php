<?php
session_start();
require_once '../includes/db.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginform.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate current password
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect";
    } else if ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $success = "Password updated successfully";
        } else {
            $error = "Error updating password: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/form.css">
</head>
<body>
    <div class="container">
        <h1>Change Password</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="post" action="" class="form">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required class="form-control" 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                       title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
            </div>
            
            <div class="password-requirements">
                <p>Password must contain:</p>
                <ul>
                    <li>At least 8 characters</li>
                    <li>At least one uppercase letter</li>
                    <li>At least one lowercase letter</li>
                    <li>At least one number</li>
                </ul>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="../index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    
    <script>
        // Password confirmation validation
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        
        function validatePassword() {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        newPassword.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);
    </script>
</body>
</html>