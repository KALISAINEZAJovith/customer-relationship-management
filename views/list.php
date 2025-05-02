<?php
session_start();
require_once '../includes/db.php';
include '../includes/header.php';
?>

<link rel="stylesheet" href="../style/table.css">

<div class="container">
    <h2>Client List</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM clients ORDER BY created_at DESC");
            $count = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$count}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['address']}</td>
                      </tr>";
                $count++;
            }
            ?>
        </tbody>
    </table>
</div>
