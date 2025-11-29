<?php
// Run: wp eval-file set_alt_batch.php --allow-root
if (!defined('WP_CLI')) { echo "Use via WP-CLI\n"; exit(1); }
global $wpdb;

function set_alt_by_filename($filename, $alt){
  global $wpdb;
  $sql = "SELECT p.ID
          FROM {$wpdb->posts} p
          LEFT JOIN {$wpdb->postmeta} m
            ON m.post_id=p.ID AND m.meta_key='_wp_attached_file'
          WHERE p.post_type='attachment'
            AND (p.guid LIKE %s OR m.meta_value LIKE %s OR p.post_title=%s)
          ORDER BY p.ID DESC
          LIMIT 1";
  $id = $wpdb->get_var( $wpdb->prepare($sql, "%$filename%", "%$filename%", $filename) );
  if ($id) {
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    WP_CLI::success("OK: $filename -> ID $id");
  } else {
    WP_CLI::warning("MISS: $filename");
  }
}

$map = [
  'p_46711_alta_2.jpg' => 'HP Smart Tank 516 - Gynjet Goiânia',
  'p_47792_alta_1.png' => 'HP Laser 107w - Gynjet Goiânia',
  'p_47807_alta_1.png' => 'HP Ink Tank 416 - Gynjet Goiânia',
  'p_49352_alta_1.png' => 'HP DeskJet 3776 - Gynjet Goiânia',
  'p_50080_alta_1.png' => 'HP Laser 107a - Gynjet Goiânia',
  'p_50146_alta_1.png' => 'HP Smart Tank 517 - Gynjet Goiânia',
  'p_50147_alta_1.png' => 'HP Neverstop 1000w - Gynjet Goiânia',
  'p_56584_alta_1.png' => 'HP Laser MFP 135a - Gynjet Goiânia',
  'p_56801_alta_1.png' => 'HP Smart Tank 720 - Gynjet Goiânia',
  'p_57097_alta_1.png' => 'HP Laser 107w - Gynjet Goiânia',
];

foreach($map as $fn => $alt) set_alt_by_filename($fn, $alt);
