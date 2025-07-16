<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (array_key_exists("g_mail", $tvc_data) && sanitize_email($tvc_data["g_mail"]) && isset($_GET['subscription_id']) && sanitize_text_field(wp_unslash($_GET['subscription_id']))) {
    update_option('ee_customer_gmail', sanitize_email($tvc_data["g_mail"]));
    $eeapidata = unserialize(get_option('ee_api_data'));
    $eeapidata_settings = new stdClass();

    if (!empty($eeapidata['setting'])) {
        $eeapidata_settings = $eeapidata['setting'];
    }

    $eeapidata['setting'] = $eeapidata_settings;
    update_option('ee_api_data', serialize($eeapidata));


    // $eeapidata['setting'] = $eeapidata_settings;
    // update_option('ee_api_data', serialize($eeapidata));

    //is not work for existing user && $ee_additional_data['con_created_at'] != "" 
    if (isset($ee_additional_data['con_created_at'])) {
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['con_updated_at'] = gmdate('Y-m-d');
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
    } else {
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['con_created_at'] = gmdate('Y-m-d');
        $ee_additional_data['con_updated_at'] = gmdate('Y-m-d');
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
    }
}
$sub_page = (isset($_GET['subpage'])) ? sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'subpage'))) : "";
?>

<div class="convwiz_pixtitle mt-0 mb-3 d-flex justify-content-between align-items-center py-0">
    <div class="col-7">
        <?php if ($sub_page == "gasettings") { ?>
            <ul class="conv-green-checklis list-unstyled mt-3">
                <li class="d-flex">
                    <span class="material-symbols-outlined text-success md-18">
                        check_circle
                    </span>
                    <?php esc_html_e("All the e-commerce event tracking including Purchase", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="page_view, purchase, view_item_list, view_item, select_item, add_to_cart, remove_from_cart, view_cart, begin_checkout, add_payment_info, and add_shipping_info.">
                        info
                    </span>
                </li>
                <li class="d-flex">
                    <span class="material-symbols-outlined text-success md-18">
                        check_circle
                    </span>
                    <?php esc_html_e("All the lead generation event tracking including Form Submit", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Lead Form Submit, Lead Email Click, Lead Phone Click, Page Scroll, File Download, Author, Login, Signup">
                        info
                    </span>
                </li>
            </ul>
        <?php } ?>
        <?php if ($sub_page == "gadssettings") { ?>
            <ul class="conv-green-checklis list-unstyled mt-3">
                <li class="d-flex">
                    <span class="material-symbols-outlined text-success md-18">
                        check_circle
                    </span>
                    <?php esc_html_e("Google Ads Purchase & Lead Generation Conversion Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </li>
                <li class="d-flex">
                    <span class="material-symbols-outlined text-success md-18">
                        check_circle
                    </span>
                    <?php esc_html_e("Easy-to-Set-Up Google Ads Pmax Campaign Creation", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-html="true" data-bs-original-title="<b>What is Pmax campaigns? </b><br>Performance max campaign are like a personal shopper for your WooCommerce store, automatically finding the best customers and showing them your ads at the perfect time.">
                        info
                    </span>
                </li>
            </ul>
        <?php } ?>
        <?php if ($sub_page == "gmcsettings") { ?>
            <ul class="conv-green-checklis list-unstyled mt-3">
                <li class="d-flex">
                    <span class="material-symbols-outlined text-success md-18">
                        check_circle
                    </span>
                    <?php esc_html_e("Showcase your products on Google Shopping", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </li>
            </ul>
        <?php } ?>

    </div>
    <div class="col convgauthcol">
        <div class="convpixsetting-inner-box ps-3" style="border-left: 3px solid #09bd83;">
            <?php
            $g_email =  (isset($tvc_data['g_mail']) && esc_attr($subscriptionId)) ? esc_attr($tvc_data['g_mail']) : "";
            ?>
            <?php if ($g_email != "") { ?>
                <h5 class="fw-normal mb-1">
                    <?php esc_html_e("Successfully signed in with account:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h5>
                <span>
                    <?php echo (isset($tvc_data['g_mail']) && esc_attr($subscriptionId)) ? esc_attr($tvc_data['g_mail']) : ""; ?>
                    <span class="conv-link-blue ps-0 tvc_google_signinbtn">
                        <?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </span>
                </span>
            <?php } else { ?>

                <div class="tvc_google_signinbtn_box" style="width: 185px;">
                    <div class="tvc_google_signinbtn google-btn">
                        <div class="google-icon-wrapper">
                            <?php echo wp_kses(
                                enhancad_get_plugin_image('/admin/images/g-logo.png', '', 'google-icon'),
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
                        <p class="btn-text"><b><?php esc_html_e("Sign in with google", "enhanced-e-commerce-for-woocommerce-store"); ?></b></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>


<!-- Google signin -->
<div class="pp-modal onbrd-popupwrp" id="tvc_google_signin" tabindex="-1" role="dialog">
    <div class="onbrdppmain" role="document">
        <div class="onbrdnpp-cntner acccretppcntnr">
            <div class="onbrdnpp-hdr">
                <div class="ppclsbtn clsbtntrgr">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/close-icon.png'),
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
            </div>
            <div class="onbrdpp-body">
                <div class="h6 py-2 px-1" style="background: #d7ffd7;">Please use Chrome browser to configure the plugin if you face any issues during setup.</div>
                <div class="google_signin_sec_left">
                    <?php if (!isset($tvc_data['g_mail']) || $tvc_data['g_mail'] == "" || $subscriptionId == "") { ?>
                        <div class="google_connect_url google-btn">
                            <div class="google-icon-wrapper">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/g-logo.png', '', 'google-icon'),
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
                            <p class="btn-text"><b><?php esc_html_e("Sign in with google", "enhanced-e-commerce-for-woocommerce-store"); ?></b></p>
                        </div>
                    <?php } else { ?>
                        <?php if ($is_refresh_token_expire == true) { ?>
                            <p class="alert alert-primary"><?php esc_html_e("It seems the token to access your Google accounts is expired. Sign in again to continue.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                            <div class="google_connect_url google-btn">
                                <div class="google-icon-wrapper">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/g-logo.png', '', 'google-icon'),
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
                                <p class="btn-text"><b><?php esc_html_e("Sign in with google", "enhanced-e-commerce-for-woocommerce-store"); ?></b></p>
                            </div>
                        <?php } else { ?>
                            <div class="google_connect_url google-btn">
                                <div class="google-icon-wrapper">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/g-logo.png', '', 'google-icon'),
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
                                <p class="btn-text mr-35"><b><?php esc_html_e("Reauthorize", "enhanced-e-commerce-for-woocommerce-store"); ?></b></p>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <p><?php esc_html_e("Make sure you sign in with the google email account that has all privileges to access google analytics, google ads and google merchant center account that you want to configure for your store.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                </div>
                <div class="google_signin_sec_right">
                    <h6><?php esc_html_e("Why do I need to sign in with google?", "enhanced-e-commerce-for-woocommerce-store"); ?></h6>
                    <p><?php esc_html_e("When you sign in with Google, we ask for limited programmatic access for your accounts in order to automate below features for you:", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    <p><strong><?php esc_html_e("1. Google Analytics:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To give you option to select GA accounts, to show actionable google analytics reports in plugin dashboard and to link your google ads account with google analytics account.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    <p><strong><?php esc_html_e("2. Google Ads:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To automate dynamic remarketing, conversion and enhanced conversion tracking and to create performance campaigns if required.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    <p><strong><?php esc_html_e("3. Google Merchant Center:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To automate product feed using content api and to set up your GMC account.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function cov_save_badge_settings(bagdeVal) {
        var data = {
            action: "cov_save_badge_settings",
            bagdeVal: bagdeVal,
            conv_nonce: "<?php echo esc_js(wp_create_nonce('conv_nonce_val')); ?>"
        };
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            success: function(response) {
                // console.log(response);
                //do nothing
            }
        });
    }
    jQuery(function() {
        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
        var tvc_ajax_url = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        let subscription_id = "<?php echo esc_attr($subscriptionId); ?>";
        let plan_id = "<?php echo esc_attr($plan_id); ?>";
        let app_id = "<?php echo esc_attr(CONV_APP_ID); ?>";
        let bagdeVal = "yes";
        let convBadgeVal = "<?php echo esc_attr($convBadgeVal); ?>";

        let ua_acc_val = jQuery('#ua_acc_val').val();
        let ga4_acc_val = jQuery('#ga4_acc_val').val();
        //let propId = jQuery('#propId').val();
        //let measurementId = jQuery('#measurementId').val();
        let googleAds = jQuery('#googleAds').val();
        let gmc_field = jQuery('#gmc_field').val();
        //console.log("ua_acc_val",ua_acc_val);  
        //console.log("ga4_acc_val",ga4_acc_val);  
        //console.log("googleAds",googleAds);  
        //console.log("gmc_field",gmc_field);  

        //open google signin popup
        jQuery(".tvc_google_signinbtn").on("click", function() {
            jQuery('#tvc_google_signin').addClass('showpopup');
            jQuery('body').addClass('scrlnone');
            if (convBadgeVal == "") {
                cov_save_badge_settings("no");
            }
        });

        jQuery(".google_connect_url").on("click", function() {
            const w = 800;
            const h = 650;
            const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
            const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

            const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            const systemZoom = width / window.screen.availWidth;
            const left = (width - w) / 2 / systemZoom + dualScreenLeft;
            const top = (height - h) / 2 / systemZoom + dualScreenTop;
            var url = '<?php echo esc_url($connect_url); ?>';
            url = url.replace(/&amp;/g, '&');
            url = url.replaceAll('&#038;', '&');
            const newWindow = window.open(url, "newwindow", config = `scrollbars=yes,
			      width=${w / systemZoom}, 
			      height=${h / systemZoom}, 
			      top=${top}, 
			      left=${left},toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no
			      `);
            if (window.focus) newWindow.focus();
        });

        jQuery(".clsbtntrgr, .ppblubtn").on("click", function() {
            jQuery(this).closest('.onbrd-popupwrp').removeClass('showpopup');
            jQuery('body').removeClass('scrlnone');
        });

        jQuery('#conv_show_badge_onboardingCheck').change(function() {
            if (jQuery(this).prop("checked")) {
                jQuery("#badge_label_check").addClass("conv_default_cls_enabled");
                jQuery("#badge_label_check").removeClass("conv_default_cls_disabled");
                bagdeVal = "yes";
            } else {
                jQuery("#badge_label_check").addClass("conv_default_cls_disabled");
                jQuery("#badge_label_check").removeClass("conv_default_cls_enabled");
                bagdeVal = "no";
            }
            cov_save_badge_settings(bagdeVal); //saving badge settings
        });

    });
</script>