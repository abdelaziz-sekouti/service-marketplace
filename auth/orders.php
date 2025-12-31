<?php
require_once '../includes/auth_manager.php';

// If not logged in, redirect to login
if (!$authManager->isLoggedIn()) {
    header('Location: login.php?redirect=orders.php');
    exit();
}

$user = $authManager->getCurrentUser();

// Get user orders
global $pdo;
$stmt = $pdo->prepare("
    SELECT o.*, 
           COUNT(oi.id) as item_count,
           oi.listing_data,
           oi.quantity,
           oi.price,
           oi.total as item_total
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    WHERE o.user_id = ? OR o.email = ?
    GROUP BY o.id 
    ORDER BY o.created_at DESC
");
$stmt->execute([$user['id'], $user['email']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Freelance Money Maker</title>
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
                    <a href="account.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-user mr-1"></i>Account
                    </a>
                    <a href="cart.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-shopping-cart mr-1"></i>Cart
                    </a>
                    <form method="POST" action="logout.php" class="inline">
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
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center">
                        <i class="fas fa-shopping-bag text-3xl text-purple-600 mr-4"></i>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">My Orders</h1>
                            <p class="text-gray-600">Track and manage your orders</p>
                        </div>
                    </div>
                    <a href="account.php" class="text-purple-600 hover:text-purple-800">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Account
                    </a>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No orders yet</h3>
                        <p class="text-gray-500 mb-6">You haven't placed any orders yet</p>
                        <a href="../index.php" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg inline-block transition">
                            <i class="fas fa-shopping-cart mr-2"></i>Start Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($orders as $order): ?>
                            <div class="border rounded-lg p-6 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold">Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                                        <p class="text-sm text-gray-600">Placed on <?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-purple-600">$<?php echo number_format($order['total_amount'], 2); ?></p>
                                        <span class="inline-block px-3 py-1 text-xs rounded-full 
                                            <?php 
                                            $statusClass = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'refunded' => 'bg-gray-100 text-gray-800'
                                            ];
                                            echo $statusClass[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2"><?php echo $order['item_count']; ?> items</p>
                                    <?php 
                                    // Get items for this order
                                    $itemStmt = $pdo->prepare("
                                        SELECT oi.*, l.title, l.image 
                                        FROM order_items oi 
                                        LEFT JOIN listings l ON oi.listing_id = l.id 
                                        WHERE oi.order_id = ?
                                    ");
                                    $itemStmt->execute([$order['id']]);
                                    $items = $itemStmt->fetchAll();
                                    ?>
                                    <div class="space-y-2">
                                        <?php foreach ($items as $item): ?>
                                            <div class="flex items-center space-x-3 text-sm">
                                                <img src="<?php echo $item['image'] ?: 'https://picsum.photos/seed/' . $item['listing_id'] . '/40/40.jpg'; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                     class="w-10 h-10 object-cover rounded">
                                                <div class="flex-grow">
                                                    <p class="font-medium"><?php echo htmlspecialchars($item['title']); ?></p>
                                                    <p class="text-gray-600">Qty: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['price'], 2); ?></p>
                                                </div>
                                                <div class="font-semibold">$<?php echo number_format($item['item_total'], 2); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Order Actions -->
                                <div class="flex justify-between items-center pt-4 border-t">
                                    <div class="flex space-x-4">
                                        <button class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                            <i class="fas fa-eye mr-1"></i>View Details
                                        </button>
                                        <?php if ($order['status'] === 'completed'): ?>
                                            <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                <i class="fas fa-download mr-1"></i>Download
                                            </button>
                                        <?php endif; ?>
                                        <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
                                            <button class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                <i class="fas fa-times-circle mr-1"></i>Cancel Order
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <i class="fas fa-redo mr-1"></i>Reorder
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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