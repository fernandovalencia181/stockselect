<?php
$url = 'http://127.0.0.1:8000/admin/login';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$start = microtime(true);
$response = curl_exec($ch);
$elapsed = microtime(true) - $start;
$info = curl_getinfo($ch);
curl_close($ch);

echo "URL: $url\n";
echo "Elapsed Time: " . round($elapsed, 4) . "s\n";
echo "HTTP Code: " . $info['http_code'] . "\n";
echo "Primary IP: " . $info['primary_ip'] . "\n";
if ($response === false) {
    echo "Error: " . curl_error($ch) . "\n";
}
