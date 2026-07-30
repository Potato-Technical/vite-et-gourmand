<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private const SESSION_KEY = 'auth';

    /**
     * @param array<string, mixed> $user
     */
    public static function login(array $user): void
    {
        Session::regenerate();

        Session::set(self::SESSION_KEY, [
            'id' => (int) $user['id_utilisateur'],
            'nom' => (string) $user['nom'],
            'prenom' => (string) $user['prenom'],
            'email' => (string) $user['email'],
            'role' => (string) $user['role_code'],
        ]);
    }

    public static function logout(): void
    {
        Session::remove(self::SESSION_KEY);
        Session::regenerate();
    }

    public static function check(): bool
    {
        $user = Session::get(self::SESSION_KEY);

        return is_array($user)
            && isset(
                $user['id'],
                $user['email'],
                $user['role']
            );
    }

    /**
     * @return array{
     *     id: int,
     *     nom: string,
     *     prenom: string,
     *     email: string,
     *     role: string
     * }|null
     */
    public static function user(): ?array
    {
        $user = Session::get(self::SESSION_KEY);

        return is_array($user) ? $user : null;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return isset($user['id'])
            ? (int) $user['id']
            : null;
    }

    public static function role(): ?string
    {
        $user = self::user();

        return isset($user['role'])
            ? (string) $user['role']
            : null;
    }

    public static function hasRole(string ...$roles): bool
    {
        $role = self::role();

        return $role !== null
            && in_array($role, $roles, true);
    }

    public static function requireLogin(): void
    {
        if (self::check()) {
            return;
        }

        header('Location: /connexion', true, 303);
        exit;
    }

    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();

        if (self::hasRole(...$roles)) {
            return;
        }

        http_response_code(403);

        require dirname(__DIR__) . '/Views/errors/403.php';

        exit;
    }

    private function __construct()
    {
    }
}