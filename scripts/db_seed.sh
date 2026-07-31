#!/usr/bin/env bash

# Arrête le script dès qu'une commande échoue, qu'une variable manque
# ou qu'une commande d'un pipeline retourne une erreur.
set -euo pipefail

# Détermine automatiquement les chemins du projet.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
ENV_FILE="${PROJECT_ROOT}/.env"
SEED_FILE="${PROJECT_ROOT}/database/sql/02_seed.sql"

# Vérifie la présence du fichier .env.
if [[ ! -f "${ENV_FILE}" ]]; then
    echo "[ERREUR] Fichier .env introuvable : ${ENV_FILE}" >&2
    exit 1
fi

# Charge les variables du projet.
set -a
# shellcheck disable=SC1090
source "${ENV_FILE}"
set +a

# Vérifie les variables nécessaires à la connexion MySQL.
: "${DB_NAME:?Variable DB_NAME manquante dans .env}"
: "${DB_USER:?Variable DB_USER manquante dans .env}"
: "${DB_PASS:?Variable DB_PASS manquante dans .env}"

# Vérifie la présence du fichier SQL.
if [[ ! -f "${SEED_FILE}" ]]; then
    echo "[ERREUR] Fichier de données introuvable : ${SEED_FILE}" >&2
    exit 1
fi

echo "[DB] Import des données : database/sql/02_seed.sql"

docker compose \
    --project-directory "${PROJECT_ROOT}" \
    exec -T db \
    mysql \
    -u"${DB_USER}" \
    -p"${DB_PASS}" \
    "${DB_NAME}" \
    < "${SEED_FILE}"

echo "[OK] Données importées"