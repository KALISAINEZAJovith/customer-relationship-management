<?php
/**
 * Authentication handler for CRM system
 * Processes login form submissions and establishes sessions
 */

// Start session
session_start();

// Include database connection
require_once('db.php');

// Function to sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and sanitize form data
    $email = sanitize($_POST['email']);
    $password = $_POST['password']; // Will be hashed later
    $role = sanitize($_POST['role']);
    
    // Validate inputs
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($role)) {
        $errors[] = "Role selection is required";
    }
    
    // If no validation errors, attempt login
    if (empty($errors)) {
        // Determine which table to check based on role
        $table = ($role == 'manager') ? 'managers' : 'employees';
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, email, password FROM $table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (assumes password is hashed in database)
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $role;
                
                // Redirect based on role
                if ($role == 'manager') {
                    header("Location: ManagerViews/ManagerView.php");
                } else {
                    header("Location: EmployeeViews/employeeView.php");
                }
                exit();
            } else {
                // Password is incorrect
                $errors[] = "Invalid email or password";
            }
        } else {
            // User not found
            $errors[] = "Invalid email or password";
        }
    }
    
    // If we have errors, store them in session and redirect back to login
    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        header("Location: loginForm.php");
        exit();
    }
} else {
    // If not POST request, redirect to login page
    header("Location: loginForm.php");
    exit();
}
?>