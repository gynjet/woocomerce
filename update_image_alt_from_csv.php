<?php
// Usage: wp eval-file update_image_alt_from_csv.php /path/to/media_alt_update.csv [--allow-root]
if (!isset($argv[1])) {
  echo "Erro: informe o caminho do CSV.\nEx.: wp eval-file update_image_alt_from_csv.php /root/media_alt_update.csv\n";
  exit(1);
}
$csv = $argv[1];
if (!file_exists($csv)) {
  echo "Arquivo não encontrado: $csv\n";
  exit(1);
}
$h = fopen($csv, 'r');
$header = fgetcsv($h);
$col_url = array_search('image_url', $header);
$col_alt = array_search('alt_text', $header);
if ($col_url === false || $col_alt === false) {
  echo "Cabeçalhos esperados: image_url,alt_text\n";
  exit(1);
}
$count = 0; $ok = 0; $fail = 0;
while (($row = fgetcsv($h)) !== false) {
  $count++;
  $url = trim($row[$col_url]);
  $alt = trim($row[$col_alt]);
  if (!$url || !$alt) { $fail++; continue; }
  // Find attachment by GUID or source URL
  global $wpdb;
  $att_id = $wpdb->get_var( $wpdb->prepare(
    "SELECT ID FROM {$wpdb->posts} WHERE post_type='attachment' AND guid=%s LIMIT 1", $url
  ));
  if (!$att_id) {
    // try to find by meta _wp_attached_file (relative path) if URL is same domain
    $rel = preg_replace('#^https?://[^/]+/#','', $url);
    $att_id = $wpdb->get_var( $wpdb->prepare(
      "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value=%s LIMIT 1", $rel
    ));
  }
  if ($att_id) {
    update_post_meta($att_id, '_wp_attachment_image_alt', $alt);
    $ok++;
  } else {
    $fail++;
  }
}
fclose($h);
echo "Concluído. Linhas: $count | Atualizados: $ok | Não encontrados: $fail\n";
