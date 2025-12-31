<?php
require_once '../includes/cart_manager.php';

header('Content-Type: application/json');

// Handle add to cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $listingId = $_POST['listing_id'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;
    
    if (!$listingId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Listing ID is required']);
        exit();
    }
    
    $result = $cartManager->addToCart($listingId, $quantity);
    
    if ($result['success']) {
        // Return updated cart count
        $cart = $cartManager->getCartItems();
        echo json_encode([
            'success' => true,
            'message' => $result['message'],
            'cart_count' => $cart['total_items']
        ]);
    } else {
        http_response_code(400);
        echo json_encode($result);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>