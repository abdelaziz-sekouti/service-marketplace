<?php
require_once '../includes/affiliate_manager.php';

header('Content-Type: application/json');

$position = $_GET['position'] ?? null;

// Get ad placements
$placements = $affiliateManager->getAdPlacements($position);

// Format for frontend
$formattedPlacements = [];
foreach ($placements as $placement) {
    $formattedPlacements[] = [
        'id' => $placement['id'],
        'name' => $placement['name'],
        'position' => $placement['position'],
        'ad_code' => $placement['ad_code'],
        'dimensions' => $placement['dimensions'],
        'priority' => $placement['priority']
    ];
}

echo json_encode($formattedPlacements);
?>