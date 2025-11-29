<?php
defined('ABSPATH') or die('No script kiddies please!');
require_once(ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'upgrade.php');
global $wpdb;
$wpdb->hide_errors();
$notCreatedTables = [];
$mysqlError = "";
if (is_multisite()) {
$sites = $wpdb->get_results('SELECT blog_id AS id FROM `'.$wpdb->blogs.'` ORDER BY blog_id', ARRAY_A);
} else {
$sites = [['id' => -1]];
}
foreach ($sites as $site) {
if ($site['id'] !== -1) {
switch_to_blog($site['id']);
}
$tiReviewsTableName = $this->get_tablename('reviews');
include $this->get_plugin_dir() . 'include' . DIRECTORY_SEPARATOR . 'schema.php';
foreach (array_keys($ti_db_schema) as $tableName) {
if (!$this->is_table_exists($tableName)) {
dbDelta(trim($ti_db_schema[ $tableName ]));
}
if ($wpdb->last_error) {
$mysqlError = $wpdb->last_error;
}
if (!$this->is_table_exists($tableName)) {
$notCreatedTables []= $tableName;
}
}
if ($site['id'] !== -1) {
restore_current_blog();
}
}
if ($notCreatedTables) {
$this->loadI18N();
deactivate_plugins(plugin_basename($this->plugin_file_path));
$sqlsToRun = array_map(function($tableName) use($ti_db_schema) {
return trim($ti_db_schema[ $tableName ]);
}, $notCreatedTables);
$preStyle = 'background: #eee; padding: 10px 20px; word-wrap: break-word; white-space: pre-wrap';
wp_die(wp_kses_post(
'<strong>' . __('Plugin activation is failed because the required database tables could not created!', 'trustindex-plugin') . '</strong><br /><br />' .
sprintf(__('We got the following error from %s:', 'trustindex-plugin'), __('database', 'trustindex-plugin')) .
'<pre style="'. $preStyle .'">'. $mysqlError .'</pre>' .
'<strong>' . __('Run the following SQL codes in your database administration interface (e.g. PhpMyAdmin) to create the tables or contact your system administrator:', 'trustindex-plugin') . '</strong>' .
'<pre style="'. $preStyle .'">' . implode('</pre><pre style="'. $preStyle .'">', $sqlsToRun) . '</pre>' .
'<strong>' . __('Then try activate the plugin again.', 'trustindex-plugin') . '</strong>'
));
}
update_option($this->get_option_name('active'), '1');
?>