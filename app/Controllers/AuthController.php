<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Models\User;
use PDO;
use PDOException;
use RuntimeException;

final class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $databaseConfig = require dirname(__DIR__, 2)
            . '/config/database.php';

        $pdo = Database::connect($databaseConfig['mysql']);

        $this->users = new User($pdo);
    }

    public function showRegister(): void
    {
        $this->render('auth/register', [
            'errors' => [],
            'old' => [],
        ]);
    }

    public function register(): void
    {
        $submittedToken = $_POST['_token'] ?? null;

        if (
            !is_string($submittedToken)
            || !Csrf::validate($submittedToken)
        ) {
            http_response_code(403);

            $this->render('errors/403');

            return;
        }

        $old = [
            'nom' => $this->postString('nom'),
            'prenom' => $this->postString('prenom'),
            'telephone' => $this->postString('telephone'),
            'email' => strtolower($this->postString('email')),
        ];

        $password = $this->postString('password', false);
        $passwordConfirmation = $this->postString(
            'password_confirmation',
            false
        );

        $errors = $this->validateRegistration(
            $old,
            $password,
            $passwordConfirmation
        );

        if (
            !isset($errors['email'])
            && $this->users->emailExists($old['email'])
        ) {
            $errors['email'] =
                'Un compte utilise déjà cette adresse e-mail.';
        }

        if ($errors !== []) {
            http_response_code(422);

            $this->render('auth/register', [
                'errors' => $errors,
                'old' => $old,
            ]);

            return;
        }

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if (!is_string($passwordHash)) {
            throw new RuntimeException(
                'Le hachage du mot de passe a échoué.'
            );
        }

        try {
            $this->users->create([
                'nom' => $old['nom'],
                'prenom' => $old['prenom'],
                'telephone' => $old['telephone'] !== ''
                    ? $old['telephone']
                    : null,
                'email' => $old['email'],
                'password_hash' => $passwordHash,
            ]);
        } catch (PDOException $exception) {
            if ($this->isDuplicateEntry($exception)) {
                http_response_code(422);

                $this->render('auth/register', [
                    'errors' => [
                        'email' =>
                            'Un compte utilise déjà cette adresse e-mail.',
                    ],
                    'old' => $old,
                ]);

                return;
            }

            throw $exception;
        }

        Csrf::regenerate();

        Flash::add(
            'success',
            'Votre compte a été créé avec succès.'
        );

        header('Location: /inscription', true, 303);
    }

    /**
     * @param array{
     *     nom: string,
     *     prenom: string,
     *     telephone: string,
     *     email: string
     * } $data
     *
     * @return array<string, string>
     */
    private function validateRegistration(
        array $data,
        string $password,
        string $passwordConfirmation
    ): array {
        $errors = [];

        if ($data['nom'] === '') {
            $errors['nom'] = 'Le nom est obligatoire.';
        } elseif ($this->length($data['nom']) > 100) {
            $errors['nom'] =
                'Le nom ne doit pas dépasser 100 caractères.';
        }

        if ($data['prenom'] === '') {
            $errors['prenom'] = 'Le prénom est obligatoire.';
        } elseif ($this->length($data['prenom']) > 100) {
            $errors['prenom'] =
                'Le prénom ne doit pas dépasser 100 caractères.';
        }

        if (
            $data['telephone'] !== ''
            && $this->length($data['telephone']) > 30
        ) {
            $errors['telephone'] =
                'Le téléphone ne doit pas dépasser 30 caractères.';
        }

        if ($data['email'] === '') {
            $errors['email'] =
                'L’adresse e-mail est obligatoire.';
        } elseif ($this->length($data['email']) > 254) {
            $errors['email'] =
                'L’adresse e-mail ne doit pas dépasser 254 caractères.';
        } elseif (
            filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false
        ) {
            $errors['email'] =
                'L’adresse e-mail n’est pas valide.';
        }

        if ($password === '') {
            $errors['password'] =
                'Le mot de passe est obligatoire.';
        } elseif ($this->length($password) < 12) {
            $errors['password'] =
                'Le mot de passe doit contenir au moins 12 caractères.';
        } elseif ($this->length($password) > 4096) {
            $errors['password'] =
                'Le mot de passe est trop long.';
        }

        if ($passwordConfirmation === '') {
            $errors['password_confirmation'] =
                'La confirmation du mot de passe est obligatoire.';
        } elseif (
            $password !== ''
            && !hash_equals($password, $passwordConfirmation)
        ) {
            $errors['password_confirmation'] =
                'Les mots de passe ne correspondent pas.';
        }

        return $errors;
    }

    private function postString(
        string $key,
        bool $trim = true
    ): string {
        $value = $_POST[$key] ?? '';

        if (!is_string($value)) {
            return '';
        }

        return $trim ? trim($value) : $value;
    }

    private function length(string $value): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value, 'UTF-8');
        }

        return strlen($value);
    }

    private function isDuplicateEntry(
        PDOException $exception
    ): bool {
        if ($exception->getCode() !== '23000') {
            return false;
        }

        $driverCode = $exception->errorInfo[1] ?? null;

        return $driverCode === 1062;
    }
}
