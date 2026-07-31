<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Flash;

/** @var array<string, string> $errors */
/** @var array<string, string> $old */

$errors = $errors ?? [];
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Inscription - Vite & Gourmand</title>
</head>
<body>
    <main>
        <h1>Créer un compte</h1>

        <?php foreach (Flash::get('success') as $message): ?>
            <p role="status">
                <?= e($message) ?>
            </p>
        <?php endforeach; ?>

        <?php if ($errors !== []): ?>
            <div role="alert">
                <p>
                    Le formulaire contient une ou plusieurs erreurs.
                </p>
            </div>
        <?php endif; ?>

        <form method="post" action="/inscription" novalidate>
            <?= Csrf::field() ?>

            <div>
                <label for="nom">Nom</label>

                <input
                    type="text"
                    id="nom"
                    name="nom"
                    maxlength="100"
                    autocomplete="family-name"
                    required
                    value="<?= e($old['nom'] ?? '') ?>"
                    <?= isset($errors['nom'])
                        ? 'aria-invalid="true" aria-describedby="nom-error"'
                        : '' ?>
                >

                <?php if (isset($errors['nom'])): ?>
                    <p id="nom-error">
                        <?= e($errors['nom']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <label for="prenom">Prénom</label>

                <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    maxlength="100"
                    autocomplete="given-name"
                    required
                    value="<?= e($old['prenom'] ?? '') ?>"
                    <?= isset($errors['prenom'])
                        ? 'aria-invalid="true" aria-describedby="prenom-error"'
                        : '' ?>
                >

                <?php if (isset($errors['prenom'])): ?>
                    <p id="prenom-error">
                        <?= e($errors['prenom']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <label for="telephone">
                    Téléphone
                    <span>(facultatif)</span>
                </label>

                <input
                    type="tel"
                    id="telephone"
                    name="telephone"
                    maxlength="30"
                    autocomplete="tel"
                    value="<?= e($old['telephone'] ?? '') ?>"
                    <?= isset($errors['telephone'])
                        ? 'aria-invalid="true" aria-describedby="telephone-error"'
                        : '' ?>
                >

                <?php if (isset($errors['telephone'])): ?>
                    <p id="telephone-error">
                        <?= e($errors['telephone']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <label for="email">Adresse e-mail</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="254"
                    autocomplete="email"
                    required
                    value="<?= e($old['email'] ?? '') ?>"
                    <?= isset($errors['email'])
                        ? 'aria-invalid="true" aria-describedby="email-error"'
                        : '' ?>
                >

                <?php if (isset($errors['email'])): ?>
                    <p id="email-error">
                        <?= e($errors['email']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password">Mot de passe</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="12"
                    maxlength="4096"
                    autocomplete="new-password"
                    required
                    <?= isset($errors['password'])
                        ? 'aria-invalid="true" aria-describedby="password-error"'
                        : '' ?>
                >

                <p>
                    Le mot de passe doit contenir au moins
                    12 caractères.
                </p>

                <?php if (isset($errors['password'])): ?>
                    <p id="password-error">
                        <?= e($errors['password']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password_confirmation">
                    Confirmer le mot de passe
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    minlength="12"
                    maxlength="4096"
                    autocomplete="new-password"
                    required
                    <?= isset($errors['password_confirmation'])
                        ? 'aria-invalid="true" aria-describedby="password-confirmation-error"'
                        : '' ?>
                >

                <?php if (isset($errors['password_confirmation'])): ?>
                    <p id="password-confirmation-error">
                        <?= e($errors['password_confirmation']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <button type="submit">
                Créer mon compte
            </button>
        </form>

        <p>
            <a href="/">Retour à l’accueil</a>
        </p>
    </main>
</body>
</html>