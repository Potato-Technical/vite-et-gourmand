#!/usr/bin/env bash
# db_full_reset.sh
set -euo pipefail

./scripts/db_reset.sh
./scripts/db_schema.sh
./scripts/db_seed.sh

echo "[OK] Full reset completed"