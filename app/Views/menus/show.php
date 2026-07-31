<?php

declare(strict_types=1);

/**
 * @var string $title
 * @var array<string, mixed> $menu
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
            width: min(850px, 100%);
            margin: 0 auto;
            padding: 1.5rem;
            border-radius: 0.5rem;
            background: #fff;
        }

        .navigation {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
        }

        .summary dl {
            margin: 0;
        }

        .summary dt {
            font-weight: bold;
        }

        .summary dd {
            margin: 0.25rem 0 0;
        }

        .dish {
            margin-bottom: 1rem;
            padding: 1rem;
            border-left: 4px solid #555;
            background: #f8f8f8;
        }

        .dish h3 {
            margin-top: 0;
        }

        .allergens {
            margin-bottom: 0;
        }

        .order-button {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.25rem;
            border: 0;
            border-radius: 0.25rem;
            background: #666;
            color: #fff;
            font: inherit;
            cursor: not-allowed;
        }

        @media (max-width: 600px) {
            body {
                padding: 0;
            }

            main {
                padding: 1rem;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <main>
        <nav class="navigation" aria-label="Fil d’Ariane">
            <a href="/">Accueil</a>
            <a href="/menus">Tous les menus</a>
        </nav>

        <article>
            <h1><?= e($menu['nom']) ?></h1>

            <p><?= nl2br(e($menu['description'])) ?></p>

            <section
                class="summary"
                aria-label="Informations du menu"
            >
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
                </dl>

                <dl>
                    <dt>Nombre minimum</dt>
                    <dd>
                        <?= (int) $menu[
                            'nombre_personnes_minimum'
                        ] ?>
                        personne(s)
                    </dd>
                </dl>

                <dl>
                    <dt>Thème</dt>
                    <dd><?= e($menu['theme']) ?></dd>
                </dl>

                <dl>
                    <dt>Régime</dt>
                    <dd><?= e($menu['regime']) ?></dd>
                </dl>
            </section>

            <section>
                <h2>Plats associés</h2>

                <?php if ($menu['plats'] === []): ?>
                    <p>
                        Aucun plat n’est actuellement associé à ce menu.
                    </p>
                <?php else: ?>
                    <?php foreach ($menu['plats'] as $dish): ?>
                        <article class="dish">
                            <h3><?= e($dish['nom']) ?></h3>

                            <p>
                                <strong>Type :</strong>
                                <?= e($dish['type']) ?>
                            </p>

                            <?php if ($dish['allergenes'] === []): ?>
                                <p class="allergens">
                                    Aucun allergène renseigné.
                                </p>
                            <?php else: ?>
                                <p class="allergens">
                                    <strong>Allergènes :</strong>
                                    <?= e(
                                        implode(
                                            ', ',
                                            $dish['allergenes']
                                        )
                                    ) ?>
                                </p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <section>
                <h2>Conditions</h2>

                <p>
                    <?= nl2br(e($menu['conditions'])) ?>
                </p>
            </section>

            <button
                type="button"
                class="order-button"
                disabled
                aria-describedby="order-message"
            >
                Commander
            </button>

            <p id="order-message">
                La commande en ligne sera disponible prochainement.
            </p>
        </article>
    </main>
</body>
</html>