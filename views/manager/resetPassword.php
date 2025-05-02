<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    die("Access denied: Please log in first.");
}
// Redirect to loginform.php after password change
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="../../style/change_password.css">
</head>
<body>
    <div class="form-box">
        <h2>Change Password</h2>
        <form method="POST" action="../../actions/user/change_password.php">
            <label>Current Password:</label>
            <input type="password" name="current_password" required>

            <label>New Password:</label>
            <input type="password" name="new_password" required>

            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>


