#!/usr/bin/env bash
set -euo pipefail

# Charger .env (sans "source" : compatible .env simple KEY=VALUE)
if [ -f .env ]; then
  export $(grep -vE '^\s*#' .env | grep -vE '^\s*$' | xargs)
else
  echo ".env manquant"
  exit 1
fi

: "${DB_NAME:?DB_NAME manquant}"
: "${DB_ROOT_PASS:?DB_ROOT_PASS manquant}"

SCHEMA_FILE="${SCHEMA_FILE:-database/sql/01_schema.sql}"
SEED_FILE="${SEED_FILE:-database/sql/03_seed.sql}"

echo "[db_reset] drop/create database ${DB_NAME}"

# 1) DROP/CREATE via root (fiable)
docker compose exec -T db mysql -uroot -p"${DB_ROOT_PASS}" -e "
DROP DATABASE IF EXISTS \`${DB_NAME}\`;
CREATE DATABASE \`${DB_NAME}\`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
"

# 2) Appliquer schema
if [ -f "${SCHEMA_FILE}" ]; then
  echo "[db_reset] apply schema (${SCHEMA_FILE})"
  docker compose exec -T db mysql -uroot -p"${DB_ROOT_PASS}" "${DB_NAME}" < "${SCHEMA_FILE}"
else
  echo "[db_reset] schema introuvable: ${SCHEMA_FILE}"
  exit 1
fi

# 3) Appliquer seed
if [ -f "${SEED_FILE}" ]; then
  echo "[db_reset] apply seed (${SEED_FILE})"
  docker compose exec -T db mysql -uroot -p"${DB_ROOT_PASS}" "${DB_NAME}" < "${SEED_FILE}"
else
  echo "[db_reset] seed introuvable: ${SEED_FILE}"
  exit 1
fi

echo "[db_reset] done"
