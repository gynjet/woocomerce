#!/usr/bin/env bash
set -euo pipefail

# Sanitize a WordPress/WooCommerce repository by removing noisy artifacts
# and compacting Git history safely.

ROOT_DIR="$(git rev-parse --show-toplevel 2>/dev/null || true)"
if [[ -z "${ROOT_DIR}" || ! -d "${ROOT_DIR}/.git" ]]; then
  echo "[erro] Execute este script dentro de um repositório Git." >&2
  exit 1
fi
cd "${ROOT_DIR}"

JUNK_PATHS=(
  "wp-content/uploads"
  "wp-content/cache"
  "wp-content/upgrade"
  "wp-content/backups"
  "wp-content/backups-*"
  "wp-content/ai1wm-backups"
  "wp-content/uploads/wc-logs"
  "wp-content/uploads/wc-logs-*"
  "wp-content/uploads/wc-analytics-*"
  "node_modules"
  "vendor"
  "coverage"
  "logs"
  ".cache"
  ".tmp"
)

PATTERN_FIND=(
  "-name" "*.log"
  "-o" "-name" "*.sql"
  "-o" "-name" "*.sql.gz"
  "-o" "-name" "*.zip"
  "-o" "-name" "*.tar"
  "-o" "-name" "*.tar.gz"
  "-o" "-name" "*.bak"
  "-o" "-name" "*.tmp"
  "-o" "-name" "*.orig"
  "-o" "-name" ".DS_Store"
  "-o" "-name" "Thumbs.db"
)

# Remove known noisy directories and files
for path in "${JUNK_PATHS[@]}"; do
  if [[ -e "${path}" ]]; then
    echo "[limpando] Removendo ${path}"
    rm -rf "${path}"
  fi
done

echo "[limpando] Removendo arquivos temporários e dumps"
find . \( "${PATTERN_FIND[@]}" \) -print -delete

# Compacta o histórico sem reescrever commits
if git rev-parse --git-dir >/dev/null 2>&1; then
  echo "[otimizando] Limpando referências antigas"
  git reflog expire --expire=now --all
  git gc --prune=now --aggressive
fi

echo "[ok] Repositório saneado. Revise o 'git status' antes de commitar."
