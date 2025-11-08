
CREATE DATABASE IF NOT EXISTS `lifestyle_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lifestyle_portal`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `assessments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `age` INT,
  `height_cm` DECIMAL(6,2),
  `weight_kg` DECIMAL(6,2),
  `bmi` DECIMAL(6,2),
  `sleep_hours` DECIMAL(4,2),
  `water_liters` DECIMAL(4,2),
  `activity_mins` INT,
  `screen_hours` DECIMAL(4,2),
  `systolic_bp` INT,
  `diastolic_bp` INT,
  `sugar_fasting` DECIMAL(6,2),
  `smoking` ENUM('yes','no') DEFAULT 'no',
  `alcohol` ENUM('none','low','moderate','high') DEFAULT 'none',
  `stress_level` ENUM('low','moderate','high') DEFAULT 'moderate',
  `junk_freq` ENUM('never','rarely','sometimes','often') DEFAULT 'sometimes',
  `fruits_veggies_servings` INT DEFAULT 0,
  `risk_score` INT,
  `risk_level` ENUM('Low','Moderate','High'),
  `diseases` TEXT,
  `recommendations` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
