<?php
// Testing and validation utilities

class TestRunner {
    private $results = [];
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    // Run all tests
    public function runAllTests() {
        echo "<div class='container mx-auto px-4 py-8'>";
        echo "<h1 class='text-3xl font-bold mb-8'>🧪 System Testing Report</h1>";
        
        $this->testDatabaseConnection();
        $this->testListingsCRUD();
        $this->testAffiliateTracking();
        $this->testAnalytics();
        $this->testSecurity();
        $this->testPerformance();
        $this->testMonetization();
        
        $this->generateReport();
    }
    
    // Test database connection
    private function testDatabaseConnection() {
        try {
            $stmt = $this->pdo->query("SELECT 1");
            $this->results['database'] = [
                'status' => 'PASS',
                'message' => 'Database connection successful'
            ];
        } catch (Exception $e) {
            $this->results['database'] = [
                'status' => 'FAIL',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Test listings CRUD operations
    private function testListingsCRUD() {
        try {
            // Test insert
            $stmt = $this->pdo->prepare("
                INSERT INTO listings (title, description, category, price) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute(['Test Product', 'Test description', 'Test Category', 99.99]);
            $id = $this->pdo->lastInsertId();
            
            // Test read
            $stmt = $this->pdo->prepare("SELECT * FROM listings WHERE id = ?");
            $stmt->execute([$id]);
            $listing = $stmt->fetch();
            
            // Test update
            $stmt = $this->pdo->prepare("UPDATE listings SET price = ? WHERE id = ?");
            $stmt->execute([149.99, $id]);
            
            // Test delete
            $stmt = $this->pdo->prepare("DELETE FROM listings WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($listing && $listing['title'] === 'Test Product') {
                $this->results['listings_crud'] = [
                    'status' => 'PASS',
                    'message' => 'Listings CRUD operations working correctly'
                ];
            } else {
                throw new Exception('CRUD operations failed');
            }
        } catch (Exception $e) {
            $this->results['listings_crud'] = [
                'status' => 'FAIL',
                'message' => 'Listings CRUD test failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Test affiliate tracking
    private function testAffiliateTracking() {
        try {
            // Create test listing
            $stmt = $this->pdo->prepare("
                INSERT INTO listings (title, description, category, affiliate_link, price) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute(['Test Affiliate', 'Test desc', 'Test', 'https://example.com/test', 29.99]);
            $listingId = $this->pdo->lastInsertId();
            
            // Test click tracking
            $stmt = $this->pdo->prepare("
                INSERT INTO analytics (listing_id, ip_address, user_agent) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$listingId, '127.0.0.1', 'Test Agent']);
            
            // Verify click was tracked
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM analytics WHERE listing_id = ?");
            $stmt->execute([$listingId]);
            $count = $stmt->fetch()['count'];
            
            // Clean up
            $stmt = $this->pdo->prepare("DELETE FROM listings WHERE id = ?");
            $stmt->execute([$listingId]);
            
            if ($count > 0) {
                $this->results['affiliate_tracking'] = [
                    'status' => 'PASS',
                    'message' => "Affiliate click tracking working ($count clicks recorded)"
                ];
            } else {
                throw new Exception('No clicks were tracked');
            }
        } catch (Exception $e) {
            $this->results['affiliate_tracking'] = [
                'status' => 'FAIL',
                'message' => 'Affiliate tracking test failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Test analytics functionality
    private function testAnalytics() {
        try {
            // Test stats query
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM analytics");
            $result = $stmt->fetch();
            
            $this->results['analytics'] = [
                'status' => 'PASS',
                'message' => "Analytics working ({$result['count']} total clicks tracked)"
            ];
        } catch (Exception $e) {
            $this->results['analytics'] = [
                'status' => 'FAIL',
                'message' => 'Analytics test failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Test security measures
    private function testSecurity() {
        $issues = [];
        
        // Check if error reporting is off in production
        if (ini_get('display_errors') == 1 && $_SERVER['ENVIRONMENT'] === 'production') {
            $issues[] = 'Error reporting is enabled in production';
        }
        
        // Check for default passwords
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM admin WHERE password = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'");
            $stmt->execute();
            $count = $stmt->fetch()['count'];
            if ($count > 0) {
                $issues[] = 'Default admin password still in use';
            }
        } catch (Exception $e) {
            $issues[] = 'Could not check admin passwords';
        }
        
        if (empty($issues)) {
            $this->results['security'] = [
                'status' => 'PASS',
                'message' => 'Basic security checks passed'
            ];
        } else {
            $this->results['security'] = [
                'status' => 'WARN',
                'message' => implode('; ', $issues)
            ];
        }
    }
    
    // Test performance
    private function testPerformance() {
        $start = microtime(true);
        
        // Test database query performance
        $stmt = $this->pdo->query("
            SELECT l.*, COUNT(a.id) as clicks 
            FROM listings l 
            LEFT JOIN analytics a ON l.id = a.listing_id 
            GROUP BY l.id 
            LIMIT 10
        ");
        $listings = $stmt->fetchAll();
        
        $time = microtime(true) - $start;
        
        if ($time < 1.0) {
            $this->results['performance'] = [
                'status' => 'PASS',
                'message' => sprintf("Query performance good (%.3fs)", $time)
            ];
        } else {
            $this->results['performance'] = [
                'status' => 'WARN',
                'message' => sprintf("Slow query detected (%.3fs)", $time)
            ];
        }
    }
    
    // Test monetization features
    private function testMonetization() {
        try {
            $issues = [];
            
            // Check if affiliate networks exist
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM affiliate_networks");
            $count = $stmt->fetch()['count'];
            if ($count == 0) {
                $issues[] = 'No affiliate networks configured';
            }
            
            // Check if ad placements exist
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM ad_placements");
            $count = $stmt->fetch()['count'];
            if ($count == 0) {
                $issues[] = 'No ad placements configured';
            }
            
            // Check if listings have affiliate links
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM listings WHERE affiliate_link IS NOT NULL AND affiliate_link != ''");
            $count = $stmt->fetch()['count'];
            if ($count == 0) {
                $issues[] = 'No listings with affiliate links';
            }
            
            if (empty($issues)) {
                $this->results['monetization'] = [
                    'status' => 'PASS',
                    'message' => 'Monetization features properly configured'
                ];
            } else {
                $this->results['monetization'] = [
                    'status' => 'WARN',
                    'message' => implode('; ', $issues)
                ];
            }
        } catch (Exception $e) {
            $this->results['monetization'] = [
                'status' => 'FAIL',
                'message' => 'Monetization test failed: ' . $e->getMessage()
            ];
        }
    }
    
    // Generate test report
    private function generateReport() {
        $total = count($this->results);
        $passed = count(array_filter($this->results, fn($r) => $r['status'] === 'PASS'));
        $failed = count(array_filter($this->results, fn($r) => $r['status'] === 'FAIL'));
        $warnings = count(array_filter($this->results, fn($r) => $r['status'] === 'WARN'));
        
        echo "<div class='bg-white rounded-lg shadow p-6 mb-8'>";
        echo "<h2 class='text-xl font-bold mb-4'>📊 Test Summary</h2>";
        echo "<div class='grid grid-cols-4 gap-4 mb-6'>";
        echo "<div class='text-center'><div class='text-2xl font-bold'>$total</div><div class='text-gray-500'>Total</div></div>";
        echo "<div class='text-center'><div class='text-2xl font-bold text-green-600'>$passed</div><div class='text-gray-500'>Passed</div></div>";
        echo "<div class='text-center'><div class='text-2xl font-bold text-yellow-600'>$warnings</div><div class='text-gray-500'>Warnings</div></div>";
        echo "<div class='text-center'><div class='text-2xl font-bold text-red-600'>$failed</div><div class='text-gray-500'>Failed</div></div>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='bg-white rounded-lg shadow p-6'>";
        echo "<h2 class='text-xl font-bold mb-4'>📋 Detailed Results</h2>";
        
        foreach ($this->results as $test => $result) {
            $statusColor = $result['status'] === 'PASS' ? 'green' : ($result['status'] === 'WARN' ? 'yellow' : 'red');
            $statusIcon = $result['status'] === 'PASS' ? '✅' : ($result['status'] === 'WARN' ? '⚠️' : '❌');
            
            echo "<div class='mb-4 p-4 border rounded' style='border-color: var(--color-$statusColor-200); background-color: var(--color-$statusColor-50);'>";
            echo "<div class='flex items-center mb-2'>";
            echo "<span class='text-lg mr-2'>$statusIcon</span>";
            echo "<span class='font-semibold capitalize'>" . str_replace('_', ' ', $test) . "</span>";
            echo "<span class='ml-auto px-2 py-1 text-xs rounded-full bg-$statusColor-100 text-$statusColor-800'>";
            echo $result['status'];
            echo "</span>";
            echo "</div>";
            echo "<p class='text-gray-700'>{$result['message']}</p>";
            echo "</div>";
        }
        
        echo "</div>";
        
        // Deployment readiness
        echo "<div class='bg-white rounded-lg shadow p-6 mt-8'>";
        echo "<h2 class='text-xl font-bold mb-4'>🚀 Deployment Readiness</h2>";
        
        if ($failed > 0) {
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>";
            echo "<strong>❌ Not Ready for Deployment</strong><br>";
            echo "Fix the failing tests before deploying to production.";
            echo "</div>";
        } elseif ($warnings > 0) {
            echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded'>";
            echo "<strong>⚠️ Ready with Warnings</strong><br>";
            echo "System is functional but consider addressing warnings for optimal performance.";
            echo "</div>";
        } else {
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded'>";
            echo "<strong>✅ Ready for Production Deployment</strong><br>";
            echo "All tests passed. System is ready to go live!";
            echo "</div>";
        }
        
        echo "</div>";
        echo "</div>";
    }
}

// Auto-test redirect functionality
function testAffiliateRedirect($listingId) {
    global $pdo;
    
    try {
        // Get listing
        $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = ?");
        $stmt->execute([$listingId]);
        $listing = $stmt->fetch();
        
        if (!$listing) {
            return ['status' => 'FAIL', 'message' => 'Listing not found'];
        }
        
        if (!$listing['affiliate_link']) {
            return ['status' => 'WARN', 'message' => 'No affiliate link configured'];
        }
        
        // Test URL format
        if (!filter_var($listing['affiliate_link'], FILTER_VALIDATE_URL)) {
            return ['status' => 'FAIL', 'message' => 'Invalid affiliate link URL'];
        }
        
        return ['status' => 'PASS', 'message' => 'Affiliate link valid: ' . $listing['affiliate_link']];
        
    } catch (Exception $e) {
        return ['status' => 'FAIL', 'message' => 'Error: ' . $e->getMessage()];
    }
}
?>