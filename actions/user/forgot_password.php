<?php
$conn = new mysqli("localhost", "root", "", "crm_portal");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style/form.css">
</head>
<body>
    <div class="form-box">
        <h2>Forgot Password</h2>
        <form method="POST" action="send_reset_link.php">
            <label>Enter your email address:</label>
            <input type="email" name="email" required>
            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
