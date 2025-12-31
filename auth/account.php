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

$user = $authManager->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Freelance Money Maker</title>
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
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</span>
                    <a href="orders.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-shopping-bag mr-1"></i>Orders
                    </a>
                    <a href="cart.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-shopping-cart mr-1"></i>Cart
                    </a>
                    <form method="POST" class="inline">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="flex items-center mb-8">
                    <div class="bg-purple-100 p-4 rounded-full mr-4">
                        <i class="fas fa-user text-2xl text-purple-600"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">My Account</h1>
                        <p class="text-gray-600">Manage your profile and preferences</p>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>Account Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-gray-500">Full Name</label>
                                <p class="font-medium"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Username</label>
                                <p class="font-medium"><?php echo htmlspecialchars($user['username']); ?></p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Email</label>
                                <p class="font-medium"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Phone</label>
                                <p class="font-medium"><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Not provided'; ?></p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-500">Member Since</label>
                                <p class="font-medium"><?php echo date('F j, Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-cog mr-2 text-green-600"></i>Quick Actions
                        </h3>
                        <div class="space-y-3">
                            <a href="#" class="block w-full text-left bg-gray-50 hover:bg-gray-100 p-4 rounded-lg transition">
                                <i class="fas fa-edit text-blue-600 mr-3"></i>
                                <span class="font-medium">Edit Profile</span>
                            </a>
                            <a href="#" class="block w-full text-left bg-gray-50 hover:bg-gray-100 p-4 rounded-lg transition">
                                <i class="fas fa-lock text-green-600 mr-3"></i>
                                <span class="font-medium">Change Password</span>
                            </a>
                            <a href="orders.php" class="block w-full text-left bg-gray-50 hover:bg-gray-100 p-4 rounded-lg transition">
                                <i class="fas fa-shopping-bag text-purple-600 mr-3"></i>
                                <span class="font-medium">View Orders</span>
                            </a>
                            <a href="cart.php" class="block w-full text-left bg-gray-50 hover:bg-gray-100 p-4 rounded-lg transition">
                                <i class="fas fa-shopping-cart text-orange-600 mr-3"></i>
                                <span class="font-medium">Shopping Cart</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mt-8 pt-8 border-t">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-clock mr-2 text-purple-600"></i>Account Stats
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <i class="fas fa-shopping-bag text-2xl text-blue-600 mb-2"></i>
                            <div class="text-2xl font-bold text-blue-600">0</div>
                            <div class="text-sm text-gray-600">Total Orders</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <i class="fas fa-dollar-sign text-2xl text-green-600 mb-2"></i>
                            <div class="text-2xl font-bold text-green-600">$0</div>
                            <div class="text-sm text-gray-600">Total Spent</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg text-center">
                            <i class="fas fa-star text-2xl text-purple-600 mb-2"></i>
                            <div class="text-2xl font-bold text-purple-600">New</div>
                            <div class="text-sm text-gray-600">Member Status</div>
                        </div>
                    </div>
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
</body>
</html>