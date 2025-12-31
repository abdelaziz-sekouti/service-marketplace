<?php
// Simple admin login test/debug
require_once '../db_connect.php';

echo "<h2>Admin Login Debug</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Login Attempt:</h3>";
    echo "Username: " . htmlspecialchars($_POST['username']) . "<br>";
    echo "Password: " . htmlspecialchars($_POST['password']) . "<br>";
    
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    try {
        echo "<h3>Checking database...</h3>";
        $stmt = executeQuery("SELECT * FROM admin_users WHERE username = ?", [$username]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            echo "Found admin user:<br>";
            echo "ID: " . $admin['id'] . "<br>";
            echo "Username: " . $admin['username'] . "<br>";
            echo "Hash: " . $admin['password_hash'] . "<br>";
            
            echo "Testing password verification...<br>";
            $passwordMatch = password_verify($password, $admin['password_hash']);
            echo "Password matches: " . ($passwordMatch ? 'YES' : 'NO') . "<br>";
            
            if ($passwordMatch) {
                echo "<div style='color: green;'>✓ Login successful!</div>";
                
                session_start();
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                echo "Session set: admin_logged_in = true<br>";
                echo "Redirecting to dashboard...";
                
                // Redirect after 3 seconds
                header('refresh:3;url=dashboard.php');
                
            } else {
                echo "<div style='color: red;'>✗ Password does not match!</div>";
            }
        } else {
            echo "<div style='color: red;'>✗ Admin user not found!</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color: red;'>Database error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login Debug</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Admin Login Test</h1>
    
    <form method="POST">
        <h3>Test Admin Credentials:</h3>
        <p><strong>Username:</strong> admin</p>
        <p><strong>Password:</strong> admin123</p>
        <p><strong>Email:</strong> admin@freelancemoneymaker.com</p>
        
        <hr>
        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="admin" required><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" value="admin123" required><br>
        
        <button type="submit">Test Login</button>
    </form>
    
    <h3>All Admin Users:</h3>
    <?php
    try {
        $stmt = executeQuery("SELECT * FROM admin_users");
        $admins = $stmt->fetchAll();
        
        foreach ($admins as $admin) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
            echo "<strong>ID:</strong> " . $admin['id'] . "<br>";
            echo "<strong>Username:</strong> " . $admin['username'] . "<br>";
            echo "<strong>Email:</strong> " . $admin['email'] . "<br>";
            echo "<strong>Full Name:</strong> " . $admin['full_name'] . "<br>";
            echo "<strong>Role:</strong> " . $admin['role'] . "<br>";
            echo "<strong>Created:</strong> " . $admin['created_at'] . "<br>";
            echo "<strong>Last Login:</strong> " . ($admin['last_login'] ?: 'Never') . "<br>";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "Error loading admin users: " . $e->getMessage();
    }
    ?>
</body>
</html>