<?php
return [
    'type' => getenv('DB_TYPE') ?: 'mysql',
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: '3306',
    'user' => getenv('DB_USER') ?: 'root',
    'pass' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'name' => getenv('DB_NAME') ?: 'evrekaraf',
    'sqlite_file' => __DIR__ . '/evrekaraf_test.db',
];
