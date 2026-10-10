-- ============================================================================
-- SQL SCHEMA FOR WEB PROGRAMMING ASSIGNMENT
-- Database: myapp
-- Table: users
-- ============================================================================
-- 
-- Instructions for XAMPP / phpMyAdmin:
-- 1. Open phpMyAdmin at http://localhost/phpmyadmin
-- 2. Click the 'SQL' tab or 'Import' tab
-- 3. Paste this script or upload this file, then click 'Go'
-- ============================================================================

-- Step 1: Create the Database `myapp`
CREATE DATABASE IF NOT EXISTS `myapp` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Step 2: Use the `myapp` Database
USE `myapp`;

-- Step 3: Create the `users` Table
-- Schema required by assignment:
-- id: INT AUTO_INCREMENT PRIMARY KEY
-- name: VARCHAR(255)
-- age: INT
-- city: VARCHAR(255)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `age` INT DEFAULT NULL,
    `city` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Insert starter sample records (Optional)
INSERT INTO `users` (`name`, `age`, `city`) VALUES
('Sara', 22, 'Dhaka'),
('Alex', 25, 'Chittagong'),
('Fahim', 24, 'Sylhet');

-- ============================================================================
-- (Optional) Legacy schema for Small Steps To-Do app (small_steps_db)
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `small_steps_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `small_steps_db`;
CREATE TABLE IF NOT EXISTS `todos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `text` VARCHAR(255) NOT NULL,
    `completed` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
