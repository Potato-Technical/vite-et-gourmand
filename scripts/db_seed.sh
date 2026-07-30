#!/usr/bin/env bash
set -euo pipefail
if [ -f .env ]; then
  set -a; source .env; set +a
else
  echo ".env manquant"; exit 1
fi

echo "[DB] Load seed (inside db container)"

docker compose exec -T db mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" < database/sql/03_seed.sql

echo "[OK] Seed loaded"
