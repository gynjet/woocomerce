<?php
// Executar com: wp --allow-root eval-file set_focuskw.php
// Opcional: aplique só na categoria abaixo (use o NOME do termo folha)
$only_category_leaf = 'Feltros e Almofadas';  // deixe "" para todos os produtos

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

// Montar consulta
$args = [
  'post_type'      => 'product',
  'post_status'    => ['publish','draft','pending','private'],
  'posts_per_page' => -1,
  'fields'         => 'ids',
];

if ($only_category_leaf) {
  $term = get_term_by('name', $only_category_leaf, 'product_cat');
  if ($term && !is_wp_error($term)) {
    $args['tax_query'] = [[
      'taxonomy' => 'product_cat',
      'field'    => 'term_id',
      'terms'    => [$term->term_id],
    ]];
  } else {
    echo "Aviso: categoria '{$only_category_leaf}' não encontrada. Rodando em todos os produtos.\n";
  }
}

$ids = get_posts($args);
$updated = 0;

foreach ($ids as $id) {
  // Tenta pelos atributos globais
  $brands = wp_get_post_terms($id, 'pa_marca-da-impressora', ['fields'=>'names']); if (is_wp_error($brands)) $brands=[];
  $models = wp_get_post_terms($id, 'pa_modelo-da-impressora', ['fields'=>'names']); if (is_wp_error($models)) $models=[];
  // Fallback: tenta pelo título
  if (!$brands || !$models) {
    $title = get_the_title($id);
    if (!$brands) { $b = detect_brand_from_text($title); if ($b) $brands = [$b]; }
    if (!$models){ $models = detect_models_from_text($title); }
  }
  $focus = trim(preg_replace('/\s+/', ' ', 'feltro '.implode(' ',$brands).' '.implode(' ',$models).' Goiânia'));
  if (!$focus || strlen($focus) < 10) continue;

  update_post_meta($id, '_yoast_wpseo_focuskw', $focus);
  update_post_meta($id, 'yoast_wpseo_focuskw',  $focus); // compat
  echo "OK #{$id}: {$focus}\n"; $updated++;
}
echo "Concluído. Produtos atualizados: {$updated}\n";
