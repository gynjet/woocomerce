<?php
defined('ABSPATH') or die('No script kiddies please!');
require_once(ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'upgrade.php');
global $wpdb;
if (version_compare($this->getVersion(), $this->getVersion('update-version-check'))) {
$tableName = $this->get_tablename('reviews');
$columns = array_column($wpdb->get_results('SHOW COLUMNS FROM `'. $tableName .'`', ARRAY_A), 'Field');

if (!in_array('highlight', $columns)) {
$wpdb->query('ALTER TABLE `'. $tableName .'` ADD highlight VARCHAR(11) NULL AFTER rating');
}

if (!in_array('reply', $columns)) {
$wpdb->query('ALTER TABLE `'. $tableName .'` ADD reply TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL AFTER date');
}
if (in_array('replied', $columns)) {
$wpdb->query('ALTER TABLE `'. $tableName .'` DROP replied');
}
if (!in_array('reviewId', $columns)) {
$wpdb->query('ALTER TABLE `'. $tableName .'` ADD reviewId TEXT NULL AFTER date');
}

if (!in_array('hidden', $columns)) {
$wpdb->query('ALTER TABLE `'. $tableName .'` ADD hidden TINYINT(1) NOT NULL DEFAULT 0 AFTER id');
}
$oldRateUs = get_option('trustindex-'. $this->getShortName() .'-rate-us');
if ($oldRateUs) {
if ($oldRateUs === 'hide') {
$this->setNotificationParam('rate-us', 'hidden', true);
}
else {
$this->setNotificationParam('rate-us', 'active', true);
$this->setNotificationParam('rate-us', 'timestamp', $oldRateUs);
}
}
$oldNotificationEmail = get_option('trustindex-'. $this->getShortName() .'-review-download-notification-email');
if ($oldNotificationEmail) {
$this->setNotificationParam('review-download-finished', 'email', $oldNotificationEmail);
}
$usedOptions = [];
foreach ($this->get_option_names() as $optName) {
$usedOptions []= $this->get_option_name($optName);
}
$wpdb->query('DELETE FROM `'. $wpdb->options .'` WHERE option_name LIKE "trustindex-'. $this->getShortName() .'-%" AND option_name NOT IN ("'. implode('", "', $usedOptions) .'")');
if (get_option($this->get_option_name('css-content'))) {
$cssCdnVersion = $this->getCdnVersion('widget-css');
if ($cssCdnVersion && version_compare($cssCdnVersion, $this->getVersion('widget-css'))) {
$this->noreg_save_css(true);
$this->updateVersion('widget-css', $cssCdnVersion);
}
}
if (get_option($this->get_option_name('review-content'))) {
$htmlCdnVersion = $this->getCdnVersion('widget-html');
if ($htmlCdnVersion && version_compare($htmlCdnVersion, $this->getVersion('widget-html'))) {
delete_option($this->get_option_name('review-content'));
$this->updateVersion('widget-html', $htmlCdnVersion);
}
}
$this->updateVersion('update-version-check', $this->getVersion());
}
?>