<?php
require_once '../../includes/auth.php';
checkAuth(); // Ensure the user is logged in
?>

<h2>Change Password</h2>
<form method="POST" action="../../actions/user/update_password.php">
    <label>Current Password:</label>
    <input type="password" name="current_password" required><br>

    <label>New Password:</label>
    <input type="password" name="new_password" required><br>

    <label>Confirm New Password:</label>
    <input type="password" name="confirm_password" required><br>

    <button type="submit">Update Password</button>
</form>
