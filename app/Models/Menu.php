<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Menu
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findVisible(): array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    m.id_menu,
                    m.titre,
                    m.description,
                    m.nombre_personnes_minimum,
                    m.prix_minimum,
                    m.stock_disponible,
                    t.libelle AS theme,
                    r.libelle AS regime
                FROM menu AS m
                INNER JOIN theme AS t
                    ON t.id_theme = m.id_theme
                INNER JOIN regime AS r
                    ON r.id_regime = m.id_regime
                WHERE m.actif = 1
                  AND m.publie = 1
                  AND m.stock_disponible > 0
                ORDER BY m.titre ASC
            SQL
        );

        $statement->execute();

        $menus = $statement->fetchAll();

        if (!is_array($menus)) {
            return [];
        }

        return array_map(
            static function (array $menu): array {
                return [
                    'id' => (int) $menu['id_menu'],
                    'nom' => (string) $menu['titre'],
                    'description_courte' => self::shortDescription(
                        (string) $menu['description']
                    ),
                    'prix' => (float) $menu['prix_minimum'],
                    'nombre_personnes_minimum' =>
                        (int) $menu['nombre_personnes_minimum'],
                    'stock_disponible' =>
                        (int) $menu['stock_disponible'],
                    'theme' => (string) $menu['theme'],
                    'regime' => (string) $menu['regime'],
                ];
            },
            $menus
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findVisibleById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    m.id_menu,
                    m.titre,
                    m.description,
                    m.nombre_personnes_minimum,
                    m.prix_minimum,
                    m.conditions,
                    m.stock_disponible,
                    t.libelle AS theme,
                    r.libelle AS regime
                FROM menu AS m
                INNER JOIN theme AS t
                    ON t.id_theme = m.id_theme
                INNER JOIN regime AS r
                    ON r.id_regime = m.id_regime
                WHERE m.id_menu = :id_menu
                  AND m.actif = 1
                  AND m.publie = 1
                  AND m.stock_disponible > 0
                LIMIT 1
            SQL
        );

        $statement->execute([
            'id_menu' => $id,
        ]);

        $menu = $statement->fetch();

        if (!is_array($menu)) {
            return null;
        }

        return [
            'id' => (int) $menu['id_menu'],
            'nom' => (string) $menu['titre'],
            'description' => (string) $menu['description'],
            'prix' => (float) $menu['prix_minimum'],
            'nombre_personnes_minimum' =>
                (int) $menu['nombre_personnes_minimum'],
            'conditions' => (string) $menu['conditions'],
            'stock_disponible' => (int) $menu['stock_disponible'],
            'theme' => (string) $menu['theme'],
            'regime' => (string) $menu['regime'],
            'plats' => $this->findDishesByMenuId($id),
        ];
    }

    /**
     * @return array<int, array{
     *     nom: string,
     *     type: string,
     *     allergenes: array<int, string>
     * }>
     */
    private function findDishesByMenuId(int $menuId): array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    p.id_plat,
                    p.nom,
                    p.type_plat
                FROM plat AS p
                INNER JOIN menu_plat AS mp
                    ON mp.id_plat = p.id_plat
                WHERE mp.id_menu = :id_menu
                  AND p.actif = 1
                ORDER BY
                    CASE p.type_plat
                        WHEN 'entree' THEN 1
                        WHEN 'plat_principal' THEN 2
                        WHEN 'dessert' THEN 3
                        ELSE 4
                    END,
                    p.nom ASC
            SQL
        );

        $statement->execute([
            'id_menu' => $menuId,
        ]);

        $dishes = $statement->fetchAll();

        if (!is_array($dishes)) {
            return [];
        }

        return array_map(
            function (array $dish): array {
                $dishId = (int) $dish['id_plat'];

                return [
                    'nom' => (string) $dish['nom'],
                    'type' => self::formatDishType(
                        (string) $dish['type_plat']
                    ),
                    'allergenes' => $this->findAllergensByDishId(
                        $dishId
                    ),
                ];
            },
            $dishes
        );
    }

    /**
     * @return array<int, string>
     */
    private function findAllergensByDishId(int $dishId): array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT a.libelle
                FROM allergene AS a
                INNER JOIN plat_allergene AS pa
                    ON pa.id_allergene = a.id_allergene
                WHERE pa.id_plat = :id_plat
                ORDER BY a.libelle ASC
            SQL
        );

        $statement->execute([
            'id_plat' => $dishId,
        ]);

        $allergens = $statement->fetchAll(
            PDO::FETCH_COLUMN
        );

        if (!is_array($allergens)) {
            return [];
        }

        return array_map(
            static fn (mixed $allergen): string =>
                (string) $allergen,
            $allergens
        );
    }

    private static function shortDescription(
        string $description
    ): string {
        $description = trim($description);

        if (mb_strlen($description) <= 160) {
            return $description;
        }

        return rtrim(
            mb_substr($description, 0, 157)
        ) . '…';
    }

    private static function formatDishType(string $type): string
    {
        return match ($type) {
            'entree' => 'Entrée',
            'plat_principal' => 'Plat principal',
            'dessert' => 'Dessert',
            default => 'Autre',
        };
    }
}