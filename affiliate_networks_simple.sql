-- Create affiliate_networks table
CREATE TABLE IF NOT EXISTS affiliate_networks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    tracking_domain VARCHAR(255) NOT NULL,
    commission_rate DECIMAL(5,2) DEFAULT 0.00,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_name (name)
);

-- Insert sample affiliate networks
INSERT INTO affiliate_networks (name, tracking_domain, commission_rate, description) VALUES
('Amazon Associates', 'amazon.com', 4.00, 'Amazon affiliate program for physical and digital products'),
('ShareASale', 'shareasale.com', 5.50, 'Network connecting merchants with affiliates'),
('ClickBank', 'clickbank.com', 7.00, 'Digital marketplace with high commission rates'),
('Rakuten Advertising', 'rakutenmarketing.com', 6.00, 'Formerly LinkShare - major affiliate network'),
('Commission Junction', 'cj.com', 5.00, 'One of the largest affiliate marketing networks'),
('Impact Radius', 'impact.com', 6.50, 'Partnership automation platform for enterprise'),
('eBay Partner Network', 'ebay.com', 3.00, 'eBay affiliate program for marketplace sellers'),
('Shopify Affiliates', 'shopify.com', 58.00, '$200 per merchant referred to Shopify'),
('ClickFunnels Affiliate', 'clickfunnels.com', 40.00, '$40 recurring monthly per customer'),
('ConvertKit', 'convertkit.com', 30.00, 'Email marketing affiliate program');