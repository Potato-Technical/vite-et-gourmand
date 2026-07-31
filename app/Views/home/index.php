<?php

declare(strict_types=1);

/** @var string $title */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

        <p>Bienvenue sur le site Vite & Gourmand.</p>
    </main>

    <?php

    use App\Core\Auth;
    use App\Core\Csrf;
    ?>

    <?php if (Auth::check()): ?>
        <?php $user = Auth::user(); ?>

        <p>
            Connecté en tant que
            <?= e($user['prenom'] ?? '') ?>
            <?= e($user['nom'] ?? '') ?>
        </p>

        <form method="post" action="/deconnexion">
            <?= Csrf::field() ?>

            <button type="submit">
                Se déconnecter
            </button>
        </form>
    <?php else: ?>
        <a href="/connexion">
            Se connecter
        </a>
    <?php endif; ?>
</body>
</html>