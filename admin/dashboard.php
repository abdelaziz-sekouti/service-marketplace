<?php
session_start();
require_once '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit();
}

// Get dashboard stats
try {
    $stats = [];
    
    // Total listings
    $stmt = executeQuery("SELECT COUNT(*) as count FROM listings");
    $stats['totalListings'] = $stmt->fetch()['count'];
    
    // Total clicks
    $stmt = executeQuery("SELECT COUNT(*) as count FROM analytics");
    $stats['totalClicks'] = $stmt->fetch()['count'];
    
    // Clicks today
    $stmt = executeQuery("SELECT COUNT(*) as count FROM analytics WHERE DATE(clicked_at) = CURDATE()");
    $stats['clicksToday'] = $stmt->fetch()['count'];
    
    // Recent listings
    $stmt = executeQuery("SELECT * FROM listings ORDER BY created_at DESC LIMIT 5");
    $stats['recentListings'] = $stmt->fetchAll();
    
    // Top performing listings
    $stmt = executeQuery("
        SELECT l.*, COUNT(a.id) as clicks 
        FROM listings l 
        LEFT JOIN analytics a ON l.id = a.listing_id 
        GROUP BY l.id 
        ORDER BY clicks DESC 
        LIMIT 5
    ");
    $stats['topListings'] = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = 'Failed to load dashboard data';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Freelance Money Maker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-cog mr-2"></i>Admin Dashboard
                </h1>
                <div class="flex items-center gap-4">
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
                <a href="dashboard.php" class="py-3 border-b-2 border-blue-500 text-blue-600 font-medium">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="listings.php" class="py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-list mr-2"></i>Listings
                </a>
                <a href="analytics.php" class="py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
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

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Listings</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['totalListings']; ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Clicks</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['totalClicks']; ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-mouse-pointer text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Clicks Today</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $stats['clicksToday']; ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar-day text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Conversion Rate</p>
                        <p class="text-2xl font-bold text-gray-800">
                            <?php echo $stats['totalListings'] > 0 ? round(($stats['totalClicks'] / $stats['totalListings']) * 100, 1) : 0; ?>%
                        </p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-chart-line text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Listings -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-clock mr-2 text-blue-600"></i>Recent Listings
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($stats['recentListings'] as $listing): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex-1">
                                    <h4 class="font-medium"><?php echo htmlspecialchars($listing['title']); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($listing['category']); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">$<?php echo $listing['price']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo date('M j', strtotime($listing['created_at'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Top Performing Listings -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-trophy mr-2 text-yellow-600"></i>Top Performing
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($stats['topListings'] as $listing): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div class="flex-1">
                                    <h4 class="font-medium"><?php echo htmlspecialchars($listing['title']); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($listing['category']); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium"><?php echo $listing['clicks']; ?> clicks</p>
                                    <p class="text-xs text-gray-500">$<?php echo $listing['price']; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-bolt mr-2 text-yellow-600"></i>Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="listings.php?action=add" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition duration-300">
                    <i class="fas fa-plus-circle text-2xl mb-2"></i>
                    <p>Add New Listing</p>
                </a>
                <a href="analytics.php" class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition duration-300">
                    <i class="fas fa-chart-bar text-2xl mb-2"></i>
                    <p>View Analytics</p>
                </a>
                <a href="../index.php" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition duration-300">
                    <i class="fas fa-external-link-alt text-2xl mb-2"></i>
                    <p>View Site</p>
                </a>
            </div>
        </div>
    </main>
</body>
</html>