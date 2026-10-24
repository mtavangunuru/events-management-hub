-- ========================================================
-- Nuustar Events - Complete MySQL Database Schema & Seed Data
-- Database Name: nuustar_events_db
-- ========================================================

CREATE DATABASE IF NOT EXISTS `nuustar_events_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nuustar_events_db`;

-- --------------------------------------------------------
-- 1. Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('customer', 'vendor', 'admin') NOT NULL DEFAULT 'customer',
  `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 2. Table: categories
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icon` VARCHAR(50) NOT NULL DEFAULT 'fa-layer-group',
  `vendor_count` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 3. Table: vendor_profiles
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `vendor_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `business_name` VARCHAR(200) NOT NULL,
  `category_id` INT NOT NULL,
  `region` VARCHAR(100) NOT NULL DEFAULT 'Dar es Salaam',
  `district` VARCHAR(100) NOT NULL DEFAULT 'Kinondoni',
  `bio` TEXT DEFAULT NULL,
  `whatsapp_number` VARCHAR(50) NOT NULL,
  `instagram_link` VARCHAR(255) DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `starting_price` DECIMAL(12,2) NOT NULL DEFAULT 500000.00,
  `rating` DECIMAL(2,1) DEFAULT 5.0,
  `reviews_count` INT DEFAULT 0,
  `verification_status` ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
  `commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
  `payout_method` VARCHAR(50) DEFAULT 'M-Pesa',
  `payout_account_name` VARCHAR(150) DEFAULT NULL,
  `payout_account_number` VARCHAR(100) DEFAULT NULL,
  `payout_bank_name` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 4. Table: packages (Vendor Service Tiers)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `packages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vendor_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `price_tzs` DECIMAL(12,2) NOT NULL,
  `guest_capacity` VARCHAR(100) DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `features` TEXT NOT NULL, -- JSON or bullet separated features
  FOREIGN KEY (`vendor_id`) REFERENCES `vendor_profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 5. Table: event_requests (Customer RFP Board)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `event_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `services_needed` VARCHAR(255) NOT NULL,
  `event_date` DATE NOT NULL,
  `location` VARCHAR(150) NOT NULL,
  `guest_count` INT NOT NULL DEFAULT 100,
  `budget_tzs` VARCHAR(100) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `status` ENUM('open', 'matched', 'closed') DEFAULT 'open',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 6. Table: inquiries (Direct Bookings to Specific Vendors)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vendor_id` INT NOT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(50) NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `event_date` DATE NOT NULL,
  `guest_count` INT DEFAULT 100,
  `notes` TEXT DEFAULT NULL,
  `status` ENUM('pending', 'contacted', 'completed') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vendor_id`) REFERENCES `vendor_profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 7. Table: bookings (Paid Bookings & Commission Escrow)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_ref` VARCHAR(50) NOT NULL UNIQUE,
  `customer_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(50) NOT NULL,
  `vendor_id` INT NOT NULL,
  `package_id` INT DEFAULT NULL,
  `package_title` VARCHAR(150) NOT NULL,
  `event_date` DATE NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `guest_count` INT DEFAULT 100,
  `notes` TEXT DEFAULT NULL,
  `total_amount` DECIMAL(12,2) NOT NULL,
  `commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
  `commission_amount` DECIMAL(12,2) NOT NULL,
  `vendor_payout` DECIMAL(12,2) NOT NULL,
  `payment_method` VARCHAR(100) DEFAULT 'Mobile Money (M-Pesa / Tigo Pesa / Airtel Money)',
  `payment_reference` VARCHAR(100) DEFAULT NULL,
  `payment_status` ENUM('unpaid', 'paid', 'refunded') DEFAULT 'paid',
  `payout_status` ENUM('pending', 'paid') DEFAULT 'pending',
  `payout_date` DATETIME DEFAULT NULL,
  `payout_reference` VARCHAR(100) DEFAULT NULL,
  `booking_status` ENUM('pending_confirmation', 'confirmed', 'completed', 'cancelled') DEFAULT 'confirmed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vendor_id`) REFERENCES `vendor_profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 8. Table: notifications (Customer & Vendor In-App Alerts)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(50) DEFAULT 'payment',
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- INITIAL SEED DATA (Ready-to-use Sample Information)
-- ========================================================

-- Insert Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `vendor_count`) VALUES
(1, 'Venues & Gardens', 'venues', 'fa-building-columns', 45),
(2, 'Decor & Floral Styling', 'decor', 'fa-wand-magic-sparkles', 60),
(3, 'Photography & Video', 'photo', 'fa-camera', 80),
(4, 'Master of Ceremonies (MC)', 'mc', 'fa-microphone-lines', 35),
(5, 'Catering & Wedding Cakes', 'catering', 'fa-cake-candles', 50),
(6, 'DJs & Sound Systems', 'music', 'fa-compact-disc', 40),
(7, 'Bridal Transport & Convoys', 'transport', 'fa-car-side', 20)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Insert Initial Users (Passwords are set to: password123)
-- Hash using password_hash('password123', PASSWORD_BCRYPT)
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `status`) VALUES
(1, 'Super Admin', 'admin@nuustar.com', '+255624947668', '$2y$10$IfLPFbZLgKFLHAmWYRKGRe6.SmSM7URSqdf9/DaPAhIr9cO8rprEq', 'admin', 'active'),
(2, 'Sarah Mwangi', 'sarah@elegancedecor.co.tz', '+255712000111', '$2y$10$IfLPFbZLgKFLHAmWYRKGRe6.SmSM7URSqdf9/DaPAhIr9cO8rprEq', 'vendor', 'active'),
(3, 'Godfrey K.', 'info@flashstudio255.com', '+255754000222', '$2y$10$IfLPFbZLgKFLHAmWYRKGRe6.SmSM7URSqdf9/DaPAhIr9cO8rprEq', 'vendor', 'active'),
(4, 'Anthony M.', 'anthony@mcofficial.co.tz', '+255768000333', '$2y$10$IfLPFbZLgKFLHAmWYRKGRe6.SmSM7URSqdf9/DaPAhIr9cO8rprEq', 'vendor', 'active')
ON DUPLICATE KEY UPDATE `email`=VALUES(`email`);

-- Insert Verified Vendor Profiles
INSERT INTO `vendor_profiles` (`id`, `user_id`, `business_name`, `category_id`, `region`, `district`, `bio`, `whatsapp_number`, `instagram_link`, `cover_image`, `starting_price`, `rating`, `reviews_count`, `verification_status`) VALUES
(1, 2, 'Elegance Decor Tanzania', 2, 'Dar es Salaam', 'Masaki', 'Modern venue styling, luxury VIP domes, and romantic ambient lighting setups for high-profile weddings and send-offs.', '+255712000111', 'https://instagram.com/nuustar_events', 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1200&q=80', 1500000.00, 4.9, 48, 'verified'),
(2, 3, 'Flash Studio 255', 3, 'Dar es Salaam', 'Kinondoni', 'Cinema-grade 4K wedding films, drone cinematography, and instant photo booths with high-end color grading.', '+255754000222', 'https://instagram.com/nuustar_events', 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?auto=format&fit=crop&w=600&q=80', 900000.00, 5.0, 62, 'verified'),
(3, 4, 'MC Anthony Official', 4, 'Dar es Salaam', 'Ilala', 'Charismatic event hosting, disciplined schedule management, and engaging entertainment tailored to your guests.', '+255768000333', 'https://instagram.com/nuustar_events', 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=600&q=80', 700000.00, 4.8, 31, 'verified')
ON DUPLICATE KEY UPDATE `business_name`=VALUES(`business_name`);

-- Insert Packages for Elegance Decor (Vendor ID 1)
INSERT INTO `packages` (`vendor_id`, `title`, `price_tzs`, `guest_capacity`, `is_featured`, `features`) VALUES
(1, 'Bronze Elegance Package', 1500000.00, '100 - 150 Guests', 0, 'Sweetheart stage setup with floral backdrop\n150 Banquet chair covers and premium satin sashes\nStandard guest table centerpieces and linens\nPerimeter mood up-lighting (8 LED units)'),
(1, 'Silver Grandeur Package', 2800000.00, '200 - 350 Guests', 1, 'Bespoke acrylic and fresh floral stage backdrop\nChiavari chairs (Gold, Silver, or Clear Ghost chairs)\nCrystal candelabras and 100% fresh floral centerpieces\nMirrored walkway with illuminated grand entrance arch\nIntelligent dynamic lighting & wash effects'),
(1, 'Gold VIP Royal Transformation', 4500000.00, '500+ Guests', 0, 'Complete ceiling draping with starry fairy light canopy\nMulti-tiered royal bridal stage with custom furniture\nPremium imported fresh flower installations\nCustom 3D photo wall and red-carpet welcome zone\n2 Dedicated on-site coordinators throughout the celebration');

-- Insert Sample Open Event Request
INSERT INTO `event_requests` (`customer_name`, `phone`, `event_type`, `services_needed`, `event_date`, `location`, `guest_count`, `budget_tzs`, `details`, `status`) VALUES
('Sarah Mwangi', '+255715999888', 'Send-off Celebration', 'Decor, Photo, MC', '2026-12-15', 'Mlimani City, Dar es Salaam', 250, '3,500,000 TZS', 'Looking for an elegant navy and gold decor theme with fresh floral arrangements and 4K photo coverage.', 'open');
