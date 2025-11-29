<?php
defined('ABSPATH') or die('No script kiddies please!');
wp_enqueue_script('trustindex-js', 'https://cdn.trustindex.io/loader.js', [], false, true);
?>
<h1 class="ti-header-title"><?php echo __('Rate Us', 'trustindex-plugin'); ?></h1>
<div class="ti-box">
<div class="ti-box-header"><?php echo __('Do you like our free plugin?', 'trustindex-plugin'); ?></div>
<p>
<?php echo __("We've spent a lot of time developing this software. If you use the free version, you can still support us by leaving a review!", 'trustindex-plugin'); ?><br />
<?php echo __('Thank you in advance!', 'trustindex-plugin'); ?>
</p>
<a class="ti-btn" href="https://wordpress.org/support/plugin/<?php echo $pluginManagerInstance->get_plugin_slug(); ?>/reviews/?rate=5#new-post" target="_blank"><?php echo __('Click here to rate us!', 'trustindex-plugin'); ?></a>
<div class="ti-box-footer">
<div data-src='https://cdn.trustindex.io/loader.js?<?php echo '3ef6962888fb40403c525129f9'; ?>'></div>
</div>
</div>