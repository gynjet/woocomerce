<?php
/**
 * Executar com: wp --allow-root eval-file yoast_audit_and_fill.php [--dry-run]
 *
 * Varre produtos e posts para preencher (apenas se estiverem vazios) os metadados
 * do Yoast SEO: _yoast_wpseo_title, _yoast_wpseo_metadesc e _yoast_wpseo_focuskw.
 * Use --dry-run ou YOAST_DRY_RUN=1 para simular sem gravar no banco.
 *
 * Pensado para sites de porte moderado. Para bases muito grandes, considere
 * reduzir YOAST_BATCH_SIZE ou rodar por tipo separadamente.
 */

// ----------------- Configuração rápida -----------------
const YOAST_CITY_NAME     = 'Goiânia';            // Cidade opcional a inserir em foco/títulos (pode deixar vazio).
const YOAST_POST_TYPES    = ['product', 'post'];  // Quais post types serão processados.
const YOAST_DESC_LIMIT    = 155;                  // Tamanho máximo da meta description.
const YOAST_BATCH_SIZE    = 200;                  // Quantidade de posts carregados por página.
// --------------------------------------------------------

$argv = $argv ?? [];
$dry_run = in_array('--dry-run', $argv, true) || getenv('YOAST_DRY_RUN') === '1';

if (!defined('WPSEO_VERSION') && !class_exists('WPSEO_Meta')) {
  echo "Yoast SEO não encontrado. Ative o plugin antes de rodar.\n";
  return;
}

if (!function_exists('update_post_meta') || !function_exists('get_post')) {
  echo "Funções básicas do WordPress indisponíveis. Execute via WP-CLI.\n";
  return;
}

if ($dry_run) {
  echo "[INFO] Rodando em DRY-RUN: nada será gravado, apenas pré-visualizado.\n";
}

/**
 * Encapsula mensagem de log curta para mudanças (ou prévias de mudanças).
 */
function log_change($type, $id, $changes, $dry_run) {
  $pairs = [];
  foreach ($changes as $key => $value) {
    $preview = mb_substr($value, 0, 80);
    if (mb_strlen($value) > 80) $preview .= '…';
    $pairs[] = "$key=\"$preview\"";
  }
  $action = $dry_run ? 'DRY-RUN' : 'OK';
  echo "[$action] {$type} #{$id}: " . implode('; ', $pairs) . "\n";
}

/**
 * Remove HTML, decodifica entidades e compacta espaços.
 */
function clean_spaces($t) {
  return trim(preg_replace('/\s+/', ' ', html_entity_decode(wp_strip_all_tags((string) $t))));
}

/**
 * Resume o texto ao limite informado mantendo cortes limpos.
 */
function summarize_text($t, $limit = YOAST_DESC_LIMIT) {
  $t = clean_spaces($t);
  if (strlen($t) <= $limit) return $t;
  $cut = substr($t, 0, $limit - 1);
  $space = strrpos($cut, ' ');
  if ($space !== false && $space > 40) {
    $cut = substr($cut, 0, $space);
  }
  return rtrim($cut, ' .,;:-'). '…';
}

/**
 * Detecta códigos de modelo comuns em textos de impressoras.
 */
function detect_models_from_text($t) {
  preg_match_all('/\b(?:L|T|XP|WF|ET|TX|MFC|DJ|IX|IP|MG)\s?-?\s?\d{2,5}\b/i', $t, $m);
  if (empty($m[0])) return [];
  $mods = array_map(fn($x) => strtoupper(preg_replace('/\s+/', '', $x)), $m[0]);
  return array_values(array_unique($mods));
}

/**
 * Detecta marcas básicas a partir do texto.
 */
function detect_brand_from_text($t) {
  $p = mb_strtolower($t);
  if (strpos($p, 'epson') !== false) return 'Epson';
  if (strpos($p, 'canon') !== false) return 'Canon';
  if (preg_match('/\bhp\b/i', $t)) return 'HP';
  if (strpos($p, 'brother') !== false) return 'Brother';
  return '';
}

/**
 * Gera palavra foco para produtos.
 */
function build_product_focuskw($post_id, $post, $brand_terms = [], $model_terms = []) {
  $brands = $brand_terms;
  $models = $model_terms;

  if (!$brands || !$models) {
    $title = $post->post_title;
    if (!$brands) {
      $b = detect_brand_from_text($title);
      if ($b) $brands = [$b];
    }
    if (!$models) {
      $models = detect_models_from_text($title);
    }
  }

  $parts = array_filter([
    implode(' ', $brands),
    implode(' ', $models),
    YOAST_CITY_NAME ?: '',
  ]);

  $focus = trim(preg_replace('/\s+/', ' ', implode(' ', $parts)));
  return strlen($focus) >= 10 ? $focus : '';
}

/**
 * Gera meta description para produtos.
 */
function build_product_description($post_id, $post, $brand_terms = [], $model_terms = []) {
  unset($post_id); // Mantém assinatura uniforme, valor não é usado aqui.

  $brand_str = is_array($brand_terms) ? implode(' ', $brand_terms) : $brand_terms;
  $model_str = is_array($model_terms) ? implode(' ', $model_terms) : $model_terms;

  $pieces = [];
  if ($brand_str) $pieces[] = $brand_str;
  if ($model_str) $pieces[] = $model_str;

  $base = $post->post_excerpt ?: $post->post_content;
  $desc = summarize_text($base ?: $post->post_title);

  $seo = trim(implode(' ', array_filter($pieces)));
  if ($seo) {
    $desc = summarize_text($desc . ' Compatível com ' . $seo . '.');
  }
  return $desc;
}

/**
 * Gera título SEO para produtos.
 */
function build_product_title($post_id, $post, $brand_terms = [], $model_terms = []) {
  unset($post_id); // Mantém assinatura uniforme, valor não é usado aqui.

  $brand_str = is_array($brand_terms) ? implode(' ', $brand_terms) : $brand_terms;
  $model_str = is_array($model_terms) ? implode(' ', $model_terms) : $model_terms;

  $extra = array_filter([$brand_str, $model_str, YOAST_CITY_NAME ?: '']);
  $suffix = $extra ? ' | ' . implode(' ', $extra) : '';
  return clean_spaces($post->post_title . $suffix);
}

/**
 * Palavra foco para posts.
 */
function build_post_focuskw($post_id, $post, $brand_terms = [], $model_terms = []) { // extra params são ignorados.
  $words = preg_split('/\s+/', clean_spaces($post->post_title));
  $focus = implode(' ', array_slice($words, 0, 6));
  return strlen($focus) >= 10 ? $focus : '';
}

/**
 * Meta description para posts.
 */
function build_post_description($post_id, $post, $brand_terms = [], $model_terms = []) { // extra params são ignorados.
  $base = $post->post_excerpt ?: $post->post_content;
  return summarize_text($base ?: $post->post_title);
}

/**
 * Título SEO para posts.
 */
function build_post_title($post_id, $post, $brand_terms = [], $model_terms = []) { // extra params são ignorados.
  return clean_spaces($post->post_title);
}

$targets = [
  'product' => [
    'build_focus' => function ($id, $post, $brand_terms = [], $model_terms = []) {
      return build_product_focuskw($id, $post, $brand_terms, $model_terms);
    },
    'build_desc'  => function ($id, $post, $brand_terms = [], $model_terms = []) {
      return build_product_description($id, $post, $brand_terms, $model_terms);
    },
    'build_title' => function ($id, $post, $brand_terms = [], $model_terms = []) {
      return build_product_title($id, $post, $brand_terms, $model_terms);
    },
    'term_taxonomy' => ['pa_marca-da-impressora', 'pa_modelo-da-impressora'],
  ],
  'post' => [
    'build_focus' => function ($id, $post, $brand_terms = [], $model_terms = []) {
      return build_post_focuskw($id, $post, $brand_terms, $model_terms);
    },
    'build_desc'  => function ($id, $post, $brand_terms = [], $model_terms = []) {
      return build_post_description($id, $post, $brand_terms, $model_terms);
    },
    'build_title' => function ($id, $post, $brand_terms = [], $model_terms = []) {
      return build_post_title($id, $post, $brand_terms, $model_terms);
    },
    'term_taxonomy' => [],
  ],
];

$summary = [];

foreach (YOAST_POST_TYPES as $type) {
  if (!isset($targets[$type])) continue;
  $cfg = $targets[$type];

  $paged = 1;
  $total_scanned = 0;
  $filled = 0;
  $skipped = 0;

  do {
    $query = new WP_Query([
      'post_type'      => $type,
      'post_status'    => ['publish', 'draft', 'pending', 'private'],
      'posts_per_page' => YOAST_BATCH_SIZE,
      'fields'         => 'ids',
      'orderby'        => 'ID',
      'order'          => 'ASC',
      'paged'          => $paged,
    ]);

    if (!$query->have_posts()) {
      break;
    }

    foreach ($query->posts as $id) {
      $total_scanned++;
      $post = get_post($id);
      if (!$post) {
        $skipped++;
        echo "[WARN] Post $id ($type) não encontrado, pulando.\n";
        continue;
      }

      // Lê sempre as duas variantes de meta: normal e legada (sem underscore)
      $title  = get_post_meta($id, '_yoast_wpseo_title', true);
      $desc   = get_post_meta($id, '_yoast_wpseo_metadesc', true);

      $focus_primary = get_post_meta($id, '_yoast_wpseo_focuskw', true);   // meta atual
      $focus_legacy  = get_post_meta($id, 'yoast_wpseo_focuskw', true);    // meta antiga (sem _)

      // Se qualquer uma estiver preenchida, consideramos que já existe palavra-foco
      $focus = $focus_primary ?: $focus_legacy;

      $brand_terms = [];
      $model_terms = [];
      foreach ($cfg['term_taxonomy'] as $tax) {
        $terms = wp_get_post_terms($id, $tax, ['fields' => 'names']);
        if (is_wp_error($terms)) $terms = [];
        if ($tax === 'pa_marca-da-impressora') $brand_terms = $terms;
        if ($tax === 'pa_modelo-da-impressora') $model_terms = $terms;
      }

      $changes = [];

      // NUNCA sobrescrever valores existentes: somente preencher vazios/não definidos.
      if (!$focus) {
        $focus_value = $cfg['build_focus']($id, $post, $brand_terms, $model_terms);
        if ($focus_value) {
          $changes['_yoast_wpseo_focuskw'] = $focus_value;
        }
      }

      if (!$desc) {
        $desc_value = $cfg['build_desc']($id, $post, $brand_terms, $model_terms);
        if ($desc_value) {
          $changes['_yoast_wpseo_metadesc'] = $desc_value;
        }
      }

      if (!$title) {
        $title_value = $cfg['build_title']($id, $post, $brand_terms, $model_terms);
        if ($title_value) {
          $changes['_yoast_wpseo_title'] = $title_value;
        }
      }

      if ($changes) {
        $filled++;
        if ($dry_run) {
          log_change($type, $id, $changes, true);
        } else {
          foreach ($changes as $meta_key => $value) {
            $ok = update_post_meta($id, $meta_key, $value);
            if ($meta_key === '_yoast_wpseo_focuskw') {
              $alt = update_post_meta($id, 'yoast_wpseo_focuskw', $value); // compat
              $ok = $ok && $alt;
            }
            if ($ok === false) {
              echo "[WARN] Falha ao salvar {$meta_key} para {$type} #{$id}.\n";
            }
          }
          log_change($type, $id, $changes, false);
        }
      } else {
        $skipped++;
      }
    }

    wp_reset_postdata();
    $paged++;
  } while (true);

  $summary[$type] = [
    'count'   => $total_scanned,
    'filled'  => $filled,
    'skipped' => $skipped,
  ];
}

echo "\nResumo:\n";
foreach ($summary as $type => $data) {
  $label = $dry_run ? 'preencheria' : 'preenchidos';
  echo strtoupper($type) . ": {$label} {$data['filled']} / {$data['count']} (skips {$data['skipped']})\n";
}
