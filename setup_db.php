<?php
// setup_db.php
// Manual database setup to bypass CI4 migration failures

header('Content-Type: text/plain');

// 1. Connection settings (hardcoded fallback + env parsing)
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP
$dbname = 'rideapp';

// Try to read .env
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, 'database.default.database') !== false) {
            $parts = explode('=', $line);
            $dbname = trim(end($parts));
        }
        // Add other parsers if needed, but defaults usually work for XAMPP
    }
}

echo "Connecting to MySQL ($host)...\n";
$mysqli = new mysqli($host, $user, $pass);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 2. Create DB
echo "Creating database '$dbname' if not exists...\n";
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$mysqli->select_db($dbname);

// 3. Create Users Table
echo "Creating 'users' table...\n";
$sql = "CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `status` ENUM('active', 'banned', 'pending') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($mysqli->query($sql)) {
    echo "Table 'users' checked/created.\n";
} else {
    echo "Error creating table: " . $mysqli->error . "\n";
}

// 4. Seed Admin
$email = 'admin@rideflow.app';
$check = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
if ($check->num_rows == 0) {
    echo "Seeding Admin User...\n";
    $passHash = password_hash('password123', PASSWORD_DEFAULT);
    $insert = "INSERT INTO users (email, password_hash, first_name, last_name, status) 
               VALUES ('$email', '$passHash', 'System', 'Admin', 'active')";
    if ($mysqli->query($insert)) {
        echo "Admin user created successfully.\n";
    } else {
        echo "Error creating admin: " . $mysqli->error . "\n";
    }
} else {
    echo "Admin user already exists.\n";
}

echo "\nDONE. You can now login.";
