<?php
$host = 'localhost'; 
$db = 'crm_portal';      
$username = 'root'; 
$password = ''; 

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: list.php");
exit;
