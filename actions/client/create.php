<?php
$conn = new mysqli("localhost", "root", "", "crm_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $created_by = trim($_POST['created_by']);
    $created_at = date('Y-m-d H:i:s');

    if ($name && $email) {
        $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, created_by, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $created_by, $created_at]);
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
    <style>
        body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f1f1f1;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.header-container {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

.form-box {
    background-color: white;
    padding: 30px 40px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

.form-box h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
}

.form-box p {
    text-align: center;
    font-size: 14px;
    margin-bottom: 15px;
}

.form-box label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    color: #444;
}

.form-box input[type="text"],
.form-box input[type="email"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.form-box button {
    width: 100%;
    background-color: #007bff;
    border: none;
    padding: 12px;
    font-size: 16px;
    color: white;
    border-radius: 4px;
    cursor: pointer;
}

.form-box button:hover {
    background-color: #0056b3;
}

    </style>
</head>
<body>
    <div class="form-box">
        <h2>Add Client</h2>
        <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone:</label>
            <input type="text" name="phone">

            <label>Created By:</label>
            <input type="text" name="created_by">

            <button type="submit">Create</button>
        </form>
    </div>
</body>

</html>
