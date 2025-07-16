<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

$TVC_Admin_Helper = new TVC_Admin_Helper();
$TVC_Admin_Helper->need_auto_update_db();
$TVC_Admin_Helper->get_feed_status();
$feed_data = $TVC_Admin_Helper->ee_get_results('ee_product_feed');
$count_feed = count($feed_data);
$subscriptionId = $TVC_Admin_Helper->get_subscriptionId();
$site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
$site_url_pmax = "admin.php?page=conversios-pmax";
$customApiObj = new CustomApi();
$googledetail = $customApiObj->getGoogleAnalyticDetail($subscriptionId);
$googleDetail = $googledetail->data;
if (isset($googleDetail->id)) {
    $conv_data['subscription_id'] = $googleDetail->id;
}
$conv_data = $TVC_Admin_Helper->get_store_data();
$conv_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$total_products = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish']))->found_posts;
$ee_options = $TVC_Admin_Helper->get_ee_options_settings();

$google_merchant_center_id = '';
if (isset($ee_options['google_merchant_id']) === TRUE && $ee_options['google_merchant_id'] !== '') {
    $google_merchant_center_id = esc_html($ee_options['google_merchant_id']);
}

$tiktok_business_account = '';
if (isset($ee_options['tiktok_setting']['tiktok_business_id']) === TRUE && $ee_options['tiktok_setting']['tiktok_business_id'] !== '') {
    $tiktok_business_account = esc_html($ee_options['tiktok_setting']['tiktok_business_id']);
}

$facebook_business_account = '';
if (isset($ee_options['facebook_setting']['fb_business_id']) === TRUE && $ee_options['facebook_setting']['fb_business_id'] !== '') {
    $facebook_business_account = esc_html($ee_options['facebook_setting']['fb_business_id']);
}

$facebook_catalog_id = '';
if (isset($ee_options['facebook_setting']['fb_catalog_id']) === TRUE && $ee_options['facebook_setting']['fb_catalog_id'] !== '') {
    $facebook_catalog_id = esc_html($ee_options['facebook_setting']['fb_catalog_id']);
}

$microsoft_merchant_center_id = '';
if (isset($ee_options['microsoft_merchant_center_id']) === TRUE && $ee_options['microsoft_merchant_center_id'] !== '') {
    $microsoft_merchant_center_id = esc_html($ee_options['microsoft_merchant_center_id']);
}
$microsoft_catalog_id = '';
if (isset($ee_options['ms_catalog_id']) === TRUE && $ee_options['ms_catalog_id'] !== '') {
    $microsoft_catalog_id = esc_html($ee_options['ms_catalog_id']);
}

$not_connected_any_gmc = false;
if (
    $google_merchant_center_id === ''
    && $tiktok_business_account === ''
    && $facebook_catalog_id === ''
    && $microsoft_catalog_id === ''
) {
    //wp_safe_redirect("admin.php?page=conversios-google-shopping-feed&tab=feed_list"); //Odd
    //exit;
    $not_connected_any_gmc = true;
}


$google_ads_id = '';
$currency_symbol = '';
if (isset($ee_options['google_ads_id']) === TRUE && $ee_options['google_ads_id'] !== '') {
    $google_ads_id = esc_html($ee_options['google_ads_id']);
    $PMax_Helper = new Conversios_PMax_Helper();
    $currency_code_rs = $PMax_Helper->get_campaign_currency_code($google_ads_id);
    if (isset($currency_code_rs->data->currencyCode)) {
        $currency_code = $currency_code_rs->data->currencyCode;
        $currency_symbol = $TVC_Admin_Helper->get_currency_symbols($currency_code);
    }
}

$googleConnect_url = '';
//$getCountris = @file_get_contents(ENHANCAD_PLUGIN_DIR."includes/setup/json/countries.json");

global $wp_filesystem;
$getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");

$contData = json_decode($getCountris);
$data = unserialize(get_option('ee_options'));
?>

<!-- Main row -->
<div class="px-50 pt-4 conv-heading-box-no">
    <h3 class="m-0">Product Feed Channels</h3>
    <div class="h6 alert alert-success p-2 m-0 mt-2 fw-light text-dark">
        <?php esc_html_e("Automated, real-time API-based product feeds ensure the highest product approval rates,", "enhanced-e-commerce-for-woocommerce-store"); ?>
        <?php esc_html_e("enhancing online campaign optimization across Google, Facebook, and TikTok.", "enhanced-e-commerce-for-woocommerce-store"); ?>
    </div>
</div>
<div id="conv_grid_list_box" class="row px-50 conv-pixel-list-item justify-content-center pt-1 p-3" style="--bs-gutter-x: 0rem;">

    <!-- Google Merchant card Start -->
    <div class="col-md-3 p-3 ps-0">
        <div class="p-3 convcard rounded-n-3 shadow-sm d-flex flex-column">

            <div class="conv-pixel-logo d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/g-logo.png'),
                        array(
                            'img' => array(
                                'src' => true,
                                'alt' => true,
                                'class' => true,
                                'style' => true,
                            ),
                        )
                    ); ?>
                    <div>
                        <span class="fw-bold fs-4 ms-2 pixel-title"> Google </span>
                        <br><span class="ms-2"> Merchant Center </span>
                    </div>
                </div>
                <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=gmcsettings'); ?>" class="align-self-center bg-white ps-2 pt-1">
                    <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                </a>
            </div>
            <div class="pt-3 pb-3 pixel-desc">
                <div class="d-flex align-items-start flex-column">

                    <?php if (isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') { ?>
                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom">
                            <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><?php echo (isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') ? esc_attr($data['google_merchant_id']) : ''; ?>
                        </div>
                    <?php } else { ?>
                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom"><span class="material-symbols-outlined text-error me-1 fs-16">cancel</span><span>Not connected</span></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>


    <!-- Microsoft Merchant card Start -->
    <div class="col-md-3 py-3 ps-0" style="padding-right:32px">
        <div class="p-3 convcard rounded-n-3 shadow-sm d-flex flex-column">

            <div class="conv-pixel-logo d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms-logo.png'); ?>" />
                    <div>
                        <span class="fw-bold fs-4 ms-2 pixel-title"> Microsoft </span>
                        <br><span class="ms-2"> Merchant Center </span>
                    </div>
                </div>
                <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=mmcsettings'); ?>" class="align-self-center bg-white ps-2 pt-1">
                    <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                </a>
            </div>
            <div class="pt-3 pb-3 pixel-desc">
                <div class="d-flex align-items-start flex-column">

                    <?php if (isset($data['ms_catalog_id']) && $data['ms_catalog_id'] != '') { ?>
                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom">
                            <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><?php echo (isset($data['ms_catalog_id']) && $data['ms_catalog_id'] != '') ? esc_attr($data['ms_catalog_id']) : ''; ?>
                        </div>
                    <?php } else { ?>
                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom"><span class="material-symbols-outlined text-error me-1 fs-16">cancel</span><span>Not connected</span></div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>

    <!-- TikTok Business Account Start -->
    <div class="col-md-3 p-3">
        <div class="p-3 convcard rounded-n-3 shadow-sm d-flex flex-column">
            <div class="conv-pixel-logo d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_tiktok_logo.png'),
                        array(
                            'img' => array(
                                'src' => true,
                                'alt' => true,
                                'class' => true,
                                'style' => true,
                            ),
                        )
                    ); ?>
                   <div>
                        <span class="fw-bold fs-4 ms-2 pixel-title"> Tiktok </span>
                        <br><span class="ms-2"> Catalog</span>
                    </div>
                </div>
                <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=tiktokBusinessSettings'); ?>" class="align-self-center bg-white ps-2 pt-1">
                    <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                </a>
            </div>
            <div class="pt-3 pb-3 pixel-desc">
                <div class="d-flex align-items-start flex-column">
                    <?php if (isset($data['tiktok_setting']['tiktok_business_id']) && $data['tiktok_setting']['tiktok_business_id'] != '') { ?>
                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom">
                            <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><?php echo (isset($data['tiktok_setting']['tiktok_business_id']) && $data['tiktok_setting']['tiktok_business_id'] != '') ? esc_attr($data['tiktok_setting']['tiktok_business_id']) : ''; ?>
                        </div>
                    <?php } else { ?>
                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom"><span class="material-symbols-outlined text-error me-1 fs-16">cancel</span><span>Not connected</span></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!-- TikTok Business Account End -->

    <!-- Meta Business Account Start -->
    <div class="col-md-3 p-3 pe-0">
        <div class="p-3 convcard rounded-n-3 shadow-sm d-flex flex-column">
            <div class="conv-pixel-logo d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/conv_meta_logo.png'),
                        array(
                            'img' => array(
                                'src' => true,
                                'alt' => true,
                                'class' => true,
                                'style' => true,
                            ),
                        )
                    ); ?>
                     <div>
                        <span class="fw-bold fs-4 ms-2 pixel-title"> Facebook (Meta) </span>
                        <br><span class="ms-2"> Catalog</span>
                    </div>
                </div>
                <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=metasettings'); ?>" class="align-self-center bg-white ps-2 pt-1">
                    <span class="material-symbols-outlined fs-2 border-2 border-solid rounded-pill" rouded-pill="">arrow_forward</span>
                </a>
            </div>
            <div class="pt-3 pb-3 pixel-desc">
                <div class="d-flex align-items-start flex-column">
                    <?php if (isset($data['facebook_setting']['fb_business_id']) && $data['facebook_setting']['fb_business_id'] != '') { ?>
                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom">
                            <span class="material-symbols-outlined text-success me-1 fs-16">check_circle</span><?php echo (isset($data['facebook_setting']['fb_business_id']) && $data['facebook_setting']['fb_business_id'] != '') ? esc_attr($data['facebook_setting']['fb_business_id']) : ''; ?>
                        </div>
                    <?php } else { ?>
                        <div class="d-flex align-items-center pb-1 mb-1 border-bottom"><span class="material-symbols-outlined text-error me-1 fs-16">cancel</span><span>Not connected</span></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Meta Business Account End -->
</div>
<style>
    .errorInput {
        border: 1.3px solid #ef1717 !important;
        padding: 0px;
        border-radius: 6px;
    }

    .dataTables_length,
    .dataTables_info {
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .dataTables-search,
    .dataTables-paging {
        float: right;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .paginate_button {
        position: relative;
        /*display: block; */
        color: #0d6efd;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #dee2e6;
        font-size: 12px;
        padding: 0.375rem 0.75rem;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    .dataTables_paginate {
        margin-top: 10px !important;
    }

    button:disabled {
        color: #131212;
        border-color: #cccccc;
    }

    button:disabled:hover {
        color: #131212;
        border-color: #cccccc;
    }
</style>
<div class="container-fluid px-50 pb-0">
    <div class="d-flex pb-0">
        <div class="m-0 p-0">
            <div class="conv-heading-box">
                <h3 class="">
                    <?php esc_html_e("Feed Management", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h3>
                <span class="fw-400 fs-14 text-secondary">
                    <?php
                    printf(
                        /* translators: %s: Total number of product */
                        esc_html__('View and manage all your product feeds in one place. Easily track feed status, sync schedules, and performance across channels.', "enhanced-e-commerce-for-woocommerce-store"),
                        esc_html(number_format_i18n($total_products))
                    );
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid px-50 p-3 pb-2">
    <div id="loadingbar_blue" class="progress-materializecss d-none ps-2 pe-2">
        <div class="indeterminate"></div>
    </div>
    <nav class="navbar navbar-light bg-white shadow-sm d-none" style="opacity:0;">
        <div class="col-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-8 col-md-8 col-sm-8 ps-3">
                    <input type="search" class="form-control border from-control-width empty" placeholder="Search..." aria-label="Search" name="search_feed" id="search_feed" aria-controls="feed_list_table">
                </div>
                <div class="col-4 d-flex justify-content-end">
                    <?php if (isset($ee_options['google_merchant_id']) && $ee_options['google_merchant_id'] !== '') {
                        if (isset($ee_options['google_ads_id']) && $ee_options['google_ads_id'] == '') {
                            $googleConnect_url = $TVC_Admin_Helper->get_custom_connect_url_subpage(admin_url() . 'admin.php?page=conversios-google-shopping-feed', "gadssettings") . "&amp;Campaign=Campaign";
                    ?>
                            <button class="signinWithGoogle btn btn-soft-primary fs-14 me-2 campaignClass" title="Select Feed from below to create performance max campaign in Google Ads." style="pointer-events: auto !important" disabled>
                                <?php esc_html_e("Create Campaign", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </button>
                        <?php } else { ?>
                            <button class="createCampaign btn btn-soft-primary fs-14 me-2 campaignClass" title="Select Feed from below to create performance max campaign in Google Ads." style="pointer-events: auto !important" disabled>
                                <?php esc_html_e("Create Campaign", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </button>
                    <?php }
                    } ?>
                    <div id="create_new_feed_div" class="d-flex align-items-center">
                        <button class="btn btn-soft-primary fs-14 me-2" name="create_new_feed" id="create_new_feed" <?php echo $not_connected_any_gmc ? 'disabled' : '' ?>>Create New Feed</button>
                        <?php if ($not_connected_any_gmc) : ?>
                            <span class="material-symbols-outlined fs-6 me-1" data-bs-toggle="tooltip" data-bs-placement="right" title="For create new feed, GMC/FB/Tiktok any one need to setup first">
                                info
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <input type="hidden" id="feedCount" name="feedCount" value="<?php echo !empty($feed_data) ? count($feed_data) : 0; ?>">
    <div class="table-responsive shadow-sm" style="border-bottom-left-radius:8px;border-bottom-right-radius:8px;">
        <?php // echo '<pre>'; print_r($feed_data); echo '</pre>'; // wow 
        ?>
        <table class="table" id="feed_list_table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col" class="text-start">
                        <div class="form-check form-check-custom">
                            <input class="form-check-input checkbox fs-17" type="checkbox" name="selectAll" id="selectAll" value="selectAll">
                        </div>
                    </th>
                    <th scope="col" class="text-start" style="width:30%">
                        <?php esc_html_e("FEED NAME", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("TARGET COUNTRY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("CHANNELS", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("TOTAL PRODUCTS", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:12%">
                        <?php esc_html_e("AUTO SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("CREATED", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("LAST SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("NEXT SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:5%">
                        <?php esc_html_e("STATUS", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:3%">
                        <?php esc_html_e("MORE", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="table-body" class="table-body">
                <?php

                // echo '<pre>'; print_r($feed_data); echo '</pre>'; wow


                $feedIdArr = [];
                if (empty($feed_data) === FALSE) {
                    foreach ($feed_data as $value) {
                        $channel_id = explode(',', $value->channel_ids);
                        if ($value->status == 'Synced') {
                            array_push($feedIdArr, $value->id);
                        }

                ?>
                        <tr class="height" style="<?php echo $value->is_delete === '1' ? 'color: #708581; opacity: 0.5;' : ''; ?>">
                            <td class="align-middle text-start">
                                <div class="form-check form-check-custom">
                                    <input class="form-check-input checkbox_feed fs-17" <?php echo $value->status == 'Synced' ? '' : 'disabled="disabled"' ?> type="checkbox" name="" id="checkFeed_<?php echo esc_attr($value->id); ?>" value="<?php echo esc_attr($value->id); ?>">
                                </div>
                            </td>
                            <td class="align-middle text-start">
                                <?php if ($value->is_delete === '1') { ?>
                                    <span style="cursor: no-drop;">
                                        <?php echo esc_html($value->feed_name); ?>
                                    </span>
                                <?php } else { ?>
                                    <span>
                                        <a title="Go to feed wise product list" href="<?php echo esc_url($site_url . 'product_list&id=' . $value->id); ?>"><?php echo esc_html($value->feed_name); ?></a>
                                    </span>
                                <?php } ?>

                            </td>
                            <td class="align-middle text-center">
                                <?php
                                foreach ($contData as $key => $country) {
                                    if ($value->target_country === $country->code) { ?>
                                        <?php echo esc_html($country->name); ?>
                                <?php }
                                }
                                ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php foreach ($channel_id as $val) {
                                    if ($val === '1') { ?>
                                        <?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/google_channel_logo.png'),
                                            array(
                                                'img' => array(
                                                    'src' => true,
                                                    'alt' => true,
                                                    'class' => true,
                                                    'style' => true,
                                                ),
                                            )
                                        ); ?>
                                    <?php } elseif ($val === '2') { ?>
                                        <?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/fb_channel_logo.png'),
                                            array(
                                                'img' => array(
                                                    'src' => true,
                                                    'alt' => true,
                                                    'class' => true,
                                                    'style' => true,
                                                ),
                                            )
                                        ); ?>
                                    <?php } elseif ($val === '3') { ?>
                                        <?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/tiktok_channel_logo.png'),
                                            array(
                                                'img' => array(
                                                    'src' => true,
                                                    'alt' => true,
                                                    'class' => true,
                                                    'style' => true,
                                                ),
                                            )
                                        ); ?>
                                    <?php } elseif ($val === '4') { ?>
                                        <?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/ms_channel_logo.svg'),
                                            array(
                                                'img' => array(
                                                    'src' => true,
                                                    'alt' => true,
                                                    'class' => true,
                                                    'style' => true,
                                                ),
                                            )
                                        ); ?>
                                <?php }
                                } ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php echo esc_html(number_format_i18n($value->total_product ? $value->total_product : 0)); ?>
                            </td>
                            <td class="align-middle text-center">
                                <span class="dot <?php echo $value->auto_schedule === '1' ? 'dot-green' : 'dot-red'; ?>"></span>
                                <span>
                                    <?php echo $value->auto_schedule === '1' ? 'Yes' : 'No'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->auto_sync_interval !== 0 && $value->auto_schedule === '1' ? 'Every ' . esc_html($value->auto_sync_interval) . ' Days' : ' '; ?>
                                </p>
                            </td>
                            <td class="align-middle text-center" data-sort='" <?php echo esc_html(strtotime($value->created_date)) ?> "'>
                                <span>
                                    <?php echo esc_html(date_format(date_create($value->created_date), "d M Y")); ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo esc_html(date_format(date_create($value->created_date), "H:i a")); ?>
                                </p>
                            </td>
                            <td class="align-middle text-center" data-sort='" <?php echo esc_html(strtotime($value->last_sync_date ?? '0000-00-00 00:00:00')) ?> "'>
                                <span>
                                    <?php echo $value->last_sync_date && $value->last_sync_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->last_sync_date), "d M Y")) : 'NA'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->last_sync_date && $value->last_sync_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->last_sync_date), "H:i a")) : ''; ?>
                                </p>
                            </td>
                            <td class="align-middle text-center" data-sort="<?php echo isset($value->next_schedule_date) && $value->next_schedule_date !== '0000-00-00 00:00:00' ? esc_html(strtotime($value->next_schedule_date)) : ''; ?>">
                                <span>
                                    <?php echo $value->next_schedule_date && $value->next_schedule_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->next_schedule_date), "d M Y")) : 'NA'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->next_schedule_date && $value->next_schedule_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->next_schedule_date), "H:i a")) : ''; ?>
                                </p>
                            </td>
                            <td class="align-middle text-center">
                                <?php if ($value->is_delete === '1') { ?>
                                    <span class="badgebox rounded-pill  fs-10 deleted">
                                        Deleted
                                    </span>
                                    <?php } else {
                                    $draft = 0;
                                    $inprogress = 0;
                                    $synced = 0;
                                    $failed = 0;
                                    switch ($value->status) {
                                        case 'Draft':
                                            $draft++;
                                            break;

                                        case 'In Progress':
                                            $inprogress++;
                                            break;

                                        case 'Synced':
                                            $synced++;
                                            break;

                                        case 'Failed':
                                            $failed++;
                                            break;
                                    }

                                    switch ($value->tiktok_status) {
                                        case 'Draft':
                                            $draft++;
                                            break;

                                        case 'In Progress':
                                            $inprogress++;
                                            break;

                                        case 'Synced':
                                            $synced++;
                                            break;

                                        case 'Failed':
                                            $failed++;
                                            break;
                                    }

                                    switch ($value->fb_status) {
                                        case 'Draft':
                                            $draft++;
                                            break;

                                        case 'In Progress':
                                            $inprogress++;
                                            break;

                                        case 'Synced':
                                            $synced++;
                                            break;

                                        case 'Failed':
                                            $failed++;
                                            break;
                                    }

                                    switch ($value->ms_status) {
                                        case 'Draft':
                                            $draft++;
                                            break;

                                        case 'In Progress':
                                            $inprogress++;
                                            break;

                                        case 'Synced':
                                            $synced++;
                                            break;

                                        case 'Failed':
                                            $failed++;
                                            break;
                                    }

                                    if ($draft !== 0) { ?>
                                        <div class="badgebox draft" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Draft'); ?>
                                            <div class="count-badge" style="margin-top:-4px;color:#DCA310">
                                                <?php echo esc_html($draft) ?>
                                            </div>
                                        </div>
                                        <input type="hidden" class="draftGmcImg" value="<?php echo $value->status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="draftTiktokImg" value="<?php echo $value->tiktok_status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="draftfbImg" value="<?php echo $value->fb_status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/fb_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="draftmsImg" value="<?php echo $value->ms_status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms_channel_logo.svg") . "' />" : '' ?>">
                                    <?php }
                                    if ($inprogress !== 0) { ?>
                                        <div class="badgebox inprogress" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('In Progress'); ?>
                                            <div class="count-badge" style="margin-top:-4px;color:#209EE1">
                                                <?php echo esc_html($inprogress) ?>
                                            </div>
                                        </div>
                                        <input type="hidden" class="inprogressGmcImg" value="<?php echo $value->status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="inprogressTiktokImg" value="<?php echo $value->tiktok_status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="inprogressfbImg" value="<?php echo $value->fb_status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/fb_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="inprogressmsImg" value="<?php echo $value->ms_status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms_channel_logo.svg") . "' />" : '' ?>">
                                    <?php }
                                    if ($synced !== 0) { ?>
                                        <div class="badgebox xyz synced" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Synced'); ?>
                                            <div class="count-badge" style="margin-top:-4px;color:#09bd83">
                                                <?php echo esc_html($synced) ?>
                                            </div>
                                        </div>
                                        <input type="hidden" class="syncedGmcImg" value="<?php echo $value->status == 'Synced' ? "<img class='synced-status xyz-s' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="syncedTiktokImg" value="<?php echo $value->tiktok_status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="syncedfbImg" value="<?php echo $value->fb_status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/fb_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="syncedmsImg" value="<?php echo $value->ms_status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms_channel_logo.svg") . "' />" : '' ?>">
                                    <?php }
                                    if ($failed !== 0) { ?>
                                        <div class="badgebox failed" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Failed'); ?>
                                            <div class="count-badge" style="margin-top:-4px;color:#f43e56">
                                                <?php echo esc_html($failed) ?>
                                            </div>
                                        </div>
                                        <input type="hidden" class="failedGmcImg" value="<?php echo $value->status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="failedTiktokImg" value="<?php echo $value->tiktok_status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="failedfbImg" value="<?php echo $value->fb_status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/fb_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="failedmsImg" value="<?php echo $value->ms_status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms_channel_logo.svg") . "' />" : '' ?>">
                                <?php }
                                } //end if 
                                ?>
                            </td>
                            <td class="align-middle">
                                <div class="dropdown position-static">
                                    <?php if ($value->is_delete === '1') { ?>
                                        <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="pointer-events: none;">
                                            <span class="material-symbols-outlined">
                                                more_horiz
                                            </span>
                                        </button>
                                    <?php } else { ?>
                                        <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined">
                                                more_horiz
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark bg-white">
                                            <li class="mb-0 pointer"><a class="dropdown-item text-secondary border-bottom fs-12" onclick="editFeed(<?php echo esc_html($value->id); ?>)">Edit</a>
                                            </li>
                                            <li class="mb-0 pointer"><a class="dropdown-item text-secondary border-bottom fs-12 " onclick="duplicateFeed(<?php echo esc_html($value->id); ?>)">Duplicate</a>
                                            </li>
                                            <li class="mb-0 pointer"><a class="dropdown-item text-secondary fs-12" onclick="deleteFeed(<?php echo esc_html($value->id); ?>)">Delete</a></li>
                                        </ul>
                                    <?php } //end if
                                    ?>
                                </div>
                            </td>
                        </tr>
                <?php } //end foreach
                } //end if
                $feedIdString = implode(",", $feedIdArr);
                ?>
            </tbody>
        </table>
        <input type="hidden" id="selecetdCampaign" name="selecetdCampaign" value="">
    </div>
    <small class="fw-400 text-secondary">
        <i><?php
            printf(
                /* translators: %s: Total number of product */
                esc_html__('You have total %s products in your WooCommerce store', "enhanced-e-commerce-for-woocommerce-store"),
                esc_html(number_format_i18n($total_products))
            );
            ?></i>
    </small>
</div>
<hr />
<!-- Modal -->
<div class="modal fade" id="convCreateFeedModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ">
            <form id="feedForm" onfocus="this.className='focused'">
                <div id="loadingbar_blue_modal" class="progress-materializecss d-none ps-2 pe-2" style="width:98%">
                    <div class="indeterminate"></div>
                </div>
                <div class="modal-header bg-light p-2 ps-4">
                    <h5 class="modal-title fs-16 fw-500" id="feedType">
                        <?php esc_html_e("Create New Feed", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="jQuery('#feedForm')[0].reset()"></button>
                </div>
                <div class="modal-body ps-4 pt-0">
                    <div class="mb-4">
                        <label for="feed_name" class="col-form-label text-dark fs-14 fw-500">
                            <?php esc_html_e("Feed Name", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                        <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Add a name to your feed for your reference, for example, 'April end-of-season sales' or 'Black Friday sales for the USA'.">
                            info
                        </span>
                        <input type="text" class="form-control fs-14" name="feedName" id="feedName" placeholder="e.g. New Summer Collection">
                    </div>
                    <div class="mb-2 row">
                        <div class="col-5">
                            <label for="auto_sync" class="col-form-label text-dark fs-14 fw-500">
                                <?php esc_html_e("Auto Sync", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Turn on this feature to schedule an automated product feed to keep your products up to date with the changes made in the products. You can come and change this any time.">
                                info
                            </span>
                        </div>
                        <div class="form-check form-switch col-7 mt-0 fs-5">
                            <input class="form-check-input" type="checkbox" name="autoSync" id="autoSync" checked>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-5">
                            <label for="auto_sync_interval" class="col-form-label text-dark fs-14 fw-500">
                                <?php esc_html_e("Auto Sync Interval", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Set the number of days to schedule the next auto-sync for the products in this feed. You can come and change this any time.">
                                info
                            </span>
                        </div>
                        <div class="col-7">
                            <input type="text" class="form-control-sm fs-14 " readonly="readonly" name="autoSyncIntvl" id="autoSyncIntvl" size="3" min="1" onkeypress="return ( event.charCode === 8 || event.charCode === 0 || event.charCode === 13 || event.charCode === 96) ? null : event.charCode >= 48 && event.charCode <= 57" oninput="removeZero();" value="25">
                            <label for="" class="col-form-label fs-14 fw-400">
                                <?php esc_html_e("Days", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span>
                                <a target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=innersetting_pfm&utm_campaign=feedpopup&plugin_name=aio">
                                    <b>
                                        Upgrade To Pro
                                    </b>
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-5">
                            <label for="target_country" class="col-form-label text-dark fs-14 fw-500" name="">
                                <?php esc_html_e("Target Country", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Specify the target country for your product feed. Select the country where you intend to promote and sell your products.">
                                info
                            </span>
                        </div>
                        <div class="col-7">
                            <select class="select2 form-select form-select-sm mb-3" aria-label="form-select-sm example" style="width: 100%" name="target_country" id="target_country">
                                <option value="">Select Country</option>
                                <?php
                                $selecetdCountry = $conv_data['user_country'];
                                foreach ($contData as $key => $value) {
                                ?>
                                    <option value="<?php echo esc_attr($value->code) ?>" <?php echo $selecetdCountry === $value->code ? 'selected = "selecetd"' : '' ?>>
                                        <?php echo esc_html($value->name) ?></option>"
                                <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="auto_sync_interval" class="col-form-label text-dark fs-14 fw-500">
                            <?php esc_html_e("Select Channel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                        <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Below is the list of channels that you have linked for product feed. Please note you will not be able to make any changes in the selected channels once product feed process is done.">
                            info
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox" value="<?php printf('%s', esc_html($microsoft_merchant_center_id)); ?>" id="mmc_id" name="mmc_id" <?php echo $microsoft_merchant_center_id !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("Microsoft Merchant Center Account :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400">
                                <?php
                                printf(
                                    '%s',
                                    esc_html($microsoft_merchant_center_id)
                                );
                                ?>
                            </label>
                        </div>
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 woow-830 errorChannel" type="checkbox" value="<?php printf('%s', esc_html($google_merchant_center_id)); ?>" id="gmc_id" name="gmc_id" <?php echo !empty($google_merchant_center_id) ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("Google Merchant Center Account :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400">
                                <?php
                                printf(
                                    '%s',
                                    esc_html($google_merchant_center_id)
                                );
                                ?>
                            </label>
                        </div>
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox" value="" id="tiktok_id" name="tiktok_id" <?php echo $tiktok_business_account !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("TikTok Catalog Id :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400 tiktok_catalog_id">

                            </label>
                        </div>
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox" value="" id="fb_id" name="fb_id" <?php echo $facebook_business_account !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("Facebook Catalog Id :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400 fb_id">
                                <?php echo isset($ee_options['facebook_setting']['fb_catalog_id']) ? esc_html($ee_options['facebook_setting']['fb_catalog_id']) : ''; ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <input type="hidden" id="edit" name="edit">
                    <input type="hidden" value="<?php echo esc_attr($conv_data['user_domain']); ?>" class="fromfiled" name="url" id="url" placeholder="Enter Website">
                    <input type="hidden" id="is_mapping_update" name="is_mapping_update" value="">
                    <input type="hidden" id="last_sync_date" name="last_sync_date" value="">
                    <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal" onclick="jQuery('#feedForm')[0].reset()">
                        <?php esc_html_e("Cancel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                    <button type="button" class="btn btn-soft-primary btn-sm" id="submitFeed">
                        <?php esc_html_e("Create and Next", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Error Save Modal -->
<div class="modal fade" id="conv_save_error_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 px-3">

            </div>
            <div class="modal-body text-center p-0">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/logos/error_logo.png', '', '', 'width:184px;'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>
                <h3 class="fw-normal pt-3">Error</h3>
                <span id="conv_save_error_txt" class="mb-1 lh-lg px-3"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-yellow-bg m-auto text-white dismissErrorModal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Error Save Modal End -->
<!-- Success Save Modal 2 -->
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
                    <?php esc_html_e("How did you like our feed creation? Any feedback is appreciated! ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <a target="_blank" href="https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store/reviews/?rate=5#rate-response" class="conv-link-blue">Leave a Review</a>
                </h3>
                <span id="conv_save_success_txt" class="mb-1 d-flex justify-content-center text-dark fs-16 px-2"></span>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 mb-1 modalFooterSuccess w-100" style="display:flex; justify-content: center">
                <button class="btn fs-20 fw-normal w-100 text-white dismissModal" data-bs-dismiss="modal" style="background-color: #209365;">Close</button>
            </div>
        </div>
    </div>
</div>

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
                    <div class="google_connect_url google-btn">
                        <div class="google-icon-wrapper">
                            <?php echo wp_kses(
                                enhancad_get_plugin_image('/admin/images/g-logo.png'),
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
                        <p class="btn-text">
                            <b><?php esc_html_e("Sign in with google", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                        </p>
                    </div>
                    <p><?php esc_html_e("Make sure you sign in with the google email account that has all privileges to access google analytics, google ads and google merchant center account that you want to configure for your store.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                </div>
                <div class="google_signin_sec_right">
                    <h6><?php esc_html_e("Why do I need to sign in with google?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h6>
                    <p><?php esc_html_e("When you sign in with Google, we ask for limited programmatic access for your accounts in order to automate below features for you:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong><?php esc_html_e("1. Google Analytics:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To give you option to select GA accounts, to show actionable google analytics reports in plugin dashboard and to link your google ads account with google analytics account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong><?php esc_html_e("2. Google Ads:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To automate dynamic remarketing, conversion and enhanced conversion tracking and to create performance campaigns if required.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong><?php esc_html_e("3. Google Merchant Center:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To automate product feed using content api and to set up your GMC account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Success Save Modal End -->
<script>
    jQuery(document).ready(function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        /*********************Card Popover Start***********************************************************************/
        jQuery(document).on('mouseover', '.synced', function() {
            var syncedGmcImg = jQuery(this).next('.syncedGmcImg').val();
            var syncedTiktokImg = jQuery(this).next('.syncedGmcImg').next('.syncedTiktokImg').val();
            var syncedfbImg = jQuery(this).next('.syncedGmcImg').next('.syncedTiktokImg').next(
                '.syncedfbImg').val();
            var syncedmsImg = jQuery(this).next('.syncedGmcImg').next('.syncedTiktokImg').next(
                '.syncedfbImg').next('.syncedmsImg').val();
            var content = '<div class="popover-box border-synced">' + syncedGmcImg + '  ' +
                syncedTiktokImg + ' ' + syncedfbImg + ' ' + syncedmsImg + '</div>';
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        })

        jQuery(document).on('mouseover', '.failed', function() {
            var failedGmcImg = jQuery(this).next('.failedGmcImg').val();
            var failedTiktokImg = jQuery(this).next('.failedGmcImg').next('.failedTiktokImg').val();
            var failedfbImg = jQuery(this).next('.failedGmcImg').next('.failedTiktokImg').next(
                '.failedfbImg').val();
            var failedmsImg = jQuery(this).next('.failedGmcImg').next('.failedTiktokImg').next(
                '.failedfbImg').next('.failedmsImg').val();
            var content = "<div class='popover-box border-failed'>" + failedGmcImg + "  " +
                failedTiktokImg + " " + failedfbImg + " " + failedmsImg + "</div>";
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        })

        jQuery(document).on('mouseover', '.draft', function() {
            var draftGmcImg = jQuery(this).next('.draftGmcImg').val();
            var draftTiktokImg = jQuery(this).next('.draftGmcImg').next('.draftTiktokImg').val();
            var draftfbImg = jQuery(this).next('.draftGmcImg').next('.draftTiktokImg').next('.draftfbImg')
                .val();
            var draftmsImg = jQuery(this).next('.draftGmcImg').next('.draftTiktokImg').next('.draftfbImg').next('.draftmsImg').val();
            var content = '<div class="popover-box border-draft">' + draftGmcImg + '  ' + draftTiktokImg +
                ' ' + draftfbImg + ' ' + draftmsImg + '</div>';
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        })
        jQuery(document).on('mouseover', '.inprogress', function() {
            var inprogressGmcImg = jQuery(this).next('.inprogressGmcImg').val();
            var inprogressTiktokImg = jQuery(this).next('.inprogressGmcImg').next('.inprogressTiktokImg')
                .val();
            var inprogressfbImg = jQuery(this).next('.inprogressGmcImg').next('.inprogressTiktokImg').next(
                '.inprogressfbImg').val();
            var inprogressmsImg = jQuery(this).next('.inprogressGmcImg').next('.inprogressTiktokImg').next(
                '.inprogressfbImg').next('.inprogressmsImg').val();
            var content = '<div class="popover-box border-inprogress">' + inprogressGmcImg + '  ' +
                inprogressTiktokImg + ' ' + inprogressfbImg + ' ' + inprogressmsImg + '</div>';
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        })
        /*********************Card Popover  End**************************************************************************/
        /*********************Custom DataTable for Search functionality Start*********************************************/
        jQuery('#feed_list_table').DataTable({
            order: [
                [6, 'desc']
            ],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12't>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            rowReorder: true,
            columnDefs: [{
                    orderable: true,
                    targets: 1
                },
                {
                    orderable: true,
                    targets: 2
                },
                {
                    orderable: true,
                    targets: 4
                },
                {
                    orderable: true,
                    targets: 5
                },
                {
                    orderable: true,
                    targets: 6
                },
                {
                    orderable: true,
                    targets: 7
                },
                {
                    orderable: true,
                    targets: 8
                },
                {
                    orderable: false,
                    targets: '_all'
                },

            ],

            initComplete: function() {
                jQuery('#search_feed').on('input', function() {
                    jQuery('#feed_list_table').DataTable().search(jQuery(this).val()).draw();
                });
            }
        });

        jQuery('.createCampaign').insertAfter('#feed_list_table_filter');
        jQuery('#create_new_feed_div').insertAfter('#feed_list_table_filter');
        jQuery('#feed_list_table_filter').insertAfter('#feed_list_table_length');
        jQuery('#feed_list_table_filter').parent().addClass('d-flex align-items-center');
        jQuery('#create_new_feed_div').parent().addClass('d-flex align-items-center justify-content-end');


        /*********************Custom DataTable for Search functionality End***********************************************/
        /****************Create Feed call start********************************/
        jQuery('#create_new_feed, .create_new_feed').on('click', function(events) {
            //jQuery('#gmc_id').attr('disabled', false);
            //jQuery('#tiktok_id').attr('disabled', false);
            jQuery('#target_country').attr('disabled', false);
            jQuery('#autoSyncIntvl').attr('disabled', false);
            jQuery("#feedForm")[0].reset();
            jQuery('#feedType').text('Create New Feed');
            jQuery('#submitFeed').text('Create and Next');
            jQuery('#edit').val('');
            jQuery('#tiktok_id').val('');
            jQuery('.tiktok_catalog_id').empty();
            jQuery('.tiktok_catalog_id').removeClass('text-danger');
            jQuery('#convCreateFeedModal').modal('show');
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            jQuery('.select2').select2({
                dropdownParent: jQuery("#convCreateFeedModal")
            });
            var tiktok_business_account = "<?php echo esc_js($tiktok_business_account) ?>";
            if (tiktok_business_account !== '' && jQuery('#tiktok_id').is(":checked")) {
                getCatalogId(jQuery('#target_country').find(":selected").val());
            }
        });
        /****************Create Feed call end***********************************/
        /****************Feed Name error dismissed start************************/
        jQuery(document).on('input', '#feedName', function(e) {
            e.preventDefault();
            jQuery('#feedName').css('margin-left', '0px');
            jQuery('#feedName').css('margin-right', '0px');
            jQuery('#feedName').removeClass('errorInput');
        });
        /****************Feed Name error dismissed end**************************/
        /****************Submit Feed call start*********************************/
        jQuery(document).on('click', '#submitFeed', function(e) {
            e.preventDefault();
            let feedName = jQuery('#feedName').val();
            if (feedName === '') {
                jQuery('#feedName').css('margin-left', '0px');
                jQuery('#feedName').css('margin-right', '0px');
                jQuery('#feedName').addClass('errorInput');
                var l = 4;
                for (var i = 0; i <= 2; i++) {
                    jQuery('#feedName').animate({
                        'margin-left': '+=' + (l = -l) + 'px',
                        'margin-right': '-=' + l + 'px'
                    }, 50);
                }
                return false;
            }

            let autoSyncIntvl = jQuery('#autoSyncIntvl').val();
            if (autoSyncIntvl === '') {
                jQuery('#autoSyncIntvl').css('margin-left', '0px');
                jQuery('#autoSyncIntvl').css('margin-right', '0px');
                jQuery('#autoSyncIntvl').addClass('errorInput');
                var l = 4;
                for (var i = 0; i <= 2; i++) {
                    jQuery('#autoSyncIntvl').animate({
                        'margin-left': '+=' + (l = -l) + 'px',
                        'margin-right': '-=' + l + 'px'
                    }, 50);
                }
                return false;
            }

            let target_country = jQuery('#target_country').find(":selected").val();
            if (target_country === "") {
                jQuery('.select2-selection').css('border', '1px solid #ef1717');
                return false;
            }

            if (!jQuery('#gmc_id').is(":checked") &&
                !jQuery('#tiktok_id').is(":checked") &&
                !jQuery('#fb_id').is(':checked') &&
                !jQuery('#mmc_id').is(":checked")) {

                jQuery('.errorChannel').not(':disabled').css('border', '1px solid red');
                return false;
            }
            jQuery('#submitFeed').addClass("disabledsection");
            save_feed_data();
        });

        /****************Submit Feed call end***********************************/
        /********************Modal POP up validation on click remove**********************************/
        jQuery(document).on('click', '#gmc_id', function(e) {
            jQuery('.errorChannel').css('color', '');
        });
        jQuery(document).on('click', '#tiktok_id', function(e) {
            jQuery('.errorChannel').css('border', '');
        });
        jQuery(document).on('click', '#fb_id', function(e) {
            jQuery('.errorChannel').css('border', '');
        });
        jQuery(document).on('click', '#mmc_id', function(e) {
            jQuery('.errorChannel').css('border', '');
        });
        /********************Modal POP up validation on click remove end **********************************/
        /****************Get tiktok catalog id on target country change ***************************************/
        jQuery(document).on('change', '#target_country', function(e) {
            var tiktok_business_account = "<?php echo esc_js($tiktok_business_account) ?>";
            jQuery('.select2-selection').css('border', '1px solid #c6c6c6');
            let target_country = jQuery('#target_country').find(":selected").val();
            jQuery('#tiktok_id').empty();
            jQuery('.tiktok_catalog_id').empty()
            if (target_country !== "" && tiktok_business_account !== "" && jQuery('input#tiktok_id').is(
                    ':checked')) {
                getCatalogId(target_country);
            }
        });
        /****************Get tiktok catalog id on target country change end ***************************************/
        /************************************* Auto Sync Toggle Button Start*************************************************************************/
        jQuery(document).on('change', '#autoSync', function() {
            var autoSync = jQuery('input#autoSync').is(':checked');
            if (autoSync) {
                jQuery('#autoSyncIntvl').attr('disabled', false);
            } else {
                jQuery('#autoSyncIntvl').attr('disabled', true);
                jQuery('#autoSyncIntvl').val(25);
                jQuery('#autoSyncIntvl').removeClass('errorInput');
            }

        });
        /************************************* Auto Sync Toggle Button End*************************************************************************/
        /****************Get tiktok catalog id on check box change ***************************************/
        jQuery(document).on('change', '#tiktok_id', function() {
            jQuery('.tiktok_catalog_id').empty();
            jQuery('#tiktok_id').val('');
            if (jQuery('#tiktok_id').is(":checked")) {
                getCatalogId(jQuery('#target_country').find(":selected").val())
            }
        });
        /****************Get tiktok catalog id on check box change end ***************************************/
    });
    /*************************************Process Loader Start*************************************************************************/
    function conv_change_loadingbar(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
        } else {
            jQuery("#loadingbar_blue").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }

    function conv_change_loadingbar_modal(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue_modal").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
            jQuery('#submitFeed').attr('disabled', true);
        } else {
            jQuery("#loadingbar_blue_modal").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
            jQuery('#submitFeed').attr('disabled', false);
        }
    }
    /*************************************Process Loader End*************************************************************************/
    /*************************************Restrict Zero start*************************************************************************/
    function removeZero() {
        var val = jQuery("#autoSyncIntvl").val();
        if (val === '0') {
            jQuery("#autoSyncIntvl").val('')
        }
    }
    /*************************************Restrict Zero  End*************************************************************************/
    /*************************************Save Feed Data Start*************************************************************************/
    function save_feed_data(google_merchant_center_id, catalog_id) {
        console.log('saving from 1295 line'); // woow 1295
        var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
        let edit = jQuery('#edit').val();
        var data = {
            action: "save_feed_data",
            feedName: jQuery('#feedName').val(),
            google_merchant_center: jQuery('input#gmc_id').is(':checked') ? '1' : '',
            fb_catalog_id: jQuery('input#fb_id').is(':checked') ? '2' : '',
            tiktok_id: jQuery('input#tiktok_id').is(':checked') ? '3' : '',
            microsoft_merchant_center: jQuery('input#mmc_id').is(':checked') ? '4' : '',
            tiktok_catalog_id: jQuery('input#tiktok_id').is(':checked') ? jQuery('input#tiktok_id').val() : '',
            autoSync: jQuery('input#autoSync').is(':checked') ? '1' : '0',
            autoSyncIntvl: '25',
            edit: edit,
            last_sync_date: jQuery('#last_sync_date').val(),
            is_mapping_update: jQuery('#is_mapping_update').val(),
            target_country: jQuery('#target_country').find(":selected").val(),
            customer_subscription_id: "<?php echo esc_js($subscriptionId) ?>",
            tiktok_business_account: "<?php echo esc_js($tiktok_business_account) ?>",
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar_modal('show');
            },
            error: function(err, status) {
                conv_change_loadingbar_modal('hide');
                jQuery('#convCreateFeedModal').modal('hide');
                jQuery("#conv_save_error_txt").html('Error occured.');
                jQuery("#conv_save_error_modal").modal("show");
            },
            success: function(response) {
                if (response.id) {
                    var feedurl = "<?php echo esc_url_raw($site_url . 'product_list&id='); ?>" + response.id;
                    location.href = feedurl

                } else if (response.errorType === 'tiktok') {
                    jQuery('.tiktok_catalog_id').empty();
                    jQuery('.tiktok_catalog_id').html(response.message);
                    jQuery('.tiktok_catalog_id').addClass('text-danger');

                } else {
                    jQuery('#convCreateFeedModal').modal('hide');
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                }
                conv_change_loadingbar_modal('hide');
            }
        });

    }
    /*************************************Save Feed Data End***************************************************************************/
    /*************************************Edit Feed Data Start*************************************************************************/
    function editFeed($id) {
        jQuery('#gmc_id').attr('disabled', false);
        jQuery('#target_country').attr('disabled', false);
        var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
        var data = {
            action: "get_feed_data_by_id",
            id: $id,
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar('show');
            },
            error: function(err, status) {
                conv_change_loadingbar('hide');
                jQuery("#conv_save_error_txt").html('Error occured.');
                jQuery("#conv_save_error_modal").modal("show");
            },
            success: function(response) {
                jQuery('#feedName').val(response[0].feed_name);
                jQuery('#last_sync_date').val(response[0].last_sync_date);
                jQuery('#is_mapping_update').val(response[0].is_mapping_update);
                jQuery('#autoSyncIntvl').val(response[0].auto_sync_interval);

                if (response[0].target_country) {
                    jQuery('#target_country').val(response[0].target_country);
                }
                if (response[0].auto_schedule === '1') {
                    jQuery('input#autoSync').prop('checked', true);
                    jQuery('#autoSyncIntvl').attr('disabled', false);
                } else {
                    jQuery('input#autoSync').prop('checked', false);
                    jQuery('#autoSyncIntvl').attr('disabled', true);
                }
                jQuery('#gmc_id').prop("checked", false);
                jQuery('#gmc_id').attr('disabled', false);
                jQuery('#tiktok_id').prop("checked", false);
                jQuery('#tiktok_id').attr('disabled', false);
                jQuery('.tiktok_catalog_id').empty();
                jQuery('#fb_id').prop("checked", false);
                jQuery('#fb_id').attr('disabled', false);
                jQuery('#mmc_id').prop("checked", false);
                jQuery('#mmc_id').attr('disabled', false);
                //jQuery('#fb_id').prop("checked", false);
                var tiktok_business_account = "<?php echo esc_js($tiktok_business_account) ?>";
                var google_merchant_center_id = "<?php echo esc_js($google_merchant_center_id) ?>";
                var facebook_business_account = "<?php echo esc_js($facebook_business_account) ?>";
                var microsoft_merchant_center_id = "<?php echo esc_js($microsoft_merchant_center_id) ?>";
                if (tiktok_business_account == "") {
                    jQuery('#tiktok_id').attr('disabled', true);
                    jQuery('#tiktok_id').attr('checked', false);
                }
                if (google_merchant_center_id == "") {
                    jQuery('#gmc_id').attr('disabled', true);
                    jQuery('#gmc_id').attr('checked', false);
                }
                if (facebook_business_account == "") {
                    jQuery('#fb_id').attr('disabled', true);
                    jQuery('#fb_id').attr('checked', false);
                }
                if (microsoft_merchant_center_id == "") {
                    jQuery('#mmc_id').attr('disabled', true);
                    jQuery('#mmc_id').attr('checked', false);
                }
                channel_id = response[0].channel_ids.split(",");
                jQuery.each(channel_id, function(index, val) {
                    if (val === '1') {
                        jQuery('#gmc_id').prop("checked", true);
                    }
                    if (val === '3') {
                        jQuery('#tiktok_id').prop("checked", true);
                        jQuery('#tiktok_id').val(response[0].tiktok_catalog_id);
                        jQuery('.tiktok_catalog_id').html(response[0].tiktok_catalog_id)
                    }
                    if (val == '2') {
                        jQuery('#fb_id').prop("checked", true);
                    }
                    if (val === '4') {
                        jQuery('#mmc_id').prop("checked", true);
                    }
                });
                if (response[0].is_mapping_update == '1') {
                    jQuery('#gmc_id').attr('disabled', true);
                    jQuery('#fb_id').attr('disabled', true);
                    jQuery('#tiktok_id').attr('disabled', true);
                    jQuery('#mmc_id').attr('disabled', true);
                    jQuery('#target_country').attr('disabled', true);
                }
                jQuery('#edit').val(response[0].id);
                jQuery('#centered').html();
                jQuery('#submitFeed').text('Update Feed');
                jQuery('#feedType').text('Edit Feed - ' + response[0].feed_name);
                conv_change_loadingbar('hide');
                jQuery('#target_country').select2({
                    dropdownParent: jQuery("#convCreateFeedModal")
                });
                jQuery('#convCreateFeedModal').modal('show');
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });
    }
    /*************************************Edit Feed Data End****************************************************************************/
    /*************************************Duplicate Feed Data Start*********************************************************************/
    function duplicateFeed($id) {
        var feed_count = jQuery('#feedCount').val();
        var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
        var data = {
            action: "ee_duplicate_feed_data_by_id",
            id: $id,
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar('show');
            },
            error: function(err, status) {
                conv_change_loadingbar('hide');
                jQuery("#conv_save_error_txt").html('Error occured.');
                jQuery("#conv_save_error_modal").modal("show");
            },
            success: function(response) {
                conv_change_loadingbar('hide');
                if (response.error === false) {
                    jQuery("#conv_save_success_txt").html(response.message);
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function() {
                        location.reload(true);
                    }, 2000);
                } else {
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                }
            }
        });
    }
    /*************************************Duplicate Feed Data End*********************************************************************/
    /*************************************DELETE Feed Data Start**********************************************************************/
    function deleteFeed($id) {
        if (confirm(
                "Alert! Deleting this feed will remove its products from the Google Merchant Center, affecting your campaigns. Make sure it aligns with your strategy. Questions? We're here!"
            )) {
            var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
            var data = {
                action: "ee_delete_feed_data_by_id",
                id: $id,
                conv_onboarding_nonce: conv_onboarding_nonce
            }
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {
                    conv_change_loadingbar('show');
                },
                error: function(err, status) {
                    conv_change_loadingbar('hide');
                    jQuery("#conv_save_error_txt").html('Error in Deleting Feed.');
                    jQuery("#conv_save_error_modal").modal("show");
                },
                success: function(response) {
                    conv_change_loadingbar('hide');
                    jQuery("#conv_save_success_txt").html(response.message);
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function() {
                        location.reload(true);
                    }, 1000);
                }
            });
        }
    }
    /*************************************Delete Feed Data End*************************************************************************/
    /*************************************Save Feed Data End***************************************************************************/
    function conv_change_loadingbar_header(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue_header").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
        } else {
            jQuery("#loadingbar_blue_header").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }
    /*************************Create Super AI Feed Start ************************************************************************/
    /*************************Slider animation start ************************************************************************/
    jQuery(document).on('click', '.toggleOpen', function() {
        jQuery('.toggleSpan').show(300);
    })
    jQuery(document).on('click', '.toggleClose', function() {
        jQuery('.toggleSpan').hide(300);
    })
    /*************************Slider animation end ************************************************************************/
    /*************************************Get saved catalog id by country code start **************************************************/
    function getCatalogId($countryCode) {
        var conv_country_nonce = "<?php echo esc_js(wp_create_nonce('conv_country_nonce')); ?>";
        var data = {
            action: "ee_getCatalogId",
            countryCode: $countryCode,
            conv_country_nonce: conv_country_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar_modal('show');
            },
            error: function(err, status) {
                //conv_change_loadingbar_modal('hide');
            },
            success: function(response) {
                jQuery('.tiktok_catalog_id').empty()
                jQuery('#tiktok_id').empty();
                jQuery('.tiktok_catalog_id').removeClass('text-danger');

                if (response.error == false) {
                    if (response.data.catalog_id !== '') {
                        jQuery('#tiktok_id').val(response.data.catalog_id);
                        jQuery('.tiktok_catalog_id').text(response.data.catalog_id)
                    } else {
                        jQuery('#tiktok_id').val('Create New');
                        jQuery('.tiktok_catalog_id').text(
                            'You do not have a catalog associated with the selected target country. Do not worry we will create a new catalog for you.'
                        );
                    }
                }
                conv_change_loadingbar_modal('hide');
            }
        });
    }
    /*************************************Get saved catalog id by country code End ****************************************************/
</script>
<script>
    /*********************************** Pmax Campaign related code start *************************************************************/
    var feedId = "<?php echo esc_js($feedIdString) ?>"; //Get all feedId 
    jQuery(document).on('change', "#selectAll", function() {
        jQuery(".checkbox_feed").not(':disabled').prop('checked', jQuery(this).prop('checked'));
        if (jQuery(this).prop('checked')) {
            if (feedId !== "") {
                jQuery('.campaignClass').attr('disabled', false);
                jQuery('#selecetdCampaign').val(feedId)
            }
        } else {
            jQuery('.campaignClass').attr('disabled', true);
            jQuery('#selecetdCampaign').val('')
        }
    })
    jQuery(document).on('change', '.checkbox_feed', function() {
        if (jQuery(this).prop('checked')) {
            let arr = Array();
            let thisVal = jQuery(this).val();
            let feedstr = jQuery('#selecetdCampaign').val();
            if (feedstr !== '') {
                arr = feedstr.split(',');
            }
            arr.push(thisVal);
            arr.join(',');
            jQuery('#selecetdCampaign').val(arr);
            jQuery('.campaignClass').attr('disabled', false);
        } else {
            let arr = Array();
            let thisVal = jQuery(this).val();
            let feedstr = jQuery('#selecetdCampaign').val();
            arr = feedstr.split(',');
            arr = jQuery.grep(arr, function(value) {
                return value != thisVal;
            });
            jQuery('#selecetdCampaign').val(arr);
            jQuery("#selectAll").prop('checked', false)
            if (jQuery('#selecetdCampaign').val() == '') {
                jQuery('.campaignClass').attr('disabled', true);
            }
        }
    })
    jQuery(document).on('click', '.page-item ', function() {
        let feedstr = jQuery('#selecetdCampaign').val();
        if (feedstr !== '') {
            arr = feedstr.split(',');
            jQuery.each(arr, function(i, v) {
                jQuery('#checkFeed_' + v).prop('checked', true)
            })
        }
    })
    jQuery(document).on('click', '.createCampaign', function() {
        const currentUrl = window.location.href;
        var newUrl = currentUrl.replace(/admin\.php\?page=.*$/, 'admin.php?page=conversios-pmax&cid=');
        var selectedValues = [];
        jQuery('.checkbox_feed:checked').each(function() {
            selectedValues.push(jQuery(this).val());
        });
        newUrl += selectedValues.join(',');
        window.location.href = newUrl;
    })

    jQuery(".google_connect_url").on("click", function() {
        const w = 600;
        const h = 650;
        const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
        const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

        const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document
            .documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document
            .documentElement.clientHeight : screen.height;

        const systemZoom = width / window.screen.availWidth;
        const left = (width - w) / 2 / systemZoom + dualScreenLeft;
        const top = (height - h) / 2 / systemZoom + dualScreenTop;
        var url = '<?php echo esc_url($googleConnect_url); ?>';
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
    jQuery(document).on('click', '.signinWithGoogle', function() {
        jQuery('#tvc_google_signin').addClass('showpopup');
        jQuery('body').addClass('scrlnone');
    });

    jQuery(".clsbtntrgr").on("click", function() {
        jQuery(this).closest('.pp-modal').removeClass('showpopup');
        jQuery('body').removeClass('scrlnone');
    });
    jQuery(document).on('click', '.gotopmaxlist', function() {
        window.location.replace("<?php echo esc_url($site_url_pmax); ?>");
    })
    /*********************************** Pmax Campaign related code End ***************************************************************/
</script>

<script>
    // make equale height divs for grid
    jQuery(document).ready(function($) {

        const gridItems = document.querySelector('#conv_grid_list_box').children;
        const rows = Array.from(gridItems).reduce((rows, item, index) => {
            const rowIndex = Math.floor(index / 4); // 4 columns
            rows[rowIndex] = rows[rowIndex] || [];
            rows[rowIndex].push(item);
            return rows;
        }, []);
        //console.log(rows); 4 columns
        rows.forEach((row) => {
            const maxHeight = Math.max(...row.map((item) => item.children[0].offsetHeight));
            row.forEach((item) => {
                item.children[0].style.minHeight = `${maxHeight}px`;
            });
        });
    });
</script>