<?php
session_start();
require_once '../includes/auth_manager.php';

$message = '';
$error = '';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    // Debug: Show what was received
    error_log("Registration POST data: " . print_r($_POST, true));
    
    $result = $authManager->register($_POST);
    
    if ($result['success']) {
        // Merge guest cart if exists
        if (isset($_SESSION['cart_session_id'])) {
            $authManager->mergeGuestCart($result['user_id']);
        }
        
        $_SESSION['registration_success'] = 'Registration successful! Please sign in.';
        header('Location: login.php');
        exit();
    } else {
        $error = $result['error'];
        error_log("Registration error: " . $error);
    }
}

// If already logged in, redirect to dashboard
if ($authManager->isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

// Check for success message from redirect
if (isset($_SESSION['registration_success'])) {
    $message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Freelance Money Maker</title>
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
                    <span class="text-gray-600">Already have an account?</span>
                    <a href="login.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-sign-in-alt mr-1"></i>Sign In
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
                    <i class="fas fa-user-plus text-4xl text-purple-600 mb-4"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Create Account</h1>
                    <p class="text-gray-600 mt-2">Join thousands of freelancers maximizing their income</p>
                </div>

                <!-- Messages -->
                <?php if ($message): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-check-circle mr-2"></i><?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                    </div>
                    <!-- Debug info -->
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                        <i class="fas fa-bug mr-2"></i>
                        <strong>Debug Info:</strong><br>
                        Please check your PHP error logs for more details.<br>
                        Common issues: database connection, table permissions, PHP version.
                    </div>
                <?php endif; ?>

                <form method="POST" id="registrationForm">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" name="first_name" required
                                       class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       placeholder="John">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" name="last_name" required
                                       class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       placeholder="Doe">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <div class="relative">
                            <i class="fas fa-at absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="username" required
                                   class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="johndoe">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
                            <input type="email" name="email" required
                                   class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="john@example.com">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone (Optional)</label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-3 top-3 text-gray-400"></i>
                            <input type="tel" name="phone"
                                   class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="+1 (555) 123-4567">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full pl-10 pr-10 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="••••••••••"
                                   id="password">
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye" id="password-toggle"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters</p>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" required class="mr-2">
                            <span class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-purple-600 hover:underline">Terms of Service</a> 
                                and <a href="#" class="text-purple-600 hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-lg transition duration-300">
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="login.php" class="text-purple-600 hover:underline font-medium">Sign In</a>
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

        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const username = document.querySelector('input[name="username"]').value;
            
            // Basic client-side validation
            if (username.length < 3) {
                e.preventDefault();
                alert('Username must be at least 3 characters long');
                return false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>