<?php
session_start();
require_once '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit();
}

// Handle settings updates
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_settings') {
        // Update general settings
        $siteTitle = sanitize($_POST['site_title']);
        $siteDescription = sanitize($_POST['site_description']);
        $adminEmail = sanitize($_POST['admin_email']);
        
        try {
            // For now, we'll just store these in a simple settings table
            // In production, you might want a more robust settings system
            $stmt = executeQuery("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES ('site_title', ?), ('site_description', ?), ('admin_email', ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ", [$siteTitle, $siteDescription, $adminEmail]);
            
            $message = 'Settings updated successfully!';
            
        } catch (Exception $e) {
            $error = 'Failed to update settings: ' . $e->getMessage();
        }
        
    } elseif ($action === 'add_network') {
        // Add new affiliate network
        $name = sanitize($_POST['network_name']);
        $domain = sanitize($_POST['network_domain']);
        $commission = floatval($_POST['commission_rate']);
        
        try {
            $stmt = executeQuery("
                INSERT INTO affiliate_networks (name, tracking_domain, commission_rate) 
                VALUES (?, ?, ?)
            ", [$name, $domain, $commission]);
            
            $message = 'Affiliate network added successfully!';
            
        } catch (Exception $e) {
            $error = 'Failed to add network: ' . $e->getMessage();
        }
        
    } elseif ($action === 'add_placement') {
        // Add new ad placement
        $name = sanitize($_POST['placement_name']);
        $position = sanitize($_POST['position']);
        $dimensions = sanitize($_POST['dimensions']);
        $adCode = sanitize($_POST['ad_code']);
        $priority = intval($_POST['priority']);
        
        try {
            $stmt = executeQuery("
                INSERT INTO ad_placements (name, position, dimensions, ad_code, priority) 
                VALUES (?, ?, ?, ?, ?)
            ", [$name, $position, $dimensions, $adCode, $priority]);
            
            $message = 'Ad placement added successfully!';
            
        } catch (Exception $e) {
            $error = 'Failed to add placement: ' . $e->getMessage();
        }
    }
}

// Get current settings
$settings = [];
try {
    $stmt = executeQuery("SELECT * FROM settings");
    $result = $stmt->fetchAll();
    foreach ($result as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Settings table might not exist yet
    $settings = [
        'site_title' => 'Freelance Money Maker',
        'site_description' => 'Discover the best tools and services to maximize your freelance income',
        'admin_email' => 'admin@example.com'
    ];
}

// Get affiliate networks
try {
    $stmt = executeQuery("SELECT * FROM affiliate_networks ORDER BY name");
    $networks = $stmt->fetchAll();
} catch (Exception $e) {
    $networks = [];
}

// Get ad placements
try {
    $stmt = executeQuery("SELECT * FROM ad_placements ORDER BY position, priority DESC");
    $placements = $stmt->fetchAll();
} catch (Exception $e) {
    $placements = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Freelance Money Maker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-cog mr-2"></i>Settings
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
                <a href="analytics.php" class="py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-chart-bar mr-2"></i>Analytics
                </a>
                <a href="settings.php" class="py-3 border-b-2 border-blue-500 text-blue-600 font-medium">
                    <i class="fas fa-cog mr-2"></i>Settings
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- General Settings -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-globe mr-2 text-blue-600"></i>General Settings
                    </h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_settings">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Site Title</label>
                                <input type="text" name="site_title" value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email</label>
                                <input type="email" name="admin_email" value="<?php echo htmlspecialchars($settings['admin_email'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Description</label>
                            <textarea name="site_description" rows="3"
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                                <i class="fas fa-save mr-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Ad Placements -->
                <div class="bg-white rounded-lg shadow p-6 mt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-ad mr-2 text-green-600"></i>Ad Placements
                        </h3>
                        <button onclick="togglePlacementForm()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-plus mr-2"></i>Add Placement
                        </button>
                    </div>
                    
                    <!-- Add Placement Form -->
                    <div id="placementForm" class="bg-gray-50 p-4 rounded-lg mb-4 hidden">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_placement">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Placement Name</label>
                                    <input type="text" name="placement_name" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                    <select name="position" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="header">Header</option>
                                        <option value="sidebar">Sidebar</option>
                                        <option value="between_content">Between Content</option>
                                        <option value="footer">Footer</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dimensions</label>
                                    <input type="text" name="dimensions" placeholder="e.g., 300x250" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                    <input type="number" name="priority" value="0" min="0"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Code</label>
                                <textarea name="ad_code" rows="3" placeholder="Paste your AdSense or other ad code here"
                                          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                            </div>
                            
                            <div class="mt-4 flex gap-3">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fas fa-save mr-2"></i>Add Placement
                                </button>
                                <button type="button" onclick="togglePlacementForm()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Placements Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dimensions</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($placements as $placement): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm font-medium"><?php echo htmlspecialchars($placement['name']); ?></td>
                                        <td class="px-4 py-2 text-sm"><?php echo ucfirst($placement['position']); ?></td>
                                        <td class="px-4 py-2 text-sm"><?php echo $placement['dimensions']; ?></td>
                                        <td class="px-4 py-2 text-sm"><?php echo $placement['priority']; ?></td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                <?php echo $placement['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo ucfirst($placement['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <button class="text-blue-600 hover:text-blue-900 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Affiliate Networks -->
            <div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            <i class="fas fa-link mr-2 text-purple-600"></i>Affiliate Networks
                        </h3>
                        <button onclick="toggleNetworkForm()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-plus mr-2"></i>Add Network
                        </button>
                    </div>
                    
                    <!-- Add Network Form -->
                    <div id="networkForm" class="bg-gray-50 p-4 rounded-lg mb-4 hidden">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_network">
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Network Name</label>
                                    <input type="text" name="network_name" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Domain</label>
                                    <input type="text" name="network_domain" placeholder="example.com" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Commission Rate (%)</label>
                                    <input type="number" name="commission_rate" step="0.01" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                            
                            <div class="mt-4 flex gap-3">
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fas fa-save mr-2"></i>Add Network
                                </button>
                                <button type="button" onclick="toggleNetworkForm()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Networks List -->
                    <div class="space-y-3">
                        <?php foreach ($networks as $network): ?>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="font-medium"><?php echo htmlspecialchars($network['name']); ?></h4>
                                        <p class="text-sm text-gray-600"><?php echo $network['tracking_domain']; ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-purple-600"><?php echo $network['commission_rate']; ?>%</p>
                                        <p class="text-xs text-gray-500">commission</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Site Statistics -->
                <div class="bg-white rounded-lg shadow p-6 mt-8">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Site Statistics
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">PHP Version</span>
                            <span class="font-medium"><?php echo PHP_VERSION; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">MySQL Version</span>
                            <span class="font-medium"><?php 
                            try {
                                $stmt = executeQuery("SELECT VERSION() as version");
                                echo $stmt->fetch()['version'];
                            } catch (Exception $e) {
                                echo 'Unknown';
                            }
                            ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Upload Max Size</span>
                            <span class="font-medium"><?php echo ini_get('upload_max_filesize'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Memory Limit</span>
                            <span class="font-medium"><?php echo ini_get('memory_limit'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        function toggleNetworkForm() {
            const form = document.getElementById('networkForm');
            form.classList.toggle('hidden');
            if (!form.classList.contains('hidden')) {
                form.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function togglePlacementForm() {
            const form = document.getElementById('placementForm');
            form.classList.toggle('hidden');
            if (!form.classList.contains('hidden')) {
                form.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Auto-hide success messages
        setTimeout(() => {
            const messages = document.querySelectorAll('.bg-green-100');
            messages.forEach(msg => msg.style.display = 'none');
        }, 5000);
    </script>
</body>
</html>