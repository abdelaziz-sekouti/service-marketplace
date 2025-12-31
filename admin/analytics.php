<?php
session_start();
require_once '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit();
}

// Get date range from URL
$dateRange = $_GET['range'] ?? '7';
$dateRange = intval($dateRange);

try {
    // Clicks over time
    $stmt = executeQuery("
        SELECT DATE(clicked_at) as date, COUNT(*) as clicks, COUNT(DISTINCT listing_id) as unique_listings
        FROM analytics 
        WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY DATE(clicked_at)
        ORDER BY date DESC
        LIMIT 30
    ", [$dateRange]);
    $clicksOverTime = $stmt->fetchAll();
    
    // Top performing listings
    $stmt = executeQuery("
        SELECT l.*, COUNT(a.id) as clicks, COUNT(DISTINCT a.ip_address) as unique_clicks
        FROM listings l 
        LEFT JOIN analytics a ON l.id = a.listing_id 
        WHERE a.clicked_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY l.id 
        ORDER BY clicks DESC 
        LIMIT 10
    ", [$dateRange]);
    $topListings = $stmt->fetchAll();
    
    // Clicks by category
    $stmt = executeQuery("
        SELECT l.category, COUNT(a.id) as clicks, COUNT(DISTINCT l.id) as listings_in_category
        FROM listings l 
        LEFT JOIN analytics a ON l.id = a.listing_id 
        WHERE a.clicked_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY l.category 
        ORDER BY clicks DESC
    ", [$dateRange]);
    $clicksByCategory = $stmt->fetchAll();
    
    // Traffic sources
    $stmt = executeQuery("
        SELECT 
            CASE 
                WHEN referrer IS NULL OR referrer = '' THEN 'Direct'
                WHEN referrer LIKE '%google%' THEN 'Google'
                WHEN referrer LIKE '%facebook%' THEN 'Facebook'
                WHEN referrer LIKE '%twitter%' THEN 'Twitter'
                WHEN referrer LIKE '%linkedin%' THEN 'LinkedIn'
                ELSE 'Other'
            END as source,
            COUNT(*) as clicks
        FROM analytics 
        WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY source
        ORDER BY clicks DESC
    ", [$dateRange]);
    $trafficSources = $stmt->fetchAll();
    
    // Hourly clicks (last 24 hours)
    $stmt = executeQuery("
        SELECT HOUR(clicked_at) as hour, COUNT(*) as clicks
        FROM analytics 
        WHERE clicked_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        GROUP BY HOUR(clicked_at)
        ORDER BY hour
    ");
    $hourlyClicks = $stmt->fetchAll();
    
    // Device types (user agent analysis)
    $stmt = executeQuery("
        SELECT 
            CASE 
                WHEN user_agent LIKE '%Mobile%' OR user_agent LIKE '%Android%' OR user_agent LIKE '%iPhone%' THEN 'Mobile'
                WHEN user_agent LIKE '%Tablet%' OR user_agent LIKE '%iPad%' THEN 'Tablet'
                ELSE 'Desktop'
            END as device,
            COUNT(*) as clicks
        FROM analytics 
        WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
        GROUP BY device
        ORDER BY clicks DESC
    ", [$dateRange]);
    $devices = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = 'Failed to load analytics data';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Freelance Money Maker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-chart-bar mr-2"></i>Analytics Dashboard
                </h1>
                <div class="flex items-center gap-4">
                    <a href="dashboard.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-sm">
                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                    </a>
                    <span class="text-gray-300">Welcome, <?php echo $_SESSION['admin_username']; ?></span>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex space-x-8">
                <a href="dashboard.php" class="py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="listings.php" class="py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-list mr-2"></i>Listings
                </a>
                <a href="revenue.php" class="py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-dollar-sign mr-2"></i>Revenue
                </a>
                <a href="analytics.php" class="py-3 border-b-2 border-blue-500 text-blue-600 font-medium">
                    <i class="fas fa-chart-bar mr-2"></i>Analytics
                </a>
                <a href="settings.php" class="py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-cog mr-2"></i>Settings
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Date Range Selector -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-calendar mr-2 text-blue-600"></i>Analytics Period
                </h3>
                <div class="flex gap-2">
                    <a href="?range=7" class="px-4 py-2 rounded <?php echo $dateRange === 7 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">7 Days</a>
                    <a href="?range=30" class="px-4 py-2 rounded <?php echo $dateRange === 30 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">30 Days</a>
                    <a href="?range=90" class="px-4 py-2 rounded <?php echo $dateRange === 90 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">90 Days</a>
                </div>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Clicks</p>
                        <p class="text-2xl font-bold text-blue-600"><?php echo array_sum(array_column($clicksOverTime, 'clicks')); ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-mouse-pointer text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Daily Average</p>
                        <p class="text-2xl font-bold text-green-600">
                            <?php 
                            $totalClicks = array_sum(array_column($clicksOverTime, 'clicks'));
                            $days = count($clicksOverTime);
                            echo $days > 0 ? round($totalClicks / $days, 1) : 0;
                            ?>
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-chart-line text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Top Day</p>
                        <p class="text-2xl font-bold text-purple-600">
                            <?php 
                            $maxClicks = !empty($clicksOverTime) ? max(array_column($clicksOverTime, 'clicks')) : 0;
                            echo $maxClicks;
                            ?>
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-trophy text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Active Listings</p>
                        <p class="text-2xl font-bold text-orange-600"><?php echo count($topListings); ?></p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-fire text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Performing Listings -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-trophy mr-2 text-yellow-600"></i>Top Performing Listings
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($topListings as $index => $listing): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center flex-1">
                                    <span class="text-lg font-bold text-gray-400 mr-3"><?php echo $index + 1; ?></span>
                                    <img src="<?php echo $listing['image'] ?: 'https://picsum.photos/seed/' . $listing['id'] . '/40/40.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($listing['title']); ?>" 
                                         class="w-10 h-10 rounded object-cover mr-3">
                                    <div class="flex-1">
                                        <h4 class="font-medium line-clamp-1"><?php echo htmlspecialchars($listing['title']); ?></h4>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($listing['category']); ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-blue-600"><?php echo $listing['clicks']; ?> clicks</p>
                                    <p class="text-xs text-gray-500"><?php echo $listing['unique_clicks']; ?> unique</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Traffic Sources -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-globe mr-2 text-blue-600"></i>Traffic Sources
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($trafficSources as $source): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center flex-1">
                                    <i class="fas 
                                        <?php 
                                        echo match($source['source']) {
                                            'Direct' => 'fa-link',
                                            'Google' => 'fa-google',
                                            'Facebook' => 'fa-facebook',
                                            'Twitter' => 'fa-twitter',
                                            'LinkedIn' => 'fa-linkedin',
                                            default => 'fa-globe'
                                        };
                                        ?> 
                                        mr-3 text-gray-600"></i>
                                    <span class="font-medium"><?php echo htmlspecialchars($source['source']); ?></span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php 
                                        $totalTraffic = array_sum(array_column($trafficSources, 'clicks'));
                                        echo $totalTraffic > 0 ? ($source['clicks'] / $totalTraffic) * 100 : 0; 
                                        ?>%"></div>
                                    </div>
                                    <span class="text-sm font-medium w-12 text-right"><?php echo $source['clicks']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Clicks by Category -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-tags mr-2 text-green-600"></i>Clicks by Category
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($clicksByCategory as $category): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex-1">
                                    <h4 class="font-medium"><?php echo htmlspecialchars($category['category']); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo $category['listings_in_category']; ?> listings</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600"><?php echo $category['clicks']; ?> clicks</p>
                                    <p class="text-xs text-gray-500">
                                        <?php 
                                        $totalCatClicks = array_sum(array_column($clicksByCategory, 'clicks'));
                                        echo $totalCatClicks > 0 ? round(($category['clicks'] / $totalCatClicks) * 100, 1) : 0; 
                                        ?>%
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Device Types -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-mobile-alt mr-2 text-purple-600"></i>Device Types
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($devices as $device): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <i class="fas 
                                        <?php 
                                        echo match($device['device']) {
                                            'Mobile' => 'fa-mobile-alt',
                                            'Tablet' => 'fa-tablet-alt',
                                            'Desktop' => 'fa-desktop',
                                            default => 'fa-question'
                                        };
                                        ?> 
                                        mr-3 text-gray-600 text-lg"></i>
                                    <span class="font-medium"><?php echo htmlspecialchars($device['device']); ?></span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: <?php 
                                        $totalDevices = array_sum(array_column($devices, 'clicks'));
                                        echo $totalDevices > 0 ? ($device['clicks'] / $totalDevices) * 100 : 0; 
                                        ?>%"></div>
                                    </div>
                                    <span class="text-sm font-medium w-12 text-right"><?php echo $device['clicks']; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clicks Over Time -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-chart-line mr-2 text-blue-600"></i>Clicks Over Time
            </h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <div class="text-center">
                    <i class="fas fa-chart-area text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">Click trend chart will be displayed here</p>
                    <p class="text-sm text-gray-400 mt-2">Last <?php echo count($clicksOverTime); ?> days of data</p>
                </div>
            </div>
            
            <!-- Simple table view -->
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Clicks</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unique Listings</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach (array_slice($clicksOverTime, 0, 10) as $click): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm"><?php echo date('M j, Y', strtotime($click['date'])); ?></td>
                                <td class="px-4 py-2 text-sm font-medium"><?php echo $click['clicks']; ?></td>
                                <td class="px-4 py-2 text-sm"><?php echo $click['unique_listings']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Export Options -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-download mr-2 text-green-600"></i>Export Analytics
            </h3>
            <div class="flex gap-3">
                <button onclick="exportData('csv')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-file-csv mr-2"></i>Export as CSV
                </button>
                <button onclick="exportData('json')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-file-code mr-2"></i>Export as JSON
                </button>
                <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        // Export data functions
        function exportData(format) {
            const data = {
                topListings: <?php echo json_encode($topListings); ?>,
                clicksByCategory: <?php echo json_encode($clicksByCategory); ?>,
                trafficSources: <?php echo json_encode($trafficSources); ?>,
                devices: <?php echo json_encode($devices); ?>,
                clicksOverTime: <?php echo json_encode($clicksOverTime); ?>
            };
            
            if (format === 'csv') {
                // Simple CSV export for top listings
                let csv = 'Title,Category,Clicks,Unique Clicks\n';
                data.topListings.forEach(item => {
                    csv += `"${item.title}","${item.category}",${item.clicks},${item.unique_clicks}\n`;
                });
                
                downloadFile(csv, 'analytics.csv', 'text/csv');
            } else if (format === 'json') {
                downloadFile(JSON.stringify(data, null, 2), 'analytics.json', 'application/json');
            }
        }

        function downloadFile(content, filename, contentType) {
            const blob = new Blob([content], { type: contentType });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>