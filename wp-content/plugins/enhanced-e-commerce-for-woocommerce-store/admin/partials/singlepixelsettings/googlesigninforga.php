<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly 
?>
<!-- Google signin -->
<div class="pp-modal onbrd-popupwrp" id="tvc_google_signin_ga" tabindex="-1" role="dialog">
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
                    <?php if (!isset($tvc_data['g_mail']) || $tvc_data['g_mail'] == "" || $subscription_id == "") { ?>
                        <div class="google_connect_url_ga google-btn">
                            <?php echo wp_kses(
                                enhancad_get_plugin_image('/admin/images/logos/btn_google_signin_dark_normal_web.png'),
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
                    <?php } else { ?>
                        <?php if ($is_refresh_token_expire == true) { ?>
                            <p class="alert alert-primary">
                                <?php esc_html_e("It seems the token to access your Google accounts is expired. Sign in again to continue.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </p>
                            <div class="google_connect_url_ga google-btn">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/btn_google_signin_dark_normal_web.png'),
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
                        <?php } else { ?>
                            <div class="google_connect_url_ga google-btn">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/btn_google_signin_dark_normal_web.png'),
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
                        <?php } ?>
                    <?php } ?>
                    <p>
                        <?php esc_html_e("Make sure you sign in with the google email account that has all privileges to access google analytics, google ads and google merchant center account that you want to configure for your store.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                </div>
                <div class="google_signin_sec_right">
                    <h6>
                        <?php esc_html_e("Why do I need to sign in with google?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h6>
                    <p>
                        <?php esc_html_e("When you sign in with Google, we ask for limited programmatic access for your accounts in order to automate below features for you:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong>
                            <?php esc_html_e("1. Google Analytics:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </strong>
                        <?php esc_html_e("To give you option to select GA accounts, to show actionable google analytics reports in plugin dashboard and to link your google ads account with google analytics account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong>
                            <?php esc_html_e("2. Google Ads:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </strong>
                        <?php esc_html_e("To automate dynamic remarketing, conversion and enhanced conversion tracking and to create performance campaigns if required.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong>
                            <?php esc_html_e("3. Google Merchant Center:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </strong>
                        <?php esc_html_e("To automate product feed using content api and to set up your GMC account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>

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
        let subscription_id = "<?php echo esc_attr($subscription_id); ?>";
        let plan_id = "<?php echo esc_attr($plan_id); ?>";
        let app_id = "<?php echo esc_attr($app_id); ?>";
        let bagdeVal = "yes";
        let ga4_acc_val = jQuery('#ga4_acc_val').val();
        let googleAds = jQuery('#googleAds').val();
        let gmc_field = jQuery('#gmc_field').val();

        //open google signin popup
        jQuery(".tvc_google_signinbtn_ga, .google-auth-btn-highlighted").on("click", function() {
            jQuery('#tvc_google_signin_ga').addClass('showpopup');
            jQuery('body').addClass('scrlnone');
        });

        jQuery(".google_connect_url_ga").on("click", function() {
            const w = 800;
            const h = 650;
            const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
            const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

            const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            const systemZoom = width / window.screen.availWidth;
            const left = (width - w) / 2 / systemZoom + dualScreenLeft;
            const top = (height - h) / 2 / systemZoom + dualScreenTop;

            let urlforga = '<?php echo esc_url($connect_url_gagads); ?>';

            var selected_tabb = jQuery(".pawizard_tab_but.active").attr("id");
            urlforga = '<?php echo esc_url($connect_url_gaa); ?>';
            urlforga = urlforga.replace(/&amp;/g, '&');
            urlforga = urlforga.replaceAll('&#038;', '&');

            //console.log("urlforga", urlforga);
            const newWindow = window.open(urlforga, "newwindow", config = `scrollbars=yes,
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