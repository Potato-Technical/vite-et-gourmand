<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use RuntimeException;

final class User
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    u.id_utilisateur,
                    u.nom,
                    u.prenom,
                    u.telephone,
                    u.email,
                    u.mot_de_passe_hash,
                    u.actif,
                    u.date_creation,
                    u.date_modification,
                    r.id_role,
                    r.code AS role_code,
                    r.libelle AS role_libelle
                FROM utilisateur AS u
                INNER JOIN role AS r
                    ON r.id_role = u.id_role
                WHERE u.email = :email
                LIMIT 1
            SQL
        );

        $statement->execute([
            'email' => self::normalizeEmail($email),
        ]);

        $user = $statement->fetch();

        return is_array($user) ? $user : null;
    }

    public function emailExists(string $email): bool
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT 1
                FROM utilisateur
                WHERE email = :email
                LIMIT 1
            SQL
        );

        $statement->execute([
            'email' => self::normalizeEmail($email),
        ]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * @param array{
     *     nom: string,
     *     prenom: string,
     *     telephone: string|null,
     *     email: string,
     *     password_hash: string
     * } $data
     */
    public function create(array $data): int
    {
        $roleId = $this->findRoleId('utilisateur');

        $statement = $this->pdo->prepare(
            <<<'SQL'
                INSERT INTO utilisateur (
                    nom,
                    prenom,
                    telephone,
                    email,
                    mot_de_passe_hash,
                    actif,
                    id_role
                ) VALUES (
                    :nom,
                    :prenom,
                    :telephone,
                    :email,
                    :mot_de_passe_hash,
                    1,
                    :id_role
                )
            SQL
        );

        $statement->execute([
            'nom' => trim($data['nom']),
            'prenom' => trim($data['prenom']),
            'telephone' => self::normalizeNullableString(
                $data['telephone']
            ),
            'email' => self::normalizeEmail($data['email']),
            'mot_de_passe_hash' => $data['password_hash'],
            'id_role' => $roleId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    private function findRoleId(string $code): int
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT id_role
                FROM role
                WHERE code = :code
                LIMIT 1
            SQL
        );

        $statement->execute([
            'code' => $code,
        ]);

        $roleId = $statement->fetchColumn();

        if ($roleId === false) {
            throw new RuntimeException(
                sprintf(
                    'Le rôle "%s" est introuvable dans la base de données.',
                    $code
                )
            );
        }

        return (int) $roleId;
    }

    private static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private static function normalizeNullableString(
        ?string $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    id_utilisateur,
                    nom,
                    prenom,
                    telephone,
                    email,
                    actif,
                    id_role
                FROM utilisateur
                WHERE id_utilisateur = :id
                LIMIT 1
            SQL
        );

        $statement->execute([
            'id' => $id,
        ]);

        $user = $statement->fetch();

        return is_array($user) ? $user : null;
    }
}
