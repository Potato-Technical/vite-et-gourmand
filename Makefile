# Utiliser bash explicitement pour les scripts
SHELL := /bin/bash

# Liste des commandes du Makefile
.PHONY: \
	up build rebuild down down-v ps logs \
	sh-web sh-db sh-mongo \
	composer-install composer-update \
	check \
	db-reset db-schema db-seed db-full \
	init

# Démarre les conteneurs en arrière-plan
up:
	docker compose up -d

# Construit les images Docker
build:
	docker compose build

# Reconstruit les images et démarre les conteneurs
rebuild:
	docker compose up -d --build

# Arrête les conteneurs sans supprimer les volumes
down:
	docker compose down

# Arrête les conteneurs et supprime les volumes
# Attention : les données MySQL et MongoDB seront supprimées
down-v:
	docker compose down -v

# Affiche l'état des conteneurs
ps:
	docker compose ps

# Affiche les logs Docker en temps réel
logs:
	docker compose logs -f --tail=200

# Ouvre un terminal dans le conteneur PHP / Apache
sh-web:
	docker compose exec web bash

# Ouvre MySQL avec l'utilisateur de l'application
sh-db:
	docker compose exec db sh -lc 'mysql -u "$$MYSQL_USER" -p"$$MYSQL_PASSWORD" "$$MYSQL_DATABASE"'

# Ouvre MongoDB avec le compte administrateur
sh-mongo:
	docker compose exec mongo sh -lc 'mongosh --username "$$MONGO_INITDB_ROOT_USERNAME" --password "$$MONGO_INITDB_ROOT_PASSWORD" --authenticationDatabase admin'

# Installe les dépendances PHP
composer-install:
	docker compose exec web composer install

# Met à jour les dépendances PHP
composer-update:
	docker compose exec web composer update

# Lance les vérifications rapides de l'application
check:
	docker compose exec -T web php scripts/sanity_check.php

# Supprime et recrée la base de données MySQL
db-reset:
	./scripts/db_reset.sh

# Applique le schéma SQL
db-schema:
	./scripts/db_schema.sh

# Ajoute les données de test
db-seed:
	./scripts/db_seed.sh

# Réinitialise complètement la base : reset + schéma + données
db-full:
	./scripts/db_full_reset.sh

# Initialise complètement le projet
# Build Docker, démarre les services, installe Composer et prépare la base
init: rebuild composer-install db-full