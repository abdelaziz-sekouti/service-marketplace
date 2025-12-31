-- Analytics table for tracking clicks and user interactions
-- This table tracks all affiliate clicks and user interactions with listings

CREATE TABLE IF NOT EXISTS analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    network_id INT NULL,
    placement_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    referrer VARCHAR(500),
    clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key relationships
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (network_id) REFERENCES affiliate_networks(id) ON DELETE SET NULL,
    FOREIGN KEY (placement_id) REFERENCES ad_placements(id) ON DELETE SET NULL,
    
    -- Indexes for performance
    INDEX idx_listing_id (listing_id),
    INDEX idx_clicked_at (clicked_at),
    INDEX idx_ip_address (ip_address),
    INDEX idx_listing_clicked (listing_id, clicked_at)
);

-- Affiliate networks table for managing different affiliate programs
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

-- Ad placements table for managing advertisements
CREATE TABLE IF NOT EXISTS ad_placements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position ENUM('header', 'sidebar', 'between_content', 'footer') NOT NULL,
    dimensions VARCHAR(20) NOT NULL, -- e.g., '300x250', '728x90'
    ad_code TEXT,
    priority INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_position (position),
    INDEX idx_status (status),
    INDEX idx_priority (priority)
);

-- Revenue tracking table for affiliate commissions
CREATE TABLE IF NOT EXISTS revenue_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT NOT NULL,
    network_id INT NULL,
    placement_id INT NULL,
    click_id INT,
    revenue_amount DECIMAL(10,2) NOT NULL,
    commission_type ENUM('cpc', 'cpa', 'cps') DEFAULT 'cps', -- Cost Per Click, Action, or Sale
    date_recorded DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (network_id) REFERENCES affiliate_networks(id) ON DELETE SET NULL,
    FOREIGN KEY (placement_id) REFERENCES ad_placements(id) ON DELETE SET NULL,
    FOREIGN KEY (click_id) REFERENCES analytics(id) ON DELETE SET NULL,
    
    INDEX idx_listing_id (listing_id),
    INDEX idx_date_recorded (date_recorded),
    INDEX idx_commission_type (commission_type)
);

-- Insert sample affiliate networks
INSERT INTO affiliate_networks (name, tracking_domain, commission_rate, description) VALUES
('Amazon Associates', 'amazon.com', 4.00, 'Amazon affiliate program for physical and digital products'),
('ShareASale', 'shareasale.com', 5.50, 'Network connecting merchants with affiliates'),
('ClickBank', 'clickbank.com', 7.00, 'Digital marketplace with high commission rates'),
('Rakuten Advertising', 'rakutenmarketing.com', 6.00, 'Formerly LinkShare - major affiliate network');

-- Insert sample ad placements
INSERT INTO ad_placements (name, position, dimensions, priority, ad_code) VALUES
('Header Banner', 'header', '728x90', 10, '<div class="text-center p-4 bg-gray-200">Header Ad Space (728x90)</div>'),
('Sidebar Top', 'sidebar', '300x250', 8, '<div class="text-center p-4 bg-gray-200">Sidebar Ad (300x250)</div>'),
('Sidebar Bottom', 'sidebar', '300x250', 6, '<div class="text-center p-4 bg-gray-200">Sidebar Ad Bottom (300x250)</div>'),
('Between Content', 'between_content', '728x90', 9, '<div class="text-center p-4 bg-gray-200">Content Ad (728x90)</div>'),
('Footer Banner', 'footer', '728x90', 5, '<div class="text-center p-4 bg-gray-200">Footer Ad (728x90)</div>');