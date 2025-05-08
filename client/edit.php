<?php
session_start();
require_once '../includes/db.php';

// Check authentication
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../loginform.php");
//     exit;
// }

// Check if id parameter exists
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id = $_GET['id'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $company = $_POST['company'];
    
    $sql = "UPDATE clients SET name=?, email=?, phone=?, company=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $name, PDO::PARAM_STR);
    $stmt->bindValue(2, $email, PDO::PARAM_STR);
    $stmt->bindValue(3, $phone, PDO::PARAM_STR);
    $stmt->bindValue(4, $company, PDO::PARAM_STR);
    $stmt->bindValue(5, $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Client updated successfully";
        $_SESSION['message_type'] = "success";
        header("Location: list.php");
        exit;
    } else {
        $error = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
$stmt->bindValue(1, $id, PDO::PARAM_INT);
// Fetch client data
$sql = "SELECT * FROM clients WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: list.php");
    exit;
}

$client = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/form.css">
</head>
<body>
    <div class="container">
        <h1>Edit Client</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post" action="" class="form">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($client['name']) ?>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($client['phone']) ?>" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" id="company" name="company" value="<?= htmlspecialchars($client['company']) ?>" class="form-control">
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update Client</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>