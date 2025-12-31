<?php
require_once '../includes/cart_manager.php';
require_once '../includes/auth_manager.php';

// Redirect to cart if empty
$cart = $cartManager->getCartItems();
if (empty($cart['items'])) {
    header('Location: cart.php');
    exit();
}

$message = '';
$error = '';
$orderPlaced = false;

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $billingAddress = [
            'first_name' => sanitize($_POST['billing_first_name']),
            'last_name' => sanitize($_POST['billing_last_name']),
            'email' => sanitize($_POST['billing_email']),
            'phone' => sanitize($_POST['billing_phone']),
            'address' => sanitize($_POST['billing_address']),
            'city' => sanitize($_POST['billing_city']),
            'state' => sanitize($_POST['billing_state']),
            'postal_code' => sanitize($_POST['billing_postal_code']),
            'country' => sanitize($_POST['billing_country'])
        ];
        
        $shippingAddress = [
            'first_name' => sanitize($_POST['shipping_first_name']),
            'last_name' => sanitize($_POST['shipping_last_name']),
            'address' => sanitize($_POST['shipping_address']),
            'city' => sanitize($_POST['shipping_city']),
            'state' => sanitize($_POST['shipping_state']),
            'postal_code' => sanitize($_POST['shipping_postal_code']),
            'country' => sanitize($_POST['shipping_country'])
        ];
        
        // Calculate totals
        $subtotal = $cart['total_amount'];
        $tax = $subtotal * 0.08;
        $shipping = $subtotal > 100 ? 0 : 10;
        $total = $subtotal + $tax + $shipping;
        
        // Generate order number
        $orderNumber = 'FMM' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        
        // Create order
        global $pdo;
        $userId = $authManager->isLoggedIn() ? $authManager->getCurrentUser()['id'] : null;
        
        $stmt = $pdo->prepare("
            INSERT INTO orders (order_number, user_id, email, total_amount, status, payment_status, 
                               billing_address, shipping_address, notes) 
            VALUES (?, ?, ?, ?, 'pending', 'pending', ?, ?, ?)
        ");
        
        $stmt->execute([
            $orderNumber,
            $userId,
            $billingAddress['email'],
            $total,
            json_encode($billingAddress),
            json_encode($shippingAddress),
            sanitize($_POST['notes'] ?? '')
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Add order items
        foreach ($cart['items'] as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, listing_id, quantity, price, total, listing_data) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $listingData = [
                'title' => $item['title'],
                'description' => $item['description'],
                'category' => $item['category'],
                'image' => $item['image']
            ];
            
            $stmt->execute([
                $orderId,
                $item['listing_id'],
                $item['quantity'],
                $item['price_at_add'],
                $item['quantity'] * $item['price_at_add'],
                json_encode($listingData)
            ]);
        }
        
        // Clear cart
        $cartManager->clearCart();
        
        $orderPlaced = true;
        $message = "Order #{$orderNumber} placed successfully!";
        
    } catch (Exception $e) {
        $error = 'Failed to place order: ' . $e->getMessage();
    }
}

// Get user info for form pre-filling
$user = $authManager->isLoggedIn() ? $authManager->getCurrentUser() : null;

// Calculate totals
$subtotal = $cart['total_amount'];
$tax = $subtotal * 0.08;
$shipping = $subtotal > 100 ? 0 : 10;
$total = $subtotal + $tax + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Freelance Money Maker</title>
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
                        <form method="POST" action="logout.php" class="inline">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                <i class="fas fa-sign-out-alt mr-1"></i>Logout
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php?redirect=checkout.php" class="text-gray-600 hover:text-gray-800">
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
                <a href="cart.php" class="text-gray-600 hover:text-purple-600">Cart</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-800">Checkout</span>
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

        <?php if ($orderPlaced): ?>
            <!-- Order Success -->
            <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Order Placed Successfully!</h2>
                <p class="text-gray-600 mb-6">Thank you for your order. We'll send you a confirmation email shortly.</p>
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <p class="text-sm text-gray-600">Order Number</p>
                    <p class="text-xl font-bold text-purple-600"><?php echo $orderNumber; ?></p>
                </div>
                <div class="flex space-x-4 justify-center">
                    <a href="../index.php" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-home mr-2"></i>Continue Shopping
                    </a>
                    <a href="orders.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg transition">
                        <i class="fas fa-list mr-2"></i>View Orders
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    <form method="POST" id="checkoutForm">
                        <!-- Billing Address -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-credit-card text-blue-600 mr-2"></i>Billing Information
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                    <input type="text" name="billing_first_name" required
                                           value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                    <input type="text" name="billing_last_name" required
                                           value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input type="email" name="billing_email" required
                                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" name="billing_phone"
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                    <input type="text" name="billing_address" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                    <input type="text" name="billing_city" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                    <input type="text" name="billing_state" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                                    <input type="text" name="billing_postal_code" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                    <select name="billing_country" required
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="United States" selected>United States</option>
                                        <option value="Canada">Canada</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="Australia">Australia</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">
                                    <i class="fas fa-truck text-green-600 mr-2"></i>Shipping Information
                                </h3>
                                <label class="flex items-center">
                                    <input type="checkbox" id="sameAsBilling" checked class="mr-2">
                                    <span class="text-sm text-gray-600">Same as billing</span>
                                </label>
                            </div>
                            
                            <div id="shippingAddressFields" class="hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                        <input type="text" name="shipping_first_name" required
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                        <input type="text" name="shipping_last_name" required
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                        <input type="text" name="shipping_address" required
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                        <input type="text" name="shipping_city" required
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                        <input type="text" name="shipping_state" required
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                                        <input type="text" name="shipping_postal_code" required
                                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                        <select name="shipping_country" required
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                            <option value="United States" selected>United States</option>
                                            <option value="Canada">Canada</option>
                                            <option value="United Kingdom">United Kingdom</option>
                                            <option value="Australia">Australia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-credit-card text-purple-600 mr-2"></i>Payment Method
                            </h3>
                            
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="credit_card" checked class="mr-3">
                                    <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                                    <span>Credit Card</span>
                                </label>
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="paypal" class="mr-3">
                                    <i class="fab fa-paypal text-blue-600 mr-2"></i>
                                    <span>PayPal</span>
                                </label>
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="stripe" class="mr-3">
                                    <i class="fas fa-lock text-green-600 mr-2"></i>
                                    <span>Stripe (Secure)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold mb-4">
                                <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>Order Notes (Optional)
                            </h3>
                            <textarea name="notes" rows="4" placeholder="Special instructions for your order..."
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h3 class="text-lg font-semibold mb-4">
                            <i class="fas fa-receipt text-green-600 mr-2"></i>Order Summary
                        </h3>

                        <!-- Order Items -->
                        <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                            <?php foreach ($cart['items'] as $item): ?>
                                <div class="flex items-center space-x-3 pb-3 border-b">
                                    <img src="<?php echo $item['image'] ?: 'https://picsum.photos/seed/' . $item['listing_id'] . '/50/50.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                         class="w-12 h-12 object-cover rounded">
                                    <div class="flex-grow">
                                        <h4 class="text-sm font-medium text-gray-800 line-clamp-1"><?php echo htmlspecialchars($item['title']); ?></h4>
                                        <p class="text-xs text-gray-600">Qty: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['price_at_add'], 2); ?></p>
                                    </div>
                                    <div class="text-sm font-semibold">$<?php echo number_format($item['quantity'] * $item['price_at_add'], 2); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Totals -->
                        <div class="space-y-2 border-t pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax (8%)</span>
                                <span>$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping</span>
                                <span><?php echo $shipping > 0 ? '$' . number_format($shipping, 2) : 'FREE'; ?></span>
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-purple-600">$<?php echo number_format($total, 2); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <button type="submit" form="checkoutForm" 
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-lg mt-6 transition">
                            <i class="fas fa-lock mr-2"></i>Place Order
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-3">
                            <i class="fas fa-lock mr-1"></i>Secure checkout powered by SSL encryption
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 Freelance Money Maker. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Toggle shipping address fields
        document.getElementById('sameAsBilling').addEventListener('change', function() {
            const shippingFields = document.getElementById('shippingAddressFields');
            if (this.checked) {
                shippingFields.classList.add('hidden');
            } else {
                shippingFields.classList.remove('hidden');
            }
        });

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="billing_email"]').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
        });
    </script>
</body>
</html>