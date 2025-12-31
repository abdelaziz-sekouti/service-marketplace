-- Complete Database Schema for Service Marketplace
-- Run this script to create all necessary tables

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- User addresses table
CREATE TABLE IF NOT EXISTS user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_type ENUM('billing', 'shipping') NOT NULL,
    street_address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(50) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(50) DEFAULT 'United States',
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Listings table (tools and services)
CREATE TABLE IF NOT EXISTS listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) DEFAULT 0,
    image VARCHAR(500),
    affiliate_link VARCHAR(500),
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    priority INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for better performance
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_featured (featured),
    INDEX idx_created (created_at),
    INDEX idx_status_created (status, created_at)
);

-- Analytics table for tracking clicks and interactions
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
    
    -- Indexes for performance
    INDEX idx_listing_id (listing_id),
    INDEX idx_clicked_at (clicked_at),
    INDEX idx_ip_address (ip_address),
    INDEX idx_listing_clicked (listing_id, clicked_at)
);

-- Shopping cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, -- NULL for guest users
    session_id VARCHAR(255) NULL, -- For guest users
    listing_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price_at_add DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, listing_id),
    UNIQUE KEY unique_guest_cart (session_id, listing_id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NULL, -- NULL for guest orders
    email VARCHAR(100) NOT NULL, -- Required for guest orders
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method ENUM('credit_card', 'paypal', 'stripe') NULL,
    billing_address JSON,
    shipping_address JSON,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    listing_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    listing_data JSON, -- Store listing snapshot
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
);

-- Affiliate networks table
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

-- Ad placements table
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

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Revenue tracking table
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

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'Freelance Money Maker', 'Website name'),
('site_description', 'Discover the best tools and services to maximize your freelance income', 'Site description'),
('admin_email', 'admin@freelancemoneymaker.com', 'Admin contact email'),
('currency', 'USD', 'Default currency'),
('tax_rate', '0.08', 'Sales tax rate (as decimal)'),
('shipping_cost', '0.00', 'Default shipping cost'),
('guest_checkout', 'enabled', 'Allow guest checkout'),
('cart_expiry_hours', '24', 'Hours before cart expires for guests');

-- Create a default admin user (password: admin123)
INSERT INTO admin_users (username, email, password_hash, full_name) VALUES
('admin', 'admin@freelancemoneymaker.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert sample listings for testing
INSERT INTO listings (title, description, category, price, affiliate_link, featured, priority) VALUES
('Upwork Pro Membership', 'Get access to premium features and better visibility on Upwork platform. Connect with high-quality clients and increase your earning potential.', 'Freelance Platforms', 14.99, 'https://www.upwork.com/plus', TRUE, 10),
('Fiverr Pro', 'Stand out from the competition with Pro features. Get verified badge, priority support, and access to premium buyer requests.', 'Freelance Platforms', 19.99, 'https://www.fiverr.com/pro', TRUE, 9),
('Adobe Creative Cloud', 'Complete suite of creative tools including Photoshop, Illustrator, Premiere Pro, and more. Essential for designers and content creators.', 'Design Tools', 52.99, 'https://www.adobe.com/creativecloud', TRUE, 8),
('Canva Pro', 'Professional design tool with templates, brand kit, and collaboration features. Perfect for creating social media graphics and marketing materials.', 'Design Tools', 12.99, 'https://www.canva.com/pro', TRUE, 7),
('Grammarly Premium', 'AI-powered writing assistant that checks grammar, spelling, and style. Essential for freelance writers and content creators.', 'Productivity', 29.99, 'https://www.grammarly.com/premium', TRUE, 6),
('FreshBooks', 'Accounting software designed for freelancers. Track time, send invoices, and manage expenses all in one place.', 'Accounting', 15.00, 'https://www.freshbooks.com', FALSE, 5),
('LastPass', 'Secure password manager for all your accounts. Generate strong passwords and store them safely with military-grade encryption.', 'Security', 3.00, 'https://www.lastpass.com', FALSE, 4),
('Zoom Pro', 'Professional video conferencing with unlimited meeting time and advanced features. Essential for client meetings and collaborations.', 'Communication', 14.99, 'https://zoom.us/pro', FALSE, 3),
('Slack Business', 'Team communication platform with advanced features. Improve collaboration with clients and team members.', 'Communication', 8.75, 'https://slack.com/pricing', FALSE, 2),
('Trello Business', 'Project management tool to organize tasks and collaborate with clients. Visual boards help track progress and deadlines.', 'Project Management', 10.00, 'https://trello.com/business', FALSE, 1);

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

-- Add indexes for better performance
CREATE INDEX idx_analytics_listing_id ON analytics(listing_id);
CREATE INDEX idx_cart_user ON cart(user_id);
CREATE INDEX idx_cart_session ON cart(session_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created ON orders(created_at);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_listings_status_created ON listings(status, created_at);