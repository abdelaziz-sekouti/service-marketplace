<?php
require_once '../includes/auth_manager.php';

$message = '';
$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authManager->login($_POST['email'], $_POST['password']);
    if ($result['success']) {
        $redirectUrl = $_POST['redirect'] ?? '../index.php';
        header('Location: ' . $redirectUrl);
        exit();
    } else {
        $error = $result['error'];
    }
}

// Check for registration success
$registered = isset($_GET['registered']) && $_GET['registered'] === 'true';

// If already logged in, redirect to dashboard
if ($authManager->isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Freelance Money Maker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="text-2xl font-bold text-purple-600">
                    <i class="fas fa-coins mr-2"></i>Freelance Money Maker
                </a>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Don't have an account?</span>
                    <a href="register.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-user-plus mr-1"></i>Sign Up
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-sign-in-alt text-4xl text-purple-600 mb-4"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Welcome Back</h1>
                    <p class="text-gray-600 mt-2">Sign in to access your account</p>
                </div>

                <!-- Messages -->
                <?php if ($registered): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-check-circle mr-2"></i>Registration successful! Please sign in.
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? '../index.php'); ?>">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
                            <input type="email" name="email" required
                                   class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
                            <input type="password" name="password" required
                                   class="w-full pl-10 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="••••••••"
                                   id="password">
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password-toggle"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">
                                <input type="checkbox" class="mr-2"> Remember me
                            </span>
                            <a href="#" class="text-sm text-purple-600 hover:underline">Forgot password?</a>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-lg transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="register.php" class="text-purple-600 hover:underline font-medium">Sign Up</a>
                    </p>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        By signing in, you agree to our 
                        <a href="#" class="text-purple-600 hover:underline">Terms of Service</a> 
                        and <a href="#" class="text-purple-600 hover:underline">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 Freelance Money Maker. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>