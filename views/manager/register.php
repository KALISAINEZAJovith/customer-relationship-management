
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <link rel="stylesheet" href="../../style/navbar.css">
</head>
<body>
    <div class="navbar">CRM - Register New User</div>
    <div class="container">
        <h2>Add New User</h2>
        <form action="../../actions/user/add.php" method="POST" class="form-box">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="employee">Sales People</option>
                <option value="manager">Sales Manager</option>
            </select>
            <button type="submit">Create User</button>
        </form>
    </div>
</body>
</html>
