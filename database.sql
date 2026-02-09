-- ========================================
-- Database: user_credentials_db
-- Description: User credentials management system
-- For use with phpMyAdmin/MySQL
-- ========================================

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS user_credentials_db;
-- USE user_credentials_db;

-- ========================================
-- Table: user_credentials
-- Stores user login credentials and account information
-- ========================================

CREATE TABLE IF NOT EXISTS `user_credentials` (
  `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  `username` VARCHAR(255) UNIQUE NOT NULL,
  `password_hash` VARCHAR(64) NOT NULL COMMENT 'SHA-256 hashed password',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  `account_type` VARCHAR(50) DEFAULT 'personal' COMMENT 'Account type: personal or business',
  INDEX `idx_username` (`username`),
  INDEX `idx_account_type` (`account_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Sample Data (Optional - Remove if not needed)
-- ========================================

-- Insert sample personal account
-- Password: demo123 (hashed with SHA-256)
INSERT INTO `user_credentials` (`username`, `password_hash`, `account_type`)
VALUES
  ('demo_user', '6ca13d52ca70c883e0f0bb101e425a89e8624de51db2d2392593af6a84118090', 'personal')
ON DUPLICATE KEY UPDATE `username` = `username`;

-- Insert sample business account
-- Password: business123 (hashed with SHA-256)
INSERT INTO `user_credentials` (`username`, `password_hash`, `account_type`)
VALUES
  ('COMP001:USER001', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'business')
ON DUPLICATE KEY UPDATE `username` = `username`;

-- ========================================
-- Notes:
-- ========================================
-- 1. Import this file into phpMyAdmin to create the table structure
-- 2. Update the database connection settings in db-config.php
-- 3. Make sure your MySQL user has appropriate permissions
-- 4. For production, remove the sample data inserts
-- ========================================
