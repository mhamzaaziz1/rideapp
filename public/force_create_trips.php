<?php
// force_create_trips.php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'rideapp';

try {
    $mysqli = new mysqli($host, $user, $pass, $db);
    echo "Connected to database '$db'.<br>";

    $sql = "CREATE TABLE IF NOT EXISTS `trips` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `trip_number` VARCHAR(50) NOT NULL,
        `customer_id` INT(11) UNSIGNED DEFAULT NULL,
        `driver_id` INT(11) UNSIGNED DEFAULT NULL,
        `status` ENUM('pending', 'dispatching', 'active', 'completed', 'cancelled', 'scheduled') DEFAULT 'pending',
        `pickup_address` TEXT NOT NULL,
        `dropoff_address` TEXT NOT NULL,
        `pickup_lat` DECIMAL(10,8) DEFAULT NULL,
        `pickup_lng` DECIMAL(11,8) DEFAULT NULL,
        `dropoff_lat` DECIMAL(10,8) DEFAULT NULL,
        `dropoff_lng` DECIMAL(11,8) DEFAULT NULL,
        `distance_miles` DECIMAL(10,2) DEFAULT '0.00',
        `duration_minutes` INT(11) DEFAULT '0',
        `fare_amount` DECIMAL(10,2) DEFAULT '0.00',
        `vehicle_type` VARCHAR(50) DEFAULT 'Sedan',
        `passengers` INT(11) DEFAULT '1',
        `notes` TEXT DEFAULT NULL,
        `scheduled_at` DATETIME DEFAULT NULL,
        `started_at` DATETIME DEFAULT NULL,
        `completed_at` DATETIME DEFAULT NULL,
        `created_at` DATETIME DEFAULT NULL,
        `updated_at` DATETIME DEFAULT NULL,
        `deleted_at` DATETIME DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `trip_number` (`trip_number`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    if ($mysqli->query($sql)) {
        echo "<h2 style='color:green'>Success: Table 'trips' created/verified!</h2>";
    } else {
        echo "<h2 style='color:red'>Error: " . $mysqli->error . "</h2>";
    }

} catch (Exception $e) {
    echo "<h1>Critical Error</h1>";
    echo $e->getMessage();
}
