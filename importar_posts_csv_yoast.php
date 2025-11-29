<?php
// Uso: php importar_posts_csv_yoast.php posts_para_importar.csv

if (PHP_SAPI !== 'cli') {
    echo "Rodar este script via CLI.\n";
    exit(1);
}

$csv_path = $argv[1] ?? '';
if (!$csv_path || !file_exists($csv_path)) {
    echo "Informe o caminho do CSV. Ex: php importar_posts_csv_yoast.php posts_para_importar.csv\n";
    exit(1);
}

echo "Lendo CSV em: {$csv_path}\n";

// carrega o WordPress dessa instalação
require __DIR__ . '/wp-load.php';

if (!function_exists('wp_insert_post')) {
    echo "Erro: WordPress não foi carregado corretamente.\n";
    exit(1);
}

// ===== Funções auxiliares =====
function limpar_espacos($txt) {
    $txt = preg_replace('~\s+~', ' ', (string) $txt);
    return trim($txt);
}

function gerar_focus_kw($meta_title, $post_title) {
    $base = $meta_title ?: $post_title;

    // tira "com a Gynjet..." e variações
    $base = preg_replace('~com a gynjet.*$~i', '', $base);

    // pega só antes de ":" "-" "|" etc
    $parts = preg_split('~[:\-–|]~', $base);
    $base  = $parts[0] ?? $base;

    $base = strtolower(limpar_espacos($base));
    $base = preg_replace('~[?!\.]+$~', '', $base);

    $words = explode(' ', $base);
    if (count($words) > 8) {
        $base = implode(' ', array_slice($words, 0, 8));
    }

    return $base;
}

function get_col($row, $idx, $name) {
    return isset($idx[$name]) && isset($row[$idx[$name]]) ? trim($row[$idx[$name]]) : '';
}

// ===== Categoria padrão =====
$cat_name = 'Blog Gynjet';
$cat_id   = get_cat_ID($cat_name);
if (!$cat_id) {
    $cat_id = wp_create_category($cat_name);
    echo "Criada categoria '{$cat_name}' (ID {$cat_id})\n";
}

// ===== Abrir CSV =====
if (($handle = fopen($csv_path, 'r')) === false) {
    echo "Não consegui abrir o CSV.\n";
    exit(1);
}

$header = fgetcsv($handle, 0, ',');
if ($header === false) {
    echo "CSV sem cabeçalho.\n";
    exit(1);
}

$idx   = array_flip($header);
$count = 0;

while (($row = fgetcsv($handle, 0, ',')) !== false) {
    $slug             = get_col($row, $idx, 'slug');
    $post_title       = get_col($row, $idx, 'post_title');
    $post_content     = get_col($row, $idx, 'post_content');
    $meta_title       = get_col($row, $idx, 'meta_title');
    $meta_description = get_col($row, $idx, 'meta_description');

    if (!$slug || !$post_title) {
        continue;
    }

    $meta_title       = $meta_title ?: $post_title;
    $meta_description = limpar_espacos($meta_description);
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($meta_description, 'UTF-8') > 155) {
            $meta_description = mb_substr($meta_description, 0, 155, 'UTF-8');
        }
    } else {
        if (strlen($meta_description) > 155) {
            $meta_description = substr($meta_description, 0, 155);
        }
    }

    // procura post existente pelo slug
    $existing = get_page_by_path($slug, OBJECT, 'post');

    $postarr = array(
        'post_title'   => $post_title,
        'post_name'    => $slug,
        'post_content' => $post_content,
        'post_status'  => 'publish',
        'post_type'    => 'post',
    );

    if ($existing) {
        $postarr['ID'] = $existing->ID;
        $post_id       = wp_update_post($postarr, true);
        $acao          = 'Atualizado';
    } else {
        $post_id = wp_insert_post($postarr, true);
        $acao    = 'Criado';
    }

    if (is_wp_error($post_id)) {
        echo "Erro no post {$slug}: " . $post_id->get_error_message() . "\n";
        continue;
    }

    // categoria
    wp_set_post_categories($post_id, array($cat_id), false);

    // Yoast: meta title e meta description
    update_post_meta($post_id, '_yoast_wpseo_title',    $meta_title);
    update_post_meta($post_id, '_yoast_wpseo_metadesc', $meta_description);

    // palavra foco
    $focuskw = gerar_focus_kw($meta_title, $post_title);
    if ($focuskw) {
        update_post_meta($post_id, '_yoast_wpseo_focuskw', $focuskw);
    }

    echo "{$acao}: {$slug} (ID {$post_id}) | focus: {$focuskw}\n";
    $count++;
}

fclose($handle);
echo "Finalizado. Processados {$count} posts.\n";
