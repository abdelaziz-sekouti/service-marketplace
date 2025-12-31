<?php
require_once '../db_connect.php';

$listingId = $_GET['id'] ?? null;
$ipAddress = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referrer = $_SERVER['HTTP_REFERER'] ?? '';

if (!$listingId) {
    die('Invalid request');
}

try {
    // Get the affiliate link
    $stmt = executeQuery("SELECT affiliate_link FROM listings WHERE id = ?", [$listingId]);
    $listing = $stmt->fetch();
    
    if ($listing && $listing['affiliate_link']) {
        // Track the click
        executeQuery(
            "INSERT INTO analytics (listing_id, ip_address, user_agent, referrer) VALUES (?, ?, ?, ?)",
            [$listingId, $ipAddress, $userAgent, $referrer]
        );
        
        // Redirect to affiliate link
        header("Location: " . $listing['affiliate_link']);
        exit();
    } else {
        die('Affiliate link not found');
    }
} catch (Exception $e) {
    die('Error processing click');
}
?>