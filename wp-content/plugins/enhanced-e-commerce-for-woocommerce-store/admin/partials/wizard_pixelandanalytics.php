<?php

/**
 * Wizard Pixel and Analytics Settings
 *
 * @package Enhanced_Ecommerce_For_Woocommerce_Store
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$tvc_admin_helper = new TVC_Admin_Helper();
$custom_api_obj = new CustomApi();
$ee_additional_data = $tvc_admin_helper->get_ee_additional_data();
$tvc_data = $tvc_admin_helper->get_store_data();
$is_refresh_token_expire = false;
if ((isset($_GET['g_mail']) && sanitize_text_field(wp_unslash($_GET['g_mail']))) && (isset($_GET['subscription_id']) && sanitize_text_field(wp_unslash($_GET['subscription_id'])))) {
    if (isset($_GET['wizard_channel']) && sanitize_text_field(wp_unslash($_GET['wizard_channel'])) == 'gtmsettings') {
        update_option('ee_customer_gtm_gmail', sanitize_email(wp_unslash($_GET['g_mail'])));
        $red_url = 'admin.php?page=conversios&wizard=pixelandanalytics';
        // header( 'Location: ' . $red_url );
    }

    if (isset($_GET['wizard_channel']) && (sanitize_text_field(wp_unslash($_GET['wizard_channel'])) == 'gasettings' || sanitize_text_field(wp_unslash($_GET['wizard_channel'])) == 'gadssettings')) {
        update_option('ee_customer_gmail', sanitize_email(wp_unslash($_GET['g_mail'])));
        $eeapidata = maybe_unserialize(get_option('ee_api_data'));
        $eeapidata_settings = new stdClass();
        // Is not work for existing user && $ee_additional_data['con_created_at'] != ''.
        if (isset($ee_additional_data['con_created_at'])) {
            $ee_additional_data = $tvc_admin_helper->get_ee_additional_data();
            $ee_additional_data['con_updated_at'] = gmdate('Y-m-d');
            $tvc_admin_helper->set_ee_additional_data($ee_additional_data);
        } else {
            $ee_additional_data = $tvc_admin_helper->get_ee_additional_data();
            $ee_additional_data['con_created_at'] = gmdate('Y-m-d');
            $ee_additional_data['con_updated_at'] = gmdate('Y-m-d');
            $tvc_admin_helper->set_ee_additional_data($ee_additional_data);
        }
    }
}

$ee_options = maybe_unserialize(get_option('ee_options'));
$ee_api_data_all = maybe_unserialize(get_option('ee_api_data'));
$ee_api_data = $ee_api_data_all['setting'];

$plan_id = $ee_api_data->plan_id;
$store_id = $ee_api_data->store_id;


// From single pixel main.
$google_detail = $ee_api_data;
$tracking_option = '';
$login_customer_id = '';

$app_id = 1;
// Get user data.
$ee_options = $tvc_admin_helper->get_ee_options_settings();
$get_ee_options_data = $tvc_admin_helper->get_ee_options_data();
$subscription_id = $ee_options['subscription_id'];

// Check last login for check RefreshToken.
$g_mail = get_option('ee_customer_gmail');
$cust_g_email = $g_mail;
$tvc_data['g_mail'] = '';
if ($g_mail) {
    $tvc_data['g_mail'] = sanitize_email($g_mail);
}

// For microsoft.
$microsoft_mail = get_option('ee_customer_msmail');
$cust_ms_email = $microsoft_mail;
$tvc_data['microsoft_mail'] = '';
if ($microsoft_mail) {
    $tvc_data['microsoft_mail'] = sanitize_email($microsoft_mail);
}

$is_sel_disable_ga = 'disabled';
$cust_g_email = (isset($tvc_data['g_mail']) && esc_attr($subscription_id)) ? esc_attr($tvc_data['g_mail']) : '';
$tracking_method = (isset($ee_options['tracking_method']) && '' != $ee_options['tracking_method']) ? $ee_options['tracking_method'] : '';

global $wp_filesystem;

// Get account settings from the api.
if ('' != $subscription_id) {
    $google_detail = $custom_api_obj->getGoogleAnalyticDetail($subscription_id);
    if (property_exists($google_detail, 'error') && false == $google_detail->error) {
        if (property_exists($google_detail, 'data') && '' != $google_detail->data) {
            $google_detail = $google_detail->data;
            $tvc_data['subscription_id'] = $google_detail->id;
            $plan_id = $google_detail->plan_id;
            $login_customer_id = $google_detail->customer_id;
            $tracking_option = $google_detail->tracking_option;
            if ('' != $google_detail->tracking_option) {
                $defaul_selection = 0;
            }
        }
    }
}
?>

<style>
    .pixel-status,
    .convpixeldoclink {
        display: inline-block;
        margin-left: 10px;
        font-weight: bold;
    }

    .pixel-active {
        color: green;
    }

    .pixel-inactive {
        color: red;
    }

    #convallpixel_table th {
        display: flex;
    }

    .convfreetrialbut {
        color: blue;
        padding: 5px 7px;
        border-radius: 5px;
        border: 1px solid blue;
        background: #fff;
    }

    .convpixeldoclink .dashicons {
        color: #0073aa;
        vertical-align: middle;
        cursor: help;
    }

    .gagadsoptionbox {
        background: #ccedfd;
        display: inline-block;
    }

    .badge.text-light.rounded-pill {
        background-color: #873EFF !important;
        font-size: 13px;
        letter-spacing: 1px;
        cursor: pointer;
    }

    .conv-sticky-save-row {
        position: sticky;
        bottom: 0;
        background: #fff;
        z-index: 10;
        box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="pt-4" style="padding-left: 20px;">
    <h3 class="pb-3 fw-normal"><?php esc_html_e('Pixel & Analytics Settings', 'enhanced-e-commerce-for-woocommerce-store'); ?></h3>

    <?php

    $remarketing = unserialize(get_option('ee_remarketing_snippets'));
    $remarketing_snippet_id = "";
    if (!empty($remarketing) && isset($remarketing['snippets']) && esc_attr($remarketing['snippets'])) {
        $remarketing_snippet_id = sanitize_text_field(isset($remarketing['id']) ? esc_attr($remarketing['id']) : "");
    }


    // Define all pixel values
    $measurement_id = !empty($ee_options["gm_id"]) ? $ee_options["gm_id"] : "";

    $gads_remarketing_id = "";
    if (!empty($ee_options["gads_remarketing_id"])) {
        $gads_remarketing_id = !empty($ee_options["gads_remarketing_id"]) ? $ee_options["gads_remarketing_id"] : "";
    } else {
        $gads_remarketing_id = $remarketing_snippet_id;
    }


    $fb_pixel_id = !empty($ee_options["fb_pixel_id"]) ? $ee_options["fb_pixel_id"] : "";
    $tiKtok_ads_pixel_id = !empty($ee_options["tiKtok_ads_pixel_id"]) ? $ee_options["tiKtok_ads_pixel_id"] : "";
    $snapchat_ads_pixel_id = !empty($ee_options["snapchat_ads_pixel_id"]) ? $ee_options["snapchat_ads_pixel_id"] : "";
    $pinterest_ads_pixel_id = !empty($ee_options["pinterest_ads_pixel_id"]) ? $ee_options["pinterest_ads_pixel_id"] : "";
    $microsoft_ads_pixel_id = !empty($ee_options["microsoft_ads_pixel_id"]) ? $ee_options["microsoft_ads_pixel_id"] : "";
    $msclarity_pixel_id = !empty($ee_options["msclarity_pixel_id"]) ? $ee_options["msclarity_pixel_id"] : "";
    $linkedin_insight_id = !empty($ee_options["linkedin_insight_id"]) ? $ee_options["linkedin_insight_id"] : "";
    $hotjar_pixel_id = !empty($ee_options["hotjar_pixel_id"]) ? $ee_options["hotjar_pixel_id"] : "";
    $crazyegg_pixel_id = !empty($ee_options["crazyegg_pixel_id"]) ? $ee_options["crazyegg_pixel_id"] : "";
    $twitter_ads_pixel_id = !empty($ee_options["twitter_ads_pixel_id"]) ? $ee_options["twitter_ads_pixel_id"] : "";

    // Helper function
    function pixel_status($value)
    {
        return $value ? '<span class="pixel-status pixel-active">Active</span>' : '<span class="pixel-status pixel-inactive">Inactive</span>';
    }
    ?>



    <table id="convallpixel_table" class="form-table" role="presentation">
        <tbody>

            <!-- Google Analytics -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_ganalytics_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Google Analytics
                </th>
                <td>
                    <input type="text" name="measurement_id" id="measurement_id" class="regular-text" value="<?php echo esc_attr($measurement_id); ?>">
                    <?php echo wp_kses_post(pixel_status($measurement_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-your-ga4-measurement-id/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Google Analytics 4 Measurement ID looks similar to this: G-XXXXXXXXXX</p>
                    <div id="enable_cid" class="pt-4 ps-2">
                        <div class="row gagadsoptionbox p-3">
                            <div class="col-12 m-auto text-end d-flex">
                                <div class="form-check p-0 me-2">
                                    <input class="form-check-input ms-auto float-end" type="checkbox" id="non_woo_tracking" name="non_woo_tracking" checked>
                                </div>
                                <label class="form-check-label fw-normal text-dark" for="non_woo_tracking">
                                    Enable Recommended Events in GA4
                                </label>
                                <span class="dashicons dashicons-info text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Page scroll tracking, File download, Author tracking, SignUp, Login."></span>
                            </div>
                            <div class="col-12 m-auto text-end d-flex">
                                <div class="form-check p-0 me-2">
                                    <input class="form-check-input ms-auto float-end" type="checkbox" id="ga_cid" name="ga_cid" checked>
                                </div>
                                <label class="form-check-label fw-normal text-dark" for="ga_cid">
                                    Enable client id and Enhance conversion in GA4 for better reporting
                                </label>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <!-- Google Ads -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_gads_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Google Ads
                </th>
                <td>
                    <input type="text" name="gads_remarketing_id" id="gads_remarketing_id" class="regular-text" value="<?php echo esc_attr($gads_remarketing_id); ?>">
                    <?php echo wp_kses_post(pixel_status($gads_remarketing_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/blog/how-to-find-google-ads-pixel-id/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Google Ads remarketing ID looks similar to this: AW-XXXXXXXXX</p>
                    <div class="pt-4 ps-2">
                        <div class="row gagadsoptionbox p-3">
                            <h5>Pro Feature</h5>
                            <div class="col-12 m-auto text-end d-flex">
                                <div class="form-check p-0 me-2">
                                    <input class="ms-auto float-end disabled" readonly type="checkbox" checked>
                                </div>
                                <label class="form-check-label fw-normal text-dark">
                                    Enable Google Ads conversion tracking for Purchase, AddtoCart & Checkout
                                </label>
                                <span class="dashicons dashicons-info text-primary ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Google Ads conversion tracking helps you see if people take action (like buying or signing up) after clicking your ad."></span>
                            </div>
                            <div class="col-12 m-auto text-end d-flex">
                                <div class="form-check p-0 me-2">
                                    <input class="ms-auto float-end disabled" readonly type="checkbox" checked>
                                </div>
                                <label class="form-check-label fw-normal text-dark">
                                    Enable Google Ads Enhanced Conversion tracking
                                </label>
                                <span class="dashicons dashicons-info text-primary ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Google Ads Enhanced Conversion tracking improves accuracy by securely using customer data (like email or phone) to better track actions after ads."></span>
                            </div>
                            <a class="convfreetrialbut ms-3" style="float: right; width: auto;" target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=pixwizard&utm_campaign=freetopro">Start 15 days trial</a>
                        </div>
                    </div>
                </td>
            </tr>

            <!-- Facebook Pixel -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_fb_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Facebook Pixel ID
                </th>
                <td>
                    <input type="text" name="fb_pixel_id" id="fb_pixel_id" class="regular-text" value="<?php echo esc_attr($fb_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($fb_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-my-facebook-pixel-id/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Facebook Pixel ID looks similar to this: 123456789012345</p>
                </td>
            </tr>


            <!-- Meta CAPI -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_fb_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Facebook Conversion API Token (FBCAPI)
                </th>
                <td>
                    <input type="text" readonly class="regular-text">
                    <span class="badge text-light rounded-pill probadgeconv">(Pro)</span>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-generate-facebook-conversion-api-token/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">
                        Send events directly from your web server to Facebook through the Conversion API.
                        <a class="convfreetrialbut" target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=pixwizard&utm_campaign=freetopro">Start 15 days trial</a>
                    </p>
                </td>
            </tr>

            <!-- TikTok Pixel -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_tiktok_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    TikTok Pixel ID
                </th>
                <td>
                    <input type="text" name="tiKtok_ads_pixel_id" id="tiKtok_ads_pixel_id" class="regular-text" value="<?php echo esc_attr($tiKtok_ads_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($tiKtok_ads_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-tiktok-pixel-id-from-business-manager-account/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The TikTok Pixel ID looks similar to this: C0ABCDE1234567890D12</p>
                </td>
            </tr>

            <!-- Tiktok CAPI -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_tiktok_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Tiktok Events API Token
                </th>
                <td>
                    <input type="text" readonly class="regular-text">
                    <span class="badge text-light rounded-pill probadgeconv">(Pro)</span>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-your-tiktok-pixel-id-and-conversion-api-token/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">
                        Send events directly from your web server to Tiktok through the Events API.
                        <a class="convfreetrialbut" target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=pixwizard&utm_campaign=freetopro">Start 15 days trial</a>
                    </p>
                </td>
            </tr>

            <!-- Snapchat Pixel -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_snap_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Snapchat Pixel ID
                </th>
                <td>
                    <input type="text" name="snapchat_ads_pixel_id" id="snapchat_ads_pixel_id" class="regular-text" value="<?php echo esc_attr($snapchat_ads_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($snapchat_ads_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-the-snapchat-pixel-id-from-the-business-manager-account/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Snapchat Pixel ID looks similar to this: abc12345-6789-def0-1234-56789abcdef0</p>
                </td>
            </tr>

            <!-- Snapchat CAPI -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_snap_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Snapchat Conversion API Token
                </th>
                <td>
                    <input type="text" readonly class="regular-text">
                    <span class="badge text-light rounded-pill probadgeconv">(Pro)</span>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-your-snapchat-pixel-id-and-conversion-api-token/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">
                        Send events directly from your web server to Snapchat through the Conversion API.
                        <a class="convfreetrialbut" target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=pixwizard&utm_campaign=freetopro">Start 15 days trial</a>
                    </p>
                </td>
            </tr>

            <!-- Pinterest Pixel -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_pint_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Pinterest Pixel ID
                </th>
                <td>
                    <input type="text" name="pinterest_ads_pixel_id" id="pinterest_ads_pixel_id" class="regular-text" value="<?php echo esc_attr($pinterest_ads_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($pinterest_ads_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-pinterest-pixel-id-from-a-business-manager-account/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Pinterest tag ID looks similar to this: 2610194491234</p>
                </td>
            </tr>

            <!-- Microsoft Ads -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_bing_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Microsoft Ads (Bing) Pixel ID
                </th>
                <td>
                    <input type="text" name="microsoft_ads_pixel_id" id="microsoft_ads_pixel_id" class="regular-text" value="<?php echo esc_attr($microsoft_ads_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($microsoft_ads_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/set-up-microsoft-advertising-with-conversios-plugin/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Microsoft UET tag ID looks similar to this: 12345678</p>
                </td>
            </tr>

            <!-- MS Clarity -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_clarity_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    MS Clarity Pixel ID
                </th>
                <td>
                    <input type="text" name="msclarity_pixel_id" id="msclarity_pixel_id" class="regular-text" value="<?php echo esc_attr($msclarity_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($msclarity_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/set-up-microsoft-advertising-with-conversios-plugin/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The MS Clarity project ID looks similar to this: abcd1234</p>
                </td>
            </tr>

            <!-- LinkedIn Insight -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_linkedin_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    LinkedIn Insight ID
                </th>
                <td>
                    <input type="text" name="linkedin_insight_id" id="linkedin_insight_id" class="regular-text" value="<?php echo esc_attr($linkedin_insight_id); ?>">
                    <?php echo wp_kses_post(pixel_status($linkedin_insight_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://business.linkedin.com/marketing-solutions/insight-tag">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The LinkedIn insight ID looks similar to this: 123456</p>
                </td>
            </tr>

            <!-- Hotjar Pixel -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_hotjar_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Hotjar Pixel
                </th>
                <td>
                    <input type="text" name="hotjar_pixel_id" id="hotjar_pixel_id" class="regular-text" value="<?php echo esc_attr($hotjar_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($hotjar_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-a-hotjar-pixel-from-hotjar-business-manager/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Hotjar Site ID looks similar to this: 1234567</p>
                </td>
            </tr>

            <!-- Twitter Pixel -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_twitter_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Twitter Pixel ID
                </th>
                <td>
                    <input type="text" name="twitter_ads_pixel_id" id="twitter_ads_pixel_id" class="regular-text" value="<?php echo esc_attr($twitter_ads_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($twitter_ads_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-crazyegg-pixel-id/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Twitter Pixel ID looks similar to this: ocihv</p>
                </td>
            </tr>

            <!-- Crazy Egg Pixel -->
            <tr>
                <th scope="row" style="vertical-align: top;">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_crazyegg_logo.png', '', 'conv_channel_logo me-2 align-self-center'),
                        ['img' => ['src' => true, 'alt' => true, 'class' => true, 'style' => true]]
                    ); ?>
                    Crazy Egg Pixel ID
                </th>
                <td>
                    <input type="text" name="crazyegg_pixel_id" id="crazyegg_pixel_id" class="regular-text" value="<?php echo esc_attr($crazyegg_pixel_id); ?>">
                    <?php echo wp_kses_post(pixel_status($crazyegg_pixel_id)); ?>
                    <a class="convpixeldoclink" target="_blank" href="https://www.conversios.io/docs/how-to-find-crazyegg-pixel-id/?utm_source=woo_aiofree_plugin&utm_medium=otherpixelsetting&utm_campaign=woo_aiofree_plugin">
                        <span class="dashicons dashicons-editor-help text-primary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Open documentation"></span>
                    </a>
                    <p class="description">The Crazy Egg account ID looks similar to this: 12345678</p>
                </td>
            </tr>
            <tr class="conv-sticky-save-row">
                <td>
                </td>
                <td>
                    <button id="convsaveallpixels" role="button" class="btn btn-primary px-5">
                        <span class="spinner-border text-light spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <?php echo esc_html__('Save Settings', 'enhanced-e-commerce-for-woocommerce-store') ?>
                    </button>
                </td>
            </tr>

        </tbody>
    </table>




</div>



<!-- Modal -->
<div class="modal fade" id="conv_wizfinish" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="conv_wizfinish" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="container-fluid">
                    <div class="row">
                        <div id="conv_wizfinish_right" class="col-12 py-3 px-4 text-center">
                            <h3 class="fw-light h2 pt-3" style="color:#09bd3a;">Congratulations!!</h3>
                            <h6>Your tracking is now live and data flowing in real time. <br> Reports and insights will be available within the next 24 hours.</h6>
                            <h6 class="mt-4">Stay connected & Follow us on social media to get product tips, updates & video tutorials.</h6>
                            <div class="d-flex justify-content-center my-4 convsocialicons">
                                <a href="https://facebook.com/Conversios" target="_blank" class="rounded-circle p-2" title="Facebook">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/conv_fb_logo.png', '', ''),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => "width:32px",
                                            ),
                                        )
                                    ); ?>
                                </a>
                                <a href="https://www.linkedin.com/company/conversios/" target="_blank" class="rounded-circle p-2" title="Facebook">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/conv_linkedin_logo.png', '', ''),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => true,
                                            ),
                                        )
                                    ); ?>
                                </a>

                                <a href="https://www.instagram.com/conversios/" target="_blank" class="rounded-circle p-2" title="Facebook">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/conv_insta_logo.png', '', ''),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => true,
                                            ),
                                        )
                                    ); ?>
                                </a>

                                <a href="https://www.youtube.com/@conversios" target="_blank" class="rounded-circle p-2" title="Facebook">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/conv_yt_logo.png', '', ''),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => true,
                                            ),
                                        )
                                    ); ?>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer m-auto">
                <a href="<?php echo esc_url('admin.php?page=conversios-analytics-reports'); ?>" class="btn btn-primary">
                    Explore Reports
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const proBadge = document.querySelectorAll('.probadgeconv');
        proBadge.forEach(el => {
            el.addEventListener('click', function() {
                window.open('https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=pixwizardbadge&utm_campaign=freetopro', '_blank');
            });
        });
    });

    jQuery(function() {
        jQuery("#navbarSupportedContent ul li").removeClass("rich-blue");
        jQuery('#navbarSupportedContent ul > li').eq(1).addClass('rich-blue');

        jQuery(document).on("click", "#convsaveallpixels", function() {
            jQuery(this).addClass('disabled');
            jQuery(this).find(".spinner-border").removeClass('d-none');
            var selected_vals = {};
            selected_vals["subscription_id"] = "<?php echo esc_html($tvc_data['subscription_id']) ?>";

            selected_vals["tracking_option"] = "GA4";
            selected_vals["property_id"] = jQuery("#measurement_id").val();
            selected_vals["measurement_id"] = jQuery("#measurement_id").val();

            selected_vals["ga_cid"] = document.getElementById('ga_cid').checked ? "1" : "0";
            selected_vals["non_woo_tracking"] = document.getElementById('non_woo_tracking').checked ? "1" : "0";

            selected_vals["cov_remarketing"] = "0";
            if (jQuery("#gads_remarketing_id").val()) {
                selected_vals["cov_remarketing"] = "1";
            }

            selected_vals["gads_remarketing_id"] = jQuery("#gads_remarketing_id").val();
            selected_vals["fb_pixel_id"] = jQuery("#fb_pixel_id").val();
            selected_vals["tiKtok_ads_pixel_id"] = jQuery("#tiKtok_ads_pixel_id").val();
            selected_vals["snapchat_ads_pixel_id"] = jQuery("#snapchat_ads_pixel_id").val();
            selected_vals["pinterest_ads_pixel_id"] = jQuery("#pinterest_ads_pixel_id").val();
            selected_vals["microsoft_ads_pixel_id"] = jQuery("#microsoft_ads_pixel_id").val();
            selected_vals["msclarity_pixel_id"] = jQuery("#msclarity_pixel_id").val();
            selected_vals["linkedin_insight_id"] = jQuery("#linkedin_insight_id").val();
            selected_vals["hotjar_pixel_id"] = jQuery("#hotjar_pixel_id").val();
            selected_vals["crazyegg_pixel_id"] = jQuery("#crazyegg_pixel_id").val();
            selected_vals["twitter_ads_pixel_id"] = jQuery("#twitter_ads_pixel_id").val();
            selected_vals["conv_onboarding_done_step"] = "<?php echo esc_js("6"); ?>";
            selected_vals["conv_onboarding_done"] = "<?php echo esc_js(gmdate('Y-m-d H:i:s')) ?>";
            //console.log(selected_vals)

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: {
                    action: "conv_save_pixel_data",
                    pix_sav_nonce: "<?php echo esc_html(wp_create_nonce('pix_sav_nonce_val')); ?>",
                    conv_options_data: selected_vals,
                    conv_options_type: ["eeoptions", "eeapidata", "middleware"],
                },
                success: function(response) {
                    jQuery(this).find(".spinner-border").addClass('d-none');
                    jQuery("#conv_wizfinish").modal("show");
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('#convallpixel_table input[type="text"]');

        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const statusSpan = this.parentElement.querySelector('.pixel-status');
                if (statusSpan) {
                    if (this.value.trim()) {
                        statusSpan.classList.remove('pixel-inactive');
                        statusSpan.classList.add('pixel-active');
                        statusSpan.textContent = 'Active';
                    } else {
                        statusSpan.classList.remove('pixel-active');
                        statusSpan.classList.add('pixel-inactive');
                        statusSpan.textContent = 'Inactive';
                    }
                }
            });
        });
    });
</script>