<?php
require_once 'includes/db.php';

// Check for error or success messages
$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM System - Forgot Password</title>
    <link rel="stylesheet" href="style/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Reset Password</h1>
            
            <?php if($error): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="success-message">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <p>Enter your email address to receive a password reset link.</p>
            
            <form action="send_reset_link.php" method="post">
                <div class="input-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <button type="submit" class="login-btn">Send Reset Link</button>
            </form>
            
            <div class="forgot-password">
                <a href="loginform.php">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>