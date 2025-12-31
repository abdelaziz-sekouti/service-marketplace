<?php
require_once '../includes/cart_manager.php';
require_once '../includes/auth_manager.php';

$message = '';
$error = '';

// Handle cart operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_quantity':
            $result = $cartManager->updateQuantity($_POST['cart_id'], $_POST['quantity']);
            if (!$result['success']) {
                $error = $result['error'];
            }
            break;
            
        case 'remove':
            $result = $cartManager->removeFromCart($_POST['cart_id']);
            if (!$result['success']) {
                $error = $result['error'];
            } else {
                $message = $result['message'];
            }
            break;
            
        case 'clear_cart':
            $result = $cartManager->clearCart();
            if (!$result['success']) {
                $error = $result['error'];
            } else {
                $message = $result['message'];
            }
            break;
    }
}

// Get cart items
$cart = $cartManager->getCartItems();
$cartItems = $cart['items'];
$totalAmount = $cart['total_amount'];
$totalItems = $cart['total_items'];

// Calculate tax and shipping
$taxRate = 0.08; // 8% tax
$tax = $totalAmount * $taxRate;
$shipping = $totalAmount > 0 ? ($totalAmount > 100 ? 0 : 10) : 0;
$grandTotal = $totalAmount + $tax + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Freelance Money Maker</title>
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
                    <?php if ($authManager->isLoggedIn()): ?>
                        <?php $user = $authManager->getCurrentUser(); ?>
                        <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</span>
                        <a href="account.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-user mr-1"></i>Account
                        </a>
                        <a href="orders.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-shopping-bag mr-1"></i>Orders
                        </a>
                        <form method="POST" action="logout.php" class="inline">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                <i class="fas fa-sign-out-alt mr-1"></i>Logout
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php?redirect=cart.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                        </a>
                        <a href="register.php" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition">
                            <i class="fas fa-user-plus mr-1"></i>Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="../index.php" class="text-gray-600 hover:text-purple-600">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800">Shopping Cart</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
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
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-shopping-cart text-purple-600 mr-3"></i>
                            Shopping Cart 
                            <span class="ml-2 text-sm text-gray-500">(<?php echo $totalItems; ?> items)</span>
                        </h2>
                    </div>

                    <?php if (empty($cartItems)): ?>
                        <div class="p-12 text-center">
                            <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">Your cart is empty</h3>
                            <p class="text-gray-500 mb-6">Looks like you haven't added any items yet</p>
                            <a href="../index.php" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg inline-block transition">
                                <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="p-6">
                            <!-- Cart Items List -->
                            <div class="space-y-4">
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0 w-20 h-20">
                                            <img src="<?php echo $item['image'] ?: 'https://picsum.photos/seed/' . $item['listing_id'] . '/80/80.jpg'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                 class="w-full h-full object-cover rounded">
                                        </div>

                                        <!-- Product Details -->
                                        <div class="flex-grow">
                                            <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['title']); ?></h4>
                                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($item['category']); ?></p>
                                            <p class="text-lg font-bold text-purple-600">$<?php echo number_format($item['price_at_add'], 2); ?></p>
                                        </div>

                                        <!-- Quantity Control -->
                                        <div class="flex items-center space-x-2">
                                            <form method="POST" class="flex items-center space-x-2">
                                                <input type="hidden" name="action" value="update_quantity">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                <div class="flex items-center border rounded">
                                                    <button type="button" onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                                                            class="px-2 py-1 hover:bg-gray-200">-</button>
                                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                           min="1" max="99" 
                                                           class="w-12 text-center border-0 focus:outline-none"
                                                           onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                                                    <button type="button" onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)" 
                                                            class="px-2 py-1 hover:bg-gray-200">+</button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Item Total & Remove -->
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800">$<?php echo number_format($item['quantity'] * $item['price_at_add'], 2); ?></p>
                                            <form method="POST" class="mt-2">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                    <i class="fas fa-trash mr-1"></i>Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Cart Actions -->
                            <div class="mt-6 flex justify-between items-center pt-6 border-t">
                                <form method="POST">
                                    <input type="hidden" name="action" value="clear_cart">
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash mr-1"></i>Clear Cart
                                    </button>
                                </form>
                                <a href="../index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg transition">
                                    <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-receipt text-green-600 mr-2"></i>Order Summary
                    </h3>

                    <?php if (!empty($cartItems)): ?>
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal (<?php echo $totalItems; ?> items)</span>
                                <span class="font-semibold">$<?php echo number_format($totalAmount, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax (8%)</span>
                                <span class="font-semibold">$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-semibold">
                                    <?php echo $shipping > 0 ? '$' . number_format($shipping, 2) : 'FREE'; ?>
                                </span>
                            </div>
                            <?php if ($shipping > 0): ?>
                                <p class="text-xs text-green-600">Free shipping on orders over $100</p>
                            <?php endif; ?>
                            <div class="border-t pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-purple-600">$<?php echo number_format($grandTotal, 2); ?></span>
                                </div>
                            </div>
                        </div>

                        <a href="checkout.php" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-lg text-center block transition">
                            <i class="fas fa-credit-card mr-2"></i>Proceed to Checkout
                        </a>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-600">Add items to cart to see summary</p>
                        </div>
                    <?php endif; ?>

                    <!-- Promo Code -->
                    <div class="mt-6 pt-6 border-t">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Code</label>
                        <div class="flex">
                            <input type="text" placeholder="Enter code" 
                                   class="flex-1 px-3 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <button class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-r-lg transition">
                                Apply
                            </button>
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

    <script>
        function updateQuantity(cartId, quantity) {
            if (quantity < 1) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="update_quantity">
                <input type="hidden" name="cart_id" value="${cartId}">
                <input type="hidden" name="quantity" value="${quantity}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>