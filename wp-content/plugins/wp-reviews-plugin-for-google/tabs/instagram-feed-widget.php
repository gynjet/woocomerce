<?php
defined('ABSPATH') or die('No script kiddies please!');
update_option($pluginManagerInstance->get_option_name('instagram-promo-opened'), 1, false);
?>
<div class="ti-container ti-narrow-page">
<h1 class="ti-header-title">Instagram Feed Widget</h1>
<div class="ti-preview-boxes-container">
<div class="ti-full-width">
<div class="ti-box">
<div class="ti-box-inner">
<div class="ti-box-header"><?php echo esc_html(sprintf(__('Display your %s with our free Widgets!', 'trustindex-plugin'), ' Feed')); ?></div>
<img class="ti-mb-2" src="<?php echo $pluginManagerInstance->get_plugin_file_url('static/img/instagram-feed-widget.jpg'); ?>" />
<a class="ti-btn" href="https://wordpress.org/plugins/social-photo-feed-widget" target="_blank"><?php echo esc_html(sprintf(__('Create %s Widgets for Free', 'trustindex-plugin'), 'Instagram Feed')); ?></a>
<div class="ti-section-title"><strong><?php echo __('Free features', 'trustindex-plugin'); ?></strong></div>
<ul class="ti-check-list">
<li><?php echo sprintf(__('Multiple layout options: %s', 'trustindex-plugin'), '<strong>Slider, Grid, List, Masonry</strong>'); ?></li>
<li><strong><?php echo __('Multiple card layouts', 'trustindex-plugin'); ?></strong></li>
<li><strong><?php echo __('Automatic, daily updates', 'trustindex-plugin'); ?></strong></li>
<li><?php echo __('Customizable', 'trustindex-plugin'); ?></li>
<li><?php echo __('<strong>Likes and Comments Count</strong>: Display the number of likes and comments for each post', 'trustindex-plugin'); ?></li>
<li><?php echo __('Fully responsive and <strong>mobile-friendly</strong>: a great layout on any screen size and container width', 'trustindex-plugin'); ?></li>
<li><?php echo sprintf(__('Includes a Follow on %s button', 'trustindex-plugin'), 'Instagram'); ?></li>
<li><?php echo __('Showcase a header at the top of your feed widget', 'trustindex-plugin'); ?></li>
<li><?php echo sprintf(__('<strong>Load more %s photos</strong> with the "Load More" button', 'trustindex-plugin'), 'Instagram'); ?></li>
<li><?php echo __('Easy setup process', 'trustindex-plugin'); ?></li>
<li><strong><?php echo __('Shortcode integration', 'trustindex-plugin'); ?></strong></li>
</ul>
<a class="ti-btn" href="https://wordpress.org/plugins/social-photo-feed-widget" target="_blank"><?php echo esc_html(sprintf(__('Create %s Widgets for Free', 'trustindex-plugin'), 'Instagram Feed')); ?></a>
</div>
</div>
</div>
</div>
</div>