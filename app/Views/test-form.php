<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Flash;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CSRF</title>
</head>
<body>
    <main>
        <h1>Test du formulaire CSRF</h1>

        <?php foreach (Flash::get('success') as $message): ?>
            <p role="alert"><?= e($message) ?></p>
        <?php endforeach; ?>

        <form method="post" action="/test-form">
            <?= Csrf::field() ?>

            <button type="submit">
                Envoyer
            </button>
        </form>
    </main>
</body>
</html>