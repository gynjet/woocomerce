<?php
defined('ABSPATH') or die('No script kiddies please!');
$autoUpdates = get_option('auto_update_plugins', []);
$pluginSlug = "wp-reviews-plugin-for-google/wp-reviews-plugin-for-google.php";
if (isset($_GET['auto_update'])) {
check_admin_referer('ti-auto-update');
if (!in_array($pluginSlug, $autoUpdates)) {
$autoUpdates []= $pluginSlug;
update_option('auto_update_plugins', $autoUpdates, false);
}
header('Location: admin.php?page=' . sanitize_text_field($_GET['page']) . '&tab=advanced');
exit;
}
if (isset($_GET['toggle_css_inline'])) {
check_admin_referer('ti-toggle-css');
$v = (int)$_GET['toggle_css_inline'];
update_option($pluginManagerInstance->get_option_name('load-css-inline'), $v, false);
if ($v && is_file($pluginManagerInstance->getCssFile())) {
unlink($pluginManagerInstance->getCssFile());
}
$pluginManagerInstance->handleCssFile();
header('Location: admin.php?page=' . sanitize_text_field($_GET['page']) . '&tab=advanced');
exit;
}
if (isset($_GET['delete_css'])) {
check_admin_referer('ti-delete-css');
if (is_file($pluginManagerInstance->getCssFile())) {
unlink($pluginManagerInstance->getCssFile());
}
$pluginManagerInstance->handleCssFile();
header('Location: admin.php?page=' . sanitize_text_field($_GET['page']) . '&tab=advanced');
exit;
}
if (isset($_POST['save-notification-email'])) {
check_admin_referer('ti-notification-email-save');
$type = strtolower(trim(sanitize_text_field($_POST['type'])));
$email = strtolower(trim(sanitize_text_field($_POST['save-notification-email'])));
$pluginManagerInstance->setNotificationParam($type, 'email', $email);
exit;
}
$yesIcon = '<span class="dashicons dashicons-yes-alt"></span>';
$noIcon = '<span class="dashicons dashicons-dismiss"></span>';
$pluginUpdated = ($pluginManagerInstance->get_plugin_current_version() <= "12.9");
$cssInline = get_option($pluginManagerInstance->get_option_name('load-css-inline'), 0);
$css = get_option($pluginManagerInstance->get_option_name('css-content'));
$tiSuccess = "";
if (isset($_COOKIE['ti-success'])) {
$tiSuccess = sanitize_text_field($_COOKIE['ti-success']);
setcookie('ti-success', '', time() - 60, "/");
}
$tiError = null;
$tiCommand = isset($_POST['command']) ? sanitize_text_field($_POST['command']) : null;
if (!in_array($tiCommand, [ 'connect', 'disconnect' ])) {
$tiCommand = null;
}
if ($tiCommand === 'connect') {
check_admin_referer('connect-reg_' . $pluginManagerInstance->get_plugin_slug());
$sanitizedEmail = sanitize_email($_POST['email']);
$sanitizedPassword = stripslashes(sanitize_text_field(htmlentities($_POST['password'], ENT_QUOTES)));
if ($sanitizedEmail && $sanitizedPassword) {
$serverOutput = $pluginManagerInstance->connect_trustindex_api([
'signin' => [
'username' => $sanitizedEmail,
'password' => html_entity_decode($sanitizedPassword),
],
'callback' => bin2hex(openssl_random_pseudo_bytes(10))
], 'connect');
if ($serverOutput['success']) {
setcookie('ti-success', 'connected', time() + 60, '/');
header('Location: #trustindex-admin');
exit;
}
else {
$tiError = __('Wrong e-mail or password!', 'trustindex-plugin');
}
}
else {
$tiError = __('You must provide a password and a valid e-mail!', 'trustindex-plugin');
}
}
else if ($tiCommand === 'disconnect') {
check_admin_referer('disconnect-reg_' . $pluginManagerInstance->get_plugin_slug());
delete_option($pluginManagerInstance->get_option_name('subscription-id'));
setcookie('ti-success', 'disconnected', time() + 60, '/');
header('Location: #trustindex-admin');
exit;
}
$trustindexSubscriptionId = $pluginManagerInstance->is_trustindex_connected();
$widgetNumber = $pluginManagerInstance->get_trustindex_widget_number();
?>
<h1 class="ti-header-title"><?php echo __('Advanced', 'trustindex-plugin'); ?></h1>
<div class="ti-box">
<div class="ti-box-header"><?php echo __('Notifications', 'trustindex-plugin'); ?></div>
<ul class="ti-troubleshooting-checklist">
<li>
<?php echo __('Review download available', 'trustindex-plugin'); ?>
<ul>
<li>
<?php
$isNotificationActive = !$pluginManagerInstance->getNotificationParam('review-download-available', 'hidden', false);
echo __('Notification', 'trustindex-plugin') .': '. ($isNotificationActive ? $yesIcon : $noIcon); ?>
<?php if ($isNotificationActive): ?>
<a href="?page=<?php echo sanitize_text_field($_GET['page']); ?>&tab=advanced&notification=review-download-available&action=hide" class="ti-btn ti-btn-loading-on-click"><?php echo __('Disable', 'trustindex-plugin'); ?></a>
<?php else: ?>
<a href="?page=<?php echo sanitize_text_field($_GET['page']); ?>&tab=advanced&notification=review-download-available&action=unhide" class="ti-btn ti-btn-loading-on-click"><?php echo __('Enable', 'trustindex-plugin'); ?></a>
<?php endif; ?>
</li>

</ul>
</li>
<li>
<?php echo __('Review download finished', 'trustindex-plugin'); ?>
<ul>
<li>
<?php
$isNotificationActive = !$pluginManagerInstance->getNotificationParam('review-download-finished', 'hidden', false);
echo __('Notification', 'trustindex-plugin') .': '. ($isNotificationActive ? $yesIcon : $noIcon); ?>
<?php if ($isNotificationActive): ?>
<a href="?page=<?php echo sanitize_text_field($_GET['page']); ?>&tab=advanced&notification=review-download-finished&action=hide" class="ti-btn ti-btn-loading-on-click"><?php echo __('Disable', 'trustindex-plugin'); ?></a>
<?php else: ?>
<a href="?page=<?php echo sanitize_text_field($_GET['page']); ?>&tab=advanced&notification=review-download-finished&action=unhide" class="ti-btn ti-btn-loading-on-click"><?php echo __('Enable', 'trustindex-plugin'); ?></a>
<?php endif; ?>
</li>
<li>
<div class="ti-notification-email">
<div class="ti-notice ti-notice-error">
<p><?php echo __('Invalid email', 'trustindex-plugin'); ?></p>
</div>
<div class="ti-inner">
<span><?php echo __('Send email notification to:', 'trustindex-plugin'); ?></span>
<input type="text" data-type="review-download-finished" placeholder="email@example.com" data-nonce="<?php echo wp_create_nonce('ti-notification-email-save'); ?>" class="ti-form-control" value="<?php echo $pluginManagerInstance->getNotificationParam('review-download-finished', 'email', get_option('admin_email')); ?>" />
<a href="#" class="ti-btn btn-notification-email-save"><?php echo __('Save', 'trustindex-plugin'); ?></a>
</div>
<div class="ti-info-text"><?php echo __('Leave the field blank if you do not want email notification.', 'trustindex-plugin'); ?></div>
</div>
</li>
</ul>
</li>
</ul>
</div>
<div class="ti-box">
<div class="ti-box-header"><?php echo __("Troubleshooting", 'trustindex-plugin'); ?></div>
<p class="ti-bold"><?php echo __('If you have any problem, you should try these steps:', 'trustindex-plugin'); ?></p>
<ul class="ti-troubleshooting-checklist">
<li>
<?php echo __("Trustindex plugin", 'trustindex-plugin'); ?>
<ul>
<li>
<?php echo __('Use the latest version:', 'trustindex-plugin') .' '. ($pluginUpdated ? $yesIcon : $noIcon); ?>
<?php if (!$pluginUpdated): ?>
<a href="/wp-admin/plugins.php" class="ti-btn ti-btn-loading-on-click"><?php echo __('Update', 'trustindex-plugin'); ?></a>
<?php endif; ?>
</li>
<li>
<?php echo __('Use automatic plugin update:', 'trustindex-plugin') .' '. (in_array($pluginSlug, $autoUpdates) ? $yesIcon : $noIcon); ?>
<?php if(!in_array($pluginSlug, $autoUpdates)): ?>
<a href="<?php echo wp_nonce_url('?page='. sanitize_text_field($_GET['page']) .'&tab=advanced&auto_update', 'ti-auto-update'); ?>" class="ti-btn ti-btn-loading-on-click"><?php echo __("Enable", 'trustindex-plugin'); ?></a>
<div class="ti-notice ti-notice-warning">
<p><?php echo __("You should enable it, to get new features and fixes automatically, right after they published!", 'trustindex-plugin'); ?></p>
</div>
<?php endif; ?>
</li>
</ul>
</li>
<?php if ($css): ?>
<li>
CSS
<ul>
<li><?php
$uploadDir = dirname($pluginManagerInstance->getCssFile());
echo __('writing permission', 'trustindex-plugin') .' (<strong>'. $uploadDir .'</strong>): '. (is_writable($uploadDir) ? $yesIcon : $noIcon); ?>
</li>
<li>
<?php echo __('CSS content:', 'trustindex-plugin'); ?>
<?php
if (is_file($pluginManagerInstance->getCssFile())) {
$content = file_get_contents($pluginManagerInstance->getCssFile());
if ($content === $css) {
echo $yesIcon;
}
else {
echo $noIcon .' '. __("corrupted", 'trustindex-plugin') .'
<div class="ti-notice ti-notice-warning">
<p><a href="'. wp_nonce_url('?page='. sanitize_text_field($_GET['page']) .'&tab=advanced&delete_css', 'ti-delete-css') .'">'. sprintf(__("Delete the CSS file at <strong>%s</strong>.", 'trustindex-plugin'), $pluginManagerInstance->getCssFile()) .'</a></p>
</div>';
}
}
else {
echo $noIcon;
}
?>
<span class="ti-checkbox ti-checkbox-row" style="margin-top: 5px">
<input type="checkbox" value="1" <?php if ($cssInline): ?>checked<?php endif;?> onchange="window.location.href = '?page=<?php echo sanitize_text_field($_GET['page']); ?>&tab=advanced&_wpnonce=<?php echo wp_create_nonce('ti-toggle-css'); ?>&toggle_css_inline=' + (this.checked ? 1 : 0)">
<label><?php echo __('Enable CSS internal loading', 'trustindex-plugin'); ?></label>
</span>
</li>
</ul>
</li>
<?php endif; ?>
<li>
<?php echo __('If you are using cacher plugin, you should:', 'trustindex-plugin'); ?>
<ul>
<li><?php echo __('clear the cache', 'trustindex-plugin'); ?></li>
<li><?php echo __("exclude Trustindex's JS file:", 'trustindex-plugin'); ?> <strong><?php echo 'https://cdn.trustindex.io/'; ?>loader.js</strong>
<ul>
<li><a href="#" onclick="jQuery('#list-w3-total-cache').toggle(); return false;">W3 Total Cache</a>
<ol id="list-w3-total-cache" style="display: none;">
<li><?php echo __('Navigate to', 'trustindex-plugin'); ?> "Performance" > "Minify"</li>
<li><?php echo __('Scroll to', 'trustindex-plugin'); ?> "Never minify the following JS files"</li>
<li><?php echo __('In a new line, add', 'trustindex-plugin'); ?> https://cdn.trustindex.io/*</li>
<li><?php echo __('Save', 'trustindex-plugin'); ?></li>
</ol>
</li>
</ul>
</li>
</ul>
</li>
<li>
<?php
$pluginUrl = 'https://wordpress.org/support/plugin/' . $pluginManagerInstance->get_plugin_slug();
$screenshotUrl = 'https://snipboard.io';
$screencastUrl = 'https://streamable.com/upload-video';
$pastebinUrl = 'https://pastebin.com';
echo sprintf(__('If the problem/question still exists, please create an issue here: %s', 'trustindex-plugin'), '<a href="'. $pluginUrl .'" target="_blank">'. $pluginUrl .'</a>');
?>
<br />
<?php echo __('Please help us with some information:', 'trustindex-plugin'); ?>
<ul>
<li><?php echo __('Describe your problem', 'trustindex-plugin'); ?></li>
<li><?php echo sprintf(__('You can share a screenshot with %s', 'trustindex-plugin'), '<a href="'. $screenshotUrl .'" target="_blank">'. $screenshotUrl .'</a>'); ?></li>
<li><?php echo sprintf(__('You can share a screencast video with %s', 'trustindex-plugin'), '<a href="'. $screencastUrl .'" target="_blank">'. $screencastUrl .'</a>'); ?></li>
<li><?php echo sprintf(__('If you have an (webserver) error log, you can copy it to the issue, or link it with %s', 'trustindex-plugin'), '<a href="'. $pastebinUrl .'" target="_blank">'. $pastebinUrl .'</a>'); ?></li>
<li><?php echo __('And include the information below:', 'trustindex-plugin'); ?></li>
</ul>
</li>
</ul>
<textarea class="ti-troubleshooting-info" readonly><?php include $pluginManagerInstance->get_plugin_dir() . 'include' . DIRECTORY_SEPARATOR . 'troubleshooting.php'; ?></textarea>
<a href=".ti-troubleshooting-info" class="ti-btn ti-pull-right ti-tooltip toggle-tooltip btn-copy2clipboard">
<?php echo __('Copy to clipboard', 'trustindex-plugin') ;?>
<span class="ti-tooltip-message">
<span style="color: #00ff00; margin-right: 2px">✓</span>
<?php echo __('Copied', 'trustindex-plugin'); ?>
</span>
</a>
<div class="clear"></div>
</div>
<div class="ti-box">
<div class="ti-box-header"><?php echo __('Re-create plugin', 'trustindex-plugin'); ?></div>
<p><?php echo __('Re-create the database tables of the plugin.<br />Please note: this removes all settings and reviews.', 'trustindex-plugin'); ?></p>
<a href="<?php echo wp_nonce_url('?page='. esc_attr(sanitize_text_field($_GET['page'])) .'&tab=free-widget-configurator&recreate', 'ti-recreate'); ?>" class="ti-btn ti-btn-loading-on-click ti-pull-right"><?php echo __('Re-create plugin', 'trustindex-plugin'); ?></a>
<div class="clear"></div>
</div>
<div class="ti-box">
<div class="ti-box-header"><?php echo __('Translation', 'trustindex-plugin'); ?></div>
<p>
<?php echo __('If you notice an incorrect translation in the plugin text, please report it here:', 'trustindex-plugin'); ?>
 <a href="mailto:support@trustindex.io">support@trustindex.io</a>
</p>
</div>
<?php include $pluginManagerInstance->get_plugin_dir() . 'include' . DIRECTORY_SEPARATOR . 'feature-request.php'; ?>
<div class="ti-box" id="trustindex-admin">
<div class="ti-box-header"><?php echo __('Connect your Trustindex account', 'trustindex-plugin'); ?></div>
<?php if ($tiSuccess === 'connected'): ?>
<?php echo $pluginManager::get_noticebox('success', __('Trustindex account successfully connected!', 'trustindex-plugin')); ?>
<?php elseif ($tiSuccess === 'disconnected'): ?>
<?php echo $pluginManager::get_noticebox('success', __('Trustindex account successfully disconnected!', 'trustindex-plugin')); ?>
<?php endif; ?>
<?php if ($tiError): ?>
<?php echo $pluginManager::get_noticebox('error', $tiError); ?>
<?php endif; ?>
<?php if ($trustindexSubscriptionId): ?>
<?php
$tiWidgets = $pluginManagerInstance->get_trustindex_widgets();
$tiPackage = is_array($tiWidgets) && $tiWidgets && isset($tiWidgets[0]['package']) ? $tiWidgets[0]['package'] : null;
?>
<p>
<?php echo sprintf(__('Your %s is connected.', 'trustindex-plugin'), __('Trustindex account', 'trustindex-plugin')); ?><br />
- <?php echo __('Your subscription ID:', 'trustindex-plugin'); ?> <strong><?php echo esc_html($trustindexSubscriptionId); ?></strong><br />
<?php if ($tiPackage): ?>
- <?php echo __('Your package:', 'trustindex-plugin'); ?> <strong><?php echo esc_html($tiPackage); ?></strong>
<?php endif; ?>
</p>
<?php if ($tiPackage === 'free'): ?>
<?php echo $pluginManager::get_noticebox('error', sprintf(__("Once the trial period has expired, the widgets will not appear. You can subscribe or switch back to the \"%s\" tab", 'trustindex-plugin'), [ __('Free Widget Configurator', 'trustindex-plugin') ])); ?>
<?php elseif ($tiPackage === 'trial'): ?>
<?php echo $pluginManager::get_noticebox('warning', sprintf(__("Once the trial period has expired, the widgets will not appear. You can subscribe or switch back to the \"%s\" tab", 'trustindex-plugin'), [ __('Free Widget Configurator', 'trustindex-plugin') ])); ?>
<?php endif; ?>
<form method="post" class="ti-mt-0" action="">
<input type="hidden" name="command" value="disconnect" />
<?php wp_nonce_field('disconnect-reg_' . $pluginManagerInstance->get_plugin_slug()); ?>
<button class="ti-btn ti-btn-loading-on-click ti-pull-right" type="submit"><?php echo __('Disconnect', 'trustindex-plugin'); ?></button>
<div class="clear"></div>
</form>
<?php else: ?>
<p><?php echo sprintf(__('You can connect your %s with your Trustindex account, and can display your widgets easier.', 'trustindex-plugin'), 'Widgets for Google Reviews'); ?></p>
<form id="form-connect" method="post" action="#trustindex-admin">
<input type="hidden" name="command" value="connect" />
<?php wp_nonce_field('connect-reg_' . $pluginManagerInstance->get_plugin_slug()); ?>
<div class="ti-form-group">
<label>E-mail</label>
<input type="email" placeholder="E-mail" name="email" class="ti-form-control" required="required" id="ti-reg-email2" value="<?php echo esc_attr($current_user->user_email); ?>" />
</div>
<div class="ti-form-group ti-mb-1">
<label><?php echo __('Password', 'trustindex-plugin'); ?></label>
<input type="password" placeholder="<?php echo __('Password', 'trustindex-plugin'); ?>" name="password" class="ti-form-control" required="required" id="ti-reg-password2" />
<span class="dashicons dashicons-visibility ti-toggle-password"></span>
</div>
<p class="ti-text-center">
<button type="submit" class="ti-btn ti-btn-loading-on-click ti-mb-1"><?php echo __('CONNECT ACCOUNT', 'trustindex-plugin');?></button>
<br />
<a class="ti-btn ti-btn-default" href="<?php echo 'https://admin.trustindex.io/'; ?>forgot-password" target="_blank"><?php echo __('Have you forgotten your password?', 'trustindex-plugin'); ?></a>
<a class="ti-btn ti-btn-default" href="https://www.trustindex.io/?a=sys&c=wp-google-4" target="_blank"><?php echo __('Create a new Trustindex account', 'trustindex-plugin');?></a>
</p>
</form>
<?php endif; ?>
<?php if ($trustindexSubscriptionId): ?>
<div class="ti-box-header ti-mt-2"><?php echo __('Manage your Trustindex account', 'trustindex-plugin'); ?></div>
<a class="ti-btn" href="<?php echo 'https://admin.trustindex.io/'; ?>widget" target="_blank"><?php echo __("Go to Trustindex's admin!", 'trustindex-plugin'); ?></a>
<div class="ti-box-header ti-mt-2"><?php echo __('Insert your widget into your wordpress site using shortcode', 'trustindex-plugin'); ?></div>
<?php if ($trustindexSubscriptionId): ?>
<?php if ($widgetNumber): ?>
<p><?php echo sprintf(__('You have got %d widgets saved in Trustindex admin.', 'trustindex-plugin'), $widgetNumber); ?></p>
<?php foreach ($tiWidgets as $wcIndex => $wc): ?>
<p class="ti-bold"><?php echo esc_html($wc['name']); ?>:</p>
<?php if ($wc['widgets']): ?>
<ul style="padding-left: 15px">
<?php foreach ($wc['widgets'] as $wiNum => $w): ?>
<li>
<?php echo esc_html($wiNum + 1); ?>.
<a href=".ti-w-<?php echo esc_attr($wcIndex .'-'. $wiNum); ?>" class="btn-toggle" data-ti-id="<?php echo esc_attr($w['id']); ?>"><?php echo esc_html($w['name']); ?></a>
<div style="display: none; padding: 15px 30px" class="ti-w-<?php echo esc_attr($wcIndex .'-'. $wiNum); ?>">
<?php
$trustindexShortCodeText = $pluginManagerInstance->get_shortcode_name().' data-widget-id="'.$w['id'].'"';
include(plugin_dir_path(__FILE__) . '../include/shortcode-paste-box.php');
?>
</div>
</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
-
<?php endif; ?>
<?php endforeach; ?>
<?php else: ?>
<?php echo $pluginManager::get_noticebox('error', __('You have no widgets saved!', 'trustindex-plugin')); ?>
<?php endif; ?>
<a class="ti-btn" href="<?php echo 'https://admin.trustindex.io/'; ?>widget" target="_blank"><?php echo __('Create more!', 'trustindex-plugin'); ?></a>
<?php endif; ?>
<?php endif; ?>
</div>
