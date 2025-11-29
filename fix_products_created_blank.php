<?php
// Uso: wp eval-file fix_products_created_blank.php --allow-root
if (!defined('WP_CLI')) { echo "Use via WP-CLI\n"; exit(1); }

$BASE = 'https://imagens.gynjet.com.br/produtos/2025';

// Dados dos 7 itens que ficaram como "Produto"
$items = [
  // sku, modelo, categoria, arquivo
  ['HP-DESIGNJET-T230-24-GYNJET', 'HP DesignJet T230 24" (A1) – Plotter', 'Impressoras', '180_impressora_plotter_hp_designjet_t230_61cm_boca_24_a1_com_(1).jpg'],
  ['HP-DESIGNJET-T250-24-GYNJET', 'HP DesignJet T250 24" (A1) – Plotter', 'Impressoras', '180_impressora_plotter_hp_designjet_t250_61cm_boca_24_a1_com_.jpg'],
  ['HP-DESIGNJET-T650-36-GYNJET', 'HP DesignJet T650 36" (A0) – Plotter', 'Impressoras', '180_impressora_plotter_hp_designjet_t650_91cm_boca_36_a0_com_.jpg'],
  ['HP-OFFICEJET-PRO-7740-GYNJET', 'HP OfficeJet Pro 7740 A3 – Multifuncional', 'Impressoras', '180_impressora_multifuncional_hp_officejet_pro_7740_a3_com_2_(1).jpg'],
  ['HP-OFFICEJET-PRO-9020-GYNJET', 'HP OfficeJet Pro 9020 – Multifuncional', 'Impressoras', '180_impressora_multifuncional_hp_officejet_pro_9020_com_bulk_.jpg'],
  ['EPSON-WF-7830-GYNJET', 'Epson WorkForce Pro WF-7830 A3 – Multifuncional', 'Impressoras', '180_impressora_multifuncional_epson_workforce_pro_wf_7830_a3_(2).jpg'],
  ['KIT-BULK-INK-PLOTTER-HP-T250-T230-T210-GYNJET', 'Bulk ink para Plotter HP T250/T230/T210 (instalação)', 'Bulk ink', '180_bulk_ink_para_plotter_hp_t250_t230_t210_a1_24_para_instala.jpg'],
];

// descrição longa (300+) com FOCO e serviço Gynjet
function gynjet_long_desc($model){
  $focus = "$model Goiânia";
  return
  "<p><strong>$focus</strong> — FOCO em economia e qualidade de impressão. Indicado para quem precisa produtividade, custo por página previsível e operação simples.</p>".
  "<p><strong>Destaques</strong><br>• Produtividade e painel intuitivo.<br>• Economia com alto rendimento de suprimentos.<br>• Conectividade (Wi-Fi/Ethernet/USB conforme versão).<br>• Qualidade em textos, traços e cores.</p>".
  "<p><strong>Instalação GYNJET (serviço incluso)</strong><br>• Instalação de drivers oficiais e atualização de firmware.<br>• Configuração de rede, calibração e teste de impressão.<br>• Dicas de uso, manutenção preventiva e melhores práticas.<br>• Suporte local em Goiânia.</p>".
  "<p><strong>Dicas rápidas</strong><br>• Use papel/mídia compatível e mantenha em superfície plana/ventilada.<br>• Atualize drivers/utilitários quando solicitado.<br>• Agende limpezas preventivas e alinhamento de cabeçotes.</p>".
  "<p><strong>Links úteis</strong><br>• <a href=\"/categoria/impressoras/\">Impressoras Gynjet</a> (interno).<br>• <a href=\"https://www.google.com/search?q=".rawurlencode($model)."\" target=\"_blank\" rel=\"nofollow noopener\">Manuais e drivers</a> (externo).</p>";
}

foreach($items as [$sku,$model,$cat_name,$file]){
  $product_id = wc_get_product_id_by_sku($sku);
  if(!$product_id){ WP_CLI::warning("SKU não encontrado: $sku"); continue; }

  $title = "$model – em Goiânia";
  $focus = "$model Goiânia";
  $yoast_title = "$model | Gynjet Goiânia";
  $yoast_desc  = "$focus: FOCO em economia e qualidade de impressão. Gynjet fornece e instala. Suporte local em Goiânia.";
  $short = "FOCO em economia e qualidade. $model com excelente custo por página. Gynjet fornece e instala em Goiânia.";
  $tags = "impressora, ".(stripos($model,'hp')!==false?'hp':(stripos($model,'epson')!==false?'epson':'gynjet')).", ".
          ((stripos($model,'Plotter')!==false || stripos($model,'DesignJet')!==false || stripos($model,'SureColor')!==false) ? 'plotter' : 'multifuncional')
          .", goiânia, gynjet, instalação, economia";

  // Atualiza post (título, conteúdo, tipo)
  wp_update_post([
    'ID'          => $product_id,
    'post_title'  => $title,
    'post_content'=> gynjet_long_desc($model),
    'post_type'   => 'product',
    'post_status' => 'publish',
  ]);

  // Short desc / Yoast
  update_post_meta($product_id, '_short_description', $short);
  update_post_meta($product_id, '_yoast_wpseo_title', $yoast_title);
  update_post_meta($product_id, '_yoast_wpseo_metadesc', $yoast_desc);
  update_post_meta($product_id, '_yoast_wpseo_focuskw', $focus);
  update_post_meta($product_id, '_yoast_wpseo_focuskw_text_input', $focus);

  // Categoria
  $term = term_exists($cat_name, 'product_cat');
  if(!$term || is_wp_error($term)){ $term = wp_insert_term($cat_name, 'product_cat'); }
  if(!is_wp_error($term)){ wp_set_post_terms($product_id, [(int)$term['term_id']], 'product_cat', false); }

  // Tags
  wp_set_post_terms($product_id, array_map('trim', explode(',', $tags)), 'product_tag', false);

  // Imagem destacada (baixa da CDN e define ALT)
  $img_url = rtrim($BASE,'/').'/'.$file;
  $att_id = media_sideload_image($img_url, $product_id, $title, 'id');
  if(!is_wp_error($att_id)){
    set_post_thumbnail($product_id, $att_id);
    update_post_meta($att_id, '_wp_attachment_image_alt', "$focus - Gynjet Goiânia");
  } else {
    WP_CLI::warning("Falha ao baixar imagem: $img_url");
  }

  WP_CLI::success("$sku corrigido.");
}
