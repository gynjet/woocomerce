<?php
/**
 * Update Image ALT text from CSV (v2)
 *
 * Usage:
 *   wp eval-file update_image_alt_from_csv_v2.php /absolute/path/media_alt_update.csv [--dry-run] [--limit=10000]
 *   Example:
 *     wp eval-file update_image_alt_from_csv_v2.php /root/media_alt_update.csv --allow-root
 *
 * CSV headers required:
 *   image_url,alt_text
 *
 * Behavior:
 *  - Tries to find attachment by GUID (exact URL). If not found, tries relative path in _wp_attached_file.
 *  - Only updates attachments with image/* mime.
 *  - Trims ALT to 120 chars and normalizes whitespace.
 *  - --dry-run: does not update, only reports.
 *  - --limit: process at most N rows (default: no limit).
 *  - Writes a log file in WP content dir: wp-content/alt_update_{Ymd_His}.log
 */

if (!defined('WP_CLI')) {
  echo "This script must be run via WP-CLI (wp eval-file ...)\n";
  exit(1);
}

global $wpdb;
$argv_copy = $argv; array_shift($argv_copy); // drop script name
if (empty($argv_copy)) {
  WP_CLI::error("Informe o caminho do CSV. Ex.: wp eval-file update_image_alt_from_csv_v2.php /root/media_alt_update.csv");
}
$csv = array_shift($argv_copy);
$dry = in_array('--dry-run', $argv_copy, true);
$limitArg = null;
foreach ($argv_copy as $a) {
  if (strpos($a, '--limit=') === 0) $limitArg = (int)substr($a, 8);
}
if (!file_exists($csv)) WP_CLI::error("Arquivo não encontrado: $csv");

$h = fopen($csv, 'r');
if (!$h) WP_CLI::error("Não foi possível abrir o CSV: $csv");
$header = fgetcsv($h);
if (!$header) WP_CLI::error("CSV vazio: $csv");
$idx_url = array_search('image_url', $header);
$idx_alt = array_search('alt_text', $header);
if ($idx_url === false || $idx_alt === false) {
  WP_CLI::error("Cabeçalhos esperados: image_url,alt_text");
}

$log_path = WP_CONTENT_DIR . '/alt_update_' . gmdate('Ymd_His') . '.log';
$log = fopen($log_path, 'a');
fwrite($log, "ALT update start @ ".gmdate('c')." CSV=$csv DRY_RUN=".($dry?'1':'0').PHP_EOL);

$total=0; $updated=0; $miss=0; $skipped=0;
$not_found = [];
$limitCount = 0;

while (($row = fgetcsv($h)) !== false) {
  $total++;
  if ($limitArg && $limitCount >= $limitArg) break;
  $limitCount++;

  $url = trim($row[$idx_url] ?? '');
  $alt = trim($row[$idx_alt] ?? '');
  if ($url === '' || $alt === '') { $skipped++; continue; }

  // Normalize ALT
  $alt = preg_replace('/\s+/u', ' ', $alt);
  if (mb_strlen($alt, 'UTF-8') > 120) {
    $alt = mb_substr($alt, 0, 117, 'UTF-8') . '...';
  }

  // Try GUID match
  $att_id = $wpdb->get_var( $wpdb->prepare(
    "SELECT ID FROM {$wpdb->posts} WHERE post_type='attachment' AND guid=%s LIMIT 1", $url
  ));

  // Try relative path (_wp_attached_file)
  if (!$att_id) {
    $rel = preg_replace('#^https?://[^/]+/#','', $url);
    $att_id = $wpdb->get_var( $wpdb->prepare(
      "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value=%s LIMIT 1", $rel
    ));
  }

  if (!$att_id) {
    $miss++; $not_found[] = $url;
    fwrite($log, "[MISS] $url\n");
    continue;
  }

  $mime = get_post_mime_type($att_id);
  if (strpos($mime, 'image/') !== 0) {
    $skipped++; fwrite($log, "[SKIP non-image] ID=$att_id URL=$url MIME=$mime\n");
    continue;
  }

  if ($dry) {
    fwrite($log, "[DRY] would update ID=$att_id ALT='$alt' URL=$url\n");
  } else {
    update_post_meta($att_id, '_wp_attachment_image_alt', $alt);
    $updated++; fwrite($log, "[OK] ID=$att_id ALT set URL=$url\n");
  }
}

fclose($h);
fwrite($log, "Done. total=$total updated=$updated miss=$miss skipped=$skipped\n");
fclose($log);

WP_CLI::success("Concluído. total=$total updated=$updated miss=$miss skipped=$skipped | Log: $log_path");
