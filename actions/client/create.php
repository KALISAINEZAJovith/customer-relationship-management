<?php
require_once '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $company = trim($_POST['company']);

    if ($name && $email) {
        $stmt = $pdo->prepare("INSERT INTO clients (name, email, phone, company) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $company]);
        $success = "Client added successfully.";
    } else {
        $error = "Name and email are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Client</title>
</head>
<body>
    <h2>Add Client</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST">
        <label>Name:</label><input type="text" name="name" required><br><br>
        <label>Email:</label><input type="email" name="email" required><br><br>
        <label>Phone:</label><input type="text" name="phone"><br><br>
        <label>Company:</label><input type="text" name="company"><br><br>
        <button type="submit">Create</button>
    </form>
</body>
</html>
