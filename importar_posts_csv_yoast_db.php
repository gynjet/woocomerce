<?php
// Uso: php importar_posts_csv_yoast_db.php posts_para_importar.csv
// CSV esperado (cabeçalho, ordem não importa mas os nomes sim):
// slug,post_title,post_content,meta_title,meta_description,tags,categories
//
// tags       -> lista separada por ";" (taxonomia post_tag)
// categories -> lista separada por ";" (taxonomia category)
//
// Para cada linha, o script:
// - procura o post EXISTENTE pelo slug (post_name) em wp_posts (post_type='post')
// - se encontrar, atualiza:
//     * post_title
//     * post_content
//     * metas Yoast: _yoast_wpseo_title, _yoast_wpseo_metadesc, _yoast_wpseo_focuskw
//     * tags (post_tag)
//     * categorias (category)
// - se não encontrar, loga "Não encontrado (slug): ..."

if (PHP_SAPI !== 'cli') {
    echo "Rodar este script via CLI.\n";
    exit(1);
}

$csv_path = $argv[1] ?? '';
if (!$csv_path || !file_exists($csv_path)) {
    echo "Informe o caminho do CSV. Ex: php importar_posts_csv_yoast_db.php posts_para_importar.csv\n";
    exit(1);
}

echo "Lendo CSV em: {$csv_path}\n";

// ---------------------------------------------------------------------
// Lê wp-config.php como TEXTO e extrai DB_NAME, DB_USER, DB_PASSWORD, DB_HOST, table_prefix
// ---------------------------------------------------------------------
$config_path = __DIR__ . '/wp-config.php';
if (!file_exists($config_path)) {
    echo "wp-config.php não encontrado em {$config_path}\n";
    exit(1);
}
$config = file_get_contents($config_path);

function cfg_const($name, $config) {
    if (preg_match("/define\(\s*'{$name}'\s*,\s*'([^']*)'\s*\)/", $config, $m)) {
        return $m[1];
    }
    if (preg_match('/define\(\s*"'.$name.'"\s*,\s*"([^"]*)"\s*\)/', $config, $m)) {
        return $m[1];
    }
    return '';
}

$db_name = cfg_const('DB_NAME', $config);
$db_user = cfg_const('DB_USER', $config);
$db_pass = cfg_const('DB_PASSWORD', $config);
$db_host = cfg_const('DB_HOST', $config);

// table_prefix
$table_prefix = 'wp_';
if (preg_match('/\$table_prefix\s*=\s*[\'"]([^\'"]+)[\'"]\s*;/', $config, $m)) {
    $table_prefix = $m[1];
}

if (!$db_name || !$db_user || !$db_host) {
    echo "Não consegui extrair DB_NAME/DB_USER/DB_HOST do wp-config.php\n";
    exit(1);
}

echo "Conectando no banco {$db_name} em {$db_host} com prefixo {$table_prefix}...\n";

$mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    echo "Erro ao conectar no MySQL: " . $mysqli->connect_error . "\n";
    exit(1);
}
$mysqli->set_charset('utf8mb4');

// Tabelas com prefixo
$posts_table = $table_prefix . 'posts';
$meta_table  = $table_prefix . 'postmeta';
$terms_table = $table_prefix . 'terms';
$tt_table    = $table_prefix . 'term_taxonomy';
$tr_table    = $table_prefix . 'term_relationships';

// ---------------------------------------------------------------------
// Funções auxiliares
// ---------------------------------------------------------------------
function limpar_espacos($txt) {
    $txt = preg_replace('~\s+~', ' ', (string) $txt);
    return trim($txt);
}

function gerar_focus_kw($meta_title, $post_title) {
    $base = $meta_title ?: $post_title;

    // tira "com a Gynjet..." e similares
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

// upsert de meta (atualiza se existir, senão insere)
function upsert_meta($mysqli, $meta_table, $post_id, $key, $value) {
    $sql_sel = "SELECT meta_id FROM {$meta_table} WHERE post_id = ? AND meta_key = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql_sel);
    if (!$stmt) return;
    $stmt->bind_param('is', $post_id, $key);
    $stmt->execute();
    $stmt->bind_result($meta_id);
    $has = $stmt->fetch();
    $stmt->close();

    if ($has && $meta_id) {
        $sql_up = "UPDATE {$meta_table} SET meta_value = ? WHERE meta_id = ?";
        $stmt2 = $mysqli->prepare($sql_up);
        if (!$stmt2) return;
        $stmt2->bind_param('si', $value, $meta_id);
        $stmt2->execute();
        $stmt2->close();
    } else {
        $sql_ins = "INSERT INTO {$meta_table} (post_id, meta_key, meta_value) VALUES (?, ?, ?)";
        $stmt2 = $mysqli->prepare($sql_ins);
        if (!$stmt2) return;
        $stmt2->bind_param('iss', $post_id, $key, $value);
        $stmt2->execute();
        $stmt2->close();
    }
}

// Garante que o termo exista para uma taxonomia (post_tag, category, etc.)
function ensure_term($mysqli, $terms_table, $tt_table, $name, $taxonomy) {
    $name = limpar_espacos($name);
    if ($name === '') {
        return [0, 0];
    }

    // procura termo+taxonomia já existentes
    $sql = "SELECT t.term_id, tt.term_taxonomy_id
            FROM {$terms_table} t
            JOIN {$tt_table} tt ON t.term_id = tt.term_id
            WHERE t.name = ? AND tt.taxonomy = ?
            LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        return [0, 0];
    }
    $stmt->bind_param('ss', $name, $taxonomy);
    $stmt->execute();
    $stmt->bind_result($term_id, $tt_id);
    $found = $stmt->fetch();
    $stmt->close();

    if ($found && $term_id && $tt_id) {
        return [$term_id, $tt_id];
    }

    // se não existe, cria
    $slug = strtolower($name);
    if (function_exists('iconv')) {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
    }
    $slug = preg_replace('~[^a-z0-9]+~', '-', $slug);
    $slug = trim($slug, '-');
    if ($slug === '' || $slug === '-') {
        $slug = $taxonomy . '-' . md5($name);
    }

    $sql_ins_term = "INSERT INTO {$terms_table} (name, slug, term_group) VALUES (?, ?, 0)";
    $stmt = $mysqli->prepare($sql_ins_term);
    if (!$stmt) {
        return [0, 0];
    }
    $stmt->bind_param('ss', $name, $slug);
    $stmt->execute();
    $term_id = $stmt->insert_id;
    $stmt->close();

    if (!$term_id) {
        return [0, 0];
    }

    $sql_ins_tt = "INSERT INTO {$tt_table} (term_id, taxonomy, description, parent, count)
                   VALUES (?, ?, '', 0, 0)";
    $stmt = $mysqli->prepare($sql_ins_tt);
    if (!$stmt) {
        return [$term_id, 0];
    }
    $stmt->bind_param('is', $term_id, $taxonomy);
    $stmt->execute();
    $tt_id = $stmt->insert_id;
    $stmt->close();

    return [$term_id, $tt_id];
}

// Aplica termos (tags, categorias, etc.) em um post para uma taxonomia
function set_post_terms($mysqli, $terms_table, $tt_table, $tr_table, $post_id, array $names, $taxonomy) {
    if ($post_id <= 0) {
        return;
    }

    // remove termos antigos dessa taxonomia para o post
    $sql_del = "DELETE tr
                FROM {$tr_table} tr
                JOIN {$tt_table} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tr.object_id = ? AND tt.taxonomy = ?";
    $stmt = $mysqli->prepare($sql_del);
    if ($stmt) {
        $stmt->bind_param('is', $post_id, $taxonomy);
        $stmt->execute();
        $stmt->close();
    }

    foreach ($names as $name) {
        $name = limpar_espacos($name);
        if ($name === '') {
            continue;
        }

        list($term_id, $tt_id) = ensure_term($mysqli, $terms_table, $tt_table, $name, $taxonomy);
        if (!$term_id || !$tt_id) {
            continue;
        }

        // relaciona post -> termo
        $sql_rel = "INSERT IGNORE INTO {$tr_table} (object_id, term_taxonomy_id, term_order)
                    VALUES (?, ?, 0)";
        $stmt2 = $mysqli->prepare($sql_rel);
        if ($stmt2) {
            $stmt2->bind_param('ii', $post_id, $tt_id);
            $stmt2->execute();
            $stmt2->close();
        }

        // atualiza count da taxonomia
        $sql_cnt = "UPDATE {$tt_table}
                    SET count = (SELECT COUNT(*) FROM {$tr_table} WHERE term_taxonomy_id = ?)
                    WHERE term_taxonomy_id = ?";
        $stmt3 = $mysqli->prepare($sql_cnt);
        if ($stmt3) {
            $stmt3->bind_param('ii', $tt_id, $tt_id);
            $stmt3->execute();
            $stmt3->close();
        }
    }
}

// ---------------------------------------------------------------------
// Ler CSV e atualizar posts EXISTENTES
// ---------------------------------------------------------------------
if (($handle = fopen($csv_path, 'r')) === false) {
    echo "Não consegui abrir o CSV.\n";
    exit(1);
}

$header = fgetcsv($handle, 0, ',');
if ($header === false) {
    echo "CSV sem cabeçalho.\n";
    exit(1);
}

$idx = array_flip($header);
$count = 0;
$nao_encontrados = 0;

while (($row = fgetcsv($handle, 0, ',')) !== false) {
    $slug             = isset($idx['slug']) ? trim($row[$idx['slug']] ?? '') : '';
    $post_title       = isset($idx['post_title']) ? trim($row[$idx['post_title']] ?? '') : '';
    $post_content     = isset($idx['post_content']) ? (string)($row[$idx['post_content']] ?? '') : '';
    $meta_title       = isset($idx['meta_title']) ? trim($row[$idx['meta_title']] ?? '') : '';
    $meta_description = isset($idx['meta_description']) ? trim($row[$idx['meta_description']] ?? '') : '';
    $tags_raw         = isset($idx['tags']) ? trim($row[$idx['tags']] ?? '') : '';
    $cats_raw         = isset($idx['categories']) ? trim($row[$idx['categories']] ?? '') : '';

    if ($slug === '' || $post_title === '') {
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

    // procura post EXISTENTE pelo slug
    $sql_post = "SELECT ID FROM {$posts_table} WHERE post_name = ? AND post_type = 'post' LIMIT 1";
    $stmt = $mysqli->prepare($sql_post);
    if (!$stmt) {
        echo "Erro preparando SQL para slug {$slug}\n";
        continue;
    }
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $stmt->bind_result($post_id);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found || !$post_id) {
        echo "Não encontrado (slug): {$slug}\n";
        $nao_encontrados++;
        continue;
    }

    // Atualiza título e conteúdo
    $sql_up_post = "UPDATE {$posts_table} SET post_title = ?, post_content = ? WHERE ID = ?";
    $stmt2 = $mysqli->prepare($sql_up_post);
    if ($stmt2) {
        $stmt2->bind_param('ssi', $post_title, $post_content, $post_id);
        $stmt2->execute();
        $stmt2->close();
    }

    // Yoast metas
    $focuskw = gerar_focus_kw($meta_title, $post_title);

    upsert_meta($mysqli, $meta_table, $post_id, '_yoast_wpseo_title',    $meta_title);
    upsert_meta($mysqli, $meta_table, $post_id, '_yoast_wpseo_metadesc', $meta_description);
    if ($focuskw !== '') {
        upsert_meta($mysqli, $meta_table, $post_id, '_yoast_wpseo_focuskw', $focuskw);
    }

    // TAGS (coluna "tags" no CSV, separadas por ;)
    $tags_list = [];
    if ($tags_raw !== '') {
        foreach (explode(';', $tags_raw) as $tag_name) {
            $tag_name = limpar_espacos($tag_name);
            if ($tag_name !== '') {
                $tags_list[] = $tag_name;
            }
        }
        if (!empty($tags_list)) {
            set_post_terms($mysqli, $terms_table, $tt_table, $tr_table, $post_id, $tags_list, 'post_tag');
        }
    }

    // CATEGORIAS (coluna "categories" no CSV, separadas por ;)
    $cats_list = [];
    if ($cats_raw !== '') {
        foreach (explode(';', $cats_raw) as $cat_name) {
            $cat_name = limpar_espacos($cat_name);
            if ($cat_name !== '') {
                $cats_list[] = $cat_name;
            }
        }
        if (!empty($cats_list)) {
            set_post_terms($mysqli, $terms_table, $tt_table, $tr_table, $post_id, $cats_list, 'category');
        }
    }

    $tags_desc = empty($tags_list) ? '-' : implode(', ', $tags_list);
    $cats_desc = empty($cats_list) ? '-' : implode(', ', $cats_list);

    echo "Atualizado: {$slug} (ID {$post_id}) | focus: {$focuskw} | tags: {$tags_desc} | cats: {$cats_desc}\n";
    $count++;
}

fclose($handle);

echo "Finalizado. Atualizados {$count} posts. Slugs não encontrados: {$nao_encontrados}.\n";
