#!/usr/bin/env bash

# Arrête immédiatement le processus si l'une des étapes échoue.
set -euo pipefail

# Récupère le chemin absolu du dossier scripts.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "[DB] Réinitialisation complète de la base"

# 1. Supprime et recrée la base vide.
"${SCRIPT_DIR}/db_reset.sh"

# 2. Importe la structure des tables.
"${SCRIPT_DIR}/db_schema.sh"

# 3. Importe les données initiales.
"${SCRIPT_DIR}/db_seed.sh"

echo "[OK] Réinitialisation complète terminée"