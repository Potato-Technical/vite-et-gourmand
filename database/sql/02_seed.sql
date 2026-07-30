-- Vite & Gourmand
-- Donnees de reference et de demonstration pour MySQL 8.4 LTS
-- Etape 05.9 - Script SQL d'insertion
--
-- Prerequis :
--   1. Executer sql/01_schema.sql.
--   2. Executer ce fichier sur une base vide.
--   3. Conserver les controles de cles etrangeres actifs.
--
-- Les identifiants sont fixes afin de rendre les relations lisibles et
-- reproductibles. Une seconde execution sans recreation de la base echouera
-- volontairement sur les contraintes UNIQUE et PRIMARY KEY.

USE `vite_et_gourmand`;

START TRANSACTION;

-- ---------------------------------------------------------------------------
-- 1. Donnees de reference
-- ---------------------------------------------------------------------------

INSERT INTO `role` (
    `id_role`,
    `code`,
    `libelle`
) VALUES
    (1, 'utilisateur', 'Utilisateur'),
    (2, 'employe', 'Employé'),
    (3, 'administrateur', 'Administrateur');

INSERT INTO `theme` (
    `id_theme`,
    `libelle`
) VALUES
    (1, 'Classique'),
    (2, 'Noël'),
    (3, 'Pâques'),
    (4, 'Événement');

INSERT INTO `regime` (
    `id_regime`,
    `libelle`
) VALUES
    (1, 'Classique'),
    (2, 'Végétarien'),
    (3, 'Végan');

INSERT INTO `allergene` (
    `id_allergene`,
    `libelle`
) VALUES
    (1, 'Gluten'),
    (2, 'Lait'),
    (3, 'Œufs'),
    (4, 'Poissons'),
    (5, 'Fruits à coque'),
    (6, 'Soja'),
    (7, 'Céleri'),
    (8, 'Arachides');

INSERT INTO `statut_commande` (
    `id_statut_commande`,
    `code`,
    `libelle`,
    `ordre_affichage`,
    `est_terminal`
) VALUES
    (1, 'en_attente', 'En attente', 1, 0),
    (2, 'acceptee', 'Acceptée', 2, 0),
    (3, 'en_preparation', 'En préparation', 3, 0),
    (4, 'en_cours_de_livraison', 'En cours de livraison', 4, 0),
    (5, 'livree', 'Livrée', 5, 0),
    (6, 'en_attente_retour_materiel', 'En attente du retour du matériel', 6, 0),
    (7, 'terminee', 'Terminée', 7, 1),
    (8, 'annulee', 'Annulée', 8, 1);

INSERT INTO `horaire` (
    `id_horaire`,
    `jour_semaine`,
    `ordre_jour`,
    `heure_ouverture`,
    `heure_fermeture`,
    `ferme`
) VALUES
    (1, 'lundi', 1, '09:00:00', '18:00:00', 0),
    (2, 'mardi', 2, '09:00:00', '18:00:00', 0),
    (3, 'mercredi', 3, '09:00:00', '18:00:00', 0),
    (4, 'jeudi', 4, '09:00:00', '18:00:00', 0),
    (5, 'vendredi', 5, '09:00:00', '18:00:00', 0),
    (6, 'samedi', 6, '10:00:00', '17:00:00', 0),
    (7, 'dimanche', 7, NULL, NULL, 1);

-- ---------------------------------------------------------------------------
-- 2. Comptes de demonstration
-- ---------------------------------------------------------------------------

-- Le meme hachage bcrypt de demonstration est utilise pour simplifier les
-- parcours. Aucun mot de passe en clair n'est insere dans la base.
INSERT INTO `utilisateur` (
    `id_utilisateur`,
    `nom`,
    `prenom`,
    `telephone`,
    `email`,
    `adresse_ligne_1`,
    `adresse_ligne_2`,
    `code_postal`,
    `ville`,
    `mot_de_passe_hash`,
    `actif`,
    `date_creation`,
    `date_modification`,
    `id_role`
) VALUES
    (
        1, 'Admin', 'Vite et Gourmand', '0500000001',
        'admin@vite-gourmand.test',
        '1 rue Exemple', NULL, '33000', 'Bordeaux',
        '$2y$10$jkmSls.YWUD/Jg2EUcPABuECu6Z6ZAxnkFain7STeFsmLYcl3yOyK',
        1, '2026-04-01 09:00:00', '2026-04-01 09:00:00', 3
    ),
    (
        2, 'Martin', 'Élodie', '0600000002',
        'employe@vite-gourmand.test',
        '2 rue Exemple', NULL, '33000', 'Bordeaux',
        '$2y$10$jkmSls.YWUD/Jg2EUcPABuECu6Z6ZAxnkFain7STeFsmLYcl3yOyK',
        1, '2026-04-02 09:00:00', '2026-04-02 09:00:00', 2
    ),
    (
        3, 'Bernard', 'Lucas', '0600000003',
        'employe.inactif@vite-gourmand.test',
        '3 rue Exemple', NULL, '33000', 'Bordeaux',
        '$2y$10$jkmSls.YWUD/Jg2EUcPABuECu6Z6ZAxnkFain7STeFsmLYcl3yOyK',
        0, '2026-04-03 09:00:00', '2026-07-01 10:00:00', 2
    ),
    (
        10, 'Durand', 'Alice', '0600000010',
        'alice@vite-gourmand.test',
        '10 rue Exemple', NULL, '33000', 'Bordeaux',
        '$2y$10$jkmSls.YWUD/Jg2EUcPABuECu6Z6ZAxnkFain7STeFsmLYcl3yOyK',
        1, '2026-05-01 09:00:00', '2026-05-01 09:00:00', 1
    ),
    (
        11, 'Benali', 'Karim', '0600000011',
        'karim@vite-gourmand.test',
        '11 rue Exemple', NULL, '33600', 'Pessac',
        '$2y$10$jkmSls.YWUD/Jg2EUcPABuECu6Z6ZAxnkFain7STeFsmLYcl3yOyK',
        1, '2026-05-02 09:00:00', '2026-05-02 09:00:00', 1
    ),
    (
        12, 'Petit', 'Laura', '0600000012',
        'laura@vite-gourmand.test',
        '12 rue Exemple', 'Appartement 2', '33000', 'Bordeaux',
        '$2y$10$jkmSls.YWUD/Jg2EUcPABuECu6Z6ZAxnkFain7STeFsmLYcl3yOyK',
        1, '2026-05-03 09:00:00', '2026-05-03 09:00:00', 1
    ),
    (
        13, 'Moreau', 'Hugo', '0600000013',
        'hugo@vite-gourmand.test',
        '13 rue Exemple', NULL, '33700', 'Mérignac',
        '$2y$10$jkmSls.YWUD/Jg2EUcPABuECu6Z6ZAxnkFain7STeFsmLYcl3yOyK',
        1, '2026-05-04 09:00:00', '2026-05-04 09:00:00', 1
    ),
    (
        14, 'Robert', 'Inès', '0600000014',
        'ines@vite-gourmand.test',
        '14 rue Exemple', NULL, '33000', 'Bordeaux',
        '$2y$10$jkmSls.YWUD/Jg2EUcPABuECu6Z6ZAxnkFain7STeFsmLYcl3yOyK',
        1, '2026-05-05 09:00:00', '2026-05-05 09:00:00', 1
    );

-- Un jeton courant, un jeton expire et un jeton deja utilise.
-- Les valeurs inserees sont uniquement des empreintes SHA-256.
INSERT INTO `jeton_reinitialisation` (
    `id_jeton`,
    `empreinte_jeton`,
    `date_creation`,
    `date_expiration`,
    `date_utilisation`,
    `id_utilisateur`
) VALUES
    (
        1,
        'f946d9d2f885d1088a5410eb4bf47e224660321e1ad8f15e76ee2cdbeeb01c1c',
        CURRENT_TIMESTAMP - INTERVAL 15 MINUTE,
        CURRENT_TIMESTAMP + INTERVAL 45 MINUTE,
        NULL,
        10
    ),
    (
        2,
        'b22aa3c2b13dea5cd49e973eda75381ea24636b649373fb6e1a9582fcff7595f',
        CURRENT_TIMESTAMP - INTERVAL 3 HOUR,
        CURRENT_TIMESTAMP - INTERVAL 1 HOUR,
        NULL,
        11
    ),
    (
        3,
        '547e4fe9aacd9033f9a989b7062c97f409f2a8c18af26bd36659bf9315ec99a9',
        CURRENT_TIMESTAMP - INTERVAL 2 HOUR,
        CURRENT_TIMESTAMP + INTERVAL 1 HOUR,
        CURRENT_TIMESTAMP - INTERVAL 30 MINUTE,
        12
    );

-- ---------------------------------------------------------------------------
-- 3. Catalogue : plats, menus, images et associations
-- ---------------------------------------------------------------------------

INSERT INTO `plat` (
    `id_plat`,
    `nom`,
    `type_plat`,
    `actif`
) VALUES
    (1, 'Velouté de potimarron', 'entree', 1),
    (2, 'Saumon gravlax aux agrumes', 'entree', 1),
    (3, 'Volaille rôtie aux herbes', 'plat_principal', 1),
    (4, 'Lasagnes véganes aux légumes', 'plat_principal', 1),
    (5, 'Risotto aux champignons', 'plat_principal', 1),
    (6, 'Parmentier de canard', 'plat_principal', 1),
    (7, 'Bûche chocolat et noisette', 'dessert', 1),
    (8, 'Tarte fine aux pommes', 'dessert', 1),
    (9, 'Salade de fruits frais', 'dessert', 1),
    (10, 'Mousse au chocolat végane', 'dessert', 1),
    (11, 'Ancienne recette de saison', 'plat_principal', 0);

INSERT INTO `menu` (
    `id_menu`,
    `titre`,
    `description`,
    `nombre_personnes_minimum`,
    `prix_minimum`,
    `conditions`,
    `stock_disponible`,
    `actif`,
    `publie`,
    `date_creation`,
    `date_modification`,
    `id_theme`,
    `id_regime`
) VALUES
    (
        1,
        'Déjeuner bordelais',
        'Un menu traditionnel composé de produits de saison.',
        4,
        120.00,
        'Commande au moins 72 heures à l''avance.',
        12,
        1,
        1,
        '2026-04-10 09:00:00',
        '2026-07-01 09:00:00',
        1,
        1
    ),
    (
        2,
        'Noël prestige',
        'Un menu de fête complet pour les repas de fin d''année.',
        6,
        240.00,
        'Commande au moins 7 jours à l''avance.',
        3,
        1,
        1,
        '2026-04-11 09:00:00',
        '2026-07-02 09:00:00',
        2,
        1
    ),
    (
        3,
        'Printemps végétarien',
        'Un menu végétarien frais adapté aux réceptions printanières.',
        4,
        140.00,
        'Commande au moins 72 heures à l''avance.',
        0,
        1,
        1,
        '2026-04-12 09:00:00',
        '2026-07-03 09:00:00',
        3,
        2
    ),
    (
        4,
        'Réception végane',
        'Un brouillon de menu entièrement végétal.',
        8,
        280.00,
        'Commande au moins 5 jours à l''avance.',
        5,
        1,
        0,
        '2026-04-13 09:00:00',
        '2026-07-04 09:00:00',
        4,
        3
    ),
    (
        5,
        'Menu historique archivé',
        'Un ancien menu conservé pour les données historiques.',
        4,
        100.00,
        'Ce menu n''est plus commercialisé.',
        0,
        0,
        0,
        '2026-04-14 09:00:00',
        '2026-07-05 09:00:00',
        1,
        1
    );

-- Ces chemins devront correspondre aux fichiers places dans les assets du
-- futur front-end.
INSERT INTO `image_menu` (
    `id_image_menu`,
    `chemin_image`,
    `texte_alternatif`,
    `ordre_affichage`,
    `id_menu`
) VALUES
    (
        1,
        '/images/menus/dejeuner-bordelais-1.webp',
        'Présentation du menu Déjeuner bordelais',
        1,
        1
    ),
    (
        2,
        '/images/menus/dejeuner-bordelais-2.webp',
        'Plat principal du menu Déjeuner bordelais',
        2,
        1
    ),
    (
        3,
        '/images/menus/noel-prestige.webp',
        'Présentation du menu Noël prestige',
        1,
        2
    ),
    (
        4,
        '/images/menus/printemps-vegetarien.webp',
        'Présentation du menu Printemps végétarien',
        1,
        3
    ),
    (
        5,
        '/images/menus/menu-historique.webp',
        'Ancienne présentation du menu archivé',
        1,
        5
    );

INSERT INTO `menu_plat` (
    `id_menu`,
    `id_plat`
) VALUES
    (1, 1),
    (1, 3),
    (1, 8),
    (2, 2),
    (2, 6),
    (2, 7),
    (3, 1),
    (3, 5),
    (3, 9),
    (4, 4),
    (4, 10),
    (5, 8),
    (5, 11);

INSERT INTO `plat_allergene` (
    `id_plat`,
    `id_allergene`
) VALUES
    (1, 2),
    (1, 7),
    (2, 4),
    (3, 2),
    (4, 1),
    (4, 6),
    (4, 7),
    (5, 2),
    (6, 2),
    (7, 1),
    (7, 2),
    (7, 3),
    (7, 5),
    (8, 1),
    (8, 2),
    (8, 3),
    (10, 5),
    (10, 6);

-- ---------------------------------------------------------------------------
-- 4. Commandes couvrant les principaux cas metier
-- ---------------------------------------------------------------------------

INSERT INTO `commande` (
    `id_commande`,
    `numero_commande`,
    `date_commande`,
    `nom_client`,
    `prenom_client`,
    `email_client`,
    `telephone_client`,
    `adresse_ligne_1`,
    `adresse_ligne_2`,
    `code_postal`,
    `ville`,
    `date_prestation`,
    `heure_livraison_souhaitee`,
    `nombre_personnes`,
    `titre_menu_applique`,
    `conditions_menu_appliquees`,
    `prix_menu_avant_remise`,
    `taux_remise_applique`,
    `montant_remise`,
    `distance_livraison_km`,
    `frais_livraison`,
    `prix_total`,
    `date_modification`,
    `id_utilisateur`,
    `id_menu`,
    `id_statut_courant`
) VALUES
    (
        1, 'CMD-2026-0001', '2026-07-25 10:00:00',
        'Durand', 'Alice', 'alice@vite-gourmand.test', '0600000010',
        '10 rue Exemple', NULL, '33000', 'Bordeaux',
        '2026-08-05', '12:30:00', 4,
        'Déjeuner bordelais',
        'Commande au moins 72 heures à l''avance.',
        120.00, 0.00, 0.00, NULL, 0.00, 120.00,
        '2026-07-25 10:00:00', 10, 1, 1
    ),
    (
        2, 'CMD-2026-0002', '2026-07-22 11:00:00',
        'Benali', 'Karim', 'karim@vite-gourmand.test', '0600000011',
        '11 rue Exemple', NULL, '33600', 'Pessac',
        '2026-08-02', '13:00:00', 6,
        'Déjeuner bordelais',
        'Commande au moins 72 heures à l''avance.',
        180.00, 0.00, 0.00, 8.50, 10.02, 190.02,
        '2026-07-22 14:00:00', 11, 1, 2
    ),
    (
        3, 'CMD-2026-0003', '2026-07-15 09:00:00',
        'Petit', 'Laura', 'laura@vite-gourmand.test', '0600000012',
        '12 rue Exemple', 'Appartement 2', '33000', 'Bordeaux',
        '2026-07-30', '12:00:00', 10,
        'Déjeuner bordelais',
        'Commande au moins 72 heures à l''avance.',
        300.00, 10.00, 30.00, NULL, 0.00, 270.00,
        '2026-07-29 08:00:00', 12, 1, 3
    ),
    (
        4, 'CMD-2026-0004', '2026-07-10 15:00:00',
        'Moreau', 'Hugo', 'hugo@vite-gourmand.test', '0600000013',
        '13 rue Exemple', NULL, '33700', 'Mérignac',
        '2026-07-31', '19:00:00', 6,
        'Noël prestige',
        'Commande au moins 7 jours à l''avance.',
        240.00, 0.00, 0.00, 9.20, 10.43, 250.43,
        '2026-07-11 10:00:00', 13, 2, 8
    ),
    (
        5, 'CMD-2026-0005', '2026-06-01 10:00:00',
        'Durand', 'Alice', 'alice@vite-gourmand.test', '0600000010',
        '10 rue Exemple', NULL, '33000', 'Bordeaux',
        '2026-06-15', '12:00:00', 12,
        'Noël prestige',
        'Commande au moins 7 jours à l''avance.',
        480.00, 10.00, 48.00, NULL, 0.00, 432.00,
        '2026-06-16 09:00:00', 10, 2, 7
    ),
    (
        6, 'CMD-2026-0006', '2026-05-01 11:00:00',
        'Benali', 'Karim', 'karim@vite-gourmand.test', '0600000011',
        '11 rue Exemple', NULL, '33600', 'Pessac',
        '2026-05-20', '12:00:00', 5,
        'Déjeuner bordelais',
        'Commande au moins 72 heures à l''avance.',
        150.00, 0.00, 0.00, 7.30, 9.31, 159.31,
        '2026-06-03 10:00:00', 11, 1, 7
    ),
    (
        7, 'CMD-2026-0007', '2026-07-01 09:00:00',
        'Petit', 'Laura', 'laura@vite-gourmand.test', '0600000012',
        '12 rue Exemple', 'Appartement 2', '33000', 'Bordeaux',
        '2026-07-20', '12:00:00', 8,
        'Noël prestige',
        'Commande au moins 7 jours à l''avance.',
        320.00, 0.00, 0.00, NULL, 0.00, 320.00,
        '2026-07-21 09:00:00', 12, 2, 6
    ),
    (
        8, 'CMD-2026-0008', '2026-06-01 09:00:00',
        'Moreau', 'Hugo', 'hugo@vite-gourmand.test', '0600000013',
        '8 avenue Exemple', NULL, '33400', 'Talence',
        '2026-06-20', '11:30:00', 9,
        'Déjeuner bordelais',
        'Commande au moins 72 heures à l''avance.',
        270.00, 10.00, 27.00, 5.40, 8.19, 251.19,
        '2026-07-07 09:00:00', 13, 1, 6
    ),
    (
        9, 'CMD-2026-0009', '2026-05-20 10:00:00',
        'Robert', 'Inès', 'ines@vite-gourmand.test', '0600000014',
        '14 rue Exemple', NULL, '33000', 'Bordeaux',
        '2026-06-05', '12:30:00', 11,
        'Noël prestige',
        'Commande au moins 7 jours à l''avance.',
        440.00, 10.00, 44.00, NULL, 0.00, 396.00,
        '2026-06-06 09:00:00', 14, 2, 7
    );

-- ---------------------------------------------------------------------------
-- 5. Historique complet des statuts
-- Le dernier statut de chaque commande correspond a id_statut_courant.
-- L'auteur est NULL uniquement pour l'entree initiale generee par le systeme.
-- ---------------------------------------------------------------------------

INSERT INTO `historique_statut_commande` (
    `id_historique_statut`,
    `date_heure_changement`,
    `commentaire`,
    `id_commande`,
    `id_statut_commande`,
    `id_auteur_changement`
) VALUES
    (1, '2026-07-25 10:00:00', 'Commande créée.', 1, 1, NULL),

    (2, '2026-07-22 11:00:00', 'Commande créée.', 2, 1, NULL),
    (3, '2026-07-22 14:00:00', 'Commande acceptée.', 2, 2, 2),

    (4, '2026-07-15 09:00:00', 'Commande créée.', 3, 1, NULL),
    (5, '2026-07-15 12:00:00', 'Commande acceptée.', 3, 2, 2),
    (6, '2026-07-29 08:00:00', 'Préparation commencée.', 3, 3, 2),

    (7, '2026-07-10 15:00:00', 'Commande créée.', 4, 1, NULL),
    (8, '2026-07-11 10:00:00', 'Commande annulée après contact.', 4, 8, 2),

    (9, '2026-06-01 10:00:00', 'Commande créée.', 5, 1, NULL),
    (10, '2026-06-01 15:00:00', 'Commande acceptée.', 5, 2, 2),
    (11, '2026-06-14 08:00:00', 'Préparation commencée.', 5, 3, 2),
    (12, '2026-06-15 10:00:00', 'Départ en livraison.', 5, 4, 2),
    (13, '2026-06-15 12:00:00', 'Commande livrée.', 5, 5, 2),
    (14, '2026-06-16 09:00:00', 'Commande terminée.', 5, 7, 2),

    (15, '2026-05-01 11:00:00', 'Commande créée.', 6, 1, NULL),
    (16, '2026-05-01 14:00:00', 'Commande acceptée.', 6, 2, 2),
    (17, '2026-05-19 08:00:00', 'Préparation commencée.', 6, 3, 2),
    (18, '2026-05-20 10:00:00', 'Départ en livraison.', 6, 4, 2),
    (19, '2026-05-20 12:00:00', 'Commande livrée.', 6, 5, 2),
    (20, '2026-05-21 09:00:00', 'Retour du matériel attendu.', 6, 6, 2),
    (21, '2026-06-03 10:00:00', 'Matériel rendu, commande terminée.', 6, 7, 2),

    (22, '2026-07-01 09:00:00', 'Commande créée.', 7, 1, NULL),
    (23, '2026-07-01 11:00:00', 'Commande acceptée.', 7, 2, 2),
    (24, '2026-07-19 08:00:00', 'Préparation commencée.', 7, 3, 2),
    (25, '2026-07-20 10:00:00', 'Départ en livraison.', 7, 4, 2),
    (26, '2026-07-20 12:00:00', 'Commande livrée.', 7, 5, 2),
    (27, '2026-07-21 09:00:00', 'Retour du matériel attendu.', 7, 6, 2),

    (28, '2026-06-01 09:00:00', 'Commande créée.', 8, 1, NULL),
    (29, '2026-06-01 12:00:00', 'Commande acceptée.', 8, 2, 2),
    (30, '2026-06-19 08:00:00', 'Préparation commencée.', 8, 3, 2),
    (31, '2026-06-20 09:00:00', 'Départ en livraison.', 8, 4, 2),
    (32, '2026-06-20 11:00:00', 'Commande livrée.', 8, 5, 2),
    (33, '2026-06-22 09:00:00', 'Retour du matériel attendu.', 8, 6, 2),

    (34, '2026-05-20 10:00:00', 'Commande créée.', 9, 1, NULL),
    (35, '2026-05-20 14:00:00', 'Commande acceptée.', 9, 2, 2),
    (36, '2026-06-04 08:00:00', 'Préparation commencée.', 9, 3, 2),
    (37, '2026-06-05 10:00:00', 'Départ en livraison.', 9, 4, 2),
    (38, '2026-06-05 12:00:00', 'Commande livrée.', 9, 5, 2),
    (39, '2026-06-06 09:00:00', 'Commande terminée.', 9, 7, 2);

-- ---------------------------------------------------------------------------
-- 6. Interventions internes
-- ---------------------------------------------------------------------------

INSERT INTO `intervention_commande` (
    `id_intervention`,
    `type_action`,
    `mode_contact`,
    `motif`,
    `date_heure_contact`,
    `date_heure_action`,
    `id_commande`,
    `id_auteur_intervention`
) VALUES
    (
        1,
        'modification',
        'gsm',
        'Augmentation du nombre de personnes de 8 à 10 après accord du client.',
        '2026-07-16 09:00:00',
        '2026-07-16 09:15:00',
        3,
        2
    ),
    (
        2,
        'annulation',
        'courriel',
        'Annulation demandée par le client pour indisponibilité à la date prévue.',
        '2026-07-11 09:30:00',
        '2026-07-11 10:00:00',
        4,
        2
    );

-- ---------------------------------------------------------------------------
-- 7. Prets de materiel
-- ---------------------------------------------------------------------------

INSERT INTO `pret_materiel` (
    `id_pret_materiel`,
    `date_debut_delai_retour`,
    `date_limite_retour`,
    `date_heure_notification_retour`,
    `date_heure_restitution`,
    `montant_frais_du`,
    `id_commande`
) VALUES
    (
        1,
        '2026-05-21 09:00:00',
        '2026-06-04',
        '2026-05-21 09:05:00',
        '2026-06-02 16:00:00',
        0.00,
        6
    ),
    (
        2,
        '2026-07-21 09:00:00',
        '2026-08-04',
        '2026-07-21 09:10:00',
        NULL,
        0.00,
        7
    ),
    (
        3,
        '2026-06-22 09:00:00',
        '2026-07-06',
        '2026-06-22 09:10:00',
        NULL,
        600.00,
        8
    );

-- ---------------------------------------------------------------------------
-- 8. Avis : valide, refuse et en attente
-- ---------------------------------------------------------------------------

INSERT INTO `avis` (
    `id_avis`,
    `note`,
    `commentaire`,
    `statut_moderation`,
    `date_creation`,
    `date_moderation`,
    `id_commande`,
    `id_moderateur`
) VALUES
    (
        1,
        5,
        'Service ponctuel et plats très appréciés.',
        'valide',
        '2026-06-17 10:00:00',
        '2026-06-18 09:00:00',
        5,
        2
    ),
    (
        2,
        2,
        'Commentaire de démonstration refusé pendant la modération.',
        'refuse',
        '2026-06-04 10:00:00',
        '2026-06-05 09:00:00',
        6,
        1
    ),
    (
        3,
        4,
        'Très bon repas, validation de l''avis encore en attente.',
        'en_attente',
        '2026-06-07 10:00:00',
        NULL,
        9,
        NULL
    );

COMMIT;

-- ---------------------------------------------------------------------------
-- 9. Requetes de controle
-- Ces SELECT ne modifient aucune donnee.
-- ---------------------------------------------------------------------------

-- Comptage des 18 tables alimentees.
SELECT 'role' AS `table`, COUNT(*) AS `nombre_lignes` FROM `role`
UNION ALL
SELECT 'utilisateur', COUNT(*) FROM `utilisateur`
UNION ALL
SELECT 'jeton_reinitialisation', COUNT(*) FROM `jeton_reinitialisation`
UNION ALL
SELECT 'theme', COUNT(*) FROM `theme`
UNION ALL
SELECT 'regime', COUNT(*) FROM `regime`
UNION ALL
SELECT 'menu', COUNT(*) FROM `menu`
UNION ALL
SELECT 'image_menu', COUNT(*) FROM `image_menu`
UNION ALL
SELECT 'plat', COUNT(*) FROM `plat`
UNION ALL
SELECT 'allergene', COUNT(*) FROM `allergene`
UNION ALL
SELECT 'menu_plat', COUNT(*) FROM `menu_plat`
UNION ALL
SELECT 'plat_allergene', COUNT(*) FROM `plat_allergene`
UNION ALL
SELECT 'statut_commande', COUNT(*) FROM `statut_commande`
UNION ALL
SELECT 'commande', COUNT(*) FROM `commande`
UNION ALL
SELECT 'historique_statut_commande', COUNT(*) FROM `historique_statut_commande`
UNION ALL
SELECT 'intervention_commande', COUNT(*) FROM `intervention_commande`
UNION ALL
SELECT 'pret_materiel', COUNT(*) FROM `pret_materiel`
UNION ALL
SELECT 'avis', COUNT(*) FROM `avis`
UNION ALL
SELECT 'horaire', COUNT(*) FROM `horaire`;

-- Resultat attendu : zero ligne.
-- Verifie que le statut courant est le dernier statut de l'historique.
SELECT
    `c`.`numero_commande`,
    `c`.`id_statut_courant`,
    `h`.`id_statut_commande` AS `dernier_statut_historique`
FROM `commande` AS `c`
JOIN `historique_statut_commande` AS `h`
    ON `h`.`id_historique_statut` = (
        SELECT `h2`.`id_historique_statut`
        FROM `historique_statut_commande` AS `h2`
        WHERE `h2`.`id_commande` = `c`.`id_commande`
        ORDER BY
            `h2`.`date_heure_changement` DESC,
            `h2`.`id_historique_statut` DESC
        LIMIT 1
    )
WHERE `c`.`id_statut_courant` <> `h`.`id_statut_commande`;

-- Resultat attendu : zero ligne.
-- Verifie le total enregistre pour chaque commande.
SELECT
    `numero_commande`,
    `prix_total`,
    (`prix_menu_avant_remise` - `montant_remise` + `frais_livraison`)
        AS `total_recalcule`
FROM `commande`
WHERE ABS(
    `prix_total`
    - (`prix_menu_avant_remise` - `montant_remise` + `frais_livraison`)
) > 0.01;

-- Resultat attendu : zero ligne.
-- Verifie les conditions minimales des menus publies.
SELECT
    `m`.`id_menu`,
    `m`.`titre`
FROM `menu` AS `m`
WHERE `m`.`publie` = 1
  AND (
      NOT EXISTS (
          SELECT 1
          FROM `image_menu` AS `im`
          WHERE `im`.`id_menu` = `m`.`id_menu`
      )
      OR NOT EXISTS (
          SELECT 1
          FROM `menu_plat` AS `mp`
          JOIN `plat` AS `p`
              ON `p`.`id_plat` = `mp`.`id_plat`
          WHERE `mp`.`id_menu` = `m`.`id_menu`
            AND `p`.`type_plat` = 'plat_principal'
      )
  );

-- Resultat attendu :
--   1 | Dejeuner bordelais | 5
--   2 | Noel prestige      | 3
-- Source de reference du graphique NoSQL : les commandes annulees sont exclues.
SELECT
    `m`.`id_menu`,
    `m`.`titre`,
    COUNT(*) AS `nombre_commandes_non_annulees`
FROM `commande` AS `c`
JOIN `menu` AS `m`
    ON `m`.`id_menu` = `c`.`id_menu`
JOIN `statut_commande` AS `s`
    ON `s`.`id_statut_commande` = `c`.`id_statut_courant`
WHERE `s`.`code` <> 'annulee'
GROUP BY
    `m`.`id_menu`,
    `m`.`titre`
ORDER BY
    `m`.`id_menu`;

-- Resultat attendu :
--   1 | Dejeuner bordelais | 159.31
--   2 | Noel prestige      | 828.00
-- Les frais de retard de materiel sont volontairement exclus : le calcul
-- repose uniquement sur commande.prix_total.
SELECT
    `m`.`id_menu`,
    `m`.`titre`,
    SUM(`c`.`prix_total`) AS `chiffre_affaires`
FROM `commande` AS `c`
JOIN `menu` AS `m`
    ON `m`.`id_menu` = `c`.`id_menu`
JOIN `statut_commande` AS `s`
    ON `s`.`id_statut_commande` = `c`.`id_statut_courant`
WHERE `s`.`code` = 'terminee'
GROUP BY
    `m`.`id_menu`,
    `m`.`titre`
ORDER BY
    `m`.`id_menu`;

-- Resultat attendu : 987.31.
SELECT
    SUM(`c`.`prix_total`) AS `chiffre_affaires_total`
FROM `commande` AS `c`
JOIN `statut_commande` AS `s`
    ON `s`.`id_statut_commande` = `c`.`id_statut_courant`
WHERE `s`.`code` = 'terminee';

-- Resultat attendu :
--   2026-05 | 159.31
--   2026-06 | 828.00
-- Aucune ligne pour juillet 2026, car aucune commande terminee n'a une
-- prestation comprise dans cette periode.
SELECT
    DATE_FORMAT(`c`.`date_prestation`, '%Y-%m') AS `periode`,
    SUM(`c`.`prix_total`) AS `chiffre_affaires`
FROM `commande` AS `c`
JOIN `statut_commande` AS `s`
    ON `s`.`id_statut_commande` = `c`.`id_statut_courant`
WHERE `s`.`code` = 'terminee'
  AND `c`.`date_prestation` >= '2026-05-01'
  AND `c`.`date_prestation` < '2026-08-01'
GROUP BY
    DATE_FORMAT(`c`.`date_prestation`, '%Y-%m')
ORDER BY
    `periode`;
