<?php
require_once 'includes/db.php';

// Process the login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate the form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Do not sanitize password before verification
    
    if (empty($email) || empty($password)) {
        header("Location: loginform.php?error=Please fill all fields");
        exit;
    }
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($user = $stmt->fetch()) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Update last login time
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
                $updateStmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                $updateStmt->execute();
                
                // Redirect based on role
                redirectToUserDashboard();
            } else {
                header("Location: loginform.php?error=Invalid email or password");
                exit;
            }
        } else {
            header("Location: loginform.php?error=Invalid email or password");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: loginform.php?error=Database error, please try again");
        exit;
    }
} else {
    // If not a POST request, redirect to login page
    header("Location: loginform.php");
    exit;
}
?>