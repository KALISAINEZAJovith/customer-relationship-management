
<?php
session_start();
require_once '../includes/db.php';

// Check authentication
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../loginform.php");
//     exit;
// }

// Fetch clients from database
$sql = "SELECT * FROM clients ORDER BY name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client List</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/table.css">
</head>
<body>
    <div class="container">
        <h1>Clients</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type']; ?>">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>
        
        <a href="create.php" class="btn btn-primary">Add New Client</a>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Company</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['company']) ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this client?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="6" class="no-records">No clients found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

/* === client/create.php === */
<?php
session_start();
require_once '../includes/db.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginform.php");
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $company = $_POST['company'];
    
    $sql = "INSERT INTO clients (name, email, phone, company) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $phone, $company);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Client added successfully";
        $_SESSION['message_type'] = "success";
        header("Location: list.php");
        exit;
    } else {
        $error = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Client</title>
    <link rel="stylesheet" href="../style/navbar.css">
    <link rel="stylesheet" href="../style/form.css">
</head>
<body>
    <div class="container">
        <h1>Add New Client</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="post" action="" class="form">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" id="company" name="company" class="form-control">
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Save Client</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

/* === client/edit.php === */
<?php
session_start();
require_once '../includes/db.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginform.php");
    exit;
}

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
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $phone, $company, $id);
    
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

// Fetch client data
$sql = "SELECT * FROM clients WHERE id = ?";
$stmt = $conn->prepare($sql);
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

/* === client/delete.php === */
<?php
session_start();
require_once '../includes/db.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginform.php");
    exit;
}

// Check if id parameter exists
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id = $_GET['id'];

$sql = "DELETE FROM clients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Client deleted successfully";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting client: " . $stmt->error;
    $_SESSION['message_type'] = "danger";
}

$stmt->close();
header("Location: list.php");
exit;
?>
