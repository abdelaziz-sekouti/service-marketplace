-- Listings table structure for Service Marketplace
-- This table stores all the tools and services available on the platform

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

-- Insert some sample listings for testing
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