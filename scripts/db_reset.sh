#!/usr/bin/env bash

# Arrête le script dès qu'une commande échoue, qu'une variable manque
# ou qu'une commande d'un pipeline retourne une erreur.
set -euo pipefail

# Détermine automatiquement la racine du projet,
# même si le script est lancé depuis un autre dossier.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
ENV_FILE="${PROJECT_ROOT}/.env"

# Charge les variables définies dans le fichier .env.
if [[ ! -f "${ENV_FILE}" ]]; then
    echo "[ERREUR] Fichier .env introuvable : ${ENV_FILE}" >&2
    exit 1
fi

set -a
# shellcheck disable=SC1090
source "${ENV_FILE}"
set +a

# Vérifie la présence des variables nécessaires.
: "${DB_NAME:?Variable DB_NAME manquante dans .env}"
: "${DB_ROOT_PASS:?Variable DB_ROOT_PASS manquante dans .env}"

echo "[DB] Suppression et recréation de la base ${DB_NAME}"

# L'utilisateur root est nécessaire pour supprimer et recréer la base.
docker compose \
    --project-directory "${PROJECT_ROOT}" \
    exec -T db \
    mysql -uroot -p"${DB_ROOT_PASS}" \
    -e "
        DROP DATABASE IF EXISTS \`${DB_NAME}\`;

        CREATE DATABASE \`${DB_NAME}\`
            CHARACTER SET utf8mb4
            COLLATE utf8mb4_unicode_ci;
    "

echo "[OK] Base ${DB_NAME} recréée"