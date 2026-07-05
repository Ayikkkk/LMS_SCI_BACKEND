<?php
// Script untuk cek apakah Redis tersedia di server
// Jalankan: php check_redis.php

$host = '127.0.0.1';
$port = 6379;

$socket = @fsockopen($host, $port, $errno, $errstr, 2);
if ($socket) {
    fclose($socket);
    echo "✅ Redis TERSEDIA di $host:$port" . PHP_EOL;
    echo "   → Aman pakai CACHE_STORE=redis, SESSION_DRIVER=redis, QUEUE_CONNECTION=redis" . PHP_EOL;
} else {
    echo "❌ Redis TIDAK tersedia di $host:$port ($errstr)" . PHP_EOL;
    echo "   → Gunakan CACHE_STORE=file, SESSION_DRIVER=file, QUEUE_CONNECTION=sync" . PHP_EOL;
}

// Cek ekstensi PHP
echo PHP_EOL . "=== PHP Extensions ===" . PHP_EOL;
$exts = ['redis', 'pdo_mysql', 'opcache', 'mbstring', 'openssl'];
foreach ($exts as $ext) {
    echo ($ext . ': ' . (extension_loaded($ext) ? '✅' : '❌')) . PHP_EOL;
}

echo PHP_EOL . "=== PHP Version ===" . PHP_EOL;
echo "PHP " . PHP_VERSION . PHP_EOL;

echo PHP_EOL . "=== Memory Limit ===" . PHP_EOL;
echo ini_get('memory_limit') . PHP_EOL;
