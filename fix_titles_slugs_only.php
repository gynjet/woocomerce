<?php
// Troca "feltro(s)" -> "almofada(s)" em TÍTULO e SLUG (URL) de TODOS os produtos
$ids = get_posts([
  'post_type'   => 'product',
  'post_status' => ['publish','draft','pending','private'],
  'numberposts' => -1,
  'fields'      => 'ids',
]);

$changed = 0;
foreach ($ids as $id) {
  $p = get_post($id);
  $t = $p->post_title;
  $s = $p->post_name ?: sanitize_title($t);

  // plural antes do singular (palavra inteira, case-insensitive)
  $t = preg_replace('/\bfeltros\b/iu', 'almofadas', $t);
  $t = preg_replace('/\bfeltro\b/iu',  'almofada',  $t);

  $s = preg_replace('/\bfeltros\b/iu', 'almofadas', $s);
  $s = preg_replace('/\bfeltro\b/iu',  'almofada',  $s);
  $s = sanitize_title($s);

  if ($t !== $p->post_title || $s !== $p->post_name) {
    if (function_exists('wp_unique_post_slug')) {
      $s = wp_unique_post_slug($s, $id, $p->post_status, $p->post_type, $p->post_parent);
    }
    wp_update_post(['ID'=>$id, 'post_title'=>$t, 'post_name'=>$s]);
    echo "OK #$id → {$t} / {$s}\n";
    $changed++;
  }
}
echo "Alterados: {$changed}\n";
