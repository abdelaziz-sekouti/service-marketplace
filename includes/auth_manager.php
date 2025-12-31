<?php
require_once  __DIR__.'/../db_connect.php';

class AuthManager {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    // Register new user
    public function register($userData) {
        try {
            // Validate required fields
            $required = ['username', 'email', 'password', 'first_name', 'last_name'];
            foreach ($required as $field) {
                if (empty($userData[$field])) {
                    return ['success' => false, 'error' => "Field $field is required"];
                }
            }
            
            // Validate email format
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'error' => 'Invalid email format'];
            }
            
            // Check if username or email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$userData['username'], $userData['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'Username or email already exists'];
            }
            
            // Hash password
            $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password_hash, first_name, last_name, phone) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userData['username'],
                $userData['email'],
                $passwordHash,
                $userData['first_name'],
                $userData['last_name'],
                $userData['phone'] ?? null
            ]);
            
            $userId = $this->pdo->lastInsertId();
            
            // Log in user
            $this->loginUser($userData['email'], $userData['password']);
            
            return ['success' => true, 'user_id' => $userId];
            
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    // Login user
    public function login($email, $password) {
        try {
            return $this->loginUser($email, $password);
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Login failed'];
        }
    }
    
    private function loginUser($email, $password) {
        // Get user by email
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !md5($password) == $user['password_hash']){
            return ['success' => false, 'error' => 'Invalid email or password'];
        }
        
        // Set session
        session_start();
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        
        // Update last login (optional)
        $stmt = $this->pdo->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        return ['success' => true, 'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'name' => $user['first_name'] . ' ' . $user['last_name']
        ]];
    }
    
    // Logout user
    public function logout() {
        session_start();
        unset($_SESSION['user_logged_in']);
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        return ['success' => true];
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        // session_start();
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }
    
    // Get current user
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Merge guest cart with user cart when logging in
    public function mergeGuestCart($userId) {
        try {
            require_once 'cart_manager.php';
            $cartManager = new CartManager();
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
    
    // Get or create session ID for guest users
    private function getOrCreateSessionId() {
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = uniqid('cart_', true);
        }
        return $_SESSION['cart_session_id'];
    }
    
    // Update user profile
    public function updateProfile($userId, $userData) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET first_name = ?, last_name = ?, phone = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([
                $userData['first_name'],
                $userData['last_name'],
                $userData['phone'] ?? null,
                $userId
            ]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Update failed: ' . $e->getMessage()];
        }
    }
    
    // Change password
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Verify current password
            $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                return ['success' => false, 'error' => 'Current password is incorrect'];
            }
            
            // Update password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newPasswordHash, $userId]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Password change failed: ' . $e->getMessage()];
        }
    }
}

// Initialize auth manager
$authManager = new AuthManager();
?>