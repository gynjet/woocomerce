<?php
// map-attrs.php — leve, idempotente, só título do produto

if (php_sapi_name() !== 'cli') { exit; }
$limit  = isset($argv[1]) ? (int)$argv[1] : 200;
$offset = isset($argv[2]) ? (int)$argv[2] : 0;

$TAX_BRAND  = 'pa_marca-da-impressora';
$TAX_SERIES = 'pa_serie-da-impressora';

$MAP_BRAND = [
  'Epson'   => ['/\bEpson\b/i'],
  'HP'      => ['/\bHP\b/i','/\bDesignJet\b/i'],
  'Brother' => ['/\bBrother\b/i','/\bDCP\b/i'],
  'Canon'   => ['/\bCanon\b/i','/\bPIXMA\b/i','/\bimagePROGRAF\b/i'],
  'Samsung' => ['/\bSamsung\b/i'],
  'Kyocera' => ['/\bKyocera\b/i'],
  'Lexmark' => ['/\bLexmark\b/i'],
];

$MAP_SERIES = [
  'T120' => ['/\\bT120\\b/i'], 'T130' => ['/\\bT130\\b/i'],
  'T230' => ['/\\bT230\\b/i'], 'T250' => ['/\\bT250\\b/i'],
  'T520' => ['/\\bT520\\b/i'], 'T530' => ['/\\bT530\\b/i'],
  'T650' => ['/\\bT650\\b/i'], 'T730' => ['/\\bT730\\b/i'], 'T830' => ['/\\bT830\\b/i'],
  'L3150' => ['/\\bL3150\\b/i'], 'L3110' => ['/\\bL3110\\b/i'],
  'L1800' => ['/\\bL1800\\b/i'], 'L8050' => ['/\\bL8050\\b/i'],
  'DCP-L2540DW' => ['/\\bDCP[- ]?L2540DW\\b/i'],
  'DCP-T420W'   => ['/\\bDCP[- ]?T420W\\b/i'],
];

function ensure_term_id($taxonomy, $name) {
  if (!$name) return 0;
  $exists = term_exists($name, $taxonomy);
  if ($exists && !is_wp_error($exists)) return (int)$exists['term_id'];
  $res = wp_insert_term($name, $taxonomy);
  return is_wp_error($res) ? 0 : (int)$res['term_id'];
}
function first_match(array $map, $hay) {
  foreach ($map as $label => $patterns) {
    foreach ($patterns as $p) { if (preg_match($p, $hay)) return $label; }
  }
  return null;
}

$q = new WP_Query([
  'post_type'      => 'product',
  'post_status'    => 'publish',
  'fields'         => 'ids',
  'posts_per_page' => $limit,
  'offset'         => $offset,
  'no_found_rows'  => true,
  'orderby'        => 'ID',
  'order'          => 'ASC',
]);

$done = 0;
foreach ($q->posts as $pid) {
  $title = get_the_title($pid) ?: '';
  $brand = first_match($MAP_BRAND,  $title);
  $series= first_match($MAP_SERIES, $title);

  if ($brand)  { wp_set_object_terms($pid, ensure_term_id($TAX_BRAND,  $brand),  $TAX_BRAND,  false); }
  if ($series) { wp_set_object_terms($pid, ensure_term_id($TAX_SERIES, $series), $TAX_SERIES, false); }

  // Deixa visível nos arquivos (sem instanciar WC_Product pesado)
  $meta = get_post_meta($pid, '_product_attributes', true);
  if (!is_array($meta)) $meta = [];
  $apply = function($tax) use (&$meta, $pid) {
    $terms = wp_get_object_terms($pid, $tax, ['fields'=>'ids']);
    if (is_wp_error($terms) || empty($terms)) return;
    $meta[$tax] = [
      'name'         => $tax,
      'value'        => '',
      'position'     => 0,
      'is_visible'   => 1,
      'is_variation' => 0,
      'is_taxonomy'  => 1,
    ];
  };
  if ($brand)  $apply($TAX_BRAND);
  if ($series) $apply($TAX_SERIES);
  update_post_meta($pid, '_product_attributes', $meta);

  $done++;
}
echo "OK batch: offset={$offset} limit={$limit} aplicados={$done}\n";
