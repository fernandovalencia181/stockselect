<?php
$url = 'http://127.0.0.1:8000/admin/products';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 70); // Long timeout
$start = microtime(true);
$response = curl_exec($ch);
$elapsed = microtime(true) - $start;
$info = curl_getinfo($ch);
curl_close($ch);

echo "URL: $url\n";
echo "Elapsed Time: " . round($elapsed, 4) . "s\n";
echo "HTTP Code: " . $info['http_code'] . "\n";
if ($response === false) {
    echo "Error: " . curl_error($ch) . "\n";
} else {
    echo "Response Length: " . strlen($response) . " bytes\n";
}
