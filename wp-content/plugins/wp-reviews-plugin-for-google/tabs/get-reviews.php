<?php
defined('ABSPATH') or die('No script kiddies please!');
?>
<h1 class="ti-header-title"><?php echo sprintf(__('%d Quick & Easy Ways to Get More Reviews', 'trustindex-plugin'), class_exists('Woocommerce') ? 4 : 3); ?></h1>
<p class="ti-mb-2">
<?php echo __('Skyrocket your sales by collecting real customer reviews quickly and easily.', 'trustindex-plugin'); ?>
<br />
<?php echo __('Boost your credibility, climb higher in search results, and attract more customers with our proven review collection solutions!', 'trustindex-plugin'); ?>
</p>
<div class="ti-box ti-promobox-with-image">
<div class="ti-box-header">1. <?php echo __('Email Campaigns', 'trustindex-plugin'); ?></div>
<div class="ti-promobox-body">
<div class="ti-promobox-text">
<?php echo __('Boost your review count effortlessly with targeted, review-collecting email campaigns!', 'trustindex-plugin'); ?>

<?php echo __('Easily reach out to your customer base and collect 100+ new Google reviews effortlessly.', 'trustindex-plugin'); ?>
<br /><br />
<?php echo __('With automated follow-ups and customizable templates, email campaigns streamline the review collection process, boost your online reputation, and help you climb to the top of Google Maps in your industry.', 'trustindex-plugin'); ?>
<br /><br />
<a href="https://www.trustindex.io/?a=sys&c=wp-getmore-email&url=/features-list/collect-reviews/email-campaigns/" target="_blank" class="ti-btn"><?php echo __('Create Your Review Collector Campaign Now!', 'trustindex-plugin'); ?></a>
</div>
<div class="ti-promobox-image">
<img src="<?php echo $pluginManagerInstance->get_plugin_file_url('static/img/review-collection-sm.png'); ?>" />
</div>
</div>
</div>
<div class="ti-box ti-promobox-with-image">
<div class="ti-box-header">2. <?php echo __('Review Collector NFC Cards', 'trustindex-plugin'); ?></div>
<div class="ti-promobox-body">
<div class="ti-promobox-text">
<?php echo sprintf(__('Collect new reviews daily with a single tap! Use the Review Collector NFC Cards to get ratings on %d+ platforms.', 'trustindex-plugin'), 130); ?>
<br /><br />
<?php echo __("By tapping their smartphones on these NFC-enabled cards, customers are instantly directed to your business's review platform, making it easy and convenient for them to leave feedback. Get more reviews effortlessly with this convenient and modern solution!", 'trustindex-plugin'); ?>
<br /><br />
<a href="https://www.trustindex.io/?a=sys&c=wp-getmore-nfc&url=/features-list/collect-reviews/nfc-cards/" target="_blank" class="ti-btn"><?php echo __('Order Your NFC Cards Today!', 'trustindex-plugin'); ?></a>
</div>
<div class="ti-promobox-image">
<img src="<?php echo $pluginManagerInstance->get_plugin_file_url('static/img/nfc-card-sm.png'); ?>" />
</div>
</div>
</div>
<div class="ti-box ti-promobox-with-image ti-qr-code-promo">
<div class="ti-box-header">3. <?php echo __('QR Codes', 'trustindex-plugin'); ?></div>
<div class="ti-promobox-body">
<div class="ti-promobox-text">
<?php echo __('Make reviewing quick and convenient for your customers!', 'trustindex-plugin'); ?>
<br />
<?php echo __('QR codes offer a seamless and effective way to gather customer feedback.', 'trustindex-plugin'); ?>
<br /><br />
<?php echo __('Our smartphone-scannable QR codes guide your customers to your review platforms in just a few seconds. Print and display them in your business to gather reviews daily on platforms like Google, Facebook, and more!', 'trustindex-plugin'); ?>
<br /><br />
<a href="https://www.trustindex.io/?a=sys&c=wp-getmore-qr&url=/features-list/collect-reviews/qr-codes/" target="_blank" class="ti-btn"><?php echo __('Create Your Custom QR Code!', 'trustindex-plugin'); ?></a>
</div>
<div class="ti-promobox-image">
<img src="<?php echo $pluginManagerInstance->get_plugin_file_url('static/img/sample-qr.jpg'); ?>" />
</div>
</div>
</div>
<?php if (class_exists('Woocommerce')): ?>
<div class="ti-box ti-promobox-with-image">
<div class="ti-box-header">4. <?php echo __('WooCommerce Integrations', 'trustindex-plugin'); ?></div>
<div class="ti-promobox-body">
<div class="ti-promobox-text">
<?php echo __('Automate your review collection on WooCommerce! Collect customer feedback on Google, Facebook, Yelp, and more, directly from your WooCommerce store.', 'trustindex-plugin'); ?>
<br /><br />
<?php echo __('This tool enables automated review requests after purchases, ensuring a steady flow of feedback for your business. Seamlessly integrate your online shop with Trustindex and watch the reviews roll in without lifting a finger.', 'trustindex-plugin'); ?>
<br /><br />
<a href="https://wordpress.org/plugins/customer-reviews-collector-for-woocommerce/" target="_blank" class="ti-btn"><?php echo __('Explore Our WooCommerce Integrations Now!', 'trustindex-plugin'); ?></a>
</div>
<div class="ti-promobox-image">
<img src="<?php echo $pluginManagerInstance->get_plugin_file_url('static/img/woocommerce-logo.png'); ?>" />
</div>
</div>
</div>
<?php endif; ?>
