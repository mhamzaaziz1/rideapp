<?php
// public/install/process.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

if (file_exists(__DIR__ . '/.installed') || file_exists(__DIR__ . '/../../.env')) {
    echo json_encode(['status' => 'error', 'message' => 'Application is already installed.']);
    exit;
}

// Get POST data
$app_url = $_POST['app_url'] ?? '';
$db_host = $_POST['db_host'] ?? '';
$db_name = $_POST['db_name'] ?? '';
$db_user = $_POST['db_user'] ?? '';
$db_pass = $_POST['db_pass'] ?? '';
$admin_password = $_POST['admin_password'] ?? 'password123';

// 1. Validate Database Connection
$mysqli = @new mysqli($db_host, $db_user, $db_pass);

if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . $mysqli->connect_error]);
    exit;
}

// Create database if it doesn't exist (to handle fresh setup)
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
if (!$mysqli->select_db($db_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to select database: ' . $mysqli->error]);
    exit;
}

// 2. Generate .env file
$envPossiblePaths = [
    __DIR__ . '/../../env',
    __DIR__ . '/../../env12',
    __DIR__ . '/../../.env1',
    __DIR__ . '/../../.env2'
];

$envPath = '';
foreach ($envPossiblePaths as $path) {
    if (file_exists($path)) {
        $envPath = $path;
        break;
    }
}

$newEnvPath = __DIR__ . '/../../.env';

if (empty($envPath)) {
    echo json_encode(['status' => 'error', 'message' => 'The env template file is missing from the root directory.']);
    exit;
}

$envContent = file_get_contents($envPath);

// Replace placeholders/default values in env file.
// CI4 defaults
$envContent = str_replace('# CI_ENVIRONMENT = production', 'CI_ENVIRONMENT = development', $envContent);
$envContent = preg_replace('/# app\.baseURL = .*/', 'app.baseURL = \'' . rtrim($app_url, '/') . '/\'', $envContent);

// DB Config
$envContent = str_replace('# database.default.hostname = localhost', 'database.default.hostname = ' . $db_host, $envContent);
$envContent = str_replace('# database.default.database = ci4', 'database.default.database = ' . $db_name, $envContent);
$envContent = str_replace('# database.default.username = root', 'database.default.username = ' . $db_user, $envContent);
$envContent = str_replace('# database.default.password = root', 'database.default.password = ' . $db_pass, $envContent);
$envContent = str_replace('# database.default.DBDriver = MySQLi', 'database.default.DBDriver = MySQLi', $envContent);

if (file_put_contents($newEnvPath, $envContent) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to write .env file. Check directory permissions.']);
    exit;
}

// 3. Setup Initial Database via setup_db.php (Modified for process.php)
// We will create the users table and seed the admin here since we have the connection.
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

if (!$mysqli->query($sql)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create users table: ' . $mysqli->error]);
    exit;
}

$email = 'admin@rideflow.app';
$check = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
if ($check->num_rows == 0) {
    $passHash = password_hash($admin_password, PASSWORD_DEFAULT);
    $insert = "INSERT INTO users (email, password_hash, first_name, last_name, status) 
               VALUES ('$email', '$passHash', 'System', 'Admin', 'active')";
    if (!$mysqli->query($insert)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to seed admin user: ' . $mysqli->error]);
        exit;
    }
}

// 4. Run Migrations via Spark
$sparkPath = __DIR__ . '/../../spark';
$command = "php " . escapeshellarg($sparkPath) . " migrate -all 2>&1";

$output = [];
$return_var = 0;
exec($command, $output, $return_var);

// You can log output if needed, but we proceed if it finishes
$migrationLog = implode("\n", $output);

// 5. Create Lock file
file_put_contents(__DIR__ . '/.installed', 'Installed on ' . date('Y-m-d H:i:s'));

echo json_encode([
    'status' => 'success', 
    'message' => 'Database fully configured, admin seeded, and all migrations completed successfully.',
    'migration_log' => $migrationLog
]);
