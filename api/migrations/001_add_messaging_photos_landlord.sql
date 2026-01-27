-- Migration: Add messaging, photos, and landlord support
-- Run this SQL against your ark_apartment_db database

-- Add landlord_id to apartments table
ALTER TABLE apartments 
ADD COLUMN IF NOT EXISTS landlord_id INT NULL,
ADD COLUMN IF NOT EXISTS location VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS bedrooms INT DEFAULT 1,
ADD COLUMN IF NOT EXISTS bathrooms INT DEFAULT 1,
ADD COLUMN IF NOT EXISTS date_created DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Add foreign key for landlord (user_type = 3)
-- ALTER TABLE apartments ADD FOREIGN KEY (landlord_id) REFERENCES users(id) ON DELETE SET NULL;

-- Create messages table for tenant-landlord communication
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    apartment_id INT NULL,
    message TEXT NOT NULL,
    is_read TINYINT DEFAULT 0,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_apartment (apartment_id),
    INDEX idx_conversation (sender_id, receiver_id),
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (apartment_id) REFERENCES apartments(id) ON DELETE SET NULL
);

-- Create apartment_photos table for property images
CREATE TABLE IF NOT EXISTS apartment_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apartment_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    is_primary TINYINT DEFAULT 0,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_apartment (apartment_id),
    FOREIGN KEY (apartment_id) REFERENCES apartments(id) ON DELETE CASCADE
);

-- Create uploads directory structure info (run in PHP or manually create)
-- Directory: api/uploads/apartments/

-- Sample data: Create a landlord user for testing
INSERT INTO users (firstname, lastname, email, password, contact, user_type) 
VALUES ('John', 'Landlord', 'landlord@arkapartments.mw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+265 888 123 456', '3')
ON DUPLICATE KEY UPDATE firstname = firstname;

-- Update existing apartments to have the landlord
-- UPDATE apartments SET landlord_id = (SELECT id FROM users WHERE user_type = '3' LIMIT 1) WHERE landlord_id IS NULL;

-- Verify tables created
SELECT 'Migration completed successfully!' as status;
