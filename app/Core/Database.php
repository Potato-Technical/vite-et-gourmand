<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    /**
     * @param array{
     *     host: string,
     *     port: int,
     *     database: string,
     *     username: string,
     *     password: string,
     *     charset: string
     * } $config
     */
    public static function connect(array $config): PDO
    {
        self::validateConfig($config);

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            return new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                ]
            );
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Connexion à la base de données impossible.',
                0,
                $exception
            );
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function validateConfig(array $config): void
    {
        $requiredKeys = [
            'host',
            'port',
            'database',
            'username',
            'password',
            'charset',
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new RuntimeException(
                    sprintf(
                        'Le paramètre de base de données "%s" est manquant.',
                        $key
                    )
                );
            }
        }

        if (
            !is_string($config['host'])
            || $config['host'] === ''
            || !is_int($config['port'])
            || $config['port'] <= 0
            || !is_string($config['database'])
            || $config['database'] === ''
            || !is_string($config['username'])
            || $config['username'] === ''
            || !is_string($config['password'])
            || $config['password'] === ''
            || !is_string($config['charset'])
            || $config['charset'] === ''
        ) {
            throw new RuntimeException(
                'La configuration de la base de données est invalide.'
            );
        }
    }

    private function __construct()
    {
    }
}
