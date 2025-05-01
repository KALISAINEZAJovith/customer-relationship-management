<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // You would usually email a secure token link here.
            // For demo, we reset password directly to "Password123"
            $newPassword = password_hash("Password123", PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->execute([$newPassword, $email]);

            $message = "Password has been reset to <strong>Password123</strong>. Please login and change it.";
        } else {
            $message = "No user found with that email.";
        }
    } else {
        $message = "Please enter your email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - CRM</title>
    <link rel="stylesheet" href="style/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #f9f9f9, #e3f2fd);
            padding: 0;
            margin: 0;
        }
        .container {
            width: 400px;
            margin: 60px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .message {
            margin-top: 20px;
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 6px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Your Password</h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Enter your email:</label>
        <input type="email" name="email" required placeholder="you@example.com">

        <button type="submit">Reset Password</button>
    </form>
</div>

</body>
</html>
