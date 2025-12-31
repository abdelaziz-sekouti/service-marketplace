<?php
header('Content-Type: application/json');
require_once '../db_connect.php';

// Add caching headers
header('Cache-Control: public, max-age=300'); // 5 minutes cache

try {
    // Get total listings
    $stmt = executeQuery("SELECT COUNT(*) as count FROM listings WHERE status = 'active'");
    $totalListings = $stmt->fetch()['count'];
    
    // Get total clicks
    $stmt = executeQuery("SELECT COUNT(*) as count FROM analytics");
    $totalClicks = $stmt->fetch()['count'];
    
    // Get total categories
    $stmt = executeQuery("SELECT COUNT(DISTINCT category) as count FROM listings WHERE status = 'active'");
    $totalCategories = $stmt->fetch()['count'];
    
    // Get average price
    $stmt = executeQuery("SELECT AVG(price) as avg_price FROM listings WHERE price IS NOT NULL AND status = 'active'");
    $avgPrice = $stmt->fetch()['avg_price'] ?? 0;
    
    echo json_encode([
        'totalListings' => (int)$totalListings,
        'totalClicks' => (int)$totalClicks,
        'totalCategories' => (int)$totalCategories,
        'avgPrice' => round($avgPrice, 2)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch stats']);
}
?>