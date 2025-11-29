<?php
// Executar: wp --allow-root eval-file set_focuskw_by_title.php
function detect_models_from_text($t){
  preg_match_all('/\b(?:L|T|XP|WF|ET|TX|MFC|DJ|IX|IP|MG)\s?-?\s?\d{2,5}\b/i',$t,$m);
  if (empty($m[0])) return [];
  $mods = array_map(fn($x)=>strtoupper(preg_replace('/\s+/','',$x)),$m[0]);
  return array_values(array_unique($mods));
}
function detect_brand_from_text($t){
  $p = mb_strtolower($t);
  if (strpos($p,'epson')!==false) return 'Epson';
  if (strpos($p,'canon')!==false) return 'Canon';
  if (preg_match('/\bhp\b/i',$t)) return 'HP';
  if (strpos($p,'brother')!==false) return 'Brother';
  return '';
}

$args = [
  'post_type'      => 'product',
  'post_status'    => ['publish','draft','pending','private'],
  'posts_per_page' => -1,
  'fields'         => 'ids',
];

$ids = get_posts($args);
$updated = 0;

foreach ($ids as $id) {
  $title = get_the_title($id);
  if (!preg_match('/feltro|almofada/i', $title)) continue; // restringe aos seus itens

  $brand = detect_brand_from_text($title);
  $mods  = detect_models_from_text($title);

  if (!$brand && !$mods) continue;

  $focus = trim(preg_replace('/\s+/', ' ', 'feltro ' . $brand . ' ' . implode(' ', $mods) . ' Goiânia'));
  if (strlen($focus) < 10) continue;

  update_post_meta($id, '_yoast_wpseo_focuskw', $focus);
  update_post_meta($id, 'yoast_wpseo_focuskw',  $focus); // compat
  echo "OK #{$id}: {$focus}\n";
  $updated++;
}
echo "Concluído. Produtos atualizados: {$updated}\n";
