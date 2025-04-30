<?php
function isManager() {
    return $_SESSION['role'] === 'manager';
}
?>