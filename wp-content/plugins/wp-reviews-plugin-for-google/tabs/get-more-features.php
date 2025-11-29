<?php
defined('ABSPATH') or die('No script kiddies please!');
?>
<h1 class="ti-header-title"><?php echo __('Get more Features', 'trustindex-plugin'); ?></h1>
<div class="ti-box">
<div class="ti-box-header"><?php echo __('Skyrocket Your Sales with Customer Reviews', 'trustindex-plugin'); ?></div>
<p class="ti-bold">
<?php echo sprintf(__('%s+ WordPress websites use Trustindex to embed reviews fast and easily.', 'trustindex-plugin'), '600.000'); ?><br />
<?php echo __('Increase SEO, trust and sales using customer reviews.', 'trustindex-plugin'); ?>
</p>
<div class="ti-section-title"><?php echo __('Top Features', 'trustindex-plugin'); ?></div>
<ul class="ti-check-list">
<li><?php echo __('Display unlimited number of reviews', 'trustindex-plugin'); ?></li>
<li><?php echo __('Create unlimited number of widgets', 'trustindex-plugin'); ?></li>
<li><?php echo __('Display reviews with photos', 'trustindex-plugin'); ?></li>
<li><?php echo sprintf(__('%d review platforms', 'trustindex-plugin'), 135); ?></li>
<li><?php echo __('Mix reviews from different platforms', 'trustindex-plugin'); ?></li>
<li><?php echo __('Get more reviews', 'trustindex-plugin'); ?></li>
<li><?php echo __('Manage all reviews in one place', 'trustindex-plugin'); ?></li>
<li><?php echo __('Automatically update with NEW reviews', 'trustindex-plugin'); ?></li>
</ul>
<?php echo $pluginManagerInstance->getProFeatureButton('wp-google-3'); ?>
<div class="ti-special-offer">
<img src="<?php echo $pluginManagerInstance->get_plugin_file_url('static/img/special_30.jpg'); ?>">
<p><?php echo str_replace('%%', '%', __('Now we offer you a 30%% discount off for your first subscription! Create your free account and benefit from the onboarding discount now!', 'trustindex-plugin')); ?></p>
<div class="clear"></div>
</div>
</div>