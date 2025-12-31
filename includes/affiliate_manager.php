<?php
require_once '../db_connect.php';

class AffiliateManager {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    // Track affiliate click with enhanced analytics
    public function trackClick($listingId, $networkId = null, $placementId = null) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        
        try {
            // Get listing details
            $stmt = $this->pdo->prepare("SELECT * FROM listings WHERE id = ?");
            $stmt->execute([$listingId]);
            $listing = $stmt->fetch();
            
            if (!$listing) {
                return ['error' => 'Listing not found'];
            }
            
            // Track the click
            $stmt = $this->pdo->prepare("
                INSERT INTO analytics (listing_id, network_id, placement_id, ip_address, user_agent, referrer) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$listingId, $networkId, $placementId, $ipAddress, $userAgent, $referrer]);
            $clickId = $this->pdo->lastInsertId();
            
            // Get affiliate link
            $affiliateLink = $this->buildAffiliateLink($listing, $networkId);
            
            return [
                'success' => true,
                'click_id' => $clickId,
                'affiliate_link' => $affiliateLink,
                'listing' => $listing
            ];
            
        } catch (Exception $e) {
            return ['error' => 'Failed to track click: ' . $e->getMessage()];
        }
    }
    
    // Build affiliate link with tracking parameters
    private function buildAffiliateLink($listing, $networkId) {
        $baseLink = $listing['affiliate_link'];
        
        if ($networkId) {
            // Add network-specific tracking
            $stmt = $this->pdo->prepare("SELECT * FROM affiliate_networks WHERE id = ?");
            $stmt->execute([$networkId]);
            $network = $stmt->fetch();
            
            if ($network) {
                // Add tracking parameters
                $separator = strpos($baseLink, '?') !== false ? '&' : '?';
                $trackingParams = [
                    'tag' => 'freelancemoney-20',
                    'utm_source' => 'freelancemoneymaker',
                    'utm_medium' => 'affiliate',
                    'utm_campaign' => $listing['category'],
                    'click_id' => uniqid()
                ];
                
                $baseLink .= $separator . http_build_query($trackingParams);
            }
        }
        
        return $baseLink;
    }
    
    // Get active ad placements
    public function getAdPlacements($position = null) {
        try {
            $sql = "SELECT * FROM ad_placements WHERE status = 'active'";
            $params = [];
            
            if ($position) {
                $sql .= " AND position = ?";
                $params[] = $position;
            }
            
            $sql .= " ORDER BY priority DESC, created_at ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Record revenue
    public function recordRevenue($clickId, $amount, $type = 'cpc') {
        try {
            // Get click details
            $stmt = $this->pdo->prepare("SELECT * FROM analytics WHERE id = ?");
            $stmt->execute([$clickId]);
            $click = $stmt->fetch();
            
            if (!$click) {
                return false;
            }
            
            // Record revenue
            $stmt = $this->pdo->prepare("
                INSERT INTO revenue_tracking (listing_id, network_id, placement_id, click_id, revenue_amount, commission_type, date_recorded) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE())
            ");
            
            return $stmt->execute([
                $click['listing_id'],
                $click['network_id'],
                $click['placement_id'],
                $clickId,
                $amount,
                $type
            ]);
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Get revenue statistics
    public function getRevenueStats($dateRange = '30') {
        try {
            $sql = "
                SELECT 
                    DATE(date_recorded) as date,
                    SUM(revenue_amount) as daily_revenue,
                    COUNT(*) as conversions,
                    commission_type
                FROM revenue_tracking 
                WHERE date_recorded >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(date_recorded), commission_type
                ORDER BY date DESC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$dateRange]);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return [];
        }
    }
}

// Initialize affiliate manager
$affiliateManager = new AffiliateManager();
?>