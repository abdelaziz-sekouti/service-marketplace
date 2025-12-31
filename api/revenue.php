<?php
require_once '../includes/affiliate_manager.php';

header('Content-Type: application/json');

// Get revenue statistics
$stats = $affiliateManager->getRevenueStats('30');

// Calculate totals
$totalRevenue = 0;
$totalConversions = 0;
$revenueByType = [];

foreach ($stats as $stat) {
    $totalRevenue += $stat['daily_revenue'];
    $totalConversions += $stat['conversions'];
    
    if (!isset($revenueByType[$stat['commission_type']])) {
        $revenueByType[$stat['commission_type']] = 0;
    }
    $revenueByType[$stat['commission_type']] += $stat['daily_revenue'];
}

echo json_encode([
    'totalRevenue' => round($totalRevenue, 2),
    'totalConversions' => $totalConversions,
    'revenueByType' => $revenueByType,
    'dailyStats' => $stats
]);
?>