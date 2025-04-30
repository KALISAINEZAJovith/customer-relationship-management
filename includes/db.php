<?php
/**
 * Database Connection File
 * Establishes connection to MySQL database for CRM system
 */

// Database configuration
$db_host = 'localhost';      // Database host
$db_name = 'crm_system';     // Database name
$db_user = 'crm_user';       // Database username
$db_pass = 'password123';    // Database password - should be stored securely in production

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Optional: Set timezone for database operations
date_default_timezone_set('UTC');

// Function to escape strings for database queries
function db_escape($conn, $string) {
    return $conn->real_escape_string($string);
}

// Function to execute query and return result
function db_query($conn, $query) {
    $result = $conn->query($query);
    if (!$result) {
        die("Query failed: " . $conn->error . "<br>Query: " . $query);
    }
    return $result;
}

// Connect once, use everywhere
// This file should be included at the beginning of any script that needs database access
?>