<?php
require_once '../includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) header("Location: list.php");

$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $company = trim($_POST['company']);

    if ($name && $email) {
        $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ?, phone = ?, company = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $company, $id]);
        $success = "Client updated.";
    } else {
        $error = "Name and email are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Client</title></head>
<body>
    <h2>Edit Client</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST">
        <label>Name:</label><input type="text" name="name" value="<?= htmlspecialchars($client['name']) ?>" required><br><br>
        <label>Email:</label><input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required><br><br>
        <label>Phone:</label><input type="text" name="phone" value="<?= htmlspecialchars($client['phone']) ?>"><br><br>
        <label>Company:</label><input type="text" name="company" value="<?= htmlspecialchars($client['company']) ?>"><br><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
