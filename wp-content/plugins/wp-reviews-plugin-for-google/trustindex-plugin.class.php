<?php
class TrustindexPlugin_google
{
private $plugin_file_path;
private $plugin_name;
private $platform_name;
private $shortname;
private $version;
public static $permissionNeeded = 'edit_pages';
public function __construct($shortname, $pluginFilePath, $version, $pluginName, $platformName)
{
$this->shortname = $shortname;
$this->plugin_file_path = $pluginFilePath;
$this->version = $version;
$this->plugin_name = $pluginName;
$this->platform_name = $platformName;
}
public function getPluginTabs()
{
$tabs = [];
$tabs[] = [
'place' => 'left',
'slug' => 'free-widget-configurator',
'name' => __('Free Widget Configurator', 'trustindex-plugin')
];
if ($this->is_noreg_linked()) {
$tabs[] = [
'place' => 'left',
'slug' => 'my-reviews',
'name' => __('My reviews', 'trustindex-plugin')
];
}
$tabs[] = [
'place' => 'left',
'slug' => 'get-reviews',
'name' => __('Get Reviews', 'trustindex-plugin')
];
$tabs[] = [
'place' => 'left',
'slug' => 'rate-us',
'name' => __('Rate Us', 'trustindex-plugin')
];
if (!$this->is_trustindex_connected()) {
$tabs[] = [
'place' => 'left',
'slug' => 'get-more-features',
'name' => __('Get more Features', 'trustindex-plugin')
];
}
$tabs[] = [
'place' => 'right',
'slug' => 'instagram-feed-widget',
'name' => 'Instagram Feed Widget',
];
$tabs[] = [
'place' => 'right',
'slug' => 'advanced',
'name' => __('Advanced', 'trustindex-plugin')
];
return $tabs;
}
public function getShortName()
{
return $this->shortname;
}
public function getWebhookAction()
{
return 'trustindex_reviews_hook_' . $this->getShortName();
}
public function getWebhookUrl()
{
return admin_url('admin-ajax.php') . '?action='. $this->getWebhookAction();
}

public function getProFeatureButton($campaignId)
{

return '<a class="ti-btn" href="https://www.trustindex.io/?a=sys&c='. $campaignId .'" target="_blank">'. __('Create a Free Account for More Features', 'trustindex-plugin') .'</a>';
}
public function is_review_download_in_progress()
{
return get_option($this->get_option_name('review-download-inprogress'), 0);
}
public function is_review_manual_download()
{
return get_option($this->get_option_name('review-manual-download'), 0);
}
public function delete_async_request()
{
$requestId = get_option($this->get_option_name('review-download-request-id'));
if (!$requestId) {
return false;
}
wp_remote_post('https://admin.trustindex.io/source/wordpressPageRequest', [
'body' => [
'is_delete' => 1,
'id' => $requestId
],
'timeout' => 300,
'redirection' => '5',
'blocking' => true
]);
return true;
}
public function save_details($tmp)
{
$name = isset($tmp['name']) ? sanitize_text_field(stripslashes($tmp['name'])) : "";
$name = json_encode($name);
$details = [
'id' => isset($tmp['page_id']) ? $tmp['page_id'] : $tmp['id'],
'name' => $name,
'address' => isset($tmp['address']) ? sanitize_text_field(stripslashes($tmp['address'])) : "",
'avatar_url' => isset($tmp['avatar_url']) ? sanitize_text_field(stripslashes($tmp['avatar_url'])) : "",
'rating_number' => isset($tmp['reviews']['count']) ? (int)$tmp['reviews']['count'] : 0,
'rating_score' => isset($tmp['reviews']['score']) ? (float)$tmp['reviews']['score'] : 0,
];
if (isset($tmp['access_token'])) {
$details['access_token'] = sanitize_text_field(stripslashes($tmp['access_token']));
}
update_option($this->get_option_name('page-details'), $details, false);
}
public function save_reviews($tmp)
{
global $wpdb;
$tableName = $this->get_tablename('reviews');
$oldReviews = $wpdb->get_results('SELECT reviewId, hidden, highlight FROM `'. $tableName .'` ORDER BY date DESC', ARRAY_A);
$oldReviews = array_combine(array_column($oldReviews, 'reviewId'), $oldReviews);
$wpdb->query('TRUNCATE `'. $tableName .'`');
if ($wpdb->last_error) {
throw new Exception('DB truncate failed: '. $wpdb->last_error);
}
foreach ($tmp as $i => $review) {
foreach ($review as $key => $value) {
if (is_array($value)) {
if ($key === 'reviewer') {
$review[ $key ] = array_map(function($v) {
return $v ? sanitize_text_field(stripslashes($v)) : $v;
}, $value);
}
else {
unset($review[ $key ]);
}
}
else if ($key === 'text') {
$review[ $key ] = $value ? wp_kses_post(stripslashes($value)) : $value;
}
else {
$review[ $key ] = $value ? sanitize_text_field(stripslashes($value)) : $value;
}
}

$hidden = 0;
$highlight = null;
if (isset($review['id']) && isset($oldReviews[$review['id']])) {
$hidden = $oldReviews[$review['id']]['hidden'];
$highlight = $oldReviews[$review['id']]['highlight'];
}
$wpdb->insert($tableName, [
'user' => $review['reviewer']['name'],
'user_photo' => $review['reviewer']['avatar_url'],
'text' => $review['text'],
'rating' => $review['rating'] ? $review['rating'] : 5,
'date' => substr($review['created_at'], 0, 10),
'reviewId' => isset($review['id']) ? $review['id'] : null,
'reply' => isset($review['reply']) ? $review['reply'] : "",
'hidden' => $hidden,
'highlight' => $highlight ? $highlight : null,
]);
if ($wpdb->last_error) {
throw new Exception('DB instert failed: '. $wpdb->last_error);
}
}
update_option($this->get_option_name('review-download-modal'), 0, false);
}


public function get_plugin_dir()
{
return plugin_dir_path($this->plugin_file_path);
}
public function get_plugin_file_url($file, $addVersioning = true)
{
$url = plugins_url($file, $this->plugin_file_path);
if ($addVersioning) {
$appendMark = strpos($url, '?') === FALSE ? '?' : '&';
$url .= $appendMark . 'ver=' . $this->getVersion();
}
return $url;
}
public function get_plugin_slug()
{
return basename($this->get_plugin_dir());
}


public function uninstall()
{
$this->delete_async_request();
include $this->get_plugin_dir() . 'include' . DIRECTORY_SEPARATOR . 'uninstall.php';
if (is_file($this->getCssFile())) {
unlink($this->getCssFile());
}
}


public function activate()
{
include $this->get_plugin_dir() . 'include' . DIRECTORY_SEPARATOR . 'activate.php';
$this->setNotificationParam('not-using-no-connection', 'timestamp', time() + 86400);
if (!$this->getNotificationParam('rate-us', 'hidden', false) && $this->getNotificationParam('rate-us', 'active', true)) {
$this->setNotificationParam('rate-us', 'active', true);
$this->setNotificationParam('rate-us', 'timestamp', time() + 86400);
}
update_option($this->get_option_name('activation-redirect'), 1, false);
}
public function deactivate()
{
update_option($this->get_option_name('active'), '0');
}
public function load()
{
global $wpdb;
$this->loadI18N();
include $this->get_plugin_dir() . 'include' . DIRECTORY_SEPARATOR . 'update.php';
if (get_option($this->get_option_name('activation-redirect'))) {
delete_option($this->get_option_name('activation-redirect'));
wp_redirect(admin_url('admin.php?page=' . $this->get_plugin_slug() . '/settings.php'));
exit;
}
if (
$this->is_noreg_linked() &&
!$this->is_review_download_in_progress() &&
get_option($this->get_option_name('download-timestamp'), time()) < time() &&
!$this->getNotificationParam('review-download-available', 'hidden') &&
$this->getNotificationParam('review-download-available', 'do-check', true)
) {
$this->setNotificationParam('review-download-available', 'active', true);
$this->setNotificationParam('review-download-available', 'do-check', false);

}
if (
!$this->is_noreg_linked() &&
!$this->getNotificationParam('not-using-no-connection', 'active', false) &&
$this->getNotificationParam('not-using-no-connection', 'do-check', true)
) {
$this->setNotificationParam('not-using-no-connection', 'active', true);
$this->setNotificationParam('not-using-no-connection', 'do-check', false);
}
if ( !class_exists('TrustindexGutenbergPlugin') && function_exists( 'register_block_type' ) && !WP_Block_Type_Registry::get_instance()->is_registered( 'trustindex/block-selector' )) {
require_once $this->get_plugin_dir() . 'static' . DIRECTORY_SEPARATOR . 'block-editor' . DIRECTORY_SEPARATOR . 'block-editor.php';
TrustindexGutenbergPlugin::instance();
}
}
public function loadI18N()
{
load_plugin_textdomain('trustindex-plugin', false, $this->get_plugin_slug() . DIRECTORY_SEPARATOR . 'languages');
}
public function is_enabled()
{
return get_option($this->get_option_name('active'), 0);
}

public function add_setting_menu()
{
global $menu, $submenu;
$settingsPageUrl = $this->get_plugin_slug() . "/settings.php";
$settingsPageTitle = $this->platform_name . ' ';
if (function_exists('mb_strtolower')) {
$settingsPageTitle .= mb_strtolower(__('Reviews', 'trustindex-plugin'));
}
else {
$settingsPageTitle .= strtolower(__('Reviews', 'trustindex-plugin'));
}
$topMenu = false;
foreach ($menu as $key => $item) {
if ($item[0] === 'Trustindex.io') {
$topMenu = $item;
break;
}
}
if ($topMenu === false) {
add_menu_page(
$settingsPageTitle,
'Trustindex.io',
self::$permissionNeeded,
$settingsPageUrl,
'',
$this->get_plugin_file_url('static/img/trustindex-sign-logo.png')
);
}
else {
if (!isset($submenu[ $topMenu[2] ])) {
add_submenu_page(
$topMenu[2],
'Trustindex.io',
$topMenu[3],
self::$permissionNeeded,
$topMenu[2]
);
}
add_submenu_page(
$topMenu[2],
'Trustindex.io',
$settingsPageTitle,
self::$permissionNeeded,
$settingsPageUrl
);
}
}
public function add_plugin_action_links($links, $file)
{
if (basename($file) === basename($this->plugin_file_path)) {
$platformLink = '<a style="background-color: #1a976a; color: white; font-weight: bold; padding: 3px 8px; border-radius: 4px; position: relative; top: 1px" ';
if (get_option($this->get_option_name('widget-setted-up'), 0)) {
$platformLink .= 'href="' . admin_url('admin.php?page=' . $this->get_plugin_slug() . '/settings.php&tab=my-reviews') . '">'. __('Review Management', 'trustindex-plugin');
}
else {
$platformLink .= 'href="' . admin_url('admin.php?page=' . $this->get_plugin_slug() . '/settings.php') . '">';
if (!$this->is_noreg_linked()) {
$platformLink .= sprintf(__('Connect %s', 'trustindex-plugin'), $this->platform_name);
}
else {
$platformLink .= __('Create Widget', 'trustindex-plugin');
}
}
$platformLink .= '</a>';
$settingsLink = '<a href="' . admin_url('admin.php?page=' . $this->get_plugin_slug() . '/settings.php') . '">' . __('Settings', 'trustindex-plugin') . '</a>';
array_unshift($links, $platformLink, $settingsLink);
}
return $links;
}
public function add_plugin_meta_links($meta, $file)
{
if (basename($file) === basename($this->plugin_file_path)) {
$meta[] = '<a href="'. admin_url('admin.php?page=' . $this->get_plugin_slug() . '/settings.php&tab=get-more-features') .'">'. __('Get more Features', 'trustindex-plugin') . ' →</a>';
$meta[] = '<a href="http://wordpress.org/support/view/plugin-reviews/'. $this->get_plugin_slug() .'" target="_blank" rel="noopener noreferrer">'. __('Rate our plugin', 'trustindex-plugin') . ' <span style="color: #F6BB07; font-size: 1.2em; line-height: 1; position: relative; top: 0.05em;">★★★★★</span></a>';
}
return $meta;
}


public function init_widget()
{
if (!class_exists('TrustindexWidget_'.$this->getShortName())) {
require $this->get_plugin_dir() . 'trustindex-'. $this->getShortName() .'-widget.class.php';
}
}
public function register_widget()
{
return register_widget('TrustindexWidget_'.$this->getShortName());
}


public function get_option_name($opt_name)
{
if (!in_array($opt_name, $this->get_option_names())) {
echo 'Option not registered in plugin (Trustindex class)';
}
if (in_array($opt_name, [ 'subscription-id', 'proxy-check' ])) {
return 'trustindex-'. $opt_name;
}
else {
return 'trustindex-'. $this->getShortName() .'-'. $opt_name;
}
}
public function get_option_names()
{
return [
'active',
'page-details',
'subscription-id',
'proxy-check',
'style-id',
'review-content',
'filter',
'scss-set',
'css-content',
'lang',
'no-rating-text',
'dateformat',
'nameformat',
'rate-us-feedback',
'verified-icon',
'enable-animation',
'show-arrows',
'show-reviewers-photo',
'download-timestamp',
'widget-setted-up',
'disable-font',
'show-logos',
'show-stars',
'load-css-inline',
'align',
'review-text-mode',
'amp-hidden-notification',
'review-download-token',
'review-download-inprogress',
'review-download-request-id',
'review-download-modal',
'review-download-is-connecting',
'review-download-is-failed',
'review-manual-download',
'reply-generated',
'instagram-promo-opened',
'footer-filter-text',
'show-header-button',
'reviews-load-more',
'activation-redirect',
'notifications',
'top-rated-type',
'top-rated-date',
'show-review-replies',
'verified-by-trustindex',
'fomo-open',
'fomo-link',
'fomo-border',
'fomo-arrow',
'fomo-icon',
'fomo-color',
'fomo-margin',
'cdn-version-control',
'version-control',
'preview',
];
}
private $widgetOptions = [];
private $widgetOptionDefaultOverride = [];
public function getWidgetOption($name, $forceDatabaseValue = false, $returnDefault = false)
{
if (isset($this->widgetOptions[$name]) && !$forceDatabaseValue && !$returnDefault) {
return $this->widgetOptions[$name];
}
if ($returnDefault && isset($this->widgetOptionDefaultOverride[$name])) {
return $this->widgetOptionDefaultOverride[$name];
}
global $wpdb;
if (!in_array($name, ['style-id', 'scss-set'])) {
$styleId = $this->getWidgetOption('style-id');
$scssSet = $this->getWidgetOption('scss-set');
}
$default = null;
if (!$forceDatabaseValue) {
switch ($name) {
case 'style-id':
$default = 4;
break;
case 'scss-set':
$default = 'light-background';
break;
case 'lang':
$default = 'en';
break;
case 'dateformat':
$default = 'Y-m-d';
break;
case 'nameformat':
$default = 1;
break;
case 'filter':
global $wpdb;
$onlyRatingsDefault = false;
if ($this->is_noreg_linked()) {
$onlyRatingsDefault = (float)$wpdb->get_var('SELECT COUNT(`id`) FROM `'. $this->get_tablename('reviews') .'` WHERE `text` != ""') >= 3;
}
$default = [
'stars' => [1, 2, 3, 4, 5],
'only-ratings' => $onlyRatingsDefault
];
break;
case 'no-rating-text':
$default = !in_array($styleId, [6, 8, 37]) ? 1 : 0;
break;
case 'verified-icon':
case 'enable-animation':
case 'show-arrows':
case 'show-header-button':
case 'reviews-load-more':
case 'fomo-open':
case 'fomo-border':
case 'fomo-arrow':
$default = 1;
break;
case 'widget-setted-up':
case 'disable-font':
case 'footer-filter-text':
case 'floating-mobile-open':
case 'show-review-replies':
case 'fomo-link':
case 'fomo-margin':
$default = 0;
break;
case 'align':
$default = in_array($styleId, [ 36, 37, 38, 39 ]) ? 'center' : ($this->isRtlLanguage() ? 'right' : 'left');
break;
case 'review-text-mode':
$default = 'readmore';
break;
case 'show-logos':
$default = isset(self::$widget_styles[$scssSet]) && self::$widget_styles[$scssSet]['hide-logos'] ? 0 : 1;
break;
case 'show-stars':
$default = isset(self::$widget_styles[$scssSet]) && self::$widget_styles[$scssSet]['hide-stars'] ? 0 : 1;
break;
case 'show-reviewers-photo':
$default = isset(self::$widget_styles[$scssSet]) && self::$widget_styles[$scssSet]['reviewer-photo'] ? 1 : 0;
break;
case 'top-rated-type':
$default = 'Service';
break;
case 'top-rated-date':
$default = in_array($styleId, [98, 100, 102, 104]) ? 'hide' : '';
break;
case 'verified-by-trustindex':
$default = 0;
break;
case 'fomo-icon':
$default = self::$widget_templates['templates'][$styleId]['params']['fomo-icon'];
break;
case 'fomo-color':
$params = self::$widget_templates['templates'][$styleId]['params'];
$default = isset($params['fomo-color']) ? $params['fomo-color'] : '#FF552B';
break;
}
}
if ($returnDefault) {
return $default;
}
$this->widgetOptions[$name] = get_option($this->get_option_name($name), $default);
return $this->widgetOptions[$name];
}


public function getNotificationOptions($type = "")
{
$platformName = $this->get_platform_name($this->getShortName());
$defaultRedirect = '?page='. $this->get_plugin_slug() .'/settings.php&tab=free-widget-configurator';
$list = [
'rate-us' => [
'type' => 'warning',
'extra-class' => 'trustindex-popup',
'button-text' => "",
'is-closeable' => true,
'hide-on-close' => false,
'hide-on-open' => true,
'redirect' => 'https://wordpress.org/support/plugin/'. $this->get_plugin_slug() .'/reviews/?rate=5#new-post',
'text' =>
/* translators: %s: Name of the plugin */
sprintf(__('We have worked a lot on the free "%s" plugin.', 'trustindex-plugin'), $this->plugin_name) . '<br />' .
__('If you love our features, please write a review to help us make the plugin even better.', 'trustindex-plugin') . '<br />' .
/* translators: %s: Trustindex CEO */
sprintf(__('Thank you. Gabor, %s', 'trustindex-plugin'), 'Trustindex CEO'),
],
'not-using-no-connection' => [
'type' => 'warning',
'extra-class' => "",
/* translators: %s: Platform name */
'button-text' => sprintf(__('Create a free %s widget! »', 'trustindex-plugin'), $platformName),
'is-closeable' => true,
'hide-on-close' => true,
'hide-on-open' => true,
'remind-later-button' => false,
'redirect' => $defaultRedirect,
/* translators: %s: Platform name */
'text' => sprintf(__('Display %s reviews on your website.', 'trustindex-plugin'), $platformName),
],
'not-using-no-widget' => [
'type' => 'warning',
'extra-class' => "",
/* translators: %s: Platform name */
'button-text' => sprintf(__('Embed the %s reviews widget! »', 'trustindex-plugin'), $platformName),
'is-closeable' => true,
'hide-on-close' => true,
'hide-on-open' => true,
'remind-later-button' => true,
'redirect' => $defaultRedirect,
/* translators: %s: Platform name */
'text' => sprintf(__('Build trust and display your %s reviews on your website.', 'trustindex-plugin'), $platformName),
],
'review-download-available' => [
'type' => 'warning',
'extra-class' => "",
'button-text' => __('Download your latest reviews! »', 'trustindex-plugin'),
'is-closeable' => true,
'hide-on-close' => true,
'hide-on-open' => true,
'remind-later-button' => false,
'redirect' => $defaultRedirect,
/* translators: %s: Platform name */
'text' => sprintf(__('You can update your %s reviews.', 'trustindex-plugin'), $platformName),
],
'review-download-finished' => [
'type' => 'warning',
'extra-class' => "",
/* translators: %s: Service name (ChatGPT) */
'button-text' => sprintf(__('Reply with %s! »', 'trustindex-plugin'), 'ChatGPT'),
'is-closeable' => true,
'hide-on-close' => true,
'hide-on-open' => true,
'remind-later-button' => false,
'redirect' => $defaultRedirect,
/* translators: %s: Platform name */
'text' => sprintf(__('Your new %s reviews have been downloaded.', 'trustindex-plugin'), $platformName),
],
];
return $type ? $list[$type] : $list;
}
public function setNotificationParam($type, $param, $value)
{
$notifications = get_option($this->get_option_name('notifications'), []);
if (!isset($notifications[ $type ])) {
$notifications[ $type ] = [];
}
$notifications[ $type ][ $param ] = $value;
update_option($this->get_option_name('notifications'), $notifications, false);
}
public function getNotificationParam($type, $param, $default = null)
{
$notifications = get_option($this->get_option_name('notifications'), []);
if (!isset($notifications[ $type ]) || !isset($notifications[ $type ][ $param ])) {
return $default;
}
return $notifications[ $type ][ $param ];
}
public function isNotificationActive($type)
{
$notifications = get_option($this->get_option_name('notifications'), []);
if (
!isset($notifications[ $type ]) ||
!isset($notifications[ $type ]['active']) || !$notifications[ $type ]['active'] ||
(isset($notifications[ $type ]['hidden']) && $notifications[ $type ]['hidden']) ||
(isset($notifications[ $type ]['timestamp']) && $notifications[ $type ]['timestamp'] > time())
) {
return false;
}
return true;
}
public function getNotificationEmailContent($type)
{
$platformName = $this->get_platform_name($this->getShortName());
$subject = "";
$message = "";
switch ($type) {

case 'review-download-finished':
$subject = $platformName . ' Reviews Downloaded';
$message = '
<p>Great news.</p>
<p><strong>Your new '. $platformName .' reviews have been downloaded.</p>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate !important;border-radius: 3px;background-color: #2AA8D7;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
<tbody>
<tr>
<td align="center" valign="middle" style="font-family: Arial;font-size: 16px;padding: 12px 20px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;">
<a title="Reply with ChatGPT! »" href="'. admin_url('admin.php') .'?page='. urlencode($this->get_plugin_slug() .'/settings.php') .'&tab=free-widget-configurator" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;display: block;">Reply with ChatGPT! »</a>
</td>
</tr>
</tbody>
</table>
';
break;
}
return [
'subject' => $subject,
'message' => $message
];
}
public function sendNotificationEmail($type)
{
if ($email = $this->getNotificationParam($type, 'email', get_option('admin_email'))) {
$msg = $this->getNotificationEmailContent($type);
if ($msg['subject'] && $msg['message']) {
try {
wp_mail($email, $msg['subject'], $msg['message'], [ 'Content-Type: text/html; charset=UTF-8' ], [ '' ]);
}
catch(Exception $e) { }
}
}
}
public function get_platforms()
{
return array (
 0 => 'facebook',
 1 => 'google',
 2 => 'tripadvisor',
 3 => 'yelp',
 4 => 'booking',
 5 => 'amazon',
 6 => 'arukereso',
 7 => 'airbnb',
 8 => 'hotels',
 9 => 'opentable',
 10 => 'foursquare',
 11 => 'capterra',
 12 => 'szallashu',
 13 => 'thumbtack',
 14 => 'expedia',
 15 => 'zillow',
 16 => 'wordpressPlugin',
 17 => 'aliexpress',
 18 => 'alibaba',
 19 => 'sourceForge',
 20 => 'ebay',
);
}
private $plugin_slugs = array (
 'facebook' => 'free-facebook-reviews-and-recommendations-widgets',
 'google' => 'wp-reviews-plugin-for-google',
 'tripadvisor' => 'review-widgets-for-tripadvisor',
 'yelp' => 'reviews-widgets-for-yelp',
 'booking' => 'review-widgets-for-booking-com',
 'amazon' => 'review-widgets-for-amazon',
 'arukereso' => 'review-widgets-for-arukereso',
 'airbnb' => 'review-widgets-for-airbnb',
 'hotels' => 'review-widgets-for-hotels-com',
 'opentable' => 'review-widgets-for-opentable',
 'foursquare' => 'review-widgets-for-foursquare',
 'capterra' => 'review-widgets-for-capterra',
 'szallashu' => 'review-widgets-for-szallas-hu',
 'thumbtack' => 'widgets-for-thumbtack-reviews',
 'expedia' => 'widgets-for-expedia-reviews',
 'zillow' => 'widgets-for-zillow-reviews',
 'wordpressPlugin' => 'reviews-widgets',
 'aliexpress' => 'widgets-for-aliexpress-reviews',
 'alibaba' => 'widgets-for-alibaba-reviews',
 'sourceForge' => 'widgets-for-sourceforge-reviews',
 'ebay' => 'widgets-for-ebay-reviews',
);
public function get_plugin_slugs()
{
return array_values($this->plugin_slugs);
}


public static function get_noticebox($type, $message)
{
return '<div class="ti-notice ti-notice-'. $type .' is-dismissible"><p>'. $message .'</p><button type="button" class="notice-dismiss"></button></div>';
}
public static function get_alertbox($type, $content, $newline_content = true)
{
$types = [
'warning' => [
'css' => 'color: #856404; background-color: #fff3cd; border-color: #ffeeba;',
'icon' => 'dashicons-warning'
],
'info' => [
'css' => 'color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb;',
'icon' => 'dashicons-info'
],
'error' => [
'css' => 'color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;',
'icon' => 'dashicons-info'
]
];
return '<div style="margin:20px 0px; padding:10px; '. $types[ $type ]['css'] .' border-radius: 5px">'
. '<span class="dashicons '. $types[ $type ]['icon'] .'"></span> <strong>'. strtoupper($type) .'</strong>'
. ($newline_content ? '<br />' : "")
. $content
. '</div>';
}


public function get_shortcode_name()
{
return 'trustindex';
}
public function init_shortcode()
{
$tag = $this->get_shortcode_name();
if (shortcode_exists($tag)) {
$initedVersion = get_option('trustindex-core-shortcode-inited', '1.0');
if (!$initedVersion || version_compare($initedVersion, $this->getVersion())) {
remove_shortcode($tag);
}
else {
return false;
}
}
update_option('trustindex-core-shortcode-inited', $this->getVersion(), false);
add_shortcode($tag, [ $this, 'shortcode_func' ]);
}
public function shortcode_func($atts)
{
$atts = shortcode_atts([ 'data-widget-id' => null, 'no-registration' => null ], $atts);
if (isset($atts['data-widget-id']) && $atts['data-widget-id']) {
$content = $this->renderWidgetFrontend($atts['data-widget-id']);
if ($this->isElementorEditing()) {
$content .= '<script type="text/javascript" src="https://cdn.trustindex.io/loader.js"></script>';
}
return $content;
}
else if (isset($atts['no-registration']) && $atts['no-registration']) {
$forcePlatform = $atts['no-registration'];
if (substr($forcePlatform, 0, 5) !== 'trust' && substr($forcePlatform, -4) !== 'ilot' && !in_array($forcePlatform, $this->get_platforms())) {
$avPlatforms = $this->get_platforms();
$forcePlatform = $avPlatforms[0];
}
$filePath = __FILE__;
if (isset($this->plugin_slugs[ $forcePlatform ])) {
$filePath = preg_replace('/[^\/\\\\]+([\\\\\/]trustindex-plugin\.class\.php)/', $this->plugin_slugs[ $forcePlatform ] . '$1', $filePath);
}
$className = 'TrustindexPlugin_' . $forcePlatform;
if (!class_exists($className)) {
return $this->frontEndErrorForAdmins(ucfirst($forcePlatform) . ' plugin is not active or not found!');
}
$chosedPlatform = new $className($forcePlatform, $filePath, "do-not-care-12.9", "do-not-care-Widgets for Google Reviews", "do-not-care-Google");
$chosedPlatform->setNotificationParam('not-using-no-widget', 'active', false);
if (!$chosedPlatform->is_noreg_linked()) {
return $this->frontEndErrorForAdmins(sprintf(__('You have to connect your business (%s)!', 'trustindex-plugin'), $forcePlatform));
} else if (!$chosedPlatform->getWidgetOption('widget-setted-up')) {
return $this->frontEndErrorForAdmins('You have to complete your widget setup!');
} else {
if ($this->isElementorEditing()) {
return $chosedPlatform->renderWidgetAdmin(true);
} else {
return $chosedPlatform->renderWidgetFrontend();
}
}
}
else {
return $this->frontEndErrorForAdmins(__('Your shortcode is deficient: Trustindex Widget ID is empty! Example: ', 'trustindex-plugin') . '<br /><code>['.$this->get_shortcode_name().' data-widget-id="478dcc2136263f2b3a3726ff"]</code>');
}
}
public function frontEndErrorForAdmins($text)
{
if (!current_user_can('manage_options')) {
return " ";
}
return self::get_alertbox('error', ' @ <strong>'. __('Trustindex plugin', 'trustindex-plugin') .'</strong> <i style="opacity: 0.65">('. __('This message is not be visible to visitors in public mode.', 'trustindex-plugin') .')</i><br /><br />'. $text, false);
}


public function is_noreg_linked()
{
$pageDetails = $this->getPageDetails();
return $pageDetails && !empty($pageDetails);
}
private $pageDetails = null;
public function getPageDetails()
{
if ($this->pageDetails) {
return $this->pageDetails;
}
$pageDetails = get_option($this->get_option_name('page-details'));
if (isset($pageDetails['name']) && $this->isJson($pageDetails['name'])) {
$pageDetails['name'] = json_decode($pageDetails['name']);
}
$this->pageDetails = $pageDetails;
return $pageDetails;
}
public function noreg_save_css($setChange = false)
{
$defaultSet = 'light-background';
$styleId = (int)get_option($this->get_option_name('style-id'), 4);
$setId = get_option($this->get_option_name('scss-set'), $defaultSet);
$response = wp_remote_get('https://cdn.trustindex.io/assets/widget-presetted-css/v2/'.$styleId.'-'.$setId.'.css', [ 'timeout' => 30 ]);
$cssContent = wp_remote_retrieve_body($response);
$cssContent = str_replace('../../../assets', 'https://cdn.trustindex.io/assets', $cssContent);
$cssContent = str_replace(".ti-widget[data-layout-id='$styleId'][data-set-id='$setId']", '.ti-widget.ti-'. substr($this->getShortName(), 0, 4), $cssContent);
if (is_wp_error($response) || !$cssContent) {
echo $this->get_alertbox('error', "Trustindex's system is not available at the moment, please try again later.");
die;
}
if (!$setChange) {
update_option($this->get_option_name('scss-set'), $defaultSet, false);
}
if (in_array($styleId, [17, 21, 52, 53, 112, 114])) {
$cssContent .= '.ti-preview-box { position: unset !important }';
}
update_option($this->get_option_name('css-content'), $cssContent, false);
return $this->handleCssFile();
}
public function getCssFile($returnOnlyFile = false)
{
$file = 'trustindex-'. $this->getShortName() .'-widget.css';
if ($returnOnlyFile) {
return $file;
}
$uploadDir = wp_upload_dir();
return trailingslashit($uploadDir['basedir']) . $file;
}
private function getCssUrl()
{
$path = wp_upload_dir()['baseurl'] .'/'. $this->getCssFile(true);
if (is_ssl()) {
$path = str_replace('http://', 'https://', $path);
}
return $path;
}
public function handleCssFile()
{
$css = get_option($this->get_option_name('css-content'));
if (!$css) {
return;
}
if (get_option($this->get_option_name('load-css-inline'), 0)) {
return;
}
$fileExists = is_file($this->getCssFile());
$success = false;
$errorType = null;
$errorMessage = "";
if ($fileExists && !is_readable($this->getCssFile())) {
$errorType = 'permission';
}
else {
if ($fileExists && $css === file_get_contents($this->getCssFile())) {
return;
}
require_once(ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php');
global $wp_filesystem;
set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, $err_context = []) {
throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
}, E_WARNING);
add_filter('filesystem_method', array($this, 'filter_filesystem_method'));
WP_Filesystem();
try {
$success = $wp_filesystem->put_contents($this->getCssFile(), $css, 0644);
}
catch (Exception $e) {
if (strpos($e->getMessage(), 'Permission denied') !== FALSE) {
$errorType = 'permission';
}
else {
$errorType = 'filesystem';
$errorMessage = $e->__toString();
}
}
restore_error_handler();
remove_filter('filesystem_method', array($this, 'filter_filesystem_method'));
}
if (!$success) {
add_action('admin_notices', function() use ($fileExists, $errorType, $errorMessage) {
$html = '
<div class="notice notice-error" style="margin: 5px 0 15px">
<p>' .
'<strong>'. __('ERROR with the following plugin:', 'trustindex-plugin') .'</strong> '. $this->plugin_name .'<br /><br />' .
__('CSS file could not saved.', 'trustindex-plugin') .' <strong>('. $this->getCssFile() .')</strong> '. __('Your widgets do not display properly!', 'trustindex-plugin') . '<br />';
if ($errorType === 'filesystem') {
$html .= '<br />
<strong>There is an error with your filesystem. We got the following error message:</strong>
<pre style="display: block; margin: 10px 0; padding: 20px; background: #eee">'. $errorMessage .'</pre>
<strong>Maybe you configured your filesystem incorrectly.<br />
<a href="https://wordpress.org/support/article/editing-wp-config-php/#wordpress-upgrade-constants" target="_blank">Here you can read about how to configure filesystem in your WordPress.</a></strong>';
}
else {
if ($fileExists) {
$html .= __('CSS file exists and it is not writeable. Delete the file', 'trustindex-plugin');
}
else {
$html .= __('Grant write permissions to upload folder', 'trustindex-plugin');
}
$html .= '<br />' .
__('or', 'trustindex-plugin') . '<br />' .
sprintf(__("enable 'CSS internal loading' in the %s page!", 'trustindex-plugin'), '<a href="'. admin_url('admin.php?page=' . $this->get_plugin_slug() . '/settings.php&tab=advanced') .'>'. __('Advanced', 'trustindex-plugin') .'</a>');
}
echo $html . '</p></div>';
});
}
return $success;
}
public static $topRatedMinimumScore = 4.5;
public static $widget_templates = array (
 'categories' => 
 array (
 'slider' => '4,5,13,14,15,19,34,36,37,39,44,45,46,47,95,105,108',
 'sidebar' => '6,7,8,9,10,18,54,81',
 'list' => '33,80',
 'grid' => '16,31,38,48,79',
 'badge' => '11,12,20,22,23,55,56,57,58,97,98,99,100,101,102,103,104,107',
 'button' => '24,25,26,27,28,29,30,32,35,59,60,61,62,106,109,110,111,113,115',
 'floating' => '17,21,52,53,112,114',
 'popup' => '23,30,32,112,114',
 'top-rated-badge' => '97,98,99,100,101,102,103,104',
 'fomo' => '116,117,118,119,120,121',
 ),
 'templates' => 
 array (
 4 => 
 array (
 'name' => 'Slider I.',
 'type' => 'slider',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 48 => 
 array (
 'name' => 'Grid I. - Big picture',
 'type' => 'grid',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 47 => 
 array (
 'name' => 'Slider III. - Big picture',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 46 => 
 array (
 'name' => 'Slider II. - Big picture',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 45 => 
 array (
 'name' => 'Slider I. - Big picture',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 14 => 
 array (
 'name' => 'Slider I. - with header',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 108 => 
 array (
 'name' => 'Slider I. - with Top Rated header and photos',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => true,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => false,
 'default-hide-date' => true,
 ),
 ),
 105 => 
 array (
 'name' => 'Slider I. - with Top Rated header and photos',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => false,
 'default-hide-date' => true,
 ),
 ),
 95 => 
 array (
 'name' => 'Slider I. - with AI summary',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 39 => 
 array (
 'name' => 'Slider II. - centered',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 5 => 
 array (
 'name' => 'Slider II.',
 'type' => 'slider',
 'is-active' => true,
 'is-popular' => true,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 36 => 
 array (
 'name' => 'Slider III.',
 'type' => 'slider',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 19 => 
 array (
 'name' => 'Slider IV.',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 34 => 
 array (
 'name' => 'Slider IV.',
 'type' => 'slider',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 13 => 
 array (
 'name' => 'Slider V.',
 'type' => 'slider',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 15 => 
 array (
 'name' => 'Slider VI.',
 'type' => 'slider',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 44 => 
 array (
 'name' => 'Slider VI.',
 'type' => 'slider',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 37 => 
 array (
 'name' => 'Slider VII.',
 'type' => 'slider',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 33 => 
 array (
 'name' => 'List I.',
 'type' => 'list',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 80 => 
 array (
 'name' => 'List I. - with header',
 'type' => 'list',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 16 => 
 array (
 'name' => 'Grid - with photos',
 'type' => 'grid',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 38 => 
 array (
 'name' => 'Grid II.',
 'type' => 'grid',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 79 => 
 array (
 'name' => 'Mansonry grid - with header',
 'type' => 'grid',
 'is-active' => false,
 'is-popular' => true,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 31 => 
 array (
 'name' => 'Mansonry grid',
 'type' => 'grid',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 54 => 
 array (
 'name' => 'Sidebar slider I.',
 'type' => 'sidebar',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 7 => 
 array (
 'name' => 'Sidebar slider II.',
 'type' => 'sidebar',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 6 => 
 array (
 'name' => 'Sidebar slider II.',
 'type' => 'sidebar',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 81 => 
 array (
 'name' => 'Full sidebar I. - with header',
 'type' => 'sidebar',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 18 => 
 array (
 'name' => 'Full sidebar I.',
 'type' => 'sidebar',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 8 => 
 array (
 'name' => 'Full sidebar II.',
 'type' => 'sidebar',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 9 => 
 array (
 'name' => 'Full sidebar II.',
 'type' => 'sidebar',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 10 => 
 array (
 'name' => 'Full sidebar III.',
 'type' => 'sidebar',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 97 => 
 array (
 'name' => 'Top Rated badge I.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => true,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => false,
 'default-hide-date' => false,
 ),
 ),
 98 => 
 array (
 'name' => 'Top Rated badge II.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => false,
 'default-hide-date' => true,
 ),
 ),
 99 => 
 array (
 'name' => 'Top Rated badge III.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => false,
 'default-hide-date' => false,
 ),
 ),
 100 => 
 array (
 'name' => 'Top Rated badge IV.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => false,
 'default-hide-date' => true,
 ),
 ),
 101 => 
 array (
 'name' => 'Top Rated badge V.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => true,
 'default-hide-date' => false,
 ),
 ),
 102 => 
 array (
 'name' => 'Top Rated badge VI.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => true,
 'default-hide-date' => true,
 ),
 ),
 103 => 
 array (
 'name' => 'Top Rated badge VII.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => true,
 'default-hide-date' => false,
 ),
 ),
 104 => 
 array (
 'name' => 'Top Rated badge VIII.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => true,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'top-rated-badge-border' => true,
 'default-hide-date' => true,
 ),
 ),
 55 => 
 array (
 'name' => 'HTML badge I.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 11 => 
 array (
 'name' => 'HTML badge II.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => true,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 12 => 
 array (
 'name' => 'HTML badge III.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 56 => 
 array (
 'name' => 'HTML badge IV.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 107 => 
 array (
 'name' => 'HTML badge V.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 22 => 
 array (
 'name' => 'Company badge I.',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 57 => 
 array (
 'name' => 'HTML badge V.',
 'type' => 'badge',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 23 => 
 array (
 'name' => 'Company badge I. - with popup',
 'type' => 'badge',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 58 => 
 array (
 'name' => 'HTML badge VI.',
 'type' => 'badge',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 20 => 
 array (
 'name' => 'HTML badge III.',
 'type' => 'badge',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 27 => 
 array (
 'name' => 'Button I.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => true,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 24 => 
 array (
 'name' => 'Button I.',
 'type' => 'button',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 26 => 
 array (
 'name' => 'Button II.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 29 => 
 array (
 'name' => 'Button III.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 30 => 
 array (
 'name' => 'Button IV. - with dropdown',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 60 => 
 array (
 'name' => 'Button V.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 28 => 
 array (
 'name' => 'Button V.',
 'type' => 'button',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 25 => 
 array (
 'name' => 'Button VI.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 35 => 
 array (
 'name' => 'Button VII.',
 'type' => 'button',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 32 => 
 array (
 'name' => 'Button VII. - with popup',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 59 => 
 array (
 'name' => 'Button VIII.',
 'type' => 'button',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 106 => 
 array (
 'name' => 'Button VIII.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => true,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 61 => 
 array (
 'name' => 'Button X.',
 'type' => 'button',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 109 => 
 array (
 'name' => 'Button IX.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 62 => 
 array (
 'name' => 'Button XI.',
 'type' => 'button',
 'is-active' => false,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 110 => 
 array (
 'name' => 'Button X.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 111 => 
 array (
 'name' => 'Button XI.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 113 => 
 array (
 'name' => 'Button XII.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 115 => 
 array (
 'name' => 'Button XIII.',
 'type' => 'button',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 17 => 
 array (
 'name' => 'Floating I.',
 'type' => 'floating',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 53 => 
 array (
 'name' => 'Floating II.',
 'type' => 'floating',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 21 => 
 array (
 'name' => 'Floating III.',
 'type' => 'floating',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 52 => 
 array (
 'name' => 'Floating IV.',
 'type' => 'floating',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 112 => 
 array (
 'name' => 'Floating V. - with popup',
 'type' => 'floating',
 'is-active' => true,
 'is-popular' => true,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 114 => 
 array (
 'name' => 'Floating VI. - with popup',
 'type' => 'floating',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 ),
 ),
 116 => 
 array (
 'name' => 'Fomo Review Widget I.',
 'type' => 'fomo',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => true,
 'params' => 
 array (
 'is-top-rated-badge' => true,
 'fomo-icon' => 'platform',
 'fomo-icon-choices' => 
 array (
 0 => 'profile-images',
 1 => 'platform',
 2 => 'fire',
 3 => 'trophy',
 ),
 'fomo-platform-block' => true,
 'top-rated-badge-border' => false,
 'default-hide-date' => false,
 ),
 ),
 117 => 
 array (
 'name' => 'Fomo Review Widget II.',
 'type' => 'fomo',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 'fomo-icon' => 'platform',
 'fomo-icon-choices' => 
 array (
 0 => 'profile-images',
 1 => 'platform',
 2 => 'fire',
 3 => 'trophy',
 ),
 'fomo-title-text' => 'RATING_TEXT rating',
 'fomo-platform-block' => true,
 ),
 ),
 120 => 
 array (
 'name' => 'Fomo Review Widget III.',
 'type' => 'fomo',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 'fomo-icon' => 'fire',
 'fomo-icon-choices' => 
 array (
 0 => 'profile-images',
 1 => 'platform',
 2 => 'fire',
 3 => 'trophy',
 ),
 'fomo-title-text' => '5STAR_RATING_NUMBER <u>5-star</u> PLATFORM_NAME reviews',
 'fomo-platform-block' => true,
 ),
 ),
 119 => 
 array (
 'name' => 'Fomo Review Widget IV.',
 'type' => 'fomo',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 'fomo-icon' => 'fire',
 'fomo-icon-choices' => 
 array (
 0 => 'profile-images',
 1 => 'platform',
 2 => 'fire',
 3 => 'trophy',
 ),
 'fomo-title-text' => '5STAR_RATING_NUMBER_LAST <u>5-star</u> PLATFORM_NAME reviews',
 'fomo-subtitle-text' => 'in the last 30 days',
 'fomo-platform-block' => true,
 ),
 ),
 118 => 
 array (
 'name' => 'Fomo Review Widget V.',
 'type' => 'fomo',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 'fomo-icon' => 'fire',
 'fomo-icon-choices' => 
 array (
 0 => 'profile-images',
 1 => 'platform',
 2 => 'fire',
 3 => 'trophy',
 ),
 'fomo-title-text' => 'RATING_TEXT rating',
 'fomo-subtitle-text' => 'based on RATING_NUMBER PLATFORM_NAME reviews',
 'fomo-break-after-subtitle' => true,
 'fomo-platform-block' => true,
 ),
 ),
 121 => 
 array (
 'name' => 'Fomo Review Widget VI.',
 'type' => 'fomo',
 'is-active' => true,
 'is-popular' => false,
 'is-top-rated-badge' => false,
 'params' => 
 array (
 'fomo-icon' => 'profile-images',
 'fomo-icon-choices' => 
 array (
 0 => 'profile-images',
 1 => 'platform',
 2 => 'fire',
 3 => 'trophy',
 ),
 'fomo-title-text' => 'RATING_TEXT rating',
 'fomo-platform-block' => true,
 'fomo-color' => '#F6BB06',
 ),
 ),
 ),
);
public static $widget_styles = array (
 'light-background' => 
 array (
 'is-active' => true,
 'name' => 'Light background',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-background-large' => 
 array (
 'is-active' => false,
 'name' => 'Light background - large',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'ligth-border' => 
 array (
 'is-active' => true,
 'name' => 'Light border',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'ligth-border-3d-large' => 
 array (
 'is-active' => false,
 'name' => 'Light border - 3D - large',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'ligth-border-large' => 
 array (
 'is-active' => false,
 'name' => 'Light border - large',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'ligth-border-large-red' => 
 array (
 'is-active' => false,
 'name' => 'Light border - large - red',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'drop-shadow' => 
 array (
 'is-active' => true,
 'name' => 'Drop shadow',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'drop-shadow-large' => 
 array (
 'is-active' => false,
 'name' => 'Drop shadow - large',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-minimal' => 
 array (
 'is-active' => true,
 'name' => 'Minimal',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-minimal-large' => 
 array (
 'is-active' => false,
 'name' => 'Minimal - large',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'soft' => 
 array (
 'is-active' => true,
 'name' => 'Soft',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-clean' => 
 array (
 'is-active' => false,
 'name' => 'Light clean',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-square' => 
 array (
 'is-active' => false,
 'name' => 'Light square',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-background-border' => 
 array (
 'is-active' => false,
 'name' => 'Light background border',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'blue' => 
 array (
 'is-active' => false,
 'name' => 'Blue',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-background-large-purple' => 
 array (
 'is-active' => false,
 'name' => 'Light background - large - purple',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-background-image' => 
 array (
 'is-active' => false,
 'name' => 'Light background image',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-contrast' => 
 array (
 'is-active' => true,
 'name' => 'Light contrast',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-contrast-large' => 
 array (
 'is-active' => false,
 'name' => 'Light contrast - large',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'light-contrast-large-blue' => 
 array (
 'is-active' => false,
 'name' => 'Light contrast - large - blue',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'dark-background' => 
 array (
 'is-active' => true,
 'name' => 'Dark background',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'dark-minimal' => 
 array (
 'is-active' => true,
 'name' => 'Minimal dark',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'dark-border' => 
 array (
 'is-active' => false,
 'name' => 'Dark border',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'dark-contrast' => 
 array (
 'is-active' => false,
 'name' => 'Dark contrast',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
 'dark-background-image' => 
 array (
 'is-active' => false,
 'name' => 'Dark background image',
 'reviewer-photo' => true,
 'hide-logos' => false,
 'hide-stars' => false,
 ),
);
public static $widget_languages = [
'ar' => "العربية",
'zh' => "汉语",
'cs' => "Čeština",
'da' => "Dansk",
'nl' => "Nederlands",
'en' => "English",
'et' => "Eestlane",
'fi' => "Suomi",
'fr' => "Français",
'de' => "Deutsch",
'el' => "Ελληνικά",
'hi' => "हिन्दी",
'hu' => "Magyar",
'it' => "Italiano",
'no' => "Norsk",
'pl' => "Polski",
'pt' => "Português",
'ro' => "Română",
'ru' => "Русский",
'sk' => "Slovenčina",
'es' => "Español",
'sv' => "Svenska",
'tr' => "Türkçe",
'gd' => 'Gàidhlig na h-Alba',
'hr' => 'Hrvatski',
'id' => 'Bahasa Indonesia',
'is' => 'Íslensku',
'he' => 'עִברִית',
'ja' => '日本',
'ko' => '한국어',
'lt' => 'Lietuvių',
'ms' => 'Bahasa Melayu',
'sl' => 'Slovenščina',
'sr' => 'Српски',
'th' => 'ไทย',
'uk' => 'Українська',
'vi' => 'Tiếng Việt',
'mk' => 'Македонски',
'bg' => 'български',
'sq' => 'Shqip',
'af' => 'Afrikaans',
'az' => 'Azərbaycan dili',
'bn' => 'বাংলা',
'bs' => 'Bosanski',
'cy' => 'Cymraeg',
'fa' => 'فارسی',
'gl' => 'Galego',
'hy' => 'հայերեն',
'ka' => 'ქართული',
'kk' => 'қазақ'
];
public static $widget_dateformats = [ 'modern', 'j F Y', 'j. F, Y', 'F j, Y', 'Y.m.d.', 'Y-m-d', 'd/m/Y', 'hide' ];
public static $widget_nameformats = array (
 1 => 
 array (
 'id' => '1',
 'sample' => 'Do not format',
 'regex' => NULL,
 'ucfirst' => NULL,
 'toupper' => NULL,
 ),
 2 => 
 array (
 'id' => '2',
 'sample' => 'F Lastname',
 'regex' => '/^(.)[^\\s]*\\s([^\\s]+).*$/',
 'ucfirst' => true,
 'toupper' => false,
 ),
 3 => 
 array (
 'id' => '3',
 'sample' => 'Lastname',
 'regex' => '/^[^\\s]*\\s?([^\\s]+).*$/',
 'ucfirst' => true,
 'toupper' => false,
 ),
 4 => 
 array (
 'id' => '4',
 'sample' => 'Firstname L',
 'regex' => '/^([^\\s]+)\\s(.).*$/',
 'ucfirst' => true,
 'toupper' => false,
 ),
 5 => 
 array (
 'id' => '5',
 'sample' => 'Firstname',
 'regex' => '/^([^\\s]+).*$/',
 'ucfirst' => true,
 'toupper' => false,
 ),
 6 => 
 array (
 'id' => '6',
 'sample' => 'FIRSTNAME LASTNAME',
 'regex' => '/^(.*)$/',
 'ucfirst' => false,
 'toupper' => true,
 ),
 8 => 
 array (
 'id' => '8',
 'sample' => 'Firstname Lastname',
 'regex' => '/^(.*)$/',
 'ucfirst' => true,
 'toupper' => false,
 ),
 9 => 
 array (
 'id' => '9',
 'sample' => 'FL',
 'regex' => '/^(.)[^\\s]*\\s*(.)?.*$/',
 'ucfirst' => false,
 'toupper' => true,
 ),
);
private static $widget_rating_texts = array (
 'en' => 
 array (
 0 => 'poor',
 1 => 'below average',
 2 => 'average',
 3 => 'good',
 4 => 'excellent',
 ),
 'af' => 
 array (
 0 => 'arm',
 1 => 'onder gemiddeld',
 2 => 'gemiddeld',
 3 => 'goed',
 4 => 'uitstekend',
 ),
 'ar' => 
 array (
 0 => 'ضعيف',
 1 => 'مقبول',
 2 => 'متوسط',
 3 => 'جيد جدا',
 4 => 'ممتاز',
 ),
 'az' => 
 array (
 0 => 'kasıb',
 1 => 'ortalamadan aşağı',
 2 => 'orta',
 3 => 'yaxşı',
 4 => 'əla',
 ),
 'bg' => 
 array (
 0 => 'беден',
 1 => 'под средното',
 2 => 'средно аритметично',
 3 => 'добре',
 4 => 'отлично',
 ),
 'bn' => 
 array (
 0 => 'দরিদ্র',
 1 => 'গড়ের নিচে',
 2 => 'গড়',
 3 => 'ভাল',
 4 => 'চমৎকার',
 ),
 'bs' => 
 array (
 0 => 'jadan',
 1 => 'ispod prosjeka',
 2 => 'prosjek',
 3 => 'dobro',
 4 => 'odličan',
 ),
 'cs' => 
 array (
 0 => 'Slabý',
 1 => 'Podprůměrný',
 2 => 'Průměrný',
 3 => 'Dobrý',
 4 => 'Vynikající',
 ),
 'cy' => 
 array (
 0 => 'gwael',
 1 => 'islaw\'r cyfartaledd',
 2 => 'cyffredin',
 3 => 'da',
 4 => 'rhagorol',
 ),
 'da' => 
 array (
 0 => 'Svag',
 1 => 'Under gennemsnitlig',
 2 => 'Gennemsnitlig',
 3 => 'God',
 4 => 'Fremragende',
 ),
 'de' => 
 array (
 0 => 'Schwach',
 1 => 'Unterdurchschnittlich',
 2 => 'Durchschnittlich',
 3 => 'Gut',
 4 => 'Ausgezeichnet',
 ),
 'el' => 
 array (
 0 => 'Χαμηλή',
 1 => 'Κάτω από τον μέσο όρο',
 2 => 'Μέτρια',
 3 => 'Καλή',
 4 => 'Άριστη',
 ),
 'es' => 
 array (
 0 => 'Flojo',
 1 => 'Por debajo de lo regular',
 2 => 'Regular',
 3 => 'Bueno',
 4 => 'Excelente',
 ),
 'et' => 
 array (
 0 => 'halb',
 1 => 'alla keskmise',
 2 => 'keskmine',
 3 => 'hea',
 4 => 'suurepärane',
 ),
 'fa' => 
 array (
 0 => 'فقیر',
 1 => 'زیر میانگین',
 2 => 'میانگین',
 3 => 'خوب',
 4 => 'عالی',
 ),
 'fi' => 
 array (
 0 => 'Heikko',
 1 => 'Keskitasoa alhaisempi',
 2 => 'Keskitasoinen',
 3 => 'Hyvä',
 4 => 'Erinomainen',
 ),
 'fr' => 
 array (
 0 => 'faible',
 1 => 'moyenne basse',
 2 => 'moyenne',
 3 => 'bien',
 4 => 'excellent',
 ),
 'gd' => 
 array (
 0 => 'bochd',
 1 => 'nas ìsle na a ’chuibheasachd',
 2 => 'cuibheasach',
 3 => 'math',
 4 => 'sgoinneil',
 ),
 'gl' => 
 array (
 0 => 'pobre',
 1 => 'por debaixo da media',
 2 => 'media',
 3 => 'bo',
 4 => 'excelente',
 ),
 'he' => 
 array (
 0 => 'עני',
 1 => 'מתחת לממוצע',
 2 => 'מְמוּצָע',
 3 => 'טוֹב',
 4 => 'מְעוּלֶה',
 ),
 'hi' => 
 array (
 0 => 'कमज़ोर',
 1 => 'औसत से कम',
 2 => 'औसत',
 3 => 'अच्छा',
 4 => 'अति उत्कृष्ट',
 ),
 'hr' => 
 array (
 0 => 'slabo',
 1 => 'ispod prosjeka',
 2 => 'prosjed',
 3 => 'dobro',
 4 => 'odličan',
 ),
 'hu' => 
 array (
 0 => 'Gyenge',
 1 => 'Átlag alatti',
 2 => 'Átlagos',
 3 => 'Jó',
 4 => 'Kiváló',
 ),
 'hy' => 
 array (
 0 => 'աղքատ',
 1 => 'միջինից ցածր',
 2 => 'միջին',
 3 => 'լավ',
 4 => 'գերազանց',
 ),
 'id' => 
 array (
 0 => 'miskin',
 1 => 'dibawah rata-rata',
 2 => 'rata-rata',
 3 => 'bagus',
 4 => 'bagus sekali',
 ),
 'is' => 
 array (
 0 => 'fátækur',
 1 => 'fyrir neðan meðallag',
 2 => 'að meðaltali',
 3 => 'góður',
 4 => 'Æðislegt',
 ),
 'it' => 
 array (
 0 => 'Scarso',
 1 => 'Sotto la media',
 2 => 'Medio',
 3 => 'Buono',
 4 => 'Eccellente',
 ),
 'ja' => 
 array (
 0 => '悪い',
 1 => '平均以下の',
 2 => '平均',
 3 => '良い',
 4 => '優れた',
 ),
 'ka' => 
 array (
 0 => 'ღარიბი',
 1 => 'საშუალოზე დაბლა',
 2 => 'საშუალო',
 3 => 'კარგი',
 4 => 'შესანიშნავი',
 ),
 'kk' => 
 array (
 0 => 'кедей',
 1 => 'орташадан төмен',
 2 => 'орташа',
 3 => 'жақсы',
 4 => 'өте жақсы',
 ),
 'ko' => 
 array (
 0 => '가난한',
 1 => '평균 이하',
 2 => '평균',
 3 => '좋은',
 4 => '훌륭한',
 ),
 'lt' => 
 array (
 0 => 'vargšas',
 1 => 'žemiau vidurkio',
 2 => 'vidurkis',
 3 => 'gerai',
 4 => 'puikus',
 ),
 'mk' => 
 array (
 0 => 'Сиромашен',
 1 => 'под просек',
 2 => 'просек',
 3 => 'Добро',
 4 => 'одлично',
 ),
 'ms' => 
 array (
 0 => 'miskin',
 1 => 'bawah purata',
 2 => 'purata',
 3 => 'baik',
 4 => 'cemerlang',
 ),
 'nl' => 
 array (
 0 => 'Slecht',
 1 => 'Onder het gemiddelde',
 2 => 'Gemiddeld',
 3 => 'Goede',
 4 => 'Uitstekend',
 ),
 'no' => 
 array (
 0 => 'Dårlig',
 1 => 'Utilstrekkelig',
 2 => 'Gjennomsnittlig',
 3 => 'Bra',
 4 => 'Utmerket',
 ),
 'pl' => 
 array (
 0 => 'Słaba',
 1 => 'Poniżej średniej',
 2 => 'Średnia',
 3 => 'Dobra',
 4 => 'Doskonała',
 ),
 'pt' => 
 array (
 0 => 'Fraco',
 1 => 'Inferior ao médio',
 2 => 'Medíocre',
 3 => 'Bom',
 4 => 'Excelente',
 ),
 'ro' => 
 array (
 0 => 'sărac',
 1 => 'sub medie',
 2 => 'in medie',
 3 => 'bun',
 4 => 'excelent',
 ),
 'ru' => 
 array (
 0 => 'Слабо',
 1 => 'Ниже среднего',
 2 => 'Средний',
 3 => 'Хорошо',
 4 => 'Отлично',
 ),
 'sk' => 
 array (
 0 => 'Slabé',
 1 => 'Podpriemerné',
 2 => 'Priemerné',
 3 => 'Dobré',
 4 => 'Vynikajúce',
 ),
 'sl' => 
 array (
 0 => 'slabo',
 1 => 'pod povprečjem',
 2 => 'povprečno',
 3 => 'dobro',
 4 => 'odlično',
 ),
 'sq' => 
 array (
 0 => 'i varfer',
 1 => 'nën mesataren',
 2 => 'mesatare',
 3 => 'mire',
 4 => 'e shkëlqyeshme',
 ),
 'sr' => 
 array (
 0 => 'Слабо',
 1 => 'Испод просека',
 2 => 'Просек',
 3 => 'Добро',
 4 => 'Oдлично',
 ),
 'sv' => 
 array (
 0 => 'Dålig',
 1 => 'Under genomsnittet',
 2 => 'Genomsnittlig',
 3 => 'Bra',
 4 => 'Utmärkt',
 ),
 'th' => 
 array (
 0 => 'ยากจน',
 1 => 'ต่ำกว่าค่าเฉลี่ย',
 2 => 'เฉลี่ย',
 3 => 'ดี',
 4 => 'ยอดเยี่ยม',
 ),
 'tr' => 
 array (
 0 => 'Zayıf',
 1 => 'Ortanın altıi',
 2 => 'Orta',
 3 => 'İyi',
 4 => 'Mükemmel',
 ),
 'uk' => 
 array (
 0 => 'погано',
 1 => 'нижче середнього',
 2 => 'середній',
 3 => 'добре',
 4 => 'відмінно',
 ),
 'vi' => 
 array (
 0 => 'nghèo nàn',
 1 => 'dưới mức trung bình',
 2 => 'Trung bình',
 3 => 'tốt',
 4 => 'thông minh',
 ),
 'zh' => 
 array (
 0 => '差',
 1 => '不如一般',
 2 => '一般',
 3 => '好',
 4 => '非常好',
 ),
);
private static $widget_verified_texts = array (
 'en' => 'Verified',
 'af' => 'Geverifieer',
 'ar' => 'تم التحقق',
 'az' => 'Doğrulanmışdır',
 'bg' => 'Проверени',
 'bn' => 'যাচাই',
 'bs' => 'Provjereno',
 'cs' => 'Ověřená',
 'cy' => 'Wedi\'i ddilysu',
 'da' => 'Bekræftet',
 'de' => 'Verifiziert',
 'el' => 'επαληθεύτηκε',
 'es' => 'Verificada',
 'et' => 'Kinnitatud',
 'fa' => 'تأیید شده',
 'fi' => 'Vahvistettu',
 'fr' => 'vérifié',
 'gd' => 'Dearbhaichte',
 'gl' => 'Verificado',
 'he' => 'מְאוּמָת',
 'hi' => 'सत्यापित',
 'hr' => 'Potvrđen',
 'hu' => 'Hitelesített',
 'hy' => 'Ստուգված',
 'id' => 'Diverifikasi',
 'is' => 'Staðfesting',
 'it' => 'Verificata',
 'ja' => '確認済み',
 'ka' => 'დამოწმებული',
 'kk' => 'тексерілген',
 'ko' => '검증 된',
 'lt' => 'Patvirtinta',
 'mk' => 'Потврдена',
 'ms' => 'Disahkan',
 'nl' => 'Geverifieerd',
 'no' => 'Bekreftet',
 'pl' => 'Zweryfikowana',
 'pt' => 'Verificada',
 'ro' => 'Verificat',
 'ru' => 'Проверенный',
 'sk' => 'Overená',
 'sl' => 'Preverjeno',
 'sq' => 'Verifikuar',
 'sr' => 'Проверено',
 'sv' => 'Verifierad',
 'th' => 'ตรวจสอบแล้ว',
 'tr' => 'Doğrulanmış',
 'uk' => 'Перевірено',
 'vi' => 'Đã xác minh',
 'zh' => '已验证',
);
private static $widget_verified_platform_texts = array (
 'en' => 'Trustindex verifies that the original source of the review is %platform%.',
 'af' => 'Trustindex verifieer dat die oorspronklike bron van die resensie %platform% is.',
 'ar' => 'تتحقق Trustindex من أن المصدر الأصلي للمراجعة هو %platform%.',
 'az' => 'Trustindex yoxlamanın orijinal mənbəyinin %platform% olduğunu təsdiqləyir.',
 'bg' => 'Trustindex проверява дали оригиналният източник на прегледа е %platform%.',
 'bn' => 'Trustindex যাচাই করে যে পর্যালোচনার মূল উৎস হল %platform%।',
 'bs' => 'Trustindex potvrđuje da je izvorni izvor recenzije %platform%.',
 'cs' => 'Trustindex ověřuje, že původní zdroj recenze je %platform%.',
 'cy' => 'Mae Trustindex yn gwirio mai ffynhonnell wreiddiol yr adolygiad yw %platform%.',
 'da' => 'Trustindex verificerer, at den oprindelige kilde til anmeldelsen er %platform%.',
 'de' => 'Trustindex überprüft, ob die Originalquelle der Bewertung %platform% ist.',
 'el' => 'Το Trustindex επαληθεύει ότι η αρχική πηγή της κριτικής είναι %platform%.',
 'es' => 'Trustindex verifica que la fuente original de la reseña sea %platform%.',
 'et' => 'Trustindex kontrollib, et arvustuse algallikas on %platform%.',
 'fa' => 'Trustindex تأیید می کند که منبع اصلی بازبینی %platform% است.',
 'fi' => 'Trustindex vahvistaa, että arvostelun alkuperäinen lähde on %platform%.',
 'fr' => 'Trustindex vérifie que la source originale de l\'avis est %platform%.',
 'gd' => 'Tha Trustindex a’ dearbhadh gur e %platform% tùs an ath-bhreithneachaidh.',
 'gl' => 'Trustindex verifica que a fonte orixinal da revisión é %platform%.',
 'he' => 'Trustindex מוודא שהמקור המקורי של הסקירה הוא %platform%.',
 'hi' => 'ट्रस्टइंडेक्स सत्यापित करता है कि समीक्षा का मूल स्रोत %platform% है।',
 'hr' => 'Trustindex provjerava je li izvorni izvor recenzije %platform%.',
 'hu' => 'A Trustindex hitelesíti, hogy a vélemény eredeti forrása %platform%.',
 'hy' => 'Trustindex-ը հաստատում է, որ վերանայման սկզբնական աղբյուրը %platform% է:',
 'id' => 'Trustindex memverifikasi bahwa sumber asli ulasan adalah %platform%.',
 'is' => 'Trustindex sannreynir að upprunaleg uppspretta endurskoðunarinnar sé %platform%.',
 'it' => 'Trustindex verifica che la fonte originale della recensione sia %platform%.',
 'ja' => 'Trustindex は、レビューの元のソースが %platform% であることを確認します。',
 'ka' => 'Trustindex ადასტურებს, რომ მიმოხილვის ორიგინალური წყაროა %platform%.',
 'kk' => 'Trustindex шолудың бастапқы көзі %platform% екенін тексереді.',
 'ko' => 'Trustindex는 리뷰의 원본 소스가 %platform% 인지 확인합니다.',
 'lt' => 'Trustindex patikrina, ar pirminis apžvalgos šaltinis yra %platform%.',
 'mk' => 'Trustindex потврдува дека оригиналниот извор на прегледот е %platform%.',
 'ms' => 'Trustindex mengesahkan bahawa sumber asal semakan adalah %platform%.',
 'nl' => 'Trustindex verifieert dat de oorspronkelijke bron van de recensie %platform% is.',
 'no' => 'Trustindex bekrefter at den opprinnelige kilden til anmeldelsen er %platform%.',
 'pl' => 'Trustindex sprawdza, czy pierwotnym źródłem recenzji jest %platform%.',
 'pt' => 'Trustindex verifica se a fonte original da avaliação é %platform%.',
 'ro' => 'Trustindex verifică că sursa originală a recenziei este %platform%.',
 'ru' => 'Trustindex проверяет, что первоначальным источником отзыва является %platform%.',
 'sk' => 'Trustindex overuje, že pôvodný zdroj recenzie je %platform%.',
 'sl' => 'Trustindex preveri, ali je izvorni vir ocene %platform%.',
 'sq' => 'Trustindex verifikon që burimi origjinal i rishikimit është %platform%.',
 'sr' => 'Trustindex потврђује да је оригинални извор рецензије %platform%.',
 'sv' => 'Trustindex verifierar att den ursprungliga källan till recensionen är %platform%.',
 'th' => 'Trustindex ตรวจสอบว่าแหล่งที่มาดั้งเดิมของรีวิวคือ %platform%',
 'tr' => 'Trustindex, incelemenin orijinal kaynağının %platform% olduğunu doğrular.',
 'uk' => 'Trustindex перевіряє, що вихідним джерелом відгуку є %platform%.',
 'vi' => 'Trustindex xác minh rằng nguồn đánh giá ban đầu là %platform%.',
 'zh' => 'Trustindex 核实该评论的原始来源是 %platform%。',
);
private static $widget_footer_filter_texts = array (
 'en' => 
 array (
 'star' => 'Showing only RATING_STAR_FILTER star reviews',
 'latest' => 'Showing our latest reviews',
 ),
 'af' => 
 array (
 'star' => 'Wys tans net RATING_STAR_FILTER sterresensies',
 'latest' => 'Wys ons jongste resensies',
 ),
 'ar' => 
 array (
 'star' => 'يتم عرض تقييمات RATING_STAR_FILTER نجمة فقط',
 'latest' => 'عرض أحدث تقييماتنا',
 ),
 'az' => 
 array (
 'star' => 'Yalnız RATING_STAR_FILTER ulduzlu rəylər göstərilir',
 'latest' => 'Ən son rəylərimiz göstərilir',
 ),
 'bg' => 
 array (
 'star' => 'Показани са само отзиви с RATING_STAR_FILTER звезди',
 'latest' => 'Показване на най-новите ни отзиви',
 ),
 'bn' => 
 array (
 'star' => 'শুধুমাত্র RATING_STAR_FILTER স্টার রিভিউ দেখানো হচ্ছে',
 'latest' => 'আমাদের সর্বশেষ পর্যালোচনা দেখাচ্ছে',
 ),
 'bs' => 
 array (
 'star' => 'Prikazuju se samo recenzije sa RATING_STAR_FILTER zvjezdicama',
 'latest' => 'Prikazujemo naše najnovije recenzije',
 ),
 'cs' => 
 array (
 'star' => 'Zobrazují se pouze recenze s RATING_STAR_FILTER hvězdičkami',
 'latest' => 'Zobrazujeme naše nejnovější recenze',
 ),
 'cy' => 
 array (
 'star' => 'Yn dangos adolygiadau seren RATING_STAR_FILTER yn unig',
 'latest' => 'Yn dangos ein adolygiadau diweddaraf',
 ),
 'da' => 
 array (
 'star' => 'Viser kun anmeldelser med RATING_STAR_FILTER stjerner',
 'latest' => 'Viser vores seneste anmeldelser',
 ),
 'de' => 
 array (
 'star' => 'Es werden nur RATING_STAR_FILTER Sternebewertungen angezeigt',
 'latest' => 'Wir zeigen unsere neuesten Bewertungen',
 ),
 'el' => 
 array (
 'star' => 'Εμφάνιση μόνο RATING_STAR_FILTER κριτικές με αστέρια',
 'latest' => 'Εμφάνιση των τελευταίων κριτικές μας',
 ),
 'es' => 
 array (
 'star' => 'Mostrando solo RATING_STAR_FILTER reseñas estrellas',
 'latest' => 'Mostrando nuestras últimas reseñas',
 ),
 'et' => 
 array (
 'star' => 'Kuvatakse ainult RATING_STAR_FILTER tärniga arvustused',
 'latest' => 'Kuvatakse meie viimased arvustused',
 ),
 'fa' => 
 array (
 'star' => 'نمایش فقط نظرات RATING_STAR_FILTER ستاره',
 'latest' => 'نمایش آخرین نظرات ما',
 ),
 'fi' => 
 array (
 'star' => 'Näytetään vain RATING_STAR_FILTER tähden arvostelut',
 'latest' => 'Näytetään viimeisimmät arvostelut',
 ),
 'fr' => 
 array (
 'star' => 'Affichage de RATING_STAR_FILTER avis étoiles uniquement',
 'latest' => 'Affichage de nos derniers avis',
 ),
 'gd' => 
 array (
 'star' => 'A’ sealltainn RATING_STAR_FILTER lèirmheasan rionnagan a-mhàin',
 'latest' => 'A’ sealltainn na lèirmheasan as ùire againn',
 ),
 'gl' => 
 array (
 'star' => 'Mostrando só RATING_STAR_FILTER comentarios de estrelas',
 'latest' => 'Mostrando os nosos últimos comentarios',
 ),
 'he' => 
 array (
 'star' => 'מציג רק RATING_STAR_FILTER ביקורות כוכבים',
 'latest' => 'מציג את הביקורות האחרונות שלנו',
 ),
 'hi' => 
 array (
 'star' => 'मैन्युअल रूप से चुनी गई समीक्षाएँ दिखाई जा रही हैं',
 'latest' => 'हमारी सबसे पुरानी समीक्षाएँ दिखा रहा हूँ',
 ),
 'hr' => 
 array (
 'star' => 'Prikazuju se samo recenzije s RATING_STAR_FILTER zvjezdica',
 'latest' => 'Prikazuju se naše najnovije recenzije',
 ),
 'hu' => 
 array (
 'star' => 'Csak RATING_STAR_FILTER csillagos vélemények láthatók',
 'latest' => 'Legkorábbi véleményeink láthatók',
 ),
 'hy' => 
 array (
 'star' => 'Ցուցադրվում են միայն RATING_STAR_FILTER աստղային կարծիքներ',
 'latest' => 'Ցուցադրվում են մեր վերջին ակնարկները',
 ),
 'id' => 
 array (
 'star' => 'Hanya menampilkan ulasan berbintang RATING_STAR_FILTER',
 'latest' => 'Menampilkan ulasan terbaru kami',
 ),
 'is' => 
 array (
 'star' => 'Sýnir aðeins RATING_STAR_FILTER stjörnu umsagnir',
 'latest' => 'Sýnir nýjustu umsagnirnar okkar',
 ),
 'it' => 
 array (
 'star' => 'Vengono visualizzate solo RATING_STAR_FILTER recensioni a stelle',
 'latest' => 'Mostrando le nostre ultime recensioni',
 ),
 'ja' => 
 array (
 'star' => 'RATING_STAR_FILTER の星付きレビューのみを表示しています',
 'latest' => '最新のレビューを表示しています',
 ),
 'ka' => 
 array (
 'star' => 'ნაჩვენებია მხოლოდ RATING_STAR_FILTER ვარსკვლავიანი მიმოხილვები',
 'latest' => 'ნაჩვენებია ჩვენი უახლესი მიმოხილვები',
 ),
 'kk' => 
 array (
 'star' => 'Тек RATING_STAR_FILTER жұлдызды шолулар көрсетілген',
 'latest' => 'Соңғы шолуларымызды көрсету',
 ),
 'ko' => 
 array (
 'star' => '별점 리뷰 RATING_STAR_FILTER 개만 표시',
 'latest' => '최신 리뷰 표시',
 ),
 'lt' => 
 array (
 'star' => 'Rodomi tik RATING_STAR_FILTER žvaigždučių atsiliepimais',
 'latest' => 'Rodomi mūsų naujausios atsiliepimais',
 ),
 'mk' => 
 array (
 'star' => 'Се прикажуваат само RATING_STAR_FILTER рецензии со ѕвезди',
 'latest' => 'Се прикажуваат нашите најнови критики',
 ),
 'ms' => 
 array (
 'star' => 'Menunjukkan ulasan bintang RATING_STAR_FILTER sahaja',
 'latest' => 'Menunjukkan ulasan terkini kami',
 ),
 'nl' => 
 array (
 'star' => 'Er worden alleen RATING_STAR_FILTER sterrecensies weergegeven',
 'latest' => 'Toont onze laatste recensies',
 ),
 'no' => 
 array (
 'star' => 'Viser bare RATING_STAR_FILTER stjerneanmeldelser',
 'latest' => 'Viser de siste anmeldelsene våre',
 ),
 'pl' => 
 array (
 'star' => 'Wyświetlanie tylko RATING_STAR_FILTER opinii w postaci gwiazdek',
 'latest' => 'Wyświetlanie naszych najnowszych opinii',
 ),
 'pt' => 
 array (
 'star' => 'Mostrando apenas avaliações com estrelas de RATING_STAR_FILTER',
 'latest' => 'Mostrando nossas avaliações mais recentes',
 ),
 'ro' => 
 array (
 'star' => 'Se afișează numai RATING_STAR_FILTER recenzii cu stele',
 'latest' => 'Se afișează cele mai recente recenzii ale noastre',
 ),
 'ru' => 
 array (
 'star' => 'Показаны только отзывы со звездами RATING_STAR_FILTER',
 'latest' => 'Показаны наши последние отзывы',
 ),
 'sk' => 
 array (
 'star' => 'Zobrazujú sa iba recenzie s RATING_STAR_FILTER hviezdičkami',
 'latest' => 'Zobrazujú sa naše najnovšie recenzie',
 ),
 'sl' => 
 array (
 'star' => 'Prikazane so le ocene s RATING_STAR_FILTER zvezdicami',
 'latest' => 'Prikazane so naše najnovejše ocene',
 ),
 'sq' => 
 array (
 'star' => 'Duke shfaqur vetëm komente RATING_STAR_FILTER yje',
 'latest' => 'Duke shfaqur komentet tona më të fundit',
 ),
 'sr' => 
 array (
 'star' => 'Приказују се само рецензије са RATING_STAR_FILTER звездицама',
 'latest' => 'Приказујемо наше најновије рецензије',
 ),
 'sv' => 
 array (
 'star' => 'Visar endast RATING_STAR_FILTER stjärnrecensioner',
 'latest' => 'Visar våra senaste recensioner',
 ),
 'th' => 
 array (
 'star' => 'แสดงเฉพาะบทวิจารณ์ระดับ RATING_STAR_FILTER ดาว',
 'latest' => 'แสดงความคิดเห็นล่าสุดของเรา',
 ),
 'tr' => 
 array (
 'star' => 'Yalnızca RATING_STAR_FILTER yıldızlı değerlendirmelere gösteriliyor',
 'latest' => 'En son değerlendirmelere gösteriliyor',
 ),
 'uk' => 
 array (
 'star' => 'Показано лише RATING_STAR_FILTER старих рецензії',
 'latest' => 'Показано наші останні рецензії',
 ),
 'vi' => 
 array (
 'star' => 'Chỉ hiển thị RATING_STAR_FILTER bài đánh giá sao',
 'latest' => 'Đang hiển thị các đánh giá mới nhất của chúng tôi',
 ),
 'zh' => 
 array (
 'star' => '仅显示 RATING_STAR_FILTER 星评价',
 'latest' => '显示我们的最新评论',
 ),
);
public static $verified_platforms = array (
 0 => 'Abia',
 1 => 'Agoda',
 2 => 'Airbnb',
 3 => 'Alibaba',
 4 => 'Aliexpress',
 5 => 'Amazon',
 6 => 'AppleAppstore',
 7 => 'Bark',
 8 => 'Booking',
 9 => 'CarGurus',
 10 => 'Classpass',
 11 => 'Ebay',
 12 => 'Ekomi',
 13 => 'Etsy',
 14 => 'Expedia',
 15 => 'Fresha',
 16 => 'Getyourguide',
 17 => 'Hotels',
 18 => 'HotelSpecials',
 19 => 'Immobilienscout24',
 20 => 'Indeed',
 21 => 'Justdial',
 22 => 'Lawyerscom',
 23 => 'Martindale',
 24 => 'Meilleursagents',
 25 => 'Mobilede',
 26 => 'OnlinePenztarca',
 27 => 'Opentable',
 28 => 'Peerspot',
 29 => 'ProductReview',
 30 => 'Realself',
 31 => 'Reco',
 32 => 'Resellerratings',
 33 => 'ReserveOut',
 34 => 'Reviewsio',
 35 => 'Sitejabber',
 36 => 'SoftwareAdvice',
 37 => 'SourceForge',
 38 => 'Szallashu',
 39 => 'Talabat',
 40 => 'Tandlakare',
 41 => 'TheFork',
 42 => 'Thumbtack',
 43 => 'Tripadvisor',
 44 => 'TrustedShops',
 45 => 'TrustRadius',
 46 => 'Vardense',
 47 => 'Vrbo',
 48 => 'WeddingWire',
 49 => 'Whatclinic',
 50 => 'Whichtrustedtraders',
 51 => 'Yelp',
 52 => 'Zillow',
 53 => 'ZocDoc',
 54 => 'Zomato',
 55 => 'G2Crowd',
 56 => 'FertilityIQ',
 57 => 'Viator',
);
private static $widget_month_names = array (
 'en' => 
 array (
 0 => 'January',
 1 => 'February',
 2 => 'March',
 3 => 'April',
 4 => 'May',
 5 => 'June',
 6 => 'July',
 7 => 'August',
 8 => 'September',
 9 => 'October',
 10 => 'November',
 11 => 'December',
 ),
 'af' => 
 array (
 0 => 'Januarie',
 1 => 'Februarie',
 2 => 'Maart',
 3 => 'April',
 4 => 'Mei',
 5 => 'Junie',
 6 => 'Julie',
 7 => 'Augustus',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'Desember',
 ),
 'ar' => 
 array (
 0 => 'يناير',
 1 => 'فبراير',
 2 => 'مارس',
 3 => 'أبريل',
 4 => 'مايو',
 5 => 'يونيو',
 6 => 'يوليه',
 7 => 'أغسطس',
 8 => 'سبتمبر',
 9 => 'أكتوبر',
 10 => 'نوفمبر',
 11 => 'ديسمبر',
 ),
 'az' => 
 array (
 0 => 'Yanvar',
 1 => 'Fevral',
 2 => 'Mart',
 3 => 'Aprel',
 4 => 'May',
 5 => 'İyun',
 6 => 'İyul',
 7 => 'Avqust',
 8 => 'Sentyabr',
 9 => 'Oktyabr',
 10 => 'Noyabr',
 11 => 'Dekabr',
 ),
 'bg' => 
 array (
 0 => 'Януари',
 1 => 'февруари',
 2 => 'Март',
 3 => 'Aприл',
 4 => 'май',
 5 => 'юни',
 6 => 'юли',
 7 => 'Август',
 8 => 'Септември',
 9 => 'Октомври',
 10 => 'Ноември',
 11 => 'Декември',
 ),
 'bn' => 
 array (
 0 => 'জানুয়ারি',
 1 => 'ফেব্রুয়ারি',
 2 => 'মার্চ',
 3 => 'এপ্রিল',
 4 => 'মে',
 5 => 'জুন',
 6 => 'জুলাই',
 7 => 'আগস্ট',
 8 => 'সেপ্টেম্বর',
 9 => 'অক্টোবর',
 10 => 'নভেম্বর',
 11 => 'ডিসেম্বর',
 ),
 'bs' => 
 array (
 0 => 'Januar',
 1 => 'Februar',
 2 => 'Mart',
 3 => 'April',
 4 => 'Maj',
 5 => 'Jun',
 6 => 'Jul',
 7 => 'Avgust',
 8 => 'Septembar',
 9 => 'Oktobar',
 10 => 'Novembar',
 11 => 'Decembar',
 ),
 'cs' => 
 array (
 0 => 'Leden',
 1 => 'Únor',
 2 => 'Březen',
 3 => 'Duben',
 4 => 'Květen',
 5 => 'Červen',
 6 => 'Červenec',
 7 => 'Srpen',
 8 => 'Září',
 9 => 'Říjen',
 10 => 'Listopad',
 11 => 'Prosinec',
 ),
 'cy' => 
 array (
 0 => 'Ionawr',
 1 => 'Chwefror',
 2 => 'Mawrth',
 3 => 'Ebrill',
 4 => 'Mai',
 5 => 'Mehefin',
 6 => 'Gorffennaf',
 7 => 'Awst',
 8 => 'Medi',
 9 => 'Hydref',
 10 => 'Tachwedd',
 11 => 'Rhagfyr',
 ),
 'da' => 
 array (
 0 => 'Januar',
 1 => 'Februar',
 2 => 'Marts',
 3 => 'April',
 4 => 'Maj',
 5 => 'Juni',
 6 => 'Juli',
 7 => 'August',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'December',
 ),
 'de' => 
 array (
 0 => 'Januar',
 1 => 'Februar',
 2 => 'März',
 3 => 'April',
 4 => 'Mai',
 5 => 'Juni',
 6 => 'Juli',
 7 => 'August',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'Dezember',
 ),
 'el' => 
 array (
 0 => 'Iανουάριος',
 1 => 'Φεβρουάριος',
 2 => 'Μάρτιος',
 3 => 'Aρίλιος',
 4 => 'Μάιος',
 5 => 'Iούνιος',
 6 => 'Iούλιος',
 7 => 'Αύγουστος',
 8 => 'Σεπτέμβριος',
 9 => 'Oκτώβριος',
 10 => 'Νοέμβριος',
 11 => 'Δεκέμβριος',
 ),
 'es' => 
 array (
 0 => 'Enero',
 1 => 'Febrero',
 2 => 'Marzo',
 3 => 'Abril',
 4 => 'Mayo',
 5 => 'Junio',
 6 => 'Julio',
 7 => 'Agosto',
 8 => 'Septiembre',
 9 => 'Octubre',
 10 => 'Noviembre',
 11 => 'Diciembre',
 ),
 'et' => 
 array (
 0 => 'jaanuar',
 1 => 'veebruar',
 2 => 'märts',
 3 => 'aprill',
 4 => 'mai',
 5 => 'juuni',
 6 => 'juuli',
 7 => 'august',
 8 => 'september',
 9 => 'oktoober',
 10 => 'november',
 11 => 'detsember',
 ),
 'fa' => 
 array (
 0 => 'ژانویه',
 1 => 'فوریه',
 2 => 'مارس',
 3 => 'آوریل',
 4 => 'ممکن است',
 5 => 'ژوئن',
 6 => 'جولای',
 7 => 'اوت',
 8 => 'سپتامبر',
 9 => 'اکتبر',
 10 => 'نوامبر',
 11 => 'دسامبر',
 ),
 'fi' => 
 array (
 0 => 'Tammikuu',
 1 => 'Helmikuu',
 2 => 'Maaliskuu',
 3 => 'Huhtikuu',
 4 => 'Toukokuu',
 5 => 'Kesäkuu',
 6 => 'Heinäkuu',
 7 => 'Elokuu',
 8 => 'Syyskuu',
 9 => 'Lokakuu',
 10 => 'Marraskuu',
 11 => 'Joulukuu',
 ),
 'fr' => 
 array (
 0 => 'Janvier',
 1 => 'Février',
 2 => 'Mars',
 3 => 'Avril',
 4 => 'Mai',
 5 => 'Juin',
 6 => 'Juillet',
 7 => 'Août',
 8 => 'Septembre',
 9 => 'Octobre',
 10 => 'Novembre',
 11 => 'Décembre',
 ),
 'gd' => 
 array (
 0 => 'am Faoilleach',
 1 => 'an Gearran',
 2 => 'am Màrt',
 3 => 'an Giblean',
 4 => 'an Cèitean',
 5 => 'an t-Ògmhios',
 6 => 'an t-luchar',
 7 => 'an Lùnastal',
 8 => 'an t-Sultain',
 9 => 'an Dàmhair',
 10 => 'an t-Samhain',
 11 => 'an Dùbhlachd',
 ),
 'gl' => 
 array (
 0 => 'Xaneiro',
 1 => 'Febreiro',
 2 => 'Marzo',
 3 => 'Abril',
 4 => 'Maio',
 5 => 'Xuño',
 6 => 'Xullo',
 7 => 'Agosto',
 8 => 'Setembro',
 9 => 'Outubro',
 10 => 'Novembro',
 11 => 'Decembro',
 ),
 'he' => 
 array (
 0 => 'ינואר',
 1 => 'פברואר',
 2 => 'מרץ',
 3 => 'אפריל',
 4 => 'מאי',
 5 => 'יוני',
 6 => 'יולי',
 7 => 'אוגוסט',
 8 => 'ספטמבר',
 9 => 'אוקטובר',
 10 => 'נובמבר',
 11 => 'דצמבר',
 ),
 'hi' => 
 array (
 0 => 'जनवरी',
 1 => 'फ़रवरी',
 2 => 'मार्च',
 3 => 'अप्रैल',
 4 => 'मई',
 5 => 'जून',
 6 => 'जुलाई',
 7 => 'अगस्त',
 8 => 'सितंबर',
 9 => 'अक्टूबर',
 10 => 'नवंबर',
 11 => 'दिसंबर',
 ),
 'hr' => 
 array (
 0 => 'Siječanj',
 1 => 'Veljača',
 2 => 'Ožujak',
 3 => 'Travanj',
 4 => 'Svibanj',
 5 => 'Lipanj',
 6 => 'Srpanj',
 7 => 'Kolovoz',
 8 => 'Rujan',
 9 => 'Listopad',
 10 => 'Studeni',
 11 => 'Prosinac',
 ),
 'hu' => 
 array (
 0 => 'Január',
 1 => 'Február',
 2 => 'Március',
 3 => 'Április',
 4 => 'Május',
 5 => 'Június',
 6 => 'Július',
 7 => 'Augusztus',
 8 => 'Szeptember',
 9 => 'Október',
 10 => 'November',
 11 => 'December',
 ),
 'hy' => 
 array (
 0 => 'Հունվար',
 1 => 'փետրվար',
 2 => 'մարտ',
 3 => 'ապրիլ',
 4 => 'մայիս',
 5 => 'հունիս',
 6 => 'հուլիս',
 7 => 'օգոստոս',
 8 => 'սեպտեմբեր',
 9 => 'հոկտեմբեր',
 10 => 'նոյեմբեր',
 11 => 'դեկտեմբեր',
 ),
 'id' => 
 array (
 0 => 'Januari',
 1 => 'Februari',
 2 => 'Maret',
 3 => 'April',
 4 => 'Mei',
 5 => 'Juni',
 6 => 'Juli',
 7 => 'Agustus',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'Desember',
 ),
 'is' => 
 array (
 0 => 'Janúar',
 1 => 'Febrúar',
 2 => 'Mars',
 3 => 'April',
 4 => 'Maí',
 5 => 'Júní',
 6 => 'Júlí',
 7 => 'Ágúst',
 8 => 'September',
 9 => 'Október',
 10 => 'Nóvember',
 11 => 'Desember',
 ),
 'it' => 
 array (
 0 => 'Gennaio',
 1 => 'Febbraio',
 2 => 'Marzo',
 3 => 'Aprile',
 4 => 'Maggio',
 5 => 'Giugno',
 6 => 'Luglio',
 7 => 'Agosto',
 8 => 'Settembre',
 9 => 'Ottobre',
 10 => 'Novembre',
 11 => 'Dicembre',
 ),
 'ja' => 
 array (
 0 => '1月',
 1 => '2月',
 2 => '3月',
 3 => '4月',
 4 => '5月',
 5 => '6月',
 6 => '7月',
 7 => '8月',
 8 => '9月',
 9 => '10月',
 10 => '11月',
 11 => '12月',
 ),
 'ka' => 
 array (
 0 => 'იანვარი',
 1 => 'თებერვალი',
 2 => 'მარტი',
 3 => 'აპრილი',
 4 => 'მაისი',
 5 => 'ივნისი',
 6 => 'ივლისი',
 7 => 'აგვისტო',
 8 => 'სექტემბერი',
 9 => 'ოქტომბერი',
 10 => 'ნოემბერი',
 11 => 'დეკემბერი',
 ),
 'kk' => 
 array (
 0 => 'қаңтар',
 1 => 'ақпан',
 2 => 'наурыз',
 3 => 'сәуір',
 4 => 'мамыр',
 5 => 'маусым',
 6 => 'шілде',
 7 => 'тамыз',
 8 => 'қыркүйек',
 9 => 'қазан',
 10 => 'қараша',
 11 => 'желтоқсан',
 ),
 'ko' => 
 array (
 0 => '일월',
 1 => '이월',
 2 => '삼월',
 3 => '사월',
 4 => '오월',
 5 => '유월',
 6 => '칠월',
 7 => '팔월',
 8 => '구월',
 9 => '시월',
 10 => '십일월',
 11 => '십이월',
 ),
 'lt' => 
 array (
 0 => 'Sausis',
 1 => 'Vasaris',
 2 => 'Kovas',
 3 => 'Balandis',
 4 => 'Gegužė',
 5 => 'Birželis',
 6 => 'Liepa',
 7 => 'Rugpjūtis',
 8 => 'Rugsėjis',
 9 => 'Spalis',
 10 => 'Lapkritis',
 11 => 'Gruodis',
 ),
 'mk' => 
 array (
 0 => 'Jануари',
 1 => 'февруари',
 2 => 'март',
 3 => 'април',
 4 => 'мај',
 5 => 'јуни',
 6 => 'јули',
 7 => 'август',
 8 => 'септември',
 9 => 'октомври',
 10 => 'ноември',
 11 => 'декември',
 ),
 'ms' => 
 array (
 0 => 'Januari',
 1 => 'Februari',
 2 => 'Mac',
 3 => 'April',
 4 => 'Mei',
 5 => 'Jun',
 6 => 'Julai',
 7 => 'Ogos',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'Disember',
 ),
 'nl' => 
 array (
 0 => 'Januari',
 1 => 'Februari',
 2 => 'Maart',
 3 => 'April',
 4 => 'Mei',
 5 => 'Juni',
 6 => 'Juli',
 7 => 'Augustus',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'December',
 ),
 'no' => 
 array (
 0 => 'Januar',
 1 => 'Februar',
 2 => 'Mars',
 3 => 'April',
 4 => 'Mai',
 5 => 'Juni',
 6 => 'Juli',
 7 => 'August',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'Desember',
 ),
 'pl' => 
 array (
 0 => 'Styczeń',
 1 => 'Luty',
 2 => 'Marzec',
 3 => 'Kwiecień',
 4 => 'Maj',
 5 => 'Czerwiec',
 6 => 'Lipiec',
 7 => 'Sierpień',
 8 => 'Wrzesień',
 9 => 'Październik',
 10 => 'Listopad',
 11 => 'Grudzień',
 ),
 'pt' => 
 array (
 0 => 'Janeiro',
 1 => 'Fevereiro',
 2 => 'Março',
 3 => 'Abril',
 4 => 'Maio',
 5 => 'Junho',
 6 => 'Julho',
 7 => 'Agosto',
 8 => 'Setembro',
 9 => 'Outubro',
 10 => 'Novembro',
 11 => 'Dezembro',
 ),
 'ro' => 
 array (
 0 => 'Ianuarie',
 1 => 'Februarie',
 2 => 'Martie',
 3 => 'Aprilie',
 4 => 'Mai',
 5 => 'Iunie',
 6 => 'Iulie',
 7 => 'August',
 8 => 'Septembrie',
 9 => 'Octombrie',
 10 => 'Noiembrie',
 11 => 'Decembrie',
 ),
 'ru' => 
 array (
 0 => 'январь',
 1 => 'февраль',
 2 => 'март',
 3 => 'апрель',
 4 => 'май',
 5 => 'июнь',
 6 => 'июль',
 7 => 'август',
 8 => 'сентябрь',
 9 => 'октябрь',
 10 => 'ноябрь',
 11 => 'декабрь',
 ),
 'sk' => 
 array (
 0 => 'Január',
 1 => 'Február',
 2 => 'Marec',
 3 => 'Apríl',
 4 => 'Máj',
 5 => 'Jún',
 6 => 'Júl',
 7 => 'August',
 8 => 'September',
 9 => 'Október',
 10 => 'November',
 11 => 'December',
 ),
 'sl' => 
 array (
 0 => 'Januar',
 1 => 'Februar',
 2 => 'Marec',
 3 => 'April',
 4 => 'Maj',
 5 => 'Junij',
 6 => 'Julij',
 7 => 'Avgust',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'December',
 ),
 'sq' => 
 array (
 0 => 'Janar',
 1 => 'Shkurt',
 2 => 'Mars',
 3 => 'Prill',
 4 => 'Maj',
 5 => 'Qershor',
 6 => 'Korrik',
 7 => 'Gusht',
 8 => 'Shtator',
 9 => 'Tetor',
 10 => 'Nëntor',
 11 => 'Dhjetor',
 ),
 'sr' => 
 array (
 0 => 'Јануар',
 1 => 'Фебруар',
 2 => 'Март',
 3 => 'Април',
 4 => 'Mај',
 5 => 'Јуни',
 6 => 'Јул',
 7 => 'Август',
 8 => 'Cептембар',
 9 => 'Октобар',
 10 => 'Новембар',
 11 => 'Децембар',
 ),
 'sv' => 
 array (
 0 => 'Januari',
 1 => 'Februari',
 2 => 'Mars',
 3 => 'April',
 4 => 'Maj',
 5 => 'Juni',
 6 => 'Juli',
 7 => 'Augusti',
 8 => 'September',
 9 => 'Oktober',
 10 => 'November',
 11 => 'December',
 ),
 'th' => 
 array (
 0 => 'มกราคม',
 1 => 'กุมภาพันธ์',
 2 => 'มีนาคม',
 3 => 'เมษายน',
 4 => 'พฤษภาคม',
 5 => 'มิถุนายน',
 6 => 'กรกฎาคม',
 7 => 'สิงหาคม',
 8 => 'กันยายน',
 9 => 'ตุลาคม',
 10 => 'พฤศจิกายน',
 11 => 'ธันวาคม',
 ),
 'tr' => 
 array (
 0 => 'Ocak',
 1 => 'Şubat',
 2 => 'Mart',
 3 => 'Nisan',
 4 => 'Mayis',
 5 => 'Haziran',
 6 => 'Temmuz',
 7 => 'Ağustos',
 8 => 'Eylül',
 9 => 'Ekim',
 10 => 'Kasım',
 11 => 'Aralık',
 ),
 'uk' => 
 array (
 0 => 'Січня',
 1 => 'Лютий',
 2 => 'Березень',
 3 => 'квітень',
 4 => 'травень',
 5 => 'червень',
 6 => 'липень',
 7 => 'серпень',
 8 => 'вересень',
 9 => 'жовтень',
 10 => 'листопад',
 11 => 'грудень',
 ),
 'vi' => 
 array (
 0 => 'tháng một',
 1 => 'tháng hai',
 2 => 'tháng ba',
 3 => 'tháng tư',
 4 => 'tháng năm',
 5 => 'tháng sáu',
 6 => 'tháng bảy',
 7 => 'tháng tám',
 8 => 'tháng chín',
 9 => 'tháng mười',
 10 => 'tháng mười một',
 11 => 'tháng mười hai',
 ),
 'zh' => 
 array (
 0 => '一月',
 1 => '二月',
 2 => '三月',
 3 => '四月',
 4 => '五月',
 5 => '六月',
 6 => '七月',
 7 => '八月',
 8 => '九月',
 9 => '十月',
 10 => '十一月',
 11 => '十二月',
 ),
);
private static $dot_separated_languages = array (
 0 => 'ar',
 1 => 'en',
 2 => 'es',
 3 => 'ms',
 4 => 'ga',
 5 => 'hi',
 6 => 'iw',
 7 => 'jp',
 8 => 'ko',
 9 => 'mi',
 10 => 'mt',
 11 => 'ne',
 12 => 'si',
 13 => 'th',
 14 => 'tl',
 15 => 'ur',
 16 => 'zh',
);
public static $widget_date_format_locales = array (
 'en' => '%d %s ago|today|day|days|week|weeks|month|months|year|years',
 'af' => '%d %s gelede|vandag|dag|dae|week|weke|maand|maande|jaar|jaar',
 'ar' => '%d %s مضى|اليوم|يوم|أيام|أسبوع|أسابيع|شهر|أشهر|سنة|سنوات',
 'az' => '%d %s əvvəl|bu gün|gün|gün|həftə|həftə|ay|ay|il|il',
 'bg' => 'преди %d %s|днес|ден|дни|седмица|седмици|месец|месеца|година|години',
 'bn' => '%d %s আগে|আজ|দিন|দিন|সপ্তাহ|সপ্তাহ|মাস|মাস|বছর|বছর',
 'bs' => 'prije %d %s|danas|dan|dana|sedmicu|sedmica|mjesec|mjeseci|godinu|godina',
 'cs' => 'před %d %s|dnes|dnem|dny|týdnem|týdny|měsícem|měsíci|rokem|roky',
 'cy' => '%d %s yn ôl|heddiw|diwrnod|diwrnod|wythnos|wythnosau|mis|mis|flwyddyn|flynyddoedd',
 'da' => '%d %s siden|i dag|dag|dage|uge|uger|måned|måneder|år|år',
 'de' => 'vor %d %s|Heute|Tag|Tagen|Woche|Wochen|Monat|Monaten|Jahr|Jahren',
 'el' => 'πριν από %d ημέρα|σήμερα|ημέρα|ημέρες|εβδομάδα|εβδομάδες|μήνα|μήνες|χρόνο|χρόνια',
 'es' => 'hace %d %s|hoy|día|días|semana|semanas|mes|meses|año|años',
 'et' => '%d %s tagasi|täna|päev|päeva|nädal|nädalat|kuu|kuud|aasta|aastat',
 'fa' => '%d %s قبل|امروز|روز|روز|هفته|هفته|ماه|ماه|سال|سال',
 'fi' => '%d %s sitten|tänään|päivä|päivää|viikko|viikkoa|kuukausi|kuukautta|vuosi|vuotta',
 'fr' => 'il y a %d %s|aujourd\'hui|jour|jours|semaine|semaines|mois|mois|année|ans',
 'gd' => '%d %s air ais|an diugh|latha|làithean|seachdain|seachdainean|mìos|mìosan|bliadhna|bliadhna',
 'gl' => 'hai %d %s|hoxe|día|días|semana|semanas|mes|meses|ano|anos',
 'he' => '%d לפני %s|היום|יום|ימים|שבוע|שבועות|חודש|חודשים|שנה|שנים',
 'hi' => '%d %s पहले|आज|दिन|दिन|सप्ताह|सप्ताह|महीने|महीने|वर्ष|वर्ष',
 'hr' => 'prije %d %s|danas|dan|dana|tjedan|tjedana|mjesec|mjeseci|godinu|godina',
 'hu' => '%d %s|ma|napja|napja|hete|hete|hónapja|hónapja|éve|éve',
 'hy' => '%d %s առաջ|այսօր|օր|օր|շաբաթ|շաբաթ|ամիս|ամիս|տարի|տարի',
 'id' => '%d %s lalu|hari ini|hari|hari yang|minggu|minggu yang|bulan|bulan yang|tahun|tahun yang',
 'is' => 'fyrir %d %s|í dag|degi|dögum|viku|vikum|mánuði|mánuðum|ári|árum',
 'it' => '%d %s fa|oggi|giorno|giorni|settimana|settimane|mese|mesi|anno|anni',
 'ja' => '%d %s 前|今日|日|日|週間|週間|か月|か月|年|年',
 'ka' => '%d %s წინ|დღეს|დღის|დღის|კვირის|კვირის|თვის|თვის|წლის|წლის',
 'kk' => '%d %s бұрын|бүгін|күн|күн|апта|апта|ай|ай|жыл|жыл',
 'ko' => '%d %s 전|오늘|일|일|주|주|월|월|년|년',
 'lt' => 'prieš %d %s|šiandien|dieną|dienų|savaitę|savaites|mėnesį|mėnesių|metų|metų',
 'mk' => 'пред %d %s|денес|ден|дена|недела|недели|месец|месеци|година|години',
 'ms' => '%d %s lalu|hari ini|hari|hari|minggu|minggu|bulan|bulan|tahun|tahun',
 'nl' => '%d %s geleden|vandaag|dag|dagen|week|weken|maand|maanden|jaar|jaar',
 'no' => '%d %s siden|i dag|dag|dager|uke|uker|måned|måneder|år|år',
 'pl' => '%d %s temu|dziś|dzień|dni|tydzień|tygodni|miesiąc|miesięcy|rok|lat',
 'pt' => '%d %s atrás|hoje|dia|dias|semana|semanas|mês|meses|ano|anos',
 'ro' => 'acum %d %s|astăzi|zi|zile|săptămână|săptămâni|lună|luni|an|ani',
 'ru' => '%d %s назад|сегодня|день|дней|неделю|недель|месяц|месяцев|год|лет',
 'sk' => 'pred %d %s|dnes|dňom|dňami|týždňom|týždňami|mesiacom|mesiacmi|rokom|rokmi',
 'sl' => 'pred %d %s|danes|dnevom|dnevi|tednom|tedni|mesecem|meseci|letom|leti',
 'sq' => '%d %s më parë|sot|ditë|ditë|javë|javë|muaj|muaj|vit|vit',
 'sr' => 'пре %d %s|данас|дан|дана|недељу|недеље|месец|месеци|године|година',
 'sv' => '%d %s sedan|i dag|dag|dagar|vecka|veckor|månad|månader|år|år',
 'th' => '%d %s ที่แล้ว|วันนี้|วัน|วัน|สัปดาห์|สัปดาห์|เดือน|เดือน|ปี|ปี',
 'tr' => '%d %s önce|bugün|gün|gün|hafta|hafta|ay|ay|yıl|yıl',
 'uk' => '%d %s тому|сьогодні|день|днів|тиждень|тижнів|місяць|місяців|рік|років',
 'vi' => '%d %s trước|hôm nay|ngày|ngày|tuần|tuần|tháng|tháng|năm|năm',
 'zh' => '%d %s 前|今天|天|天|周|周|个月|个月|年|年',
);
public static $widget_top_rated_titles = array (
 'Apartment' => 
 array (
 'en' => 'Top Rated <br /> Apartment %date%',
 'af' => 'Topgegradeerde <br /> woonstel %date%',
 'ar' => 'شقة <br />الأعلى تقييمًا %date%',
 'az' => 'Ən Reytinqli <br /> Mənzil %date%',
 'bg' => 'Най-високо оценен <br /> апартамент %date%',
 'bn' => 'শীর্ষ রেটেড <br /> অ্যাপার্টমেন্ট %date%',
 'bs' => 'Najbolje ocijenjen <br /> stan %date%',
 'cs' => 'Nejlépe hodnocený <br /> apartmán %date%',
 'cy' => 'Fflat â\'r <br /> sgôr uchaf %date%',
 'da' => 'Bedst bedømte <br /> lejlighed %date%',
 'de' => 'Bestbewertete <br /> Wohnung %date%',
 'el' => 'Διαμέρισμα με κορυφαία <br /> βαθμολογία %date%',
 'es' => 'Apartamento mejor <br /> valorado %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> korter %date%',
 'fa' => 'آپارتمان با <br />رتبه برتر %date%',
 'fi' => 'Parhaiksi arvioitu <br /> huoneisto %date%',
 'fr' => 'Appartement le <br /> mieux noté %date%',
 'gd' => 'Apartment le <br /> inbhe as àirde %date%',
 'gl' => 'Apartamento mellor <br /> valorado %date%',
 'he' => 'דירה בדירוג <br />הגבוה ביותר %date%',
 'hi' => 'शीर्ष रेटेड <br /> अपार्टमेंट %date%',
 'hr' => 'Najbolje ocijenjen <br /> stan %date%',
 'hu' => 'Kiválóra értékelt <br /> apartman %date%',
 'hy' => 'Ամենաբարձր վարկանիշով <br /> բնակարան %date% թ',
 'id' => 'Apartemen dengan <br /> Nilai Tertinggi %date%',
 'is' => 'Íbúð með <br /> hæstu einkunn %date%',
 'it' => 'Appartamento più <br /> votato %date%',
 'ja' => 'トップ評価の <br />アパート%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> ბინა %date%',
 'kk' => 'Ең жоғары <br /> бағаланған пәтер %date%',
 'ko' => '%date%년 최고 <br /> 등급 아파트',
 'lt' => 'Geriausiai įvertintas <br /> %date% m. butas',
 'mk' => 'Највисоко оценет <br /> стан за %date% година',
 'ms' => 'Pangsapuri <br /> Tertinggi %date%',
 'nl' => 'Best beoordeelde <br /> appartement %date%',
 'no' => 'Topprangert <br /> leilighet %date%',
 'pl' => 'Najwyżej oceniane <br /> mieszkanie %date%',
 'pt' => 'Apartamento mais <br /> bem avaliado em %date%',
 'ro' => 'Cel mai bine <br /> cotat apartament %date%',
 'ru' => 'Самый рейтинговый <br /> Квартира %date% года',
 'sk' => 'Najlepšie hodnotený <br /> apartmán v roku %date%',
 'sl' => 'Najbolje ocenjeno <br /> stanovanje %date%',
 'sq' => 'Apartament me vlerësim <br /> më të lartë %date%',
 'sr' => 'Најбоље оцењен <br /> стан %date%',
 'sv' => 'Topprankad <br /> lägenhet %date%',
 'th' => 'อพาร์ทเมนต์ <br /> ติดอันดับยอดนิยมปี %date%',
 'tr' => 'En Çok Oy Alan <br /> Daire %date%',
 'uk' => 'Квартира з найвищим <br /> рейтингом %date%',
 'vi' => 'Căn hộ được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 公寓 %date%',
 ),
 'Bar' => 
 array (
 'en' => 'Top Rated <br /> Bar %date%',
 'af' => 'Topgegradeerde <br /> kroeg %date%',
 'ar' => 'الشريط <br />الأعلى تقييمًا لعام %date%',
 'az' => 'Ən Reytinqli <br /> Bar %date%',
 'bg' => 'Най-високо оценен <br /> бар %date%',
 'bn' => 'শীর্ষ রেটযুক্ত <br /> বার %date%',
 'bs' => 'Najbolje ocijenjeni <br /> bar %date%',
 'cs' => 'Nejlépe hodnocený <br /> bar %date%',
 'cy' => 'Bar â\'r <br /> sgôr uchaf %date%',
 'da' => 'Bedst bedømte <br /> bar %date%',
 'de' => 'Bestbewertete <br /> Bar %date%',
 'el' => 'Μπαρ με κορυφαία <br /> βαθμολογία %date%',
 'es' => 'Barra mejor <br /> valorada %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> baar %date%',
 'fa' => 'بار با <br />رتبه برتر %date%',
 'fi' => 'Parhaiksi arvioitu <br /> baari %date%',
 'fr' => 'Barre la <br /> mieux notée %date%',
 'gd' => 'Bàr le <br /> inbhe as àirde %date%',
 'gl' => 'Bar mellor <br /> valorado %date%',
 'he' => 'בר <br />המדורג מוביל %date%',
 'hi' => 'टॉप रेटेड <br /> बार %date%',
 'hr' => 'Najbolje ocijenjeni <br /> bar %date%',
 'hu' => 'Kiválóra értékelt <br /> bár %date%',
 'hy' => 'Ամենաբարձր վարկանիշ <br /> ունեցող բար %date% թ',
 'id' => 'Bar dengan <br /> Nilai Tertinggi %date%',
 'is' => 'Hæsta einkunn <br /> bar %date%',
 'it' => 'Bar più <br /> votato del %date%',
 'ja' => 'トップ評価の <br />バー%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> ბარი %date%',
 'kk' => 'Ең жоғары <br /> бағаланған жолақ %date%',
 'ko' => '%date%년 최고 <br /> 등급 바',
 'lt' => 'Geriausiai įvertintas <br /> %date% m. baras',
 'mk' => 'Најдобро оценет <br /> бар %date% година',
 'ms' => 'Bar Penilaian <br /> Tertinggi %date%',
 'nl' => 'Best beoordeelde <br /> staaf %date%',
 'no' => 'Topprangert <br /> bar %date%',
 'pl' => 'Najwyżej oceniany <br /> bar %date%',
 'pt' => 'Barra mais <br /> bem avaliado em %date%',
 'ro' => 'Cel mai bine <br /> cotat bar %date%',
 'ru' => 'Самый рейтинговый <br /> бар %date% года',
 'sk' => 'Najlepšie hodnotený <br /> bar %date%',
 'sl' => 'Najbolje ocenjen <br /> bar %date%',
 'sq' => 'Bar me vlerësim <br /> më të lartë %date%',
 'sr' => 'Најбоље оцењени <br /> бар %date%',
 'sv' => 'Topprankad <br /> bar %date%',
 'th' => 'บาร์ <br /> เรทสูงสุด %date%',
 'tr' => 'En Çok Oy Alan <br /> Çubuk %date%',
 'uk' => 'Бар з найвищим <br /> рейтингом %date%',
 'vi' => 'Thanh được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 酒吧 %date%',
 ),
 'Cafe' => 
 array (
 'en' => 'Top Rated <br /> Cafe %date%',
 'af' => 'Topgegradeerde <br /> kafee %date%',
 'ar' => 'المقهى <br />الأعلى تقييمًا لعام %date%',
 'az' => 'Ən Reytinqli <br /> Kafe %date%',
 'bg' => 'Най-високо оценено <br /> кафене %date% г',
 'bn' => 'শীর্ষ রেটেড <br /> ক্যাফে %date%',
 'bs' => 'Najbolje ocijenjen <br /> kafić %date%',
 'cs' => 'Nejlépe hodnocená <br /> kavárna %date%',
 'cy' => 'Caffi o\'r <br /> Radd Flaenaf %date%',
 'da' => 'Bedst bedømte <br /> cafe %date%',
 'de' => 'Bestbewertetes <br /> Café %date%',
 'el' => 'Κορυφαία βαθμολογία <br /> Cafe %date%',
 'es' => 'Café mejor <br /> valorado %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> kohvik %date%',
 'fa' => 'کافه با <br />رتبه برتر %date%',
 'fi' => 'Parhaiksi arvioitu <br /> kahvila %date%',
 'fr' => 'Café le <br /> mieux noté %date%',
 'gd' => 'Cafaidh le <br /> inbhe as àirde %date%',
 'gl' => 'Café mellor <br /> valorado %date%',
 'he' => 'בית קפה עם הדירוג <br />הגבוה ביותר %date%',
 'hi' => 'टॉप रेटेड <br /> कैफ़े %date%',
 'hr' => 'Najbolje ocijenjeni <br /> kafić %date%',
 'hu' => 'Kiválóra értékelt <br /> kávézó %date%',
 'hy' => 'Ամենաբարձր վարկանիշ <br /> ունեցող Սրճարան %date%',
 'id' => 'Kafe dengan <br /> Nilai Tertinggi %date%',
 'is' => 'Kaffihús með <br /> hæstu einkunn %date%',
 'it' => 'Il caffè più <br /> votato del %date%',
 'ja' => 'トップ評価の <br />カフェ%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> კაფე %date%',
 'kk' => 'Ең жоғары <br /> бағаланған кафе %date%',
 'ko' => '%date%년 최고 <br /> 평점 카페',
 'lt' => 'Geriausiai įvertinta <br /> %date% m. kavinė',
 'mk' => 'Најдобро оценет <br /> Кафе %date% година',
 'ms' => 'Kafe <br /> Tertinggi %date%',
 'nl' => 'Best beoordeeld <br /> café %date%',
 'no' => 'Topprangert <br /> kafé %date%',
 'pl' => 'Najwyżej oceniana <br /> kawiarnia %date%',
 'pt' => 'Café mais <br /> bem avaliado em %date%',
 'ro' => 'Cel mai bine <br /> cotat cafe %date%',
 'ru' => 'Самый рейтинговый <br /> Кафе %date% года',
 'sk' => 'Najlepšie hodnotená <br /> kaviareň v roku %date%',
 'sl' => 'Najbolje ocenjena <br /> kavarna %date%',
 'sq' => 'Kafeneja më e <br /> vlerësuar %date%',
 'sr' => 'Најбоље оцењени <br /> кафић %date%',
 'sv' => 'Topprankade <br /> kafé %date%',
 'th' => 'คาเฟ่ <br /> ยอดนิยมปี %date%',
 'tr' => 'En Çok Oy Alan <br /> Kafe %date%',
 'uk' => 'Кафе з найвищим <br /> рейтингом %date% року',
 'vi' => 'Quán cà phê được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 咖啡厅 %date%',
 ),
 'Clinic' => 
 array (
 'en' => 'Top Rated <br /> Clinic %date%',
 'af' => 'Topgegradeerde <br /> kliniek %date%',
 'ar' => 'العيادة <br />الأعلى تقييمًا لعام %date%',
 'az' => 'Ən Reytinqli <br /> Klinika %date%',
 'bg' => 'Най-високо оценена <br /> клиника %date% г',
 'bn' => 'শীর্ষ রেটেড <br /> ক্লিনিক %date%',
 'bs' => 'Najbolje ocijenjena <br /> klinika %date%',
 'cs' => 'Nejlépe hodnocená <br /> klinika %date%',
 'cy' => 'Clinig â\'r <br /> sgôr uchaf %date%',
 'da' => 'Bedst bedømte <br /> klinik %date%',
 'de' => 'Bestbewertete <br /> Klinik %date%',
 'el' => 'Κλινική με κορυφαία <br /> βαθμολογία %date%',
 'es' => 'Clínica mejor <br /> valorada %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> kliinik %date%',
 'fa' => 'کلینیک با <br />رتبه برتر %date%',
 'fi' => 'Parhaiksi arvioitu <br /> klinikka %date%',
 'fr' => 'Clinique la <br /> mieux notée %date%',
 'gd' => 'Clionaig le <br /> inbhe as àirde %date%',
 'gl' => 'Clínica mellor <br /> valorada %date%',
 'he' => 'המרפאה <br />המדורגת ביותר %date%',
 'hi' => 'शीर्ष रेटेड <br /> क्लिनिक %date%',
 'hr' => 'Najbolje ocijenjena <br /> klinika %date%',
 'hu' => 'Kiválóra értékelt <br /> klinika %date%',
 'hy' => 'Ամենաբարձր վարկանիշ <br /> ունեցող կլինիկա %date%',
 'id' => 'Klinik dengan <br /> Nilai Tertinggi %date%',
 'is' => 'Hæstu einkunnir <br /> heilsugæslustöðvar %date%',
 'it' => 'Clinica più <br /> votata %date%',
 'ja' => 'トップ評価の <br />クリニック%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> კლინიკა %date% წელი',
 'kk' => 'Ең жоғары <br /> бағаланған клиника %date%',
 'ko' => '%date%년 최고 <br /> 등급 클리닉',
 'lt' => 'Geriausiai įvertinta <br /> klinika %date% m',
 'mk' => 'Најдобро оценета <br /> клиника %date% година',
 'ms' => 'Klinik <br /> Tertinggi %date%',
 'nl' => 'Best beoordeelde <br /> kliniek %date%',
 'no' => 'Topprangert <br /> klinikk %date%',
 'pl' => 'Najwyżej oceniana <br /> klinika %date%',
 'pt' => 'Clínica mais <br /> bem avaliado em %date%',
 'ro' => 'Cel mai bine <br /> cotat clinică %date%',
 'ru' => 'Самый рейтинговый <br /> Клиника %date% года',
 'sk' => 'Najlepšie hodnotená <br /> klinika %date%',
 'sl' => 'Najbolje ocenjena <br /> klinika %date%',
 'sq' => 'Klinika më e <br /> vlerësuar %date%',
 'sr' => 'Најбоље оцењена <br /> клиника %date%',
 'sv' => 'Topprankad <br /> klinik %date%',
 'th' => 'คลินิก <br /> ยอดนิยมปี %date%',
 'tr' => 'En Çok Oy Alan <br /> Klinik %date%',
 'uk' => 'клініка з найвищим <br /> рейтингом %date%',
 'vi' => 'Phòng khám được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 诊所 %date%',
 ),
 'Hotel' => 
 array (
 'en' => 'Top Rated <br /> Hotel %date%',
 'af' => 'Topgegradeerde <br /> hotel %date%',
 'ar' => 'الفندق <br />الأعلى تقييمًا لعام %date%',
 'az' => 'Ən Reytinqli <br /> Otel %date%',
 'bg' => 'Най-високо оценен <br /> хотел %date% г',
 'bn' => 'শীর্ষ রেটেড <br /> হোটেল %date%',
 'bs' => 'Najbolje ocijenjeni <br /> hotel %date%',
 'cs' => 'Nejlépe hodnocený <br /> hotel %date%',
 'cy' => 'Gwesty â\'r <br /> sgôr uchaf %date%',
 'da' => 'Bedst bedømte <br /> hotel %date%',
 'de' => 'Bestbewertetes <br /> Hotel %date%',
 'el' => 'Ξενοδοχείο με κορυφαία <br /> βαθμολογία %date%',
 'es' => 'Hotel mejor <br /> valorado %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> hotell %date%',
 'fa' => 'هتل با <br />رتبه برتر %date%',
 'fi' => 'Parhaiksi arvioitu <br /> hotelli %date%',
 'fr' => 'Hôtel le <br /> mieux noté en %date%',
 'gd' => 'Taigh-òsta aig an <br /> ìre as àirde %date%',
 'gl' => 'Hotel mellor <br /> valorado en %date%',
 'he' => 'המלון בעל הדירוג <br />הגבוה ביותר לשנת %date%',
 'hi' => 'शीर्ष रेटेड <br /> होटल %date%',
 'hr' => 'Najbolje ocijenjeni <br /> hotel %date%',
 'hu' => 'Kiválóra értékelt <br /> szálláshely %date%',
 'hy' => 'Ամենաբարձր վարկանիշ <br /> ունեցող հյուրանոց %date%',
 'id' => 'Hotel dengan <br /> Nilai Tertinggi %date%',
 'is' => 'Hæsta einkunn <br /> hótel %date%',
 'it' => 'Hotel con le migliori <br /> valutazioni nel %date%',
 'ja' => 'トップ評価の <br />ホテル%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> სასტუმრო %date%',
 'kk' => 'Ең жоғары <br /> бағаланған қонақ үй %date%',
 'ko' => '%date%년 최고 <br /> 등급 호텔',
 'lt' => 'Geriausiai įvertintas <br /> viešbutis %date% m',
 'mk' => 'Највисоко оценет <br /> хотел за %date% година',
 'ms' => 'Hotel Penilaian <br /> Teratas %date%',
 'nl' => 'Best beoordeelde <br /> hotel %date%',
 'no' => 'Topprangert <br /> hotell %date%',
 'pl' => 'Najwyżej oceniany <br /> hotel %date%',
 'pt' => 'Hotéis mais <br /> bem avaliado em %date%',
 'ro' => 'Cel mai bine <br /> cotat hotel %date%',
 'ru' => 'Самый рейтинговый <br /> отель %date% года',
 'sk' => 'Najlepšie hodnotený <br /> hotel v roku %date%',
 'sl' => 'Najbolje ocenjen <br /> hotel %date%',
 'sq' => 'Hoteli më i <br /> vlerësuar %date%',
 'sr' => 'Најбоље оцењен <br /> хотел %date%',
 'sv' => 'Topprankade <br /> hotell %date%',
 'th' => 'โรงแรม <br /> ยอดนิยมปี %date%',
 'tr' => 'En Çok Oy Alan <br /> Otel %date%',
 'uk' => 'готель з найвищим <br /> рейтингом %date%',
 'vi' => 'Khách sạn được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 酒店 %date%',
 ),
 'Provider' => 
 array (
 'en' => 'Top Rated <br /> Provider %date%',
 'af' => 'Topgegradeerde <br /> verskaffer %date%',
 'ar' => 'المزود <br />الأعلى تقييمًا لعام %date%',
 'az' => 'Ən Reytinqli <br /> Provayder %date%',
 'bg' => 'Най-високо оценен <br /> доставчик %date% г',
 'bn' => 'শীর্ষ রেট <br /> প্রদানকারী %date%',
 'bs' => 'Najbolje ocijenjeni <br /> provajder %date%',
 'cs' => 'Nejlépe hodnocený <br /> poskytovatel %date%',
 'cy' => 'Darparwr â\'r <br /> sgôr uchaf %date%',
 'da' => 'Bedst bedømte <br /> udbyder %date%',
 'de' => 'Bestbewerteter <br /> Anbieter %date%',
 'el' => 'Πάροχος με κορυφαία <br /> βαθμολογία %date%',
 'es' => 'Proveedor mejor <br /> valorado %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> teenusepakkuja %date%',
 'fa' => 'ارائه دهنده با <br />رتبه برتر %date%',
 'fi' => 'Parhaiksi arvioitu <br /> palveluntarjoaja %date%',
 'fr' => 'Fournisseur le <br /> mieux noté en %date%',
 'gd' => 'Solaraiche le <br /> inbhe as àirde %date%',
 'gl' => 'Provedor mellor <br /> valorado %date%',
 'he' => 'הספק <br />המדורג מוביל %date%',
 'hi' => 'शीर्ष रेटेड <br /> प्रदाता %date%',
 'hr' => 'Najbolje ocijenjeni <br /> pružatelj usluga %date%',
 'hu' => 'Kiválóra értékelt <br /> szolgáltató %date%',
 'hy' => 'Ամենաբարձր վարկանիշ <br /> ունեցող մատակարար %date%',
 'id' => 'Penyedia Nilai <br /> Tertinggi %date%',
 'is' => 'Hæst metinn <br /> veitandi %date%',
 'it' => 'Fornitore più <br /> votato %date%',
 'ja' => 'トップ評価の <br />プロバイダー%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> პროვაიდერი %date%',
 'kk' => 'Ең жоғары <br /> бағаланған провайдер %date%',
 'ko' => '%date%년 최고 <br /> 등급 제공업체',
 'lt' => 'Geriausiai įvertintas <br /> teikėjas %date% m',
 'mk' => 'Најдобро оценет <br /> провајдер за %date% година',
 'ms' => 'Penyedia Penilaian <br /> Tertinggi %date%',
 'nl' => 'Best beoordeelde <br /> aanbieder %date%',
 'no' => 'Topprangert <br /> leverandør %date%',
 'pl' => 'Najwyżej oceniany <br /> dostawca %date%',
 'pt' => 'Provedor mais <br /> bem avaliado em %date%',
 'ro' => 'Cel mai bine <br /> cotat furnizor %date%',
 'ru' => 'Самый рейтинговый <br /> провайдер %date% года.',
 'sk' => 'Najlepšie hodnotený <br /> poskytovateľ v roku %date%',
 'sl' => 'Najbolje ocenjeni <br /> ponudnik %date%',
 'sq' => 'Ofruesi më i <br /> vlerësuar për %date%',
 'sr' => 'Најбоље оцењени <br /> провајдер %date%',
 'sv' => 'Topprankad <br /> leverantör %date%',
 'th' => 'ผู้ให้บริการ <br /> ที่ได้รับคะแนนสูงสุดปี %date%',
 'tr' => 'En Çok Oy Alan <br /> Sağlayıcısı %date%',
 'uk' => 'постачальник з найвищим <br /> рейтингом %date%',
 'vi' => 'Nhà cung cấp được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 提供商 %date%',
 ),
 'Restaurant' => 
 array (
 'en' => 'Top Rated <br /> Restaurant %date%',
 'af' => 'Topgegradeerde <br /> restaurant %date%',
 'ar' => 'المطعم <br />الأعلى تقييمًا لعام %date%',
 'az' => 'Ən Reytinqli <br /> Restoran %date%',
 'bg' => 'Най-високо оценен <br /> ресторант %date% г',
 'bn' => 'শীর্ষ রেটিং <br /> রেস্তোরাঁ %date%',
 'bs' => 'Najbolje ocijenjen <br /> restoran %date%',
 'cs' => 'Nejlépe hodnocená <br /> restaurace %date%',
 'cy' => 'Bwyty â\'r <br /> sgôr uchaf %date%',
 'da' => 'Bedst bedømte <br /> restaurant %date%',
 'de' => 'Bestbewertetes <br /> Restaurant %date%',
 'el' => 'Εστιατόριο με κορυφαία <br /> βαθμολογία %date%',
 'es' => 'Restaurante mejor <br /> valorado %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> restoran %date%',
 'fa' => 'رستوران با <br />رتبه برتر %date%',
 'fi' => 'Parhaiksi arvioitu <br /> ravintola %date%',
 'fr' => 'Restaurant le <br /> mieux noté en %date%',
 'gd' => 'Taigh-bìdh leis an <br /> ìre as àirde %date%',
 'gl' => 'Restaurante mellor <br /> valorado en %date%',
 'he' => 'המסעדה <br />המדורגת ביותר %date%',
 'hi' => 'शीर्ष रेटेड <br /> रेस्तरां %date%',
 'hr' => 'Najbolje ocijenjeni <br /> restoran %date%',
 'hu' => 'Kiválóra értékelt <br /> étterem %date%',
 'hy' => 'Ամենաբարձր վարկանիշ <br /> ունեցող ռեստորան %date% թ',
 'id' => 'Restoran dengan <br /> Nilai Tertinggi %date%',
 'is' => 'Veitingastaður með <br /> hæstu einkunn %date%',
 'it' => 'Ristorante più <br /> votato %date%',
 'ja' => 'トップ評価の <br />レストラン%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> რესტორანი %date% წელი',
 'kk' => 'Ең жоғары <br /> бағаланған мейрамхана %date%',
 'ko' => '%date%년 최고 <br /> 평점 레스토랑',
 'lt' => 'Geriausiai įvertintas <br /> %date% m. restoranas',
 'mk' => 'Најдобро оценет <br /> ресторан %date% година',
 'ms' => 'Restoran Penarafan <br /> Tertinggi %date%',
 'nl' => 'Best beoordeelde <br /> restaurant %date%',
 'no' => 'Topprangert <br /> restaurant %date%',
 'pl' => 'Najwyżej oceniana <br /> restauracja %date%',
 'pt' => 'Restaurante mais <br /> bem avaliado em %date%',
 'ro' => 'Cel mai bine <br /> cotat restaurant %date%',
 'ru' => 'Самый рейтинговый <br /> Ресторан %date% года',
 'sk' => 'Najlepšie hodnotená <br /> reštaurácia v roku %date%',
 'sl' => 'Najbolje ocenjena <br /> restavracija %date%',
 'sq' => 'Restorant me vlerësim <br /> më të lartë %date%',
 'sr' => 'Најбоље оцењен <br /> ресторан %date%',
 'sv' => 'Topprankad <br /> restaurang %date%',
 'th' => 'ร้านอาหาร <br /> ยอดนิยมปี %date%',
 'tr' => 'En Çok Oy Alan <br /> Restoran %date%',
 'uk' => 'Ресторан з найвищим <br /> рейтингом %date% року',
 'vi' => 'Nhà hàng được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 餐厅 %date%',
 ),
 'Service' => 
 array (
 'en' => 'Top Rated <br /> Service %date%',
 'af' => 'Topgegradeerde <br /> diens %date%',
 'ar' => 'الخدمة <br />الأعلى تقييمًا لعام %date%',
 'az' => 'Ən Reytinqli <br /> Xidmət %date%',
 'bg' => 'Най-високо оценена <br /> услуга %date% г',
 'bn' => 'সেরা রেটেড <br /> পরিষেবা %date%',
 'bs' => 'Najbolje ocijenjena <br /> usluga %date%',
 'cs' => 'Nejlépe hodnocená <br /> služba %date%',
 'cy' => 'Gwasanaeth o\'r <br /> Radd Flaenaf %date%',
 'da' => 'Bedst bedømte <br /> service %date%',
 'de' => 'Bestbewerteter <br /> Service %date%',
 'el' => 'Υπηρεσία με κορυφαία <br /> βαθμολογία %date%',
 'es' => 'Servicio mejor <br /> valorado %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> teenus %date%',
 'fa' => 'خدمات با <br />رتبه برتر %date%',
 'fi' => 'Parhaiksi arvioitu <br /> palvelu %date%',
 'fr' => 'Service le <br /> mieux noté %date%',
 'gd' => 'Seirbheis aig an <br /> ìre as àirde %date%',
 'gl' => 'Servizo mellor <br /> valorado %date%',
 'he' => 'השירות <br />המדורג ביותר %date%',
 'hi' => 'सर्वोच्च रेटेड <br /> सेवा %date%',
 'hr' => 'Najbolje ocijenjena <br /> usluga %date%',
 'hu' => 'Kiválóra értékelt <br /> szolgáltatás %date%',
 'hy' => 'Ամենաբարձր վարկանիշ <br /> ունեցող ծառայություն %date%',
 'id' => 'Layanan Nilai <br /> Tertinggi %date%',
 'is' => 'Hæsta einkunn <br /> þjónusta %date%',
 'it' => 'Servizio più <br /> votato %date%',
 'ja' => 'トップ評価の <br />サービス%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> სერვისი %date%',
 'kk' => 'Ең жоғары <br /> бағаланған қызмет %date%',
 'ko' => '%date%년 최고 <br /> 평점 서비스',
 'lt' => 'Geriausiai įvertinta <br /> %date% m. paslauga',
 'mk' => 'Најдобро оценет <br /> сервис за %date% година',
 'ms' => 'Perkhidmatan <br /> Tertinggi %date%',
 'nl' => 'Best beoordeelde <br /> service %date%',
 'no' => 'Topprangert <br /> tjeneste %date%',
 'pl' => 'Najwyżej oceniana <br /> usługa %date%',
 'pt' => 'Serviço mais <br /> bem avaliado em %date%',
 'ro' => 'Cel mai bine <br /> cotat serviciu %date%',
 'ru' => 'Самый рейтинговый <br /> сервис %date% года',
 'sk' => 'Najlepšie hodnotená <br /> služba %date%',
 'sl' => 'Najbolje ocenjena <br /> storitev %date%',
 'sq' => 'Shërbimi më i <br /> vlerësuar %date%',
 'sr' => 'Најбоље оцењена <br /> услуга %date%',
 'sv' => 'Topprankad <br /> tjänst %date%',
 'th' => 'บริการ <br /> ยอดนิยมปี %date%',
 'tr' => 'En Çok Oy Alan <br /> Hizmet %date%',
 'uk' => 'сервіс з найвищим <br /> рейтингом %date%',
 'vi' => 'Dịch vụ được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 服务 %date%',
 ),
 'Webshop' => 
 array (
 'en' => 'Top Rated <br /> Webshop %date%',
 'af' => 'Topgegradeerde <br /> webwinkel %date%',
 'ar' => 'متجر الويب <br />الأعلى تقييمًا لعام %date%',
 'az' => 'Ən Reytinqli <br /> Veb Mağaza %date%',
 'bg' => 'Най-високо оценен <br /> уеб магазин %date% г',
 'bn' => 'সেরা রেটেড <br /> ওয়েবশপ %date%',
 'bs' => 'Najbolje ocijenjena <br /> web trgovina %date%',
 'cs' => 'Nejlépe hodnocený <br /> internetový obchod roku %date%',
 'cy' => 'Gwefan â\'r <br /> sgôr uchaf %date%',
 'da' => 'Bedst bedømte <br /> webshop %date%',
 'de' => 'Bestbewerteter <br /> Webshop %date%',
 'el' => 'Κορυφαία βαθμολογία <br /> webshop %date%',
 'es' => 'Tienda web mejor <br /> valorada %date%',
 'et' => 'Kõrgeimalt hinnatud <br /> veebipood %date%',
 'fa' => 'متجر الويب <br />الأعلى تقييمًا لعام %date%',
 'fi' => 'Parhaiksi arvioitu <br /> verkkokauppa %date%',
 'fr' => 'Boutique en ligne la <br /> mieux notée en %date%',
 'gd' => 'Bùth lìn aig an <br /> ìre as àirde %date%',
 'gl' => 'Tenda web mellor <br /> valorada %date%',
 'he' => 'חנות אינטרנט <br />בדירוג הגבוה ביותר %date%',
 'hi' => 'टॉप रेटेड <br /> वेबशॉप %date%',
 'hr' => 'Najbolje ocijenjeni <br /> webshop %date%',
 'hu' => 'Kiválóra értékelt <br /> webáruház %date%',
 'hy' => 'Ամենաբարձր վարկանիշ <br /> ունեցող վեբ խանութ %date% թ',
 'id' => 'Toko Web dengan <br /> Nilai Tertinggi %date%',
 'is' => 'Vefverslun með <br /> hæstu einkunn %date%',
 'it' => 'Il negozio online più <br /> votato del %date%',
 'ja' => 'トップ評価の <br />ウェブショップ%date%',
 'ka' => 'ყველაზე რეიტინგული <br /> ვებ მაღაზია %date%',
 'kk' => 'Ең жоғары <br /> бағаланған веб-дүкен %date%',
 'ko' => '%date%년 최고 <br /> 평점 웹숍',
 'lt' => 'Geriausiai įvertinta <br /> internetinė parduotuvė %date% m',
 'mk' => 'Најдобро оценет <br /> веб-продавница %date% година',
 'ms' => 'Kedai Web Penilaian <br /> Tertinggi %date%',
 'nl' => 'Best beoordeelde <br /> webshop %date%',
 'no' => 'Topprangert <br /> nettbutikk %date%',
 'pl' => 'Najwyżej oceniany <br /> sklep internetowy %date%',
 'pt' => 'Loja virtual mais <br /> bem avaliada em %date%',
 'ro' => 'Cel mai bine <br /> cotat magazin web %date%',
 'ru' => 'Самый рейтинговый <br /> Интернет-магазин %date% года',
 'sk' => 'Najlepšie hodnotený <br /> internetový obchod v roku %date%',
 'sl' => 'Najbolje ocenjena <br /> spletna trgovina %date%',
 'sq' => 'Dyqani në internet më i <br /> vlerësuar për %date%',
 'sr' => 'Најбоље оцењена <br /> веб продавница %date%',
 'sv' => 'Topprankad <br /> webbshop %date%',
 'th' => 'เว็บช็อป <br /> ยอดนิยมประจำปี %date%',
 'tr' => 'En Çok Oy Alan <br /> İnternet Mağazası %date%',
 'uk' => 'веб-магазину з найвищим <br /> рейтингом %date%',
 'vi' => 'Webshop được xếp <br /> hạng hàng đầu năm %date%',
 'zh' => '评分最高的 <br /> 网店 %date%',
 ),
);
public static $widget_reply_by_texts = array (
 'en' => 'Owner\'s reply',
 'af' => 'Antwoord deur eienaar',
 'ar' => 'الرد من قبل المالك',
 'az' => 'Sahibi tərəfindən cavab',
 'bg' => 'Отговор от собственика',
 'bn' => 'মালিক দ্বারা উত্তর',
 'bs' => 'Odgovor vlasnika',
 'cs' => 'Odpověď majitele',
 'cy' => 'Ateb gan y perchennog',
 'da' => 'Svar fra ejer',
 'de' => 'Antwort des Eigentümers',
 'el' => 'Απάντηση από τον ιδιοκτήτη',
 'es' => 'Respuesta del propietario',
 'et' => 'Vastus omanikult',
 'fa' => 'پاسخ توسط مالک',
 'fi' => 'Vastaus omistajalta',
 'fr' => 'Réponse du propriétaire',
 'gd' => 'Freagairt leis an t-sealbhadair',
 'gl' => 'Resposta do propietario',
 'he' => 'תשובה מאת הבעלים',
 'hi' => 'स्वामी द्वारा उत्तर',
 'hr' => 'Odgovor vlasnika',
 'hu' => 'Válasz a tulajdonostól',
 'hy' => 'Պատասխանել սեփականատիրոջ կողմից',
 'id' => 'Balasan dari pemilik',
 'is' => 'Svar frá eiganda',
 'it' => 'Risposta dal proprietario',
 'ja' => 'オーナーからの返信',
 'ka' => 'პასუხი მფლობელის მიერ',
 'kk' => 'Иесінің жауабы',
 'ko' => '소유자의 답변',
 'lt' => 'Atsakymas iš savininko',
 'mk' => 'Одговор од сопственикот',
 'ms' => 'Balas oleh pemilik',
 'nl' => 'Antwoord van eigenaar',
 'no' => 'Svar fra eier',
 'pl' => 'Odpowiedź właściciela',
 'pt' => 'Resposta do proprietário',
 'ro' => 'Răspunsul proprietarului',
 'ru' => 'Ответ владельца',
 'sk' => 'Odpoveď od vlastníka',
 'sl' => 'Odgovor lastnika',
 'sq' => 'Përgjigje nga pronari',
 'sr' => 'Одговор власника',
 'sv' => 'Svar från ägaren',
 'th' => 'ตอบโดยเจ้าของ',
 'tr' => 'Sahibinden cevap',
 'uk' => 'Відповідь власника',
 'vi' => 'Trả lời của chủ sở hữu',
 'zh' => '版主回覆',
);
private static $page_urls = array (
 'facebook' => 'https://www.facebook.com/%page_id%',
 'google' => 'https://www.google.com/maps/search/?api=1&query=Google&query_place_id=%page_id%',
 'tripadvisor' => 'https://www.tripadvisor.com/%page_id%',
 'yelp' => 'https://www.yelp.com/biz/%25page_id%25',
 'booking' => 'https://www.booking.com/hotel/%page_id%',
 'amazon' => 'https://www.amazon.%domain%/sp?seller=%page_id%',
 'arukereso' => 'https://www.arukereso.hu/stores/%page_id%/#velemenyek',
 'airbnb' => 'https://www.airbnb.com/rooms/%page_id%',
 'hotels' => 'https://hotels.com/%page_id%',
 'opentable' => 'https://www.opentable.com/%page_id%',
 'foursquare' => 'https://foursquare.com/v/%25page_id%25',
 'capterra' => 'https://www.capterra.%page_id%/reviews',
 'szallashu' => 'https://szallas.hu/%page_id%?#rating',
 'thumbtack' => 'https://www.thumbtack.com/%page_id%',
 'expedia' => 'https://www.expedia.com/%page_id%',
 'zillow' => 'https://www.zillow.com/profile/%page_id%/#reviews',
 'wordpressPlugin' => 'https://www.wordpress.org/plugins/%page_id%',
 'aliexpress' => 'https://www.aliexpress.com/store/%page_id%',
 'alibaba' => 'https://%page_id%.en.alibaba.com',
 'sourceForge' => 'https://sourceforge.net/software/product/%page_id%/',
 'ebay' => 'https://www.ebay.com/fdbk/feedback_profile/%page_id%',
);
public function getPageUrl()
{
if (!isset(self::$page_urls[ $this->getShortName() ])) {
return "";
}
$pageDetails = $this->getPageDetails();
if (!$pageDetails) {
return "";
}
$pageId = $pageDetails['id'];
$domain = "";

$url = str_replace([ '%domain%', '%page_id%', '%25page_id%25' ], [ $domain, $pageId, $pageId ], self::$page_urls[ $this->getShortName() ]);
if ($this->getGoogleType($pageId) === 'shop') {
$url = 'https://customerreviews.google.com/v/merchant?q=' . $pageId;
}

return $url;
}
private function getDefaultCompanyAvatarUrl()
{
return 'https://cdn.trustindex.io/companies/default_avatar.jpg';
}

private function getGoogleType($pageId)
{
return preg_match('/&c=\w+&v=\d+/', $pageId) ? 'shop' : 'map';
}
private function getReviewPageUrl()
{
$pageDetails = $this->getPageDetails();
if (!$pageDetails) {
return "";
}
$pageId = $pageDetails['id'];
if ($this->getGoogleType($pageId) === 'shop') {
return 'https://customerreviews.google.com/v/merchant?q=' . $pageId;
}
else {
return 'http://search.google.com/local/reviews?placeid=' . $pageId;
}
}
private function getReviewWriteUrl()
{
$pageDetails = $this->getPageDetails();
if (!$pageDetails) {
return "";
}
$pageId = $pageDetails['id'];
if ($this->getGoogleType($pageId) === 'shop') {
return 'https://customerreviews.google.com/v/merchant?q=' . $pageId;
}
else {
return 'https://admin.trustindex.io/api/googleWriteReview?place-id=' . $pageId;
}
}
public function getReviewHtml($review)
{
$html = $review->text;
if ($review->text) {
$html = $this->parseReviewText($review->text);
}
if (isset($review->highlight) && $review->highlight) {
$tmp = explode(',', $review->highlight);
$start = (int)$tmp[0];
$length = (int)$tmp[1];
$html = mb_substr($html, 0, $start) . '<mark class="ti-highlight">' . mb_substr($html, $start, $length) . '</mark>' . mb_substr($html, $start + $length, mb_strlen($html));
/* format <mark></mark> tags in other tags
 * like:
 * <strong><mark>...</strong>...</mark>....
 * to:
 * <strong><mark>...</mark></strong><mark>...</mark>....
 */
preg_match('/<mark class="ti-highlight">(.*)<\/mark>/Us', $html, $matches);
if (isset($matches[1])) {
$replaced_content = preg_replace('/(<\/?[^>]+>)/U', '</mark>$1<mark class="ti-highlight">', $matches[1]);
$html = str_replace($matches[0], '<mark class="ti-highlight">' . $replaced_content . '</mark>', $html);
}
}
return $html;
}
private function parseReviewText($text)
{
return preg_replace('/\r\n|\r|\n/', "\n", trim(html_entity_decode($text, ENT_HTML5 | ENT_QUOTES)));
}
private function getProfileImageUrl($imageUrl, $layoutId, $sizeMultiply = 1) {

$size = $this->getProfileImageSize($layoutId) * $sizeMultiply;
$imageUrl = preg_replace('/([=-])(?:s\d+|w\d+-h\d+)(-|$)/', "$1w$size-h$size$2", $imageUrl);
return $imageUrl;
}

private function getProfileImageSize($layoutId)
{
if (in_array($layoutId, [36,37,38,39,44])) {
return 64;
}
return 40;
}
private function getHeaderProfileImageSize($layoutId)
{
return 65;
}
public function renderWidgetFrontend($tiPublicId = null)
{
$this->enqueueLoaderScript();
if ($tiPublicId) {
$tiPublicId = preg_replace('/[^a-zA-Z0-9]/', '', $tiPublicId);
}
$preContent = "";
$attributes = ['data-src' => 'https://cdn.trustindex.io/loader.js?'.$tiPublicId];
if (!$tiPublicId) {
$pageDetails = $this->getPageDetails();
$styleId = (int)$this->getWidgetOption('style-id');
if (self::is_amp_active() && self::is_amp_enabled()) {
return $this->frontEndErrorForAdmins(__('Free plugin features are unavailable with AMP plugin.', 'trustindex-plugin'));
}
if (self::$widget_templates['templates'][$styleId]['is-top-rated-badge'] && (float)$pageDetails['rating_score'] < self::$topRatedMinimumScore) {
$text = sprintf(__('Our exclusive "Top Rated" badge is awarded to service providers with a rating of %s and above.', 'trustindex-plugin'), self::$topRatedMinimumScore)
.'<br />'
.'<a href="'.admin_url('admin.php?page='.$this->get_plugin_slug().'/settings.php&tab=free-widget-configurator&step=2').'">'.__('Please select another widget', 'trustindex-plugin').'.</a>';
return $this->frontEndErrorForAdmins($text);
}
if ($reviews = $this->getReviewsForWidgetHtml()) {
$templateId = 'trustindex-'.$this->getShortName().'-widget-html';
$attributes['data-src'] .= 'wp-widget';
$attributes['data-template-id'] = $templateId;
$preContent = '<pre class="ti-widget" style="display: none"><template id="'.esc_attr($templateId).'">'.$this->getWidgetHtml($reviews);
$preContent = preg_replace('/<img (.*)src="([^"]+)"(.*)\/?>/U', '<trustindex-image $1data-imgurl="$2"$3></trustindex-image>', $preContent);
$preContent = str_replace('srcset="', 'data-imgurlset="', $preContent);
if (is_file($this->getCssFile()) && !get_option($this->get_option_name('load-css-inline'), 0)) {
$attributes['data-css-url'] = $this->getCssUrl().'?'.filemtime($this->getCssFile());
} else {
$preContent .= '<style type="text/css">'.get_option($this->get_option_name('css-content')).'</style>';
}
$preContent .= '</template></pre>';
} else {
$text = sprintf(__('There are no reviews on your %s platform.', 'trustindex-plugin'), ucfirst($this->getShortName()));

return $this->frontEndErrorForAdmins($text);
}
}
$attributesHtml = implode(' ', array_map(function($attribute, $value) {
return esc_attr($attribute).'="'.esc_attr($value).'"';
}, array_keys($attributes), $attributes));
return $preContent.'<div '.$attributesHtml.'></div>';
}
public function renderWidgetAdmin($isDemoReviews = false, $isForceDemoReviews = false, $previewData = null)
{
$this->widgetOptionDefaultOverride = [];
if ($previewData) {
$this->widgetOptions['style-id'] = $previewData['style-id'];
$this->widgetOptions['scss-set'] = $previewData['set-id'];
$this->widgetOptions['review-content'] = "";
$fileName = $previewData['style-id'].'-'.$previewData['set-id'].'.css';
wp_enqueue_style('trustindex-widget-preview-'.$fileName, "https://cdn.trustindex.io/assets/widget-presetted-css/v2/$fileName");
if (isset($previewData['verified-by-trustindex']) && $previewData['verified-by-trustindex']) {
$this->widgetOptionDefaultOverride['verified-by-trustindex'] = 1;
}
}
$reviews = $this->getReviewsForWidgetHtml(true, $isForceDemoReviews, (bool)$previewData);
if (!$reviews) {
return self::get_alertbox('error', __('You do not have reviews with the current filters. <br />Change your filters if you would like to display reviews on your page!', 'trustindex-plugin'));
}
$html = $this->getWidgetHtml($reviews, (bool)$previewData);
if (!$previewData) {
if (is_file($this->getCssFile()) && !$this->isElementorEditing()) {
wp_enqueue_style('trustindex-widget-editor', $this->getCssUrl(), [], filemtime($this->getCssFile()));
} else {
$html .= '<style type="text/css">'.get_option($this->get_option_name('css-content')).'</style>';
}
}
if ($this->isElementorEditing()) {
$html .= '<script type="text/javascript" src="https://cdn.trustindex.io/loader.js"></script>';
} else {
$this->enqueueLoaderScript();
}
return $html;
}
public function renderNameFormat($name, $formatId = null)
{
if (!$formatId) {
$formatId = $this->getWidgetOption('nameformat');
}
if (!isset(self::$widget_nameformats[$formatId])) {
return $name;
}
$format = self::$widget_nameformats[$formatId];
if (!$format['regex']) {
return $name;
}
$result = preg_replace($format['regex'], '$1 $2', $name);
if ($format['ucfirst']) {
$result = ucfirst($result);
}
if ($format['toupper']) {
$result = function_exists('mb_strtoupper') ? mb_strtoupper($result) : strtoupper($result);
}
return $result;
}
public function enqueueLoaderScript()
{
if (wp_script_is('trustindex-loader-js', 'registered')) {
wp_enqueue_script('trustindex-loader-js');
} else {
wp_enqueue_script('trustindex-loader-js', 'https://cdn.trustindex.io/loader.js', [], null, [
'strategy' => 'async',
'in_footer' => true,
]);
}
}
private $templateCache = null;
private function getWidgetHtml($reviews, $isPreview = false)
{
$styleId = (int)$this->getWidgetOption('style-id');
$setId = $this->getWidgetOption('scss-set');
$content = $this->getWidgetOption('review-content');
$language = $this->getWidgetOption('lang', false, $isPreview);
if (!$content || strpos($content, '<!-- R-LIST -->') === false) {
if (!$this->templateCache) {
add_action('http_api_curl', function($handle) {
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
}, 10);
$response = wp_remote_get("https://cdn.trustindex.io/widget-assets/template/v2/$language.json", [ 'timeout' => 300 ]);
if (is_wp_error($response)) {
return $this->frontEndErrorForAdmins(__('Could not download the template for the widget.<br />Please reload the page.<br />If the problem persists, please write an email to support@trustindex.io.', 'trustindex-plugin') .'<br /><br />'. print_r($response, true));
}
$this->templateCache = json_decode($response['body'], true);
}
$content = $this->templateCache[$styleId];
if (!$isPreview) {
update_option($this->get_option_name('review-content'), $content, false);
}
}
$content = $this->parseWidgetHtml($reviews, $content, $isPreview);
$content = preg_replace('/data-set[_-]id=[\'"][^\'"]*[\'"]/m', 'data-set-id="'. $setId .'"', $content);
$classAppends = ['ti-' . substr($this->getShortName(), 0, 4)];
if (!$this->getWidgetOption('show-reviewers-photo', false, $isPreview)) {
$classAppends []= 'ti-no-profile-img';
}
if ($this->getWidgetOption('disable-font', false, $isPreview)) {
$classAppends []= 'ti-disable-font';
}
if (!$this->getWidgetOption('show-arrows', false, $isPreview)) {
$classAppends []= 'ti-disable-nav';
}
if (!$this->getWidgetOption('enable-animation', false, $isPreview)) {
$classAppends []= 'ti-disable-animation';
}
if ($this->isFomoWidget()) {
$classAppends []= 'ti-fomo-align-'.$this->getWidgetOption('align', false, $isPreview);
if (!$this->getWidgetOption('fomo-border', false, $isPreview)) {
$classAppends []= 'ti-hide-fomo-border';
}
if (!$this->getWidgetOption('fomo-arrow', false, $isPreview)) {
$classAppends []= 'ti-hide-fomo-arrow';
}
} else {
if (!$this->getWidgetOption('no-rating-text', false, $isPreview)) {
$classAppends []= 'ti-show-rating-text';
}
$classAppends []= 'ti-review-text-mode-'.$this->getWidgetOption('review-text-mode', false, $isPreview);
$classAppends []= 'ti-'.(in_array($styleId, [36, 37, 38, 39]) ? 'content' : 'text').'-align-'.$this->getWidgetOption('align', false, $isPreview);
}
$content = str_replace('" data-layout-id=', ' '. implode(' ', $classAppends) .'" data-no-translation="true" data-layout-id=', $content);
if ($this->isRtlLanguage()) {
$content = str_replace('" data-layout-id=', '" data-rotate-to="left" data-layout-id=', $content);
}
if ($this->getWidgetOption('dateformat', false, $isPreview) === 'modern') {
$language = $this->getWidgetOption('lang', false, $isPreview);
$content = str_replace('" data-layout-id=', '" data-time-locale="'. self::$widget_date_format_locales[$language] .'" data-layout-id=', $content);
}
if ($this->isFomoWidget()) {
$styleText = '--fomo-color:'.$this->getWidgetOption('fomo-color', false, $isPreview).';';
if ('right' === $this->getWidgetOption('align', false, $isPreview)) {
$styleText .= 'left: auto;right: '.$this->getWidgetOption('fomo-margin', false, $isPreview).'px;';
} else {
$styleText .= 'left: '.$this->getWidgetOption('fomo-margin', false, $isPreview).'px;';
}
$content = str_replace('" data-layout-id=', '" style="'.$styleText.'" data-layout-id=', $content);
}
return $content;
}
private function getReviewsForWidgetHtml($isDemoReviews = false, $isForceDemoReviews = false, $isPreview = false)
{
global $wpdb;
$sqlRatingField = 'rating';
if ($this->is_ten_scale_rating_platform()) {
$sqlRatingField = 'ROUND(rating / 2, 0)';
}
$sql = 'SELECT *, rating AS original_rating, '. $sqlRatingField .' AS rating FROM `'. $this->get_tablename('reviews') .'` ';
$filter = $this->getWidgetOption('filter', false, $isPreview);
if (isset($filter['stars']) && count($filter['stars']) === 0) {
$sql .= 'WHERE 0 ';
} else {
$sql .= 'WHERE hidden = 0 AND ('. $sqlRatingField .' IN ('. implode(',', $filter['stars']) .')';
if (in_array(5, $filter['stars'])) {
$sql .= ' OR rating IS NULL';
}
$sql .= ') ';
if (isset($filter['only-ratings']) && $filter['only-ratings']) {
$sql .= 'AND text != "" ';
}
}
$sql .= 'ORDER BY date DESC';
$reviews = $wpdb->get_results($sql);
if ($isDemoReviews && ($isForceDemoReviews || !$reviews)) {
if (!$reviews && !$isForceDemoReviews && $wpdb->get_results('SELECT * FROM `'. $this->get_tablename('reviews') .'`')) {
return [];
}
$pageDetails = $this->getPageDetails();
$lang = substr(get_locale(), 0, 2);
if (!isset(self::$widget_languages[$lang])) {
$lang = 'en';
}
if (!$pageDetails) {
$pageDetails = [];
}
if (!isset($pageDetails['avatar_url'])) {
$pageDetails['avatar_url'] = 'https://cdn.trustindex.io/companies/default_avatar.jpg';
}
$ratingNum = 127;
$pageDetails['rating_number'] = $ratingNum;
$scoreTmp = round((($ratingNum - 1) * 5 + 4) / ($ratingNum * 5) * 10, 1);
if ($this->is_ten_scale_rating_platform()) {
$pageDetails['rating_score'] = number_format($scoreTmp, 1);
}
else {
$pageDetails['rating_score'] = number_format($scoreTmp / 2, 1);
}
if (!isset($pageDetails['id'])) {
$pageDetails['id'] = 'hu';
}
if (!isset($pageDetails['name'])) {
$pageDetails['name'] = get_bloginfo('name');
}
$this->pageDetails = $pageDetails;
$reviews = $this->getRandomReviews(10);
}
return $reviews;
}
private function parseWidgetHtml($reviews, $content, $isPreview = false)
{
$pageDetails = $this->getPageDetails();
$styleId = (int)$this->getWidgetOption('style-id');
$setId = $this->getWidgetOption('scss-set');
$language = $this->getWidgetOption('lang', false, $isPreview);
$widgetTemplate = self::$widget_templates['templates'][$styleId];
$showStars = $this->getWidgetOption('show-stars', false, $isPreview);
preg_match('/<!-- R-LIST -->(.*)<!-- R-LIST -->/', $content, $matches);
if (isset($matches[1])) {
$reviewContent = "";
if ($reviews) {
foreach ($reviews as $r) {
$customAttributes = 'data-empty="'. (empty($r->text) ? 1 : 0) .'"';
$date = "&nbsp;";
if ($r->date && $r->date !== '0000-00-00') {
$dateformat = $this->getWidgetOption('dateformat', false, $isPreview);
if (in_array($dateformat, [ 'hide', 'modern' ])) {
$date = '';
if ($dateformat === 'modern') {
$customAttributes .= ' data-time="'. strtotime($r->date) .'"';
}
}
else {
$date = str_replace(self::$widget_month_names['en'], self::$widget_month_names[$language], date($dateformat, strtotime($r->date)));
}
}
$ratingContent = $this->get_rating_stars($r->rating, $showStars);
if ($showStars) {

if ($this->is_ten_scale_rating_platform()) {
$ratingContent .= '<span class="ti-ten-rating-score">'. $this->formatTenRating($r->original_rating, $language) .'</span>';
}
}
$platformName = ucfirst($this->getShortName());
if ($this->getWidgetOption('verified-icon', false, $isPreview)) {
$verifiedIconTooltipText = self::$widget_verified_texts[$language];
$verifiedIconClass = 'ti-verified-review';
if (!in_array($platformName, self::$verified_platforms)) {
$verifiedIconClass = 'ti-verified-review ti-verified-platform';
$verifiedIconTooltipText = str_replace('%platform%', 'PLATFORM_NAME', self::$widget_verified_platform_texts[$language]);
}
$ratingContent .= '<span class="'.$verifiedIconClass.'"><span class="ti-verified-tooltip">'.$verifiedIconTooltipText.'</span></span>';
}
if (!$this->getWidgetOption('show-reviewers-photo', false, $isPreview)) {
$matches[1] = preg_replace('/<div class="ti-profile-img">.+<\/div>/U', '', $matches[1]);
}
$text = $this->getReviewHtml($r);
if ($r->reply && $this->getWidgetOption('show-review-replies', false, $isPreview)) {
$text .= '<br /><br /><strong class="ti-reply-by-owner-title">'.self::$widget_reply_by_texts[$language].'</strong><br />'.$this->parseReviewText($r->reply);
}
$reviewContent .= str_replace([
'%platform%',
'%reviewer_photo% 2x',
'%reviewer_photo%',
'%reviewer_name%',
'%created_at%',
'%text%',
'%rating_score%',
'class="ti-review-item',
'<!-- STARS-CONTENT -->',
], [
$platformName,
$this->getProfileImageUrl($r->user_photo, $styleId, 2).' 2x',
$this->getProfileImageUrl($r->user_photo, $styleId),
$this->renderNameFormat($r->user),
$date,
$text,
round($r->original_rating),
$customAttributes . ' class="ti-review-item',
$ratingContent,
], $matches[1]);
$reviewContent = str_replace('<div></div>', '', $reviewContent);
}
}
$content = str_replace($matches[0], $reviewContent, $content);
}
$ratingCount = $pageDetails['rating_number'];
$ratingScore = $pageDetails['rating_score'];
if (empty($ratingCount)) {
$ratingCount = count($reviews);
}
if (empty($ratingScore)) {
$ratingSum = 0.0;
foreach ($reviews as $review) {
$ratingSum += (float)$review->rating;
}
$c = count($reviews);
$ratingScore = $c ? $ratingSum / $c : 0;
}
$ratingText = $this->get_rating_text($ratingScore, $language);
$ratingTextUcfirst = ucfirst(strtolower($ratingText));
if (function_exists('mb_strtolower')) {
$ratingTextUcfirst = mb_substr($ratingText, 0, 1, 'UTF-8') . mb_strtolower(mb_substr($ratingText, 1, null, 'UTF-8'));
}
$imageUrl = isset($pageDetails['avatar_url']) && $pageDetails['avatar_url'] ? $pageDetails['avatar_url'] : $this->getDefaultCompanyAvatarUrl();
$image2xUrl = $imageUrl;

$size = $this->getHeaderProfileImageSize($styleId);
$imageUrl = preg_replace('/([=-])(s\d+|w\d+-h\d+)/', "$1w$size-h$size", $imageUrl);
$size *= 2;
$image2xUrl = preg_replace('/([=-])(s\d+|w\d+-h\d+)/', "$1w$size-h$size", $imageUrl);
$profileImageListForButton = "";
if ($reviews) {
$max = $widgetTemplate['type'] === 'fomo' ? 3 : 5;
for ($i = 0; $i < min(count($reviews), $max); $i++) {
$profileImageListForButton .= '
<div class="ti-profile-img">
<img
src="'.esc_url($this->getProfileImageUrl($reviews[$i]->user_photo, $styleId)).'"
srcset="'.esc_url($this->getProfileImageUrl($reviews[$i]->user_photo, $styleId, 2)).' 2x"
alt="'.esc_attr($reviews[$i]->user).'"
loading="lazy"
/>
</div>';
}
}
if ($widgetTemplate['type'] === 'fomo') {
$ratingText = $ratingTextUcfirst;
}
$fiveStarRatingNumber = 0;
if (isset($pageDetails['rating_numbers'])) {
$fiveStarRatingNumber = $pageDetails['rating_numbers'][5];
}
$fiveStarRatingNumberLast = 0;
if (isset($pageDetails['rating_numbers_last'])) {
$fiveStarRatingNumberLast = $pageDetails['rating_numbers_last'][5];
}
$content = str_replace([
'%platform%',
'%site_name%',
"5STAR_RATING_NUMBER_LAST",
"5STAR_RATING_NUMBER",
"RATING_NUMBER",
"RATING_SCORE",
"RATING_SCALE",
"RATING_TEXT",
"Rating_Text",
"PLATFORM_URL_LOGO 2x",
"PLATFORM_URL_LOGO",
"PLATFORM_NAME",
'<!-- STARS-CONTENT -->',
'PLATFORM_SMALL_LOGO',
'PLATFORM_SMALL_ICON',
'<div class="ti-profile-images"></div>',
], [
ucfirst($this->getShortName()),
$pageDetails['name'],
$fiveStarRatingNumberLast,
$fiveStarRatingNumber,
$ratingCount,
number_format((float)$ratingScore, 1),
$this->is_ten_scale_rating_platform() ? 10 : 5,
$ratingText,
$ratingTextUcfirst,
$image2xUrl.' 2x',
$imageUrl,
$this->get_platform_name($this->getShortName(), $pageDetails['id']),
$this->get_rating_stars($this->is_ten_scale_rating_platform() ? $ratingScore / 2 : $ratingScore, $showStars),
'<div class="ti-small-logo"><img src="'.$this->get_plugin_file_url('static/img/platform/logo.svg').'" alt="'.ucfirst($this->getShortName()).'" width="150" height="25" loading="lazy"></div>',
'<img class="ti-platform-icon" src="https://cdn.trustindex.io/assets/platform/'.ucfirst($this->getShortName()).'/icon.svg" alt="'.ucfirst($this->getShortName()).'" width="20" height="20" loading="lazy" />',
'<div class="ti-profile-images">'.$profileImageListForButton.'</div>',
], $content);
if (!in_array($widgetTemplate['type'], [ 'button', 'badge', 'top-rated-badge', 'fomo' ]) && !$this->getWidgetOption('show-logos', false, $isPreview)) {
$content = preg_replace('/<img class="ti-platform-icon".+>/U', '', $content);
}
if ($this->is_ten_scale_rating_platform() && $styleId === 11) {
$content = str_replace('<span class="ti-rating">'. $ratingScore .'</span> ', '', $content);
}
if (in_array($styleId, [8, 10, 11, 12, 13, 20, 22, 24, 25, 26, 27, 28, 29, 35, 55, 56, 57, 58, 59, 60, 61, 62, 106, 107, 109, 110, 111, 113, 115]) || $widgetTemplate['type'] === 'fomo') {
if ($widgetTemplate['type'] === 'fomo' && !$this->getWidgetOption('fomo-link', false, $isPreview)) {
$content = preg_replace('/<a href=[\'"]%footer_link%[\'"][^>]*>(.+)<\/a>/mU', '<div class="ti-header">$1</div>', $content);
} else {
if (!$this->getWidgetOption('show-header-button', false, $isPreview)) {
$content = preg_replace('/<!-- HEADER-BUTTON-START.+HEADER-BUTTON-END -->/s', '', $content);
}
$content = str_replace(['<!-- HEADER-BUTTON-START', 'HEADER-BUTTON-END -->'], '', $content);

$content = str_replace('%footer_link%', in_array($styleId, [8, 13, 26]) ? $this->getReviewWriteUrl() : $this->getReviewPageUrl(), $content);
}
} else {
$content = preg_replace('/<a href=[\'"]%footer_link%[\'"][^>]*>(.+)<\/a>/mU', '$1', $content);
}
if ($widgetTemplate['type'] === 'fomo') {
if (!$this->getWidgetOption('fomo-open', false, $isPreview)) {
$content = str_replace('class="ti-widget-container"', 'class="ti-widget-container ti-header-closed"', $content);
}
$iconType = $this->getWidgetOption('fomo-icon', false, $isPreview);
$iconHtml = '<div class="ti-fomo-icon" data-type="'.$iconType.'"></div>';
$platformIconHtml = '<img class="ti-platform-icon" src="https://cdn.trustindex.io/assets/platform/'.ucfirst($this->getShortName()).'/icon.svg" alt="'.ucfirst($this->getShortName()).'" width="20" height="20" loading="lazy">';
if ('profile-images' === $iconType) {
$iconHtml = '<div class="ti-profile-images">'.$profileImageListForButton.'</div>';
} else if ('platform' === $iconType) {
$iconHtml = $platformIconHtml;
}
$content = preg_replace('/<div class="ti-icon">\s*<\/div>/', '<div class="ti-icon">'.$iconHtml.'</div>', $content);
$platformBlockText = '';
if ($widgetTemplate['params']['fomo-platform-block'] && 'platform' !== $iconType) {
$platformBlockText = $platformIconHtml;
}
$content = str_replace('<!-- PLATFORM-ICON -->', $platformBlockText, $content);
}
if (!$this->getWidgetOption('reviews-load-more', false, $isPreview)) {
$content = preg_replace('/<div class="ti-load-more-reviews-container"[^>]*>.+<\/div>\s*<\/div>/U', '', $content);
}
if (in_array($styleId, [4, 6, 7, 15, 16, 19, 31, 33, 36, 37, 38, 39, 44]) && $this->getWidgetOption('no-rating-text', false, $isPreview)) {
if (in_array($styleId, [6, 7])) {
$content = preg_replace('/<div class="ti-footer">.*<\/div>/mU', '<div class="ti-footer"></div>', $content);
} else if(in_array($styleId, [31, 33])) {
$content = preg_replace('/<div class="ti-header source-.*<\/div>\s?<div class="ti-reviews-container">/mU', '<div class="ti-reviews-container">', $content);
} else {
$content = preg_replace('/<div class="ti-rating-text">.*<\/div>/mU', '', $content);
$content = preg_replace('/<div class="ti-footer">\s*<\/div>/m', '', $content);
}
}
if ($this->getWidgetOption('footer-filter-text', false, $isPreview) && (!in_array($widgetTemplate['type'], ['button', 'badge', 'floating', 'top-rated-badge', 'fomo']) || in_array($styleId, [23, 30, 32, 53]))) {
$filterText = $this->get_footer_filter_text($language);
if (!in_array($styleId, [5, 8, 9, 10, 13, 18, 23, 30, 31, 32, 33, 34, 53, 54]) && !$this->getWidgetOption('no-rating-text', false, $isPreview)) {
$content = str_replace('</span><!-- FOOTER FILTER TEXT -->', ',</span><span class="nowrap"><!-- FOOTER FILTER TEXT --></span>', $content);
$content = str_replace('<div class="ti-footer-filter-text"><!-- FOOTER FILTER TEXT --></div>', '', $content);
$content = str_replace('<!-- FOOTER FILTER TEXT -->', function_exists('mb_strtolower') ? mb_strtolower($filterText) : strtolower($filterText), $content);
} else {
$content = str_replace('<!-- FOOTER FILTER TEXT -->', $filterText, $content);
}
} else {
$content = str_replace([ '<div class="ti-footer-filter-text"><!-- FOOTER FILTER TEXT --></div>', '<!-- FOOTER FILTER TEXT -->' ], '', $content);
}
$verifiedByTrustindex = (int)$this->getWidgetOption('verified-by-trustindex', false, $isPreview);
if ($verifiedByTrustindex && $this->isVerifiedByTrustindexAvailable()) {
$content = str_replace('data-style="1"', 'data-style="'.$verifiedByTrustindex.'"', $content);
$content = str_replace('a=sys&c=verified-badge', 'a=sys&c=wp-verified-badge', $content);
$content = str_replace('<!-- VERIFIED BY TRUSTINDEX START', '', $content);
$content = str_replace('VERIFIED BY TRUSTINDEX END -->', '', $content);
} else {
$content = preg_replace('/<!-- VERIFIED BY TRUSTINDEX START.*VERIFIED BY TRUSTINDEX END -->/s', '', $content);
}
if (!in_array($styleId, [ 53, 54 ])) {
preg_match('/src="([^"]+logo[^\.]*\.svg)"/m', $content, $matches);
if (isset($matches[1]) && !empty($matches[1])) {
$content = str_replace($matches[0], $matches[0] . ' width="150" height="25"', $content);
$content = preg_replace('/width="([\d%]+)" height="([\d%]+)"( alt="[^"]+")? width="([\d%]+)" height="([\d%]+)"/', 'width="$1" height="$2"$3', $content);
}
}
if ($widgetTemplate['is-top-rated-badge']) {
$topRatedDate = $this->getWidgetOption('top-rated-date', false, $isPreview);
$topRatedType = $this->getWidgetOption('top-rated-type', false, $isPreview);
$date = date('Y');
if ($topRatedDate === 'last-year') {
$date = date('Y') - 1;
} else if ($topRatedDate === 'hide' || $widgetTemplate['type'] === 'fomo') {
$date = '';
}
$title = trim(str_replace('%date%', $date, self::$widget_top_rated_titles[$topRatedType][$language]));
if (in_array($styleId, [97, 98, 104]) || $widgetTemplate['type'] === 'fomo') {
$title = str_replace('<br />', '', $title);
}
$title = '<div class="ti-top-rated-title">'.$title.'</div>';
$regex = '/<div class="ti-top-rated-title">.*<\/div>/mU';
if ($widgetTemplate['type'] === 'fomo') {
$title = '<div class="ti-title-text">'.$title.'</div>';
$regex = '/<div class="ti-title-text">.*<\/div>/mU';
}
$content = preg_replace($regex, $title, $content);
$content = str_replace('a=sys&c=top-rated-badge', 'a=sys&c=wp-top-rated-badge', $content);
}
return $content;
}
public function isVerifiedByTrustindexAvailable()
{
$pageDetails = $this->getPageDetails();
$styleId = (int)$this->getWidgetOption('style-id');
$widgetTemplate = self::$widget_templates['templates'][$styleId];
return $this->isLayoutHasReviews() && !in_array($widgetTemplate['type'], ['floating']) && !$widgetTemplate['is-top-rated-badge'] && (float)$pageDetails['rating_score'] >= self::$topRatedMinimumScore;
}
public function isLayoutHasReviews()
{
$styleId = (int)$this->getWidgetOption('style-id');
return !in_array(self::$widget_templates['templates'][$styleId]['type'], ['button', 'badge', 'top-rated-badge', 'fomo']) || in_array($styleId, [23, 30, 32]);
}
public function isFomoWidget()
{
$styleId = (int)$this->getWidgetOption('style-id');
return self::$widget_templates['templates'][$styleId]['type'] === 'fomo';
}
public function isRtlLanguage()
{
if (in_array($this->getWidgetOption('lang'), array (
 0 => 'ar',
 1 => 'he',
 2 => 'fa',
))) {
return true;
}
return false;
}
public function get_footer_filter_text($lang = 'en')
{
$filter = $this->getWidgetOption('filter');
$langExists = self::$widget_footer_filter_texts && isset(self::$widget_footer_filter_texts[ $lang ]);
$text = $langExists ? self::$widget_footer_filter_texts[ $lang ]['latest'] : 'Showing our latest reviews';
if (isset($filter['stars']) && count($filter['stars']) < 4) {
sort($filter['stars']);
$start = $filter['stars'][0];
$end = array_pop($filter['stars']);
if ($start == $end) {
$replace = $start;
}
else {
$replace = $start .'-'. $end;
}
$text = str_replace('RATING_STAR_FILTER', $replace, $langExists ? self::$widget_footer_filter_texts[ $lang ]['star'] : 'Showing only RATING_STAR_FILTER star reviews');
}
return $text;
}
public function get_platform_name($type, $id = "")
{
$text = ucfirst($type);
if ($text === 'Szallashu') {
$domains = [
'cz' => 'Hotely.cz',
'hu' => 'Szallas.hu',
'ro' => 'Hotelguru.ro',
'com' => 'Revngo.com',
'pl' => 'Noclegi.pl'
];
$tmp = explode('/', $id);
if (isset($domains[ $tmp[0] ])) {
$text = $domains[ $tmp[0] ];
}
}
else if ($text === 'Arukereso') {
$domains = [
'hu' => 'Árukereső.hu',
'bg' => 'Pazaruvaj.com',
'ro' => 'Compari.ro'
];
$tmp = explode('|', $id);
if (isset($domains[ $tmp[0] ])) {
$text = $domains[ $tmp[0] ];
}
}
else if($text === 'WordpressPlugin') {
$text = 'Wordpress Plugin';
}
return $text;
}
public function get_rating_text($rating, $lang = "en")
{
$texts = self::$widget_rating_texts[ $lang ];
$rating = round($rating);
if ($rating < 1) {
$rating = 1;
}
else if($rating > 5) {
$rating = 5;
}
if (function_exists('mb_strtoupper')) {
return mb_strtoupper($texts[ $rating - 1 ]);
}
else {
return strtoupper($texts[ $rating - 1 ]);
}
}
public function get_rating_stars($ratingScore, $platformStars = true)
{
$text = "";
if (!is_numeric($ratingScore)) {
return $text;
}
$platform = ucfirst($this->getShortName());
$altPlatform = $platform;
if (!$platformStars) {
$platform = 'Trustindex';
}
$fullStarUrl = '<img class="ti-star" src="https://cdn.trustindex.io/assets/platform/'.$platform.'/star/f.svg" alt="'.$altPlatform.'" width="17" height="17" loading="lazy" />';
for ($si = 1; $si <= $ratingScore; $si++) {
$text .= $fullStarUrl;
}
$fractional = $ratingScore - floor($ratingScore);
if(0.25 <= $fractional) {
if ($fractional < 0.75) {
$text .= preg_replace('/f(\.svg)?"/', 'h$1"', $fullStarUrl);
}
else {
$text .= $fullStarUrl;
}
$si++;
}
for (; $si <= 5; $si++) {
$text .= preg_replace('/f(\.svg)?"/', 'e$1"', $fullStarUrl);
}
return $text;
}
private function getRandomReviews($count = 9)
{
$exampleReviews = null;
$jsonFile = plugin_dir_path($this->plugin_file_path) . 'static' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'example-reviews.json';
if (file_exists($jsonFile)) {
$exampleReviews = json_decode(file_get_contents($jsonFile), true);
if (is_array($exampleReviews)) {
foreach ($exampleReviews as $i => $tmp) {
$exampleReviews[ $i ]['image'] = $this->get_plugin_file_url('static/img/'. $tmp['image']);
}
}
}
$reviews = [];
foreach ($exampleReviews as $i => $exampleReview) {
if ($i >= $count) {
break;
}
$r = new stdClass();
$r->id = $i;
$r->user = $exampleReview['name'];
$r->user_photo = $exampleReview['image'];
$r->text = $exampleReview['text'];
$r->original_rating = $i == max(0, $count-2) ? 4 : 5;
$r->rating = $r->original_rating;
$r->highlight = null;
$r->date = date('Y-m-d', strtotime('-'. ($i * 2) .' days'));
$r->reviewId = null;
$r->reply = null;
if ($this->is_ten_scale_rating_platform()) {
$r->original_rating = number_format($i == max(0, $count-2) ? 8 : 10, 1);
$r->rating = round($r->original_rating / 2);
}
$reviews[] = $r;
$i++;
}
return $reviews;
}
public function get_plugin_current_version()
{
add_action('http_api_curl', function($handle) {
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
}, 10);
$response = wp_remote_get('https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]='. $this->get_plugin_slug());
if (is_wp_error($response)) {
return false;
}
$json = json_decode($response['body'], true);
if (!$json || !isset($json['version'])) {
return false;
}
return $json['version'];
}


public function post_request($url, $args)
{
$response = wp_remote_post($url, $args);
if (is_wp_error($response)) {
echo $this->get_alertbox('error', '<br />Error with wp_remote_post, error message: <br /><b>'. $response->get_error_message() .'</b>');
die;
}
return wp_remote_retrieve_body($response);
}


public function is_trustindex_connected()
{
return get_option($this->get_option_name("subscription-id"));
}
public function get_trustindex_widget_number()
{
$widgets = $this->get_trustindex_widgets();
$number = 0;
foreach ($widgets as $wc) {
$number += count($wc['widgets']);
}
return $number;
}
public function get_trustindex_widgets()
{
$widgets = array();
$trustindexSubscriptionId = $this->is_trustindex_connected();
if ($trustindexSubscriptionId) {
$response = wp_remote_get("https://admin.trustindex.io/" . "api/getWidgets?subscription_id=" . $trustindexSubscriptionId);
if ($response && !is_wp_error($response)) {
$widgets = json_decode($response['body'], true);
}
}
return $widgets;
}
public function connect_trustindex_api($postData, $mode = 'new')
{
$url = "https://admin.trustindex.io/" . "api/connectApi";
$postData['wp_info'] = $this->get_wp_details();
$serverOutput = $this->post_request($url, [
'body' => $postData,
'timeout' => 300,
'redirection' => '5',
'blocking' => true
]);
if ($serverOutput[0] !== '[' && $serverOutput[0] !== '{') {
$serverOutput = substr($serverOutput, strpos($serverOutput, '('));
$serverOutput = trim($serverOutput,'();');
}
$serverOutput = json_decode($serverOutput, true);
if ($serverOutput['success']) {
update_option( $this->get_option_name("subscription-id"), $serverOutput["subscription_id"]);
$GLOBALS['wp_object_cache']->delete( $this->get_option_name('subscription-id'), 'options' );
}
return $serverOutput;
}


public function register_tinymce_features()
{
if (!has_filter('mce_external_plugins', 'add_tinymce_buttons')) {
add_filter('mce_external_plugins', [ $this, 'add_tinymce_buttons' ]);
add_filter('mce_buttons', [ $this, 'register_tinymce_buttons' ]);
}
}
public function add_tinymce_buttons($pluginArray)
{
$pluginName = 'trustindex';
if (!isset($pluginArray[ $pluginName ])) {
$pluginArray[ $pluginName ] = $this->get_plugin_file_url('static/js/admin-editor.js');
}
wp_localize_script('jquery', 'ajax_object', [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ]);
return $pluginArray;
}
public function register_tinymce_buttons($buttons)
{
$buttonName = 'trustindex';
if (!in_array($buttonName, $buttons)) {
$buttons []= $buttonName;
}
return $buttons;
}


public function list_trustindex_widgets_ajax()
{
$ti_widgets = $this->get_trustindex_widgets();
if ($this->is_trustindex_connected()): ?>
<?php if ($ti_widgets): ?>
<h2><?php echo __('Your saved widgets', 'trustindex-plugin'); ?></h2>
<?php foreach ($ti_widgets as $wc): ?>
<p><strong><?php echo esc_html($wc['name']); ?>:</strong></p>
<p>
<?php foreach ($wc['widgets'] as $w): ?>
<a href="#" class="btn-copy-widget-id" data-ti-id="<?php echo esc_attr($w['id']); ?>">
<span class="dashicons dashicons-admin-post"></span>
<?php echo esc_html($w['name']); ?>
</a><br />
<?php endforeach; ?>
</p>
<?php endforeach; ?>
<?php else: ?>
<?php echo self::get_alertbox("warning",
__("You have no widget saved!", 'trustindex-plugin') . " "
. "<a target='_blank' href='" . "https://admin.trustindex.io/" . "widget'>". __("Let's go, create amazing widgets for free!", 'trustindex-plugin')."</a>"
); ?>
<?php endif; ?>
<?php else: ?>
<?php echo self::get_alertbox("warning",
__("You have not set up your Trustindex account yet!", 'trustindex-plugin') . " "
. sprintf(__("Go to <a href='%s'>plugin setup page</a> to complete the one-step setup guide and enjoy the full functionalization!", 'trustindex-plugin'), admin_url('admin.php?page='.$this->get_plugin_slug().'/settings.php&tab=advanced'))
); ?>
<?php endif;
wp_die();
}
public function trustindex_add_scripts($hook)
{
if ($hook === 'widgets.php') {
wp_enqueue_script('trustindex_script', $this->get_plugin_file_url('static/js/admin-widget.js'), [], $this->getVersion());
wp_enqueue_style('trustindex_style', $this->get_plugin_file_url('static/css/admin-widget.css'), [], $this->getVersion());
}
else if ($hook === 'post.php') {
wp_enqueue_style('trustindex_editor_style', $this->get_plugin_file_url('static/css/admin-editor.css'), [], $this->getVersion());
}
else {
$tmp = explode(DIRECTORY_SEPARATOR, $this->plugin_file_path);
$pluginSlug = preg_replace('/\.php$/', '', array_pop($tmp));
$tmp = explode('/', $hook);
$currentSlug = array_shift($tmp);
if ($pluginSlug === $currentSlug) {
if (file_exists($this->get_plugin_dir() . 'static' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'admin-page-settings.css')) {
wp_enqueue_style('trustindex_settings_style_'. $this->getShortName(), $this->get_plugin_file_url('static/css/admin-page-settings.css'), [], $this->getVersion());
}
if (file_exists($this->get_plugin_dir() . 'static' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin-page-settings-common.js')) {
wp_enqueue_script('trustindex_settings_script_common_'. $this->getShortName(), $this->get_plugin_file_url('static/js/admin-page-settings-common.js'), [], $this->getVersion());
}
if(file_exists($this->get_plugin_dir() . 'static' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin-page-settings-connect.js')) {
wp_enqueue_script('trustindex_settings_script_connect_'. $this->getShortName(), $this->get_plugin_file_url('static/js/admin-page-settings-connect.js'), [], $this->getVersion());
}
if (file_exists($this->get_plugin_dir() . 'static' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'spectrum.css')) {
wp_enqueue_style('trustindex_settings_spectrum_'. $this->getShortName(), $this->get_plugin_file_url('static/css/spectrum.css'), [], $this->getVersion());
}
if (file_exists($this->get_plugin_dir() . 'static' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'spectrum.js')) {
wp_enqueue_script('trustindex_settings_spectrum_'. $this->getShortName(), $this->get_plugin_file_url('static/js/spectrum.js'), [], $this->getVersion());
}
}
}
wp_register_script('trustindex_admin_notification', $this->get_plugin_file_url('static/js/admin-notification.js') );
wp_enqueue_script('trustindex_admin_notification');
wp_enqueue_style('trustindex_admin_notification', $this->get_plugin_file_url('static/css/admin-notification.css'), [], $this->getVersion());
}


public function get_plugin_details($pluginSlug = null)
{
if (!$pluginSlug) {
$pluginSlug = $this->get_plugin_slug();
}
$pluginReturn = false;
$wpRepoPlugins = '';
$wpResponse = '';
$wpVersion = get_bloginfo('version');
if ($pluginSlug && $wpVersion > 3.8) {
$args = [
'author' => 'Trustindex.io',
'fields' => [
'downloaded' => true,
'active_installs' => true,
'ratings' => true
]
];
$wpResponse = wp_remote_post(
'http://api.wordpress.org/plugins/info/1.0/',
[
'body' => [
'action' => 'query_plugins',
'request' => serialize((object) $args)
]
]
);
if (!is_wp_error($wpResponse)) {
$wpRepoResponse = unserialize(wp_remote_retrieve_body($wpResponse));
$wpRepoPlugins = $wpRepoResponse->plugins;
}
if ($wpRepoPlugins) {
foreach ($wpRepoPlugins as $pluginDetails) {
if ($pluginSlug === $pluginDetails->slug) {
$pluginReturn = $pluginDetails;
}
}
}
}
return $pluginReturn;
}
public function get_wp_details()
{
$data = [
'domain' => $_SERVER['SERVER_NAME'],
'current_theme' => [ 'slug' => get_template() ],
'themes' => [],
'plugins' => []
];
if (function_exists('wp_get_theme')) {
$theme = wp_get_theme();
}
else {
$theme = get_theme(get_current_theme());
}
$data['current_theme']['name'] = $theme['Name'];
$data['current_theme']['author'] = strip_tags($theme['Author']);
$data['current_theme']['version'] = $theme['Version'];
if (function_exists('wp_get_themes')) {
$themes = wp_get_themes();
}
else {
$themes = get_themes();
}
if ($themes) {
foreach ($themes as $slug => $theme) {
$data['themes'][] = [
'slug' => $theme['Template'],
'name' => $theme['Name'],
'author' => strip_tags($theme['Author']),
'version' => $theme['Version']
];
}
}
if (!function_exists('get_plugins')) {
require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php';
}
$plugins = get_plugins();
if ($plugins) {
foreach ($plugins as $slug => $plugin) {
$data['plugins'][] = [
'slug' => explode('/', $slug)[0],
'name' => $plugin['Name'],
'author' => strip_tags($plugin['Author']),
'version' => $plugin['Version']
];
}
}
return json_encode($data);
}


public function is_ten_scale_rating_platform()
{
return in_array($this->getShortName(), [ 'booking', 'hotels', 'foursquare', 'szallashu', 'expedia' ]);
}
public function formatTenRating($rating, $language = null)
{
if (!$language) {
$language = get_option($this->get_option_name('lang'), 'en');
}
if ($rating == 10) {
$rating = '10';
}
if (!in_array($language, self::$dot_separated_languages)) {
$rating = str_replace('.', ',', $rating);
}
return $rating;
}
public static function is_amp_active()
{
if (!function_exists('get_plugins')) {
require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php';
}
$amp_plugin_keys = [
'accelerated-mobile-pages/accelerated-moblie-pages.php',
'amp/amp.php'
];
foreach (get_plugins() as $key => $plugin) {
if (in_array($key, $amp_plugin_keys) && is_plugin_active($key)) {
return true;
}
}
return false;
}
public static function is_amp_enabled()
{
if (function_exists('amp_is_request')) {
return amp_is_request();
}
else if (function_exists('ampforwp_is_amp_endpoint')) {
return ampforwp_is_amp_endpoint();
}
else {
return false;
}
}
private function isElementorEditing()
{
return class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode();
}
public function filter_filesystem_method($method)
{
if ($method !== 'direct' && !defined('FS_METHOD')) {
return 'direct';
}
return $method;
}
public function isJson($str) {
$json = json_decode($str);
return $json && $str !== $json;
}
public function register_block_editor()
{
if (!class_exists('WP_Block_Type_Registry')) {
return;
}
if (!WP_Block_Type_Registry::get_instance()->is_registered('trustindex/block-selector')) {
wp_register_script('trustindex-block-editor', $this->get_plugin_file_url('static/block-editor/block-editor.js'), [ 'wp-blocks', 'wp-editor' ], true);
register_block_type('trustindex/block-selector', [ 'editor_script' => 'trustindex-block-editor' ]);
}
}
function is_widget_setted_up()
{
$result = [];
$activePlugins = get_option('active_plugins');
$platforms = $this->get_platforms();
foreach ($this->get_plugin_slugs() as $index => $slug) {
if (in_array($slug .'/'. $slug .'.php', $activePlugins)) {
$activePluginSlug = $slug;
$result[ $platforms[ $index ] ] = get_option('trustindex-'. $platforms[ $index ] .'-widget-setted-up', 0);
}
}
return [
'result' => $result,
'setup_url' => admin_url('admin.php?page='. $activePluginSlug .'/settings.php&tab=advanced') ."#trustindex-admin"
];
}
function init_restapi()
{
register_rest_route('trustindex/v1', '/get-widgets', [
'methods' => 'GET',
'callback' => [ $this, 'get_trustindex_widgets' ],
'permission_callback' => '__return_true'
]);
register_rest_route('trustindex/v1', '/setup-complete', [
'methods' => 'GET',
'callback' => [ $this, 'is_widget_setted_up' ],
'permission_callback' => '__return_true'
]);
}


private function getCdnVersionControl()
{
$data = get_option($this->get_option_name('cdn-version-control'), []);
if (!$data || $data['last-saved-at'] < time() + 60) {
$response = wp_remote_get('https://cdn.trustindex.io/version-control.json', [ 'timeout' => 60 ]);
if (!is_wp_error($response)) {
$data = array_merge($data, json_decode($response['body'], true));
}
$data['last-saved-at'] = time();
update_option($this->get_option_name('cdn-version-control'), $data, false);
}
return $data;
}
private function getCdnVersion($name = "")
{
$data = $this->getCdnVersionControl();
return isset($data[ $name ]) ? $data[ $name ] : "";
}
public function getVersion($name = "")
{
if (!$name) {
return $this->version;
}
$data = get_option($this->get_option_name('version-control'), []);
return isset($data[ $name ]) ? $data[ $name ] : "1.0";
}
private function updateVersion($name, $value)
{
$data = get_option($this->get_option_name('version-control'), []);
$data[ $name ] = $value;
return update_option($this->get_option_name('version-control'), $data, false);
}


public function get_tablename($name = "")
{
global $wpdb;
return $wpdb->prefix . 'trustindex_' . $this->getShortName() . '_' . $name;
}
public function is_table_exists($name = "")
{
global $wpdb;
$tableName = $this->get_tablename($name);
/*
check both actual name and lowercase name because LIKE is case sensitive in this query (unfortunately)
and there is a possibility that $wpdb->prefix is something like "JxdFg_"
(2024-08-23: "jxdfg_trustindex_google_reviews" table existed but this query returned false)
*/
return ($wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) || ($wpdb->get_var("SHOW TABLES LIKE '". strtolower($tableName) ."'") == strtolower($tableName));
}
}
?>
