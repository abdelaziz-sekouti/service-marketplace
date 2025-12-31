<?php
session_start();
require_once '../db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    try {
        // Check if admin user exists (case-insensitive)
        $stmt = executeQuery("SELECT * FROM admin_users WHERE  username = ?", [ strtolower($username)]);
        $admin = $stmt->fetch();
      
        
        if ($admin && md5($password) == $admin['password_hash']) {
            // var_dump($admin);
            // var_dump(md5($password));
            // var_dump($admin['password_hash']);
            //  header('Location: dashboard.php');
            // echo '<h1>hello world</h1>';
            // die();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Update last login
            executeQuery("UPDATE admin_users SET last_login = NOW() WHERE id = ?", [$admin['id']]);
            
            header('Location: dashboard.php');
            // exit();
        } 
            
            
    } catch (Exception $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Freelance Money Maker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-cog text-purple-600 mr-2"></i>Admin Login
                </h2>
                <p class="text-gray-600">Access your admin dashboard</p>
            </div>

            <!-- Error Display -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <div class="bg-white shadow-lg rounded-lg p-8">
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="username" name="username" required
                                   class="pl-10 w-full focus:ring-purple-500 focus:border-purple-500 block w-full py-3 border border-gray-300 rounded-lg"
                                   placeholder="admin"
                                   value="admin">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required
                                   class="pl-10 w-full focus:ring-purple-500 focus:border-purple-500 block w-full py-3 border border-gray-300 rounded-lg"
                                   placeholder="••••••••••"
                                   value="admin123">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>
                    </div>
                </form>

              
            </div>
        </div>
    </div>
</body>
</html>