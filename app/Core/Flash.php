<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Flash
{
    private const SESSION_KEY = '_flash';

    public static function startRequest(): void
    {
        self::ensureSessionStarted();

        $flash = $_SESSION[self::SESSION_KEY] ?? [];

        $_SESSION[self::SESSION_KEY] = [
            'old' => is_array($flash['new'] ?? null)
                ? $flash['new']
                : [],
            'new' => [],
        ];
    }

    public static function add(string $type, string $message): void
    {
        self::ensureSessionStarted();

        if ($type === '' || $message === '') {
            throw new RuntimeException(
                'Le type et le contenu du message Flash sont obligatoires.'
            );
        }

        $_SESSION[self::SESSION_KEY]['new'][$type][] = $message;
    }

    /**
     * @return list<string>
     */
    public static function get(string $type): array
    {
        self::ensureSessionStarted();

        $messages = $_SESSION[self::SESSION_KEY]['old'][$type] ?? [];

        unset($_SESSION[self::SESSION_KEY]['old'][$type]);

        return is_array($messages)
            ? array_values(array_filter($messages, 'is_string'))
            : [];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function all(): array
    {
        self::ensureSessionStarted();

        $messages = $_SESSION[self::SESSION_KEY]['old'] ?? [];

        $_SESSION[self::SESSION_KEY]['old'] = [];

        if (!is_array($messages)) {
            return [];
        }

        $result = [];

        foreach ($messages as $type => $items) {
            if (!is_string($type) || !is_array($items)) {
                continue;
            }

            $result[$type] = array_values(
                array_filter($items, 'is_string')
            );
        }

        return $result;
    }

    public static function has(string $type): bool
    {
        self::ensureSessionStarted();

        return !empty(
            $_SESSION[self::SESSION_KEY]['old'][$type]
        );
    }

    private static function ensureSessionStarted(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException(
                'Une session active est nécessaire pour utiliser les messages Flash.'
            );
        }
    }

    private function __construct()
    {
    }
}
