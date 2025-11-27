<?php
$url = 'http://localhost/gotribe/api/projects/6927cc684545f8.33563696';
echo "Testing URL: $url\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Raw Response Length: " . strlen($response) . "\n";
echo "Raw Response (Hex): " . bin2hex(substr($response, 0, 10)) . "...\n";
echo "Raw Response: $response\n";

$decoded = json_decode($response, true);
if ($decoded === null) {
    echo "JSON Decode Error: " . json_last_error_msg() . "\n";
} else {
    echo "JSON Decode Success\n";
    print_r($decoded);
}
