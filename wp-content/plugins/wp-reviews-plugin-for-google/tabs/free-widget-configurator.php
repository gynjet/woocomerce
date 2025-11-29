<?php
defined('ABSPATH') or die('No script kiddies please!');
$ti_command = isset($_REQUEST['command']) ? sanitize_text_field($_REQUEST['command']) : null;
$ti_command_list = [
'save-page',
'delete-page',
'save-style',
'save-filter-stars',
'save-set',
'save-language',
'save-dateformat',
'save-nameformat',
'save-top-rated-type',
'save-top-rated-date',
'save-options',
'save-align',
'save-fomo-icon',
'save-fomo-color',
'save-fomo-margin',
'save-review-text-mode',
'save-verified-by-trustindex',
'save-amp-notice-hide',

];
if (!in_array($ti_command, $ti_command_list)) {
$ti_command = null;
}
function trustindex_plugin_connect_page($pageDetails = null, $defaultSettings = true, $reviewDownload = false)
{
global $pluginManager;
global $pluginManagerInstance;
global $wpdb;
if (!$pageDetails) {
return false;
}
if ($pageDetails['name']) {
$pageDetails['name'] = json_encode($pageDetails['name']);
}
$pluginManagerInstance->setNotificationParam('not-using-no-connection', 'active', false);
$pluginManagerInstance->setNotificationParam('not-using-no-widget', 'active', true);
$pluginManagerInstance->setNotificationParam('not-using-no-widget', 'timestamp', time() + (2 * 3600));
$tableName = $pluginManagerInstance->get_tablename('reviews');
$wpdb->query('TRUNCATE `'. $tableName .'`');
$reviews = null;
if (isset($pageDetails['reviews'])) {
$reviews = $pageDetails['reviews'];
unset($pageDetails['reviews']);
}
$requestId = null;
if (isset($pageDetails['request_id'])) {
$requestId = $pageDetails['request_id'];
unset($pageDetails['request_id']);
}
else if (isset($_REQUEST['review_request_id'])) {
$requestId = $_REQUEST['review_request_id'];
}
if ($requestId) {
update_option($pluginManagerInstance->get_option_name('review-download-request-id'), $requestId, false);
}
$manualDownload = 0;
if (isset($pageDetails['manual_download'])) {
$manualDownload = (int)$pageDetails['manual_download'];
unset($pageDetails['manual_download']);
}
else if (isset($_REQUEST['manual_download'])) {
$manualDownload = (int)$_REQUEST['manual_download'];
}
delete_option($pluginManagerInstance->get_option_name('review-download-token'));
if ($reviewDownload) {
update_option($pluginManagerInstance->get_option_name('review-download-inprogress'), $reviewDownload, false);
update_option($pluginManagerInstance->get_option_name('review-manual-download'), $manualDownload, false);
update_option($pluginManagerInstance->get_option_name('review-download-is-connecting'), 1, false);
}
else {
delete_option($pluginManagerInstance->get_option_name('review-download-inprogress'));
delete_option($pluginManagerInstance->get_option_name('review-manual-download'));
delete_option($pluginManagerInstance->get_option_name('review-download-is-connecting'));
}
if (is_array($reviews)) {
foreach ($reviews as $row) {
$date = isset($row['created_at']) ? $row['created_at'] : (isset($row['date']) ? $row['date'] : '');
$wpdb->insert($tableName, [
'user' => $row['reviewer']['name'],
'user_photo' => $row['reviewer']['avatar_url'],
'text' => $row['text'],
'rating' => $row['rating'] ? $row['rating'] : 5,
'date' => substr($date, 0, 10),
'reviewId' => isset($row['id']) ? $row['id'] : null,
'reply' => isset($row['reply']) ? $row['reply'] : ""
]);
}
update_option($pluginManagerInstance->get_option_name('download-timestamp'), time() + (86400 * 10), false);
delete_option($pluginManagerInstance->get_option_name('review-download-inprogress'));
delete_option($pluginManagerInstance->get_option_name('review-manual-download'));
update_option($pluginManagerInstance->get_option_name('review-download-modal'), 0, false);
}
update_option($pluginManagerInstance->get_option_name('page-details'), $pageDetails, false);
$GLOBALS['wp_object_cache']->delete( $pluginManagerInstance->get_option_name('page-details'), 'options' );
if ($defaultSettings) {
$lang = strtolower(substr(get_locale(), 0, 2));
if (!isset($pluginManager::$widget_languages[ $lang ])) {
$lang = 'en';
}
update_option($pluginManagerInstance->get_option_name('lang'), $lang, false);
header('Location: admin.php?page=' . sanitize_text_field($_GET['page']) .'&tab=free-widget-configurator');
}
else {
$pluginManagerInstance->noreg_save_css(true);
}
}
function trustindex_plugin_disconnect_page($settingsDelete = true)
{
global $pluginManagerInstance;
global $wpdb;
$pluginManagerInstance->delete_async_request();
delete_option($pluginManagerInstance->get_option_name('review-download-inprogress'));
delete_option($pluginManagerInstance->get_option_name('review-download-request-id'));
delete_option($pluginManagerInstance->get_option_name('review-manual-download'));
delete_option($pluginManagerInstance->get_option_name('page-details'));
delete_option($pluginManagerInstance->get_option_name('review-content'));
delete_option($pluginManagerInstance->get_option_name('css-content'));
if (is_file($pluginManagerInstance->getCssFile())) {
unlink($pluginManagerInstance->getCssFile());
}
if ($settingsDelete) {
delete_option($pluginManagerInstance->get_option_name('style-id'));
delete_option($pluginManagerInstance->get_option_name('scss-set'));
delete_option($pluginManagerInstance->get_option_name('filter'));
delete_option($pluginManagerInstance->get_option_name('lang'));
delete_option($pluginManagerInstance->get_option_name('dateformat'));
delete_option($pluginManagerInstance->get_option_name('nameformat'));
delete_option($pluginManagerInstance->get_option_name('no-rating-text'));
delete_option($pluginManagerInstance->get_option_name('verified-icon'));
delete_option($pluginManagerInstance->get_option_name('enable-animation'));
delete_option($pluginManagerInstance->get_option_name('show-arrows'));
delete_option($pluginManagerInstance->get_option_name('show-header-button'));
delete_option($pluginManagerInstance->get_option_name('reviews-load-more'));
delete_option($pluginManagerInstance->get_option_name('show-reviewers-photo'));
delete_option($pluginManagerInstance->get_option_name('widget-setted-up'));
delete_option($pluginManagerInstance->get_option_name('show-review-replies'));
delete_option($pluginManagerInstance->get_option_name('verified-by-trustindex'));
}
$wpdb->query('TRUNCATE `'. $pluginManagerInstance->get_tablename('reviews') .'`');
$pluginManagerInstance->setNotificationParam('not-using-no-connection', 'active', true);
$pluginManagerInstance->setNotificationParam('not-using-no-connection', 'timestamp', time() + 86400);
$pluginManagerInstance->setNotificationParam('not-using-no-widget', 'active', false);
}
function trustindex_plugin_change_step($step = 5)
{
global $pluginManagerInstance;
if ($step < 5) {
$optionsToDelete = [
'widget-setted-up',
'align',
'review-text-mode',
'verified-icon',
'enable-animation',
'no-rating-text',
'disable-font',
'show-reviewers-photo',
'show-logos',
'show-stars',
'footer-filter-text',
'top-rated-type',
'top-rated-date',
'show-arrows',
'show-header-button',
'reviews-load-more',
'dateformat',
'nameformat',
'show-review-replies',
'verified-by-trustindex',
'fomo-open',
'fomo-link',
'fomo-border',
'fomo-arrow',
'fomo-icon',
'fomo-color',
'fomo-margin',
];
foreach ($optionsToDelete as $name) {
delete_option($pluginManagerInstance->get_option_name($name));
}
}
if ($step < 4) {
delete_option($pluginManagerInstance->get_option_name('scss-set'));
}
if ($step < 3) {
delete_option($pluginManagerInstance->get_option_name('style-id'));
}
if ($step < 2) {
trustindex_plugin_disconnect_page();
}
}
if ($ti_command === 'save-page') {
check_admin_referer('ti-save-page');
$pageDetails = isset($_POST['page_details']) ? json_decode(stripcslashes($_POST['page_details']), true) : null;
$reviewDownload = isset($_POST['review_download']) ? sanitize_text_field($_POST['review_download']) : 0;
trustindex_plugin_connect_page($pageDetails, true, $reviewDownload);
header('Location: admin.php?page=' . sanitize_text_field($_GET['page']) .'&tab=free-widget-configurator');
exit;
}
else if ($ti_command === 'delete-page') {
check_admin_referer('ti-delete-page');
trustindex_plugin_disconnect_page();
header('Location: admin.php?page='. sanitize_text_field($_GET['page']) .'&tab=free-widget-configurator');
exit;
}
else if ($ti_command === 'save-style') {
check_admin_referer('ti-save-style');
$styleId = (int)$_REQUEST['style_id'];
update_option($pluginManagerInstance->get_option_name('style-id'), $styleId, false);
delete_option($pluginManagerInstance->get_option_name('review-content'));
trustindex_plugin_change_step(3);
if (in_array($pluginManager::$widget_templates['templates'][$styleId]['type'], ['floating', 'fomo'])) {
$pluginManagerInstance->noreg_save_css();
}
if (isset($_GET['style_id'])) {
header('Location: admin.php?page='. sanitize_text_field($_GET['page']) .'&tab=free-widget-configurator');
}
exit;
}
else if ($ti_command === 'save-set') {
check_admin_referer('ti-save-set');
update_option($pluginManagerInstance->get_option_name('scss-set'), sanitize_text_field($_REQUEST['set_id']), false);
trustindex_plugin_change_step(4);
$pluginManagerInstance->noreg_save_css(true);
if (isset($_GET['verified_by_trustindex'])) {
update_option($pluginManagerInstance->get_option_name('verified-by-trustindex'), 1, false);
}
if (isset($_GET['set_id'])) {
header('Location: admin.php?page='. sanitize_text_field($_GET['page']) .'&tab=free-widget-configurator');
}
exit;
}
else if ($ti_command === 'save-filter-stars') {
check_admin_referer('ti-save-filter-stars');
$filter = $pluginManagerInstance->getWidgetOption('filter');
$filter['stars'] = isset($_POST['stars']) ? explode(',', sanitize_text_field($_POST['stars'])) : $pluginManagerInstance->getWidgetOption('filter', false, true)['stars'];
update_option($pluginManagerInstance->get_option_name('filter'), $filter, false);
exit;
}
else if ($ti_command === 'save-language') {
check_admin_referer('ti-save-language');
update_option($pluginManagerInstance->get_option_name('lang'), sanitize_text_field($_POST['lang']), false);
delete_option($pluginManagerInstance->get_option_name('review-content'));
if ($pluginManagerInstance->isRtlLanguage()) {
update_option($pluginManagerInstance->get_option_name('align'), 'right', false);
}
exit;
}
else if ($ti_command === 'save-dateformat') {
check_admin_referer('ti-save-dateformat');
update_option($pluginManagerInstance->get_option_name('dateformat'), sanitize_text_field($_POST['dateformat']), false);
exit;
}
else if ($ti_command === 'save-nameformat') {
check_admin_referer('ti-save-nameformat');
update_option($pluginManagerInstance->get_option_name('nameformat'), sanitize_text_field($_POST['nameformat']), false);
exit;
}
else if ($ti_command === 'save-top-rated-type') {
check_admin_referer('ti-save-top-rated-type');
update_option($pluginManagerInstance->get_option_name('top-rated-type'), sanitize_text_field($_POST['type']), false);
exit;
}
else if ($ti_command === 'save-top-rated-date') {
check_admin_referer('ti-save-top-rated-date');
update_option($pluginManagerInstance->get_option_name('top-rated-date'), sanitize_text_field($_POST['date']), false);
exit;
}
else if ($ti_command === 'save-options') {
$scssSet = get_option($pluginManagerInstance->get_option_name('scss-set'));
check_admin_referer('ti-save-options');
$r = 0;
if (isset($_POST['verified-icon'])) {
$r = sanitize_text_field($_POST['verified-icon']);
}
update_option($pluginManagerInstance->get_option_name('verified-icon'), $r, false);
$r = 1;
if (isset($_POST['enable-animation'])) {
$r = sanitize_text_field($_POST['enable-animation']);
}
update_option($pluginManagerInstance->get_option_name('enable-animation'), $r, false);
$r = 1;
if (isset($_POST['show-arrows'])) {
$r = sanitize_text_field($_POST['show-arrows']);
}
update_option($pluginManagerInstance->get_option_name('show-arrows'), $r, false);
$r = 1;
if (isset($_POST['show-header-button'])) {
$r = sanitize_text_field($_POST['show-header-button']);
}
update_option($pluginManagerInstance->get_option_name('show-header-button'), $r, false);
$r = 1;
if (isset($_POST['reviews-load-more'])) {
$r = sanitize_text_field($_POST['reviews-load-more']);
}
update_option($pluginManagerInstance->get_option_name('reviews-load-more'), $r, false);
$r = 1;
if (isset($_POST['show-reviewers-photo'])) {
$r = sanitize_text_field($_POST['show-reviewers-photo']);
}
update_option($pluginManagerInstance->get_option_name('show-reviewers-photo'), $r, false);
$r = 0;
if (isset($_POST['no-rating-text'])) {
$r = sanitize_text_field($_POST['no-rating-text']);
}
update_option($pluginManagerInstance->get_option_name('no-rating-text'), $r, false);
$r = 0;
if (isset($_POST['disable-font'])) {
$r = sanitize_text_field($_POST['disable-font']);
}
update_option($pluginManagerInstance->get_option_name('disable-font'), $r, false);
$r = 1;
if (isset($_POST['show-logos'])) {
$r = sanitize_text_field($_POST['show-logos']);
}
update_option($pluginManagerInstance->get_option_name('show-logos'), $r, false);
$r = 1;
if (isset($_POST['show-stars'])) {
$r = sanitize_text_field($_POST['show-stars']);
}
update_option($pluginManagerInstance->get_option_name('show-stars'), $r, false);
$r = 0;
if (isset($_POST['footer-filter-text'])) {
$r = sanitize_text_field($_POST['footer-filter-text']);
}
update_option($pluginManagerInstance->get_option_name('footer-filter-text'), $r, false);
$r = 0;
if (isset($_POST['show-review-replies'])) {
$r = sanitize_text_field($_POST['show-review-replies']);
}
update_option($pluginManagerInstance->get_option_name('show-review-replies'), $r, false);
$filter = $pluginManagerInstance->getWidgetOption('filter');
$filter['only-ratings'] = isset($_POST['only-ratings']) ? (bool)$_POST['only-ratings'] : $pluginManagerInstance->getWidgetOption('filter', false, true)['only-ratings'];
update_option($pluginManagerInstance->get_option_name('filter'), $filter, false);
$r = 1;
if (isset($_POST['fomo-open'])) {
$r = sanitize_text_field($_POST['fomo-open']);
}
update_option($pluginManagerInstance->get_option_name('fomo-open'), $r, false);
$r = 0;
if (isset($_POST['fomo-link'])) {
$r = sanitize_text_field($_POST['fomo-link']);
}
update_option($pluginManagerInstance->get_option_name('fomo-link'), $r, false);
$r = 1;
if (isset($_POST['fomo-border'])) {
$r = sanitize_text_field($_POST['fomo-border']);
}
update_option($pluginManagerInstance->get_option_name('fomo-border'), $r, false);
$r = 1;
if (isset($_POST['fomo-arrow'])) {
$r = sanitize_text_field($_POST['fomo-arrow']);
}
update_option($pluginManagerInstance->get_option_name('fomo-arrow'), $r, false);
exit;
}
else if ($ti_command === 'save-align') {
check_admin_referer('ti-save-align');
update_option($pluginManagerInstance->get_option_name('align'), sanitize_text_field($_POST['align']), false);
exit;
}
else if ($ti_command === 'save-fomo-icon') {
check_admin_referer('ti-save-fomo-icon');
update_option($pluginManagerInstance->get_option_name('fomo-icon'), sanitize_text_field($_POST['fomo-icon']), false);
exit;
}
else if ($ti_command === 'save-fomo-color') {
check_admin_referer('ti-save-fomo-color');
update_option($pluginManagerInstance->get_option_name('fomo-color'), sanitize_text_field($_POST['fomo-color']), false);
exit;
}
else if ($ti_command === 'save-fomo-margin') {
check_admin_referer('ti-save-fomo-margin');
update_option($pluginManagerInstance->get_option_name('fomo-margin'), sanitize_text_field($_POST['fomo-margin']), false);
exit;
}
else if ($ti_command === 'save-review-text-mode') {
check_admin_referer('ti-save-review-text-mode');
update_option($pluginManagerInstance->get_option_name('review-text-mode'), sanitize_text_field($_POST['review_text_mode']), false);
exit;
}
else if ($ti_command === 'save-verified-by-trustindex') {
check_admin_referer('ti-save-verified-by-trustindex');
update_option($pluginManagerInstance->get_option_name('verified-by-trustindex'), sanitize_text_field($_POST['verified-by-trustindex']), false);
exit;
}
else if ($ti_command === 'save-amp-notice-hide') {
update_option($pluginManagerInstance->get_option_name('amp-hidden-notification'), 1, false);
exit;
}
if (isset($_GET['recreate'])) {
check_admin_referer('ti-recreate');
$pluginManagerInstance->uninstall();
$pluginManagerInstance->activate();
header('Location: admin.php?page=' . sanitize_text_field($_GET['page']) .'&tab=free-widget-configurator');
exit;
}
if (isset($_GET['setup_widget'])) {
check_admin_referer('ti-setup-widget');
update_option($pluginManagerInstance->get_option_name('widget-setted-up'), 1, false);
header('Location: admin.php?page=' . sanitize_text_field($_GET['page']) .'&tab=free-widget-configurator');
}
$reviews = [];
if ($pluginManagerInstance->is_noreg_linked()) {
$reviews = $wpdb->get_results('SELECT * FROM `'. $pluginManagerInstance->get_tablename('reviews') .'` ORDER BY date DESC');
}
$isReviewDownloadInProgress = $pluginManagerInstance->is_review_download_in_progress();
$styleId = (int)$pluginManagerInstance->getWidgetOption('style-id', true);
$scssSet = $pluginManagerInstance->getWidgetOption('scss-set', true);
$widgetSettedUp = $pluginManagerInstance->getWidgetOption('widget-setted-up');
if (!$pluginManagerInstance->is_noreg_linked()) {
$styleId = null;
$scssSet = null;
$widgetSettedUp = null;
} else {
$pageDetails = $pluginManagerInstance->getPageDetails();
$isTopRatedBadge = $styleId ? $pluginManager::$widget_templates['templates'][$styleId]['is-top-rated-badge'] : false;
if ($isTopRatedBadge) {
$isTopRatedBadgeValid = isset($pageDetails['rating_score']) ? (float)$pageDetails['rating_score'] >= $pluginManager::$topRatedMinimumScore : false;
}
}
wp_enqueue_style('trustindex-widget-preview-css', 'https://cdn.trustindex.io/assets/ti-preview-box.css');
?>
<?php
$stepUrl = '?page='. $_GET['page'] .'&tab=free-widget-configurator&step=%step%';
$stepList = [
sprintf(__('Connect %s', 'trustindex-plugin'), 'Google'),
__('Select Layout', 'trustindex-plugin'),
__('Select Style', 'trustindex-plugin'),
__('Set up widget', 'trustindex-plugin'),
__('Insert code', 'trustindex-plugin')
];
$stepDone = 0;
$stepCurrent = isset($_GET['step']) ? (int)sanitize_text_field($_GET['step']) : 0;
if ($stepCurrent === 3 && in_array($pluginManager::$widget_templates['templates'][$styleId]['type'], ['floating', 'fomo'])) {
$stepCurrent = 4;
}
if ($widgetSettedUp) {
$stepDone = 4;
}
else if ($scssSet) {
$stepDone = 3;
}
else if ($styleId) {
$stepDone = 2;
}
else if ($pluginManagerInstance->is_noreg_linked()) {
$stepDone = 1;
}
if ($stepDone >= 4 && $isTopRatedBadge && !$isTopRatedBadgeValid) {
$stepDone = 3;
}
if (!$stepCurrent) {
$stepCurrent = $stepDone + 1;
} else if ($stepCurrent > ($stepDone + 1)) {
$stepCurrent = $stepDone + 1;
}
include(plugin_dir_path(__FILE__) . '../include/step-list.php');
?>
<div class="ti-container<?php if ($stepCurrent < 5): ?> ti-narrow-page<?php endif; ?>">
<?php if ($pluginManagerInstance->is_trustindex_connected()): ?>
<div class="ti-notice ti-notice-warning">
<p>
<?php
$advancedTab = '<a href="?page='.esc_attr($_GET['page']).'&tab=advanced#trustindex-admin">'.__('Advanced', 'trustindex-plugin').'</a>';
echo sprintf(__("You have connected your Trustindex account, so you can find premium functionality under the %s tab. You no longer need this tab unless you choose the limited but forever free mode.", 'trustindex-plugin'), $advancedTab);
?>
</p>
</div>
<?php endif; ?>

<?php if ($pluginManager::is_amp_active() && !get_option($pluginManagerInstance->get_option_name('amp-hidden-notification'), 0)): ?>
<div class="ti-notice ti-notice-warning is-dismissible">
<p>
<?php echo __('Free plugin features are unavailable with AMP plugin.', 'trustindex-plugin'); ?>
<?php if ($pluginManagerInstance->is_trustindex_connected()): ?>
 <a href="?page=<?php echo esc_attr($_GET['page']); ?>&tab=advanced">Trustindex admin</a>
<?php else: ?>
 <a href="https://www.trustindex.io/?a=sys&c=wp-amp" target="_blank"><?php echo __('Try premium features (like AMP) for free', 'trustindex-plugin'); ?></a>
<?php endif; ?>
</p>
<button type="button" class="notice-dismiss" data-command="save-amp-notice-hide"></button>
</div>
<?php endif; ?>
<?php if ($stepCurrent === 1): ?>
<h1 class="ti-header-title"><?php echo sprintf(__('Connect %s', 'trustindex-plugin'), 'Google'); ?></h1>
<?php if ($pluginManagerInstance->is_noreg_linked()): ?>
<div class="ti-source-box">
<?php if (isset($pageDetails['avatar_url']) && $pageDetails['avatar_url']): ?>
<img src="<?php echo esc_url($pageDetails['avatar_url']); ?>" />
<?php endif; ?>
<div class="ti-source-info">
<?php if (isset($pageDetails['name']) && $pageDetails['name']): ?>
<strong><?php echo esc_html($pageDetails['name']); ?></strong><br />
<?php endif; ?>
<?php if (isset($pageDetails['address']) && $pageDetails['address']): ?>
<?php echo esc_html($pageDetails['address']); ?><br />
<?php endif; ?>
<a href="<?php echo esc_url($pluginManagerInstance->getPageUrl()); ?>" target="_blank"><?php echo esc_url($pluginManagerInstance->getPageUrl()); ?></a>
</div>
<a href="<?php echo wp_nonce_url('?page='. esc_attr($_GET['page']) .'&tab=free-widget-configurator&command=delete-page', 'ti-delete-page'); ?>" class="ti-btn ti-btn-primary ti-btn-loading-on-click"><?php echo __('Disconnect', 'trustindex-plugin'); ?></a>
</div>
<?php else: ?>
<div class="ti-box">
<form method="post" action="" data-platform="google" id="ti-connect-platform-form">
<?php wp_nonce_field('ti-save-page'); ?>
<input type="hidden" name="command" value="save-page" />
<input type="hidden" name="page_details" required="required" id="ti-noreg-page-details" value="" />
<?php
$reviewDownloadToken = get_option($pluginManagerInstance->get_option_name('review-download-token'));
if (!$reviewDownloadToken) {
$reviewDownloadToken = wp_create_nonce('ti-noreg-connect-token');
update_option($pluginManagerInstance->get_option_name('review-download-token'), $reviewDownloadToken, false);
}
?>
<input type="hidden" id="ti-noreg-connect-token" name="ti-noreg-connect-token" value="<?php echo $reviewDownloadToken; ?>" />
<input type="hidden" id="ti-noreg-webhook-url" value="<?php echo $pluginManagerInstance->getWebhookUrl(); ?>" />
<input type="hidden" id="ti-noreg-email" value="<?php echo get_option('admin_email'); ?>" />
<input type="hidden" id="ti-noreg-version" value="<?php echo esc_attr($pluginManagerInstance->getVersion()); ?>" />
<input type="hidden" id="ti-noreg-review-download" name="review_download" value="0" />
<input type="hidden" id="ti-noreg-review-request-id" name="review_request_id" value="" />
<input type="hidden" id="ti-noreg-manual-download" name="manual_download" value=0 />
<input type="hidden" id="ti-noreg-page-id" value="" />
<div class="ti-notice ti-notice-info ti-d-none" id="ti-connect-info">
<p><?php echo __("A popup window should be appear! Please, go to there and continue the steps! (If there is no popup window, you can check the the browser's popup blocker)", 'trustindex-plugin'); ?></p>
</div>

<a href="#" class="ti-btn btn-connect-public"><?php echo __('Connect', 'trustindex-plugin'); ?></a>


</form>
</div>
<?php endif; ?>
<h1 class="ti-header-title ti-mt-2"><?php echo sprintf(__('Check some %s widget layouts and styles', 'trustindex-plugin'), 'Google Reviews'); ?></h1>
<?php include(plugin_dir_path(__FILE__) . '../include/demo-widgets.php'); ?>
<?php elseif ($stepCurrent === 2): ?>
<h1 class="ti-header-title"><?php echo __('Select Layout', 'trustindex-plugin'); ?></h1>
<?php if (!count($reviews) && !$isReviewDownloadInProgress): ?>
<div class="ti-notice ti-notice-warning" style="margin: 0 0 15px 0">
<p>
<?php echo sprintf(__('There are no reviews on your %s platform.', 'trustindex-plugin'), 'Google'); ?>
</p>
</div>
<?php endif; ?>
<div class="ti-box ti-box-filter">
<label><?php echo __('Layout', 'trustindex-plugin'); ?>:</label>
<span class="ti-checkbox">
<input type="radio" name="layout-select" value="" data-ids="" checked>
<label><?php echo __('All', 'trustindex-plugin'); ?></label>
</span>
<?php foreach ($pluginManager::$widget_templates['categories'] as $category => $ids): ?>
<span class="ti-checkbox">
<input type="radio" name="layout-select" value="<?php echo esc_attr($category); ?>" data-ids="<?php echo esc_attr($ids); ?>">
<label><?php
$categoryName = ucwords(str_replace('-', ' ', $category));
echo esc_html(__($categoryName, 'trustindex-plugin'));
?></label>
</span>
<?php endforeach; ?>
</div>
<div class="ti-preview-boxes-container">
<?php foreach ($pluginManager::$widget_templates['templates'] as $id => $template): ?>
<?php
$className = 'ti-full-width';
if (in_array($template['type'], [ 'badge', 'button', 'floating', 'popup', 'sidebar', 'top-rated-badge', 'fomo' ])) {
$className = 'ti-half-width';
}
$set = 'light-background';
if (in_array($template['type'], [ 'badge', 'button' ])) {
$set = 'drop-shadow';
}
if ($template['is-top-rated-badge']) {
$set = 'light-minimal';
if (isset($template['params']['top-rated-badge-border']) && $template['params']['top-rated-badge-border']) {
$set = 'ligth-border';
}
}
$isTopRatedBadgeValid = isset($pageDetails['rating_score']) ? (float)$pageDetails['rating_score'] >= $pluginManager::$topRatedMinimumScore : false;
$fomoWidgetInvalid = in_array($id, [119, 120]) && (!isset($pageDetails['rating_numbers']) || !isset($pageDetails['rating_numbers_last']));

if (!isset($template['is-active']) || $template['is-active']):
?>
<div class="<?php echo esc_attr($className); ?>">
<div class="ti-box ti-preview-boxes" data-layout-id="<?php echo esc_attr($id); ?>" data-set-id="<?php echo $set; ?>">
<div class="ti-box-inner">
<div class="ti-box-header ti-box-header-normal">
<?php echo __('Layout', 'trustindex-plugin'); ?>:
<strong><?php echo esc_html(__($template['name'], 'trustindex-plugin')); ?></strong>
<?php if ($template['is-popular']): ?>
<span class="ti-badge ti-most-popular-badge ti-tooltip">
<?php echo __('Most popular', 'trustindex-plugin'); ?> <span class="dashicons dashicons-info-outline"></span>
<span class="ti-tooltip-message"><?php echo
__('Selected by most users!', 'trustindex-plugin').' '.
__('This widget layout helps build trust and effectively increases sales.', 'trustindex-plugin');
?></span>
</span>
<?php endif; ?>
<?php if ((!$template['is-top-rated-badge'] || $isTopRatedBadgeValid) && !$fomoWidgetInvalid): ?>
<a href="<?php echo wp_nonce_url('?page='. esc_attr($_GET['page']) .'&tab=free-widget-configurator&command=save-style&style_id='. esc_attr(urlencode($id)), 'ti-save-style'); ?>" class="ti-btn ti-btn-sm ti-btn-loading-on-click ti-pull-right"><?php echo __('Select', 'trustindex-plugin'); ?></a>
<div class="clear"></div>
<?php endif; ?>
</div>
<div class="preview">
<?php if ($template['is-top-rated-badge'] && !$isTopRatedBadgeValid): ?>
<div class="ti-notice ti-notice-info" style="margin: 0 0 15px 0">
<p>
<?php echo sprintf(__('Our exclusive "Top Rated" badge is awarded to service providers with a rating of %s and above.', 'trustindex-plugin'), $pluginManager::$topRatedMinimumScore); ?><br />
</p>
</div>
<?php endif; ?>
<?php if ($fomoWidgetInvalid): ?>
<div class="ti-notice ti-notice-info" style="margin: 0 0 15px 0">
<p>
<?php echo __('Update your reviews to use this widget.', 'trustindex-plugin'); ?><br />
</p>
</div>
<?php endif; ?>
<?php echo $pluginManagerInstance->renderWidgetAdmin(true, false, ['style-id' => $id, 'set-id' => $set]); ?>
</div>
</div>
</div>
</div>
<?php endif; ?>
<?php endforeach; ?>
</div>
<?php elseif ($stepCurrent === 3): ?>
<h1 class="ti-header-title"><?php echo __('Select Style', 'trustindex-plugin'); ?></h1>
<?php if (!count($reviews) && !$isReviewDownloadInProgress): ?>
<div class="ti-notice ti-notice-warning" style="margin: 0 0 15px 0">
<p>
<?php echo sprintf(__('There are no reviews on your %s platform.', 'trustindex-plugin'), 'Google'); ?>
</p>
</div>
<?php endif; ?>
<?php
$className = 'ti-full-width';
if (in_array($pluginManager::$widget_templates['templates'][ $styleId ]['type'], [ 'badge', 'button', 'floating', 'popup', 'sidebar', 'top-rated-badge' ])) {
$className = 'ti-half-width';
}
?>
<div class="ti-preview-boxes-container">
<?php
$isFirstLayout = true;
$isVerifiedByTrustindexAvailable = $pluginManagerInstance->isVerifiedByTrustindexAvailable();
foreach ($pluginManager::$widget_styles as $id => $style): ?>
<?php if (!isset($style['is-active']) || $style['is-active']): ?>
<div class="<?php echo esc_attr($className); ?>">
<div class="ti-box ti-preview-boxes" data-layout-id="<?php echo esc_attr($styleId); ?>" data-set-id="<?php echo esc_attr($id); ?>">
<div class="ti-box-inner">
<div class="ti-box-header ti-box-header-normal">
<?php echo __('Style', 'trustindex-plugin'); ?>:
<strong><?php echo __($style['name'], 'trustindex-plugin'); ?></strong>
<?php if ($isFirstLayout && !$isVerifiedByTrustindexAvailable): ?>
<span class="ti-badge ti-most-popular-badge ti-tooltip">
<?php echo __('Most popular', 'trustindex-plugin'); ?> <span class="dashicons dashicons-info-outline"></span>
<span class="ti-tooltip-message"><?php echo
__('Selected by most users!', 'trustindex-plugin').' '.
__('This widget style helps build trust and effectively increases sales.', 'trustindex-plugin');
?></span>
</span>
<?php endif; ?>
<a href="<?php echo wp_nonce_url('?page='. esc_attr($_GET['page']) .'&tab=free-widget-configurator&command=save-set&set_id='. esc_attr(urlencode($id)), 'ti-save-set'); ?>" class="ti-btn ti-btn-sm ti-btn-loading-on-click ti-pull-right"><?php echo __('Select', 'trustindex-plugin'); ?></a>
<div class="clear"></div>
</div>
<div class="preview">
<?php echo $pluginManagerInstance->renderWidgetAdmin(true, false, ['style-id' => $styleId, 'set-id' => $id]); ?>
</div>
</div>
</div>
</div>
<?php if ($id === 'light-background' && $isVerifiedByTrustindexAvailable): ?>
<div class="<?php echo esc_attr($className); ?>">
<div class="ti-box ti-preview-boxes" data-layout-id="<?php echo esc_attr($styleId); ?>" data-set-id="<?php echo esc_attr($id); ?>">
<div class="ti-box-inner">
<div class="ti-box-header ti-box-header-normal">
<?php echo __('Style', 'trustindex-plugin'); ?>:
<strong>
<?php echo __($style['name'], 'trustindex-plugin'); ?>
-
<?php echo __('with Trustindex verified', 'trustindex-plugin'); ?>
</strong>
<?php if ($className === 'ti-half-width'): ?><br /><?php endif; ?>
<span class="ti-badge ti-most-popular-badge ti-tooltip">
<?php echo __('Most popular', 'trustindex-plugin'); ?> <span class="dashicons dashicons-info-outline"></span>
<span class="ti-tooltip-message"><?php echo
__('Selected by most users!', 'trustindex-plugin').' '.
__('This widget style helps build trust and effectively increases sales.', 'trustindex-plugin');
?></span>
</span>
<a href="<?php echo wp_nonce_url('?page='. esc_attr($_GET['page']) .'&tab=free-widget-configurator&command=save-set&set_id='. esc_attr(urlencode($id)), 'ti-save-set'); ?>&verified_by_trustindex" class="ti-btn ti-btn-sm ti-btn-loading-on-click ti-pull-right"><?php echo __('Select', 'trustindex-plugin'); ?></a>
<div class="clear"></div>
</div>
<div class="preview">
<?php echo $pluginManagerInstance->renderWidgetAdmin(true, false, ['style-id' => $styleId, 'set-id' => $id, 'verified-by-trustindex' => true]); ?>
</div>
</div>
<div class="ti-notice ti-notice-info ti-verified-badge-notice">
<p>
<span class="dashicons dashicons-star-empty"></span> <strong><?php echo esc_html(__('Congratulations!', 'trustindex-plugin')); ?></strong><br />
<?php echo sprintf(__('Our system ranked you in the top %d%% of companies based on your reviews. Your total rating score above %s in the last %d month, and your reviews are genuine', 'trustindex-plugin'), 5, $pluginManager::$topRatedMinimumScore, 12); ?><br />
<?php echo __('This allows you to <strong>use in the widgets the Trustindex verified badge, the Universal Symbol of Trust.</strong> With the verified badge you can build more trust, and sell more!', 'trustindex-plugin'); ?>
</p>
</div>
</div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php
$isFirstLayout = false;
endforeach; ?>
</div>
<?php elseif ($stepCurrent === 4): ?>
<?php $widgetType = $pluginManager::$widget_templates['templates'][$styleId]['type']; ?>
<h1 class="ti-header-title"><?php echo __('Set up widget', 'trustindex-plugin'); ?></h1>
<?php if (!count($reviews) && !$isReviewDownloadInProgress): ?>
<div class="ti-notice ti-notice-warning" style="margin: 0 0 15px 0">
<p>
<?php echo sprintf(__('There are no reviews on your %s platform.', 'trustindex-plugin'), 'Google'); ?>
</p>
</div>
<?php endif; ?>
<?php if ($isTopRatedBadge && !$isTopRatedBadgeValid): ?>
<div class="ti-notice ti-notice-error" style="margin: 0 0 15px 0">
<p>
<?php echo sprintf(__('Our exclusive "Top Rated" badge is awarded to service providers with a rating of %s and above.', 'trustindex-plugin'), $pluginManager::$topRatedMinimumScore); ?><br />
<a href="?page=<?php echo esc_attr($_GET['page']); ?>&tab=free-widget-configurator&step=2" class="ti-btn ti-btn-sm ti-btn-loading-on-click" style="margin-top: 10px"><?php echo __('Please select another widget', 'trustindex-plugin'); ?></a>
</p>
</div>
<?php endif; ?>
<div class="ti-preview-boxes-container">
<div class="ti-full-width">
<div class="ti-box ti-preview-boxes" data-layout-id="<?php echo esc_attr($styleId); ?>" data-set-id="<?php echo esc_attr($scssSet); ?>">
<div class="ti-box-inner">
<div class="ti-box-header">
<?php echo __('Widget Preview', 'trustindex-plugin'); ?>
<?php if (!in_array($styleId, [17, 21, 52, 53, 112, 114])): ?>
<span class="ti-box-header-normal ti-pull-right">
<?php echo __('Style', 'trustindex-plugin'); ?>:
<strong><?php echo esc_html(__($pluginManager::$widget_styles[ $scssSet ]['name'], 'trustindex-plugin')); ?></strong>
</span>
<?php endif; ?>
<span class="ti-box-header-normal ti-pull-right">
<?php echo __('Layout', 'trustindex-plugin'); ?>:
<strong><?php echo esc_html(__($pluginManager::$widget_templates['templates'][ $styleId ]['name'], 'trustindex-plugin')); ?></strong>
</span>
</div>
<div class="preview ti-widget-editor-preview">
<?php echo $pluginManagerInstance->renderWidgetAdmin(true); ?>
</div>
</div>
</div>
</div>
</div>
<?php $filter = $pluginManagerInstance->getWidgetOption('filter'); ?>
<div class="ti-preview-boxes-container">
<div class="ti-full-width">
<div class="ti-box">
<div class="ti-box-inner">
<div class="ti-box-header"><?php echo __('Widget Settings', 'trustindex-plugin'); ?></div>
<div class="ti-left-block" id="ti-widget-selects">
<?php if ($pluginManagerInstance->isVerifiedByTrustindexAvailable()): ?>
<div class="ti-form-group">
<label>
<?php echo __('Verified by Trustindex', 'trustindex-plugin'); ?>
<span class="ti-badge ti-badge-info"><?php echo esc_html(__('Recommended', 'trustindex-plugin')); ?></span>
</label>
<form method="post" action="">
<input type="hidden" name="command" value="save-verified-by-trustindex" />
<?php wp_nonce_field('ti-save-verified-by-trustindex'); ?>
<?php $verifiedByTrustindex = (int)$pluginManagerInstance->getWidgetOption('verified-by-trustindex'); ?>
<select class="ti-form-control" name="verified-by-trustindex">
<option value="0"<?php if (!$verifiedByTrustindex): ?> selected<?php endif; ?>><?php echo esc_html(__('Hide', 'trustindex-plugin')); ?></option>
<option value="1"<?php if ($verifiedByTrustindex === 1): ?> selected<?php endif; ?>><?php echo esc_html(sprintf(__('Style %d', 'trustindex-plugin'), 1)); ?></option>
<option value="2"<?php if ($verifiedByTrustindex === 2): ?> selected<?php endif; ?>><?php echo esc_html(sprintf(__('Style %d', 'trustindex-plugin'), 2)); ?></option>
</select>
</form>
</div>
<?php endif; ?>
<?php if ($pluginManagerInstance->isLayoutHasReviews()): ?>
<div class="ti-form-group">
<label><?php echo __('Filter your ratings', 'trustindex-plugin'); ?></label>

<form method="post" action="">
<input type="hidden" name="command" value="save-filter-stars" />
<?php wp_nonce_field('ti-save-filter-stars'); ?>
<select class="ti-form-control" name="stars">
<option value="1,2,3,4,5"<?php if (count($filter['stars']) > 2): ?> selected<?php endif; ?>><?php echo esc_html(__('Show all', 'trustindex-plugin')); ?></option>
<option value="4,5"<?php if (count($filter['stars']) === 2): ?> selected<?php endif; ?>>&starf;&starf;&starf;&starf; - &starf;&starf;&starf;&starf;&starf;</option>
<option value="5"<?php if (count($filter['stars']) === 1): ?> selected<?php endif; ?>><?php echo __('only', 'trustindex-plugin'); ?> &starf;&starf;&starf;&starf;&starf;</option>
</select>
</form>
</div>
<?php endif; ?>
<div class="ti-form-group">
<label><?php echo __('Select language', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-language" />
<?php wp_nonce_field('ti-save-language'); ?>
<select class="ti-form-control" name="lang">
<?php foreach ($pluginManager::$widget_languages as $id => $name): ?>
<option value="<?php echo esc_attr($id); ?>" <?php echo $pluginManagerInstance->getWidgetOption('lang') == $id ? 'selected' : ''; ?>><?php echo esc_html($name); ?></option>
<?php endforeach; ?>
</select>
</form>
</div>
<?php if ($pluginManagerInstance->isLayoutHasReviews()): ?>
<div class="ti-form-group">
<label><?php echo __('Select date format', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-dateformat" />
<?php wp_nonce_field('ti-save-dateformat'); ?>
<select class="ti-form-control" name="dateformat">
<?php foreach ($pluginManager::$widget_dateformats as $format): ?>
<option value="<?php echo esc_attr($format); ?>" <?php echo $pluginManagerInstance->getWidgetOption('dateformat') == $format ? 'selected' : ''; ?>><?php
switch ($format) {
case 'modern':
$lang = substr(get_locale(), 0, 2);
if (!in_array($lang, array_keys($pluginManager::$widget_date_format_locales))) {
$lang = 'en';
}
$tmp = explode('|', $pluginManager::$widget_date_format_locales[$lang]);
echo str_replace([ '%d', '%s' ], [ 2, $tmp[3] ], $tmp[0]);
break;
case 'hide':
echo __('Hide', 'trustindex-plugin');
break;
default:
echo date($format);
break;
}
?></option>
<?php endforeach; ?>
</select>
</form>
</div>
<div class="ti-form-group">
<label><?php echo __('Select name format', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-nameformat" />
<?php wp_nonce_field('ti-save-nameformat'); ?>
<select class="ti-form-control" name="nameformat">
<?php foreach ($pluginManager::$widget_nameformats as $format): ?>
<option value="<?php echo esc_attr($format['id']); ?>" <?php echo $pluginManagerInstance->getWidgetOption('nameformat') == $format['id'] ? 'selected' : ''; ?>>
<?php
if (1 === (int)$format['id']) {
echo __('Do not format', 'trustindex-plugin');
} else {
echo esc_html($pluginManagerInstance->renderNameFormat('Firstname Lastname', $format['id']));
}
?>
</option>
<?php endforeach; ?>
</select>
</form>
</div>
<?php if (!in_array($styleId, [17, 21, 52, 53, 112, 114])): ?>
<div class="ti-form-group">
<label><?php echo __('Align', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-align" />
<?php wp_nonce_field('ti-save-align'); ?>
<select class="ti-form-control" name="align">
<?php foreach ([ 'left', 'center', 'right', 'justify' ] as $type): ?>
<option value="<?php echo esc_attr($type); ?>" <?php echo $pluginManagerInstance->getWidgetOption('align') == $type ? 'selected' : ''; ?>><?php echo __($type, 'trustindex-plugin'); ?></option>
<?php endforeach; ?>
</select>
</form>
</div>
<div class="ti-form-group">
<label><?php echo __('Review text', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-review-text-mode" />
<?php wp_nonce_field('ti-save-review-text-mode'); ?>
<select class="ti-form-control" name="review_text_mode">
<?php foreach ([
'scroll' => 'Scroll',
'readmore' => 'Read more',
'truncated' => 'Truncated'
] as $type => $translated): ?>
<option value="<?php echo esc_attr($type); ?>" <?php echo $pluginManagerInstance->getWidgetOption('review-text-mode') == $type ? 'selected' : ''; ?>><?php echo __($translated, 'trustindex-plugin'); ?></option>
<?php endforeach; ?>
</select>
</form>
</div>
<?php endif; ?>
<?php endif; ?>
<?php if ($isTopRatedBadge): ?>
<div class="ti-form-group">
<label><?php echo __('Select type', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-top-rated-type" />
<?php wp_nonce_field('ti-save-top-rated-type'); ?>
<select class="ti-form-control" name="type">
<?php foreach ($pluginManager::$widget_top_rated_titles as $type => $langs): ?>
<option value="<?php echo esc_attr($type); ?>" <?php echo $pluginManagerInstance->getWidgetOption('top-rated-type') == $type ? 'selected' : ''; ?>><?php echo esc_html(__($type, 'trustindex-plugin')); ?></option>
<?php endforeach; ?>
</select>
</form>
</div>
<?php if (!$pluginManagerInstance->isFomoWidget()): ?>
<div class="ti-form-group">
<label><?php echo __('Select date format', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-top-rated-date" />
<?php wp_nonce_field('ti-save-top-rated-date'); ?>
<?php $topRatedDate = $pluginManagerInstance->getWidgetOption('top-rated-date'); ?>
<select class="ti-form-control" name="date">
<option value="hide"<?php if ($topRatedDate === 'hide'): ?> selected<?php endif; ?>><?php echo esc_html(__("Hide", 'trustindex-plugin')); ?></option>
<option value="last-year"<?php if ($topRatedDate === 'last-year'): ?> selected<?php endif; ?>><?php echo esc_html(__("Last year", 'trustindex-plugin')); ?></option>
<option value=""<?php if (!$topRatedDate): ?> selected<?php endif; ?>><?php echo esc_html(__("Current year", 'trustindex-plugin')); ?></option>
</select>
</form>
</div>
<?php endif; ?>
<?php endif; ?>
<?php if ($pluginManagerInstance->isFomoWidget()): ?>
<div class="ti-form-row">
<div class="ti-form-group">
<label><?php echo __('Icon', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-fomo-icon" />
<?php wp_nonce_field('ti-save-fomo-icon'); ?>
<select class="ti-form-control" name="fomo-icon">
<?php foreach ($pluginManager::$widget_templates['templates'][$styleId]['params']['fomo-icon-choices'] as $icon): ?>
<?php echo $iconName = ucfirst(str_replace('-', ' ', $icon)); ?>
<option value="<?php echo esc_attr($icon); ?>" <?php echo $pluginManagerInstance->getWidgetOption('fomo-icon') == $icon ? 'selected' : ''; ?>><?php echo __($iconName, 'trustindex-plugin'); ?></option>
<?php endforeach; ?>
</select>
</form>
</div>
<div class="ti-form-group ti-right-block">
<label><?php echo __('Color', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-fomo-color" />
<?php wp_nonce_field('ti-save-fomo-color'); ?>
<input type="text" class="ti-form-control ti-color-picker ti-save-input-on-change-color" value="<?php echo esc_attr($pluginManagerInstance->getWidgetOption('fomo-color')); ?>" name="fomo-color" readonly />
</form>
</div>
</div>
<div class="ti-form-row">
<div class="ti-form-group ti-left-block">
<label><?php echo __('Align', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-align" />
<?php wp_nonce_field('ti-save-align'); ?>
<select class="ti-form-control" name="align">
<?php foreach ([ 'left', 'right' ] as $type): ?>
<option value="<?php echo esc_attr($type); ?>" <?php echo $pluginManagerInstance->getWidgetOption('align') == $type ? 'selected' : ''; ?>><?php echo __($type, 'trustindex-plugin'); ?></option>
<?php endforeach; ?>
</select>
</form>
</div>
<div class="ti-form-group ti-right-block">
<label><?php echo __('Margin', 'trustindex-plugin'); ?></label>
<form method="post" action="">
<input type="hidden" name="command" value="save-fomo-margin" />
<?php wp_nonce_field('ti-save-fomo-margin'); ?>
<input type="number" class="ti-form-control ti-save-input-on-change" min=0 step=1 value="<?php echo esc_attr($pluginManagerInstance->getWidgetOption('fomo-margin')); ?>" name="fomo-margin" />
</form>
</div>
</div>
<?php endif; ?>
</div>
<div class="ti-right-block">
<form method="post" id="ti-widget-options">
<input type="hidden" name="command" value="save-options" />
<?php wp_nonce_field('ti-save-options'); ?>
<?php if ($pluginManagerInstance->isLayoutHasReviews()): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="only-ratings" value="1"<?php if ($filter['only-ratings']): ?> checked<?php endif; ?> />
<label><?php echo __('Hide reviews without comments', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if (in_array($styleId, [ 4, 6, 7, 15, 16, 19, 31, 33, 36, 37, 38, 39, 44 ])): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="no-rating-text" value="1"<?php if ($pluginManagerInstance->getWidgetOption('no-rating-text')): ?> checked<?php endif; ?> />
<label><?php echo __('Hide rating text', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if ($pluginManagerInstance->isLayoutHasReviews() && (!in_array($widgetType, ['floating']) || $styleId === 53)): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="footer-filter-text" value="1"<?php if ($pluginManagerInstance->getWidgetOption('footer-filter-text')): ?> checked<?php endif; ?> />
<label><?php echo __('Show minimum review filter condition', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if ($pluginManagerInstance->isLayoutHasReviews()): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="show-review-replies" value="1"<?php if ($pluginManagerInstance->getWidgetOption('show-review-replies')): ?> checked<?php endif; ?> />
<label><?php echo __('Show review reply', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if (in_array($styleId, [ 8, 10, 13 ])): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="show-header-button" value="1"<?php if ($pluginManagerInstance->getWidgetOption('show-header-button')): ?> checked<?php endif; ?> />
<label><?php echo __('Show write review button', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if (in_array($styleId, [ 8, 16, 18, 31, 33 ])): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="reviews-load-more" value="1"<?php if ($pluginManagerInstance->getWidgetOption('reviews-load-more')): ?> checked<?php endif; ?> />
<label><?php echo __('Show "Load more" button', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if ($pluginManagerInstance->isLayoutHasReviews() && !in_array($styleId, [53,54])): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="verified-icon" value="1"<?php if ($pluginManagerInstance->getWidgetOption('verified-icon')): ?> checked<?php endif; ?> />
<label><?php echo __('Show verified review icon', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if (in_array($widgetType, [ 'slider', 'sidebar' ]) && !in_array($styleId, [ 8, 9, 10, 18, 19, 37, 54 ])): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="show-arrows" value="1"<?php if ($pluginManagerInstance->getWidgetOption('show-arrows')): ?> checked<?php endif; ?> />
<label><?php echo __('Show navigation arrows', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if ($pluginManagerInstance->isLayoutHasReviews() && $styleId != 52): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="show-reviewers-photo" value="1"<?php if ($pluginManagerInstance->getWidgetOption('show-reviewers-photo')): ?> checked<?php endif; ?> />
<label><?php echo __("Show reviewer's profile picture", 'trustindex-plugin'); ?></label>
</span>
<span class="ti-checkbox ti-checkbox-row ti-disabled">
<input type="checkbox" value="1" disabled />
<label class="ti-tooltip">
<?php echo __("Show reviewer's profile picture locally, from a single image (less requests)", 'trustindex-plugin'); ?>
<span class="ti-tooltip-message"><?php echo __('Paid package feature', 'trustindex-plugin'); ?></span>
</label>
</span>
<span class="ti-checkbox ti-checkbox-row ti-disabled">
<input type="checkbox" value="1" disabled />
<label class="ti-tooltip">
<?php echo __('Show photos in reviews', 'trustindex-plugin'); ?>
<span class="ti-tooltip-message"><?php echo __('Paid package feature', 'trustindex-plugin'); ?></span>
</label>
</span>
<?php endif; ?>
<?php if (!in_array($widgetType, [ 'floating', 'fomo' ]) && !$isTopRatedBadge && $scssSet !== 'drop-shadow' && $styleId != 54): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="enable-animation" value="1"<?php if ($pluginManagerInstance->getWidgetOption('enable-animation')): ?> checked<?php endif; ?> />
<label><?php echo __('Enable mouseover animation', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if (!$pluginManagerInstance->isFomoWidget()): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="disable-font" value="1"<?php if ($pluginManagerInstance->getWidgetOption('disable-font')): ?> checked<?php endif; ?> />
<label><?php echo __("Use site's font", 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php if ($pluginManagerInstance->isLayoutHasReviews()): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="show-logos" value="1"<?php if ($pluginManagerInstance->getWidgetOption('show-logos')): ?> checked<?php endif;?> />
<label><?php echo __('Show platform logos', 'trustindex-plugin'); ?></label>
</span>
<?php if (!$pluginManagerInstance->is_ten_scale_rating_platform() && $pluginManagerInstance->getShortName() !== 'google'): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="show-stars" value="1"<?php if ($pluginManagerInstance->getWidgetOption('show-stars')): ?> checked<?php endif;?> />
<label><?php echo __('Show platform stars', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php endif; ?>
<?php if ($pluginManagerInstance->isFomoWidget()): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="fomo-open" value="1"<?php if ($pluginManagerInstance->getWidgetOption('fomo-open')): ?> checked<?php endif; ?> />
<label><?php echo __('Default open', 'trustindex-plugin'); ?></label>
</span>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="fomo-link" value="1"<?php if ($pluginManagerInstance->getWidgetOption('fomo-link')): ?> checked<?php endif; ?> />
<label><?php echo __('Enable link', 'trustindex-plugin'); ?></label>
</span>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="fomo-border" value="1"<?php if ($pluginManagerInstance->getWidgetOption('fomo-border')): ?> checked<?php endif; ?> />
<label><?php echo __('Show border', 'trustindex-plugin'); ?></label>
</span>
<?php if ($pluginManagerInstance->getWidgetOption('fomo-link')): ?>
<span class="ti-checkbox ti-checkbox-row">
<input type="checkbox" name="fomo-arrow" value="1"<?php if ($pluginManagerInstance->getWidgetOption('fomo-arrow')): ?> checked<?php endif; ?> />
<label><?php echo __('Show arrow', 'trustindex-plugin'); ?></label>
</span>
<?php endif; ?>
<?php endif; ?>
</form>
</div>
<div class="clear"></div>
<?php if (!$isTopRatedBadge || $isTopRatedBadgeValid): ?>
<div class="ti-box-footer">
<a href="<?php echo wp_nonce_url('?page='. esc_attr($_GET['page']) .'&tab=free-widget-configurator&setup_widget', 'ti-setup-widget'); ?>" class="ti-btn ti-btn-loading-on-click ti-pull-right"><?php echo __('Save and get code', 'trustindex-plugin'); ?></a>
<div class="clear"></div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
<?php else: ?>
<h1 class="ti-header-title"><?php echo __('Insert code', 'trustindex-plugin'); ?></h1>
<?php if (!count($reviews) && !$isReviewDownloadInProgress): ?>
<div class="ti-notice ti-notice-warning" style="margin: 0 0 15px 0">
<p>
<?php echo sprintf(__('There are no reviews on your %s platform.', 'trustindex-plugin'), 'Google'); ?>
</p>
</div>
<?php endif; ?>
<div class="ti-box">
<div class="ti-box-header"><?php echo __('Insert this shortcode into your website', 'trustindex-plugin'); ?></div>
<?php include(plugin_dir_path(__FILE__) . '../include/shortcode-paste-box.php'); ?>
</div>
<?php if (!get_option($pluginManagerInstance->get_option_name('rate-us-feedback'), 0)): ?>
<?php include(plugin_dir_path(__FILE__) . '../include/rate-us-feedback-box.php'); ?>
<?php endif; ?>
<?php
$tiCampaign1 = 'wp-google-1';
$tiCampaign2 = 'wp-google-2';
include(plugin_dir_path(__FILE__) . '../include/get-more-customers-box.php');
?>
<?php endif; ?>
</div>
