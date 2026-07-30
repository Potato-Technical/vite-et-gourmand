#!/usr/bin/env bash
set -euo pipefail
if [ -f .env ]; then
  set -a; source .env; set +a
else
  echo ".env manquant"; exit 1
fi

echo "[DB] Load schema (inside db container)"

docker compose exec -T db mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < database/sql/01_schema.sql

echo "[OK] Schema loaded"
