<?php
// public/api_login.php
// Standalone Login Handler to bypass CI4 missing Intl/Locale issues in broken environments.

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
// use Dotenv\Dotenv; // Removed to avoid dependency issues

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Manual .env parser for standalone usage
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

try {
    // 1. Load Environment
    loadEnv(__DIR__ . '/.env');

    // 2. Get Input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        throw new Exception("Email and password are required");
    }

    // 3. Connect DB
    $host = $_ENV['database.default.hostname'] ?? 'localhost';
    $user = $_ENV['database.default.username'] ?? 'root';
    $pass = $_ENV['database.default.password'] ?? '';
    $name = $_ENV['database.default.database'] ?? 'rideapp';

    $mysqli = new mysqli($host, $user, $pass, $name);
    if ($mysqli->connect_error) {
        throw new Exception("DB Connection failed: " . $mysqli->connect_error);
    }

    // 4. Query User
    $stmt = $mysqli->prepare("SELECT id, email, password_hash, first_name, last_name, status FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $data['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || !password_verify($data['password'], $user['password_hash'])) {
        throw new Exception("Invalid credentials");
    }

    if ($user['status'] !== 'active') {
        throw new Exception("Account is " . $user['status']);
    }

    // 5. Generate Token
    $key = $_ENV['encryption.key'] ?? 'your-secret-key-CHANGE-ME-IN-PROD';
    $payload = [
        'iss'  => 'http://localhost/rideapp/',
        'sub'  => $user['id'],
        'iat'  => time(),
        'exp'  => time() + (60 * 60 * 24), // 24 hours
        'role' => 'admin' // Simplified
    ];

    $token = JWT::encode($payload, $key, 'HS256');

    echo json_encode([
        'status' => 'success',
        'token'  => $token,
        'user'   => [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['first_name'] . ' ' . $user['last_name']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
