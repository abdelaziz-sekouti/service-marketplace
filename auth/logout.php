<?php
require_once '../includes/auth_manager.php';

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authManager->logout();
    header('Location: ../index.php');
    exit();
}

// If not logged in, redirect to login
if (!$authManager->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Redirect to account since logout is POST only
header('Location: account.php');
exit();
?>