-- Create the database
CREATE DATABASE IF NOT EXISTS `web-programming`;

-- Use the database
USE `web-programming`;

-- Table for storing user information
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `surname` VARCHAR(255) NOT NULL,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `password_status` VARCHAR(20),
    `secret` VARCHAR(30),
    `login_timestamp` DATETIME,
    `screen_resolution` VARCHAR(255) NOT NULL,
    `operating_system` VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `users_id` INT NOT NULL,
    `event_type` VARCHAR(255) NOT NULL,
    `event_details` TEXT,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`users_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `points` (
    `users_id` INT NOT NULL,
    `points` INT NOT NULL DEFAULT 100,
    is_active BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`users_id`),
    FOREIGN KEY (`users_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Table for storing product information
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `quantity` INT NOT NULL,
    `image_url` VARCHAR(255),
    `description` TEXT
);

-- Table for storing shopping cart items
CREATE TABLE IF NOT EXISTS `cart-header` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `users_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`users_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `cart-body` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `warenkorb_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `rabatt` DECIMAL(5, 2),
    FOREIGN KEY (`warenkorb_id`) REFERENCES `cart-header`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- Table for storing orders
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `users_id` INT,
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `total_amount` DECIMAL(10, 2),
    `shipping_method` VARCHAR(50),
    `is_express_shipping` BOOLEAN,
    `is_paid` BOOLEAN,
    `is_returned` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (`users_id`) REFERENCES `users`(`id`)
);

-- Table for storing order items
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT,
    `product_id` INT,
    `quantity` INT,
    `unit_price` DECIMAL(10, 2),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- Table for storing coupon information
CREATE TABLE IF NOT EXISTS `coupons` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(255) NOT NULL,
    discount_percentage DECIMAL(5,2) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    expiration_date DATE
);
