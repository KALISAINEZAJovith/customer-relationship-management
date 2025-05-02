<?php
session_start();

// If user is already logged in, redirect based on role
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'manager':
            header("Location: manager/dashboard.php");
            break;
        case 'employee':
            header("Location: employee/dashboard.php");
            break;
        default:
            header("Location: dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRM Portal - Login</title>
    <link rel="stylesheet" href="style/form.css">
</head>
<body>
    <div class="form-box">
        <h2>Login</h2>
        <?php
        if (isset($_GET['error'])) {
            echo "<p style='color:red; text-align:center;'>" . htmlspecialchars($_GET['error']) . "</p>";
        }
        ?>
        <form method="POST" action="authenticate.php">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <div style="text-align:center; margin-top: 10px;">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>
