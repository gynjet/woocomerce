#!/usr/bin/env bash
# alt_update.sh — wrapper seguro para atualizar ALT via WP-CLI
# Uso:
#   bash alt_update.sh /caminho/para/wordpress /caminho/para/media_alt_update.csv [--dry-run]
set -euo pipefail

WP_PATH="${1:-}"
CSV_PATH="${2:-}"
DRY="${3:-}"

if [[ -z "$WP_PATH" || -z "$CSV_PATH" ]]; then
  echo "Uso: bash alt_update.sh /caminho/wordpress /caminho/media_alt_update.csv [--dry-run]"
  exit 1
fi

if [[ ! -d "$WP_PATH" ]]; then
  echo "Erro: pasta do WordPress não existe: $WP_PATH"; exit 1
fi
if [[ ! -f "$CSV_PATH" ]]; then
  echo "Erro: CSV não encontrado: $CSV_PATH"; exit 1
fi

cd "$WP_PATH"

# Verificações básicas
if ! command -v wp >/dev/null 2>&1; then
  echo "Erro: WP-CLI não encontrado (comando 'wp'). Instale e tente novamente."; exit 1
fi
wp core is-installed >/dev/null 2>&1 || { echo "Erro: WordPress não detectado em $WP_PATH"; exit 1; }

echo "Site: $(wp option get siteurl)"
echo "Banco: $(wp db size --format=bytes | tr -d '\n'; echo ' bytes')"
echo "Anexos (attachments): $(wp post list --post_type=attachment --format=count)"

# Backup rápido do banco (opcional mas recomendado)
STAMP="$(date +%Y%m%d_%H%M%S)"
BK="backup_before_alt_$STAMP.sql.gz"
echo "Fazendo backup do banco em: $BK"
wp db export - | gzip > "$BK"

PHP_SCRIPT="update_image_alt_from_csv_v2.php"
if [[ ! -f "$PHP_SCRIPT" ]]; then
  echo "Erro: $PHP_SCRIPT não está em $WP_PATH"; exit 1
fi

echo "Rodando atualização de ALT... (DRY-RUN=$DRY)"
wp eval-file "$PHP_SCRIPT" "$CSV_PATH" ${DRY:+--dry-run} --allow-root

echo "Pronto. Veja o log gerado em wp-content/ (arquivo alt_update_*.log)"
