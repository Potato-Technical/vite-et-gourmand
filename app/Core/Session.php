<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Session
{
    /**
     * @param array{
     *     name: string,
     *     lifetime: int
     * } $config
     */
    public static function start(array $config): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        self::validateConfig($config);

        if (headers_sent($filename, $line)) {
            throw new RuntimeException(
                sprintf(
                    'Impossible de démarrer la session : les en-têtes ont déjà été envoyés dans %s à la ligne %d.',
                    $filename,
                    $line
                )
            );
        }

        $secure = self::isHttps();

        if (!session_name($config['name'])) {
            throw new RuntimeException(
                'Impossible de définir le nom de la session.'
            );
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', $secure ? '1' : '0');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', (string) $config['lifetime']);

        $started = session_start([
            'cookie_lifetime' => $config['lifetime'],
            'cookie_path' => '/',
            'cookie_secure' => $secure,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'use_strict_mode' => true,
            'use_only_cookies' => true,
        ]);

        if (!$started) {
            throw new RuntimeException(
                'Impossible de démarrer la session.'
            );
        }
    }

    public static function regenerate(): void
    {
        self::ensureStarted();

        if (!session_regenerate_id(false)) {
            throw new RuntimeException(
                'Impossible de régénérer l’identifiant de session.'
            );
        }
    }

    public static function destroy(): void
    {
        self::ensureStarted();

        $_SESSION = [];

        $cookieParameters = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 3600,
                'path' => $cookieParameters['path'],
                'domain' => $cookieParameters['domain'],
                'secure' => $cookieParameters['secure'],
                'httponly' => $cookieParameters['httponly'],
                'samesite' => $cookieParameters['samesite'] ?: 'Lax',
            ]
        );

        session_destroy();
    }

    public static function set(string $key, mixed $value): void
    {
        self::ensureStarted();

        $_SESSION[$key] = $value;
    }

    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        self::ensureStarted();

        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::ensureStarted();

        return array_key_exists($key, $_SESSION);
    }

    public static function remove(string $key): void
    {
        self::ensureStarted();

        unset($_SESSION[$key]);
    }

    private static function ensureStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException(
                'Aucune session active.'
            );
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function validateConfig(array $config): void
    {
        if (
            !isset($config['name'], $config['lifetime'])
            || !is_string($config['name'])
            || $config['name'] === ''
            || !is_int($config['lifetime'])
            || $config['lifetime'] <= 0
        ) {
            throw new RuntimeException(
                'La configuration de session est invalide.'
            );
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $config['name'])) {
            throw new RuntimeException(
                'Le nom de session contient des caractères invalides.'
            );
        }
    }

    private static function isHttps(): bool
    {
        if (
            isset($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== ''
            && $_SERVER['HTTPS'] !== 'off'
        ) {
            return true;
        }

        return isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
    }

    private function __construct()
    {
    }
}