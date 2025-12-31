<?php
// Test registration - debug version
require_once 'db_connect.php';

echo "<h2>Registration Test - Debug Mode</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    try {
        $title = "Test User";
        $description = "Test Description";
        $category = "Test Category";
        $affiliate_link = "https://test.com";
        $price = 99.99;
        $first_name = "Test";
        $last_name = "User";
        $phone = "1234567890";
        $email = "test" . time() . "@example.com";
        $username = "testuser" . time();
        $password = "testpass123";
        
        echo "<h3>Attempting to insert user:</h3>";
        echo "Username: $username<br>";
        echo "Email: $email<br>";
        
        // Check if user already exists
        $stmt = executeQuery("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            echo "<div style='color: red;'>User already exists!</div>";
        } else {
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            echo "Password Hash: $passwordHash<br>";
            
            // Insert user
            $stmt = executeQuery("
                INSERT INTO users (username, email, password_hash, first_name, last_name, phone) 
                VALUES (?, ?, ?, ?, ?, ?)
            ", [$username, $email, $passwordHash, $first_name, $last_name, $phone]);
            
            $userId = $pdo->lastInsertId();
            echo "<div style='color: green;'>User created with ID: $userId</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
        echo "<div style='color: red;'>Stack Trace: " . $e->getTraceAsString() . "</div>";
    }
} else {
    echo "<p>Submit the form below to test registration:</p>";
}
?>

<form method="POST">
    <input type="hidden" name="action" value="register">
    <button type="submit">Test Registration</button>
</form>