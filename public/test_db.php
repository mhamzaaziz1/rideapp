<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli("localhost", "root", "", "rideapp");
    echo "Connected successfully.<br>";
    
    $result = $mysqli->query("SELECT * FROM users");
    echo "Users count: " . $result->num_rows . "<br>";
    
    while ($row = $result->fetch_assoc()) {
        $row['password_hash'] = substr($row['password_hash'], 0, 10) . '...';
        echo "<pre>" . print_r($row, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
