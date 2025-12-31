<?php
session_start();
require_once '../db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    try {
        $stmt = executeQuery("SELECT * FROM admin WHERE username = ?", [$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Update last login
            executeQuery("UPDATE admin SET last_login = NOW() WHERE id = ?", [$admin['id']]);
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    } catch (Exception $e) {
        $error = 'Login failed. Please try again.';
    }
}

include 'index.php';
?>