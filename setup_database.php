<?php
echo "=== Lifestyle Risk Portal Database Setup ===\n\n";

// Database configuration
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'lifestyle_portal';

try {
    // Connect to MySQL
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);

    if ($mysqli->connect_errno) {
        throw new Exception('MySQL connect error: ' . $mysqli->connect_error);
    }

    echo "✓ Connected to MySQL successfully\n";

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS `$DB_NAME` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($mysqli->query($sql)) {
        echo "✓ Database '$DB_NAME' created/verified\n";
    } else {
        throw new Exception('Error creating database: ' . $mysqli->error);
    }

    // Select database
    $mysqli->select_db($DB_NAME);
    echo "✓ Selected database '$DB_NAME'\n";

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(120) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB";

    if ($mysqli->query($sql)) {
        echo "✓ Users table created/verified\n";
    } else {
        throw new Exception('Error creating users table: ' . $mysqli->error);
    }

    // Create assessments table
    $sql = "CREATE TABLE IF NOT EXISTS `assessments` (
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
    ) ENGINE=InnoDB";

    if ($mysqli->query($sql)) {
        echo "✓ Assessments table created/verified\n";
    } else {
        throw new Exception('Error creating assessments table: ' . $mysqli->error);
    }

    // Create sample user for testing
    $name = 'Test User';
    $email = 'test@example.com';
    $password = password_hash('password123', PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare('INSERT IGNORE INTO users (name, email, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $email, $password);

    if ($stmt->execute()) {
        echo "✓ Sample user created (test@example.com / password123)\n";
    }

    echo "\n=== Setup Complete! ===\n";
    echo "You can now:\n";
    echo "1. Access the portal at: http://localhost/lifestyle-risk-portal-full/\n";
    echo "2. Login with: test@example.com / password123\n";
    echo "3. Or register a new account\n";
    echo "4. Start creating health assessments\n\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Please make sure:\n";
    echo "1. MySQL is running\n";
    echo "2. You have the correct database credentials\n";
    echo "3. The MySQL user has CREATE privileges\n";
}

$mysqli->close();
?>
