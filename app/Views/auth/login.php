<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Flash;

/**
 * @var array<string, string> $errors
 * @var array<string, string> $old
 */

$credentialError = $errors['credentials'] ?? null;
$successMessages = Flash::get('success');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Connexion — Vite & Gourmand</title>
</head>

<body>
    <main>
        <h1>Connexion</h1>

        <?php foreach ($successMessages as $successMessage): ?>
            <p role="status">
                <?= e($successMessage) ?>
            </p>
        <?php endforeach; ?>

        <?php if (is_string($credentialError)): ?>
            <p role="alert">
                <?= e($credentialError) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="/connexion">
            <?= Csrf::field() ?>

            <div>
                <label for="email">
                    Adresse e-mail
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="254"
                    autocomplete="email"
                    required
                    value="<?= e($old['email'] ?? '') ?>"
                    <?php if (isset($errors['email'])): ?>
                        aria-invalid="true"
                        aria-describedby="email-error"
                    <?php endif; ?>
                >

                <?php if (isset($errors['email'])): ?>
                    <p id="email-error" role="alert">
                        <?= e($errors['email']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password">
                    Mot de passe
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    maxlength="4096"
                    autocomplete="current-password"
                    required
                    <?php if (isset($errors['password'])): ?>
                        aria-invalid="true"
                        aria-describedby="password-error"
                    <?php endif; ?>
                >

                <?php if (isset($errors['password'])): ?>
                    <p id="password-error" role="alert">
                        <?= e($errors['password']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <button type="submit">
                Se connecter
            </button>
        </form>

        <p>
            Aucun compte ?
            <a href="/inscription">
                Créer un compte
            </a>
        </p>
    </main>
</body>
</html>