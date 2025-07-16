<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

$TVC_Admin_Helper = new TVC_Admin_Helper();
//$this->customApiObj = new CustomApi();
$class = "";
$message_p = "";
$validate_pixels = array();
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$plan_id = 1;
$googleDetail = "";
if (isset($google_detail['setting'])) {
    $googleDetail = $google_detail['setting'];
    if (isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))) {
        $plan_id = $googleDetail->plan_id;
    }
}

$data = unserialize(get_option('ee_options'));
$conv_selected_events = unserialize(get_option('conv_selected_events'));
//$this->current_customer_id = $TVC_Admin_Helper->get_currentCustomerId();
$subscription_id = $TVC_Admin_Helper->get_subscriptionId();

$TVC_Admin_Helper->add_spinner_html();
$is_show_tracking_method_options =  true;
$conv_pro_url = "https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=wizard&utm_campaign=freetopro";
?>

<!-- Main container -->
<div class="container-old conv-setting-container pt-4">

    <!-- Main row -->
    <div class="row row-x-0 justify-content-center">
        <!-- Main col8 center -->
        <div class="convfixedcontainermid-removed col-md-12 px-45 pb-5 border-bottom">

            <!-- GTM Card -->
            <?php
            $tracking_method = (isset($data['tracking_method']) && $data['tracking_method'] != "") ? $data['tracking_method'] : "";
            $want_to_use_your_gtm = (isset($data['want_to_use_your_gtm']) && $data['want_to_use_your_gtm'] != "") ? $data['want_to_use_your_gtm'] : "0";
            $use_your_gtm_id = "";
            if (isset($tracking_method) && $tracking_method == "gtm") {
                $use_your_gtm_id =  ($data['tracking_method'] == 'gtm' && $want_to_use_your_gtm == 1) ? "Your own GTM container - " . $data['use_your_gtm_id'] : (($data['tracking_method'] == 'gtm') ? "Container ID: GTM-K7X94DG (Conversios Default Container) " : esc_attr("Your own GTM container - " . $data['use_your_gtm_id']));
            }
            ?>

            <?php if (isset($tracking_method) && $tracking_method == 'gtag') { ?>
                <div class="alert d-flex align-items-cente p-0" role="alert">
                    <div class="text-light conv-error-bg rounded-start d-flex">
                        <span class="p-2 material-symbols-outlined align-self-center">info</span>
                    </div>

                    <div class="p-2 w-100 rounded-end border border-start-0 shadow-sm conv-notification-alert lh-lg bg-white">
                        <h6 class="fs-6 lh-1 text-dark fw-bold border-bottom w-100 py-2">
                            <?php esc_html_e("Attention!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h6>

                        <span class="fs-6 lh-1 text-dark">
                            <?php esc_html_e("As you might be knowing, GA3 is seeing sunset from 1st July 2023, we are also removing gtag.js based implementation for the old app users soon. Hence, we recommend you to change your implementation method to Google Tag Manager from below to avoid data descrepancy in the future.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </div>
                </div>

            <?php } ?>

            <!-- All pixel list -->
            <?php
            $conv_gtm_not_connected = (empty($subscription_id) || $tracking_method != "gtm") ? "conv-gtm-not-connected" : "conv-gtm-connected";

            $remarketing = unserialize(get_option('ee_remarketing_snippets'));
            $remarketing_snippet_id = "";
            if (!empty($remarketing) && isset($remarketing['snippets']) && esc_attr($remarketing['snippets'])) {
                $remarketing_snippet_id = sanitize_text_field(isset($remarketing['id']) ? esc_attr($remarketing['id']) : "");
            }

            $pixel_not_connected = array(
                "ga_id" => (isset($data['ga_id']) && $data['ga_id'] != '') ? '' : 'conv-pixel-not-connected',
                "gm_id" => (isset($data['gm_id']) && $data['gm_id'] != '') ? '' : 'conv-pixel-not-connected',
                "google_ads_id" => (isset($remarketing_snippet_id) && $remarketing_snippet_id != '') ? '' : 'conv-pixel-not-connected',
                "gads_remarketing_id" => (isset($data['gads_remarketing_id']) && $data['gads_remarketing_id'] != '') ? '' : 'conv-pixel-not-connected',
                "fb_pixel_id" => (isset($data['fb_pixel_id']) && $data['fb_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
                "microsoft_ads_pixel_id" => (isset($data['microsoft_ads_pixel_id']) && $data['microsoft_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
                "msclarity_pixel_id" => (isset($data['msclarity_pixel_id']) && $data['msclarity_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
                "twitter_ads_pixel_id" => (isset($data['twitter_ads_pixel_id']) && $data['twitter_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
                "pinterest_ads_pixel_id" => (isset($data['pinterest_ads_pixel_id']) && $data['pinterest_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
                "snapchat_ads_pixel_id" => (isset($data['snapchat_ads_pixel_id']) && $data['snapchat_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
                "linkedin_insight_id" => (isset($data['linkedin_insight_id']) && $data['linkedin_insight_id'] != '') ? '' : 'conv-pixel-not-connected',
                "tiKtok_ads_pixel_id" => (isset($data['tiKtok_ads_pixel_id']) && $data['tiKtok_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
                "hotjar_pixel_id" => (isset($data['hotjar_pixel_id']) && $data['hotjar_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
                "crazyegg_pixel_id" => (isset($data['hotjar_pixel_id']) && $data['crazyegg_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
            );

            $pixel_video_link = array(
                "gm_id" => "https://www.conversios.io/docs/ecommerce-events-that-will-be-automated-using-conversios/?utm_source=galisting_inapp&utm_medium=resource_center_list&utm_campaign=resource_center",
                "google_ads_id" => "https://youtu.be/Vr7vEeMIf7c",
                "fb_pixel_id" => "https://youtu.be/8nIyvQjeEkY",
                "microsoft_ads_pixel_id" => "https://www.conversios.io/docs/how-to-integrate-microsoft-bing-pixel-with-conversios-plugin/?utm_source=galisting_inapp&utm_medium=resource_center_list&utm_campaign=resource_center",
                "twitter_ads_pixel_id" => "",
                "pinterest_ads_pixel_id" => "https://youtu.be/Z0rcP1ItJDk",
                "snapchat_ads_pixel_id" => "https://youtu.be/uLQqAMQhFUo",
                "tiKtok_ads_pixel_id" => "https://www.conversios.io/docs/how-to-set-up-tiktok-pixel-using-conversios-plugin/?utm_source=Tiktoklisting_inapp&utm_medium=resource_center_list&utm_campaign=resource_center",
                "hotjar_pixel_id" => "",
                "crazyegg_pixel_id" => "https://www.conversios.io/docs/how-to-integrate-crazy-egg-pixel-with-conversios-plugin/?utm_source=galisting_inapp&utm_medium=resource_center_list&utm_campaign=resource_center",
            );
            ?>

            <div id="pixelslist" class="px-1 pb-0 conv-heading-box">
                <h2 class="m-0"><?php esc_html_e("Pixel Integrations", "enhanced-e-commerce-for-woocommerce-store"); ?></h2>
            </div>

            <div id="conv_pixel_list_box" class="row">

                <!-- Google analytics  -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">

                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_ganalytics_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Google Analytics", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios-google-analytics&subpage=gasettings'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>

                        </div>

                        <div class="pt-3 pb-3 pixel-desc">

                            <div class="d-flex align-items-start flex-column">
                                <?php if ((empty($pixel_not_connected['ga_id']) || empty($pixel_not_connected['gm_id'])) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <?php if (isset($data['gm_id']) && $data['gm_id'] != '') { ?>
                                        <div class="d-flex align-items-center pb-1 mb-1">
                                            <span class="d-flex  align-items-center m-0">
                                                <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span>Measurement ID: <?php echo (isset($data['gm_id']) && $data['gm_id'] != '') ? esc_attr($data['gm_id']) : ''; ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>Measurement ID: Not connected</span></div>
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Google Ads -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_gads_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Google Ads", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>

                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>

                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['gads_remarketing_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1">
                                        <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><span>Remarketing Id: <?php echo (isset($data['gads_remarketing_id']) && $data['gads_remarketing_id'] != '') ? esc_attr($data['gads_remarketing_id']) : ''; ?></span>
                                    </div>
                                <?php } elseif (empty($pixel_not_connected['google_ads_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1">
                                        <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><span>Remarketing Id: <?php echo (isset($remarketing_snippet_id) && $remarketing_snippet_id != '') ? esc_attr($remarketing_snippet_id) : ''; ?></span>
                                    </div>

                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>Google ads Account ID: Not connected</span></div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FB Pixel -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_meta_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Facebook", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>

                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['fb_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1">
                                        <span class="material-symbols-outlined text-success me-1 fs-16 ps-1">check_circle</span><span class="pe-2 m-0">Meta Pixel ID: <?php echo (isset($data['fb_pixel_id']) && $data['fb_pixel_id'] != '') ? esc_attr($data['fb_pixel_id']) : ''; ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>Meta Pixel ID: Not connected</span></div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- MS Bing Ads -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_bing_logo.png'); ?>" />
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Microsoft Ads (Bing)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    <br><span class="badge rounded-pill py-1.5 px-2.5 text-center fs-12 new-feature-badge fw-normal">Enhanced Automation</span>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios-google-analytics&subpage=bingsettings'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>

                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['microsoft_ads_pixel_id'])  && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <?php if (isset($data['microsoft_ads_pixel_id']) && $data['microsoft_ads_pixel_id'] != '') { ?>
                                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n">
                                            <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><span class="pe-2 m-0">Ads Pixel ID: <?php echo (isset($data['microsoft_ads_pixel_id']) && $data['microsoft_ads_pixel_id'] != '') ? esc_attr($data['microsoft_ads_pixel_id']) : 'Not connected'; ?></span>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n"><span class="material-symbols-outlined text-error me-1 fs-16">cancel</span><span>Ads Pixel ID: Not connected</span></div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- MS Bing Clarity -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_clarity_logo.png'); ?>" />
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Microsoft Clarity", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>

                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['msclarity_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <?php if (isset($data['msclarity_pixel_id']) && $data['msclarity_pixel_id'] != '') { ?>
                                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n">
                                            <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><span class="pe-2 m-0">Clarity ID: <?php echo (isset($data['msclarity_pixel_id']) && $data['msclarity_pixel_id'] != '') ? esc_attr($data['msclarity_pixel_id']) : 'Not connected'; ?></span>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n"><span class="material-symbols-outlined text-error me-1 fs-16">cancel</span><span>Clarity ID: Not connected</span></div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Linkedin Pixel -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_linkedin_logo.png'); ?>" />
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Linkedin Insight", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>

                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['linkedin_insight_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n">
                                        <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><span class="pe-2 m-0">Linkedin Insight ID: <?php echo (isset($data['linkedin_insight_id']) && $data['linkedin_insight_id'] != '') ? esc_attr($data['linkedin_insight_id']) : 'Not connected'; ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n"><span class="material-symbols-outlined text-error me-1 fs-16">cancel</span><span>Linkedin Insight ID: Not connected</span></div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Tiktok -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_tiktok_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Tiktok Pixel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>
                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['tiKtok_ads_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1">
                                        <span class="material-symbols-outlined text-success me-1 fs-16 ps-1">check_circle</span><span class="pe-2 m-0">TikTok Pixel ID: <?php echo (isset($data['tiKtok_ads_pixel_id']) && $data['tiKtok_ads_pixel_id'] != '') ? esc_attr($data['tiKtok_ads_pixel_id']) : 'Not connected'; ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>TikTok Pixel ID: Not connected</span></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Snapchat Pixel -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_snap_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Snapchat Pixel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>
                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['snapchat_ads_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1">
                                        <span class="material-symbols-outlined text-success me-1 fs-16 ps-1">check_circle</span><span class="pe-2 m-0">Snapchat Pixel ID: <?php echo (isset($data['snapchat_ads_pixel_id']) && $data['snapchat_ads_pixel_id'] != '') ? esc_attr($data['snapchat_ads_pixel_id']) : 'Not connected'; ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>Snapchat Pixel ID: Not connected</span></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pinterest Pixel -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_pint_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Pinterest Pixel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>

                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['pinterest_ads_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1">
                                        <span class="material-symbols-outlined text-success me-1 fs-16 ps-1">check_circle</span><span class="pe-2 m-0">Pinterest Pixel ID: <?php echo (isset($data['pinterest_ads_pixel_id']) && $data['pinterest_ads_pixel_id'] != '') ? esc_attr($data['pinterest_ads_pixel_id']) : 'Not connected'; ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>Pinterest Pixel ID: Not connected</span></div>
                                <?php } ?>

                            </div>
                        </div>

                    </div>
                </div>


                <!-- Twitter Pixel -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_twitter_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Twitter Pixel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>
                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <div class="d-flex align-items-start flex-column">
                                <?php if (empty($pixel_not_connected['twitter_ads_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n">
                                        <span class="material-symbols-outlined text-success me-1 fs-16 ps-1">check_circle</span><span class="pe-2 m-0">Twitter Pixel ID: <?php echo (isset($data['twitter_ads_pixel_id']) && $data['twitter_ads_pixel_id'] != '') ? esc_attr($data['twitter_ads_pixel_id']) : 'Not connected'; ?></span>
                                    </div>
                                <?php } else { ?>
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>Twitter Pixel ID: Not connected</span></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hotjar -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_hotjar_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Hotjar Pixel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>
                        </div>

                        <div class="pt-3 pb-3 pixel-desc">
                            <?php if (empty($pixel_not_connected['hotjar_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                <div class="d-flex align-items-start flex-column">
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n">
                                        <span class="material-symbols-outlined text-success me-1 fs-16 ps-1">check_circle</span><span class="pe-2 m-0">Hotjar Pixel ID: <?php echo (isset($data['hotjar_pixel_id']) && $data['hotjar_pixel_id'] != '') ? esc_attr($data['hotjar_pixel_id']) : 'Not connected'; ?></span>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>Hotjar Pixel ID: Not connected</span></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Crazyegg -->
                <div class="col-md-4 p-3">
                    <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                        <div class="conv-pixel-logo d-flex justify-content-between">
                            <div class="d-flex align-items-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/conv_crazyegg_logo.png', '', 'align-self-center'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?>
                                <span class="fw-bold fs-4 ms-2 pixel-title">
                                    <?php esc_html_e("Crazyegg Pixel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>
                            <a href="<?php echo esc_url('admin.php?page=conversios&wizard=pixelandanalytics'); ?>" class="align-self-center">
                                <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                            </a>
                        </div>

                        <div class="pt-3 pb-3 pixel-desc align-items-start flex-column">
                            <?php if (empty($pixel_not_connected['crazyegg_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
                                <div class="d-flex">
                                    <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n">
                                        <span class="material-symbols-outlined text-success me-1 fs-16 ps-1">check_circle</span><span class="pe-2 m-0">Crazyegg Pixel ID: <?php echo (isset($data['crazyegg_pixel_id']) && $data['crazyegg_pixel_id'] != '') ? esc_attr($data['crazyegg_pixel_id']) : 'Not connected'; ?></span>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="d-flex align-items-center pb-1 mb-1 border-bottom-n"><span class="material-symbols-outlined text-error me-1 fs-16 ps-1">cancel</span><span>Crazyegg Pixel ID: Not connected</span></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>


                <!-- All pixel list end -->

                <!-- Advanced option -->
                <?php if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) { ?>
                    <div class="col-md-4 p-3">
                        <div class="p-3 convcard d-flex justify-content-between-no flex-column conv-pixel-list-item border <?php echo esc_attr($conv_gtm_not_connected); ?>">
                            <div class="conv-pixel-logo d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/conv_event_track_custom.png', '', 'align-self-center'),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => true,
                                            ),
                                        )
                                    ); ?>
                                    <span class="fw-bold fs-4 ms-2 pixel-title">
                                        <?php esc_html_e("Additional Configurations", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </span>
                                </div>
                                <a href="<?php echo esc_url('admin.php?page=conversios-google-analytics&subpage=customintgrationssettings'); ?>" class="align-self-center">
                                    <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                                </a>
                            </div>

                            <div class="pt-3 pb-3 pixel-desc d-flex align-items-start">
                                <span class="material-symbols-outlined align-text-bottom pe-1 fs-18">settings</span>
                                <span class="fw-bold">
                                    <?php esc_html_e("Events Tracking - Custom Integration", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </div>

                            <div class="pt-3 pb-3 pixel-desc align-items-start flex-column">
                                <span><?php esc_html_e("This feature is for the woocommerce store which has changed standard woocommerce hooks or implemented custom woocommerce hooks.", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                                <div class="d-flex">
                                    <span class="pe-2 m-0">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Advance option End -->
                <?php }  ?>

            </div>

        </div>
        <!-- Main col8 center -->
    </div>
    <!-- Main row -->
</div>
<!-- Main container End -->

<!-- EC Start -->
<div class="rounded-3 p-3 bg-white ecbuttonbox d-none hidden">
    <div class="convcard-left conv-pixel-logo">
        <div class="convcard-title">
            <h6 class="mb-0 text-white">
                <?php esc_html_e("Event Tracking Wizard", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h6>
            <p class="mb-2 text-white mt-2" style="line-height: 15px;">
                <?php esc_html_e("See in real time if events are being tracked correctly on your website.", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </p>
            <small class="mb-3 text-white">
                <b><?php esc_html_e("Note:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                <?php esc_html_e("Make sure to use this feature in Chrome browser for accurate result. Also, make sure the pop blocker is not enabled in your browser.", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </small>
        </div>
    </div>

    <div id="starttrackingbut" class="w-100 d-flex justify-content-between rounded-3 px-3 align-items-center py-2">
        <div class="convecbuttext">
            <?php esc_html_e("Start Wizard", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </div>
        <span class="material-symbols-outlined align-self-center">chevron_right</span>
    </div>
</div>
<!-- EC End -->
<!-- Upgrade to PRO modal End -->


<script>
    // Set GTM on page load. 
    let tracking_method = "<?php echo esc_js($tracking_method) ?>";
    if (tracking_method != 'gtm' && tracking_method == '') {
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "conv_save_pixel_data",
                pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                conv_options_data: {
                    want_to_use_your_gtm: 0,
                    tracking_method: 'gtm'
                },
                conv_options_type: ["eeoptions", "eeapidata"],
            },
            success: function(response) {
                jQuery('.gtm-badge').removeClass('conv-badge-yellow').addClass('conv-badge-green');
                jQuery('.gtm-badge').text('Connected')
                jQuery('.conv-pixel-list-item').removeClass('conv-gtm-not-connected').addClass(
                    'conv-gtm-connected')
                jQuery('.gtm-lable').html('Container ID: <b> GTM-K7X94DG (Conversios Default Container)</b>')
            },
            error: function(error) {
                // console.log('error', error)
                jQuery('.gtm-badge').removeClass('conv-badge-green').addClass('conv-badge-yellow');
                jQuery('.gtm-badge').text('Mandatory')
                jQuery('.conv-pixel-list-item').removeClass('conv-gtm-connected').addClass(
                    'conv-gtm-not-connected')
            }
        });
    }
    jQuery(function() {
        var connectedcount = jQuery(
            "#conv_pixel_list_box .conv-gtm-connected.conv-pixel-list-item .conv-badge.conv-badge-green").length;
        if (connectedcount == 0) {
            jQuery(".ecbuttonbox").hide();
        }

        jQuery(".propixel_card").css("cursor", "pointer").click(function() {
            window.open("<?php echo esc_js($conv_pro_url); ?>");
        });
        jQuery('#starttrackingbut').click(function() {
            jQuery('#starttrackingbut').addClass('convdisabledbox');
            var ecrandomstring = "<?php echo esc_js($TVC_Admin_Helper->generateRandomStringConv()); ?>";
            var subscription_id = "<?php echo esc_js($subscription_id); ?>";
            var fronturl = '<?php echo esc_url(site_url()); ?>?is_calc_on=1&ec_token=' + ecrandomstring;
            // console.log(fronturl);
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: {
                    action: "conv_create_ec_row",
                    pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                    ecrandomstring: ecrandomstring,
                    subscription_id: subscription_id
                },
                success: function(response) {
                    window.open(fronturl, '_blank');
                    // console.log(response);
                }
            });
        });
    });
</script>

<script>
    // make equale height divs for grid
    jQuery(document).ready(function($) {

        const gridItems = document.querySelector('#conv_pixel_list_box').children;
        const rows = Array.from(gridItems).reduce((rows, item, index) => {
            const rowIndex = Math.floor(index / 3);
            rows[rowIndex] = rows[rowIndex] || [];
            rows[rowIndex].push(item);
            return rows;
        }, []);

        rows.forEach((row) => {
            const maxHeight = Math.max(...row.map((item) => item.children[0].offsetHeight));
            row.forEach((item) => {
                item.children[0].style.minHeight = `${maxHeight}px`;
            });
        });
    });
</script>