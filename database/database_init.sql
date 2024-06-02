-- Create the database
CREATE DATABASE IF NOT EXISTS `web-programming`;

-- Use the database
USE `web-programming`;

-- Table for storing user information
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255),
    `login_timestamp` DATETIME NOT NULL,
    `screen_resolution` VARCHAR(20) NOT NULL,
    `operating_system` VARCHAR(20) NOT NULL,
    `points_awarded` INT NOT NULL DEFAULT 100,
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
CREATE TABLE IF NOT EXISTS `shopping_cart` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `product_id` INT,
    `quantity` INT,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- Table for storing orders
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `total_amount` DECIMAL(10, 2),
    `shipping_method` VARCHAR(50),
    `is_express_shipping` BOOLEAN,
    `is_paid` BOOLEAN,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
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
