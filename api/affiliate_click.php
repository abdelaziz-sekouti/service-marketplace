<?php
require_once '../includes/affiliate_manager.php';

header('Content-Type: application/json');

$listingId = $_GET['id'] ?? null;
$networkId = $_GET['network'] ?? null;
$placementId = $_GET['placement'] ?? null;

if (!$listingId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing listing ID']);
    exit;
}

// Track the click
$result = $affiliateManager->trackClick($listingId, $networkId, $placementId);

if (isset($result['error'])) {
    http_response_code(500);
    echo json_encode(['error' => $result['error']]);
    exit;
}

// Redirect to affiliate link
if ($result['success']) {
    // Set cookie for attribution (30 days)
    setcookie('affiliate_click_' . $listingId, $result['click_id'], time() + (30 * 24 * 60 * 60), '/');
    
    // Log the click for analytics
    error_log("Affiliate click tracked: Listing ID $listingId, Click ID {$result['click_id']}");
    
    // Redirect
    header("Location: " . $result['affiliate_link']);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process affiliate click']);
}
?>