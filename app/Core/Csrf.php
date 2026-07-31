<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        self::ensureSessionStarted();

        $token = $_SESSION[self::SESSION_KEY] ?? null;

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $_SESSION[self::SESSION_KEY] = $token;
        }

        return $token;
    }

    public static function field(): string
    {
        return sprintf(
            '<input type="hidden" name="_token" value="%s">',
            e(self::token())
        );
    }

    public static function validate(?string $submittedToken): bool
    {
        self::ensureSessionStarted();

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? null;

        if (
            !is_string($sessionToken)
            || $sessionToken === ''
            || !is_string($submittedToken)
            || $submittedToken === ''
        ) {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }

    public static function regenerate(): string
    {
        self::ensureSessionStarted();

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY] = $token;

        return $token;
    }

    private static function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException(
                'Une session active est nécessaire pour utiliser la protection CSRF.'
            );
        }
    }

    private function __construct()
    {
    }
}
