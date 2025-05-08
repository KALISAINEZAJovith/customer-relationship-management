<?php
require_once '../includes/db.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !hasRole('admin')) {
    header('Location: ../loginform.php?error=You must be logged in as an admin to perform this action');
    exit;
}

// Get user ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate user ID
if ($id <= 0) {
    header('Location: users.php?error=Invalid user ID');
    exit;
}

// Prevent deleting self
if ($id == $_SESSION['user_id']) {
    header('Location: users.php?error=You cannot delete your own account');
    exit;
}

try {
    // First, get the name of the user for logging purposes
    $nameStmt = $pdo->prepare("SELECT name FROM users WHERE id = :id");
    $nameStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $nameStmt->execute();
    $userName = $nameStmt->fetch();
    
    if (!$userName) {
        header('Location: users.php?error=User not found');
        exit;
    }
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Log the activity
    $activity = "User {$_SESSION['name']} deleted user: {$userName['name']}";
    $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, description, created_at) VALUES (:user_id, :description, NOW())");
    $logStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $logStmt->bindParam(':description', $activity, PDO::PARAM_STR);
    $logStmt->execute();
    
    // Commit transaction
    $pdo->commit();
    
    // Redirect to users page with success message
    header("Location: users.php?success=User deleted successfully");
    exit;
    
} catch (PDOException $e) {
    // Rollback transaction
    $pdo->rollBack();
    header("Location: users.php?error=Database error: " . $e->getMessage());
    exit;
}
?>