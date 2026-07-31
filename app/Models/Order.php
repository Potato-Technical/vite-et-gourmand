<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\PricingService;
use InvalidArgumentException;
use PDO;
use RuntimeException;
use Throwable;

final class Order
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly PricingService $pricingService
    ) {
    }

    /**
     * @param array{
     *     id_menu: int,
     *     telephone: string,
     *     adresse_ligne_1: string,
     *     adresse_ligne_2: string|null,
     *     code_postal: string,
     *     ville: string,
     *     date_prestation: string,
     *     heure_livraison_souhaitee: string,
     *     nombre_personnes: int
     * } $input
     *
     * @return array{
     *     id_commande: int,
     *     numero_commande: string,
     *     price_before_discount_cents: int,
     *     discount_rate: int,
     *     discount_amount_cents: int,
     *     delivery_fee_cents: int,
     *     total_cents: int
     * }
     */
    public function place(int $userId, array $input): array
    {
        $this->validateInput($userId, $input);

        try {
            $this->pdo->beginTransaction();

            $user = $this->findActiveUserForOrder($userId);

            $menu = $this->findMenuForUpdate(
                $input['id_menu']
            );

            $minimumPriceCents = self::decimalToCents(
                (string) $menu['prix_minimum']
            );

            $pricing = $this->pricingService->calculate(
                $minimumPriceCents,
                (int) $menu['nombre_personnes_minimum'],
                $input['nombre_personnes'],
                $input['ville']
            );

            $statusId = $this->findStatusId('en_attente');

            $orderNumber = self::generateOrderNumber();

            $this->decrementMenuStock(
                $input['id_menu']
            );

            $orderId = $this->insertOrder(
                $userId,
                $input,
                $user,
                $menu,
                $pricing,
                $statusId,
                $orderNumber
            );

            $this->insertInitialStatusHistory(
                $orderId,
                $statusId,
                $userId
            );

            $this->pdo->commit();

            return [
                'id_commande' => $orderId,
                'numero_commande' => $orderNumber,
                ...$pricing,
            ];
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function findActiveUserForOrder(int $userId): array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    id_utilisateur,
                    nom,
                    prenom,
                    email,
                    telephone,
                    actif
                FROM utilisateur
                WHERE id_utilisateur = :id_utilisateur
                LIMIT 1
            SQL
        );

        $statement->execute([
            'id_utilisateur' => $userId,
        ]);

        $user = $statement->fetch();

        if (
            !is_array($user)
            || (int) $user['actif'] !== 1
        ) {
            throw new RuntimeException(
                'Le compte utilisateur est introuvable ou inactif.'
            );
        }

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function findMenuForUpdate(int $menuId): array
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    id_menu,
                    titre,
                    conditions,
                    nombre_personnes_minimum,
                    prix_minimum,
                    stock_disponible,
                    actif,
                    publie
                FROM menu
                WHERE id_menu = :id_menu
                LIMIT 1
                FOR UPDATE
            SQL
        );

        $statement->execute([
            'id_menu' => $menuId,
        ]);

        $menu = $statement->fetch();

        if (!is_array($menu)) {
            throw new RuntimeException(
                'Le menu demandé est introuvable.'
            );
        }

        if (
            (int) $menu['actif'] !== 1
            || (int) $menu['publie'] !== 1
        ) {
            throw new RuntimeException(
                'Ce menu ne peut pas être commandé.'
            );
        }

        if ((int) $menu['stock_disponible'] <= 0) {
            throw new RuntimeException(
                'Ce menu est actuellement en rupture de stock.'
            );
        }

        return $menu;
    }

    private function findStatusId(string $code): int
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT id_statut_commande
                FROM statut_commande
                WHERE code = :code
                LIMIT 1
            SQL
        );

        $statement->execute([
            'code' => $code,
        ]);

        $statusId = $statement->fetchColumn();

        if ($statusId === false) {
            throw new RuntimeException(
                sprintf(
                    'Le statut de commande "%s" est introuvable.',
                    $code
                )
            );
        }

        return (int) $statusId;
    }

    private function decrementMenuStock(int $menuId): void
    {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                UPDATE menu
                SET
                    stock_disponible = stock_disponible - 1,
                    date_modification = CURRENT_TIMESTAMP
                WHERE id_menu = :id_menu
                  AND stock_disponible > 0
                  AND actif = 1
                  AND publie = 1
            SQL
        );

        $statement->execute([
            'id_menu' => $menuId,
        ]);

        if ($statement->rowCount() !== 1) {
            throw new RuntimeException(
                'Le stock du menu a changé. La commande ne peut pas être créée.'
            );
        }
    }

    /**
     * @param array{
     *     id_menu: int,
     *     telephone: string,
     *     adresse_ligne_1: string,
     *     adresse_ligne_2: string|null,
     *     code_postal: string,
     *     ville: string,
     *     date_prestation: string,
     *     heure_livraison_souhaitee: string,
     *     nombre_personnes: int
     * } $data
     * @param array<string, mixed> $user
     * @param array<string, mixed> $menu
     * @param array{
     *     price_before_discount_cents: int,
     *     discount_rate: int,
     *     discount_amount_cents: int,
     *     delivery_fee_cents: int,
     *     total_cents: int
     * } $pricing
     */
    private function insertOrder(
        int $userId,
        array $data,
        array $user,
        array $menu,
        array $pricing,
        int $statusId,
        string $orderNumber
    ): int {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                INSERT INTO commande (
                    numero_commande,
                    nom_client,
                    prenom_client,
                    email_client,
                    telephone_client,
                    adresse_ligne_1,
                    adresse_ligne_2,
                    code_postal,
                    ville,
                    date_prestation,
                    heure_livraison_souhaitee,
                    nombre_personnes,
                    titre_menu_applique,
                    conditions_menu_appliquees,
                    prix_menu_avant_remise,
                    taux_remise_applique,
                    montant_remise,
                    distance_livraison_km,
                    frais_livraison,
                    prix_total,
                    id_utilisateur,
                    id_menu,
                    id_statut_courant
                ) VALUES (
                    :numero_commande,
                    :nom_client,
                    :prenom_client,
                    :email_client,
                    :telephone_client,
                    :adresse_ligne_1,
                    :adresse_ligne_2,
                    :code_postal,
                    :ville,
                    :date_prestation,
                    :heure_livraison_souhaitee,
                    :nombre_personnes,
                    :titre_menu_applique,
                    :conditions_menu_appliquees,
                    :prix_menu_avant_remise,
                    :taux_remise_applique,
                    :montant_remise,
                    NULL,
                    :frais_livraison,
                    :prix_total,
                    :id_utilisateur,
                    :id_menu,
                    :id_statut_courant
                )
            SQL
        );

        $statement->execute([
            'numero_commande' => $orderNumber,
            'nom_client' => trim((string) $user['nom']),
            'prenom_client' => trim((string) $user['prenom']),
            'email_client' => strtolower(
                trim((string) $user['email'])
            ),
            'telephone_client' => trim($data['telephone']),
            'adresse_ligne_1' => trim(
                $data['adresse_ligne_1']
            ),
            'adresse_ligne_2' => self::normalizeNullableString(
                $data['adresse_ligne_2']
            ),
            'code_postal' => trim($data['code_postal']),
            'ville' => trim($data['ville']),
            'date_prestation' => $data['date_prestation'],
            'heure_livraison_souhaitee' =>
                $data['heure_livraison_souhaitee'],
            'nombre_personnes' => $data['nombre_personnes'],
            'titre_menu_applique' => (string) $menu['titre'],
            'conditions_menu_appliquees' =>
                (string) $menu['conditions'],
            'prix_menu_avant_remise' => self::centsToDecimal(
                $pricing['price_before_discount_cents']
            ),
            'taux_remise_applique' => self::centsToDecimal(
                $pricing['discount_rate'] * 100
            ),
            'montant_remise' => self::centsToDecimal(
                $pricing['discount_amount_cents']
            ),
            'frais_livraison' => self::centsToDecimal(
                $pricing['delivery_fee_cents']
            ),
            'prix_total' => self::centsToDecimal(
                $pricing['total_cents']
            ),
            'id_utilisateur' => $userId,
            'id_menu' => $data['id_menu'],
            'id_statut_courant' => $statusId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    private function insertInitialStatusHistory(
        int $orderId,
        int $statusId,
        int $userId
    ): void {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                INSERT INTO historique_statut_commande (
                    commentaire,
                    id_commande,
                    id_statut_commande,
                    id_auteur_changement
                ) VALUES (
                    :commentaire,
                    :id_commande,
                    :id_statut_commande,
                    :id_auteur_changement
                )
            SQL
        );

        $statement->execute([
            'commentaire' => 'Commande créée par le client.',
            'id_commande' => $orderId,
            'id_statut_commande' => $statusId,
            'id_auteur_changement' => $userId,
        ]);
    }

    /**
     * @param array{
     *     id_menu: int,
     *     telephone: string,
     *     adresse_ligne_1: string,
     *     adresse_ligne_2: string|null,
     *     code_postal: string,
     *     ville: string,
     *     date_prestation: string,
     *     heure_livraison_souhaitee: string,
     *     nombre_personnes: int
     * } $data
     */
    private function validateInput(int $userId, array $data): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                'L’identifiant utilisateur est invalide.'
            );
        }

        if ($data['id_menu'] <= 0) {
            throw new InvalidArgumentException(
                'L’identifiant du menu est invalide.'
            );
        }

        if ($data['nombre_personnes'] <= 0) {
            throw new InvalidArgumentException(
                'Le nombre de personnes est invalide.'
            );
        }

        $requiredStrings = [
            'telephone' => 'Le téléphone est obligatoire.',
            'adresse_ligne_1' => 'L’adresse est obligatoire.',
            'code_postal' => 'Le code postal est obligatoire.',
            'ville' => 'La ville est obligatoire.',
            'date_prestation' =>
                'La date de prestation est obligatoire.',
            'heure_livraison_souhaitee' =>
                'L’heure de livraison est obligatoire.',
        ];

        foreach ($requiredStrings as $key => $message) {
            if (trim($data[$key]) === '') {
                throw new InvalidArgumentException($message);
            }
        }
    }

    private static function decimalToCents(string $amount): int
    {
        $amount = trim($amount);

        if (!preg_match('/^\d+(?:\.\d{1,2})?$/', $amount)) {
            throw new RuntimeException(
                'Le prix du menu possède un format invalide.'
            );
        }

        [$euros, $cents] = array_pad(
            explode('.', $amount, 2),
            2,
            '0'
        );

        $cents = str_pad($cents, 2, '0');

        return ((int) $euros * 100) + (int) $cents;
    }

    private static function centsToDecimal(int $cents): string
    {
        if ($cents < 0) {
            throw new InvalidArgumentException(
                'Un montant monétaire ne peut pas être négatif.'
            );
        }

        return sprintf(
            '%d.%02d',
            intdiv($cents, 100),
            $cents % 100
        );
    }

    private static function generateOrderNumber(): string
    {
        return sprintf(
            'VG-%s-%s',
            date('Ymd'),
            strtoupper(bin2hex(random_bytes(6)))
        );
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

    /**
     * @return array<string, mixed>|null
     */
    public function findOwnedByNumber(
        string $number,
        int $userId
    ): ?array {
        $statement = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    id_commande,
                    numero_commande,
                    date_commande,
                    nom_client,
                    prenom_client,
                    email_client,
                    telephone_client,
                    adresse_ligne_1,
                    adresse_ligne_2,
                    code_postal,
                    ville,
                    date_prestation,
                    heure_livraison_souhaitee,
                    nombre_personnes,
                    titre_menu_applique,
                    conditions_menu_appliquees,
                    prix_menu_avant_remise,
                    taux_remise_applique,
                    montant_remise,
                    distance_livraison_km,
                    frais_livraison,
                    prix_total,
                    id_utilisateur,
                    id_menu,
                    id_statut_courant
                FROM commande
                WHERE numero_commande = :numero_commande
                AND id_utilisateur = :id_utilisateur
                LIMIT 1
            SQL
        );

        $statement->execute([
            'numero_commande' => trim($number),
            'id_utilisateur' => $userId,
        ]);

        $order = $statement->fetch();

        return is_array($order) ? $order : null;
    }
}