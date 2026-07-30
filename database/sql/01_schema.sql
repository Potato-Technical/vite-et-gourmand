-- Vite & Gourmand
-- Schéma relationnel MySQL 8.4 LTS

CREATE DATABASE IF NOT EXISTS `vite_et_gourmand`
    CHARACTER SET `utf8mb4`
    COLLATE `utf8mb4_0900_ai_ci`;

USE `vite_et_gourmand`;

CREATE TABLE `role` (
    `id_role` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(30) NOT NULL,
    `libelle` VARCHAR(50) NOT NULL,

    CONSTRAINT `pk_role`
        PRIMARY KEY (`id_role`),
    CONSTRAINT `uq_role_code`
        UNIQUE (`code`),
    CONSTRAINT `uq_role_libelle`
        UNIQUE (`libelle`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `utilisateur` (
    `id_utilisateur` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(100) NULL,
    `prenom` VARCHAR(100) NULL,
    `telephone` VARCHAR(30) NULL,
    `email` VARCHAR(254) NOT NULL,
    `adresse_ligne_1` VARCHAR(255) NULL,
    `adresse_ligne_2` VARCHAR(255) NULL,
    `code_postal` VARCHAR(20) NULL,
    `ville` VARCHAR(100) NULL,
    `mot_de_passe_hash` VARCHAR(255) NOT NULL,
    `actif` BOOLEAN NOT NULL DEFAULT TRUE,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Mise à jour par le serveur à chaque modification',
    `id_role` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_utilisateur`
        PRIMARY KEY (`id_utilisateur`),
    CONSTRAINT `uq_utilisateur_email`
        UNIQUE (`email`),
    CONSTRAINT `ck_utilisateur_actif`
        CHECK (`actif` IN (0, 1))
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `jeton_reinitialisation` (
    `id_jeton` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `empreinte_jeton` VARCHAR(255) NOT NULL,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_expiration` DATETIME NOT NULL,
    `date_utilisation` DATETIME NULL,
    `id_utilisateur` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_jeton_reinitialisation`
        PRIMARY KEY (`id_jeton`),
    CONSTRAINT `uq_jeton_empreinte`
        UNIQUE (`empreinte_jeton`),
    CONSTRAINT `uq_jeton_utilisateur`
        UNIQUE (`id_utilisateur`),
    CONSTRAINT `ck_jeton_expiration`
        CHECK (`date_expiration` > `date_creation`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `theme` (
    `id_theme` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `libelle` VARCHAR(100) NOT NULL,

    CONSTRAINT `pk_theme`
        PRIMARY KEY (`id_theme`),
    CONSTRAINT `uq_theme_libelle`
        UNIQUE (`libelle`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `regime` (
    `id_regime` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `libelle` VARCHAR(100) NOT NULL,

    CONSTRAINT `pk_regime`
        PRIMARY KEY (`id_regime`),
    CONSTRAINT `uq_regime_libelle`
        UNIQUE (`libelle`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `menu` (
    `id_menu` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `titre` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `nombre_personnes_minimum` SMALLINT UNSIGNED NOT NULL,
    `prix_minimum` DECIMAL(10, 2) NOT NULL,
    `conditions` TEXT NOT NULL,
    `stock_disponible` INT UNSIGNED NOT NULL DEFAULT 0,
    `actif` BOOLEAN NOT NULL DEFAULT TRUE,
    `publie` BOOLEAN NOT NULL DEFAULT FALSE,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Mise à jour par le serveur à chaque modification',
    `id_theme` BIGINT UNSIGNED NOT NULL,
    `id_regime` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_menu`
        PRIMARY KEY (`id_menu`),
    CONSTRAINT `ck_menu_nb_personnes`
        CHECK (`nombre_personnes_minimum` > 0),
    CONSTRAINT `ck_menu_prix`
        CHECK (`prix_minimum` >= 0),
    CONSTRAINT `ck_menu_stock`
        CHECK (`stock_disponible` >= 0),
    CONSTRAINT `ck_menu_actif`
        CHECK (`actif` IN (0, 1)),
    CONSTRAINT `ck_menu_publie`
        CHECK (`publie` IN (0, 1)),
    CONSTRAINT `ck_menu_archivage`
        CHECK (`actif` = 1 OR `publie` = 0),

    INDEX `idx_menu_visibilite` (`actif`, `publie`, `stock_disponible`),
    INDEX `idx_menu_prix` (`prix_minimum`),
    INDEX `idx_menu_nb_personnes` (`nombre_personnes_minimum`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `image_menu` (
    `id_image_menu` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `chemin_image` VARCHAR(2048) NOT NULL,
    `texte_alternatif` VARCHAR(255) NOT NULL,
    `ordre_affichage` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `id_menu` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_image_menu`
        PRIMARY KEY (`id_image_menu`),
    CONSTRAINT `ck_image_menu_ordre`
        CHECK (`ordre_affichage` >= 0),

    INDEX `idx_image_menu_ordre` (`id_menu`, `ordre_affichage`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `plat` (
    `id_plat` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(150) NOT NULL,
    `type_plat` VARCHAR(30) NOT NULL,
    `actif` BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT `pk_plat`
        PRIMARY KEY (`id_plat`),
    CONSTRAINT `ck_plat_type`
        CHECK (`type_plat` IN ('entree', 'plat_principal', 'dessert')),
    CONSTRAINT `ck_plat_actif`
        CHECK (`actif` IN (0, 1))
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `allergene` (
    `id_allergene` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `libelle` VARCHAR(100) NOT NULL,

    CONSTRAINT `pk_allergene`
        PRIMARY KEY (`id_allergene`),
    CONSTRAINT `uq_allergene_libelle`
        UNIQUE (`libelle`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `menu_plat` (
    `id_menu` BIGINT UNSIGNED NOT NULL,
    `id_plat` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_menu_plat`
        PRIMARY KEY (`id_menu`, `id_plat`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `plat_allergene` (
    `id_plat` BIGINT UNSIGNED NOT NULL,
    `id_allergene` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_plat_allergene`
        PRIMARY KEY (`id_plat`, `id_allergene`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `statut_commande` (
    `id_statut_commande` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(40) NOT NULL,
    `libelle` VARCHAR(100) NOT NULL,
    `ordre_affichage` TINYINT UNSIGNED NOT NULL,
    `est_terminal` BOOLEAN NOT NULL DEFAULT FALSE,

    CONSTRAINT `pk_statut_commande`
        PRIMARY KEY (`id_statut_commande`),
    CONSTRAINT `uq_statut_commande_code`
        UNIQUE (`code`),
    CONSTRAINT `uq_statut_commande_libelle`
        UNIQUE (`libelle`),
    CONSTRAINT `ck_statut_ordre`
        CHECK (`ordre_affichage` > 0),
    CONSTRAINT `ck_statut_est_terminal`
        CHECK (`est_terminal` IN (0, 1))
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `commande` (
    `id_commande` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `numero_commande` VARCHAR(30) NOT NULL,
    `date_commande` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `nom_client` VARCHAR(100) NOT NULL,
    `prenom_client` VARCHAR(100) NOT NULL,
    `email_client` VARCHAR(254) NOT NULL,
    `telephone_client` VARCHAR(30) NOT NULL,
    `adresse_ligne_1` VARCHAR(255) NOT NULL,
    `adresse_ligne_2` VARCHAR(255) NULL,
    `code_postal` VARCHAR(20) NOT NULL,
    `ville` VARCHAR(100) NOT NULL,
    `date_prestation` DATE NOT NULL,
    `heure_livraison_souhaitee` TIME NOT NULL,
    `nombre_personnes` SMALLINT UNSIGNED NOT NULL,
    `titre_menu_applique` VARCHAR(150) NOT NULL,
    `conditions_menu_appliquees` TEXT NOT NULL,
    `prix_menu_avant_remise` DECIMAL(10, 2) NOT NULL,
    `taux_remise_applique` DECIMAL(5, 2) NOT NULL,
    `montant_remise` DECIMAL(10, 2) NOT NULL,
    `distance_livraison_km` DECIMAL(8, 2) NULL,
    `frais_livraison` DECIMAL(10, 2) NOT NULL,
    `prix_total` DECIMAL(10, 2) NOT NULL,
    `date_modification` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Mise à jour par le serveur à chaque modification',
    `id_utilisateur` BIGINT UNSIGNED NOT NULL,
    `id_menu` BIGINT UNSIGNED NOT NULL,
    `id_statut_courant` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_commande`
        PRIMARY KEY (`id_commande`),
    CONSTRAINT `uq_commande_numero`
        UNIQUE (`numero_commande`),
    CONSTRAINT `ck_commande_nb_personnes`
        CHECK (`nombre_personnes` > 0),
    CONSTRAINT `ck_commande_prix_menu`
        CHECK (`prix_menu_avant_remise` >= 0),
    CONSTRAINT `ck_commande_taux_remise`
        CHECK (`taux_remise_applique` IN (0, 10)),
    CONSTRAINT `ck_commande_montant_remise`
        CHECK (`montant_remise` >= 0),
    CONSTRAINT `ck_commande_distance`
        CHECK (`distance_livraison_km` IS NULL OR `distance_livraison_km` >= 0),
    CONSTRAINT `ck_commande_frais_livraison`
        CHECK (`frais_livraison` >= 0),
    CONSTRAINT `ck_commande_prix_total`
        CHECK (`prix_total` >= 0),
    CONSTRAINT `ck_commande_date_prestation`
        CHECK (`date_prestation` >= DATE(`date_commande`)),

    INDEX `idx_commande_utilisateur_date` (`id_utilisateur`, `date_commande`),
    INDEX `idx_commande_statut_date` (`id_statut_courant`, `date_commande`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `historique_statut_commande` (
    `id_historique_statut` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `date_heure_changement` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `commentaire` TEXT NULL,
    `id_commande` BIGINT UNSIGNED NOT NULL,
    `id_statut_commande` BIGINT UNSIGNED NOT NULL,
    `id_auteur_changement` BIGINT UNSIGNED NULL,

    CONSTRAINT `pk_historique_statut_commande`
        PRIMARY KEY (`id_historique_statut`),

    INDEX `idx_historique_commande_date`
        (`id_commande`, `date_heure_changement`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `intervention_commande` (
    `id_intervention` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type_action` VARCHAR(20) NOT NULL,
    `mode_contact` VARCHAR(20) NOT NULL,
    `motif` TEXT NOT NULL,
    `date_heure_contact` DATETIME NOT NULL,
    `date_heure_action` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id_commande` BIGINT UNSIGNED NOT NULL,
    `id_auteur_intervention` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_intervention_commande`
        PRIMARY KEY (`id_intervention`),
    CONSTRAINT `ck_intervention_type`
        CHECK (`type_action` IN ('modification', 'annulation')),
    CONSTRAINT `ck_intervention_contact`
        CHECK (`mode_contact` IN ('gsm', 'courriel')),
    CONSTRAINT `ck_intervention_dates`
        CHECK (`date_heure_contact` <= `date_heure_action`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `pret_materiel` (
    `id_pret_materiel` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `date_debut_delai_retour` DATETIME NULL,
    `date_limite_retour` DATE NULL,
    `date_heure_notification_retour` DATETIME NULL,
    `date_heure_restitution` DATETIME NULL,
    `montant_frais_du` DECIMAL(10, 2) NOT NULL DEFAULT 0,
    `id_commande` BIGINT UNSIGNED NOT NULL,

    CONSTRAINT `pk_pret_materiel`
        PRIMARY KEY (`id_pret_materiel`),
    CONSTRAINT `uq_pret_commande`
        UNIQUE (`id_commande`),
    CONSTRAINT `ck_pret_montant_frais`
        CHECK (`montant_frais_du` IN (0, 600))
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `avis` (
    `id_avis` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `note` TINYINT UNSIGNED NOT NULL,
    `commentaire` TEXT NOT NULL,
    `statut_moderation` VARCHAR(20) NOT NULL DEFAULT 'en_attente',
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_moderation` DATETIME NULL,
    `id_commande` BIGINT UNSIGNED NOT NULL,
    `id_moderateur` BIGINT UNSIGNED NULL,

    CONSTRAINT `pk_avis`
        PRIMARY KEY (`id_avis`),
    CONSTRAINT `uq_avis_commande`
        UNIQUE (`id_commande`),
    CONSTRAINT `ck_avis_note`
        CHECK (`note` BETWEEN 1 AND 5),
    CONSTRAINT `ck_avis_statut`
        CHECK (`statut_moderation` IN ('en_attente', 'valide', 'refuse')),

    INDEX `idx_avis_moderation_date` (`statut_moderation`, `date_creation`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `horaire` (
    `id_horaire` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `jour_semaine` VARCHAR(10) NOT NULL,
    `ordre_jour` TINYINT UNSIGNED NOT NULL,
    `heure_ouverture` TIME NULL,
    `heure_fermeture` TIME NULL,
    `ferme` BOOLEAN NOT NULL,

    CONSTRAINT `pk_horaire`
        PRIMARY KEY (`id_horaire`),
    CONSTRAINT `uq_horaire_jour`
        UNIQUE (`jour_semaine`),
    CONSTRAINT `ck_horaire_jour`
        CHECK (`jour_semaine` IN (
            'lundi',
            'mardi',
            'mercredi',
            'jeudi',
            'vendredi',
            'samedi',
            'dimanche'
        )),
    CONSTRAINT `ck_horaire_ordre`
        CHECK (`ordre_jour` BETWEEN 1 AND 7),
    CONSTRAINT `ck_horaire_ferme`
        CHECK (`ferme` IN (0, 1)),
    CONSTRAINT `ck_horaire_plage`
        CHECK (
            `ferme` = 1
            OR (
                `heure_ouverture` IS NOT NULL
                AND `heure_fermeture` IS NOT NULL
                AND `heure_fermeture` > `heure_ouverture`
            )
        ),

    INDEX `idx_horaire_ordre` (`ordre_jour`)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `utilisateur`
    ADD CONSTRAINT `fk_utilisateur_role`
        FOREIGN KEY (`id_role`)
        REFERENCES `role` (`id_role`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT;

ALTER TABLE `jeton_reinitialisation`
    ADD CONSTRAINT `fk_jeton_utilisateur`
        FOREIGN KEY (`id_utilisateur`)
        REFERENCES `utilisateur` (`id_utilisateur`)
        ON DELETE CASCADE
        ON UPDATE RESTRICT;

ALTER TABLE `menu`
    ADD CONSTRAINT `fk_menu_theme`
        FOREIGN KEY (`id_theme`)
        REFERENCES `theme` (`id_theme`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_menu_regime`
        FOREIGN KEY (`id_regime`)
        REFERENCES `regime` (`id_regime`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT;

ALTER TABLE `image_menu`
    ADD CONSTRAINT `fk_image_menu`
        FOREIGN KEY (`id_menu`)
        REFERENCES `menu` (`id_menu`)
        ON DELETE CASCADE
        ON UPDATE RESTRICT;

ALTER TABLE `menu_plat`
    ADD CONSTRAINT `fk_menu_plat_menu`
        FOREIGN KEY (`id_menu`)
        REFERENCES `menu` (`id_menu`)
        ON DELETE CASCADE
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_menu_plat_plat`
        FOREIGN KEY (`id_plat`)
        REFERENCES `plat` (`id_plat`)
        ON DELETE CASCADE
        ON UPDATE RESTRICT;

ALTER TABLE `plat_allergene`
    ADD CONSTRAINT `fk_plat_allergene_plat`
        FOREIGN KEY (`id_plat`)
        REFERENCES `plat` (`id_plat`)
        ON DELETE CASCADE
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_plat_allergene_allergene`
        FOREIGN KEY (`id_allergene`)
        REFERENCES `allergene` (`id_allergene`)
        ON DELETE CASCADE
        ON UPDATE RESTRICT;

ALTER TABLE `commande`
    ADD CONSTRAINT `fk_commande_utilisateur`
        FOREIGN KEY (`id_utilisateur`)
        REFERENCES `utilisateur` (`id_utilisateur`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_commande_menu`
        FOREIGN KEY (`id_menu`)
        REFERENCES `menu` (`id_menu`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_commande_statut`
        FOREIGN KEY (`id_statut_courant`)
        REFERENCES `statut_commande` (`id_statut_commande`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT;

ALTER TABLE `historique_statut_commande`
    ADD CONSTRAINT `fk_historique_commande`
        FOREIGN KEY (`id_commande`)
        REFERENCES `commande` (`id_commande`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_historique_statut`
        FOREIGN KEY (`id_statut_commande`)
        REFERENCES `statut_commande` (`id_statut_commande`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_historique_auteur`
        FOREIGN KEY (`id_auteur_changement`)
        REFERENCES `utilisateur` (`id_utilisateur`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT;

ALTER TABLE `intervention_commande`
    ADD CONSTRAINT `fk_intervention_commande`
        FOREIGN KEY (`id_commande`)
        REFERENCES `commande` (`id_commande`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_intervention_auteur`
        FOREIGN KEY (`id_auteur_intervention`)
        REFERENCES `utilisateur` (`id_utilisateur`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT;

ALTER TABLE `pret_materiel`
    ADD CONSTRAINT `fk_pret_commande`
        FOREIGN KEY (`id_commande`)
        REFERENCES `commande` (`id_commande`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT;

ALTER TABLE `avis`
    ADD CONSTRAINT `fk_avis_commande`
        FOREIGN KEY (`id_commande`)
        REFERENCES `commande` (`id_commande`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,
    ADD CONSTRAINT `fk_avis_moderateur`
        FOREIGN KEY (`id_moderateur`)
        REFERENCES `utilisateur` (`id_utilisateur`)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT;
