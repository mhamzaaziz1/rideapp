<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $mysqli = new mysqli("localhost", "root", "", "rideapp");
    $result = $mysqli->query("SHOW TABLES");
    
    echo "<h1>Tables in 'rideapp'</h1><ul>";
    while ($row = $result->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";

    // Also check migrations table
    echo "<h2>Migrations</h2><ul>";
    $result = $mysqli->query("SELECT * FROM migrations ORDER BY id DESC");
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['version'] . " - " . $row['class'] . " (" . $row['group'] . ", " . $row['namespace'] . ")</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
