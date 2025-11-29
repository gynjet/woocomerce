<?php
// Run: wp eval-file set_alt_by_filename.php --allow-root
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
    WP_CLI::success("ALT set for $filename (ID: $id)");
  } else {
    WP_CLI::warning("Not found: $filename");
  }
}

// Alvo: p_56801_alta_1.png  -> HP Smart Tank 720
set_alt_by_filename('p_56801_alta_1.png', 'HP Smart Tank 720 - Gynjet Goiânia');
