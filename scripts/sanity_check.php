#!/usr/bin/env php
<?php

declare(strict_types=1);

$required = [
    'DB_HOST',
    'DB_PORT',
    'DB_NAME',
    'DB_USER',
    'DB_PASS',
];

foreach ($required as $variable) {
    if (getenv($variable) === false || getenv($variable) === '') {
        fwrite(STDERR, "[FAIL] Variable manquante : {$variable}\n");
        exit(1);
    }
}

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        getenv('DB_HOST'),
        getenv('DB_PORT'),
        getenv('DB_NAME')
    );

    $pdo = new PDO(
        $dsn,
        getenv('DB_USER'),
        getenv('DB_PASS'),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );

    $version = $pdo->query('SELECT VERSION()')->fetchColumn();

    echo "[OK] MySQL connecté\n";
    echo "[OK] Version serveur : {$version}\n";
    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, "[FAIL] {$exception->getMessage()}\n");
    exit(1);
}