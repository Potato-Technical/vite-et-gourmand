<?php

declare(strict_types=1);

return [
    'environment' => $_ENV['APP_ENV'] ?? 'production',

    'url' => rtrim(
        $_ENV['APP_URL'] ?? 'http://localhost',
        '/'
    ),

    'debug' => filter_var(
        $_ENV['APP_DEBUG'] ?? false,
        FILTER_VALIDATE_BOOL
    ),

    'session' => [
        'name' => $_ENV['SESSION_NAME'] ?? 'vite_gourmand_session',
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 7200),
    ],
];
