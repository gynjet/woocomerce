<?php
// Executar:
// wp --allow-root --path=/www/wwwroot/loja.gynjet.com.br --url=https://loja.gynjet.br eval-file importar_t3170_sem_imagem.php
//
// Varre as páginas da SULINK (T3170), extrai TÍTULO e PREÇO,
// e cria/atualiza produtos simples (sem imagem) por TÍTULO.

$DRY_RUN   = false;        // true = só mostra; false = grava
$CATEGORIA = 'T3170';      // "" para não categorizar
$PAGINAS   = range(1, 8);  // ajuste conforme a listagem

// Modo da URL (se "busca" não coletar nada, tente "atalho")
$URL_MODE   = 'busca'; // 'busca' ou 'atalho'
$BASE_BUSCA = 'https://www.lojasulink.com.br/loja/busca.php?loja=773912&palavra_busca=t3170&pg=%d';
$BASE_ATALHO= 'https://www.lojasulink.com.br/t3170'; // paginação provável: ?pg=2

function fetch_url($url){
  $args = [
    'timeout' => 20,
    'headers' => [
      'User-Agent' => 'Mozilla/5.0',
      'Accept'     => 'text/html',
    ],
  ];
  $res = wp_remote_get($url, $args);
  if (is_wp_error($res)) return '';
  $code = wp_remote_retrieve_response_code($res);
  if ($code < 200 || $code >= 300) return '';
  return wp_remote_retrieve_body($res);
}

// <<< nome diferente para não colidir com core >>>
function parse_product_blocks($html){
  $blocks = [];
  $html = preg_replace('/\s+/', ' ', $html);

  // tenta capturar cards comuns de produto
  if (preg_match_all('/<(?:li|div)[^>]+class="[^"]*(?:listagem-item|produto|vitrine|box-produto)[^"]*"[^>]*>(.*?)<\/(?:li|div)>/i', $html, $m)){
    foreach ($m[1] as $chunk) $blocks[] = $chunk;
  }

  // fallback: cortar por "Comprar"
  if (!$blocks && preg_match_all('/(<a[^>]*>Comprar<\/a>)/i', $html, $m, PREG_OFFSET_CAPTURE)){
    $pos = array_column($m[1], 1);
    $pos[] = strlen($html);
    $start = 0;
    foreach ($pos as $p){ $blocks[] = substr($html, $start, $p-$start); $start = $p; }
  }

  return $blocks ?: [$html];
}

function extract_title($chunk){
  if (preg_match('/<a[^>]+>(.+?)<\/a>/i', $chunk, $m)){
    $txt = trim(strip_tags($m[1]));
    if ($txt && !preg_match('/comprar|ver mais|detalhes/i', $txt) && mb_strlen($txt) >= 6) return $txt;
    if (preg_match_all('/<a[^>]+>(.+?)<\/a>/i', $chunk, $mm)){
      foreach ($mm[1] as $cand){
        $t = trim(strip_tags($cand));
        if ($t && !preg_match('/comprar|ver mais|detalhes/i',$t) && mb_strlen($t) >= 6) return $t;
      }
    }
  }
  if (preg_match('/<h[23][^>]*>(.+?)<\/h[23]>/i', $chunk, $m)){
    $t = trim(strip_tags($m[1]));
    if ($t) return $t;
  }
  return '';
}

function extract_price($chunk){
  if (preg_match_all('/R\$\s*([\d\.\,]+)/i', $chunk, $m)){
    $vals = array_map(function($s){
      $s = str_replace('.', '', $s);
      $s = str_replace(',', '.', $s);
      return (float)$s;
    }, $m[1]);
    rsort($vals, SORT_NUMERIC);
    if ($vals) return number_format($vals[0], 2, '.', '');
  }
  return '';
}

function ensure_category($name){
  if ($name === '') return 0;
  $tax = 'product_cat';
  $t = term_exists($name, $tax);
  if (!$t) $t = wp_insert_term($name, $tax, ['slug'=>sanitize_title($name)]);
  if (is_wp_error($t)) return 0;
  return is_array($t) ? $t['term_id'] : $t;
}

function make_sku($title){
  $base = strtoupper(preg_replace('/[^A-Z0-9]+/', '-', remove_accents($title)));
  $base = trim($base, '-');
  if (strlen($base) > 40) $base = substr($base, 0, 40);
  $sku = 'T3170-' . $base;
  if (function_exists('wc_get_product_id_by_sku')) {
    $orig=$sku; $i=1;
    while (wc_get_product_id_by_sku($sku)) { $sku = $orig.'-'.$i++; if ($i>999) break; }
  }
  return $sku;
}

$cat_id = $CATEGORIA ? ensure_category($CATEGORIA) : 0;

$seen = []; $items = [];
foreach ($PAGINAS as $pg){
  $url = ($URL_MODE==='atalho')
    ? ($pg===1 ? $BASE_ATALHO : $BASE_ATALHO.'?pg='.$pg)
    : sprintf($BASE_BUSCA, $pg);

  $html = fetch_url($url);
  if (!$html) { echo "Falha ao abrir $url\n"; continue; }

  $blocks = parse_product_blocks($html);
  foreach ($blocks as $b){
    $title = extract_title($b);
    $price = extract_price($b);
    if (!$title || !$price) continue;
    $key = mb_strtolower(trim($title));
    if (isset($seen[$key])) continue;
    $seen[$key] = true;
    $items[] = ['title'=>$title, 'price'=>$price];
  }
  echo "OK página $pg: ".count($items)." itens acumulados\n";
}

echo "Coletados: ".count($items)." itens\n";
if (!$items){ echo "Nada coletado. Tente \$URL_MODE=\"atalho\" e/ou ajustar \$PAGINAS.\n"; return; }

global $wpdb;
$novos=0; $atual=0;

foreach ($items as $it){
  $t = $it['title']; $p = $it['price'];

  $id_exist = $wpdb->get_var($wpdb->prepare(
    "SELECT ID FROM {$wpdb->posts} WHERE post_type='product' AND post_status<>'trash' AND post_title=%s LIMIT 1",
    $t
  ));

  if ($id_exist){
    if (!$DRY_RUN){
      update_post_meta($id_exist, '_regular_price', $p);
      update_post_meta($id_exist, '_price',         $p);
      if ($cat_id) wp_set_post_terms($id_exist, [$cat_id], 'product_cat', true);
    }
    echo "UPD #$id_exist: {$t} → R$ {$p}\n"; $atual++; continue;
  }

  if ($DRY_RUN){ echo "NEW (dry): {$t} → R$ {$p}\n"; $novos++; continue; }

  $pid = wp_insert_post([
    'post_title'   => $t,
    'post_type'    => 'product',
    'post_status'  => 'publish',
    'post_content' => '',
    'post_excerpt' => '',
  ], true);

  if (is_wp_error($pid)){ echo "ERRO criar: ".$pid->get_error_message()." | {$t}\n"; continue; }

  wp_set_object_terms($pid, 'simple', 'product_type', false);
  if ($cat_id) wp_set_post_terms($pid, [$cat_id], 'product_cat', true);

  $sku = make_sku($t);
  update_post_meta($pid, '_sku', $sku);
  update_post_meta($pid, '_regular_price', $p);
  update_post_meta($pid, '_price',         $p);
  update_post_meta($pid, '_stock_status',  'instock');
  update_post_meta($pid, '_manage_stock',  'no');

  echo "NEW #$pid: {$t} → R$ {$p} (SKU {$sku})\n"; $novos++;
}

echo "Concluído. Novos: {$novos} | Atualizados: {$atual}\n";
