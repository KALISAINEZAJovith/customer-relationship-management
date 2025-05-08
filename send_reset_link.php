<?php
require_once 'includes/db.php';

// Process the password reset request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (empty($email)) {
        header("Location: forgot_password.php?error=Please enter your email address");
        exit;
    }
    
    try {
        // Check if email exists in database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($user = $stmt->fetch()) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $updateStmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id");
            $updateStmt->bindParam(':token', $token, PDO::PARAM_STR);
            $updateStmt->bindParam(':expires', $expires, PDO::PARAM_STR);
            $updateStmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $updateStmt->execute();
            
            // Create reset URL
            $resetUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            
            // Send email (in a real implementation, you would use a proper email library like PHPMailer)
            $to = $email;
            $subject = "Password Reset for CRM System";
            $message = "Click the link below to reset your password:\n\n" . $resetUrl . "\n\nThis link will expire in 1 hour.";
            $headers = "From: noreply@crmsystem.com";
            
            // For this example, we'll just simulate the email sending process
            // mail($to, $subject, $message, $headers);
            
            // Redirect with success message
            header("Location: forgot_password.php?success=Password reset link has been sent to your email");
            exit;
        } else {
            // Even if email doesn't exist, show success message for security reasons
            header("Location: forgot_password.php?success=If your email is registered, you will receive a password reset link");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: forgot_password.php?error=Database error, please try again");
        exit;
    } catch (Exception $e) {
        header("Location: forgot_password.php?error=An error occurred, please try again");
        exit;
    }
} else {
    // If not a POST request, redirect to forgot password page
    header("Location: forgot_password.php");
    exit;
}
?>