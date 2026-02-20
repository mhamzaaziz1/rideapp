<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli("localhost", "root", "", "rideapp");

// 1. Clear migration history
$mysqli->query("DELETE FROM migrations WHERE class LIKE '%CreateTripsTable%' OR namespace LIKE '%Dispatch%'");
echo "Deleted migration history.<br>";

// 2. Drop table to ensure clean state
$mysqli->query("DROP TABLE IF EXISTS trips");
echo "Dropped 'trips' table.<br>";

echo "Ready for fresh migration.";
