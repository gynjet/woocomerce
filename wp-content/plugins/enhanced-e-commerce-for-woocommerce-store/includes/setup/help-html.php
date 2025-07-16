<?php
function get_connect_google_popup_html_to_active_licence()
{
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    return '<div class="modal fade popup-modal overlay" id="tvc_google_connect_active_licence">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body">
          <h5 class="modal-title" id="staticBackdropLabel">' . esc_html__("Connect Tatvic with your website to active licence key", "enhanced-e-commerce-for-woocommerce-store") . '</h5>
          <button type="button" id="tvc_google_connect_active_licence_close" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
          </button>
          <br>              
          <p>' . esc_html__("Make sure you sign in with the google account that has all privileges to access google analytics, google ads and google merchant center account.", "enhanced-e-commerce-for-woocommerce-store") . '</p>
        </div>
        <div class="modal-footer">
          <a class="ee-oauth-container btn darken-4 white black-text" href="' . esc_url($TVC_Admin_Helper->get_onboarding_page_url()) . '" style="text-transform:none; margin: 0 auto;">
            <p style="font-size: inherit; margin-top:5px;"><img width="20px" style="margin-right:8px" alt="Google sign-in" src="' . esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/g-logo.png") . '" />' . esc_html__("Sign In With Google", "enhanced-e-commerce-for-woocommerce-store") . '</p>
          </a>
        </div>
      </div>
    </div>
  </div>';
}
function info_htnml($validation)
{
    if ($validation == true) {
        return wp_kses(
            enhancad_get_plugin_image('/admin/images/config-success.svg', 'configuration  success', 'config-success'),
            array(
                'img' => array(
                    'src' => true,
                    'alt' => true,
                    'class' => true,
                    'style' => true,
                ),
            )
        );
    } else {
        return wp_kses(
            enhancad_get_plugin_image('/admin/images/exclaimation.png', 'configuration  success', 'config-fail'),
            array(
                'img' => array(
                    'src' => true,
                    'alt' => true,
                    'class' => true,
                    'style' => true,
                ),
            )
        );
    }
}
function get_google_shopping_tabs_html($site_url, $google_merchant_center_id)
{
    $site_url_p = (isset($google_merchant_center_id) && $google_merchant_center_id != '') ? $site_url : "";
    $site_url_p_target = "";
    if (isset($google_merchant_center_id) && $google_merchant_center_id == '') {
        $site_url_p_target = 'data-toggle="modal" data-target="#tvc_google_connect"';
    }
    $tab = (isset($_GET['tab']) && sanitize_text_field(wp_unslash($_GET['tab']))) ? sanitize_text_field(wp_unslash($_GET['tab'])) : "";
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    ob_start();
?><ul class="nav nav-tabs nav-justified edit-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <div class="tvc-tooltip nav-link <?php echo ($tab == "gaa_config_page") ? "active" : ""; ?>">
                <a href="<?php echo esc_url($site_url . 'gaa_config_page'); ?>"
                    id="smart-shopping-campaigns"><?php echo esc_html__("Configuration", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
            </div>
        </li>
        <li class="nav-item" role="presentation">
            <div class="tvc-tooltip nav-link <?php echo ($tab == "sync_product_page") ? "active" : ""; ?>"
                <?php echo esc_attr($site_url_p_target); ?>>
                <a href="<?php echo ($site_url_p) ? esc_url($site_url_p . 'sync_product_page') : "#"; ?>"
                    id="smart-shopping-campaigns"><?php echo esc_html__("Product Sync", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
            </div>
        </li>
        <li class="nav-item" role="presentation">
            <div class="tvc-tooltip nav-link <?php echo ($tab == "shopping_campaigns_page") ? "active" : ""; ?>"
                <?php echo esc_attr($site_url_p_target); ?>>
                <a href="<?php echo esc_url('admin.php?page=conversios-pmax'); ?>"
                    id="smart-shopping-campaigns"><?php echo esc_html__("Performance Max", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
            </div>
        </li>
    </ul>
<?php
    return ob_get_clean();
}
function get_tvc_google_ads_help_html()
{
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    ob_start(); ?>
    <div class="right-content">
        <div class="content-section">
            <div class="content-icon">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/icon/information.svg', 'information'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>
            </div>
            <h4 class="content-heading"><?php esc_html_e("Help Center:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h4>
            <section class="tvc-help-slider">
                <?php if ($TVC_Admin_Helper->is_ga_property() == false) { ?>
                    <div>
                        <?php esc_html_e('In order to configure your Google Ads account, you need to sign in with the associated Google account. Click on "Get started"', 'enhanced-e-commerce-for-woocommerce-store'); ?>
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/icon/add.svg', 'connect account'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                        <?php esc_html_e("icon to set up the plugin.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </div>
                    <div>
                        <?php esc_html_e("Once you select or create a new google ads account, your account will be enabled for the following:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <ol>
                            <li><?php esc_html_e("Remarketing and dynamic remarketing tags for all the major eCommerce events on your website (Optional)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                            <li><?php esc_html_e("Your google ads account will be linked with the previously selected google analytics account (Optional)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                            <li><?php esc_html_e("Your google ads account will be linked with google merchant center account in the next step so that you can start running google shopping campaigns(Optional)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                        </ol>
                    </div>
                <?php } else { ?>
                    <div>
                        <?php esc_html_e("You can update or change the google ads account anytime by clicking on", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/icon/refresh.svg', 'refresh'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                        icon.
                    </div>
                <?php } ?>

            </section>
        </div>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item page-prev h-page-prev">
                    <span class="page-link">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/icon/arrow-down-sign-to-navigate.svg'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                    </span>
                </li>
                <li class="page-item"><span class="paging_info" id="paging_info">1</span></li>
                <li class="page-item page-next h-page-next">
                    <span class="page-link">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/icon/arrow-down-sign-to-navigate.svg'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                    </span>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        let rtl = <?php echo (is_rtl()) ? "true" : "false"; ?>;
        jQuery(".tvc-help-slider").slick({
            autoplay: false,
            dots: false,
            prevArrow: jQuery('.h-page-prev'),
            nextArrow: jQuery('.h-page-next'),
            rtl: rtl
        });
        jQuery(".tvc-help-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide) {
            jQuery("#paging_info").html(nextSlide + 1);
        });
    </script>
    <div class="right-content">
        <div class="content-section">
            <div class="content-icon">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/icon/lamp.svg', 'information'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>
            </div>
            <h4 class="content-heading"><?php esc_html_e("Business Value:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h4>
            <section class="tvc-b-value-slider">
                <div>
                    <?php esc_html_e("With dynamic remarketing tags, you will be able to show ads to your past site visitors with specific product information that is tailored to your customer’s previous site visits.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </div>
                <div>
                    <?php esc_html_e("This plugin enables dynamic remarketing tags for crucial eCommerce events like product list views, product detail page views, add to cart and final purchase event.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </div>
                <div>
                    <?php esc_html_e("Dynamic remarketing along with the product feeds in your merchant center account will enable you for smart shopping campaigns which is very essential for any eCommerce business globally. ", "enhanced-e-commerce-for-woocommerce-store"); ?><a
                        target="_blank"
                        href="<?php echo esc_url("https://support.google.com/google-ads/answer/3124536?hl=en"); ?>"><?php esc_html_e("Learn More", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                </div>
            </section>
        </div>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item page-prev b-page-prev">
                    <span class="page-link">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/icon/arrow-down-sign-to-navigate.svg'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                    </span>
                </li>
                <li class="page-item"><span class="paging_info" id="b-paging-info">1</span></li>
                <li class="page-item page-next b-page-next">
                    <span class="page-link">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/icon/arrow-down-sign-to-navigate.svg'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                    </span>
                </li>
            </ul>
        </nav>
    </div>
    <div class="tvc-footer-links">
        <a target="_blank" href="<?php echo esc_url("https://conversios.io/help-center/Installation-Manual.pdf"); ?>"
            tabindex="0"><?php esc_html_e("Installation manual", "enhanced-e-commerce-for-woocommerce-store"); ?></a> | <a
            target="_blank" href="<?php echo esc_url("https://conversios.io/help-center/Google-shopping-Guide.pdf"); ?>"
            tabindex="0"><?php esc_html_e("Google shopping guide", "enhanced-e-commerce-for-woocommerce-store"); ?></a> | <a
            target="_blank"
            href="<?php echo esc_url("https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/"); ?>"
            tabindex="0"><?php esc_html_e("FAQ", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
    </div>
    <script>
        //let rtl= "<?php echo (is_rtl()) ? "true" : "false"; ?>"; 
        jQuery(".tvc-b-value-slider").slick({
            autoplay: false,
            dots: false,
            prevArrow: jQuery('.b-page-prev'),
            nextArrow: jQuery('.b-page-next'),
            rtl: rtl
        });
        jQuery(".tvc-b-value-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide) {
            jQuery("#b-paging-info").html(nextSlide + 1);
        });
    </script>
<?php
    return ob_get_clean();
}
function get_tvc_help_html()
{
    ob_start(); ?>
    <div class="right-content">
        <div class="content-section">
            <div class="content-icon">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/icon/information.svg', 'information'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>
            </div>
            <h4 class="content-heading"><?php esc_html_e("Help Center:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h4>
            <section class="tvc-help-slider">
                <div>
                    <?php esc_html_e("Set up your Google Merchant Center Account and make your WooCommerce shop and products available to millions of shoppers across Google.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </div>
                <div>
                    <?php esc_html_e("Our plugin will help you automate everything you need to make your products available to interested customers across Google.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </div>
                <div>Follow <a target="_blank"
                        href="<?php echo esc_url("https://support.google.com/merchants/answer/6363310?hl=en&ref_topic=3163841"); ?>"><?php esc_html_e("merchant center guidelines for site requirements", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                    <?php esc_html_e("in order to avoid account suspension issues.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </div>
            </section>
        </div>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item page-prev h-page-prev">
                    <span class="page-link"><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/icon/arrow-down-sign-to-navigate.svg'); ?>"
                            alt="" /></span>
                </li>
                <li class="page-item"><span class="paging_info" id="paging_info">1</span></li>
                <li class="page-item page-next h-page-next">
                    <span class="page-link"><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/icon/arrow-down-sign-to-navigate.svg'); ?>"
                            alt="" /></span>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        let rtl = <?php echo (is_rtl()) ? "true" : "false"; ?>;
        jQuery(".tvc-help-slider").slick({
            autoplay: false,
            dots: false,
            prevArrow: jQuery('.h-page-prev'),
            nextArrow: jQuery('.h-page-next'),
            rtl: rtl
        });
        jQuery(".tvc-help-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide) {
            jQuery("#paging_info").html(nextSlide + 1);
        });
    </script>
    <div class="right-content">
        <div class="content-section">
            <div class="content-icon">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/icon/lamp.svg', 'information'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>
            </div>
            <h4 class="content-heading"><?php esc_html_e("Business Value:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h4>
            <section class="tvc-b-value-slider">
                <div>
                    <?php esc_html_e("Opt your product data into programmes, like surfaces across Google, Shopping ads, local inventory ads and Shopping Actions, to highlight your products to shoppers across Google.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </div>
                <div>
                    <?php esc_html_e(" store’s products will be eligible to get featured under the shopping tab when anyone searches for products that match your store’s product attributes.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </div>
                <div>
                    <?php esc_html_e("Reach out to customers leaving your store by running smart shopping campaigns based on their past site behavior.", "enhanced-e-commerce-for-woocommerce-store"); ?><a
                        target="_blank"
                        href="<?php echo esc_url("https://www.google.com/intl/en_in/retail/?fmp=1&utm_id=bkws&mcsubid=in-en-ha-g-mc-bkws"); ?>"><?php esc_html_e("Learn More", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                </div>
            </section>
        </div>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item page-prev b-page-prev">
                    <span class="page-link"><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/icon/arrow-down-sign-to-navigate.svg'); ?>"
                            alt="" /></span>
                </li>
                <li class="page-item"><span class="paging_info" id="b-paging-info">1</span></li>
                <li class="page-item page-next b-page-next">
                    <span class="page-link"><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/icon/arrow-down-sign-to-navigate.svg'); ?>"
                            alt="" /></span>
                </li>
            </ul>
        </nav>
    </div>
    <div class="tvc-footer-links">
        <a target="_blank" href="<?php echo esc_url("https://conversios.io/help-center/Installation-Manual.pdf"); ?>"
            tabindex="0"><?php esc_html_e("Installation manual", "enhanced-e-commerce-for-woocommerce-store"); ?></a> | <a
            target="_blank" href="<?php echo esc_url("https://conversios.io/help-center/Google-shopping-Guide.pdf"); ?>"
            tabindex="0"><?php esc_html_e("Google shopping guide", "enhanced-e-commerce-for-woocommerce-store"); ?></a> | <a
            target="_blank"
            href="<?php echo esc_url("https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/"); ?>"
            tabindex="0"><?php esc_html_e("FAQ", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
    </div>
    <script>
        //let rtl= "<?php echo (is_rtl()) ? "true" : "false"; ?>"; 
        jQuery(".tvc-b-value-slider").slick({
            autoplay: false,
            dots: false,
            prevArrow: jQuery('.b-page-prev'),
            nextArrow: jQuery('.b-page-next'),
            rtl: rtl
        });
        jQuery(".tvc-b-value-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide) {
            jQuery("#b-paging-info").html(nextSlide + 1);
        });
    </script>
<?php
    return ob_get_clean();
}
function get_tvc_google_ga_sidebar()
{
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    ob_start(); ?>
    <div class="right-content">
        <div class="content-section">
            <div class="content-icon">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/icon/information.svg', 'information'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>
            </div>
            <h4 class="content-heading"><?php esc_html_e("Help Center:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h4>
            <section class="tvc-help-slider">
                <?php if ($TVC_Admin_Helper->is_ga_property() == false) { ?>
                    <div>
                        <?php esc_html_e("In order to configure your Google Analytics account, you need to sign in with the associated Google account. Click on \"Get started\" ", "enhanced-e-commerce-for-woocommerce-store"); ?><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/icon/add.svg"); ?>"
                            alt="connect account" />
                        <?php esc_html_e("icon to set up the plugin.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </div>
                    <div>
                        <?php esc_html_e("Once you sign in with an associated google account, you will be asked to select a google analytics account from the drop down.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </div>
                    <div>
                        <?php esc_html_e("If you have already added the gtag.js snippet manually, YOU MUST uncheck the “add gtag.js”", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </div>
                <?php
                } else {
                ?>
                    <div>
                        <?php esc_html_e("You can update or change the google analytics account anytime by clicking on", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/icon/refresh.svg', 'refresh'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                        icon.
                    </div>
                <?php
                } ?>
            </section>
        </div>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item page-prev h-page-prev help-ga-prev">
                    <span class="page-link"><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/icon/arrow-down-sign-to-navigate.svg'); ?>"
                            alt="" /></span>
                </li>
                <li class="page-item"><span class="paging_info" id="help_ga_paging_info">1</span></li>
                <li class="page-item page-next h-page-next help-ga-next">
                    <span class="page-link"><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/icon/arrow-down-sign-to-navigate.svg'); ?>"
                            alt="" /></span>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        jQuery(".tvc-help-slider").slick({
            autoplay: false,
            dots: false,
            prevArrow: jQuery('.help-ga-prev'),
            nextArrow: jQuery('.help-ga-next')
        });
        jQuery(".tvc-help-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide) {
            jQuery("#help_ga_paging_info").html(nextSlide + 1);
        });
    </script>
    <div class="right-content">
        <div class="content-section">
            <div class="content-icon">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/icon/lamp.svg', 'information'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>
            </div>
            <h4 class="content-heading"> <?php esc_html_e("Business Value:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h4>
            <section class="tvc-b-value-slider">
                <div>
                    <p> <?php esc_html_e("Once you set up google analytics account, your website will be tagged for all the important eCommerce events in google analytics and you will be able to start taking data driven decisions in order to scale your eCommerce business faster. Some of the important data points are:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <ol>
                        <li> <?php esc_html_e("What exactly is your site’s conversion rate?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </li>
                        <li> <?php esc_html_e("What is the exact drop at each stage in your eCommerce funnel? For example, 100 people are coming to your website, how many users are seeing any product detail page, how many are adding products to cart, how many are abandoning cart etc.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </li>
                    </ol>
                </div>
                <div>
                    <?php esc_html_e("What all are your star products and what all are just consuming the space in your website?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </div>
            </section>
        </div>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item page-prev b-page-prev value-ga-prev">
                    <span class="page-link"><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/icon/arrow-down-sign-to-navigate.svg'); ?>"
                            alt="" /></span>
                </li>
                <li class="page-item"><span class="paging_info" id="value_ga_paging_info">1</span></li>
                <li class="page-item page-next b-page-next value-ga-next">
                    <span class="page-link"><img
                            src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/icon/arrow-down-sign-to-navigate.svg'); ?>"
                            alt="" /></span>
                </li>
            </ul>
        </nav>
    </div>
    <div class="tvc-footer-links">
        <a target="_blank" href="<?php echo esc_url("https://conversios.io/help-center/Installation-Manual.pdf"); ?>"
            tabindex="0"> <?php esc_html_e("Installation manual", "enhanced-e-commerce-for-woocommerce-store"); ?></a> | <a
            target="_blank" href="<?php echo esc_url("https://conversios.io/help-center/Google-shopping-Guide.pdf"); ?>"
            tabindex="0"> <?php esc_html_e("Google shopping guide", "enhanced-e-commerce-for-woocommerce-store"); ?></a> | <a
            target="_blank"
            href="<?php echo esc_url("https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/"); ?>"
            tabindex="0"> <?php esc_html_e("FAQ", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
    </div>
    <script>
        jQuery(".tvc-b-value-slider").slick({
            autoplay: false,
            dots: false,
            prevArrow: jQuery('.value-ga-prev'),
            nextArrow: jQuery('.value-ga-next')
        });
        jQuery(".tvc-b-value-slider").on("beforeChange", function(event, slick, currentSlide, nextSlide) {
            jQuery("#value_ga_paging_info").html(nextSlide + 1);
        });
    </script>
<?php
    return ob_get_clean();
}
