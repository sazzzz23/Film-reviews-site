<?php

declare(strict_types=1);

$configFile = __DIR__ . '/config.php';

if (!is_file($configFile)) {
    throw new RuntimeException('Local database configuration is missing. Copy includes/config.example.php to includes/config.php.');
}

$config = require $configFile;
$database = $config['database'] ?? [];
$appConfig = $config['app'] ?? [];

foreach (['host', 'database', 'username', 'password'] as $key) {
    if (!array_key_exists($key, $database) || $database[$key] === '') {
        throw new RuntimeException('The local database configuration is incomplete.');
    }
}

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=utf8mb4%s',
    $database['host'],
    $database['database'],
    !empty($database['port']) ? ';port=' . (int) $database['port'] : ''
);

$pdo = new PDO($dsn, $database['username'], $database['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
