<?php

declare(strict_types=1);

/**
 * @var string $title
 * @var array<int, array<string, mixed>> $menus
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= e($title) ?> — Vite & Gourmand</title>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 1rem;
            font-family: Arial, sans-serif;
            line-height: 1.5;
            background: #f5f5f5;
            color: #222;
        }

        main {
            width: min(1100px, 100%);
            margin: 0 auto;
        }

        .navigation {
            margin-bottom: 1.5rem;
        }

        .menus {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .menu-card {
            display: flex;
            flex-direction: column;
            padding: 1.25rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            background: #fff;
        }

        .menu-card h2 {
            margin-top: 0;
        }

        .menu-card dl {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 0.35rem 0.75rem;
        }

        .menu-card dt {
            font-weight: bold;
        }

        .menu-card dd {
            margin: 0;
        }

        .menu-card a {
            display: inline-block;
            align-self: flex-start;
            margin-top: auto;
            padding: 0.65rem 1rem;
            border-radius: 0.25rem;
            background: #222;
            color: #fff;
            text-decoration: none;
        }

        .menu-card a:focus,
        .menu-card a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main>
        <nav class="navigation" aria-label="Navigation principale">
            <a href="/">Accueil</a>
        </nav>

        <h1><?= e($title) ?></h1>

        <?php if ($menus === []): ?>
            <p>Aucun menu n’est disponible actuellement.</p>
        <?php else: ?>
            <div class="menus">
                <?php foreach ($menus as $menu): ?>
                    <article class="menu-card">
                        <h2><?= e($menu['nom']) ?></h2>

                        <p>
                            <?= e($menu['description_courte']) ?>
                        </p>

                        <dl>
                            <dt>Prix minimum</dt>
                            <dd>
                                <?= e(
                                    number_format(
                                        (float) $menu['prix'],
                                        2,
                                        ',',
                                        ' '
                                    )
                                ) ?>
                                €
                            </dd>

                            <dt>Minimum</dt>
                            <dd>
                                <?= (int) $menu[
                                    'nombre_personnes_minimum'
                                ] ?>
                                personne(s)
                            </dd>

                            <dt>Thème</dt>
                            <dd><?= e($menu['theme']) ?></dd>

                            <dt>Régime</dt>
                            <dd><?= e($menu['regime']) ?></dd>
                        </dl>

                        <a
                            href="/menu?id=<?= (int) $menu['id'] ?>"
                            aria-label="Voir le menu <?= e($menu['nom']) ?>"
                        >
                            Voir le détail
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>