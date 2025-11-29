<?php
// Uso: wp eval-file set_shipping_dims.php --allow-root
// Para simular sem salvar: defina $DRY_RUN = true;
$DRY_RUN = false;

function logln($msg){ \WP_CLI::line($msg); }

/**
 * Atualiza _weight, _length, _width, _height por SKU
 * $data = [ 'SKU' => [peso, comprimento, largura, altura] ]
 */
function apply_dims(array $data, $label){
  global $wpdb, $DRY_RUN;
  \WP_CLI::log("== $label ==");
  foreach($data as $sku => $v){
    list($w,$L,$W,$H) = $v;
    $post_id = $wpdb->get_var($wpdb->prepare(
      "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_sku' AND meta_value=%s LIMIT 1",
      $sku
    ));
    if(!$post_id){ \WP_CLI::warning("SKU não encontrado: $sku"); continue; }

    if($DRY_RUN){
      \WP_CLI::line("DRY-RUN $sku (post $post_id): peso=$w kg, CxLxA={$L}x{$W}x{$H} cm");
      continue;
    }
    update_post_meta($post_id, '_weight', $w);
    update_post_meta($post_id, '_length', $L);
    update_post_meta($post_id, '_width',  $W);
    update_post_meta($post_id, '_height', $H);
    \WP_CLI::success("$sku (post $post_id) atualizado: $w kg | {$L}x{$W}x{$H} cm");
  }
}

/* --------------------------
   CANON (planilha anterior)
   SKUs: CANON-001..007 + CANON-009..012
   Valores aproximados de embalagem
---------------------------*/
$canon = [
  // Identificados
  'CANON-001-GYNJET' => [ 9.5, 66, 40, 24], // PIXMA iX6810 (A3)
  'CANON-002-GYNJET' => [11.0, 49, 52, 33], // MAXIFY GX6010
  'CANON-003-GYNJET' => [ 8.2, 55, 40, 26], // PIXMA G3160
  'CANON-004-GYNJET' => [ 8.2, 55, 40, 26], // PIXMA G3111
  'CANON-005-GYNJET' => [ 9.2, 52, 45, 29], // PIXMA G6010 (single)
  'CANON-006-GYNJET' => [12.5, 50, 52, 35], // MAXIFY GX7010 (com ADF)
  'CANON-007-GYNJET' => [ 8.2, 55, 40, 26], // PIXMA G2160
  // Genéricos (009..012) — tanque padrão Canon
  'CANON-009-GYNJET' => [ 8.2, 55, 40, 26],
  'CANON-010-GYNJET' => [ 8.2, 55, 40, 26],
  'CANON-011-GYNJET' => [ 8.2, 55, 40, 26],
  'CANON-012-GYNJET' => [ 8.2, 55, 40, 26],
];

/* --------------------------
   BROTHER (lote identificado 101..110)
---------------------------*/
$brother = [
  'BROTHER-101-GYNJET' => [21.0, 62, 52, 43], // MFC-J5855DW (Inkvestment A3 ADF)  ~shipping
  'BROTHER-102-GYNJET' => [ 1.1, 36, 12,  9], // Scanner DS-740D
  'BROTHER-103-GYNJET' => [22.0, 51, 49, 45], // HL-L3230CDW (laser color)
  'BROTHER-104-GYNJET' => [ 7.0, 43, 33, 30], // HL-1212W (laser mono compacto)
  'BROTHER-105-GYNJET' => [ 8.5, 53, 43, 27], // DCP-T420W (InkTank)
  'BROTHER-106-GYNJET' => [ 8.7, 53, 43, 27], // DCP-T520W (InkTank)
  'BROTHER-107-GYNJET' => [12.0, 58, 48, 32], // DCP-T720DW (InkTank ADF)
  'BROTHER-108-GYNJET' => [22.0, 51, 49, 45], // HL-L3270CDW (laser color)
  'BROTHER-109-GYNJET' => [15.0, 52, 50, 43], // DCP-L2540DW (laser mono MFP)
  'BROTHER-110-GYNJET' => [ 9.5, 47, 43, 30], // HL-L2360DW (laser mono)
];

/* --------------------------
   (Opcional) BROTHER lote anterior 001..011
   Se precisar, adicione aqui com os mesmos valores.
---------------------------*/

// Executa
apply_dims($canon,   'CANON');
apply_dims($brother, 'BROTHER');

\WP_CLI::success('Concluído.');
