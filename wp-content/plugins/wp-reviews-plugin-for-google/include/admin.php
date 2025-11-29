<?php
defined('ABSPATH') or die('No script kiddies please!');
if (!current_user_can('edit_pages')) {
die('The account you are logged in to does not have permission to access this page.');
}
if (isset($_GET['test_proxy'])) {
check_admin_referer('ti-test-proxy');
delete_option($pluginManagerInstance->get_option_name('proxy-check'));
$params = [];
if (isset($_GET['page'])) {
$params['page'] = sanitize_text_field(wp_unslash($_GET['page']));
}
if (isset($_GET['tab'])) {
$params['tab'] = sanitize_text_field(wp_unslash($_GET['tab']));
}
header('Location: admin.php?' . build_query($params));
exit;
}
if (isset($_GET['notification'])) {
if (isset($_GET['action'])) {
$type = sanitize_text_field(wp_unslash($_GET['notification']));
$action = sanitize_text_field(wp_unslash($_GET['action']));

$options = $pluginManagerInstance->getNotificationOptions($type);
switch ($action) {
case 'later':
$remindDays = isset($_GET['remind-days']) ? (int)$_GET['remind-days'] : 14;
$pluginManagerInstance->setNotificationParam($type, 'timestamp', time() + ($remindDays * 86400));
break;
case 'close':
if ($options['hide-on-close']) {
$pluginManagerInstance->setNotificationParam($type, 'active', false);
}
break;
case 'open':
if ($options['hide-on-open']) {
$pluginManagerInstance->setNotificationParam($type, 'active', false);
}
if ($options['redirect']) {
header('Location: '. $options['redirect']);
exit;
}
break;
case 'hide':
$pluginManagerInstance->setNotificationParam($type, 'hidden', true);
header('Location: admin.php?page=' . sanitize_text_field(wp_unslash($_GET['page'])) .'&tab=advanced');
break;
case 'unhide':
$pluginManagerInstance->setNotificationParam($type, 'hidden', false);
header('Location: admin.php?page=' . sanitize_text_field(wp_unslash($_GET['page'])) .'&tab=advanced');
break;
}
}
exit;
}
if (isset($_REQUEST['command']) && $_REQUEST['command'] === 'rate-us-feedback') {
check_admin_referer('ti-rate-us');
$text = isset($_POST['text']) ? trim(wp_kses_post(sanitize_text_field(wp_unslash($_POST['text'])))) : "";
$email = isset($_POST['email']) ? trim(sanitize_text_field(wp_unslash($_POST['email']))) : "";
$star = isset($_REQUEST['star']) ? (int)$_REQUEST['star'] : 1;
update_option($pluginManagerInstance->get_option_name('rate-us-feedback'), $star, false);
if ($star > 3) {
header('Location: https://wordpress.org/support/plugin/'. $pluginManagerInstance->get_plugin_slug() . '/reviews/?rate='. $star .'#new-post');
}
else {
wp_mail('support@trustindex.io', 'Feedback from '. $pluginNameForEmails .' plugin', "We received a <strong>$star star</strong> feedback about the $pluginNameForEmails plugin from $email:<br /><br />$text", [
'From: '. $email,
'Content-Type: text/html; charset=UTF-8'
]);
}
exit;
}
$httpBlocked = false;
if (defined('WP_HTTP_BLOCK_EXTERNAL') && WP_HTTP_BLOCK_EXTERNAL) {
if (!defined('WP_ACCESSIBLE_HOSTS') || strpos(WP_ACCESSIBLE_HOSTS, '*.trustindex.io') === FALSE) {
$httpBlocked = true;
}
}
$proxy = new WP_HTTP_Proxy();
$proxyCheck = true;
if ($proxy->is_enabled()) {
$optName = $pluginManagerInstance->get_option_name('proxy-check');
$dbData = get_option($optName, "");
if (!$dbData) {
$response = wp_remote_post("https://admin.trustindex.io/" . 'api/userCheckLoggedIn', [
'timeout' => '30',
'redirection' => '5',
'blocking' => true
]);
if (is_wp_error($response)) {
$proxyCheck = $response->get_error_message();
update_option($optName, $response->get_error_message(), false);
}
else {
update_option($optName, 1, false);
}
}
else {
if ($dbData !== '1') {
$proxyCheck = $dbData;
}
}
}
$tabs = $pluginManagerInstance->getPluginTabs();
$selectedTab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : null;
if (!$selectedTab || !in_array($selectedTab, array_column($tabs, 'slug'))) {
$selectedTab = $tabs[0]['slug'];
}
?>
<?php if (isset($assetCheckJs) && isset($assetCheckCssFile)): ?>
<div id="ti-assets-error" class="notice notice-warning" style="display: none; margin-left: 0; margin-right: 0; padding-bottom: 9px">
<p>
<?php echo wp_kses_post(__('For some reason, the <strong>CSS</strong> file required to run the plugin was not loaded.<br />One of your plugins is probably causing the problem.', 'trustindex-plugin')); ?>
</p>
</div>
<?php
$jsKey = 'trustindex-check-frontend-assets';
$jsFiles = [];
foreach ($assetCheckJs as $id => $file) {
$jsFiles []= [
'id' => $id,
'url' => $pluginManagerInstance->get_plugin_file_url($file),
];
}
$jsContent = "
window.onload = function() {
let notLoaded = [];
let loadedCount = 0;
let jsFiles = ". wp_json_encode($jsFiles) .";
let addElement = function(type, url, callback) {
let element = document.createElement(type);
if (type === 'script') {
element.type = 'text/javascript';
element.src = url;
}
else {
element.type = 'text/css';
element.rel = 'stylesheet';
element.href = url;
element.id = '". esc_html($assetCheckCssId) ."-css';
}
document.head.appendChild(element);
element.addEventListener('load', function() { callback(true); });
element.addEventListener('error', function() { callback(false); });
};
let isCSSExists = function() {
let link = document.getElementById('". esc_html($assetCheckCssId) ."-css');
return link && Boolean(link.sheet);
};
let isJSExists = function(id) {
return typeof TrustindexJsLoaded !== 'undefined' && typeof TrustindexJsLoaded[ id ] !== 'undefined';
};
let process = function() {
if (loadedCount < jsFiles.length + 1) {
return false;
}
if (notLoaded.length) {
document.getElementById('trustindex-plugin-settings-page').remove();
let warningBox = document.getElementById('ti-assets-error');
if (warningBox) {
warningBox.style.display = 'block';
warningBox.querySelector('p strong').innerHTML = notLoaded.join(', ');
}
}
}
if (!isCSSExists()) {
addElement('link', '". esc_attr($pluginManagerInstance->get_plugin_file_url($assetCheckCssFile)) ."', function(success) {
loadedCount++;
if (!success) {
notLoaded.push('CSS');
}
process();
});
}
else {
loadedCount++;
}
jsFiles.forEach(function(js) {
if (!isJSExists(js.id)) {
addElement('script', js.url, function(success) {
loadedCount++;
if (!success) {
if (notLoaded.indexOf('JS') === -1) {
notLoaded.push('JS');
}
}
process();
});
}
else {
loadedCount++;
}
});
};
";
wp_register_script($jsKey, false, [], true, [ 'in_footer' => true ]);
wp_enqueue_script($jsKey);
wp_add_inline_script($jsKey, $jsContent);
?>
<?php endif; ?>
<div id="trustindex-plugin-settings-page" class="ti-plugin-wrapper ti-toggle-opacity">
<div class="ti-header-nav">
<?php foreach ($tabs as $tab): ?>
<a
class="ti-nav-item<?php if ($selectedTab === $tab['slug']): ?> ti-active<?php endif; ?><?php if ($tab['place'] === 'right'): ?> ti-right<?php endif; ?>"
href="<?php echo esc_url(admin_url('admin.php?page='. esc_attr(sanitize_text_field(wp_unslash($_GET['page']))) .'&tab='. esc_attr($tab['slug']))); ?>"
>
<?php echo esc_html($tab['name']); ?>
<?php if (isset($newBadgeTabs) && in_array($tab['slug'], $newBadgeTabs)): ?>
<span class="ti-new-badge"><?php echo esc_html(__('new', 'trustindex-plugin')); ?></span>
<?php endif; ?>
</a>
<?php endforeach; ?>
<a href="https://www.trustindex.io/?a=sys&c=<?php echo esc_attr($logoCampaignId); ?>" target="_blank" title="Trustindex" class="ti-logo">
<img src="<?php echo esc_url($pluginManagerInstance->get_plugin_file_url($logoFile)); ?>" />
</a>
</div>
<?php if ($httpBlocked): ?>
<div class="ti-box ti-notice-error ti-mb-1">
<p>
<?php echo esc_html(__('Your site cannot download our widget templates, because of your server settings not allowing that:', 'trustindex-plugin')); ?><br /><a href="https://wordpress.org/support/article/editing-wp-config-php/#block-external-url-requests" target="_blank">https://wordpress.org/support/article/editing-wp-config-php/#block-external-url-requests</a><br /><br />
<strong><?php echo esc_html(__('Solution', 'trustindex-plugin')); ?></strong><br />
<?php echo wp_kses_post(__('a) You should define <strong>WP_HTTP_BLOCK_EXTERNAL</strong> as false', 'trustindex-plugin')); ?><br />
<?php echo wp_kses_post(__("b) or you should add Trustindex as an <strong>WP_ACCESSIBLE_HOSTS</strong>: \"*.trustindex.io\"", 'trustindex-plugin')); ?><br />
</p>
</div>
<?php endif; ?>
<?php if ($proxyCheck !== TRUE): ?>
<div class="ti-box ti-notice-error ti-mb-1">
<p>
<?php echo esc_html(__('It seems you are using a proxy for HTTP requests but after a test request it returned a following error:', 'trustindex-plugin')); ?><br />
<strong><?php echo wp_kses_post($proxyCheck); ?></strong><br /><br />
<?php echo esc_html(__('Therefore, our plugin might not work properly. Please, contact your hosting support, they can resolve this easily.', 'trustindex-plugin')); ?>
</p>
<a href="<?php echo esc_url(wp_nonce_url('?page='. esc_attr(sanitize_text_field(wp_unslash($_GET['page']))) .'&tab='. esc_attr(sanitize_text_field(wp_unslash($_GET['tab']))) .'&test_proxy', 'ti-test-proxy')); ?>" class="ti-btn ti-btn-loading-on-click"><?php echo esc_html(__('Test again', 'trustindex-plugin')); ?></a>
</div>
<?php endif; ?>
<?php if (!isset($noContainerElementTabs) || !in_array($selectedTab, $noContainerElementTabs)): ?>
<div class="ti-container" id="tab-<?php echo esc_attr($selectedTab); ?>">
<?php include(plugin_dir_path(__FILE__) . '../tabs' . DIRECTORY_SEPARATOR . $selectedTab . '.php'); ?>
</div>
<?php else: ?>
<?php include(plugin_dir_path(__FILE__) . '../tabs' . DIRECTORY_SEPARATOR . $selectedTab . '.php'); ?>
<?php endif; ?>
</div>
<div id="ti-loading">
<div class="ti-loading-effect">
<div></div>
<div></div>
<div></div>
</div>
</div>
