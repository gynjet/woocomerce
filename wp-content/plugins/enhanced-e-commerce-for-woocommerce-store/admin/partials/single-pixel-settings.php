<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

$pixel_settings_arr = array(
    "gtmsettings" => array(
        "logo" => "/admin/images/logos/conv_gtm_logo.png",
        "title" => "Google Tag Manager",
        "topnoti" => "Use your own Google Tag Manager account to increase the page speed and customize events as per your requirements."
    ),
    "gasettings" => array(
        "logo" => "/admin/images/logos/conv_ganalytics_logo.png",
        "title" => "Google Analytics 4",
        "topnoti" => "Universal Analytics (Google Analytics 3) Will no longer be available after July 1st 2023. A new GA4 property will automatically be created for you, and your universal Analytics configurations will be copied to the new GA4 property, unless you opt out."
    ),
    "gadssettings" => array(
        "logo" => "/admin/images/logos/conv_gads_logo.png",
        "title" => "Google Ads Remarketing & Conversion Tracking",
        "topnoti" => "Enabling Google Ads enhanced conversion along with Google Ads conversion tracking helps in campaign performance."
    ),
    "fbsettings" => array(
        "logo" => "/admin/images/logos/conv_meta_logo.png",
        "title" => "Facebook Pixel & Facebook Conversions API (Meta)",
        "topnoti" => "Enable FBCAPI along with FB pixel for higher accuracy and better campaign performance."
    ),
    "bingsettings" => array(
        "logo" => "/admin/images/logos/conv_bing_logo.png",
        "title" => "Microsoft Ads Pixel (Bing)",
    ),
    "bingclaritysettings" => array(
        "logo" => "/admin/images/logos/conv_clarity_logo.png",
        "title" => "Microsoft Clarity",
    ),
    "twittersettings" => array(
        "logo" => "/admin/images/logos/conv_twitter_logo.png",
        "title" => "Twitter Pixel",
    ),
    "pintrestsettings" => array(
        "logo" => "/admin/images/logos/conv_pint_logo.png",
        "title" => "Pinterest Pixel",
    ),
    "linkedinsettings" => array(
        "logo" => "/admin/images/logos/conv_linkedin_logo.png",
        "title" => "Linkedin Insight",
    ),
    "snapchatsettings" => array(
        "logo" => "/admin/images/logos/conv_snap_logo.png",
        "title" => "Snapchat Pixel",
    ),
    "tiktoksettings" => array(
        "logo" => "/admin/images/logos/conv_tiktok_logo.png",
        "title" => "TikTok Pixel",
    ),
    "customintgrationssettings" => array(
        "logo" => "/admin/images/logos/conv_event_track_custom.png",
        "title" => "Events Tracking - Custom Integration",
    ),
    "gmcsettings" => array(
        "logo" => "/admin/images/logos/conv_gmc_logo.png",
        "title" => "Google Merchant Center Account",
        "topnoti" => "Product feed to Google Merchant Center helps you improve your product's visibility in Google search results and helps to optimize your Google Campaigns resulting in high ROAS."
    ),
    "mmcsettings" => array(
        "logo" => "/admin/images/logos/ms-logo.png",
        "title" => "Microsoft Merchant Center Account",
        "topnoti" => "Product feed to Microsoft Merchant Center helps you improve your product's visibility in Microsoft search results and helps to optimize your Microsoft Campaigns resulting in high ROAS."
    ),
    "tiktokBusinessSettings" => array(
        "logo" => "/admin/images/logos/conv_tiktok_logo.png",
        "title" => "TikTok Business Account",
        "topnoti" => "Product feed to TikTok catalog help you to run ads on tiktok for your product and reach out to more than 900 Million people."
    ),
    "metasettings" => array(
        "logo" => "/admin/images/logos/conv_meta_logo.png",
        "title" => "Facebook Business Account",
        "topnoti" => "Seamlessly sync and link your WooCommerce store to your FB catalog for targeted advertising, maximizing visibility and engagement through powerful FB ads. Elevate your business presence!"
    ),
    "hotjarsettings" => array(
        "logo" => "/admin/images/logos/conv_hotjar_logo.png",
        "title" => "Hotjar Pixel",
    ),
    "crazyeggsettings" => array(
        "logo" => "/admin/images/logos/conv_crazyegg_logo.png",
        "title" => "Crazyegg Pixel",
    ),
);

//$mmcsettings = ''; // mmcsettings.php

$subpage = (isset($_GET["subpage"]) && $_GET["subpage"] != "") ? esc_attr(sanitize_text_field(wp_unslash($_GET['subpage']))) : "";
$version = PLUGIN_TVC_VERSION;

$googleDetail = "";
$tracking_option = "UA";
$login_customer_id = "";

$TVC_Admin_Helper = new TVC_Admin_Helper();
$customApiObj = new CustomApi();
$app_id = CONV_APP_ID;
//get user data
$ee_options = $TVC_Admin_Helper->get_ee_options_settings();
$ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
$get_ee_options_data = $TVC_Admin_Helper->get_ee_options_data();
$tvc_data = $TVC_Admin_Helper->get_store_data();

$subscriptionId = $ee_options['subscription_id'];

$url = $TVC_Admin_Helper->get_onboarding_page_url();
$is_refresh_token_expire = false;

//get badge settings
$convBadgeVal = isset($ee_options['conv_show_badge']) ? $ee_options['conv_show_badge'] : "";
$convBadgePositionVal = isset($ee_options['conv_badge_position']) ? $ee_options['conv_badge_position'] : "";

// for google
$g_mail = get_option('ee_customer_gmail');
$tvc_data['g_mail'] = "";
if ($g_mail) {
    $tvc_data['g_mail'] = sanitize_email($g_mail);
}

// for microsoft
$microsoft_mail = get_option('ee_customer_msmail');
$tvc_data['microsoft_mail'] = "";
if ($microsoft_mail) {
    $tvc_data['microsoft_mail'] = sanitize_email($microsoft_mail);
}

//check if redirected from the authorization
if (isset($_GET['subscription_id']) && sanitize_text_field(wp_unslash($_GET['subscription_id']))) {

    $subscriptionId = sanitize_text_field(wp_unslash($_GET['subscription_id']));

    // for google
    if (isset($_GET['g_mail']) && sanitize_email(wp_unslash($_GET['g_mail']))) {
        $tvc_data['g_mail'] = sanitize_email(wp_unslash($_GET['g_mail']));
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['ee_last_login'] = sanitize_text_field(current_time('timestamp'));
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
        $is_refresh_token_expire = false;
    }

    // for microsoft    
    if (isset($_GET['microsoft_mail']) && sanitize_email(wp_unslash($_GET['microsoft_mail']))) {
        $tvc_data['microsoft_mail'] = sanitize_email(wp_unslash($_GET['microsoft_mail']));
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['ee_last_login'] = sanitize_text_field(current_time('timestamp'));
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
        $is_refresh_token_expire = false;
    }
}

$resource_center_data = array();
//get account settings from the api
if ($subscriptionId != "") {
    $google_detail = $customApiObj->getGoogleAnalyticDetail($subscriptionId);

    if (property_exists($google_detail, "error") && $google_detail->error == false) {
        if (property_exists($google_detail, "data") && $google_detail->data != "") {
            $googleDetail = $google_detail->data;
            $tvc_data['subscription_id'] = $googleDetail->id;
            $plan_id = $googleDetail->plan_id;
            $login_customer_id = $googleDetail->customer_id;
            $tracking_option = $googleDetail->tracking_option;
            if ($googleDetail->tracking_option != '') {
                $defaulSelection = 0;
            }
        }
    }
}
?>
<!-- Main container -->
<div class="container-old conv-container conv-setting-container pt-4">
    <!-- Main row -->
    <div class="row justify-content-center gx-0">
        <!-- Main col8 center -->
        <div class="col-xs-12 row convfixedcontainerfull m-0 p-0">

            <div class="col-md-12 g-0">
                <!-- Pixel setting header -->
                <div class="conv_pixel_settings_head d-flex flex-row mt-0 align-items-center mb-3">
                    <a href="<?php echo esc_url('admin.php?page=conversios-google-analytics'); ?>" class="link-dark rounded-3 border border-2 hreflink">
                        <span class="material-symbols-outlined p-1">arrow_back</span>
                    </a>
                    <div class="ms-4 ps-1">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image($pixel_settings_arr[$subpage]['logo']),
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
                    <h4 class="m-0 fw-normal ms-2 fw-bold-500">
                        <?php echo esc_html($pixel_settings_arr[$subpage]['title']); ?>
                    </h4>
                    <button class="btn text-white ms-auto d-flex justify-content-center conv-btn-connect conv-btn-connect-disabled" style="width:110px">Save</button>
                </div>
                <!-- Pixel setting header end-->

                <!-- Pixel setting body -->

                <div id="loadingbar_blue" class="progress-materializecss d-none">
                    <div class="indeterminate"></div>
                </div>
                <?php
                if (array_key_exists($subpage, $pixel_settings_arr)) {
                    require_once("singlepixelsettings/" . $subpage . '.php');
                }
                ?>
                <!-- Pixel setting body end -->
            </div>
        </div>
        <!-- Main col8 center End-->
    </div>
    <!-- Main row End -->

</div>
<!-- Main container End -->


<!-- Success Save Modal 3 -->
<div class="modal fade" id="conv_save_success_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-600">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center px-5">
                <div class="success-round d-flex rounded-circle justify-content-center align-items-center border-radius">
                    <span class="material-symbols-outlined text-white  fww-bold">check</span>
                </div>
                <h2 class="fw-normal pt-3 text-dark"><?php esc_html_e("Successful!", "enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                <h3 class="leave-a-review fw-normal mb-4 text-dark" style="display:none">
                    <?php esc_html_e("How did you like our onboarding setup? Any feedback is appreciated! ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <a target="_blank" href="https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store/reviews/?rate=5#rate-response" class="conv-link-blue">Leave a Review</a>
                </h3>
                <span id="conv_save_success_txt" class="mb-1 d-flex justify-content-center text-dark fs-16 px-2"></span>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 mb-1 modalFooterSuccess" style="display:flex; justify-content: center">
                <button id="conv-modal-redirect-btn" class="btn fs-20 fw-normal text-white dismissModal btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                <?php
                $sub_page = filter_input(INPUT_GET, 'subpage', FILTER_DEFAULT);
                if ($sub_page == "gasettings") {
                ?>
                    <a href="<?php echo esc_url('admin.php?page=conversios-analytics-reports'); ?>" class="btn fs-20 fw-normal text-white btn-success px-4">Reports</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<!-- Success Save Modal End -->


<script>
    //Other then GTM,GA,GAds
    function change_top_button_state(state = "enable") {
        if (state == "enable" && !jQuery("form#pixelsetings_form input").hasClass("conv-border-danger")) {
            jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled");
            jQuery(".conv-btn-connect").text('Save');
        }

        if (state == "disable") {
            jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
            jQuery(".conv-btn-connect").removeClass("conv-btn-connect-enabled");
            jQuery(".conv-btn-connect").text('Save');
        }
    }

    function conv_change_loadingbar(state = 'show') {
        if (state == 'show') {
            jQuery("#loadingbar_blue").removeClass('d-none');
        } else {
            jQuery("#loadingbar_blue").addClass('d-none');
        }
    }

    function getAlertMessageAll(type = 'Success', title = 'Success', message = '', icon = 'success', buttonText =
        'Done!', buttonColor = '#1967D2', iconImageTag = '') {

        Swal.fire({
            type: type,
            icon: icon,
            title: title,
            confirmButtonText: buttonText,
            confirmButtonColor: buttonColor,
            text: message,
        })
        let swalContainer = Swal.getContainer();
        jQuery(swalContainer).find('.swal2-icon-show').removeClass('swal2-' + icon).removeClass('swal2-icon').addClass(
            'justify-content-center')
        jQuery('.swal2-icon-show').html(iconImageTag)

    }
    //On page load logics
    jQuery(function() {
        var tvc_ajax_url = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        let subscription_id = "<?php echo esc_attr($subscriptionId); ?>";

        //initilize select2 for the inner screens
        jQuery(".selecttwo").select2({
            minimumResultsForSearch: -1,
            placeholder: function() {
                jQuery(this).data('placeholder');
            }
        });

        // Show tootltip on click
        jQuery('a[data-bs-toggle="tooltip"]').tooltip({
            trigger: 'click'
        });

        //For tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });


        // Enable save button on form change
        jQuery(document).on("change", "form#pixelsetings_form", function() {
            change_top_button_state("enable");
        });

        // Client side pixel id validations
        jQuery(document).on("input",
            "#fb_pixel_id, #twitter_ads_pixel_id, #pinterest_ads_pixel_id, #snapchat_ads_pixel_id, #tiKtok_ads_pixel_id, #hotjar_pixel_id, #crazyegg_pixel_id",
            function() {
                var ele_id = this.id;
                var ele_val = jQuery(this).val();
                var regex_arr = {
                    fb_pixel_id: new RegExp(/^\d{14,16}$/m),
                    microsoft_ads_pixel_id: new RegExp(/^\d{7,9}$/m),
                    twitter_ads_pixel_id: new RegExp(/^[a-z0-9]{5,7}$/m),
                    pinterest_ads_pixel_id: new RegExp(/^\d{13}$/m),
                    snapchat_ads_pixel_id: new RegExp(/^[a-z0-9\-]*$/m),
                    tiKtok_ads_pixel_id: new RegExp(/^[A-Z0-9]{20,20}$/m),
                    hotjar_pixel_id: new RegExp(/^[0-9]{7,7}$/m),
                    crazyegg_pixel_id: new RegExp(/^[0-9]{8,8}$/m),
                    msclarity_pixel_id: new RegExp(/^[a-z0-9]{10,10}$/m),
                };
                if (ele_val.match(regex_arr[ele_id]) || ele_val === "") {
                    jQuery(this).removeClass("conv-border-danger");
                    change_top_button_state("enable");
                } else {
                    jQuery(this).addClass("conv-border-danger");
                    change_top_button_state("disable");
                }

            });


        //Save data other then GTM,GA,GAds
        jQuery(document).on("click", ".conv-btn-connect-enabled", function() {
            conv_change_loadingbar("show");
            jQuery(this).addClass('disabled');
            var valtoshow_inpopup = jQuery("#valtoshow_inpopup").val() + " " + jQuery(
                ".valtoshow_inpopup_this").val();
            var selected_vals = {};
            selected_vals["subscription_id"] = "<?php echo esc_html($tvc_data['subscription_id']) ?>";

            jQuery('form#pixelsetings_form input, textarea').each(function() {
                selected_vals[jQuery(this).attr("name")] = jQuery(this).val();
            });

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: {
                    action: "conv_save_pixel_data",
                    pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                    conv_options_data: selected_vals,
                    conv_options_type: ["eeoptions"],
                },
                beforeSend: function() {
                    jQuery(".conv-btn-connect-enabled").text("Saving...");
                },
                success: function(response) {
                    var user_modal_txt =
                        "Congratulations, you have successfully connected your <br> " +
                        valtoshow_inpopup;

                    if (response == "0" || response == "1") {
                        jQuery(".conv-btn-connect-enabled").text("Save");
                        jQuery("#conv_save_success_txt").html(user_modal_txt);
                        jQuery("#conv_save_success_modal").modal("show");
                    }
                    conv_change_loadingbar("hide");
                }

            });

        });

        jQuery("#conv-modal-redirect-btn").click(function() {
            var redirectscreen =
                '<?php echo (isset($_GET["redirectscreen"]) && $_GET["redirectscreen"] == "productfeed") ? "1" : "0"; ?>';
            var subPage =
                '<?php echo (isset($_GET["subpage"]) && $_GET["subpage"] == "gmcsettings") ? "1" : "0"; ?>';
            if (subPage == "1") {
                redirectscreen = "1";
            }
            if (redirectscreen == "1") {
                location.href = "admin.php?page=conversios-google-shopping-feed&tab=feed_list";
            } else {
                location.href = "admin.php?page=conversios-google-analytics";
            }

        });

    });
</script>

<?php
// echo '<pre>--ee_options--';
// print_r($ee_options);
// echo '</pre>';


// echo '<pre>--tvc_data---';
// print_r($tvc_data);
// echo '</pre>';


// echo '<pre>--ee_additional_data--';
// print_r($ee_additional_data);
// echo '</pre>';

// echo '<pre>--ee_api_data--';
// print_r($get_ee_options_data);
// echo '</pre>';



// echo '<pre>--Google Details--';
// print_r($googleDetail);
// echo '</pre>';
?>