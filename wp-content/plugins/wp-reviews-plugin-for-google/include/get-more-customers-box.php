<?php
defined('ABSPATH') or die('No script kiddies please!');
?>
<h1 class="ti-header-title ti-mt-2"><?php echo __('Want to get more customers?', 'trustindex-plugin'); ?></h1>
<div class="ti-box">
<div class="ti-box-header"><?php echo __('Increase SEO, trust and sales using customer reviews.', 'trustindex-plugin'); ?></div>
<?php echo $pluginManagerInstance->getProFeatureButton($tiCampaign1); ?>
<ul class="ti-seo-list">
<li>
<strong><?php echo __('Display unlimited number of reviews', 'trustindex-plugin'); ?></strong><br />
<?php echo __('You can test Trustindex with 10 reviews in the free version. Upgrade to Business to display ALL the reviews received. Be the undisputed customer choice in your industry!', 'trustindex-plugin'); ?>
</li>
<li>
<strong><?php echo __('Create unlimited number of widgets', 'trustindex-plugin'); ?></strong><br />
<?php echo __('Use the widgets matching your page the best to build trust.', 'trustindex-plugin'); ?>
</li>
<li>
<strong><?php echo __('Display reviews with photos', 'trustindex-plugin'); ?></strong><br />
<?php echo __('Boost engagement by displaying customer photos in an interactive popup slider, bringing real experiences to life.', 'trustindex-plugin'); ?>
</li>
<li>
<strong><?php echo sprintf(__("%d review platforms", 'trustindex-plugin'), 135); ?></strong><br />
<?php echo sprintf(__("Add more reviews to your widget from %s, etc. to enjoy more trust, and to keep customers on your site.", 'trustindex-plugin'), 'Google, Facebook, Yelp, Amazon, Tripadvisor, Booking.com, Airbnb, Hotels.com, Capterra, Foursquare, Opentable'); ?><br />
<img src="<?php echo $pluginManagerInstance->get_plugin_file_url('static/img/platforms.png'); ?>" alt="" style="margin-top: 5px" />
</li>
<li>
<strong><?php echo __('Mix Reviews', 'trustindex-plugin'); ?></strong><br />
<?php echo __('You can mix your reviews from different platforms and display them in 1 review widget.', 'trustindex-plugin'); ?>
</li>
<li>
<strong><?php echo __('Get more reviews', 'trustindex-plugin'); ?></strong><br />
<?php echo __('Use our Review Invitation System to collect hundreds of new reviews. Become impossible to resist!', 'trustindex-plugin'); ?>
</li>
<li>
<strong><?php echo __('Manage all reviews in one place', 'trustindex-plugin'); ?></strong><br />
<?php echo __('Turn on email alert to ALL new reviews, so that you can manage them quickly.', 'trustindex-plugin'); ?>
</li>
<li>
<strong><?php echo __('Automatically update with NEW reviews', 'trustindex-plugin'); ?></strong><br />
<?php echo __('Wordpress cannot update reviews, but Trustindex can! As soon as you get a new review, Trustindex Business can automatically add it to your website. Customers love fresh reviews!', 'trustindex-plugin'); ?>
</li>
</ul>
<?php echo $pluginManagerInstance->getProFeatureButton($tiCampaign2); ?>
<div class="ti-special-offer">
<img src="<?php echo $pluginManagerInstance->get_plugin_file_url('static/img/special_30.jpg'); ?>">
<p><?php echo str_replace('%%', '%', __('Now we offer you a 30%% discount off for your first subscription! Create your free account and benefit from the onboarding discount now!', 'trustindex-plugin')); ?></p>
<div class="clear"></div>
</div>
</div>