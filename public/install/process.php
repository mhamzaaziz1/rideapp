<?php
// public/install/process.php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/installer_error.log');
set_time_limit(300); // 5 minutes for migrations

header('Content-Type: application/json');

function sendJsonAndExit($data) {
    $buffer = ob_get_clean();
    if (!empty(trim($buffer))) {
        error_log("Discarded Output Buffer: " . $buffer);
    }
    
    // Send standard headers
    header('Content-Type: application/json');
    $json = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    
    if ($json === false) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Failed to encode JSON response: ' . json_last_error_msg()
        ]);
    } else {
        echo $json;
    }
    exit;
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Only handle fatal-like or catchable errors, allow others to be buffered/discarded.
    if (!(error_reporting() & $errno)) return;
    sendJsonAndExit(['status' => 'error', 'message' => "PHP Error: $errstr in $errfile:$errline"]);
});

set_exception_handler(function($e) {
    sendJsonAndExit(['status' => 'error', 'message' => "Exception: " . $e->getMessage()]);
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        sendJsonAndExit(['status' => 'error', 'message' => "Fatal Error: {$error['message']} in {$error['file']}:{$error['line']}"]);
    }
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonAndExit(['status' => 'error', 'message' => 'Invalid request method.']);
}

if (file_exists(__DIR__ . '/.installed') || file_exists(__DIR__ . '/../../.env')) {
    sendJsonAndExit(['status' => 'error', 'message' => 'Application is already installed.']);
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
    sendJsonAndExit(['status' => 'error', 'message' => 'Database Connection Failed: ' . $mysqli->connect_error]);
}

// Create database if it doesn't exist (to handle fresh setup)
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
if (!$mysqli->select_db($db_name)) {
    sendJsonAndExit(['status' => 'error', 'message' => 'Failed to select database: ' . $mysqli->error]);
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
    sendJsonAndExit(['status' => 'error', 'message' => 'The env template file is missing from the root directory.']);
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
    sendJsonAndExit(['status' => 'error', 'message' => 'Failed to write .env file. Check directory permissions.']);
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
    sendJsonAndExit(['status' => 'error', 'message' => 'Failed to create users table: ' . $mysqli->error]);
}

$email = 'admin@rideflow.app';
$check = $mysqli->query("SELECT id FROM users WHERE email = '$email'");
if ($check->num_rows == 0) {
    $passHash = password_hash($admin_password, PASSWORD_DEFAULT);
    $insert = "INSERT INTO users (email, password_hash, first_name, last_name, status) 
               VALUES ('$email', '$passHash', 'System', 'Admin', 'active')";
    if (!$mysqli->query($insert)) {
        sendJsonAndExit(['status' => 'error', 'message' => 'Failed to seed admin user: ' . $mysqli->error]);
    }
}

// 4. Run Migrations via Spark
$sparkPath = realpath(__DIR__ . '/../../spark');
$cwdPath = realpath(__DIR__ . '/../../');

// We use an extended execution time and pass the full path. We also suppress warnings.
$command = "cd " . escapeshellarg($cwdPath) . " && php spark migrate -all 2>&1";

$output = [];
$return_var = 0;
exec($command, $output, $return_var);

$migrationLog = implode("\n", $output);

// 5. Create Lock file
file_put_contents(__DIR__ . '/.installed', 'Installed on ' . date('Y-m-d H:i:s'));

sendJsonAndExit([
    'status' => 'success', 
    'message' => 'Database fully configured, admin seeded, and all migrations completed successfully.',
    'migration_log' => $migrationLog
]);

