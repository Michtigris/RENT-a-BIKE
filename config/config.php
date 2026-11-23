<?php

$databaseConfig = [
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'bike_rental',
    'DB_USER' => 'root',
    'DB_PASS' => '',
];
$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $databaseConfig = array_merge($databaseConfig, require $localConfig);
}

define('DB_HOST', $databaseConfig['DB_HOST']);
define('DB_NAME', $databaseConfig['DB_NAME']);
define('DB_USER', $databaseConfig['DB_USER']);
define('DB_PASS', $databaseConfig['DB_PASS']);
