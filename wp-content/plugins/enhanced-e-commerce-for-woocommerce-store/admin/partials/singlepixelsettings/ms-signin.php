<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (array_key_exists("microsoft_mail", $tvc_data) && sanitize_email($tvc_data["microsoft_mail"]) && isset($_GET['subscription_id']) && sanitize_text_field(wp_unslash($_GET['subscription_id']))) {
    update_option('ee_customer_msmail', sanitize_email($tvc_data["microsoft_mail"]));

    if (array_key_exists("access_token", $tvc_data) && array_key_exists("refresh_token", $tvc_data)) {
        $eeapidata = unserialize(get_option('ee_api_data'));
        $eeapidata_settings = new stdClass();

        if (!empty($eeapidata['setting'])) {
            $eeapidata_settings = $eeapidata['setting'];
        }

        $eeapidata_settings->access_token = base64_encode(sanitize_text_field($tvc_data["access_token"]));
        $eeapidata_settings->refresh_token = base64_encode(sanitize_text_field($tvc_data["refresh_token"]));

        $eeapidata['setting'] = $eeapidata_settings;
        update_option('ee_api_data', serialize($eeapidata));
    }

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

        <?php if ($sub_page == "bingsettings") { ?>
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
                    <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="lead_form_submit. email_click, phone_click, address_click">
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
                    <?php esc_html_e("Microsoft Ads Purchase & Lead Generation Conversion Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </li>
                <li class="d-flex">
                    <span class="material-symbols-outlined text-success md-18">
                        check_circle
                    </span>
                    <?php esc_html_e("Easy-to-Set-Up Microsoft Ads Pmax Campaign Creation", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-html="true" data-bs-original-title="<b>What is Pmax campaigns? </b><br>Performance max campaign are like a personal shopper for your WooCommerce store, automatically finding the best customers and showing them your ads at the perfect time.">
                        info
                    </span>
                </li>
            </ul>
        <?php } ?>
        <?php if ($sub_page == "mmcsettings") { ?>
            <ul class="conv-green-checklis list-unstyled mt-3">
                <li class="d-flex">
                    <span class="material-symbols-outlined text-success md-18">
                        check_circle
                    </span>
                    <?php esc_html_e("Showcase your products on Bing Shopping", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </li>
                <!-- Showcase your products on Microsoft Ads -->
            </ul>
        <?php } ?>

    </div>

    <div class="col convgauthcol">
        <div class="convpixsetting-inner-box ps-3" style="border-left: 3px solid #09bd83;">
            <?php
            $g_email =  (isset($tvc_data['microsoft_mail']) && esc_attr($subscriptionId)) ? esc_attr($tvc_data['microsoft_mail']) : "";
            ?>
            <?php if ($g_email != "") { ?>
                <h5 class="fw-normal mb-1">
                    <?php esc_html_e("Successfully signed in with account:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h5>
                <span>
                    <?php echo (isset($tvc_data['microsoft_mail']) && esc_attr($subscriptionId)) ? esc_attr($tvc_data['microsoft_mail']) : ""; ?>

                    <?php if (isset($_GET['subpage']) && $_GET['subpage'] != "mmcsettings") { ?>
                        <span class="conv-link-blue ps-0 tvc_microsoft_signinbtn">
                            <?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </span>
                    <?php } ?>

                </span>
            <?php } else { ?>

                <div class="tvc_microsoft_signinbtn_box">
                    <div class="tvc_microsoft_signinbtn microsoft-btn d-flex align-items-center">
                        <div class="microsoft-icon-wrapper">
                            <?php echo wp_kses(
                                enhancad_get_plugin_image('/admin/images/logos/ms-logo.png', '', 'microsoft-icon', ''),
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
                        <div class="btn-text"><b><?php esc_html_e("Sign in with Microsoft", "enhanced-e-commerce-for-woocommerce-store"); ?></b></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>


<!-- Microsoft signin -->
<div class="pp-modal onbrd-popupwrp" id="tvc_microsoft_signin" tabindex="-1" role="dialog">
    <div class="onbrdppmain" role="document">
        <div class="onbrdnpp-cntner acccretppcntnr">
            <div class="onbrdnpp-hdr">
                <div class="ppclsbtn clsbtntrgr">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/close-icon.png', '', 'ppclsbtn clsbtntrgr', ''),
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
                <div class="microsoft_signin_sec_left">
                    <?php
                    $woo_currency = get_option('woocommerce_currency');
                    $timezone = get_option('timezone_string');

                    
                    $confirm_url = urlencode(string: "admin.php?page=conversios-google-analytics&subpage=bingsettings");
                    $ms_redirect_uri = TVC_API_CALL_URL_TEMP.'/auth/microsoft/callback';
                    $state = ['confirm_url' => admin_url() . $confirm_url, 'subscription_id' => $subscriptionId, 'ms_redirect_uri' => $ms_redirect_uri];
                    $microsoft_auth_url = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=892127ea-3496-4e25-8909-12705e629eae&response_type=code&redirect_uri=$ms_redirect_uri&response_mode=query&tenant=d6545bb5-03a2-461a-880a-14ce7ce63143&scope=openid email profile offline_access https://ads.microsoft.com/msads.manage User.Read&state=" . urlencode(wp_json_encode($state));


                    ?>
                    <?php if (!isset($tvc_data['microsoft_mail']) || $tvc_data['microsoft_mail'] == "" || $subscriptionId == "") { ?>
                        <div class="microsoft_connect_url microsoft-btn d-flex align-items-center" onclick='window.open("<?php echo esc_js(esc_url($microsoft_auth_url)); ?>","MyWindow","width=800,height=700,left=300, top=150"); return false;'>
                            <?php if (isset($ee_options['microsoft_ads_manager_id']) || isset($_GET['subscription_id'])) { ?>
                                <span>Change</span>
                            <?php } else { ?>
                                <div class="microsoft-icon-wrapper">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/ms-logo.png', '', 'microsoft-icon', ''),
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
                                <div class="btn-text"><b><?php esc_html_e("Sign in with Microsoft", "enhanced-e-commerce-for-woocommerce-store"); ?></b></div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <?php if ($is_refresh_token_expire == true) { ?>
                            <p class="alert alert-primary"><?php esc_html_e("It seems the token to access your Microsoft accounts is expired. Sign in again to continue.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                            <div class="microsoft_connect_url microsoft-btn d-flex align-items-center" onclick='window.open("<?php echo esc_js(esc_url($microsoft_auth_url)); ?>","MyWindow","width=800,height=700,left=300, top=150"); return false;'>
                                <div class="microsoft-icon-wrapper">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/ms-logo.png', '', 'microsoft-icon', ''),
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
                                <div class="btn-text"><b><?php esc_html_e("Sign in with Microsoft", "enhanced-e-commerce-for-woocommerce-store"); ?></b></div>
                            </div>
                        <?php } else { ?>
                            <div class="microsoft_connect_url microsoft-btn d-flex align-items-center" onclick='window.open("<?php echo esc_js(esc_url($microsoft_auth_url)); ?>","MyWindow","width=800,height=700,left=300, top=150"); return false;'>
                                <div class="microsoft-icon-wrapper">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/ms-logo.png', '', 'microsoft-icon', ''),
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
                                <div class="btn-text"><b><?php esc_html_e("Reauthorize Microsoft", "enhanced-e-commerce-for-woocommerce-store"); ?></b></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <p class="p-0 pe-2 pt-2"><?php esc_html_e("Make sure you sign in with the Microsoft email account that has all privileges to access  Microsoft Advertising account that you want to configure for your store.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                </div>
                <div class="microsoft_signin_sec_right">
                    <h6><?php esc_html_e("Why do I need to sign in with Microsoft?", "enhanced-e-commerce-for-woocommerce-store"); ?></h6>
                    <p><?php esc_html_e("When you sign in with Microsoft, we ask for limited programmatic access to your accounts in order to automate the following features for you:", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    <p><strong><?php esc_html_e("1. Microsoft Ads:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To automate dynamic remarketing, conversion tracking, enhanced conversion tracking, and to create performance campaigns as required.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    <p><strong><?php esc_html_e("2. Microsoft Merchant Center:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To automate product feed submission using the Content API and to set up your Merchant Center account.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
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
        let plan_id = "<?php echo (isset($plan_id)) ? esc_attr($plan_id) : ''; ?>";
        let app_id = "<?php echo esc_attr(CONV_APP_ID); ?>";
        let bagdeVal = "yes";
        let convBadgeVal = "<?php echo esc_attr($convBadgeVal); ?>";

        let ua_acc_val = jQuery('#ua_acc_val').val();
        let ga4_acc_val = jQuery('#ga4_acc_val').val();
        //let propId = jQuery('#propId').val();
        //let measurementId = jQuery('#measurementId').val();
        let bingAds = jQuery('#bingAds').val();
        let gmc_field = jQuery('#gmc_field').val();
        //console.log("ua_acc_val",ua_acc_val);  
        //console.log("ga4_acc_val",ga4_acc_val);  
        //console.log("bingAds",bingAds);  
        //console.log("gmc_field",gmc_field);  

        //open microsoft signin popup
        jQuery(".tvc_microsoft_signinbtn").on("click", function() {
            jQuery('#tvc_microsoft_signin').addClass('showpopup');
            jQuery('body').addClass('scrlnone');
            if (convBadgeVal == "") {
                cov_save_badge_settings("no");
            }
        });

        /*jQuery(".microsoft_connect_url").on("click", function() {
            const w = 800;
            const h = 650;
            const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
            const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

            const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            const systemZoom = width / window.screen.availWidth;
            const left = (width - w) / 2 / systemZoom + dualScreenLeft;
            const top = (height - h) / 2 / systemZoom + dualScreenTop;
            var url = '<?php //echo esc_url_raw($connect_url); 
                        ?>';
            url = url.replace(/&amp;/g, '&');
            url = url.replaceAll('&#038;', '&');
            console.log(url);
            const newWindow = window.open(url, "newwindow", config = `scrollbars=yes,
			      width=${w / systemZoom}, 
			      height=${h / systemZoom}, 
			      top=${top}, 
			      left=${left},toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no
			      `);
            if (window.focus) newWindow.focus();
        });*/

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