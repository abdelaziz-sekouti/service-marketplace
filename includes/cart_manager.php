<?php
require_once 'auth_manager.php';
require_once '../db_connect.php';

class CartManager {
    private $pdo;
    private $authManager;
    
    public function __construct() {
        global $pdo, $authManager;
        $this->pdo = $pdo;
        $this->authManager = $authManager;
    }
    
    // Get or create session ID for guest users
    private function getOrCreateSessionId() {
        session_start();
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = uniqid('cart_', true);
        }
        return $_SESSION['cart_session_id'];
    }
    
    // Add item to cart
    public function addToCart($listingId, $quantity = 1) {
        try {
            // Get listing details
            $stmt = $this->pdo->prepare("SELECT * FROM listings WHERE id = ? AND (status = 'active' OR status IS NULL)");
            $stmt->execute([$listingId]);
            $listing = $stmt->fetch();
            
            if (!$listing) {
                return ['success' => false, 'error' => 'Product not found'];
            }
            
            // Determine user or guest
            $userId = null;
            $sessionId = null;
            
            if ($this->authManager->isLoggedIn()) {
                $user = $this->authManager->getCurrentUser();
                $userId = $user['id'];
            } else {
                $sessionId = $this->getOrCreateSessionId();
            }
            
            // Check if item already exists in cart
            if ($userId) {
                $stmt = $this->pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND listing_id = ?");
                $stmt->execute([$userId, $listingId]);
            } else {
                $stmt = $this->pdo->prepare("SELECT * FROM cart WHERE session_id = ? AND listing_id = ?");
                $stmt->execute([$sessionId, $listingId]);
            }
            
            $existingItem = $stmt->fetch();
            
            if ($existingItem) {
                // Update quantity
                $newQuantity = $existingItem['quantity'] + $quantity;
                $stmt = $this->pdo->prepare("
                    UPDATE cart 
                    SET quantity = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$newQuantity, $existingItem['id']]);
            } else {
                // Add new item
                $stmt = $this->pdo->prepare("
                    INSERT INTO cart (user_id, session_id, listing_id, quantity, price_at_add) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $userId,
                    $sessionId,
                    $listingId,
                    $quantity,
                    $listing['price'] ?? 0
                ]);
            }
            
            return ['success' => true, 'message' => 'Item added to cart'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Failed to add to cart: ' . $e->getMessage()];
        }
    }
    
    // Get cart items
    public function getCartItems() {
        try {
            $userId = null;
            $sessionId = null;
            
            if ($this->authManager->isLoggedIn()) {
                $user = $this->authManager->getCurrentUser();
                $userId = $user['id'];
                $sql = "
                    SELECT c.*, l.title, l.description, l.image, l.category
                    FROM cart c
                    JOIN listings l ON c.listing_id = l.id
                    WHERE c.user_id = ?
                    ORDER BY c.created_at DESC
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$userId]);
            } else {
                $sessionId = $this->getOrCreateSessionId();
                $sql = "
                    SELECT c.*, l.title, l.description, l.image, l.category
                    FROM cart c
                    JOIN listings l ON c.listing_id = l.id
                    WHERE c.session_id = ?
                    ORDER BY c.created_at DESC
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$sessionId]);
            }
            
            $items = $stmt->fetchAll();
            
            // Calculate totals
            $totalItems = 0;
            $totalAmount = 0;
            
            foreach ($items as $item) {
                $totalItems += $item['quantity'];
                $totalAmount += $item['quantity'] * $item['price_at_add'];
            }
            
            return [
                'items' => $items,
                'total_items' => $totalItems,
                'total_amount' => $totalAmount,
                'item_count' => count($items)
            ];
            
        } catch (Exception $e) {
            return [
                'items' => [],
                'total_items' => 0,
                'total_amount' => 0,
                'item_count' => 0
            ];
        }
    }
    
    // Update cart item quantity
    public function updateQuantity($cartItemId, $quantity) {
        try {
            if ($quantity <= 0) {
                return $this->removeFromCart($cartItemId);
            }
            
            // Verify ownership
            if ($this->authManager->isLoggedIn()) {
                $user = $this->authManager->getCurrentUser();
                $stmt = $this->pdo->prepare("
                    UPDATE cart 
                    SET quantity = ?, updated_at = NOW() 
                    WHERE id = ? AND user_id = ?
                ");
                $result = $stmt->execute([$quantity, $cartItemId, $user['id']]);
            } else {
                $sessionId = $this->getOrCreateSessionId();
                $stmt = $this->pdo->prepare("
                    UPDATE cart 
                    SET quantity = ?, updated_at = NOW() 
                    WHERE id = ? AND session_id = ?
                ");
                $result = $stmt->execute([$quantity, $cartItemId, $sessionId]);
            }
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Cart updated'];
            } else {
                return ['success' => false, 'error' => 'Item not found in cart'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Failed to update cart: ' . $e->getMessage()];
        }
    }
    
    // Remove item from cart
    public function removeFromCart($cartItemId) {
        try {
            // Verify ownership
            if ($this->authManager->isLoggedIn()) {
                $user = $this->authManager->getCurrentUser();
                $stmt = $this->pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $result = $stmt->execute([$cartItemId, $user['id']]);
            } else {
                $sessionId = $this->getOrCreateSessionId();
                $stmt = $this->pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
                $result = $stmt->execute([$cartItemId, $sessionId]);
            }
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Item removed from cart'];
            } else {
                return ['success' => false, 'error' => 'Item not found in cart'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Failed to remove item: ' . $e->getMessage()];
        }
    }
    
    // Clear cart
    public function clearCart() {
        try {
            if ($this->authManager->isLoggedIn()) {
                $user = $this->authManager->getCurrentUser();
                $stmt = $this->pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$user['id']]);
            } else {
                $sessionId = $this->getOrCreateSessionId();
                $stmt = $this->pdo->prepare("DELETE FROM cart WHERE session_id = ?");
                $stmt->execute([$sessionId]);
            }
            
            return ['success' => true, 'message' => 'Cart cleared'];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Failed to clear cart: ' . $e->getMessage()];
        }
    }
    
    // Get cart count for header display
    public function getCartCount() {
        $cart = $this->getCartItems();
        return $cart['total_items'];
    }
    
    // Merge guest cart with user cart when logging in
    public function mergeGuestCart($userId) {
        try {
            $sessionId = $this->getOrCreateSessionId();
            
            // Get guest cart items
            $stmt = $this->pdo->prepare("SELECT * FROM cart WHERE session_id = ?");
            $stmt->execute([$sessionId]);
            $guestItems = $stmt->fetchAll();
            
            foreach ($guestItems as $item) {
                // Check if user already has this item
                $stmt = $this->pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND listing_id = ?");
                $stmt->execute([$userId, $item['listing_id']]);
                $existingItem = $stmt->fetch();
                
                if ($existingItem) {
                    // Update quantity
                    $newQuantity = $existingItem['quantity'] + $item['quantity'];
                    $stmt = $this->pdo->prepare("
                        UPDATE cart 
                        SET quantity = ?, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->execute([$newQuantity, $existingItem['id']]);
                    
                    // Remove guest item
                    $stmt = $this->pdo->prepare("DELETE FROM cart WHERE id = ?");
                    $stmt->execute([$item['id']]);
                } else {
                    // Transfer to user cart
                    $stmt = $this->pdo->prepare("
                        UPDATE cart 
                        SET user_id = ?, session_id = NULL, updated_at = NOW() 
                        WHERE id = ?
                    ");
                    $stmt->execute([$userId, $item['id']]);
                }
            }
            
            return ['success' => true];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Failed to merge cart: ' . $e->getMessage()];
        }
    }
}

// Initialize cart manager
$cartManager = new CartManager();
?>