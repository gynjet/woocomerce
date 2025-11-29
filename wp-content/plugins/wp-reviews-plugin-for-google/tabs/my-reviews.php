<?php
defined('ABSPATH') or die('No script kiddies please!');
if (isset($_POST['save-highlight'])) {
check_admin_referer('ti-save-highlight');
$id = null;
$start = null;
$length = null;
if (isset($_POST['id'])) {
$id = (int)$_POST['id'];
}
if (isset($_POST['start'])) {
$start = sanitize_text_field($_POST['start']);
}
if (isset($_POST['length'])) {
$length = sanitize_text_field($_POST['length']);
}
if ($id) {
$highlight = "";
if (!is_null($start)) {
$highlight = $start . ',' . $length;
}
$wpdb->query("UPDATE `". $pluginManagerInstance->get_tablename('reviews') ."` SET highlight = '$highlight' WHERE id = '$id'");
}
exit;
}
if (isset($_GET['toggle-hide'])) {
check_admin_referer('ti-toggle-hide');
$id = (int)$_GET['toggle-hide'];
if ($id) {
$hidden = 1;
if ($wpdb->get_var('SELECT hidden FROM `'. $pluginManagerInstance->get_tablename('reviews') .'` WHERE id = '. $id)) {
$hidden = 0;
}
$wpdb->query("UPDATE `". $pluginManagerInstance->get_tablename('reviews') ."` SET hidden = $hidden WHERE id = '$id'");
}
header('Location: admin.php?page=' . sanitize_text_field($_GET['page']) . '&tab=' . sanitize_text_field($_GET['tab']));
exit;
}
/* Replied flag saving:
- Google: comes after source connect
- Facebook: we saved internal
- other: dont save anything & only show "Reply with ChatGPT" button
*/
if (isset($_POST['save-reply'])) {
check_admin_referer('ti-save-reply');
$id = null;
$reply = null;
if (isset($_POST['id'])) {
$id = (int)$_POST['id'];
}
$reply = wp_kses_post(stripslashes($_POST['save-reply']));
if ($id && $reply) {
$wpdb->query("UPDATE `". $pluginManagerInstance->get_tablename('reviews') ."` SET reply = '". str_replace("'", "\'", $reply) ."' WHERE id = '$id'");
}
exit;
}
if (isset($_POST['save-reply-generated'])) {
update_option($pluginManagerInstance->get_option_name('reply-generated'), 1, false);
exit;
}

if (isset($_POST['download_data'])) {
check_admin_referer('ti-download-reviews');
$data = json_decode(stripcslashes($_POST['download_data']), true);
if (isset($data['is_new_reviews']) && $data['is_new_reviews']) {
if (isset($data['reviews']) && is_array($data['reviews']) && $data['reviews']) {
$pluginManagerInstance->save_reviews($data['reviews']);
if (!$pluginManagerInstance->getNotificationParam('review-download-finished', 'hidden')) {
$pluginManagerInstance->setNotificationParam('review-download-finished', 'active', true);
}
$pluginManagerInstance->sendNotificationEmail('review-download-finished');
}
$pageDetails = $pluginManagerInstance->getPageDetails();
if (isset($data['name'])) {
$pageDetails['name'] = $data['name'];
if (isset($pageDetails['address'])) {
$pageDetails['address'] = $data['address'];
}
if (isset($pageDetails['avatar_url'])) {
$pageDetails['avatar_url'] = $data['avatar_url'];
}
$pageDetails['rating_number'] = $data['rating_number'];
if (isset($data['rating_numbers']) && $data['rating_numbers']) {
$pageDetails['rating_numbers'] = $data['rating_numbers'];
}
if (isset($data['rating_numbers_last']) && $data['rating_numbers_last']) {
$pageDetails['rating_numbers_last'] = $data['rating_numbers_last'];
}
$pageDetails['rating_score'] = $data['rating_score'];
update_option($pluginManagerInstance->get_option_name('page-details'), $pageDetails, false);
$GLOBALS['wp_object_cache']->delete($pluginManagerInstance->get_option_name('page-details'), 'options');
}
if (!$pluginManagerInstance->getNotificationParam('review-download-available', 'hidden')) {
$pluginManagerInstance->setNotificationParam('review-download-available', 'do-check', true);
$pluginManagerInstance->setNotificationParam('review-download-available', 'active', false);
}
} else {
update_option($pluginManagerInstance->get_option_name('review-download-is-failed'), 1, false);
}
update_option($pluginManagerInstance->get_option_name('download-timestamp'), time() + (int)$data['next_update_available'], false);
exit;
}
$reviews = [];
if ($pluginManagerInstance->is_noreg_linked()) {
$reviews = $wpdb->get_results('SELECT * FROM `'. $pluginManagerInstance->get_tablename('reviews') .'` ORDER BY date DESC');
}
$isReviewDownloadInProgress = $pluginManagerInstance->is_review_download_in_progress();
function trustindex_plugin_write_rating_stars($score)
{
global $pluginManagerInstance;
if ($pluginManagerInstance->is_ten_scale_rating_platform()) {
return '<div class="ti-rating-box">'. $pluginManagerInstance->formatTenRating($score) .'</div>';
}
$text = "";
$link = "https://cdn.trustindex.io/assets/platform/".ucfirst("google")."/star/";
if (!is_numeric($score)) {
return $text;
}
for ($si = 1; $si <= $score; $si++) {
$text .= '<img src="'. $link .'f.svg" class="ti-star" />';
}
$fractional = $score - floor($score);
if (0.25 <= $fractional) {
if ($fractional < 0.75) {
$text .= '<img src="'. $link .'h.svg" class="ti-star" />';
}
else {
$text .= '<img src="'. $link .'f.svg" class="ti-star" />';
}
$si++;
}
for (; $si <= 5; $si++) {
$text .= '<img src="'. $link .'e.svg" class="ti-star" />';
}
return $text;
}
wp_enqueue_style('trustindex-widget-css', 'https://cdn.trustindex.io/assets/widget-presetted-css/4-light-background.css');
wp_enqueue_script('trustindex-review-js', 'https://cdn.trustindex.io/assets/js/trustindex-review.js', [], false, true);
wp_add_inline_script('trustindex-review-js', '
jQuery(".ti-review-content").TI_shorten({
"showLines": 2,
"lessText": "'. __('Show less', 'trustindex-plugin') .'",
"moreText": "'. __('Show more', 'trustindex-plugin') .'",
});
jQuery(".ti-review-content").TI_format();
');
$downloadTimestamp = get_option($pluginManagerInstance->get_option_name('download-timestamp'), time());
$pageDetails = $pluginManagerInstance->getPageDetails();
if ($reviewDownloadFailed = get_option($pluginManagerInstance->get_option_name('review-download-is-failed'))) {
delete_option($pluginManagerInstance->get_option_name('review-download-is-failed'));
}
?>
<div class="ti-header-title"><?php echo __('My Reviews', 'trustindex-plugin'); ?></div>
<div class="ti-box">
<?php if (!$isReviewDownloadInProgress): ?>
<?php if ($reviewDownloadFailed): ?>
<div class="ti-notice ti-notice-error">
<p><?php echo __('The manual review download not available yet.', 'trustindex-plugin'); ?></p>
</div>
<?php endif; ?>
<?php if ($downloadTimestamp <= time()): ?>
<div class="ti-notice ti-d-none ti-notice-info" id="ti-connect-info">
<p><?php echo __("A popup window should be appear! Please, go to there and continue the steps! (If there is no popup window, you can check the the browser's popup blocker)", 'trustindex-plugin'); ?></p>
</div>
<a href="#" data-nonce="<?php echo wp_create_nonce('ti-download-reviews'); ?>" class="ti-btn ti-btn-lg ti-btn-loading-on-click ti-tooltip ti-show-tooltip ti-tooltip-light ti-mb-1 btn-download-reviews" data-delay=10>
<?php echo __('Download new reviews', 'trustindex-plugin');?>
<span class="ti-tooltip-message"><?php echo __('Now, you can download your new reviews.', 'trustindex-plugin'); ?></span>
</a>
<?php else: ?>
<?php $days = ceil(($downloadTimestamp - time()) / 86400); ?>
<a href="#" class="ti-btn ti-btn-lg ti-btn-disabled ti-tooltip ti-show-tooltip ti-tooltip-light ti-mb-1">
<?php echo __('Download new reviews', 'trustindex-plugin'); ?>
<span class="ti-tooltip-message"><?php echo sprintf(__('The manual review download will be available again in %d day(s).', 'trustindex-plugin'), $days); ?></span>
</a>
<?php endif; ?>
<?php $pageDetails = $pluginManagerInstance->getPageDetails(); ?>
<input type="hidden" id="ti-noreg-page-id" value="<?php echo esc_attr($pageDetails['id']); ?>" />
<input type="hidden" id="ti-noreg-webhook-url" value="<?php echo $pluginManagerInstance->getWebhookUrl(); ?>" />
<input type="hidden" id="ti-noreg-email" value="<?php echo get_option('admin_email'); ?>" />
<input type="hidden" id="ti-noreg-version" value="<?php echo esc_attr($pluginManagerInstance->getVersion()); ?>" />

<?php
$reviewDownloadToken = get_option($pluginManagerInstance->get_option_name('review-download-token'));
if (!$reviewDownloadToken) {
$reviewDownloadToken = wp_create_nonce('ti-noreg-connect-token');
update_option($pluginManagerInstance->get_option_name('review-download-token'), $reviewDownloadToken, false);
}
?>
<input type="hidden" id="ti-noreg-connect-token" name="ti-noreg-connect-token" value="<?php echo $reviewDownloadToken; ?>" />
<?php endif; ?>
<div class="ti-upgrade-notice">
<strong><?php echo __('UPGRADE to PRO Features', 'trustindex-plugin'); ?></strong>
<p><?php echo sprintf(__('Automatic review update, creating unlimited review widgets, downloading and displaying all reviews, %d review platforms available!', 'trustindex-plugin'), 135); ?></p>
<?php echo $pluginManagerInstance->getProFeatureButton('wp-google-pro'); ?>
</div>

<?php if (!count($reviews)): ?>
<?php if (!$isReviewDownloadInProgress): ?>
<div class="ti-notice ti-notice-warning">
<p><?php echo __('You had no reviews at the time of last review downloading.', 'trustindex-plugin'); ?></p>
</div>
<?php endif; ?>
<?php else: ?>
<input type="hidden" id="ti-widget-language" value="<?php echo esc_attr(get_option($pluginManagerInstance->get_option_name('lang'), 'en')); ?>" />
<table class="wp-list-table widefat fixed striped table-view-list ti-my-reviews ti-widget">
<thead>
<tr>
<th class="ti-text-center"><?php echo __('Reviewer', 'trustindex-plugin'); ?></th>
<th class="ti-text-center" style="width: 90px;"><?php echo __('Rating', 'trustindex-plugin'); ?></th>
<th class="ti-text-center"><?php echo __('Date', 'trustindex-plugin'); ?></th>
<th style="width: 50%"><?php echo __('Text', 'trustindex-plugin'); ?></th>
</tr>
</thead>
<tbody>
<?php foreach ($reviews as $review): ?>
<?php $reviewText = $pluginManagerInstance->getReviewHtml($review); ?>
<tr data-id="<?php echo esc_attr($review->id); ?>"<?php if ($review->hidden): ?> class="ti-hidden-review"<?php endif; ?>>
<td class="ti-text-center">
<img src="<?php echo esc_url($review->user_photo); ?>" class="ti-user-avatar" /><br />
<?php echo esc_html($review->user); ?>
</td>
<td class="ti-text-center source-<?php echo ucfirst("google") ?>"><?php echo trustindex_plugin_write_rating_stars($review->rating); ?></td>
<td class="ti-text-center"><?php echo esc_html($review->date); ?></td>
<td>
<div class="ti-review-content"><?php echo $reviewText; ?></div>
<?php

$state = 'reply';
if ($review->reply) {
$state = 'replied';
}
$hideReplyButton = false;
$hideReplyButton = get_option($pluginManagerInstance->get_option_name('review-download-modal'), 1);
?>
<?php if (!$review->hidden): ?>
<?php if (!$hideReplyButton): ?>
<?php if ($review->reply): ?>
<a href="#" class="ti-btn ti-btn-default ti-btn-sm ti-btn-default-disabled btn-show-ai-reply"><?php echo __('Reply', 'trustindex-plugin'); ?></a>
<?php else: ?>
<a href="#" class="ti-btn ti-btn-sm btn-show-ai-reply" data-edit-reply-text="<?php echo __('Reply', 'trustindex-plugin'); ?>"><?php echo __('Reply with ChatGPT', 'trustindex-plugin'); ?></a>
<?php endif; ?>
<?php endif; ?>
<?php if ($review->text): ?>
<a href="<?php echo esc_attr($review->id); ?>" class="ti-btn ti-btn-sm ti-btn-default btn-show-highlight<?php if (isset($review->highlight) && $review->highlight): ?> has-highlight<?php endif; ?>"><?php echo __('Highlight text', 'trustindex-plugin') ;?></a>
<?php endif; ?>
<?php endif; ?>
<a href="<?php echo wp_nonce_url('?page='. sanitize_text_field($_GET['page']) .'&tab=my-reviews&toggle-hide='. $review->id, 'ti-toggle-hide'); ?>" class="ti-btn ti-btn-sm ti-btn-default btn-toggle-hide">
<?php if (!$review->hidden): ?>
<?php echo __('Hide review', 'trustindex-plugin'); ?>
<?php else: ?>
<?php echo __('Show review', 'trustindex-plugin'); ?>
<?php endif; ?>
</a>
<?php if (!$review->hidden && !$hideReplyButton): ?>
<div class="ti-button-dropdown ti-reply-box<?php if ($state === 'replied'): ?> ti-active<?php endif; ?>" data-state="<?php echo $state; ?>" data-original-state="<?php echo $state; ?>">
<span class="ti-button-dropdown-arrow" data-button=".btn-show-ai-reply"></span>
<?php if ($state !== 'copy-reply'): ?>
<div class="ti-reply-box-state state-reply">
<div class="ti-button-dropdown-title">
<strong><?php echo __('ChatGPT generated reply', 'trustindex-plugin'); ?></strong>
<span><?php echo __('you can modify before upload', 'trustindex-plugin'); ?>
</div>
<textarea id="ti-ai-reply-<?php echo esc_attr($review->id); ?>" rows="1"></textarea>
<?php if (!$review->text): ?>
<div class="ti-alert ti-alert-empty-review d-none"><?php echo __("The reply was generated in your widget language because the review's text is empty.", 'trustindex-plugin'); ?></div>
<?php endif; ?>
<a href="<?php echo esc_attr($review->id); ?>" data-nonce="<?php echo wp_create_nonce('ti-save-reply'); ?>" class="ti-btn ti-btn-sm btn-post-reply"><?php echo sprintf(__('Upload reply to %s', 'trustindex-plugin'), 'Google'); ?></a>
<a href="#" class="ti-btn ti-btn-sm ti-btn-no-background btn-hide-ai-reply"><?php echo __('Cancel', 'trustindex-plugin'); ?></a>
</div>

<div class="ti-reply-box-state state-replied">
<div class="ti-button-dropdown-title">
<strong><?php echo sprintf(__('Reply by %s', 'trustindex-plugin'), $pageDetails['name']); ?></strong>
</div>
<div class="ti-alert ti-d-none"><?php echo __('Reply successfully uploaded.', 'trustindex-plugin'); ?></div>
<p><?php echo esc_html($review->reply); ?></p>
<?php if ($pluginManagerInstance->getShortName() === 'google'): ?>
<a href="<?php echo esc_attr($review->id); ?>" class="ti-btn ti-btn-sm ti-btn-white btn-show-edit-reply"><?php echo __('Edit reply', 'trustindex-plugin'); ?></a>
<?php endif; ?>
</div>

<div class="ti-reply-box-state state-edit-reply">
<div class="ti-button-dropdown-title">
<strong><?php echo __('Edit reply', 'trustindex-plugin'); ?></strong>
<span><?php echo __('change your previous reply', 'trustindex-plugin'); ?>
</div>
<textarea rows="1"><?php echo esc_html($review->reply); ?></textarea>
<a href="<?php echo esc_attr($review->id); ?>" data-nonce="<?php echo wp_create_nonce('ti-save-reply'); ?>" class="ti-btn ti-btn-sm btn-post-reply"><?php echo sprintf(__('Upload reply to %s', 'trustindex-plugin'), 'Google'); ?></a>
<a href="#" class="ti-btn ti-btn-sm ti-btn-no-background btn-hide-edit-reply"><?php echo __('Cancel', 'trustindex-plugin'); ?></a>
</div>
<?php endif; ?>
<div class="ti-reply-box-state state-copy-reply">
<div class="ti-button-dropdown-title">
<strong><?php echo __('Copy the reply', 'trustindex-plugin'); ?></strong>
</div>
<div class="ti-alert ti-alert-warning ti-d-none">
<?php echo __('We could not connect your account with the review.', 'trustindex-plugin'); ?>
<a href="#" class="btn-try-reply-again"><?php echo __('Try again', 'trustindex-plugin'); ?></a>
</div>
<textarea id="ti-copy-ai-reply-<?php echo esc_attr($review->id); ?>" rows="1"></textarea>
<a href="#ti-copy-ai-reply-<?php echo esc_attr($review->id); ?>" class="ti-btn ti-btn-sm ti-tooltip ti-toggle-tooltip btn-copy2clipboard ">
<?php echo __('Copy to clipboard', 'trustindex-plugin') ;?>
<span class="ti-tooltip-message">
<span style="color: #00ff00; margin-right: 2px">✓</span>
<?php echo __('Copied', 'trustindex-plugin'); ?>
</span>
</a>
<a href="#" class="ti-btn ti-btn-sm ti-btn-no-background btn-hide-ai-reply"><?php echo __('Cancel', 'trustindex-plugin'); ?></a>
</div>
</div>
<script type="application/ld+json"><?php echo json_encode([
'source' => [
'page_id' => $pageDetails['id'],
'name' => $pageDetails['name'],
'reviews' => [
'count' => $pageDetails['rating_number'],
'score' => $pageDetails['rating_score'],
],
'access_token' => isset($pageDetails['access_token']) ? $pageDetails['access_token'] : null
],
'review' => [
'id' => $review->reviewId,
'reviewer' => [
'name' => $review->user,
'avatar_url' => $review->user_photo
],
'rating' => $review->rating,
'text' => $review->text,
'created_at' => $review->date
]
]); ?></script>
<?php endif; ?>
<?php if (!$review->hidden && $review->text): ?>
<div class="ti-button-dropdown ti-highlight-box">
<span class="ti-button-dropdown-arrow" data-button=".btn-show-highlight"></span>
<div class="ti-button-dropdown-title">
<strong><?php echo __('Highlight text', 'trustindex-plugin'); ?></strong>
<span><?php echo __('just select the text you want to highlight', 'trustindex-plugin'); ?>
</div>
<div class="ti-highlight-content">
<div class='ti-raw-content'><?php echo $reviewText; ?></div>
<div class='ti-selection-content'><?php echo preg_replace('/<mark class="ti-highlight">/', '', $reviewText); ?></div>
</div>
<a href="<?php echo esc_attr($review->id); ?>" data-nonce="<?php echo wp_create_nonce('ti-save-highlight'); ?>" class="ti-btn ti-btn-sm btn-save-highlight"><?php echo __('Save', 'trustindex-plugin'); ?></a>
<a href="#" class="ti-btn ti-btn-sm ti-btn-no-background btn-hide-highlight"><?php echo __('Cancel', 'trustindex-plugin'); ?></a>
<?php if ($review->highlight): ?>
<a href="<?php echo esc_attr($review->id); ?>" data-nonce="<?php echo wp_create_nonce('ti-save-highlight'); ?>" class="ti-btn ti-btn-sm ti-btn-danger ti-pull-right btn-remove-highlight"><?php echo __('Remove highlight', 'trustindex-plugin'); ?></a>
<?php endif; ?>
</div>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
</div>
<?php if (!get_option($pluginManagerInstance->get_option_name('rate-us-feedback'), 0)): ?>
<?php include(plugin_dir_path(__FILE__ ) . '../include/rate-us-feedback-box.php'); ?>
<?php endif; ?>
<?php
$tiCampaign1 = 'wp-google-4';
$tiCampaign2 = 'wp-google-5';
include(plugin_dir_path(__FILE__ ) . '../include/get-more-customers-box.php');
?>
