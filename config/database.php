<?php

declare(strict_types=1);

return [
    'mysql' => [
        'host' => $_ENV['DB_HOST'] ?? 'db',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'database' => $_ENV['DB_NAME'] ?? '',
        'username' => $_ENV['DB_USER'] ?? '',
        'password' => $_ENV['DB_PASS'] ?? '',
        'charset' => 'utf8mb4',
    ],

    'mongodb' => [
        'host' => $_ENV['MONGO_HOST'] ?? 'mongo',
        'port' => (int) ($_ENV['MONGO_PORT'] ?? 27017),
        'database' => $_ENV['MONGO_DB'] ?? '',
        'username' => $_ENV['MONGO_ROOT_USER'] ?? '',
        'password' => $_ENV['MONGO_ROOT_PASS'] ?? '',
        'collection_stats' => $_ENV['MONGO_COLLECTION_STATS'] ?? '',
        'uri' => $_ENV['MONGODB_URI'] ?? '',
    ],
];
