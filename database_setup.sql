-- Smart Door Lock System Database Setup
-- Run this file in your MySQL/phpMyAdmin to create the database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS door_lock_system;
USE door_lock_system;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) UNIQUE NOT NULL COMMENT 'RFID Card UID',
    name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    email VARCHAR(100) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create door_access_log table for history
CREATE TABLE IF NOT EXISTS door_access_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) NOT NULL,
    user_name VARCHAR(100),
    access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    access_type ENUM('Granted', 'Denied') NOT NULL,
    door_status VARCHAR(50) DEFAULT 'Closed',
    FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
);

-- Create system_config table for WiFi credentials
CREATE TABLE IF NOT EXISTS system_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wifi_name VARCHAR(100) NOT NULL,
    wifi_password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data for testing (ignore duplicates)
INSERT IGNORE INTO users (uid, name, gender, email, mobile) VALUES
('1234567890', 'John Doe', 'Male', 'john@example.com', '+1234567890'),
('0987654321', 'Jane Smith', 'Female', 'jane@example.com', '+0987654321');

-- Insert sample access logs (ignore duplicates)
INSERT IGNORE INTO door_access_log (uid, user_name, access_type, door_status) VALUES
('1234567890', 'John Doe', 'Granted', 'Opened'),
('0987654321', 'Jane Smith', 'Granted', 'Opened');
