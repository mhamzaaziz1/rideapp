<?php
$apiKey = 'AIzaSyDJWoevXadJVDNHB3awypKVJGIn9UR8HQA';
$query = 'New York';
$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($query) . '&key=' . $apiKey;

$options = [
    'http' => [
        'header' => "Referer: http://localhost/\r\n"
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);
echo "With localhost referer:\n";
echo $response ? $response : "FAILED\n";

$options2 = [
    'http' => [
        'header' => "Referer: https://rideapp.com/\r\n"
    ]
];
$context2 = stream_context_create($options2);
$response2 = @file_get_contents($url, false, $context2);
echo "With rideapp.com referer:\n";
echo $response2 ? $response2 : "FAILED\n";
