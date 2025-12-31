<?php
session_start();
require_once '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit();
}

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        // Add new listing
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $category = sanitize($_POST['category']);
        $affiliate_link = sanitize($_POST['affiliate_link']);
        $price = floatval($_POST['price']);
        $image = sanitize($_POST['image']);
        $network_id = intval($_POST['network_id']);
        
        try {
            $stmt = executeQuery("
                INSERT INTO listings (title, description, category, affiliate_link, price, image) 
                VALUES (?, ?, ?, ?, ?, ?)
            ", [$title, $description, $category, $affiliate_link, $price, $image]);
            
            $message = 'Listing added successfully!';
            
        } catch (Exception $e) {
            $error = 'Failed to add listing: ' . $e->getMessage();
        }
        
    } elseif ($action === 'edit') {
        // Edit existing listing
        $id = intval($_POST['id']);
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $category = sanitize($_POST['category']);
        $affiliate_link = sanitize($_POST['affiliate_link']);
        $price = floatval($_POST['price']);
        $image = sanitize($_POST['image']);
        $status = sanitize($_POST['status']);
        
        try {
            $stmt = executeQuery("
                UPDATE listings 
                SET title = ?, description = ?, category = ?, affiliate_link = ?, price = ?, image = ?, status = ?
                WHERE id = ?
            ", [$title, $description, $category, $affiliate_link, $price, $image, $status, $id]);
            
            $message = 'Listing updated successfully!';
            
        } catch (Exception $e) {
            $error = 'Failed to update listing: ' . $e->getMessage();
        }
        
    } elseif ($action === 'delete') {
        // Delete listing
        $id = intval($_POST['id']);
        
        try {
            $stmt = executeQuery("DELETE FROM listings WHERE id = ?", [$id]);
            $message = 'Listing deleted successfully!';
            
        } catch (Exception $e) {
            $error = 'Failed to delete listing: ' . $e->getMessage();
        }
    }
}

// Get listings for display
try {
    $stmt = executeQuery("
        SELECT l.*, COUNT(a.id) as clicks 
        FROM listings l 
        LEFT JOIN analytics a ON l.id = a.listing_id 
        GROUP BY l.id 
        ORDER BY l.created_at DESC
    ");
    $listings = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Failed to load listings';
}

// Get categories
$categories = [];
if (!empty($listings)) {
    $categories = array_unique(array_column($listings, 'category'));
    sort($categories);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings Management - Freelance Money Maker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-gray-800 text-white">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-list mr-2"></i>Listings Management
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
                <a href="listings.php" class="py-3 border-b-2 border-blue-500 text-blue-600 font-medium">
                    <i class="fas fa-list mr-2"></i>Listings
                </a>
                <a href="revenue.php" class="py-3 border-b-2 border-transparent text-gray-600 hover:text-gray-800">
                    <i class="fas fa-dollar-sign mr-2"></i>Revenue
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Listings</p>
                        <p class="text-2xl font-bold"><?php echo count($listings); ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Active Listings</p>
                        <p class="text-2xl font-bold"><?php echo count(array_filter($listings, fn($l) => $l['status'] === 'active')); ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Clicks</p>
                        <p class="text-2xl font-bold"><?php echo array_sum(array_column($listings, 'clicks')); ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-mouse-pointer text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Categories</p>
                        <p class="text-2xl font-bold"><?php echo count($categories); ?></p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-tags text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Listing Button -->
        <div class="mb-6">
            <button onclick="showAddForm()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                <i class="fas fa-plus-circle mr-2"></i>Add New Listing
            </button>
        </div>

        <!-- Add/Edit Listing Form -->
        <div id="listingForm" class="bg-white rounded-lg shadow p-6 mb-8 hidden">
            <h3 class="text-lg font-semibold mb-4">
                <span id="formTitle">Add New Listing</span>
            </h3>
            <form method="POST" id="listingFormElement">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="listingId">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" name="title" id="title" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <input type="text" name="category" id="category" required
                               placeholder="e.g., Design Tools, Marketing"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price ($) *</label>
                        <input type="number" name="price" id="price" step="0.01" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                        <input type="url" name="image" id="image"
                               placeholder="https://example.com/image.jpg"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea name="description" id="description" rows="4" required
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Affiliate Link</label>
                    <input type="url" name="affiliate_link" id="affiliate_link"
                           placeholder="https://example.com/affiliate-link"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i>Save Listing
                    </button>
                    <button type="button" onclick="hideForm()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Listings Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-list mr-2 text-blue-600"></i>All Listings
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Listing</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($listings as $listing): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="<?php echo $listing['image'] ?: 'https://picsum.photos/seed/' . $listing['id'] . '/50/50.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($listing['title']); ?>" 
                                             class="w-10 h-10 rounded object-cover mr-3">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($listing['title']); ?></div>
                                            <div class="text-xs text-gray-500">Created: <?php echo date('M j, Y', strtotime($listing['created_at'])); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($listing['category']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    $<?php echo number_format($listing['price'], 2); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $listing['clicks']; ?></div>
                                    <?php if ($listing['clicks'] > 0): ?>
                                        <div class="text-xs text-gray-500">CTR: <?php echo round(($listing['clicks'] / 100) * 100, 1); ?>%</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?php echo $listing['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                                ($listing['status'] === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                        <?php echo ucfirst($listing['status'] ?: 'active'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <button onclick="editListing(<?php echo $listing['id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="confirmDelete(<?php echo $listing['id']; ?>, '<?php echo addslashes($listing['title']); ?>')" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        // Show add form
        function showAddForm() {
            document.getElementById('listingForm').classList.remove('hidden');
            document.getElementById('formTitle').textContent = 'Add New Listing';
            document.getElementById('formAction').value = 'add';
            document.getElementById('listingFormElement').reset();
            document.getElementById('listingId').value = '';
            window.scrollTo({ top: document.getElementById('listingForm').offsetTop - 100, behavior: 'smooth' });
        }

        // Hide form
        function hideForm() {
            document.getElementById('listingForm').classList.add('hidden');
        }

        // Edit listing
        function editListing(id) {
            const listings = <?php echo json_encode($listings); ?>;
            const listing = listings.find(l => l.id == id);
            
            if (listing) {
                document.getElementById('listingForm').classList.remove('hidden');
                document.getElementById('formTitle').textContent = 'Edit Listing';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('listingId').value = listing.id;
                
                document.getElementById('title').value = listing.title;
                document.getElementById('category').value = listing.category;
                document.getElementById('price').value = listing.price;
                document.getElementById('status').value = listing.status || 'active';
                document.getElementById('image').value = listing.image || '';
                document.getElementById('description').value = listing.description;
                document.getElementById('affiliate_link').value = listing.affiliate_link || '';
                
                window.scrollTo({ top: document.getElementById('listingForm').offsetTop - 100, behavior: 'smooth' });
            }
        }

        // Confirm delete
        function confirmDelete(id, title) {
            if (confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
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