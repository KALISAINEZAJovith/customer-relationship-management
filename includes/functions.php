<?php
/**
 * Common functions for CRM system
 * Contains utility functions used across the application
 */

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user has specific role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['message'] = "Please log in to access this page.";
        $_SESSION['message_type'] = "error";
        header("Location: " . getBaseUrl() . "loginForm.php");
        exit();
    }
}

// Redirect if not the right role
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        $_SESSION['message'] = "You don't have permission to access this page.";
        $_SESSION['message_type'] = "error";
        
        // Redirect to appropriate dashboard based on role
        if ($_SESSION['role'] === 'manager') {
            header("Location: " . getBaseUrl() . "ManagerViews/ManagerView.php");
        } else {
            header("Location: " . getBaseUrl() . "EmployeeViews/employeeView.php");
        }
        exit();
    }
}

// Get base URL for the application
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    
    // Make sure the directory ends with a slash
    if (substr($scriptDir, -1) !== '/') {
        $scriptDir .= '/';
    }
    
    return $protocol . "://" . $host . $scriptDir;
}

// Format date for display
function formatDate($date, $format = 'M d, Y') {
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

// Format currency
function formatCurrency($amount, $currency = 'USD') {
    $locale = 'en_US';
    $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    return $fmt->formatCurrency($amount, $currency);
}

// Generate random token (for CSRF protection, etc.)
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Clean data for display (prevent XSS)
function cleanOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Get pagination parameters
function getPagination($total, $perPage = 10, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'per_page' => $perPage,
        'offset' => $offset
    ];
}

// Display flash messages
function displayMessages() {
    if (isset($_SESSION['message']) && isset($_SESSION['message_type'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'];
        
        // Clear the message from session
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        
        return "<div class=\"alert alert-{$type}\">{$message}</div>";
    }
    
    return '';
}

// Log activity
function logActivity($conn, $userId, $action, $details = '') {
    $query = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("isss", $userId, $action, $details, $ipAddress);
    $stmt->execute();
}

// Get user's full name
function getUserName($conn, $userId, $role) {
    $table = ($role === 'manager') ? 'managers' : 'employees';
    $query = "SELECT name FROM {$table} WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['name'];
    }
    
    return 'Unknown User';
}
?>