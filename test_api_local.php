<?php

$url = 'https://gotribe.co/api/projects';
$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI2OTIzYTg3OTIyZmE2Ni4xNjg1MjQ3OCIsImVtYWlsIjoiYWRtaW5AZ290cmliZS5jbyIsInJvbGUiOiJDUkVBVE9SIiwiaWF0IjoxNzY0MTI3NTM4LCJleHAiOjE3NjQxMzExMzh9.YSKXEkJTKe7A0IdRvm04tkYCS_eRNOUEnkojojW_kE0';

$data = [
    'title' => 'Test Project PHP ' . time(),
    'description' => 'Testing API stability',
    'activityType' => 'HIKING',
    'startDateTime' => '2025-12-01T10:00:00',
    'meetingPoint' => 'Central Park',
    'price' => 10000,
    'currency' => 'COP'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
if ($error) {
    echo "Error: $error\n";
}
