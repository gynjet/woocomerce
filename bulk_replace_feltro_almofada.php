<?php
// Executar com: wp --allow-root eval-file bulk_replace_feltro_almofada.php
// Opcional: aplique apenas numa categoria-LEAF (ex.: "Feltros e Almofadas"). Deixe vazio para TODOS.
$only_category_leaf = '';  // ex.: 'Feltros e Almofadas'
$dry_run = false;          // true = só mostra o que mudaria (não salva)

function rpl($s){
  if ($s === '' || $s === null) return $s;
  // plural primeiro, depois singular (case-insensitive, palavra inteira)
  $s = preg_replace('/\bfeltros\b/iu', 'almofadas', $s);
  $s = preg_replace('/\bfeltro\b/iu',  'almofada',  $s);
  return $s;
}

$args = [
  'post_type'      => 'product',
  'post_status'    => ['publish','draft','pending','private'],
  'posts_per_page' => -1,
  'fields'         => 'ids',
];

// Filtrar por categoria folha, se definido
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
$total = count($ids);
$changed = 0;

foreach ($ids as $id) {
  $post = get_post($id);
  $upd  = ['ID' => $id];
  $log  = [];

  // Título / conteúdo / excerpt
  $new_title   = rpl($post->post_title);
  $new_content = rpl($post->post_content);
  $new_excerpt = rpl($post->post_excerpt);
  if ($new_title !== $post->post_title)      { $upd['post_title'] = $new_title;   $log[]='title'; }
  if ($new_content !== $post->post_content)  { $upd['post_content'] = $new_content; $log[]='content'; }
  if ($new_excerpt !== $post->post_excerpt)  { $upd['post_excerpt'] = $new_excerpt; $log[]='excerpt'; }

  // Slug (post_name) com unicidade garantida
  $orig_slug = $post->post_name ?: sanitize_title($post->post_title);
  $new_slug  = sanitize_title(rpl($orig_slug));
  if ($new_slug !== $post->post_name) {
    if (function_exists('wp_unique_post_slug')) {
      $new_slug = wp_unique_post_slug($new_slug, $id, $post->post_status, $post->post_type, $post->post_parent);
    }
    $upd['post_name'] = $new_slug; $log[]='slug';
  }

  // Yoast metas (title, desc, focuskw, synonyms) — chaves mais comuns
  $yoast_keys = [
    '_yoast_wpseo_title','yoast_wpseo_title',
    '_yoast_wpseo_metadesc','yoast_wpseo_metadesc',
    '_yoast_wpseo_focuskw','yoast_wpseo_focuskw',
    '_yoast_wpseo_keywordsynonyms','yoast_wpseo_keywordsynonyms'
  ];
  foreach ($yoast_keys as $k) {
    $v = get_post_meta($id, $k, true);
    if ($v !== '' && $v !== null) {
      $nv = rpl($v);
      if ($nv !== $v) {
        if (!$dry_run) update_post_meta($id, $k, $nv);
        $log[] = "meta:$k";
      }
    }
  }

  // Tags do produto (product_tag)
  $tags = wp_get_post_terms($id, 'product_tag', ['fields'=>'names']);
  if (!is_wp_error($tags) && $tags) {
    $new_tags = array_values(array_unique(array_map('rpl', $tags)));
    if ($new_tags !== $tags) {
      if (!$dry_run) wp_set_post_terms($id, $new_tags, 'product_tag', false);
      $log[] = 'tags';
    }
  }

  // Gravar alterações
  if (count($log)) {
    if (!$dry_run) {
      $res = wp_update_post($upd, true);
      if (is_wp_error($res)) {
        echo "ERRO #$id: ".$res->get_error_message()."\n";
        continue;
      }
    }
    $changed++;
    echo "OK #$id → ".implode(', ',$log)."\n";
  }
}

echo "Concluído. Produtos verificados: {$total}. Produtos alterados: {$changed}.\n";
