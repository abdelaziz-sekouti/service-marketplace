<?php
header('Content-Type: application/json');
require_once '../db_connect.php';

// Add caching headers
header('Cache-Control: public, max-age=600'); // 10 minutes cache
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 600));

try {
    $stmt = executeQuery("
        SELECT l.id, l.title, l.description, l.category, l.price, l.image, l.affiliate_link, l.created_at,
               COUNT(a.id) as clicks 
        FROM listings l 
        LEFT JOIN analytics a ON l.id = a.listing_id 
        WHERE l.status = 'active' OR l.status IS NULL
        GROUP BY l.id 
        ORDER BY l.created_at DESC
        LIMIT 50
    ");
    $listings = $stmt->fetchAll();
    
    // Optimize image URLs for better performance
    foreach ($listings as &$listing) {
        if (!$listing['image']) {
            $listing['image'] = "https://picsum.photos/seed/{$listing['id']}/400/250.jpg";
        }
        // Add WebP support check
        $listing['image_webp'] = str_replace('.jpg', '.webp', $listing['image']);
    }
    
    echo json_encode($listings);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch listings']);
}
?>