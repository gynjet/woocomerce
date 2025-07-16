<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

add_filter('safe_style_css', function ($styles) {
    $styles[] = 'display';
    return $styles;
});
$TVC_Admin_Helper = new TVC_Admin_Helper();
$TVC_Admin_Helper->need_auto_update_db();
$TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
$TVCProductSyncHelper = new TVCProductSyncHelper();
$category = $TVC_Admin_Helper->get_tvc_product_cat_list_with_name();
$customApiObj = new CustomApi();
$merchantId = $TVC_Admin_Helper->get_merchantId();
$accountId = $TVC_Admin_Helper->get_main_merchantId();
$currentCustomerId = $TVC_Admin_Helper->get_currentCustomerId();
$subscriptionId = $TVC_Admin_Helper->get_subscriptionId();
$google_detail_api = $googledetail = $customApiObj->getGoogleAnalyticDetail($subscriptionId);
$TVC_Admin_Helper->get_feed_status();
//$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$conv_data = $TVC_Admin_Helper->get_store_data();
$ee_options = $TVC_Admin_Helper->get_ee_options_settings();
$site_url = "admin.php?page=conversios-google-shopping-feed&tab=feed_list";
$category_wrapper_obj = new Tatvic_Category_Wrapper();
$gmcAttributes = $TVC_Admin_Helper->get_gmcAttributes();
$wooCommerceAttributes = array_map("unserialize", array_unique(array_map("serialize", $TVCProductSyncHelper->wooCommerceAttributes())));
$ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
$total_products = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish']))->found_posts;
$tiktok_business_account = '';
if (isset($ee_options['tiktok_setting']['tiktok_business_id']) === TRUE && $ee_options['tiktok_setting']['tiktok_business_id'] !== '') {
    $tiktok_business_account = $ee_options['tiktok_setting']['tiktok_business_id'];
}
//echo '<pre>'; print_r($ee_options); echo '</pre>';
$google_merchant_center_id = '';
if (isset($ee_options['google_merchant_id']) === TRUE && $ee_options['google_merchant_id'] !== '') {
    $google_merchant_center_id = $ee_options['google_merchant_id'];
}
$facebook_business_account = '';
if (isset($ee_options['facebook_setting']['fb_business_id']) === TRUE && $ee_options['facebook_setting']['fb_business_id'] !== '') {
    $facebook_business_account = $ee_options['facebook_setting']['fb_business_id'];
}

$facebook_catalog_id = '';
if (isset($ee_options['facebook_setting']['fb_catalog_id']) === TRUE && $ee_options['facebook_setting']['fb_catalog_id'] !== '') {
    $facebook_catalog_id = $ee_options['facebook_setting']['fb_catalog_id'];
}

$facebook_business_account = '';
if (isset($ee_options['facebook_setting']['fb_business_id']) === TRUE && $ee_options['facebook_setting']['fb_business_id'] !== '') {
    $facebook_business_account = $ee_options['facebook_setting']['fb_business_id'];
}

$microsoft_merchant_center_id = '';
if (isset($ee_options['microsoft_merchant_center_id']) === TRUE && $ee_options['microsoft_merchant_center_id'] !== '') {
    $microsoft_merchant_center_id = $ee_options['microsoft_merchant_center_id'];
}

$microsoft_catalog_id = '';
if (isset($ee_options['ms_catalog_id']) === TRUE && $ee_options['ms_catalog_id'] !== '') {
    $microsoft_catalog_id = $ee_options['ms_catalog_id'];
}


if ($google_merchant_center_id === '' && $tiktok_business_account === '' && $facebook_catalog_id === '' && $microsoft_merchant_center_id === '') {
    wp_safe_redirect("admin.php?page=conversios&wizard=productFeedOdd"); //Odd
    exit;
}

//$getCountris = file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");
global $wp_filesystem;

$getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");

$contData = json_decode($getCountris);

//$path = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/category.json';
//$str = file_get_contents(ENHANCAD_PLUGIN_DIR . 'includes/setup/json/category.json');
$str = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . 'includes/setup/json/category.json');
$str = json_decode($str);
$data = unserialize(get_option('ee_options'));
?>
<div class="modal fade" id="conv_bad_req_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

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
                <h3 class="fw-normal pt-3">
                    <?php esc_html_e("Bad Request, Feed Id missing", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h3>
                <span id="conv_save_req_error_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <a href="<?php echo esc_url($site_url); ?>" type="button" class="btn conv-yellow-bg m-auto text-white"><?php esc_html_e("Go back", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
            </div>
        </div>
    </div>
</div>
<?php

if (!isset($_GET['id']) || filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) == '') {
    print_r("<script type='text/javascript'>jQuery(document).ready(function(){ jQuery('#conv_bad_req_modal').modal('show'); }); </script>");
?>
<?php
    return print_r('Cannot access this page, "Feed Id" is missing !!');
}

$where = '`id` = ' . esc_sql(sanitize_text_field(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT)));
$filed = ['id', 'feed_name', 'channel_ids', 'auto_sync_interval', 'auto_schedule', 'categories', 'attributes', 'filters', 'include_product', 'exclude_product', 'total_product', 'product_id_prefix', 'status', 'created_date', 'is_mapping_update', 'target_country', 'tiktok_status', 'tiktok_catalog_id', 'fb_status', 'ms_status', 'product_sync_batch_size'];
$result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
$product_sync_batch_size = isset($result[0]['product_sync_batch_size']) && $result[0]['product_sync_batch_size'] ? $result[0]['product_sync_batch_size'] : 200;
if ($result === FALSE) {
    print_r("<script type='text/javascript'> jQuery(document).ready(function(){ jQuery('#conv_bad_req_modal').modal('show'); }); </script>");
?>
<?php
    return print_r('"Feed Id not found", Bad Request..!!!!');
}
$disabled_attr = false;
if ($result[0]['attributes']) {
    $ee_mapped_attrs = json_decode($result[0]['attributes'], true);
    $disabled_attr = true;
}
$tempAddAttr = $ee_mapped_attrs;
$attr_id = $result[0]['attributes'] ? $result[0]['attributes'] : "1";
$if_exclude_product = $result[0]['exclude_product'] === TRUE ? $result[0]['exclude_product'] : "1";
$if_include_product = $result[0]['include_product'] === TRUE ? $result[0]['include_product'] : "1";
$p_ids = isset($result[0]['attributes']) && $result[0]['attributes'] !== '' ? json_decode($result[0]['attributes']) : '';
$p_id = isset($p_ids->id) ? $p_ids->id : '';

$filters = isset($result[0]['filters']) && $result[0]['filters'] !== '' ? json_decode($result[0]['filters']) : '';
$attr = '';
$condition = '';
$value = '';
// $filters = isset($result[0]['filters']) && $result[0]['filters'] !== '' ? json_decode($result[0]['filters']) : '';
$attr = '';
$condition = '';
$value = '';
$count = 0;
$html = '';
$filterAttributes = ['product_cat' => 'Category', 'ID' => 'Product Id', '_stock_status' => 'Stock Status'];
if ($filters !== '') {
    $count = 0;
    foreach ($filters as $val) {
        if ($val->attr == '_sku' || $val->attr == '_regular_price' || $val->attr == '_sale_price' || $val->attr == 'post_content' || $val->attr == 'post_excerpt' || $val->attr == 'post_title') {
            continue;
        }
        $attr .= $attr === '' ? $val->attr : ',' . $val->attr;
        $condition .= $condition === '' ? $val->condition : ',' . $val->condition;
        $value .= $value === '' ? $val->value : ',' . $val->value;
        $terms = '';
        $eachVallue = $val->value;
        if ($val->attr === 'product_cat') {
            $terms = get_term_by('id', $val->value, 'product_cat');
            $eachVallue = $terms->name;
        }
        if ($result[0]['is_mapping_update'] == '1') {
            $html .= '<div class="btn-group border rounded mt-1 me-1 disabled"><button class="btn btn-light btn-sm text-secondary fs-7 ps-1 pe-1 pt-0 pb-0" type="button">' . $filterAttributes[$val->attr] . '  <b>' . $val->condition . '</b> ' . $eachVallue . ' </button>
                        <button type="button" class="btn btn-sm btn-light onhover-close pt-0 pb-0" data-bs-toggle=""
                            aria-expanded="false" style="cursor: no-drop;">
                            <span class="material-symbols-outlined fs-6 pt-1 onhover-close">
                                close
                            </span>
                        </button>
                    </div>';
        } else {
            $html .= '<div class="btn-group border rounded mt-1 me-1 removecardThis" ><button value="' . $count++ . '" class="btn btn-light btn-sm text-secondary fs-7 ps-1 pe-1 pt-0 pb-0" type="button">' . $filterAttributes[$val->attr] . '  <b>' . $val->condition . '</b> ' . $eachVallue . ' </button>
                        <button type="button" class="btn btn-sm btn-light onhover-close pt-0 pb-0" data-bs-toggle=""
                            aria-expanded="false">
                            <span class="material-symbols-outlined fs-6 pt-1 onhover-close removecard">
                                close
                            </span>
                        </button>
                    </div>';
        }
    } //end foreach

} //end if

$woo_product = wp_count_posts('product')->publish;
$livechannel = 0;
$channel_id = explode(',', $result[0]['channel_ids']);
$googleDetail = $googledetail->data;
if (isset($google_detail['setting'])) {
    if ($google_detail['setting']) {
        $googleDetailWP = $google_detail['setting'];
    }
}
$conv_data['subscription_id'] = $googleDetail->id;
?>
<style>
    #categoryModal .modal-dialog .modal-content {
        overflow: hidden;
    }

    .table-responsive {
        overflow-x: inherit;
    }

    .dataTables_length,
    .dataTables_info {
        margin-top: 5px;
        margin-bottom: 5px;
        width: 50%;
        float: left;
    }

    .dataTables_paginate {
        padding: 10px;
    }

    .dataTables-search,
    .dataTables-paging {
        float: right;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .paginate_button {
        position: relative;
        /* display: block; */
        color: #0d6efd;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #dee2e6;
        font-size: 12px;
        padding: 0.375rem 0.75rem;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    .selectStatus {
        animation: test;
        animation-duration: 2s;
        animation-iteration-count: 2;
    }
</style>
<div class="container-fluid pt-4 px-50">
    <div class="row row-x-0 align-items-center">
        <div class="m-0 p-0 col-6">
            <div class="m-0 p-0 col-12">
                <div class="conv-heading-box d-flex align-items-center">
                    <span class="float-start d-flex align-items-center">
                        <a href="<?php echo esc_url($site_url); ?>" class="text-dark">
                            <label class="fs-20">
                                <?php esc_html_e("Feed Management", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                        </a>
                        <span class="material-symbols-outlined fs-14 mx-1"> arrow_forward_ios </span>
                        <label class="fw-400 fs-20 text-dark">
                            <?php echo esc_attr($result[0]['feed_name']); ?>
                        </label>
                    </span>
                    <div class="conv-link-blue d-flex align-items-center ms-2">
                        <span style="cursor: pointer" class="material-symbols-outlined fs-14 me-1" onclick="editFeed(<?php echo filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT); ?>)">edit</span>
                        <label class="fs-14 text" onclick="editFeed(<?php echo esc_js(sanitize_text_field(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT))); ?>)"><?php esc_html_e("Edit Feed", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-x-0 align-items-center">
        <?php
        if ((isset($result[0]['status']) && $result[0]['status'] === 'Draft') ||
            (isset($result[0]['tiktok_status']) && $result[0]['tiktok_status'] === 'Draft') ||
            (isset($result[0]['fb_status']) && $result[0]['fb_status'] === 'Draft') ||
            (isset($result[0]['ms_status']) && $result[0]['ms_status'] === 'Draft')
        ) {
            $class_column = 'col-12';
        } else {
            $class_column = 'col-8';
        }
        ?>
        <div class="woow-292 m-0 p-0 <?php echo esc_attr($class_column); ?>">
            <div class="conv-heading-box">
                <span>
                    <label class="fs-14 fw-500 defaultPointer">
                        <?php esc_html_e("Created : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <label class="col-form-label fs-14 fw-400 pt-2 text-secondary defaultPointer">
                        <?php echo esc_html(date_format(date_create($result[0]['created_date']), "d-m-Y")); ?>
                        <?php echo esc_html(date_format(date_create($result[0]['created_date']), "H:i a")); ?>
                    </label>
                    <span class="ms-1 me-1">|</span>
                    <label class="fs-14 fw-500 defaultPointer">
                        <?php esc_html_e("Auto Sync : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <label class="col-form-label fs-14 fw-400 pt-2 text-secondary defaultPointer">
                        <?php echo esc_html($result[0]['auto_schedule'] == '1' ? 'Yes , Every ' . $result[0]['auto_sync_interval'] . ' Days' : 'No'); ?>
                    </label>
                    <span class="ms-1 me-1">|</span>
                    <label class="fs-14 fw-500">
                        <?php esc_html_e("Channels Status: ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <label class="col-form-label fs-14 fw-400 pt-2">

                        <?php
                        $convfeedchnames = [];
                        foreach ($channel_id as $val) {
                            if ($val === '1') {
                                $convfeedchnames[] = "Google Merchant Center";

                        ?>
                                <img class="<?php echo esc_attr(strtolower(str_replace(' ', '', $result[0]['status']))) ?>-status" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png'); ?>" title="<?php echo "GMC Id - " . esc_attr($google_merchant_center_id) ?>" />
                            <?php } else if ($val === '2') {
                                $convfeedchnames[] = "Meta Catalog"; ?>
                                <img class="<?php echo esc_attr(strtolower(str_replace(' ', '', $result[0]['fb_status']))) ?>-status" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png'); ?>" title="<?php echo "Facebook Business Account - " . esc_attr($facebook_business_account) ?>" />
                            <?php } else if ($val === '3') {
                                $convfeedchnames[] = "Tiktok Catalog"; ?>
                                <img class="<?php echo esc_attr(strtolower(str_replace(' ', '', $result[0]['tiktok_status']))) ?>-status" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png'); ?>" title="<?php echo "Tiktok Business Account - " . esc_attr($tiktok_business_account) ?>" />
                            <?php } else if ($val === '4') {
                                $convfeedchnames[] = "Microsoft Merchant Center"; ?>
                                <img class="<?php echo esc_attr(strtolower(str_replace(' ', '', $result[0]['ms_status']))) ?>-status" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg'); ?>" title="<?php echo "MMC Id - " . esc_attr($microsoft_merchant_center_id) ?>" />
                        <?php }
                        }
                        ?>
                    </label>
                </span>
            </div>
        </div>

        <!-- Below in commented before 07 feb 2025 -->
        <div class="m-0 p-0 col-8 d-none">
            <span class="float-start fs-12">Note:
                <label class="fs-12 fw-400 text-secondary defaultPointer">
                    <?php echo esc_html('You have reached ' . number_format_i18n(round(($google_detail_api->data->product_count / $google_detail_api->data->max_limit) * 100)) . ' % of your product limit for the product feed, with a maximum allowance of ' . number_format_i18n($google_detail_api->data->max_limit));
                    ?>
                </label>
            </span>
        </div>

        <?php // echo '<pre>'; print_r($result); echo '</pre>'; wow 
        ?>
        <?php
        if ((isset($result[0]['status']) && $result[0]['status'] === 'Draft') ||
            (isset($result[0]['tiktok_status']) && $result[0]['tiktok_status'] === 'Draft') ||
            (isset($result[0]['fb_status']) && $result[0]['fb_status'] === 'Draft') ||
            (isset($result[0]['ms_status']) && $result[0]['ms_status'] === 'Draft') 
        ) {
            $class_1 = 'col-12';
            $class_2 = 'alert alert-danger';
        } else {
            $class_1 = 'col-4 d-flex justify-content-end';
            $class_2 = '';
        }
        ?>
        <div class="m-0 p-0 filteredProductSynDiv <?php echo esc_attr($class_1);  ?>">
            <div class="<?php echo esc_attr($class_2); ?> m-0 d-flex align-items-center justify-content-end fs-6">
                <?php if ($class_2 != '') : ?>
                    <div>
                        <span>Your feed is in draft mode. Sync your products with <?php echo esc_html(implode(", ", $convfeedchnames)) ?> by clicking here.</span>
                        <?php if (in_array('1', $channel_id)) { ?>
                            <div class="">
                                <span>Products will typically appear in the Microsoft Merchant Center within 24 to 48 hours.</span>
                            </div>
                        <?php } ?>
                    </div>

                <?php endif; ?>




                <?php
                $filteredProductSyn = 'filteredProductSyn';
                if ($result[0]['status'] === 'In Progress' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1) {
                    $filteredProductSyn = '';
                }
                if ($result[0]['status'] === 'Draft' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1) {
                    $filteredProductSyn = '';
                }

                if ($result[0]['tiktok_status'] === 'In Progress' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1) {
                    $filteredProductSyn = '';
                }
                if ($result[0]['tiktok_status'] === 'Draft' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1) {
                    $filteredProductSyn = '';
                }

                if ($result[0]['fb_status'] === 'Draft' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1) {
                    $filteredProductSyn = '';
                }
                if ($result[0]['fb_status'] === 'In Progress' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1) {
                    $filteredProductSyn = '';
                }

                if ($result[0]['ms_status'] === 'Draft' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1) {
                    $filteredProductSyn = '';
                }
                if ($result[0]['ms_status'] === 'In Progress' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1) {
                    $filteredProductSyn = '';
                }
                ?>
                <button type="button" class="btn btn-soft-primary float-end ms-2 fs-14 fw-500 <?php echo esc_attr($filteredProductSyn) ?> " name="filteredProductSyn" id="filteredProductSyn" value="syncAll" <?php echo ($filteredProductSyn == '') ? 'style="cursor: no-drop;"' : '' ?>>
                    <?php
                    esc_html_e("Sync 0 Products", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid px-50 py-3">
    <div id="loadingbar_blue" class="progress-materializecss d-none ps-2 pe-2">
        <div class="indeterminate"></div>
    </div>
    <div class="card" style="max-width:100%; border-top-left-radius:8px;border-top-right-radius:8px;">
        <div class="card-body row ">
            <div class="col-9 pt-0">
                <span class="ps-2 pe-1 pt-1 pb-1 filterDiv rounded border <?php echo $result[0]['is_mapping_update'] == '1' ? '' : 'addFilter'; ?>" style="<?php echo $result[0]['is_mapping_update'] == '1' ? 'cursor: no-drop' : 'cursor: pointer'; ?>">
                    <span class="material-symbols-outlined fs-6 text-secondary">
                        filter_list
                    </span> <label class="fs-14 fw-500 mb-2 pt-1 text-secondary" style="<?php echo $result[0]['is_mapping_update'] == '1' ? 'cursor: no-drop' : ''; ?>">Filter</label>
                </span>
                <span class="ms-2 ps-2 pe-1 pt-1 pb-1 rounded border">
                    <label class="fs-14 fw-500  text-dark pb-1 defaultPointer">
                        <?php echo esc_html_e("Total Products : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <label class="fs-14 fw-500  text-secondary">
                            <?php echo esc_html(number_format_i18n($total_products)); ?>
                        </label>
                    </label>
                </span>
                <?php $total_synced = !empty($result[0]['total_product']) ? $result[0]['total_product'] : 0; ?>
                <span class="ms-2 ps-2 pe-1 pt-1 pb-1 rounded border <?php echo $total_synced == 0 ? 'd-none' : ''; ?>">
                    <label class="fs-14 fw-500 text-info pb-1 defaultPointer">
                        <?php echo esc_html_e("Synced : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <label class="fs-14 fw-500 text-secondary">
                            <?php echo esc_html($result[0]['status'] === 'Synced' || $result[0]['tiktok_status'] === 'Synced' || $result[0]['fb_status'] === 'Synced' || $result[0]['ms_status'] === 'Synced' ? $total_synced : 0); ?>
                        </label>
                    </label>
                </span>
            </div>
            <div class="col-3 pb-1">
                <span class="pb-2">
                    <input class="form-control me-2 " type="search" placeholder="Search" aria-label="Search" id="searchName" name="searchName" aria-controls="product_list_table">
                </span>
            </div>
            <div class="col-12 row pe-0">
                <div class="col-8" id="addFiltersCard">
                    <?php echo wp_kses_post($html); ?>
                </div>
                <div class="col-4 filter_count defaultPointer">

                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive shadow-sm" style="border-bottom-left-radius:8px;border-bottom-right-radius:8px;">
        <table class="table" id="product_list_table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col" class="padding-start-1 text-start sorting_disabled" style="width:3%">
                        <div class="form-check form-check-custom">
                            <input class="form-check-input checkbox fs-17" type="checkbox" name="syncAll" id="syncAll" checked value="syncAll">
                        </div>
                    </th>
                    <th scope="col" class="text-start sorting_disabled">
                        <?php esc_html_e("PRODUCT INFORMATION", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-start sorting_disabled">
                        <?php esc_html_e("CATEGORY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-start sorting_disabled">
                        <?php esc_html_e("AVAILABILITY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center sorting_disabled">
                        <?php esc_html_e("QUANTITY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center sorting_disabled">
                        <select class="selectStatus" id="selectStatus">
                            <?php if (in_array('1', $channel_id)) { ?> <option value="1" <?php echo isset($channel_id[0]) && $channel_id[0] == 1 ? 'selected' : '' ?>>GMC Status
                                </option> <?php } ?>
                            <?php if (in_array('3', $channel_id)) { ?><option value="3" <?php echo isset($channel_id[0]) && $channel_id[0] == 3 ? 'selected' : '' ?>>Tiktok
                                    Status</option> <?php } ?>
                            <?php if (in_array('2', $channel_id)) { ?><option value="2" <?php echo isset($channel_id[0]) && $channel_id[0] == 2 ? 'selected' : '' ?>>Facebook
                                    Status</option> <?php } ?>
                            <?php if (in_array('4', $channel_id)) { ?><option value="4" <?php echo isset($channel_id[0]) && $channel_id[0] == 4 ? 'selected' : '' ?>>MMC
                                    Status</option> <?php } ?>
                        </select>

                    </th>
                    <th scope="col" class="text-center sorting_disabled" style="width:5%">
                        <?php esc_html_e("ACTION", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="table-body">
            </tbody>
        </table>
    </div>
</div>
<hr />
<!-- Modal -->
<div class="modal fade" id="filterModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content ">
            <div class="modal-header bg-white p-2">
                <h5 class="modal-title fs-6 p-2 col-8">
                    <?php esc_html_e("Apply Filters for Product", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h5>
                <span class="col-4 addButton">
                    <label class="text-primary float-end p-1">
                        <?php esc_html_e("Add Filter", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <span class="material-symbols-outlined text-primary float-end">
                        add_circle
                    </span>
                </span>
            </div>
            <div class="modal-body ps-2 pt-2" id="">
                <form id="filterForm">
                    <div class="filterRow mb-3 row">
                        <div class="col-11 row">
                            <div class="col-4 productDiv">
                                <select class="select2 product" name="product[]">
                                    <option value="0">
                                        <?php esc_html_e("Select Attribute", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </option>
                                    <option value="product_cat">
                                        <?php esc_html_e("Category", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </option>
                                    <option value="ID">
                                        <?php esc_html_e("Product Id", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </option>
                                    <option value="_stock_status">
                                        <?php esc_html_e("Stock Status", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="col-4 conditionDiv">
                                <select class="select2 condition" name="condition[]">
                                    <option value="0">
                                        <?php esc_html_e("Select Conditions", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </option>
                                </select>
                            </div>
                            <div class="col-4 textValue">
                                <input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]">
                            </div>
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                    <div id="allFilters">
                    </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-light btn-sm ps-4 pe-4 border-primary text-primary" id="filterReset">
                    <?php esc_html_e("Clear", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </button>
                <button type="button" class="btn btn-soft-primary btn-sm ps-4 pe-4" id="filterSubmit">
                    <?php esc_html_e("Apply", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </button>
            </div>
            </form>
        </div>
    </div>
    <input type="hidden" id="feed_id" value="<?php echo esc_attr(sanitize_text_field(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT))); ?>">
    <input type="hidden" id="strProData" value="<?php echo esc_attr(sanitize_text_field($attr)); ?>">
    <input type="hidden" id="strConditionData" value="<?php echo esc_attr(sanitize_text_field($condition)); ?>">
    <input type="hidden" id="strValueData" value="<?php echo esc_attr(sanitize_text_field($value)); ?>">
    <input type="hidden" id="excludeProductFromSync" value="<?php echo esc_attr(sanitize_text_field($result[0]['exclude_product'])); ?>">
    <input type="hidden" id="includeProductFromSync" value="<?php echo esc_attr(sanitize_text_field($result[0]['include_product'])); ?>">
    <input type="hidden" id="includeExtraProductForFeed" value="">
    <input type="hidden" id="selectAllunchecked" name="selectAllunchecked" value="">
    <input type="hidden" id="totProduct" name="totProduct" value="">
    <input type="hidden" name="syncProductCount" id="syncProductCount" value="<?php echo esc_attr($google_detail_api->data->product_count) ?>">

</div>
<!-- Modal -->
<div class="modal fade" id="convCreateFeedModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ">
            <form id="feedForm" onfocus="this.className='focused'">
                <div id="loadingbar_blue_modal" class="progress-materializecss d-none ps-2 pe-2" style="width:98%">
                    <div class="indeterminate"></div>
                </div>
                <div class="modal-header bg-light p-2 ps-4 fw-500">
                    <h5 class="modal-title fs-16" id="feedType">
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
                            <label for="" class="col-form-label fs-14">
                                <?php esc_html_e("Days", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span>
                                <a target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=productlist&utm_campaign=Pricing"><b>
                                        Upgrade To Pro</b></a>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-5">
                            <label for="target_country_feed" class="col-form-label text-dark fs-14 fw-500" name="">
                                <?php esc_html_e("Target Country", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Specify the target country for your product feed. Select the country where you intend to promote and sell your products.">
                                info
                            </span>
                        </div>
                        <div class="col-7">
                            <select class="select2 form-select form-select-sm mb-3" aria-label="form-select-sm example" style="width: 100%" name="target_country_feed" id="target_country_feed">
                                <option value="">Select Country</option>
                                <?php
                                $selecetdCountry = $conv_data['user_country'];
                                foreach ($contData as $key => $value) {
                                ?>
                                    <option value="<?php echo esc_attr($value->code) ?>" <?php echo  $selecetdCountry == $value->code ? 'selected = "selecetd"' : '' ?>>
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
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox" value="<?php echo esc_attr($microsoft_merchant_center_id); ?>" id="mmc_id" name="mmc_id" <?php echo $microsoft_merchant_center_id !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("microsoft Merchant Center Account :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400">
                                <?php echo esc_html(sanitize_text_field($microsoft_merchant_center_id));  ?>
                            </label>
                        </div>
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox" value="<?php echo esc_attr($google_merchant_center_id); ?>" id="gmc_id" name="gmc_id" <?php echo $google_merchant_center_id !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("Google Merchant Center Account :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400">
                                <?php echo esc_html(sanitize_text_field($google_merchant_center_id));  ?>
                            </label>
                        </div>
                        <div class="form-check form-check-custom d-none">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox" value="" id="tiktok_id" name="tiktok_id" disabled>
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
                                <?php echo isset($ee_options['facebook_setting']['fb_catalog_id']) ? esc_html(sanitize_text_field($ee_options['facebook_setting']['fb_catalog_id'])) : ''; ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-2">
                    <input type="hidden" id="channel_ids" name="channel_ids" value="<?php echo esc_attr(sanitize_text_field($result[0]['channel_ids'])); ?>">
                    <input type="hidden" id="autoSyncInterval" name="autoSyncInterval" value="<?php echo esc_attr(sanitize_text_field($result[0]['auto_sync_interval'])); ?>">
                    <input type="hidden" id="edit" name="edit">
                    <input type="hidden" id="loginCustomerId" name="loginCustomerId" value="<?php echo esc_attr($googleDetail->customer_id); ?>">
                    <input type="hidden" value="<?php echo esc_attr($conv_data['user_domain']); ?>" class="fromfiled" name="url" id="url" placeholder="Enter Website">
                    <input type="hidden" id="is_mapping_update" name="is_mapping_update" value="">
                    <input type="hidden" id="tiktok_catalog_id" name="tiktok_catalog_id" value="<?php echo esc_attr(sanitize_text_field($result[0]['tiktok_catalog_id'])); ?>">
                    <input type="hidden" id="last_sync_date" name="last_sync_date" value="">
                    <input type="hidden" value="<?php echo esc_attr($conv_data['user_domain']); ?>" class="fromfiled" name="url" id="url">
                    <input type="hidden" id="subscriptionMerchantCenId" name="subscriptionMerchantCenId" value="<?php echo property_exists($googleDetail, "google_merchant_center_id") ? esc_attr($googleDetail->google_merchant_center_id) : ""; ?>">
                    <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal" onclick="jQuery('#feedForm')[0].reset()">
                        <?php esc_html_e("Cancel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                    <button type="button" class="btn btn-soft-primary btn-sm" id="submitFeed">
                        <?php esc_html_e("Update", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="categoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light p-2 ps-4 ">
                <div id="loadingbar_blue_modal" class="progress-materializecss d-none ps-2 pe-2" style="width:98%">
                    <div class="indeterminate"></div>
                </div>
                <label class="modal-title fs-14 fw-400" id="">
                    <?php esc_html_e("Map your product category and attributes", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="wrapper p-20 pt-0" style="overflow:auto">
                <form id="productSync" action="" class="d-flex justify-content-center flex-column" style="min-height:50vh">
                    <p class="top-desc pt-2" style="display:none">
                        <?php esc_html_e("Map the categories and other attributed of your WooCommerce products with Conversios categories and attributes. At Conversios we automatically maps your product categories and other attributes to the categories and attributes of the selected channels respectively.", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </p>
                    <h2 class="top-title text-center">Please select your choice:</h2>
                    <span class="Outofstock fs-12 asterisk"> </span><span class="catCount fs-12 fw-500"></span>

                    <div class="tab productButtons">
                        <!-- <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;border-radius:4px;">
                            <div class="col-6 pt-2">
                                <span class="ps-2 fw-normal">
                                    <button class="btn btn-soft-primary me-1 ms-1" type="button" 
                                    onclick="nextPrev(1)" style="width:130px; height:38px;">Do it yourself </button>
                                </span>
                            </div>
                            <div class="col-6 pt-2 ps-0">
                                <span class="ps-1 fw-normal">
                                    <button class="btn btn-soft-primary me-1 ms-1" type="button" id="AuttomaticSync"
                                    onclick="AuttomaticSync()" style="width:130px; height:38px;">Allow us to complete </button>    
                                </span>
                            </div>
                        </div> -->
                    </div>

                    <div class="tab productCategory">
                        <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;border-radius:4px;">
                            <div class="col-6 pt-2">
                                <span class="ps-2 fw-normal">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/woocommerce_logo.png'),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => true,
                                            ),
                                        )
                                    ); ?>
                                    <?php esc_html_e("WooCommerce Product Category", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </div>
                            <div class="col-6 pt-2 ps-0">
                                <span class="ps-1 fw-normal">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/conversios_logo.png'),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => true,
                                            ),
                                        )
                                    ); ?>
                                    <?php esc_html_e("Conversios Product Category", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </div>
                        </div>
                        <div class="col-12 row bg-white m-0 p-0">
                            <div class="col-12 row categoryDiv" style="overflow-y: scroll; max-height:450px;">
                                <?php $category_html = $category_wrapper_obj->category_table_content(0, 0, 'mapping');
                                echo wp_kses(
                                    $category_html,
                                    array(
                                        "div" => array(
                                            'class' => array(),
                                            'style' => array(),
                                            'id' => array(),
                                            'title' => array(),
                                        ),
                                        "button" => array(
                                            'type' => array(),
                                            'class' => array(),
                                            'style' => array(),
                                            'id' => array(),
                                            'title' => array(),
                                        ),
                                        "select" => array(
                                            'name' => array(),
                                            'class' => array(),
                                            'id' => array(),
                                            'style' => array('display'),
                                            'catid' => array(),
                                            'onchange' => array(),
                                            'iscategory' => array(),
                                            'tabindex' => array(),
                                        ),
                                        "option" => array(
                                            'value' => array(),
                                            'selected' => array(),
                                        ),
                                        "span" => array(
                                            'class' => array(),
                                            'style' => array(),
                                            'id' => array(),
                                            'title' => array(),
                                            'data-bs-toggle' => array(),
                                            'data-bs-placement' => array(),
                                            'data-cat-id' => array(),
                                            'data-id' => array(),
                                        ),
                                        "input" => array(
                                            'type' => array(),
                                            'name' => array(),
                                            'class' => array(),
                                            'id' => array(),
                                            'placeholder' => array(),
                                            'style' => array(),
                                            'value' => array(),
                                        ),
                                        "label" => array(
                                            'class' => array(),
                                            'id' => array(),
                                            'style' => array(),
                                        ),
                                        "small" => array(),
                                    )
                                );

                                ?>
                            </div>
                        </div>
                        <div class="col-12 row p-2">
                            <p>
                                <?php esc_html_e("We are using WooCommerce’s action schedulers to make the product sync process go smoothly. Please confirm with your hosting provider to ensure CRON is activated/running and confirm that CRON is enabled on your server.", "enhanced-e-commerce-for-woocommerce-store") ?>
                            </p>
                        </div>
                    </div>

                    <div class="tab productAttribute">
                        <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;border-radius:4px;">
                            <div class="col-6 pt-2">
                                <span class="ps-2 fw-normal">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/conversios_logo.png'),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => true,
                                            ),
                                        )
                                    ); ?>
                                    <?php esc_html_e("Conversios Product Attribute", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </div>
                            <div class="col-6 pt-2 ps-0">
                                <span class="ps-1 fw-normal">
                                    <?php echo wp_kses(
                                        enhancad_get_plugin_image('/admin/images/logos/woocommerce_logo.png'),
                                        array(
                                            'img' => array(
                                                'src' => true,
                                                'alt' => true,
                                                'class' => true,
                                                'style' => true,
                                            ),
                                        )
                                    ); ?>
                                    <?php esc_html_e("WooCommerce Product Attribute", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </div>
                        </div>
                        <div class="col-12 row bg-white m-0 p-0 mb-3">
                            <div class="col-12 row  attributeDiv" style="overflow-y: scroll; max-height:450px;">
                                <?php foreach ($gmcAttributes as $key => $attribute) {
                                    if (isset($tempAddAttr[$attribute["field"]])) {
                                        unset($tempAddAttr[$attribute["field"]]);
                                    }
                                    $sel_val = ""; ?>
                                    <div class="col-6 mt-2">
                                        <span class="ps-3 font-weight-400 text-color fs-12">
                                            <?php echo esc_attr($attribute["field"]) . " " . (isset($attribute["required"]) && esc_attr($attribute["required"]) === '1' ? '<span class="text-color fs-6"> *</span>' : ""); ?>
                                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo (isset($attribute['desc']) ? esc_attr($attribute['desc']) : ''); ?>">
                                                info
                                            </span>
                                        </span>
                                        <div class="float-end">
                                            <?php
                                            if ($attribute["field"] == 'id') { ?>
                                                <input type="text" class="form-control" name="product_id_prefix" id="product_id_prefix" placeholder="Add Prefix" value="" <?php echo $disabled_attr == true ? 'readonly=    "readonly"' : '' ?>>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <?php
                                        $ee_select_option = $TVC_Admin_Helper->add_additional_option_in_tvc_select($wooCommerceAttributes, $attribute["field"]);
                                        $require = (isset($attribute['required']) && $attribute['required']) ? true : false;
                                        $sel_val_def = (isset($attribute['wAttribute'])) ? $attribute['wAttribute'] : "";
                                        if ($attribute["field"] === 'link') {
                                            "product link";
                                        } else if ($attribute["field"] === 'shipping') {
                                            $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? $ee_mapped_attrs[$attribute["field"]] : $sel_val_def;
                                            $TVC_Admin_Helper->tvc_text($attribute["field"], 'number', '', esc_html__('Add shipping flat rate', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require);
                                        } else if ($attribute["field"] === 'tax') {
                                            $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? esc_attr($ee_mapped_attrs[$attribute["field"]]) : esc_attr($sel_val_def);
                                            $TVC_Admin_Helper->tvc_text($attribute["field"], 'number', '', 'Add TAX flat (%)', $sel_val, $require);
                                        } else if ($attribute["field"] === 'content_language') {
                                            $TVC_Admin_Helper->tvc_language_select($attribute["field"], 'content_language', esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), 'en', $require);
                                        } else if ($attribute["field"] === 'target_country') {
                                            $TVC_Admin_Helper->tvc_countries_select($attribute["field"], 'target_country', esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $require);
                                        } else {
                                            if (isset($attribute['fixed_options']) && $attribute['fixed_options'] !== "") {
                                                $ee_select_option_t = explode(",", $attribute['fixed_options']);
                                                $ee_select_option = [];
                                                foreach ($ee_select_option_t as $o_val) {
                                                    $ee_select_option[]['field'] = esc_attr($o_val);
                                                }
                                                $sel_val = $sel_val_def;
                                                $TVC_Admin_Helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require, $ee_select_option);
                                            } else {
                                                $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? $ee_mapped_attrs[$attribute["field"]] : $sel_val_def;
                                                $TVC_Admin_Helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require, $ee_select_option);
                                            }
                                        }
                                        ?>
                                    </div>

                                <?php } ?>
                                <div class="col-12 m-0 p-0 additinal_attr_main_div">
                                    <?php
                                    $cnt = 0;
                                    if (!empty($tempAddAttr)) {
                                        $additionalAttribute = array(
                                            'condition',
                                            'shipping_weight',
                                            'product_weight',
                                            'gender',
                                            'sizes',
                                            'color',
                                            'age_group',
                                            'additional_image_links',
                                            'sale_price_effective_date',
                                            'material',
                                            'pattern',
                                            'product_types',
                                            'availability_date',
                                            'expiration_date',
                                            'adult',
                                            'ads_redirect',
                                            'shipping_length',
                                            'shipping_width',
                                            'shipping_height',
                                            'custom_label_0',
                                            'custom_label_1',
                                            'custom_label_2',
                                            'custom_label_3',
                                            'custom_label_4',
                                            'mobile_link',
                                            'energy_efficiency_class',
                                            'is_bundle',
                                            'loyalty_points',
                                            'unit_pricing_measure',
                                            'unit_pricing_base_measure',
                                            'promotion_ids',
                                            'shipping_label',
                                            'excluded_destinations',
                                            'included_destinations',
                                            'tax_category',
                                            'multipack',
                                            'installment',
                                            'min_handling_time',
                                            'max_handling_time',
                                            'min_energy_efficiency_class',
                                            'max_energy_efficiency_class',
                                            'identifier_exists',
                                            'cost_of_goods_sold'
                                        );
                                        $count_arr = count($additionalAttribute);
                                        foreach ($tempAddAttr as $key => $value) {
                                            $options = '<option>Please Select Attribute</option>';
                                            foreach ($additionalAttribute as $val) {
                                                $selected = "";
                                                $disabled = "";
                                                if ($val == $key) {
                                                    $selected = "selected";
                                                } else {
                                                    if (array_key_exists($val, $tempAddAttr)) {
                                                        $disabled = "disabled";
                                                    }
                                                }

                                                $options .= '<option value="' . $val . '" ' . $selected . ' ' . $disabled . '>' . $val . '</option>';
                                            }
                                            $option1 = '<option>Please Select Attribute</option>';
                                            $fixed_att_select_list = ["gender", "age_group", "condition", "adult", "is_bundle", "identifier_exists"];
                                            if (in_array($key, $fixed_att_select_list)) {
                                                if ($key == 'gender') {
                                                    $gender = ['male' => 'Male', 'female' => 'Female', 'unisex' => 'Unisex'];
                                                    foreach ($gender as $genKey => $genVal) {
                                                        $selected = "";
                                                        if ($genKey == $value) {
                                                            $selected = "selected";
                                                        }
                                                        $option1 .= '<option value="' . $genKey . '" ' . $selected . '>' . $genVal . '</option>';
                                                    }
                                                }
                                                if ($key == 'condition') {
                                                    $conArr = ['new' => 'New', 'refurbished' => 'Refurbished', 'used' => 'Used'];
                                                    foreach ($conArr as $conKey => $conVal) {
                                                        $selected = "";
                                                        if ($conKey == $value) {
                                                            $selected = "selected";
                                                        }
                                                        $option1 .= '<option value="' . $conKey . '" ' . $selected . '>' . $conVal . '</option>';
                                                    }
                                                }
                                                if ($key == 'age_group') {
                                                    $ageArr = ['newborn' => 'Newborn', 'infant' => 'Infant', 'toddler' => 'Toddler', 'kids' => 'Kids', 'adult' => 'Adult'];
                                                    foreach ($ageArr as $ageKey => $ageVal) {
                                                        $selected = "";
                                                        if ($ageKey == $value) {
                                                            $selected = "selected";
                                                        }
                                                        $option1 .= '<option value="' . $ageKey . '" ' . $selected . '>' . $ageVal . '</option>';
                                                    }
                                                }
                                                if ($key == 'adult' || $key == 'is_bundle' || $key == 'identifier_exists') {
                                                    $boolArr = ['yes' => 'Yes', 'no' => 'No'];
                                                    foreach ($boolArr as $boolKey => $boolVal) {
                                                        $selected = "";
                                                        if ($boolKey == $value) {
                                                            $selected = "selected";
                                                        }
                                                        $option1 .= '<option value="' . $boolKey . '" ' . $selected . '>' . $boolVal . '</option>';
                                                    }
                                                }
                                            } else {
                                                foreach ($wooCommerceAttributes as $valattr) {
                                                    $selected = "";
                                                    if ($valattr['field'] == $value) {
                                                        $selected = "selected";
                                                    }
                                                    $option1 .= '<option value="' . $valattr['field'] . '" ' . $selected . '>' . $valattr['field'] . '</option>';
                                                }
                                            }
                                    ?>
                                            <div class="row additinal_attr_div m-0 p-0">
                                                <div class="col-6 mt-2">
                                                    <select style="width:100%" id="<?php echo esc_attr($cnt++) ?>" name="additional_attr_[]" class="additinal_attr fw-light text-secondary fs-6 form-control form-select-sm select2 select2-hidden-accessible">
                                                        <?php
                                                        echo wp_kses($options, array(
                                                            "option" => array(
                                                                'value' => array(),
                                                                'selected' => array(),
                                                            ),
                                                        ));
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-5 mt-2">
                                                    <select style="width:100%" id="" name="additional_attr_value_[]" class="additional_attr_value fw-light text-secondary fs-6 form-control form-select-sm select2 select2-hidden-accessible">
                                                        <?php
                                                        echo wp_kses($option1, array(
                                                            "option" => array(
                                                                'value' => array(),
                                                                'selected' => array(),
                                                            ),
                                                        ));
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-1 mt-2">
                                                    <span class="material-symbols-outlined text-danger remove_additional_attr fs-5 mt-2" title="Add Additional Attribute" style="cursor: pointer; margin-right:35px;">
                                                        delete
                                                    </span>
                                                </div>
                                            </div>
                                    <?php }
                                    } ?>
                                </div>
                                <div class="row add_additional_attr_div m-0 p-0">
                                    <div class="add_additional_attr_div mt-2" style="display: flex; justify-content: start">
                                        <button type="button" class="fs-12 btn btn-soft-primary add_additional_attr <?php echo isset($count_arr) && $count_arr == $cnt ? 'd-none' : '' ?>" title="Add Attribute"> Add Attributes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 p-2">
                            <div style="float:right;">
                                <label>Product Batch Size <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="If you are facing an issue with the product feed process with the current batch change the size of the batch according to your count of products.">
                                        info
                                    </span></label>
                                <select id="product_batch_size" style="border-radius:15px;">
                                    <option value="25" <?php echo $product_sync_batch_size == 25 ? 'selected' : '' ?>>25
                                    </option>
                                    <option value="50" <?php echo $product_sync_batch_size == 50 ? 'selected' : '' ?>>50
                                    </option>
                                    <option value="100" <?php echo $product_sync_batch_size == 100 ? 'selected' : '' ?>>
                                        100</option>
                                    <option value="200" <?php echo $product_sync_batch_size == 200 ? 'selected' : '' ?>>
                                        200</option>
                                    <option value="500" <?php echo $product_sync_batch_size == 500 ? 'selected' : '' ?>>
                                        500</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 row p-2">
                            <p>
                                <?php esc_html_e("We are using WooCommerce’s action schedulers to make the product sync process go smoothly. Please confirm with your hosting provider to ensure CRON is activated/running and confirm that CRON is enabled on your server.", "enhanced-e-commerce-for-woocommerce-store") ?>
                            </p>
                        </div>
                    </div>
                    <div style="overflow:auto;" class="pt-2">
                        <div style="" class="allBtn">
                            <button class="btn btn-soft-primary me-1 ms-1" type="button" id="prevBtn" onclick="nextPrev(-1)" style="min-width:130px; height:38px;">Previous</button>
                            <button class="btn btn-soft-secondary m-1" type="button" id="nextBtn" onclick="nextPrev(1)" style="height:38px;">Next</button>
                            <button class="btn btn-soft-primary btn-success m-1" type="button" id="syncBtn" onclick="snyNowAutomatic()" style="display:none;height:38px;">Allow us to complete this
                                automatically</button>
                        </div>
                    </div>

                    <input type="hidden" name="selectedCategory" id="selectedCategory">
                </form>
                <!-- Circles which indicates the steps of the form: -->
                <div style="text-align:center;margin-top:10px;">
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Error Save Modal -->
<div class="modal fade" id="conv_save_error_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:9999">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                <h3 class="fw-normal pt-3 errorText">Error</h3>
                <span id="conv_save_error_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1 errorFooter m-auto">

            </div>
        </div>
    </div>
</div>
<!-- Error Save Modal End -->
<!-- Success Save Modal 1 -->
<div class="modal fade" id="conv_save_success_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-600">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center px-5">
                <span class="material-symbols-outlined conv-success-check-big">
                    check_circle
                </span>
                <span id="conv_save_success_txt" class="mb-1 d-flex text-dark justify-content-center fs-16 px-2"></span>
            </div>
            <div class="modal-footer px-4 pb-4 mb-1 modalFooterSuccess">
                <button class="btn btn-secondary fs-20 fw-normal text-white dismissModal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Success Save Modal End -->
<script>
    var cat_json = <?php echo wp_json_encode($str) ?>;
    var currentTab = 0; // Current tab is set to be the first tab (0)
    var totalProduct = 0;
    let selected_cat_id = Array();
    jQuery(document).ready(function() {
        let p_id = "<?php echo esc_html($p_id) ?>"
        jQuery('.select2').select2({
            dropdownParent: jQuery("#filterModal")
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        /*************************** DataTable init Start *********************************************************************************************/
        var table = jQuery('#product_list_table').DataTable({
            "ordering": false,
            scrollX: false,
            scrolly: true,
            processing: true,
            serverSide: true,
            searching: false,
            columnDefs: [{
                    className: "align-middle text-start",
                    targets: 0
                },
                {
                    className: "align-middle text-start ps-1 pb-1",
                    targets: 1
                },
                {
                    className: "align-middle text-start",
                    targets: 2
                },
                {
                    className: "align-middle text-start",
                    targets: 3
                },
                {
                    className: "align-middle",
                    targets: 4
                },
                {
                    className: "align-middle",
                    targets: 5
                },
                {
                    className: "align-middle",
                    targets: 6
                }
            ],
            initComplete: function() {
                jQuery('#searchName').on('input', function() {
                    jQuery('#product_list_table').DataTable().search(jQuery(this).val()).draw();
                });
            },
            "language": {
                processing: false,
            },
            ajax: {
                url: tvc_ajax_url,
                type: 'POST',
                data: function(d) {
                    conv_change_loadingbar('show');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    return jQuery.extend({}, d, {
                        action: "ee_get_product_details_for_table",
                        productData: jQuery("#strProData").val(),
                        conditionData: jQuery("#strConditionData").val(),
                        valueData: jQuery("#strValueData").val(),
                        searchName: jQuery("#searchName").val(),
                        feed_id: jQuery('#feed_id').val(),
                        p_id: p_id,
                        prefix: "<?php echo esc_html($result[0]['product_id_prefix']) ?>",
                        product_details_nonce: "<?php echo esc_js(wp_create_nonce('conv_product_details-nonce')); ?>"
                    });
                },
                dataType: 'JSON',
                error: function(err, status) {

                },
            },
            "drawCallback": function(settings) {
                if (jQuery('#selectAllunchecked').val() == 1) {
                    jQuery(".checkbox").prop('checked', false);
                } else {
                    jQuery(".checkbox").prop('checked', true);
                }
                var total = addCommas(settings.json.recordsTotal);
                totalProduct = settings.json.recordsTotal;
                getrealcheckedcount();
                if (jQuery("#strProData").val() == '') {
                    jQuery('.filter_count').empty();
                } else {
                    jQuery('.filter_count').empty();
                    jQuery('.filter_count').append(
                        '<label class="fs-7 text-dark float-end mt-3 defaultPointer">Filter Applied :<lable class="fs-7  text-secondary"> Found ' +
                        total + ' Products</lable></label>');
                }
            },
            columns: [{
                    data: 'checkbox'
                },
                {
                    data: 'product'
                },
                {
                    data: 'category'
                },
                {
                    data: 'availability'
                },
                {
                    data: 'quantity'
                },
                {
                    data: 'channelstatus'
                },
                {
                    data: 'action'
                }
            ],

        }).on('draw', function() {
            conv_change_loadingbar('hide');
            let exclude = [];
            let include = [];
            var availableId = Array();
            var product_list = '';
            var prefix = "<?php echo esc_html($result[0]['product_id_prefix']) ?>";

            if (jQuery('#excludeProductFromSync').val() != '') {
                exclude = jQuery('#excludeProductFromSync').val().split(',');
                jQuery('#syncAll').prop('checked', false)
                jQuery.each(exclude, function(key, value) {
                    jQuery('#sync_' + value).prop('checked', false);
                    jQuery('#attr_' + value).prop('checked', false);
                });
            }
            if (jQuery('#includeProductFromSync').val() != '') {
                include = jQuery('#includeProductFromSync').val().split(',');
                product_list = jQuery('#includeProductFromSync').val();
                jQuery('#syncAll').prop('checked', false);
                jQuery('input[name="syncProduct"]').prop('checked', false);
                jQuery('input[name="attrProduct"]').prop('checked', false);
                jQuery('#selectAllunchecked').val(1);
                jQuery.each(include, function(key, value) {
                    jQuery('#sync_' + value).prop('checked', true);
                    jQuery('#attr_' + value).prop('checked', true);
                });
            }

            var status = "<?php echo esc_html($result[0]['status']); ?>";
            var tiktok_status = "<?php echo esc_html($result[0]['tiktok_status']) ?>";
            var fb_status = "<?php echo esc_html($result[0]['fb_status']) ?>";
            var ms_status = "<?php echo esc_html($result[0]['ms_status']) ?>";

            var is_mapping_update = "<?php echo esc_html($result[0]['is_mapping_update']); ?>"
            if (status === 'Draft' && tiktok_status == 'Draft' && fb_status == 'Draft' && ms_status == 'Draft' &&
                is_mapping_update != '1') {
                jQuery('.action_').remove();
                jQuery("input:checkbox[name=attrProduct]").each(function() {
                    jQuery('.channelStatus_' + jQuery(this).val()).empty();
                    jQuery('.channelStatus_' + jQuery(this).val()).html('Not yet sync');
                    jQuery('#channel_action_' + jQuery(this).val()).empty();
                    jQuery('#channel_action_' + jQuery(this).val()).append(
                        '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                        jQuery(this).val() + '" value="' + jQuery(this).val() +
                        '">cached</span><input type="hidden" class="filteredProductSyn_' +
                        jQuery(this).val() + '" value="' + jQuery(this).val() + '">'
                    )
                });
            } else if (status == 'Draft' && tiktok_status == 'Draft' && fb_status == 'Draft' && ms_status == 'Draft' &&
                is_mapping_update == '1') {
                jQuery('.action_').remove();
                jQuery("input:checkbox[name=attrProduct]").each(function() {
                    jQuery('#channel_action_' + jQuery(this).val()).empty();
                    jQuery('#channel_action_' + jQuery(this).val()).append(
                        '<span class="material-symbols-outlined  no-drop" value="' + jQuery(
                            this).val() + '" title="Product Sync In Progress">cached</span>'
                    );

                    if (jQuery(this).is(':checked')) {
                        jQuery('.pending_count_' + jQuery(this).val()).text(1);
                    }

                    var channel_ids = <?php echo wp_json_encode($channel_id) ?>;
                    if (channel_ids.indexOf('1') != -1) {
                        jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                            "<h2 class='card-title fs-6'><img src='<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                        );
                    }

                    if (channel_ids.indexOf('2') != -1) {
                        jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                            "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') ?>' /> Facebook Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                        );
                    }

                    if (channel_ids.indexOf('3') != -1) {
                        jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                            "<h2 class='card-title fs-6'><img src='<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' /> TikTok Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                        );
                    }

                    if (channel_ids.indexOf('4') != -1) {
                        jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                            "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg') ?>' /> Microsoft Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                        );
                    }


                });

                jQuery('.allPendingCount').text(addCommas(
                    <?php echo $result[0]['total_product'] !== "" ? esc_html($result[0]['total_product']) : 0 ?>
                ));

            } else if (status == 'In Progress' && tiktok_status == 'In Progress' && fb_status == 'In Progress' && ms_status == 'In Progress' && is_mapping_update == '1') {
                jQuery('.action_').remove();
                jQuery("input:checkbox[name=attrProduct]").each(function() {
                    jQuery('#channel_action_' + jQuery(this).val()).empty();
                    jQuery('#channel_action_' + jQuery(this).val()).append(
                        '<span class="material-symbols-outlined  no-drop" value="' + jQuery(
                            this).val() + '" title="Product Sync In Progress">cached</span>'
                    )

                    if (jQuery(this).is(':checked')) {
                        jQuery('.pending_count_' + jQuery(this).val()).text(1);
                    }

                    var channel_ids = <?php echo wp_json_encode($channel_id) ?>;
                    if (channel_ids.indexOf('1') != -1) {
                        jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                            "<h2 class='card-title fs-6'><img src='<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                        );
                    }

                    if (channel_ids.indexOf('2') != -1) {
                        jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                            "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') ?>' /> Facebook Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                        );
                    }

                    if (channel_ids.indexOf('3') != -1) {
                        jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                            "<h2 class='card-title fs-6'><img src='<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' /> TikTok Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                        );
                    }

                    if (channel_ids.indexOf('4') != -1) {
                        jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                            "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg') ?>' /> Microsoft Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                        );
                    }


                });

                jQuery('.allPendingCount').text(addCommas(
                    <?php echo $result[0]['total_product'] !== "" ? esc_html($result[0]['total_product']) : 0 ?>
                ));
            } else {
                /*********Fetch real time status from API***********/
                var all_check_list = Array(); // all checked box value
                jQuery("input:checkbox[name=attrProduct]:checked").each(function() {
                    all_check_list.push(jQuery(this).val());
                });
                var uncheck = Array(); // get all unchecked check box
                jQuery("input:checkbox[name=attrProduct]:not(:checked)").each(function() {
                    uncheck.push(jQuery(this).val());
                    /*******Append filter sync button for unchecked checkbox *********/
                    jQuery('.channelStatus_' + jQuery(this).val()).empty();
                    jQuery('.channelStatus_' + jQuery(this).val()).html('Not yet sync');
                    jQuery('#action_' + jQuery(this).val().replace(prefix, '')).remove();
                    jQuery('#channel_action_' + jQuery(this).val()).empty();
                    jQuery('#channel_action_' + jQuery(this).val()).append(
                        '<span class="material-symbols-outlined filteredProductSyn" id="filteredProductSyn_' +
                        jQuery(this).val() + '" value="' + jQuery(this).val().replace(prefix,
                            '') +
                        '">cached</span><input type="hidden" class="filteredProductSyn_' +
                        jQuery(this).val() + '" value="' + jQuery(this).val().replace(prefix,
                            '') + '">');
                });
                var product_data = {
                    action: "ee_get_product_status",
                    product_list: all_check_list.join(','),
                    feed_id: jQuery('#feed_id').val(),
                    maxResults: jQuery('select[name=product_list_table_length]').val(),
                    channel_id: jQuery('#selectStatus').find(":selected").val(),
                    catalog_id: "<?php echo esc_html($facebook_catalog_id) ?>",
                    tiktok_business_id: "<?php echo esc_html($tiktok_business_account) ?>",
                    tiktok_catalog_id: "<?php echo esc_html($result[0]['tiktok_catalog_id']) ?>",
                    ms_store_id: "<?php echo esc_js($microsoft_merchant_center_id) ?>",
                    ms_catalog_id: "<?php echo esc_js($microsoft_catalog_id) ?>",
                    conv_licence_nonce: "<?php echo esc_js(wp_create_nonce('conv_licence-nonce')); ?>"
                };
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: product_data,
                    success: function(response) {
                        console.log(response); //wow
                        /*********Remove All other spinner***********/
                        jQuery('.status_').remove();
                        jQuery('.issue_').remove();
                        jQuery('.action_').remove();
                        if (response != "Product does not exists" && response !=
                            "Product not synced") {
                            var AllApproved = 0;
                            var AllDisapproved = 0;
                            var AllPending = 0;
                            jQuery.each(response, function(key, value) {
                                var countApproved = 0;
                                var countDisapproved = 0;
                                if (value.productId == '' || value.productId == null) {
                                    return true;
                                }

                                var prodID = value.productId.split(':');
                                // // prodID[3] = 66; // woow manully change
                                all_check_list = jQuery.grep(all_check_list, function(
                                    values) {
                                    return values != prodID[3];
                                });

                                if (value.googleStatus) {
                                    if (value.googleStatus && value.googleStatus !==
                                        'disapproved') {
                                        countApproved++;
                                        AllApproved++;
                                        jQuery('.channel_logo_' + prodID[3]).append(
                                            "<img src='<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' />"
                                        );
                                    } else {
                                        countDisapproved++;
                                        AllDisapproved++;
                                        var uniqueGoogleIssues = value.googleIssues
                                            .filter(function(itm, i, a) {
                                                return i == a.indexOf(itm);
                                            });
                                        var gIssue = '<ul class="b">';
                                        jQuery.each(uniqueGoogleIssues, function(key,
                                            issue) {
                                            gIssue += '<li>' + issue + '</li>';
                                        });
                                        gIssue += '</ul>'
                                        jQuery('.rejected_issue_text_' + prodID[3])
                                            .append(
                                                "<h2 class='card-title fs-6'><img src='<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'>" +
                                                gIssue + "</span>");
                                    }
                                }

                                if (value.facebookStatus && value.facebookStatus !==
                                    'disapproved') {
                                    countApproved++;
                                    AllApproved++;
                                    jQuery('.channel_logo_' + prodID[3]).append(
                                        "<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') ?>' />"
                                    );
                                } else if (value.facebookStatus) {
                                    countDisapproved++;
                                    AllDisapproved++;
                                    jQuery('.rejected_issue_text_' + prodID[3]).append(
                                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') ?>' /> Facebook Catalog</h2><span class='text-dark fs-7'>" +
                                        value.facebookIssues + "</span>");
                                }

                                if (value.tiktokStatus && value.tiktokStatus !==
                                    'disapproved') {
                                    countApproved++;
                                    AllApproved++;
                                    jQuery('.channel_logo_' + prodID[3]).append(
                                        "<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' />"
                                    );
                                } else if (value.tiktokStatus) {
                                    countDisapproved++;
                                    AllDisapproved++;
                                    jQuery('.rejected_issue_text_' + prodID[3]).append(
                                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' /> Tiktok Catalog</h2><span class='text-dark fs-7'>" +
                                        value.tiktokStatus + "</span>");
                                }

                                if (value.microsoftStatus && (value.microsoftStatus !== 'Disapproved' && value.microsoftStatus !== 'disapproved')) {
                                    countApproved++;
                                    AllApproved++;
                                    jQuery('.channel_logo_' + prodID[3]).append(
                                        "<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg') ?>' />"
                                    );
                                } else if (value.microsoftStatus) {
                                    countDisapproved++;
                                    AllDisapproved++;
                                    jQuery('.rejected_issue_text_' + prodID[3]).append(
                                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg') ?>' /> Microsoft Catalog</h2><span class='text-dark fs-7'>" +
                                        value.microsoftIssues + "</span>");
                                }


                                jQuery('.approved_count_' + prodID[3]).text(
                                    countApproved);
                                jQuery('.rejected_count_' + prodID[3]).text(
                                    countDisapproved)
                                jQuery('#channel_action_' + prodID[3]).empty();
                                jQuery('#channel_action_' + prodID[3]).append(
                                    '<span class="material-symbols-outlined pointer" id="action_' +
                                    prodID[3] +
                                    '" value="Delete" onclick="deleteProduct(' +
                                    jQuery('#feed_id').val() + ', \'' + prodID[3] +
                                    '\')">delete</span>');
                                jQuery('#channel_action_' + prodID[3]).next('.action_')
                                    .remove();

                                console.log('wow2');
                                console.log(value);
                                console.log(value.microsoftStatus + '=pid=' + prodID[3]); //wow2
                            });

                            jQuery('.allApprovedCount').text(addCommas(AllApproved));
                            jQuery('.allRejectedCount').text(addCommas(AllDisapproved));
                            jQuery.each(all_check_list, function(remain_key, remain_value) {
                                jQuery('.channelStatus_' + remain_value).empty();
                                jQuery('.channelStatus_' + remain_value).html(
                                    'Not yet sync.');
                                jQuery('#channel_action_' + remain_value).empty();
                                jQuery('#channel_action_' + remain_value).append(
                                    '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                                    remain_value + '" value="' + remain_value
                                    .replace(prefix, '') +
                                    '">cached</span><input type="hidden" class="filteredProductSyn_' +
                                    remain_value + '" value="' + remain_value
                                    .replace(prefix, '') + '">');
                            });
                            jQuery.each(uncheck, function(ch_key, ch_value) {
                                jQuery('.channelStatus_' + ch_value).empty();
                                jQuery('.channelStatus_' + ch_value).html(
                                    'Not yet sync..');
                                jQuery('#channel_action_' + ch_value).empty();
                                jQuery('#channel_action_' + ch_value).append(
                                    '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                                    ch_value + '" value="' + ch_value.replace(
                                        prefix, '') +
                                    '">cached</span><input type="hidden" class="filteredProductSyn_' +
                                    ch_value + '" value="' + ch_value.replace(
                                        prefix, '') + '">');
                            });
                        } else {
                            jQuery.each(all_check_list, function(key, value) {
                                jQuery('.channelStatus_' + value).empty();
                                jQuery('.channelStatus_' + value).html('Not yet sync .');
                                jQuery('#channel_action_' + value).empty();
                                jQuery('#channel_action_' + value).append(
                                    '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                                    value + '" value="' + value +
                                    '">cached</span><input type="hidden" class="filteredProductSyn_' +
                                    value + '" value="' + value + '">');
                            });
                            jQuery.each(uncheck, function(ch_key, ch_value) {
                                jQuery('.channelStatus_' + ch_value).empty();
                                jQuery('.channelStatus_' + ch_value).html(
                                    'Not yet sync ..');
                                jQuery('#channel_action_' + ch_value).empty();
                                jQuery('#channel_action_' + ch_value).append(
                                    '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                                    ch_value + '" value="' + ch_value.replace(
                                        prefix, '') +
                                    '">cached</span><input type="hidden" class="filteredProductSyn_' +
                                    ch_value + '" value="' + ch_value.replace(
                                        prefix, '') + '">');
                            });
                        }
                    }
                });
            }

            return true;
        });
        /*************************** DataTable init End *********************************************************************************************/
        /**************** Reset Modal filter Start ***************************************/
        jQuery(document).on('click', '#filterReset', function(event) {
            jQuery('#allFilters').empty();
            jQuery("#filterForm")[0].reset();
            jQuery(".product").select2('val', '0');
            jQuery('.select2').select2({
                dropdownParent: jQuery("#allFilters")
            });
        });
        /**************** Reset Modal filter End ******************************************/
        /*********************Add Filter Show Start***********************************************************************/
        jQuery('.addFilter').on('click', function(events) {
            let attr = jQuery('#strProData').val();
            let condition = jQuery('#strConditionData').val();
            let value = jQuery('#strValueData').val();
            var a = 0;
            if (attr != '' && condition != '' && value != '') {
                let attrArry = attr.split(",");
                let conditionArry = condition.split(",");
                let valueArry = value.split(",");
                jQuery('#allFilters').empty()
                jQuery.each(attrArry, function(i, value) {
                    if (a == 0) {
                        a = 1;
                        jQuery('select[name="product[]"]').val(value).trigger('change');
                        jQuery('select[name="condition[]"]').val(conditionArry[i]).trigger(
                            'change');
                        if (value === 'product_cat' || value === '_stock_status') {
                            jQuery('select[name="value[]"]').val(valueArry[i]).trigger('change');
                        } else {
                            jQuery('input[name="value[]"]').val(valueArry[i]);
                        }

                    } else {
                        var conditionDropDown = getConditionDropDown(value, conditionArry[i]);
                        if (value === 'product_cat') {
                            var category = <?php echo wp_json_encode($category); ?>;
                            let option = '<option value="0">Select Category</option>';
                            jQuery.each(category, function(key, values) {
                                option += '<option value="' + key + '" ' + ((key ==
                                        valueArry[i]) ? "selected" : "") + '>' + values +
                                    '</option>';
                            });
                            var html = '<select class="select2" name="value[]">' +
                                option +
                                '</select>';
                        } else if (value === '_stock_status') {
                            let option = '<option value="0">Select Stock Status</option>' +
                                '<option value="instock" ' + ((key == valueArry[i]) ? "selected" :
                                    "") + '>In Stock</option>' +
                                '<option value="outofstock" ' + ((key == valueArry[i]) ?
                                    "selected" : "") + '>Out Of Stock</option>';
                            var html = '<select class="select2" name="value[]">' +
                                option +
                                '</select>';
                        } else {
                            var html =
                                '<input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]" value="' +
                                valueArry[i] + '">';
                        }
                        var newRow = '<div class="filterRow mb-3 row">' +
                            '<div class="col-11 row">' +
                            '<div class="col-4 productDiv">' +
                            '<select class="select2 product" name="product[]">' +
                            '<option value="0">Select Attribute</option>' +
                            '<option value="product_cat" ' + ((value == "product_cat") ?
                                "selected" : "") + '>Category</option>' +
                            '<option value="ID" ' + ((value == "ID") ? "selected" : "") +
                            '>Product Id</option>' +
                            '<option value="post_title" ' + ((value == "post_title") ? "selected" :
                                "") + '>Product Title</option>' +
                            '<option value="_stock_status" ' + ((value == "_stock_status") ?
                                "selected" : "") + '>Stock Status</option>' +
                            '</select>' +
                            '</div>' +
                            '<div class="col-4 conditionDiv">' +
                            conditionDropDown +
                            '</div>' +
                            '<div class="col-4 textValue">' +
                            html +
                            '</div>' +
                            '</div>' +
                            '<div class="col-1 pt-2">' +
                            '<span class="material-symbols-outlined deleteButton text-primary" style="cursor: pointer;" title="Remove Filter">remove</span>' +
                            '</div>' +
                            '</div>';
                        jQuery('#allFilters').append(newRow);
                    }
                });
            }
            jQuery('#filterModal').modal('show');
            jQuery('.select2').select2({
                dropdownParent: jQuery("#filterModal")
            });
        });
        /*********************Add Filter Show End************************************************************************/
        /**************** Add more filter Start**************************************************************************/
        jQuery(document).on("click", ".addButton", function(event) {
            var newRow = '<div class="filterRow mb-3 row">' +
                '<div class="col-11 row">' +
                '<div class="col-4 productDiv">' +
                '<select class="select2 product" name="product[]">' +
                '<option value="0">Select Attribute</option>' +
                '<option value="product_cat">Category</option>' +
                '<option value="ID">Product Id</option>' +
                '<option value="_stock_status">Stock Status</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-4 conditionDiv">' +
                '<select class="select2 condition" name="condition[]">' +
                '<option value="0">Select Conditions</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-4 textValue">' +
                '<input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]" >' +
                '</div>' +
                '</div>' +
                '<div class="col-1 pt-2">' +
                '<span class="material-symbols-outlined deleteButton text-primary" style="cursor: pointer;" title="Remove Filter">remove</span>' +
                '</div>' +
                '</div>';

            jQuery('#allFilters').append(newRow);
            jQuery('.select2').select2({
                dropdownParent: jQuery("#filterModal")
            });
        });
        /**************** Add more filter End******************************************************************************/
        /**************** get dependent dropdown product change start******************************************************/
        jQuery(document).on('change', '.product', function(event) {
            var changeValue = jQuery(this).val();
            jQuery(this).parent().parent().children('div').eq(1).empty();
            var conditionDropDown = getConditionDropDown(changeValue);
            jQuery(this).parent().parent().children('div').eq(1).append(conditionDropDown);
            if (changeValue === 'product_cat') {
                var category = <?php echo wp_json_encode($category); ?>;
                let option = '<option value="0">Select Category</option>';
                jQuery.each(category, function(key, value) {
                    option += '<option value="' + key + '">' + value + '</option>';
                });
                jQuery(this).parent().parent().children('.textValue').empty();
                var html = '<select class="select2" name="value[]">' +
                    option +
                    '</select>';
                jQuery(this).parent().parent().children('.textValue').append(html);
            } else if (changeValue === '_stock_status') {
                jQuery(this).parent().parent().children('.textValue').empty();
                var html = '<select class="select2" name="value[]">' +
                    '<option value="0">Select Stock Status</option>' +
                    '<option value="instock">In Stock</option>' +
                    '<option value="outofstock">Out Of Stock</option>' +
                    '</select>';
                jQuery(this).parent().parent().children('.textValue').append(html);
            } else {
                jQuery(this).parent().parent().children('.textValue').empty();
                var html =
                    '<input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]" >';
                jQuery(this).parent().parent().children('.textValue').append(html);
            }
            jQuery('.select2').select2({
                dropdownParent: jQuery("#allFilters")
            });
        });
        /**************** get dependent dropdown product change end**********************************************************/
        /**************** Delete Add more filed column start*****************************************************************/
        jQuery("body").on("click", ".deleteButton", function() {
            jQuery(this).parents(".filterRow").remove();
        });
        /**************** Delete Add more filed column  end******************************************************************/
        /****************Feed Name error dismissed start************************/
        jQuery(document).on('input', '#feedName', function(e) {
            e.preventDefault();
            jQuery('#feedName').css('margin-left', '0px');
            jQuery('#feedName').css('margin-right', '0px');
            jQuery('#feedName').removeClass('errorInput');
        });
        jQuery(document).on('click', '#gmc_id', function(e) {
            jQuery('.errorChannel').css('color', '');
        });
        jQuery(document).on('change', '#target_country_feed', function(e) {
            var tiktok_business_account = "<?php echo esc_html($tiktok_business_account) ?>";
            let target_country = jQuery('#target_country_feed').find(":selected").val();
            jQuery('#tiktok_id').empty();
            jQuery('.tiktok_catalog_id').empty();
            jQuery('#tiktok_catalog_id').val('');
            if (target_country !== "" && tiktok_business_account !== "" && jQuery('input#tiktok_id').is(
                    ':checked')) {
                getCatalogId(target_country);
            }
            jQuery('.select2-selection').css('border', '1px solid #c6c6c6');
            jQuery('.select2-selection').css('border', '1px solid #c6c6c6');
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

            let target_country = jQuery('#target_country_feed').find(":selected").val();
            if (target_country === "") {
                jQuery('.select2-selection').css('border', '1px solid #ef1717');
                return false;
            }

            if (!jQuery('#gmc_id').is(":checked") && !jQuery('#tiktok_id').is(":checked") && !jQuery('#fb_id').is(':checked') && !jQuery('#mmc_id').is(':checked')) {
                jQuery('.errorChannel').css('border', '1px solid red');
                return false;
            }
            jQuery('#submitFeed').addClass("disabledsection");
            save_feed_data();
        });

        /****************Submit Feed call end***********************************/
        /**************** Get filtered data Start ******************************************/
        jQuery(document).on('click', '#filterSubmit', function(event) {
            let product = jQuery("select[name='product[]'] option:selected").map(function() {
                return jQuery(this).val();
            }).get();
            let producttext = jQuery("select[name='product[]'] option:selected").map(function() {
                return jQuery(this).text();
            }).get();
            let condition = jQuery("select[name='condition[]'] option:selected").map(function() {
                return jQuery(this).val();
            }).get();
            let value = jQuery("input[name='value[]']").map(function() {
                return jQuery(this).val();
            }).get();
            let seltext = jQuery("select[name='value[]'] option:selected").map(function() {
                return jQuery(this).text();
            }).get();
            let selVal = jQuery("select[name='value[]'] option:selected").map(function() {
                return jQuery(this).val() ? jQuery(this).val() : '';
            }).get();
            let flag = 0;
            let valFlag = 0;
            let prodData = Array();
            let conditionData = Array();
            let valueData = Array();
            jQuery('#addFiltersCard').empty();
            jQuery.each(product, function(i, val) {
                if (val != "0" && condition[i] != "0" && (value[valFlag] != "" || selVal[flag] !=
                        "")) {
                    if (val === 'product_cat' || val === '_stock_status') {
                        prodData[i] = val;
                        conditionData[i] = condition[i];
                        valueData[i] = selVal[flag];
                        var newCard =
                            '<div class="btn-group border rounded mt-1 me-1 removecardThis" >' +
                            '<button class="btn btn-light btn-sm text-secondary fs-7 ps-1 pe-1 pt-0 pb-0" type="button" value="' +
                            i + '">' + producttext[i] + ' <b>' + condition[i] + '</b> ' + seltext[
                                flag++] + '</button>' +
                            '<button type="button" class="btn btn-sm btn-light onhover-close pt-0 pb-0" data-bs-toggle="" aria-expanded="false" style="cursor: pointer;">' +
                            '<span class="material-symbols-outlined fs-6 pt-1 onhover-close removecard">close</span></button></div>';
                    } else {
                        prodData[i] = val;
                        conditionData[i] = condition[i];
                        valueData[i] = value[valFlag];
                        var newCard =
                            '<div class="btn-group border rounded mt-1 me-1 removecardThis">' +
                            '<button class="btn btn-light btn-sm text-secondary fs-7 ps-1 pe-1 pt-0 pb-0" type="button" value="' +
                            i + '">' + producttext[i] + ' <b>' + condition[i] + '</b> ' + value[
                                valFlag++] + '</button>' +
                            '<button type="button" class="btn btn-sm btn-light onhover-close pt-0 pb-0" data-bs-toggle="" aria-expanded="false" style="cursor: pointer;">' +
                            '<span class="material-symbols-outlined fs-6 pt-1 onhover-close removecard">close</span></button></div>';
                    }
                    jQuery('#addFiltersCard').append(newCard);
                }
            });
            jQuery('#strProData').val('');
            jQuery('#strConditionData').val('');
            jQuery('#strValueData').val('');
            let strProData = prodData.join(',');
            let strConditionData = conditionData.join(',');
            let strValueData = valueData.join(',');
            jQuery('#strProData').val(jQuery('#strProData').val() ? jQuery('#strProData').val() + "," +
                strProData : strProData);
            jQuery('#strConditionData').val(jQuery('#strConditionData').val() ? jQuery('#strConditionData')
                .val() + "," + strConditionData : strConditionData);
            jQuery('#strValueData').val(jQuery('#strValueData').val() ? jQuery('#strValueData').val() +
                "," + strValueData : strValueData);
            jQuery('#excludeProductFromSync').val('');
            jQuery('#includeProductFromSync').val('');
            jQuery('#selectAllunchecked').val('');
            jQuery('#includeExtraProductForFeed').val('');
            jQuery('#allFilters').empty();
            jQuery("#filterForm")[0].reset();
            jQuery(".product").select2('val', '0');
            jQuery('#filterDelete').addClass('disabled');
            jQuery('#filterModal').modal('hide');
            table.draw();
        });
        /************************************* Get filtered data End **********************************************************************************/
        /***************************** Remove Cards Startm ******************************************************************************************/
        jQuery(document).on('click', '.removecard', function(event) {
            var ele = jQuery(this).parent();
            var strProData = jQuery('#strProData').val().split(',');
            var strConditionData = jQuery('#strConditionData').val().split(',');
            var strValueData = jQuery('#strValueData').val().split(',');
            var val = ele.prev().val();
            jQuery(ele.parent()).remove();

            strProData.splice(val, 1);
            strConditionData.splice(val, 1);
            strValueData.splice(val, 1);

            jQuery(".removecard").each(function(index, value) {
                jQuery(this).parent().prev().val(index);
            });
            strProData = strProData.join();
            strConditionData = strConditionData.join();
            strValueData = strValueData.join();

            jQuery('#strProData').val(strProData);
            jQuery('#strConditionData').val(strConditionData);
            jQuery('#strValueData').val(strValueData);
            jQuery('#excludeProductFromSync').val('');
            jQuery('#includeProductFromSync').val('');
            jQuery('#selectAllunchecked').val('');
            jQuery('#includeExtraProductForFeed').val('');
            table.draw();
        });
        /****************************** Remove Cards End *********************************************************************************************/
        /****************************** Select All CheckBox Start ***********************************************************************************/
        jQuery(document).on('click', '#syncAll', function(e) {
            jQuery(".checkbox").prop('checked', jQuery(this).prop('checked'));
            if (jQuery(this).prop("checked")) {
                jQuery('#excludeProductFromSync').val('')
                jQuery('#selectAllunchecked').val('');
                jQuery('#includeProductFromSync').val('');
            } else {
                jQuery('#selectAllunchecked').val(1);
            }
        });

        jQuery(document).on('change', '.checkbox', function(e) {
            let exclude = [];
            let include = [];
            if (jQuery('#excludeProductFromSync').val() != '') {
                exclude = jQuery('#excludeProductFromSync').val().split(',');
            }
            if (jQuery('#includeProductFromSync').val() != '') {
                include = jQuery('#includeProductFromSync').val().split(',');
            }

            if (jQuery('#selectAllunchecked').val() == 1) {
                if (jQuery(this).prop("checked")) {
                    include.push(jQuery(this).val());
                    let uniqueInclude = include.filter((item, i, ar) => ar.indexOf(item) === i);
                    let val = uniqueInclude.join(',');
                    jQuery('#includeProductFromSync').val(val);
                } else {
                    const newArr = include.filter(e => e !== jQuery(this).val());
                    jQuery('#includeProductFromSync').val(newArr.join(','));
                }
            } else {
                if (!jQuery(this).prop("checked")) {
                    jQuery("#syncAll").prop("checked", false);
                    exclude.push(jQuery(this).val());
                    let unique = exclude.filter((item, i, ar) => ar.indexOf(item) === i);
                    let val = unique.join(',');
                    jQuery('#excludeProductFromSync').val(val);
                } else {
                    const newArr = exclude.filter(e => e !== jQuery(this).val());
                    jQuery('#excludeProductFromSync').val(newArr.join(','));
                }
            }
            getrealcheckedcount();
        });
        /****************************** Select All CheckBox End ************************************************************************************/
        /****************************** Product Wise Category Start ************************************************************************************/
        jQuery(document).on('click', '.filteredProductSyn', function(e) {
            var data = {
                action: "get_category_for_filter",
                type: "total_synced_product_count",
                get_category_for_filter: "<?php echo esc_js(wp_create_nonce('get_category_for_filter-nonce')); ?>"
            };
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {},
                success: function(total_product_feed) {

                    jQuery('#totProduct').val(total_product_feed);
                    console.log('your current limit=' + total_product_feed);
                    console.log('you max limit=100');

                    if (total_product_feed > 100) {
                        console.log(total_product_feed);
                        jQuery('#conv_save_error_txt').html('')
                        jQuery('.errorText').text('Notice');

                        var upgradetopro = '<a class="btn btn-soft-primary pointer upgrade__btn" target="_blank" href="https://www.conversios.io/pricing?utm_source=aiopro&utm_medium=pfmlimitpopup&utm_campaign=upgrade&plugin_name=aiofree">Upgrade to Professional</a>';
                        var closebtn = '<button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>';
                        jQuery('.errorFooter').html(upgradetopro + closebtn);

                        jQuery('#conv_save_error_txt').html('According to your plan, the maximum limit for syncing products is 100');

                        jQuery('#conv_save_error_modal').modal('show')
                        return false;
                    }

                    var thisVal = jQuery('.' + e.target.id).val();
                    let productArray = Array();
                    let exclude = Array();
                    let include = Array();
                    let attr_ids =
                        <?php echo isset($attr_id) && $attr_id == 1 ? 1 : wp_json_encode(json_decode($attr_id)); ?>;
                    let prefix = "<?php echo esc_html($result[0]['product_id_prefix']) ?>";
                    if (prefix != '') {
                        jQuery('#product_id_prefix').val(prefix);
                    }
                    if (attr_ids != 1) {
                        jQuery.each(attr_ids, function(attr_key, attr_value) {
                            jQuery('select[name^="' + attr_key + '"] option[value="' + attr_value + '"]')
                                .attr("selected", "selected");
                        });
                    }
                    var prodId = '';

                    if (thisVal != 'syncAll') {
                        productArray.push(jQuery('.syncProduct_' + thisVal).val());
                        if (attr_ids != 1) {
                            exclude = jQuery('#excludeProductFromSync').val().split(',');
                            include = jQuery('#includeProductFromSync').val();
                            if (exclude != '') {
                                prodId = thisVal;
                                var arrProd = [];
                                exclude = jQuery.grep(exclude, function(values) {
                                    return values != prodId;
                                });
                                var excludeProd = exclude.join(',');
                                jQuery('#excludeProductFromSync').val(excludeProd);
                                jQuery('#includeExtraProductForFeed').val(prodId);
                            } else if (include != '') {
                                prodId = thisVal;
                                jQuery('#includeProductFromSync').val(include + ',' + prodId);
                                jQuery('#includeExtraProductForFeed').val(prodId);
                            }

                        } else {
                            jQuery(".checkbox").prop('checked', false);
                            jQuery('.syncProduct_' + thisVal).prop('checked', true);
                            jQuery("#filteredProductSyn").text('Sync 1 Product');
                            jQuery('#totProduct').val(1);
                            jQuery('#includeProductFromSync').val(thisVal);
                            jQuery('#excludeProductFromSync').val('');
                        }

                    } else if (jQuery('#excludeProductFromSync').val() != '' && jQuery('#selectAllunchecked')
                        .val() == '') {
                        exclude = jQuery('#excludeProductFromSync').val().split(',');
                        jQuery('#includeProductFromSync').val('');
                    } else if (jQuery('#includeProductFromSync').val() != '' && jQuery('#selectAllunchecked')
                        .val() != '') {
                        include = jQuery('#includeProductFromSync').val().split(',');
                        jQuery('#excludeProductFromSync').val('');
                    }

                    if (!jQuery.isEmptyObject(productArray) || !jQuery.isEmptyObject(exclude) || (thisVal ==
                            'syncAll' && jQuery('.syncProduct').is(':checked')) || !jQuery.isEmptyObject(include)) {
                        let totProduct = parseInt(jQuery('#totProduct').val());
                        let syncProductCount = parseInt(jQuery('#syncProductCount').val());
                        if (totProduct > 100 || (totProduct + syncProductCount) > 100) {
                            jQuery('.errorFooter').empty();
                            jQuery('.errorFooter').html(
                                '<a class="btn conv-blue-bg m-auto text-white" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=productlist&utm_campaign=Pricing" target="_blank" >Upgrade to pro</a>'
                            );
                            jQuery('.errorText').html('Oops!');
                            jQuery('#conv_save_error_txt').html(
                                "<p>You've reached the maximum product limit of 100 products for your current plan.</p>" +
                                "<p>With our Pro version, you'll benefit from unlimited products, advanced analytics, priority support, and much more. Upgrade now to unlock the full potential of our platform and take your business to the next level. " +
                                "<a href='https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=productlist&utm_campaign=Pricing' target='_blank'>Upgrade to pro.</a></p>"
                            );
                            jQuery('#conv_save_error_modal').modal('show');
                            return false;
                        }

                        var data = {
                            action: "ee_syncProductCategory",
                            productData: jQuery("#strProData").val(),
                            conditionData: jQuery("#strConditionData").val(),
                            valueData: jQuery("#strValueData").val(),
                            searchName: jQuery("#searchName").val(),
                            productArray: productArray,
                            exclude: exclude,
                            include: include,
                            inculdeExtraProduct: prodId,
                            conv_syncprodcat_nonce: "<?php echo esc_js(wp_create_nonce('conv_syncprodcat')); ?>"
                        };
                        jQuery.ajax({
                            url: tvc_ajax_url,
                            type: "POST",
                            data: data,
                            dataType: "json",
                            beforeSend: function() {
                                conv_change_loadingbar('show');
                            },
                            error: function(err, status) {
                                conv_change_loadingbar('hide');
                            },
                            success: function(response) {
                                conv_change_loadingbar('hide');
                                if (response.error === true) {
                                    // console.log(response.message)
                                    return false;
                                }
                                selected_cat_id = response;
                                jQuery('#selectedCategory').val('');
                                jQuery('.catTermId').hide();
                                let totCatCount = 0;
                                jQuery('.catTermId').each(function(index) {
                                    totCatCount++;
                                })
                                let selectedCategory = [];
                                let catCount = 0;
                                jQuery.each(response, function(key, value) {
                                    var last_element = value;
                                    jQuery('#selectedCategory').val() == '' ? jQuery(
                                        '#selectedCategory').val(jQuery.trim(
                                        last_element)) : jQuery('#selectedCategory').val(
                                        jQuery(
                                            '#selectedCategory').val() + ',' + jQuery.trim(
                                            last_element))
                                    jQuery('.termId_' + jQuery.trim(last_element)).show();
                                });
                                catCount = jQuery('.catTermId').filter(function(index) {
                                    return jQuery(this).css('display') !== 'none';
                                }).length;
                                // jQuery('.catCount').html(catCount+' Out of '+totCatCount+' categories found. Only mapped categories will be synced in Google Merchant Center.');
                                jQuery('.productAttribute').css('display', 'none')
                                var target_country =
                                    "<?php echo esc_html($result[0]['target_country']) ?>";
                                if (target_country !== "") {
                                    jQuery('#target_country').val(
                                        "<?php echo esc_html($result[0]['target_country']) ?>");
                                    jQuery('#target_country').attr('disabled', true);
                                }
                                var attr = <?php echo wp_json_encode($result[0]['attributes']) ?>;
                                if (attr) {
                                    jQuery('#id').attr('disabled', true);
                                }

                                // var is_tvc_cat_selected = false;
                                // selected_cat_id.forEach(function(v,i){	
                                //     if(is_tvc_cat_selected == false && jQuery("#category-"+jQuery.trim(v)).val() != "" && jQuery("#category-"+jQuery.trim(v)).val() != 0){
                                //         is_tvc_cat_selected =true;
                                //     }          
                                // });
                                // if(is_tvc_cat_selected == 1 || is_tvc_cat_selected == true){
                                // }else{
                                // jQuery('#nextBtn').addClass('disabled')
                                // }

                                currentTab = 0; // Current tab is set to be the first tab (0)
                                showTab(currentTab); // Display the current tab
                                jQuery("#categoryModal").modal("show");
                                jQuery('.catCount').show();
                                jQuery('.asterisk').show();
                                setTimeout(function() {
                                    jQuery('.select2').select2({
                                        dropdownParent: jQuery("#categoryModal")
                                    });
                                }, 100);
                            }
                        });
                    } else {
                        conv_change_loadingbar('hide');
                        jQuery('.errorFooter').empty();
                        jQuery('.errorFooter').html(
                            '<button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>'
                        )
                        jQuery(".errorText").html('Error: No Product Selected');
                        jQuery("#conv_save_error_txt").html(
                            "We're sorry, but it seems you haven't selected any products. <br/>In order to proceed, please select at least one product from the available options."
                        );
                        jQuery("#conv_save_error_modal").modal("show");
                    }
                }
            });

        });

        /****************************** Product Wise Category End ************************************************************************************/
        /****************************** Mapping value is Numeric Start ************************************************************************************/
        jQuery(document).on('keydown', 'input[name="shipping"]', function(event) {
            if (event.shiftKey == true) {
                event.preventDefault();
            }
            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <=
                    105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event
                .keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }

            if (jQuery(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();
        })
        jQuery(document).on('keydown', 'input[name="tax"]', function() {
            if (event.shiftKey == true) {
                event.preventDefault();
            }
            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <=
                    105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event
                .keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }

            if (jQuery(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();
        })
        /****************************** Mapping value is Numeric End ************************************************************************************/
    });

    /*********************Card Popover Start***********************************************************************/
    jQuery(document).on('mouseover', '.approvedChannel', function() {
        jQuery(this).popover('dispose');
        if (jQuery(this).find('span').html() > 0) {
            var content = jQuery(this).next().html();
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        }
    })

    jQuery(document).on('mouseover', '.pendingIssues', function() {
        jQuery(this).popover('dispose');
        if (jQuery(this).find('span').html() > 0) {
            var content = jQuery(this).next().html();
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        }
    })

    jQuery(document).on('mouseover', '.rejectIssues', function() {
        jQuery(this).popover('dispose');
        if (jQuery(this).find('span').html() > 0) {
            var content = jQuery(this).next().html();
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        }
    })
    /*********************Card Popover  End**************************************************************************/
    /*************************************Get Condition Dropdown Start******************************************************************/
    function getConditionDropDown(val = '', condition = '') {
        let conditionOption =
            '<select class="select2 condition" name="condition[]"><option value="0">Select Condition</option>';
        if (val != '0') {
            if (val != '' || condition != '') {
                switch (val) {
                    case 'product_cat':
                    case 'ID':
                    case '_stock_status':
                        conditionOption += '<option value="=" ' + ((condition == "=") ? "selected" : "") +
                            ' > = </option>' +
                            '<option value="!=" ' + ((condition == "!=") ? "selected" : "") + ' > != </option>';
                        break;
                }
            }
        }
        conditionOption += '</select>';
        return conditionOption;
    }
    /*************************************Get Condition Dropdown End**********************************************************************/
    /*************************************Save Feed Data Start*************************************************************************/
    function save_feed_data(google_merchant_center_id, catalog_id) {
        console.log('saving from 2481 line'); // woow 2481
        var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
        let edit = jQuery('#edit').val();
        var data = {
            action: "save_feed_data",
            feedName: jQuery('#feedName').val(),
            google_merchant_center: jQuery('input#gmc_id').is(':checked') ? '1' : '',
            fb_catalog_id: jQuery('input#fb_id').is(':checked') ? '2' : '',
            autoSync: jQuery('input#autoSync').is(':checked') ? '1' : '0',
            autoSyncIntvl: 25,
            edit: edit,
            last_sync_date: jQuery('#last_sync_date').val(),
            is_mapping_update: jQuery('#is_mapping_update').val(),
            target_country: jQuery('#target_country_feed').find(":selected").val(),
            customer_subscription_id: "<?php echo esc_html($subscriptionId) ?>",
            tiktok_business_account: "<?php echo esc_html($tiktok_business_account) ?>",
            tiktok_id: jQuery('input#tiktok_id').is(':checked') ? '3' : '',
            tiktok_catalog_id: jQuery('input#tiktok_id').is(':checked') ? jQuery('input#tiktok_id').val() : '',
            microsoft_merchant_center: jQuery('input#mmc_id').is(':checked') ? '4' : '',
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
            success: function(response) {
                conv_change_loadingbar_modal('hide');
                jQuery('#convCreateFeedModal').modal('hide');
                if (response.id) {
                    jQuery("#conv_save_success_txt").html("Feed saved successfully");
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function() {
                        if (edit != '') {
                            location.reload(true);
                        } else {
                            window.location.replace(
                                "<?php echo esc_url_raw($site_url . 'product_list&id='); ?>" + response
                                .id);
                        }

                    }, 100);
                } else if (response.errorType === 'tiktok') {
                    jQuery('.tiktok_catalog_id').empty();
                    jQuery('.tiktok_catalog_id').html(response.message);
                    jQuery('.tiktok_catalog_id').addClass('text-danger');

                } else {
                    jQuery('.errorFooter').empty();
                    jQuery('.errorFooter').html(
                        '<button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>'
                    )
                    jQuery(".errorText").html('Error: Data Saving Failed');
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                }

            }
        });

    }
    /*************************************Save Feed Data End***************************************************************************/
    /*************************************Edit Feed Data Start*************************************************************************/
    function editFeed($id) {
        jQuery('#gmc_id').attr('disabled', false);
        jQuery('#target_country_feed').attr('disabled', false);
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
            success: function(response) {
                jQuery('#feedName').val(response[0].feed_name);
                jQuery('#last_sync_date').val(response[0].last_sync_date);
                jQuery('#is_mapping_update').val(response[0].is_mapping_update);
                jQuery('#autoSyncIntvl').val(response[0].auto_sync_interval);
                if (response[0].target_country) {
                    jQuery('#target_country_feed').val(response[0].target_country);
                }

                if (response[0].auto_schedule == 1) {
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
                jQuery('#tiktok_catalog_id').val('');
                jQuery('#fb_id').prop("checked", false);
                jQuery('#fb_id').attr('disabled', false);
                var tiktok_business_account = "<?php echo esc_html($tiktok_business_account) ?>";
                var google_merchant_center_id = "<?php echo esc_html($google_merchant_center_id) ?>";
                var microsoft_merchant_center_id = "<?php echo esc_html($microsoft_merchant_center_id) ?>";
                var facebook_business_account = "<?php echo esc_html($facebook_business_account) ?>";
                if (tiktok_business_account == "") {
                    jQuery('#tiktok_id').attr('disabled', true);
                    jQuery('#tiktok_id').attr('checked', false);
                }
                if (google_merchant_center_id == "") {
                    jQuery('#gmc_id').attr('disabled', true);
                    jQuery('#gmc_id').attr('checked', false);
                }
                if (microsoft_merchant_center_id == "") {
                    jQuery('#mmc_id').attr('disabled', true);
                    jQuery('#mmc_id').attr('checked', false);
                }
                if (facebook_business_account == "") {
                    jQuery('#fb_id').attr('disabled', true);
                    jQuery('#fb_id').attr('checked', false);
                }

                channel_id = response[0].channel_ids.split(",");
                jQuery.each(channel_id, function(index, val) {
                    if (val == '1') {
                        jQuery('#gmc_id').prop("checked", true);
                    }
                    if (val === '3') {
                        jQuery('#tiktok_id').prop("checked", true);
                        jQuery('#tiktok_id').val(response[0].tiktok_catalog_id);
                        jQuery('.tiktok_catalog_id').html(response[0].tiktok_catalog_id);
                        jQuery('#tiktok_catalog_id').val(response[0].tiktok_catalog_id)
                    }
                    if (val == '2') {
                        jQuery('#fb_id').prop("checked", true);
                    }
                    if (val == '4') {
                        jQuery('#mmc_id').prop("checked", true);
                    }

                });
                if (response[0].is_mapping_update == '1') {
                    jQuery('#gmc_id').attr('disabled', true);
                    jQuery('#tiktok_id').attr('disabled', true);
                    jQuery('#fb_id').attr('disabled', true);
                    jQuery('#target_country_feed').attr('disabled', true);
                }
                jQuery('#edit').val(response[0].id);
                jQuery('#centered').html();
                jQuery('#submitFeed').text('Update Feed');
                jQuery('#feedType').text('Edit Feed - ' + response[0].feed_name);
                conv_change_loadingbar('hide');
                jQuery('#convCreateFeedModal').modal('show');
                jQuery('#target_country_feed').select2({
                    dropdownParent: jQuery("#convCreateFeedModal")
                });
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });
    }
    /*************************************Edit Feed Data End****************************************************************************/
    /*************************************Process Loader Start*************************************************************************/
    function conv_change_loadingbar(state = 'show') {
        if (state == 'show') {
            jQuery("#loadingbar_blue").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
            jQuery("#wpwrap").css("pointer-events", "none");
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        } else {
            jQuery("#loadingbar_blue").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
            jQuery("#wpwrap").css("pointer-events", "auto");
        }
    }

    function conv_change_loadingbar_modal(state = 'show') {
        if (state == 'show') {
            jQuery("#loadingbar_blue_modal").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
        } else {
            jQuery("#loadingbar_blue_modal").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }
    /*************************************Process Loader End*************************************************************************/
    /*************************************Restrict Zero start*************************************************************************/
    function removeZero() {
        var val = jQuery("#autoSyncIntvl").val();
        if (val == 0) {
            jQuery("#autoSyncIntvl").val('')
        }
    }
    /*************************************Restrict Zero  End*************************************************************************/
    /*************************************Number Format start*************************************************************************/
    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
    /*************************************Number Format End*************************************************************************/
    /*************************************Product Mapping steps Start*************************************************************************/
    function showTab(n) {
        // This function will display the specified tab of the form ...
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        if (n == (x.length - 1)) {
            document.getElementById("nextBtn").innerHTML = "Sync Products";
        } else {
            if (n == 0) {
                document.getElementById("nextBtn").innerHTML = "Do it yourself";
                document.getElementById("syncBtn").style.display = "inline";
                jQuery('.top-title').show();
                jQuery('.top-desc').hide();
                jQuery('.allBtn').addClass('text-center');
            } else {
                document.getElementById("syncBtn").style.display = "none";
                document.getElementById("nextBtn").innerHTML = "Next";
                jQuery('.top-desc').show();
                jQuery('.top-title').hide();
                jQuery('.allBtn').removeClass('text-center');
            }
        }
        fixStepIndicator(n)
    }

    function nextPrev(n) {
        if (n == 1) {
            var is_tvc_cat_selected = false;
            selected_cat_id.forEach(function(v, i) {
                if (is_tvc_cat_selected == false && jQuery("#category-" + jQuery.trim(v)).val() != "" && jQuery(
                        "#category-" + jQuery.trim(v)).val() != 0) {
                    is_tvc_cat_selected = true;
                }
            });
            // if(is_tvc_cat_selected == 1 || is_tvc_cat_selected == true){
            // }else{
            //     jQuery('.errorFooter').empty();
            //     jQuery('.errorFooter').html('<button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>')
            //     jQuery(".errorText").html('Error: Category mapping');
            //     jQuery("#conv_save_error_txt").html('Category mapping required');
            //     jQuery("#conv_save_error_modal").modal("show");
            //     return false;
            // }
            jQuery('.catCount').hide();
            jQuery('.asterisk').hide();
            /****additional Attribute validation start*********/
            if (jQuery('#nextBtn').text() == 'Sync Products') {
                var attrCheck = ['id', 'title', 'description', 'price'];
                var hasError = false;
                jQuery.each(attrCheck, function(index, value) {
                    if (jQuery("#" + value).find(":selected").val() == '') {
                        jQuery('.selection').find("[aria-labelledby='select2-" + value + "-container']").addClass(
                            'selectError');
                        hasError = true;
                    }
                })
                if (hasError === true) {
                    return false
                }
                var attrValidation = false;
                jQuery(".additinal_attr").each(function() {
                    if (this.selectedIndex === 0) {
                        jQuery(this).parent().addClass('errorInput');
                        attrValidation = true;
                        return false;
                    }
                })
                if (attrValidation === true) {
                    return false;
                }
                var attrValueValidation = false;
                jQuery(".additional_attr_value").each(function() {
                    if (this.selectedIndex === 0) {
                        jQuery(this).parent().addClass('errorInput');
                        attrValueValidation = true;
                        return false;
                    }
                })
                if (attrValueValidation === true) {
                    return false;
                }
            }
            /****additional Attribute validation end*********/
        } else {
            jQuery('.catCount').show();
            jQuery('.asterisk').show();
        }
        // This function will figure out which tab to display
        var x = document.getElementsByClassName("tab");
        if (n == 1 && !validateForm()) return false;
        x[currentTab].style.display = "none";
        currentTab = currentTab + n;
        if (currentTab >= x.length) {
            submitProductSyncUp();
            jQuery("#categoryModal").modal("hide");
            return false;
        }
        // Otherwise, display the correct tab:
        showTab(currentTab);
    }

    function snyNowAutomatic() {
        submitProductSyncUp();
        jQuery("#categoryModal").modal("hide");
        return false;
    }

    function validateForm() {
        var x, y, i, valid = true;
        x = document.getElementsByClassName("tab");
        y = x[currentTab].getElementsByTagName("input");
        for (i = 0; i < y.length; i++) {
            if (y[i].value == "") {
                y[i].className += " invalid";
                valid = false;
            }
        }
        if (valid) {
            document.getElementsByClassName("step")[currentTab].className += " finish";
        }
        return valid; // return the valid status
    }

    function fixStepIndicator(n) {
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        x[n].className += " active";
    }
    /*************************************Product Mapping steps End***************************************************************************/
    function selectSubCategory(thisObj) {
        selectId = thisObj.id;
        wooCategoryId = jQuery(thisObj).attr("catid");
        var selvalue = jQuery('#' + selectId).find(":selected").val();
        var seltext = jQuery('#' + selectId).find(":selected").text();
        jQuery("#category-" + wooCategoryId).val(selvalue);
        jQuery("#category-name-" + wooCategoryId).val(seltext);
        setTimeout(function() {
            jQuery('.select2').select2({
                dropdownParent: jQuery("#categoryModal")
            });
        }, 100);

    }
    jQuery(document).on("click", ".change_prodct_feed_cat", function() {
        jQuery(this).hide();
        var feed_select_cat_id = jQuery(this).attr("data-id");
        var woo_cat_id = jQuery(this).attr("data-cat-id");
        jQuery("#category-" + woo_cat_id).val("0");
        jQuery("#category-name-" + woo_cat_id).val("");
        jQuery("#label-" + feed_select_cat_id).hide();
        jQuery("#" + feed_select_cat_id).css('width', '100%');
        jQuery("#" + feed_select_cat_id).addClass('select2');
        jQuery("#" + feed_select_cat_id).slideDown();
        jQuery('.select2').select2({
            dropdownParent: jQuery("#categoryModal")
        });
    });

    function getrealcheckedcount() {
        var excludeProductFromSync = jQuery('#excludeProductFromSync').val();
        var includeProductFromSync = jQuery('#includeProductFromSync').val();
        jQuery('#totProduct').val(0);
        if (excludeProductFromSync !== '' && excludeProductFromSync !== 'syncAll') {
            var count = excludeProductFromSync.split(',').length;
            jQuery('#filteredProductSyn').text('Sync ' + addCommas(totalProduct - count) + ' Products');
            jQuery('#totProduct').val(totalProduct - count);
            return true;
        }
        if (includeProductFromSync !== '') {
            var count = includeProductFromSync.split(',').length;
            jQuery('#filteredProductSyn').text('Sync ' + addCommas(count) + ' Products');
            jQuery('#totProduct').val(count);
            return true;
        }
        if (jQuery("#syncAll").prop("checked")) {
            jQuery('#filteredProductSyn').text('Sync ' + addCommas(totalProduct) + ' Products');
            jQuery('#totProduct').val(totalProduct);
            return true;
        }
        jQuery('#filteredProductSyn').text('Sync 0 Products');
    }
    /*************************************Submit Product Sync Start***************************************************************************/
    function submitProductSyncUp(sync_progressive_data = null) {
        jQuery('#target_country').attr('disabled', false);
        jQuery('#id').attr('disabled', false);
        var data = {
            action: 'ee_feed_wise_product_sync_batch_wise',
            merchant_id: '<?php echo esc_js($merchantId); ?>',
            account_id: '<?php echo esc_js($accountId); ?>',
            customer_id: '<?php echo esc_js($currentCustomerId); ?>',
            subscription_id: '<?php echo esc_js($subscriptionId); ?>',
            conv_data: jQuery("#productSync").find("input[value!=''], select:not(:empty), input[type='number']")
                .serialize(),
            product_batch_size: jQuery("#product_batch_size").val(),
            product_id_prefix: jQuery("#product_id_prefix").val(),
            conv_nonce: "<?php echo esc_js(wp_create_nonce('conv_ajax_product_sync_bantch_wise-nonce')); ?>",
            // Adding filters value to feed table
            feedId: <?php echo esc_js(sanitize_text_field(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT))) ?>,
            productData: jQuery("#strProData").val(),
            conditionData: jQuery("#strConditionData").val(),
            valueData: jQuery("#strValueData").val(),
            searchName: jQuery("#searchName").val(),
            include: jQuery('#includeProductFromSync').val(),
            exclude: jQuery('#excludeProductFromSync').val(),
            autoSyncInterval: jQuery('#autoSyncInterval').val(),
            channel_ids: jQuery('#channel_ids').val(),
            tiktok_catalog_id: jQuery('#tiktok_catalog_id').val(),
            ms_store_id: '<?php echo esc_js($microsoft_merchant_center_id); ?>',
            ms_catalog_id: '<?php echo esc_js($microsoft_catalog_id); ?>',
            inculdeExtraProduct: jQuery('#includeExtraProductForFeed').val()
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
            },
            success: function(response) { //console.log(response); 
                conv_change_loadingbar('hide');
                if (response.status == "success") {
                    var message =
                        "Your products are being synced. Data will reflect in the dashboards within 30 minutes, and you can check product status in the feeds.";
                    jQuery("#conv_save_success_txt").html(message);
                    jQuery("#conv_save_success_modal .leave-a-review").show();
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function() {
                        var url = "<?php echo esc_url_raw($site_url); ?>";
                        var anchorTag = '<a href="' + url +
                            '" class="btn btn-secondary fs-20 fw-normal">Close</a>'
                        jQuery("#conv_save_success_modal button").replaceWith(anchorTag);
                        //window.location.replace("<?php echo esc_url_raw($site_url); ?>");
                    }, 1000);

                } else {
                    conv_change_loadingbar('hide');
                    jQuery('.errorFooter').empty();
                    jQuery('.errorFooter').html(
                        '<button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>'
                    )


                    if (typeof response.message !== "undefined" && response.message.indexOf('maximum') > -1 && response.message.indexOf('limit') > -1) {
                        //Your maximum limit to add product is over.
                        jQuery(".errorText").html('Warning: Data Limit Exceeded');
                        jQuery("#conv_save_error_txt").html(response.message + ' <a href="https://www.conversios.io/checkout/?pid=wpAIO_PY1&utm_source=freeplugin&utm_medium=productsyncmodal&utm_campaign=productsyncbuttonclick" target="_blank"> Upgrade Your Plam</a>');
                    } else {
                        jQuery(".errorText").html('Error: Data Saving Failed');
                        jQuery("#conv_save_error_txt").html(response.message);
                    }
                    jQuery("#conv_save_error_modal").modal("show");
                    setTimeout(function() {
                        //location.reload(true);
                    }, 1000);
                }
            }
        });
    }
    /*************************************Submit Product Sync End***************************************************************************/
    /*************************************DELETE Feed Data Start**********************************************************************/
    function deleteProduct($id, $product_id = null) {
        var message =
            'Product in this feed will be deleted from the channels selected in the feed. Are you sure you want to delete it? ';
        if (confirm(message)) {
            var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
            var data = {
                action: "ee_delete_feed_gmc",
                feed_id: $id,
                product_ids: $product_id,
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
                },
                success: function(response) {
                    conv_change_loadingbar('hide');
                    if (response.error == false) {
                        jQuery("#conv_save_success_txt").html('Deleted Successfully!!');
                        jQuery("#conv_save_success_modal").modal("show");
                    } else {
                        jQuery('.errorFooter').empty();
                        jQuery('.errorFooter').html(
                            '<button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>'
                        )
                        jQuery(".errorText").html('Error: Failed');
                        jQuery("#conv_save_error_txt").html(response.message);
                        jQuery("#conv_save_error_modal").modal("show");
                    }
                    location.reload(true);

                }
            });
        }
    }
    /*************************************Delete Feed Data End*************************************************************************/
    /*************************************Select2 in Modal - scroll issue fix start ***************************************************/
    jQuery(document).on('select2:close', '.select2', function(e) {
        var evt = "scroll.select2"
        jQuery(e.target).parents().off(evt)
        jQuery(window).off(evt)
    })
    /*************************************Select2 in Modal - scroll issue fix End *****************************************************/
    jQuery(document).on('click', '.select2-selection.select2-selection--single', function(e) {
        var iscatMapped = jQuery(this).parent().parent().prev().attr('iscategory')
        var selectId = jQuery(this).parent().parent().prev().attr('id')
        var toAppend = '';
        if (iscatMapped == 'false') {
            jQuery(this).parent().parent().prev().attr('iscategory', 'true')
            jQuery.each(cat_json, function(i, o) {
                toAppend += '<option value="' + o.id + '">' + o.name + '</option>';
            });
            jQuery('#' + selectId).append(toAppend);
            jQuery('#' + selectId).select2({
                dropdownParent: jQuery("#categoryModal")
            });
            jQuery('#' + selectId).select2('open');
        }
    });
    /******************************************************************************************************************* */

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
    // jQuery(document).on('change', '.categorySelect', function() {
    //     jQuery('#nextBtn').removeClass('disabled');
    // })

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
                conv_change_loadingbar_modal('hide');
            },
            success: function(response) {
                jQuery('.tiktok_catalog_id').empty();
                jQuery('#tiktok_catalog_id').val('');
                jQuery('#tiktok_id').empty();
                jQuery('.tiktok_catalog_id').removeClass('text-danger');
                if (response.error == false) {
                    if (response.data.catalog_id !== '') {
                        jQuery('#tiktok_id').val(response.data.catalog_id);
                        jQuery('.tiktok_catalog_id').text(response.data.catalog_id);
                        jQuery('#tiktok_catalog_id').val(response.data.catalog_id)
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
    var selected = Array();
    var cnt = <?php echo esc_html($cnt) ?>;
    jQuery(document).on('click', '.add_additional_attr', function() {
        var additionalAttribute = [{
                "field": "condition"
            }, {
                "field": "shipping_weight"
            }, {
                "field": "product_weight"
            },
            {
                "field": "gender"
            }, {
                "field": "sizes"
            }, {
                "field": "color"
            }, {
                "field": "age_group"
            },
            {
                "field": "additional_image_links"
            }, {
                "field": "sale_price_effective_date"
            },
            {
                "field": "material"
            }, {
                "field": "pattern"
            }, {
                "field": "availability_date"
            }, {
                "field": "expiration_date"
            },
            {
                "field": "product_types"
            }, {
                "field": "ads_redirect"
            }, {
                "field": "adult"
            }, {
                "field": "shipping_length"
            },
            {
                "field": "shipping_width"
            }, {
                "field": "shipping_height"
            }, {
                "field": "custom_label_0"
            }, {
                "field": "custom_label_1"
            },
            {
                "field": "custom_label_2"
            }, {
                "field": "custom_label_3"
            }, {
                "field": "custom_label_4"
            }, {
                "field": "mobile_link"
            },
            {
                "field": "energy_efficiency_class"
            }, {
                "field": "is_bundle"
            }, {
                "field": "promotion_ids"
            }, {
                "field": "loyalty_points"
            },
            {
                "field": "unit_pricing_measure"
            }, {
                "field": "unit_pricing_base_measure"
            }, {
                "field": "shipping_label"
            },
            {
                "field": "excluded_destinations"
            }, {
                "field": "included_destinations"
            }, {
                "field": "tax_category"
            },
            {
                "field": "multipack"
            }, {
                "field": "installment"
            }, {
                "field": "min_handling_time"
            }, {
                "field": "max_handling_time"
            },
            {
                "field": "min_energy_efficiency_class"
            }, {
                "field": "max_energy_efficiency_class"
            }, {
                "field": "identifier_exists"
            },
            {
                "field": "cost_of_goods_sold"
            }
        ];
        var count = Object.keys(additionalAttribute).length;
        var option = '<option value="">Please Select Attribute</option>';
        jQuery.each(additionalAttribute, function(index, value) {
            /*****Check for selected option to disabled start*******/
            var disabled = "";
            if (jQuery.inArray(value.field, selected) !== -1) {
                disabled = "disabled";
            }
            /*****Check for selected option to disabled end*******/
            option += '<option value="' + value.field + '" ' + disabled + '>' + value.field + '</option>'
        });
        var wooCommerceAttributes = <?php echo wp_json_encode($wooCommerceAttributes); ?>;
        var option1 = '<option value="">Please Select Attribute</option>';
        jQuery.each(wooCommerceAttributes, function(index, value) {
            option1 += '<option value="' + value.field + '" >' + value.field + '</option>'
        });

        var html = '';
        html += '<div class="row additinal_attr_div m-0 p-0" ><div class="col-6 mt-2">';
        html += '<select style="width:100%" id="' + cnt++ +
            '" name="additional_attr_[]" class="additinal_attr fw-light text-secondary fs-6 form-control form-select-sm select2 select2-hidden-accessible">';
        html += option;
        html += '</select></div>';
        html += '<div class="col-5 mt-2">';
        html +=
            '<select style="width:100%" id="" name="additional_attr_value_[]" class="additional_attr_value fw-light text-secondary fs-6 form-control form-select-sm select2 select2-hidden-accessible">';
        html += option1;
        html += '</select></div>';
        html += '<div class="col-1 mt-2">';
        html +=
            '<span class="material-symbols-outlined text-danger remove_additional_attr fs-5 mt-2" title="Add Additional Attribute" style="cursor: pointer; margin-right:35px;">';
        html += 'delete';
        html += '</span>';
        html += '</div></div>';
        jQuery('.additinal_attr_main_div').append(html);
        jQuery('.select2').select2({
            dropdownParent: jQuery("#categoryModal")
        });

        var div_count = jQuery('.additinal_attr_div').length;
        if (count == div_count) {
            jQuery('.add_additional_attr').addClass('d-none');
        }
    });
    jQuery(document).on('click', '.remove_additional_attr', function() {
        jQuery('.remove_additional_attr *').addClass('disabled');
        //get deleted selected tag value
        var deleted = jQuery(this).parent().parent('.additinal_attr_div').find('.additinal_attr').find(':selected')
            .val();
        if (deleted != '') {
            //Remove value from array
            selected = jQuery.grep(selected, function(value) {
                return value != deleted;
            });
            //Enable deleted value to other selecet tag
            jQuery(".additinal_attr option").each(function() {
                var thisOption = jQuery(this);
                var valueToCompare = deleted;
                if (thisOption.val() == valueToCompare) {
                    thisOption.removeAttr("disabled");
                }
            });
        }

        jQuery(this).parent().parent('.additinal_attr_div').remove();
        jQuery('.add_additional_attr').removeClass('d-none');
        jQuery('.remove_additional_attr *').removeClass('disabled');
    });
    jQuery(document).on('change', '.additinal_attr', function() {
        selected = [];
        jQuery(this).parent().removeClass('errorInput');
        var sel = jQuery(this).find(":selected").val();
        var id = jQuery(this).attr("id");
        //All empty select add more used, it will add disable attribute to selected value
        jQuery(".additinal_attr:not(#" + id + ") option").each(function() {
            var thisOption = jQuery(this);
            var valueToCompare = sel;
            if (thisOption.val() == valueToCompare) {
                thisOption.attr("disabled", "disabled");
            }
        });
        var attr_choices = jQuery(".additinal_attr option:selected");
        jQuery(attr_choices).each(function(i, v) {
            selected.push(attr_choices.eq(i).val());
        })
        disableOptions();
    })
    jQuery(document).on('change', '.additional_attr_value', function() {
        jQuery(this).parent().removeClass('errorInput');
    });

    jQuery(document).ready(function() {
        var tempArr = <?php echo wp_json_encode($tempAddAttr) ?>;
        var arr = Object.keys(tempArr).map(function(key) {
            return key;
        });
        selected = arr;
    })

    function disableOptions() {
        //remove attr
        jQuery('.additinal_attr *').removeAttr("disabled");
        jQuery(selected).each(function(i, v) {
            jQuery(".additinal_attr option").each(function() {
                var thisOption = jQuery(this);
                var valueToCompare = v;
                if (jQuery(this).parent().find(':selected').val() != v) {
                    if (thisOption.val() == valueToCompare) {
                        thisOption.attr("disabled", "disabled");
                    }
                }
            });
        });
    }
    jQuery(document).on('change', '.additinal_attr', function() {
        var fixed_att_select_list = ["gender", "age_group", "condition", "adult", "is_bundle", "identifier_exists"];
        var attr = jQuery(this).val();
        if (jQuery.inArray(attr, fixed_att_select_list) !== -1) {
            var option1 = '<option value="">Please Select Attribute</option>';
            if (attr == 'gender') {
                option1 +=
                    '<option value="male">Male</option><option value="female">Female</option><option value="unisex">Unisex</option>'
            }
            if (attr == 'condition') {
                option1 +=
                    '<option value="new">New</option><option value="refurbished">Refurbished</option><option value="used">Used</option>'
            }
            if (attr == 'age_group') {
                option1 +=
                    '<option value="newborn">Newborn</option><option value="infant">Infant</option><option value="toddler">Toddler</option><option value="kids">Kids</option><option value="adult">Adult</option>'
            }
            if (attr == 'adult' || attr == 'is_bundle' || attr == 'identifier_exists') {
                option1 += '<option value="yes">Yes</option><option value="no">No</option>'
            }
            jQuery(this).parent().next().find('.additional_attr_value').html(option1)
        } else {
            var wooCommerceAttributes = <?php echo wp_json_encode($wooCommerceAttributes); ?>;
            var option1 = '<option value="">Please Select Attribute</option>';
            jQuery.each(wooCommerceAttributes, function(index, value) {
                option1 += '<option value="' + value.field + '">' + value.field + '</option>'
            });
            jQuery(this).parent().next().find('.additional_attr_value').html(option1)
        }
    })

    jQuery(document).on('change', '#selectStatus', function() {
        jQuery('.channelStatus_').each(function() {
            var className_prdId = (this.className.split(' ')[1]).replace(this.className.split(' ')[0], '');
            var html = '<div>' +
                '<button type="button" class="rounded-pill approved fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 approvedChannel" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">' +
                'Approved <span class="badge bg-light rounded-circle fs-7 approved-text ms-2 margin-badge approved_count_' +
                className_prdId + '" style="top:0px;">0</span>' +
                '</button>' +
                '<div class="hidden approvedDivContent">' +
                '<div class="card custom-width rounded-5">' +
                '<div class="card-header bg-white channel_logo_ channel_logo_' + className_prdId + '">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div>' +
                '<button type="button" class="rounded-pill pending fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 pendingIssues" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">' +
                'Pending&nbsp; <span class="badge bg-light rounded-circle fs-7 pending-text ms-2 margin-badge pending_count_' +
                className_prdId + '" style="top:0px;">0</span>' +
                '</button>' +
                '<div class="hidden pendingDivContent">' +
                '<div class="card rounded-5">' +
                '<div class="card-header bg-warning-soft text-white">Pending Issues</div>' +
                '<div class="card-body pending_issue_text_ pending_issue_text_' + className_prdId + '">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<button type="button" class="rounded-pill rejected fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 rejectIssues" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">' +
                'Rejected <span class="badge bg-light rounded-circle fs-7 rejected-text ms-2 margin-badge rejected_count_' +
                className_prdId + '" style="top:0px;">0</span>' +
                '</button>' +
                '<div class="hidden rejectDivContent">' +
                '<div class="card rounded-5">' +
                '<div class="card-header bg-danger-soft text-white">Rejected Issues - ' + jQuery('.title_' +
                    className_prdId).text() + '</div>' +
                '<div class="card-body card-body-rejected rejected_issue_text_ rejected_issue_text_' +
                className_prdId + '">' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            jQuery('.channelStatus_' + className_prdId).empty();
            jQuery('.channelStatus_' + className_prdId).html(html);
        });

        /****************Check Channel Status before fetching product status start **********************************/
        var status = "<?php echo esc_html($result[0]['status']) ?>";
        var tiktok_status = "<?php echo esc_html($result[0]['tiktok_status']) ?>";
        var fb_status = "<?php echo esc_html($result[0]['fb_status']) ?>";
        var ms_status = "<?php echo esc_html($result[0]['fb_status']) ?>";
        var is_mapping_update = "<?php echo esc_html($result[0]['is_mapping_update']); ?>";
        var prefix = "<?php echo esc_html($result[0]['product_id_prefix']) ?>";
        if (status == 'Draft' && tiktok_status == 'Draft' && fb_status == 'Draft' && ms_status == 'Draft' && is_mapping_update != '1') {
            jQuery('.action_').remove();
            jQuery("input:checkbox[name=attrProduct]").each(function() {
                jQuery('.channelStatus_' + jQuery(this).val()).empty();
                jQuery('.channelStatus_' + jQuery(this).val()).html('Not yet sync');
                jQuery('#channel_action_' + jQuery(this).val()).empty();
                jQuery('#channel_action_' + jQuery(this).val()).append(
                    '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                    jQuery(this).val() + '" value="' + jQuery(this).val() +
                    '">cached</span><input type="hidden" class="filteredProductSyn_' + jQuery(this)
                    .val() + '" value="' + jQuery(this).val() + '">'
                )
            });

        } else if (status == 'Draft' && tiktok_status == 'Draft' && fb_status == 'Draft' && ms_status == 'Draft' && is_mapping_update ==
            '1') {
            jQuery('.action_').remove();
            jQuery("input:checkbox[name=attrProduct]").each(function() {
                jQuery('#channel_action_' + jQuery(this).val()).empty();
                jQuery('#channel_action_' + jQuery(this).val()).append(
                    '<span class="material-symbols-outlined  no-drop" value="' + jQuery(this).val() +
                    '" title="Product Sync In Progress">cached</span>'
                );

                if (jQuery(this).is(':checked')) {
                    jQuery('.pending_count_' + jQuery(this).val()).text(1);
                }

                var channel_ids = <?php echo wp_json_encode($channel_id) ?>;
                if (channel_ids.indexOf('1') != -1) {
                    jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                    );
                }

                if (channel_ids.indexOf('2') != -1) {
                    jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') ?>' /> Facebook Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                    );
                }

                if (channel_ids.indexOf('3') != -1) {
                    jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' /> TikTok Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                    );
                }

                if (channel_ids.indexOf('4') != -1) {
                    jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg') ?>' /> Microsoft Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                    );
                }

            });

            jQuery('.allPendingCount').text(addCommas(
                <?php echo $result[0]['total_product'] !== "" ? esc_html($result[0]['total_product']) : 0 ?>
            ));

        } else if (status == 'In Progress' && tiktok_status == 'In Progress' && fb_status == 'In Progress' && ms_status == 'In Progress' &&
            is_mapping_update == '1') {
            jQuery('.action_').remove();
            jQuery("input:checkbox[name=attrProduct]").each(function() {
                jQuery('#channel_action_' + jQuery(this).val()).empty();
                jQuery('#channel_action_' + jQuery(this).val()).append(
                    '<span class="material-symbols-outlined  no-drop" value="' + jQuery(this).val() +
                    '" title="Product Sync In Progress">cached</span>'
                );

                if (jQuery(this).is(':checked')) {
                    jQuery('.pending_count_' + jQuery(this).val()).text(1);
                }

                var channel_ids = <?php echo wp_json_encode($channel_id) ?>;
                if (channel_ids.indexOf('1') != -1) {
                    jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                    );
                }

                if (channel_ids.indexOf('2') != -1) {
                    jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') ?>' /> Facebook Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                    );
                }

                if (channel_ids.indexOf('3') != -1) {
                    jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' /> TikTok Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                    );
                }

                if (channel_ids.indexOf('4') != -1) {
                    jQuery('.pending_issue_text_' + jQuery(this).val()).append(
                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg') ?>' /> Microsoft Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>"
                    );
                }

            });

            jQuery('.allPendingCount').text(addCommas(
                <?php echo $result[0]['total_product'] !== "" ? esc_html($result[0]['total_product']) : 0 ?>
            ));
        } else {
            /*********Fetch real time status from API***********/
            var all_check_list = Array(); // all checked box value
            jQuery("input:checkbox[name=attrProduct]:checked").each(function() {
                all_check_list.push(jQuery(this).val());
            });
            var uncheck = Array(); // get all unchecked check box
            jQuery("input:checkbox[name=attrProduct]:not(:checked)").each(function() {
                uncheck.push(jQuery(this).val());
                /*******Append filter sync button for unchecked checkbox *********/
                jQuery('.channelStatus_' + jQuery(this).val()).empty();
                jQuery('.channelStatus_' + jQuery(this).val()).html('Not yet sync');
                jQuery('#action_' + jQuery(this).val().replace(prefix, '')).remove();
                jQuery('#channel_action_' + jQuery(this).val()).empty();
                jQuery('#channel_action_' + jQuery(this).val()).append(
                    '<span class="material-symbols-outlined filteredProductSyn" id="filteredProductSyn_' +
                    jQuery(this).val() + '" value="' + jQuery(this).val().replace(prefix, '') +
                    '">cached</span><input type="hidden" class="filteredProductSyn_' + jQuery(this)
                    .val() + '" value="' + jQuery(this).val().replace(prefix, '') + '">');
            });
            /*******************************Fetch Product status if mapping updated and feed status is Synced for all channel *******/
            var product_data = {
                action: "ee_get_product_status",
                product_list: all_check_list.join(','),
                feed_id: jQuery('#feed_id').val(),
                maxResults: jQuery('select[name=product_list_table_length]').val(),
                channel_id: jQuery('#selectStatus').find(":selected").val(),
                catalog_id: "<?php echo esc_js($facebook_catalog_id) ?>",
                tiktok_business_id: "<?php echo esc_js($tiktok_business_account) ?>",
                tiktok_catalog_id: "<?php echo esc_js($result[0]['tiktok_catalog_id']) ?>",
                ms_store_id: "<?php echo esc_js($microsoft_merchant_center_id) ?>",
                ms_catalog_id: "<?php echo esc_js($microsoft_catalog_id) ?>",
                conv_licence_nonce: "<?php echo esc_js(wp_create_nonce('conv_licence-nonce')); ?>"
            };
            conv_change_loadingbar('show');
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: product_data,
                success: function(response) {
                    conv_change_loadingbar('hide');
                    /*********Remove All other spinner***********/
                    jQuery('.status_').remove();
                    jQuery('.issue_').remove();
                    jQuery('.action_').remove();
                    if (response != "Product does not exists" && response != "Product not synced") {
                        var AllApproved = 0;
                        var AllDisapproved = 0;
                        var AllPending = 0;

                        jQuery('.rejected-text*').text(0)
                        jQuery('.pending-text*').text(0)
                        jQuery('.approved-text*').text(0)
                        jQuery('.allApprovedCount').text(AllApproved);
                        jQuery('.allRejectedCount').text(AllDisapproved);
                        jQuery('.channel_logo_*').html('')
                        jQuery('.rejected_issue_text_*').html('')
                        jQuery('.pending_issue_text_*').html('')

                        jQuery.each(response, function(key, value) {
                            var countApproved = 0;
                            var countDisapproved = 0;
                            if (value.productId == '' || value.productId == null) {
                                return true;
                            }
                            var prodID = value.productId.split(':');
                            all_check_list = jQuery.grep(all_check_list, function(values) {
                                return values != prodID[3];
                            });


                            if (value.googleStatus) {
                                if (value.googleStatus && value.googleStatus !==
                                    'disapproved') {
                                    countApproved++;
                                    AllApproved++;
                                    jQuery('.channel_logo_' + prodID[3]).append(
                                        "<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' />"
                                    );
                                } else {
                                    countDisapproved++;
                                    AllDisapproved++;
                                    var uniqueGoogleIssues = value.googleIssues.filter(function(
                                        itm, i, a) {
                                        return i == a.indexOf(itm);
                                    });
                                    var gIssue = '<ul class="b">';
                                    jQuery.each(uniqueGoogleIssues, function(key, issue) {
                                        gIssue += '<li>' + issue + '</li>';
                                    });
                                    gIssue += '</ul>'
                                    jQuery('.rejected_issue_text_' + prodID[3]).append(
                                        "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'>" +
                                        gIssue + "</span>");
                                }
                            }

                            if (value.facebookStatus && value.facebookStatus !==
                                'disapproved') {
                                countApproved++;
                                AllApproved++;
                                jQuery('.channel_logo_' + prodID[3]).append(
                                    "<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') ?>' />"
                                );
                            } else if (value.facebookStatus) {
                                countDisapproved++;
                                AllDisapproved++;
                                jQuery('.rejected_issue_text_' + prodID[3]).append(
                                    "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') ?>' /> Facebook Catalog</h2><span class='text-dark fs-7'>" +
                                    value.facebookIssues + "</span>");
                            }

                            if (value.tiktokStatus && value.tiktokStatus !== 'disapproved') {
                                countApproved++;
                                AllApproved++;
                                jQuery('.channel_logo_' + prodID[3]).append(
                                    "<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' />"
                                );
                            } else if (value.tiktokStatus) {
                                countDisapproved++;
                                AllDisapproved++;
                                jQuery('.rejected_issue_text_' + prodID[3]).append(
                                    "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' /> Tiktok Catalog</h2><span class='text-dark fs-7'>" +
                                    value.tiktokIssues + "</span>");
                            }

                            if (value.microsoftStatus && (value.microsoftStatus !== 'Disapproved' && value.microsoftStatus !== 'disapproved')) {
                                countApproved++;
                                AllApproved++;
                                jQuery('.channel_logo_' + prodID[3]).append(
                                    "<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg') ?>' />"
                                );
                            } else if (value.microsoftStatus) {
                                countDisapproved++;
                                AllDisapproved++;
                                jQuery('.rejected_issue_text_' + prodID[3]).append(
                                    "<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms_channel_logo.svg') ?>' /> Microsoft Catalog</h2><span class='text-dark fs-7'>" +
                                    value.microsoftIssues + "</span>");
                            }

                            jQuery('.approved_count_' + prodID[3]).text(countApproved);
                            jQuery('.rejected_count_' + prodID[3]).text(countDisapproved)
                            jQuery('#channel_action_' + prodID[3]).empty();
                            jQuery('#channel_action_' + prodID[3]).append(
                                '<span class="material-symbols-outlined pointer" id="action_' +
                                prodID[3] + '" value="Delete" onclick="deleteProduct(' +
                                jQuery('#feed_id').val() + ', \'' + prodID[3] +
                                '\')">delete</span>');
                            jQuery('#channel_action_' + prodID[3]).next('.action_').remove();

                            console.log('wow1');
                            console.log(value); //wow1
                        });

                        jQuery('.allApprovedCount').text(addCommas(AllApproved));
                        jQuery('.allRejectedCount').text(addCommas(AllDisapproved));
                        jQuery.each(all_check_list, function(remain_key, remain_value) {
                            jQuery('.channelStatus_' + remain_value).empty();
                            jQuery('.channelStatus_' + remain_value).html('Not yet sync');
                            jQuery('#channel_action_' + remain_value).empty();
                            jQuery('#channel_action_' + remain_value).append(
                                '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                                remain_value + '" value="' + remain_value.replace(prefix,
                                    '') +
                                '">cached</span><input type="hidden" class="filteredProductSyn_' +
                                remain_value + '" value="' + remain_value.replace(prefix,
                                    '') + '">');
                        });

                        jQuery.each(uncheck, function(ch_key, ch_value) {
                            jQuery('.channelStatus_' + ch_value).empty();
                            jQuery('.channelStatus_' + ch_value).html('Not yet sync');
                            jQuery('#channel_action_' + ch_value).empty();
                            jQuery('#channel_action_' + ch_value).append(
                                '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                                ch_value + '" value="' + ch_value.replace(prefix, '') +
                                '">cached</span><input type="hidden" class="filteredProductSyn_' +
                                ch_value + '" value="' + ch_value.replace(prefix, '') + '">'
                            );
                        });

                    } else {
                        jQuery.each(all_check_list, function(key, value) {
                            jQuery('.channelStatus_' + value).empty();
                            jQuery('.channelStatus_' + value).html('Not yet sync');
                            jQuery('#channel_action_' + value).empty();
                            jQuery('#channel_action_' + value).append(
                                '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                                value + '" value="' + value +
                                '">cached</span><input type="hidden" class="filteredProductSyn_' +
                                value + '" value="' + value + '">');
                        });

                        jQuery.each(uncheck, function(ch_key, ch_value) {
                            jQuery('.channelStatus_' + ch_value).empty();
                            jQuery('.channelStatus_' + ch_value).html('Not yet sync');
                            jQuery('#channel_action_' + ch_value).empty();
                            jQuery('#channel_action_' + ch_value).append(
                                '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' +
                                ch_value + '" value="' + ch_value.replace(prefix, '') +
                                '">cached</span><input type="hidden" class="filteredProductSyn_' +
                                ch_value + '" value="' + ch_value.replace(prefix, '') + '">'
                            );
                        });
                    }
                }
            });
        }
    });
</script>