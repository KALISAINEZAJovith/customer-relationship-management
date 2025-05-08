<?php
session_start();
require_once '../includes/db.php';

// Check authentication
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../loginform.php");
//     exit;
// }

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $created_at = $_POST['created_at'];
    
    $sql = "INSERT INTO clients (name, email, phone, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    // Bind each parameter individually
    $stmt->bindParam(1, $name, PDO::PARAM_STR);
    $stmt->bindParam(2, $email, PDO::PARAM_STR);
    $stmt->bindParam(3, $phone, PDO::PARAM_STR);
    $stmt->bindParam(4, $created_at, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Client added successfully";
        $_SESSION['message_type'] = "success";
        header("Location: list.php");
        exit;
    } else {
        $errorInfo = $stmt->errorInfo();
        $error = "Error: " . $errorInfo[2];
    }
    
    // No need to explicitly close the statement
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Client</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/form.css">
</head>
<body>
    <div class="container">
        <h1>Add New Client</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post" action="" class="form">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="created_at">created_at</label>
                <input type="text" id="created_at" name="created_at" class="form-control">
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Save Client</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>