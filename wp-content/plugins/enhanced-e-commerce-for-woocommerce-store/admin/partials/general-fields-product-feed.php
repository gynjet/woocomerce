<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] != 'feed_list') ) {
    wp_safe_redirect("admin.php?page=conversios-google-shopping-feed&tab=feed_list"); //Odd
    exit;
}
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
echo "<script>var return_url ='" . esc_url_raw($this->url) . "';</script>";
$TVC_Admin_Helper = new TVC_Admin_Helper();
$customApiObj = new CustomApi();
$TVCProductSyncHelper = new TVCProductSyncHelper();
$wooCommerceAttributes = array_map("unserialize", array_unique(array_map("serialize", $TVCProductSyncHelper->wooCommerceAttributes())));

$ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
$tempAddAttr = $ee_mapped_attrs;
$ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
$product_id_prefix = '';
if (isset($ee_additional_data['product_id_prefix']) === TRUE && $ee_additional_data['product_id_prefix'] === TRUE) {
    $product_id_prefix = $ee_additional_data['product_id_prefix'];
}
$gmcAttributes = $TVC_Admin_Helper->get_gmcAttributes();
$category_wrapper_obj = new Tatvic_Category_Wrapper();
$country = $TVC_Admin_Helper->get_woo_country();
$currentCustomerId = $TVC_Admin_Helper->get_currentCustomerId();
$class = "";
$message_p = "";
$validate_pixels = array();
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$plan_id = 1;
$site_url_feedlist = "admin.php?page=conversios-google-shopping-feed&tab=feed_list";
$googleDetail = "";
if (isset($google_detail['setting'])) {
    $googleDetail = $google_detail['setting'];
    if (isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))) {
        $plan_id = esc_html($googleDetail->plan_id);
    }
}

$data = unserialize(get_option('ee_options'));
if(isset($_GET['tab']) === FALSE 
    && ((isset($data['google_merchant_id']) && $data['google_merchant_id'] !== '') 
    || (isset($data['tiktok_setting']['tiktok_business_id']) && $data['tiktok_setting']['tiktok_business_id'] != '')
    || (isset($data['facebook_setting']['fb_business_id']) && $data['facebook_setting']['fb_business_id'] != '')
    )) 
{
    //wp_safe_redirect("admin.php?page=conversios-google-shopping-feed&tab=feed_list");
    //exit;
}
$channel_not_connected = array(
    "gmc_id" => (isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') ? '' : 'conv-pixel-not-connected',
    "tiktok_bussiness_id" => (isset($data['tiktok_setting']['tiktok_business_id']) && $data['tiktok_setting']['tiktok_business_id'] != '') ? '' : 'tik-tok-not-connected',
    "fb_business_id" => (isset($data['facebook_setting']['fb_business_id']) && $data['facebook_setting']['fb_business_id'] != '') ? '' : 'facebook-not-connected',
);
global $wp_filesystem;
$getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");
// $getCountris = @file_get_contents(ENHANCAD_PLUGIN_DIR."includes/setup/json/countries.json");
$contData = json_decode($getCountris);
$site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
$conv_data = $TVC_Admin_Helper->get_store_data();
?>
<style>
body {
    max-height: 100%;
    background: #f0f0f1;
}

#wpbody-content {
    overflow: hidden;
}

#tvc_popup_box {
    width: 500px;
    overflow: hidden;
    background: #eee;
    box-shadow: 0 0 10px black;
    border-radius: 10px;
    position: absolute;
    top: 30%;
    left: 40%;
    display: none;
}

.errorInput {
    border: 1.3px solid #ef1717 !important;
    padding: 0px;
    border-radius: 6px;
}
</style>
<!-- Main container -->
<div class="container-old conv-container conv-setting-container pt-4">
    <!-- Main row -->
    <div class="row justify-content-center" style="--bs-gutter-x: 0rem;">
        <!-- Main col8 center -->
        <div class="convfixedcontainermid col-md-8 col-xs-12 m-0 p-0">

            <div class="pt-4 pb-4 conv-heading-box">
                <h3>CHANNEL CONFIGURATION</h3>
                <span>You can configure your Ads channels for your product feeds</span>
            </div>
            <!-- Google Merchant card Start -->
            <div class="convcard d-flex flex-row p-2 mt-0 rounded-3 shadow-sm">
                <div class="convcard-left conv-pixel-logo">
                    <div class="convcard-logo text-center p-2 pe-3 border-end">
                        <img
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_gmc_logo.png'); ?>" />
                    </div>
                </div>
                <div class="convcard-center p-2 ps-3 col-10">
                    <div class="convcard-title">
                        <div class="row">
                            <div class="col-8">
                                <h3><?php esc_html_e("Google Merchant Center", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </h3>
                            </div>
                            <div class="col-4">
                                <span
                                    class="float-end badge rounded-pill conv-badge <?php echo !empty($channel_not_connected['gmc_id']) ? "conv-badge-yellow" : "conv-badge-green"; ?>">
                                    <?php echo !empty($channel_not_connected['gmc_id']) ? "Not Connected" : "Connected"; ?>
                                </span>
                            </div>
                        </div>
                        <?php if (isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') { ?>
                        <span>
                            <?php esc_html_e("Google Merchant Center Account -", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <?php echo esc_html($data['google_merchant_id']) ?>
                        </span>
                        <?php } ?>

                        <hr>
                        <div class="d-flex">
                            <span>
                                <?php esc_html_e("How to connect your Google Merchant Center Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <a class="conv-link-blue conv-watch-video"
                                    href="https://www.youtube.com/watch?v=Ku8iW02Os-w" target="_blank">
                                    Watch here
                                    <span class="material-symbols-outlined align-middle">play_circle_outline</span>
                                </a>
                            </span>
                        </div>

                        <div class="d-flex mt-3">
                            <span>
                                <?php esc_html_e("Benefits of integrating Google Merchant Center Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <a class="conv-link-blue conv-watch-video"
                                    href="https://www.conversios.io/docs/benefits-of-product-sync-to-google-merchant-center/?utm_source=gmc_inapp&utm_medium=resource_center_list&utm_campaign=resource_center"
                                    target="_blank">
                                    Click here
                                    <span class="material-symbols-outlined align-middle">open_in_new_outline</span>
                                </a>
                            </span>
                        </div>

                    </div>
                </div>
                <div class="convcard-right ms-auto">
                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=gmcsettings'); ?>"
                        class="h-100 rounded-end d-flex justify-content-center convcard-right-arrow link-dark">
                        <span class="material-symbols-outlined align-self-center">chevron_right</span>
                    </a>
                </div>
            </div>

            <!-- TikTok Business Account Start -->
            <div class="convcard d-flex flex-row p-2 mt-4 rounded-3 shadow-sm">
                <div class="convcard-left conv-pixel-logo">
                    <div class="convcard-logo text-center p-2 pe-3 border-end">
                        <img
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_tiktok_logo.png'); ?>" />
                    </div>
                </div>
                <div class="convcard-center p-2 ps-3 col-10">
                    <div class="convcard-title">
                        <div class="row">
                            <div class="col-8">
                                <h3><?php esc_html_e("TikTok Business Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </h3>
                            </div>
                            <div class="col-4">
                                <span
                                    class="float-end badge rounded-pill conv-badge <?php echo !empty($channel_not_connected['tiktok_bussiness_id']) ? "conv-badge-yellow" : "conv-badge-green"; ?>">
                                    <?php echo !empty($channel_not_connected['tiktok_bussiness_id']) ? "Not Connected" : "Connected"; ?>
                                </span>
                            </div>
                        </div>

                        <?php if (isset($data['tiktok_setting']['tiktok_business_id'] ) && $data['tiktok_setting']['tiktok_business_id']  != '') { ?>
                        <span>
                            <?php esc_html_e("TikTok Business Account -", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <?php echo esc_html($data['tiktok_setting']['tiktok_business_id']) ?>
                        </span>
                        <?php } ?>
                        <hr>
                        <div class="d-flex mt-3">
                            <span>
                                <?php esc_html_e("Benefits and how to integrate Tiktok Business Account","enhanced-e-commerce-for-woocommerce-store") ?>
                                <a class="conv-link-blue conv-watch-video"
                                    href="https://www.conversios.io/docs/how-to-create-product-feed-to-your-tik-tok-catalog/"
                                    target="_blank">
                                    Click here
                                    <span class="material-symbols-outlined align-middle">open_in_new_outline</span>
                                </a>
                            </span>
                        </div>

                    </div>
                </div>
                <div class="convcard-right ms-auto">
                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=tiktokBusinessSettings'); ?>"
                        class="h-100 rounded-end d-flex justify-content-center convcard-right-arrow link-dark">
                        <span class="material-symbols-outlined align-self-center">chevron_right</span>
                    </a>
                </div>
            </div>
            <!-- TikTok Business Account End -->

            <!-- Meta Business Account Start -->
            <div class="convcard d-flex flex-row p-2 mt-4 rounded-3 shadow-sm">
                <div class="convcard-left conv-pixel-logo">
                    <div class="convcard-logo text-center p-2 pe-3 border-end">
                        <img
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_fb_catalog_logo.png'); ?>" />
                    </div>
                </div>
                <div class="convcard-center p-2 ps-3 col-10">
                    <div class="convcard-title">
                        <div class="row">
                            <div class="col-8">
                                <h3><?php esc_html_e("Facebook Business Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </h3>
                            </div>
                            <div class="col-4">
                                <span
                                    class="float-end badge rounded-pill conv-badge <?php echo !empty($channel_not_connected['fb_business_id']) ? "conv-badge-yellow" : "conv-badge-green"; ?>">
                                    <?php echo !empty($channel_not_connected['fb_business_id']) ? "Not Connected" : "Connected"; ?>
                                </span>
                            </div>
                        </div>
                        <?php if (isset($data['facebook_setting']['fb_business_id']) && $data['facebook_setting']['fb_business_id']  != '') { ?>
                        <span>
                            <?php esc_html_e("Facebook Business Account -", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <?php echo esc_html($data['facebook_setting']['fb_business_id']) ?>
                        </span>
                        <?php } ?>
                        <hr>
                        <div class="d-flex mt-3">
                            <span>
                                <?php esc_html_e("Benefits and how to integrate Facebook Business Account","enhanced-e-commerce-for-woocommerce-store") ?>
                                <a class="conv-link-blue conv-watch-video"
                                    href="https://www.conversios.io/docs/how-to-sync-your-woocommerce-products-to-facebook-catalogue/"
                                    target="_blank">
                                    Click here
                                    <span class="material-symbols-outlined align-middle">open_in_new_outline</span>
                                </a>
                            </span>
                        </div>

                    </div>
                </div>
                <div class="convcard-right ms-auto">
                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=metasettings'); ?>"
                        class="h-100 rounded-end d-flex justify-content-center convcard-right-arrow link-dark">
                        <span class="material-symbols-outlined align-self-center">chevron_right</span>
                    </a>
                </div>
            </div>
            <!-- Meta Business Account End -->

            <?php if ($plan_id == 1) { ?>
            <!-- Blue upgrade to pro -->
            <div class="convcard conv-green-grad-bg rounded-3 d-flex flex-row p-3 mt-4 shadow-sm">
                <div class="convcard-blue-left align-self-center p-2 bd-highlight">
                    <h3 class="text-light mb-3">
                        <?php esc_html_e("Upgrade your Plan to get pro benefits", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h3>
                    <span class="text-light">
                        <ul class="conv-green-banner-list ps-4">
                            <li>
                                <?php esc_html_e("Take control, boost speed. Automate your Google Tag Manager.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                            <li>
                                <?php esc_html_e("Maximize campaigns with Google Ads Conversion integration.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                            <li>
                                <?php esc_html_e("Quick and Easy install of Facebook Conversions API to drive sales via Facebook Ads.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                            <li>
                                <?php esc_html_e("Sync unlimited product feeds with Content API and more.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                            <li>
                                <?php esc_html_e("Make data-driven decisions. Scale your ecommerce business with our reporting dashboard.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                            <li>
                                <?php esc_html_e("Free website audit, dedicated success manager, priority slack support.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </li>
                        </ul>
                    </span>
                    <span class="d-flex">
                        <a style="padding:8px 24px 8px 24px;" class="btn conv-yellow-bg mt-4 btn-lg"
                            href="<?php echo esc_url($TVC_Admin_Helper->get_conv_pro_link_adv("banner", "channel_config", "", "linkonly")); ?>"
                            target="_blank">Upgrade Now</a>
                    </span>
                </div>
                <div class="convcard-blue-right align-self-center p-2 bd-highlight mx-auto">
                    <img
                        src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/freetopaid_banner_img.png'); ?>" />
                </div>
            </div>
            <!-- Blue upgrade to pro End -->
            <?php } ?>

        </div>
        <!-- Main col8 center -->
    </div>
    <!-- Main row -->
    <!-- Product Mapping card Start -->
    <div class="container-old conv-container conv-setting-container pt-0" id="attributeMapping">
        <div class="row justify-content-center">
            <!-- Main col8 center -->
            <div class="convfixedcontainermid col-8 col-xs-12 m-0 p-0">
                <!-- Google Merchant card End -->
                <div id="conv_att_map" class="pt-4 pb-4 conv-heading-box">
                    <h3><?php esc_html_e("ATTRIBUTE MAPPING", "enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                    <span><?php esc_html_e("At Conversios, we provide an automatic mapping feature that enables you to align the
                        categories
                        and attributes of your WooCommerce products with Conversios categories and attributes. This
                        mapping
                        ensures that your product categories and attributes seamlessly correspond to the categories
                        and
                        attributes of the selected channels.", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                </div>
                <!-- Product Mapping card Start -->
                <div id="loadingbar_blue" class="progress-materializecss d-none ps-2 pe-2">
                    <div class="indeterminate"></div>
                </div>
                <div class="convcard flex-row p-2 mt-0 rounded-3 shadow-sm container-fluid font-style"
                    style="height:700px">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link-conv active" id="pills-home-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                                aria-selected="true"><?php esc_html_e("Attribute Mapping", "enhanced-e-commerce-for-woocommerce-store") ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link-conv" id="pills-profile-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                                aria-selected="false"><?php esc_html_e("Category Mapping", "enhanced-e-commerce-for-woocommerce-store") ?></button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                            aria-labelledby="pills-home-tab">
                            <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;">
                                <div class="col-6 pt-2">
                                    <span class="ps-2">
                                        <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conversios_logo.png'); ?>" />
                                        <?php esc_html_e("Conversios Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?></span>
                                </div>
                                <div class="col-6 pt-2 ps-0">
                                    <span class="ps-0">
                                        <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/woocommerce_logo.png'); ?>" />
                                        <?php esc_html_e("WooCommerce Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?></span>
                                </div>
                            </div>
                            <div class="col-12 row bg-white m-0 p-0 mb-3">
                                <div class="col-12  attributeDiv"
                                    style="overflow-y: auto; max-height:550px; position: relative;">
                                    <form id="attribute_mapping" class="row">
                                        <?php 
                                        if (is_array($gmcAttributes)) {
                                            foreach ($gmcAttributes as $key => $attribute) {
                                                if( isset($tempAddAttr[$attribute["field"]]) )
                                                unset($tempAddAttr[$attribute["field"]]);
                                                $sel_val = ""; ?>
                                        <div class="col-6 mt-2">
                                            <span class="ps-3 fw-400 text-color fs-12">
                                                <?php echo esc_attr($attribute["field"]) . " " . (isset($attribute["required"]) === TRUE && esc_attr($attribute["required"]) === '1' ? '<span class="text-color fs-6"> *</span>' : ""); ?>
                                                <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                                    data-bs-placement="right"
                                                    title="<?php echo (isset($attribute['desc']) === TRUE ? esc_attr($attribute['desc']) : ''); ?>">
                                                    info
                                                </span>
                                            </span>
                                        </div>
                                        <div class="col-5 mt-2">
                                            <?php
                                                    $ee_select_option = $TVC_Admin_Helper->add_additional_option_in_tvc_select($wooCommerceAttributes, $attribute["field"]);
                                                    $require = FALSE;
                                                    if (isset($attribute['required']) === TRUE) {
                                                        $require = TRUE;
                                                    }
                                                    $sel_val_def = "";
                                                    if (isset($attribute['wAttribute']) === TRUE) {
                                                        $sel_val_def = $attribute['wAttribute'];
                                                    }
                                                    if ($attribute["field"] === 'link') {
                                                        "product link";
                                                    } else if ($attribute["field"] === 'shipping') {
                                                        $sel_val = esc_attr($sel_val_def);
                                                        if (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) {
                                                            $sel_val = esc_attr($ee_mapped_attrs[$attribute["field"]]);
                                                        }

                                                        $TVC_Admin_Helper->tvc_text($attribute["field"], 'number', '', esc_html__('Add shipping flat rate', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require);
                                                    } else if ($attribute["field"] === 'tax') {
                                                        $sel_val = esc_attr($sel_val_def);
                                                        if (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) {
                                                            $sel_val = esc_attr($ee_mapped_attrs[$attribute["field"]]);
                                                        }

                                                        $TVC_Admin_Helper->tvc_text($attribute["field"], 'number', '', 'Add TAX flat (%)', $sel_val, $require);
                                                    } else if ($attribute["field"] === 'content_language') {
                                                        $TVC_Admin_Helper->tvc_language_select($attribute["field"], 'content_language', esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), 'en', $require);
                                                    } else if ($attribute["field"] === 'target_country') {
                                                        $TVC_Admin_Helper->tvc_countries_select($attribute["field"], 'target_country', esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $require);
                                                    } else {
                                                        if (isset($attribute['fixed_options']) === TRUE && $attribute['fixed_options'] !== "") {
                                                            $ee_select_option_t = explode(",", $attribute['fixed_options']);
                                                            $ee_select_option = [];
                                                            foreach ($ee_select_option_t as $o_val) {
                                                                $ee_select_option[]['field'] = esc_attr($o_val);
                                                            }

                                                            $sel_val = $sel_val_def;
                                                            $TVC_Admin_Helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require, $ee_select_option);
                                                        } else {
                                                            $sel_val = esc_attr($sel_val_def);
                                                            if (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) {
                                                                $sel_val = esc_attr($ee_mapped_attrs[$attribute["field"]]);
                                                            }
                                                            $TVC_Admin_Helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require, $ee_select_option);
                                                        }
                                                    } //end attribute if                                                
                                                    ?>
                                        </div>
                                        <?php 
                                            } //end gmcAttributes foreach 
                                        }
                                        ?>
                                        <div class="col-12 m-0 p-0 additinal_attr_main_div">
                                            <?php
                                            $cnt = 0;
                                            if(!empty($tempAddAttr)) {
                                                $additionalAttribute = array('condition','shipping_weight','product_weight','gender','sizes','color','age_group','additional_image_links', 'sale_price_effective_date','material',
                                                                            'pattern','product_types','availability_date','expiration_date','adult', 'ads_redirect',
                                                                            'shipping_length','shipping_width', 'shipping_height','custom_label_0',
                                                                            'custom_label_1','custom_label_2',
                                                                            'custom_label_3','custom_label_4','mobile_link','energy_efficiency_class',
                                                                            'is_bundle','loyalty_points','unit_pricing_measure','unit_pricing_base_measure',
                                                                            'promotion_ids','shipping_label','excluded_destinations','included_destinations','tax_category',
                                                                            'multipack','installment','min_handling_time','max_handling_time','min_energy_efficiency_class',
                                                                            'max_energy_efficiency_class','identifier_exists','cost_of_goods_sold');                                               
                                                $count_arr = count($additionalAttribute);
                                                foreach($tempAddAttr as $key => $value){ 
                                                    $options = '<option>Please Select Attribute</option>';
                                                    foreach($additionalAttribute as $val ) { 
                                                        $selected = "";
                                                        $disabled = "";                                                       
                                                        if($val == $key) {
                                                            $selected = "selected";
                                                        }else{
                                                            if(array_key_exists($val, $tempAddAttr)) {
                                                                $disabled = "disabled"; 
                                                            }
                                                        }
                                                        
                                                        $options .= '<option value="'.$val.'" '.$selected.' '.$disabled.'>'.$val.'</option>';
                                                     } 
                                                     $option1 = '<option>Please Select Attribute</option>';
                                                     $fixed_att_select_list = ["gender", "age_group", "condition", "adult", "is_bundle", "identifier_exists"];
                                                     if(in_array($key, $fixed_att_select_list)) {
                                                        if($key == 'gender') {    
                                                          $gender = ['male' => 'Male', 'female' => 'Female', 'unisex' => 'Unisex'];
                                                          foreach($gender as $genKey => $genVal) {
                                                            $selected = "";
                                                            if($genKey == $value) {
                                                              $selected = "selected";
                                                            }
                                                            $option1 .= '<option value="'.$genKey.'" '.$selected.'>'.$genVal.'</option>';
                                                          }
                                                        }
                                                        if($key == 'condition') {
                                                          $conArr = ['new' => 'New', 'refurbished' => 'Refurbished', 'used' => 'Used'];
                                                          foreach($conArr as $conKey => $conVal) {
                                                            $selected = "";
                                                            if($conKey == $value) {
                                                              $selected = "selected";
                                                            }
                                                            $option1 .= '<option value="'.$conKey.'" '.$selected.'>'.$conVal.'</option>';
                                                          }
                                                        }
                                                        if($key == 'age_group') {    
                                                          $ageArr = ['newborn' => 'Newborn', 'infant' => 'Infant', 'toddler' => 'Toddler', 'kids' => 'Kids', 'adult' => 'Adult'];            
                                                          foreach($ageArr as $ageKey => $ageVal) {
                                                            $selected = "";
                                                            if($ageKey == $value) {
                                                              $selected = "selected";
                                                            }
                                                            $option1 .= '<option value="'.$ageKey.'" '.$selected.'>'.$ageVal.'</option>';
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
                                                      }else {
                                                        foreach($wooCommerceAttributes as $valattr ) { 
                                                            $selected = "";
                                                            if($valattr['field'] == $value) {
                                                                $selected = "selected";
                                                            }
                                                            $option1 .= '<option value="'.$valattr['field'].'" '.$selected.'>'.$valattr['field'].'</option>';
                                                        } 
                                                    }
                                                    ?>
                                            <div class="row additinal_attr_div m-0 p-0">
                                                <div class="col-6 mt-2">
                                                    <select style="width:100%" id="<?php echo esc_attr($cnt++) ?>"
                                                        name="additional_attr_[]"
                                                        class="additinal_attr fw-light text-secondary fs-6 form-control form-select-sm select2 select2-hidden-accessible">
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
                                                    <select style="width:100%" id="" name="additional_attr_value_[]"
                                                        class="additional_attr_value fw-light text-secondary fs-6 form-control form-select-sm select2 select2-hidden-accessible">
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
                                                    <span
                                                        class="material-symbols-outlined text-danger remove_additional_attr fs-5 mt-2"
                                                        title="Add Additional Attribute"
                                                        style="cursor: pointer; margin-right:35px;">
                                                        delete
                                                    </span>
                                                </div>
                                            </div>
                                            <?php }
                                            } ?>
                                        </div>
                                        <div class="row add_additional_attr_div m-0 p-0">
                                            <div class="add_additional_attr_div mt-2"
                                                style="display: flex; justify-content: start">
                                                <button type="button"
                                                    class="fs-12 btn btn-soft-primary add_additional_attr <?php echo isset($count_arr) && $count_arr == $cnt ? 'd-none' : ''?>"
                                                    title="Add Attribute"> Add Attributes
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="button" id="attr_mapping_save"
                                                class="btn btn-soft-primary float-end mt-2 ps-4 pe-4">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                            aria-labelledby="pills-profile-tab">
                            <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;">
                                <div class="col-6 pt-2">
                                    <span class="ps-2 fw-normal">
                                        <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/woocommerce_logo.png'); ?>" />
                                        <?php esc_html_e("WooCommerce Product Category", "enhanced-e-commerce-for-woocommerce-store") ?></span>
                                </div>
                                <div class="col-6 pt-2 ps-0">
                                    <span class="ps-1 fw-normal">
                                        <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conversios_logo.png'); ?>" />
                                        <?php esc_html_e("Conversios Product Category", "enhanced-e-commerce-for-woocommerce-store") ?></span>
                                </div>
                            </div>
                            <div class="row bg-white m-0 p-0 mb-3">
                                <div class="col-12 categoryDiv"
                                    style="overflow-y: scroll; max-height:550px; position: relative;">
                                    <form id="category_mapping">
                                        <?php $category_html = $category_wrapper_obj->category_table_content(0, 0, 'mapping'); 
                                            echo wp_kses($category_html, array(
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
                                        <div class="col-12">
                                            <button type="button" id="cat_mapping_save"
                                                class="btn btn-soft-primary float-end mt-2 ps-4 pe-4">Save</button>
                                        </div>
                                        <input type="hidden" name="selectedCategory" id="selectedCategory">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Mapping card End -->
    <!-- Create Feed Modal -->
    <div class="modal fade" id="convCreateFeedModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            onclick="jQuery('#feedForm')[0].reset()"></button>
                    </div>
                    <div class="modal-body ps-4 pt-0">
                        <div class="mb-4 feed_name">
                            <label for="feed_name" class="col-form-label text-dark fs-14 fw-500">
                                <?php esc_html_e("Feed Name", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Add a name to your feed for your reference, for example, 'April end-of-season sales' or 'Black Friday sales for the USA'.">
                                info
                            </span>
                            <input type="text" class="form-control fs-14" name="feedName" id="feedName"
                                placeholder="e.g. New Summer Collection">
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5">
                                <label for="auto_sync" class="col-form-label text-dark fs-14 fw-500">
                                    <?php esc_html_e("Auto Sync", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                    data-bs-placement="right"
                                    title="Turn on this feature to schedule an automated product feed to keep your products up to date with the changes made in the products. You can come and change this any time.">
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
                                <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                    data-bs-placement="right"
                                    title="Set the number of days to schedule the next auto-sync for the products in this feed. You can come and change this any time.">
                                    info
                                </span>
                            </div>
                            <div class="col-7">
                                <input type="text" class="form-control-sm fs-14 " readonly="readonly"
                                    name="autoSyncIntvl" id="autoSyncIntvl" size="3" min="1"
                                    onkeypress="return ( event.charCode === 8 || event.charCode === 0 || event.charCode === 13 || event.charCode === 96) ? null : event.charCode >= 48 && event.charCode <= 57"
                                    oninput="removeZero();" value="25">
                                <label for="" class="col-form-label fs-14 fw-400">
                                    <?php esc_html_e("Days", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <span>
                                    <a target="_blank"
                                        href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=tiktok_banner&utm_campaign=pricing"><b>
                                            Upgrade To Pro</b></a>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-5">
                                <label for="target_country" class="col-form-label text-dark fs-14 fw-500" name="">
                                    <?php esc_html_e("Target Country", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                    data-bs-placement="right"
                                    title="Specify the target country for your product feed. Select the country where you intend to promote and sell your products.">
                                    info
                                </span>
                            </div>
                            <div class="col-7">
                                <select class="select2 form-select form-select-sm mb-3"
                                    aria-label="form-select-sm example" style="width: 100%" name="target_country"
                                    id="target_country">
                                    <option value="">Select Country</option>
                                    <?php
                            $selecetdCountry = $conv_data['user_country'];
                            foreach ($contData as $key => $value) {
                                ?>
                                    <option value="<?php echo esc_attr($value->code) ?>"
                                        <?php echo $selecetdCountry === $value->code ? 'selected = "selecetd"' : '' ?>>
                                        <?php echo esc_html($value->name) ?></option>"
                                    <?php
                            }
                            //echo '<pre>'; print_r($data); echo '</pre>';
                            ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="auto_sync_interval" class="col-form-label text-dark fs-14 fw-500">
                                <?php esc_html_e("Select Channel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Below is the list of channels that you have linked for product feed. Please note you will not be able to make any changes in the selected channels once product feed process is done.">
                                info
                            </span>
                        </div>
                        <?php if(isset($data['google_merchant_id']) === TRUE && $data['google_merchant_id'] !== '') { ?>
                        <div class="mb-3">
                            <div class="form-check form-check-custom">
                                <input class="form-check-input check-height fs-14 woow-789 errorChannel" type="checkbox" value=""
                                    id="gmc_id" name="gmc_id"
                                    <?php echo isset($data['google_merchant_id']) === TRUE ? 'checked' : '' ?>>
                                <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                    <?php esc_html_e("Google Merchant Center Account :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <label class="col-form-label fs-14 pt-0 fw-400 modal_google_merchant_center_id">
                                    <?php echo isset($data['google_merchant_id']) === TRUE ? esc_html($data['google_merchant_id']) : '' ?>
                                </label>
                            </div>
                        </div>
                        <?php }else { ?>
                        <div class="mb-3">
                            <div class="form-check form-check-custom">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                                    onclick="jQuery('#feedForm')[0].reset()">Connect Google Merchant Centre</button>
                            </div>
                        </div>
                        <?php   }?>

                    </div>
                    <div class="modal-footer p-2">
                        <input type="hidden" id="edit" name="edit">
                        <input type="hidden" id="is_mapping_update" name="is_mapping_update" value="">
                        <input type="hidden" id="last_sync_date" name="last_sync_date" value="">
                        <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal"
                            onclick="jQuery('#feedForm')[0].reset()">
                            <?php esc_html_e("Cancel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </button>
                        <button type="button" class="btn btn-soft-primary btn-sm"
                            <?php echo isset($data['google_merchant_id']) === TRUE && $data['google_merchant_id'] !== ''? '' : 'disabled' ?>
                            <?php echo isset($data['google_merchant_id']) === TRUE && $data['google_merchant_id'] !== ''? 'id="submitFeed"' : 'id=""' ?>>
                            <?php esc_html_e("Create and Next", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Suucess Modal---->
<div class="modal fade" id="conv_save_success_modal_cta" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header connection-header border-0 pb-0">
                <div class="connection-box">
                    <div class="items">
                        <img style="width:35px;"
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/popup_woocommerce_logo.png'); ?>">
                        <span>Woo Commerce</span>
                    </div>
                    <div class="items">
                        <span class="material-symbols-outlined text-primary">
                            arrow_forward
                        </span>
                    </div>
                    <div class="items">
                        <img style="width:35px;"
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/popup_mapping_logo.png'); ?>">
                        <span>Conversios Product Attributes</span>
                    </div>

                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="connected-content">
                    <h4>Saved Successfully</h4>
                    <p class="my-3"><?php esc_html_e("Congratulations on successfully mapping your product categories and attributes! By
                        ensuring accurate classification and detailed product information, you've enhanced the
                        discoverability and relevance of your products, providing a better shopping experience for
                        customers.", "enhanced-e-commerce-for-woocommerce-store") ?> </p>
                </div>
                <div>
                    <div class="attributemapping-box">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                                <div class="attribute-box mb-3">
                                    <div class="attribute-icon">
                                        <img style="width:35px;"
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/Manage_feed.png'); ?>">
                                    </div>
                                    <div class="attribute-content para">
                                        <h3><?php esc_html_e("Manage Feeds", "enhanced-e-commerce-for-woocommerce-store") ?>
                                        </h3>
                                        <p>
                                            <?php esc_html_e("A feed management tool offers benefits such as centralized product updates,
                                            optimized product listings, and improved data quality, ultimately enhancing
                                            the efficiency and effectiveness of your product feed management process.", "enhanced-e-commerce-for-woocommerce-store") ?>

                                        </p>
                                        <div class="attribute-btn">
                                            <a href="<?php echo esc_url_raw($site_url_feedlist); ?>"
                                                class="btn btn-dark common-btn">Manage Feed</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Save Modal -->
<div class="modal fade" id="conv_save_error_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;"
                    src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/error_logo.png'); ?>">
                <h3 class="fw-normal pt-3">Error</h3>
                <span id="conv_save_error_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Error Save Modal End -->
<!-- Main container End -->
<?php
$fpath = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/category.json';
global $wp_filesystem;
$str = $wp_filesystem->get_contents($fpath);
$str = json_decode($str);
?>
<script>
var cat_json = <?php echo wp_json_encode($str) ?>;
jQuery(document).ready(function() {

    jQuery('.select2').select2();
    jQuery(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });
    jQuery('.target_country').select2();
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
/****************************** Mapping value is Numeric Start ************************************************************************************/
jQuery(document).on('keydown', 'input[name="shipping"]', function(event) {
    if (event.shiftKey == true) {
        event.preventDefault();
    }
    if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event
        .keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode ==
        46 || event.keyCode == 190) {

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
    if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event
        .keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode ==
        46 || event.keyCode == 190) {

    } else {
        event.preventDefault();
    }

    if (jQuery(this).val().indexOf('.') !== -1 && event.keyCode == 190)
        event.preventDefault();
})
/****************************** Mapping value is Numeric End ************************************************************************************/
/**************************Append All conversios categories to select tag *********************************************************/
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
        jQuery('#' + selectId).select2();
        jQuery('#' + selectId).select2('open');
    }
});
/**************************Append All conversios categories to select tag end*********************************************************/
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
    jQuery('.select2').select2();
});
/***********************Save Attribute Mapping Start **************************************************************************/
jQuery(document).on("click", "#attr_mapping_save", function() {
    /****additional Attribute validation start*********/
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
    /****additional Attribute validation end*********/
    let ee_data = jQuery("#attribute_mapping").find(
        "input[value!=''], select:not(:empty), input[type='number']").serialize();
    var data = {
        action: "save_attribute_mapping",
        ee_data: ee_data,
        auto_product_sync_setting: "<?php echo esc_js(wp_create_nonce('auto_product_sync_setting-nonce')); ?>"
    };
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: data,
        beforeSend: function() {
            jQuery('html, body').animate({
                scrollTop: jQuery("#attributeMapping").offset().top
            }, 0);
            conv_change_loadingbar('show');
        },
        success: function(response) {
            conv_change_loadingbar('hide');
            if (response.error === false) {
                jQuery("#conv_save_success_modal_cta").modal("show");
            } else {
                jQuery("#conv_save_error_txt").html(response.message);
                jQuery("#conv_save_error_modal").modal("show");
            }

        }
    });
});
/***********************Save Attribute Mapping End **************************************************************************/
/***********************Save Category Mapping Start **************************************************************************/
jQuery(document).on("click", "#cat_mapping_save", function() {
    let ee_data = jQuery("#category_mapping").find("input[value!=''], select:not(:empty), input[type='number']")
        .serialize();
    var data = {
        action: "save_category_mapping",
        ee_data: ee_data,
        auto_product_sync_setting: "<?php echo esc_js(wp_create_nonce('auto_product_sync_setting-nonce')); ?>"
    };
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: data,
        beforeSend: function() {

            jQuery('html, body').animate({
                scrollTop: jQuery("#attributeMapping").offset().top
            }, 0);
            conv_change_loadingbar('show');
        },
        success: function(response) {
            conv_change_loadingbar('hide');
            if (response.error === false) {
                jQuery("#conv_save_success_modal_cta").modal("show");
            } else {
                jQuery("#conv_save_error_txt").html(response.message);
                jQuery("#conv_save_error_modal").modal("show");
            }
        }
    });
});
/***********************Save Category Mapping End **************************************************************************/
/***********************Enable edited category Start **************************************************************************/
function selectSubCategory(thisObj) {
    selectId = thisObj.id;
    wooCategoryId = jQuery(thisObj).attr("catid");
    var selvalue = jQuery('#' + selectId).find(":selected").val();
    var seltext = jQuery('#' + selectId).find(":selected").text();
    jQuery("#category-" + wooCategoryId).val(selvalue);
    jQuery("#category-name-" + wooCategoryId).val(seltext);
    setTimeout(function() {
        jQuery(".select2").select2();
    }, 100);
}
/***********************Enable edited category End **************************************************************************/
/***********************Show Loading Bar Start **************************************************************************/
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
    } else {
        jQuery("#loadingbar_blue_modal").addClass('d-none');
        jQuery("#wpbody").css("pointer-events", "auto");
    }
}
/***********************Show Loading Bar End **************************************************************************/
/***************************Call create feed modal ****************************************************************************/
jQuery(".createFeed").on("click", function() {
    jQuery("#conv_save_success_modal_cta").modal("hide");
    jQuery('#autoSyncIntvl').attr('disabled', false);
    jQuery('#gmc_id').attr('disabled', false);
    jQuery('#target_country').attr('disabled', false);
    jQuery("#feedForm")[0].reset();
    jQuery('#feedType').text('Create New Feed');
    jQuery('#edit').val('');
    // jQuery('.modal_google_merchant_center_id').html(jQuery("#google_merchant_center_id").val())
    // jQuery('#gmc_id').val(jQuery("#google_merchant_center_id").val());
    jQuery('#convCreateFeedModal').modal('show');
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    jQuery('.select2').select2({
        dropdownParent: jQuery("#convCreateFeedModal")
    });
});
/***************************Call create feed modal end****************************************************************************/
/***************************Submit Feed call start *******************************************************************************/
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

    if (!jQuery('#gmc_id').is(":checked")) {
        jQuery('.errorChannel').css('border', '1px solid red');
        return false;
    }
    jQuery('#submitFeed').addClass("disabledsection");
    save_feed_data();
});

/**************************Submit Feed call end*******************************************************************************/
/*************************************Save Feed Data Start*************************************************************************/
function save_feed_data(google_merchant_center_id, catalog_id) {
    console.log('saving feed from 1194 line'); // woow 1194
    var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
    let edit = jQuery('#edit').val();
    var data = {
        action: "save_feed_data",
        feedName: jQuery('#feedName').val(),
        google_merchant_center: jQuery('input#gmc_id').is(':checked') ? '1' : '',
        autoSync: jQuery('input#autoSync').is(':checked') ? '1' : '0',
        autoSyncIntvl: '25',
        edit: edit,
        last_sync_date: jQuery('#last_sync_date').val(),
        is_mapping_update: jQuery('#is_mapping_update').val(),
        target_country: jQuery('#target_country').find(":selected").val(),
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
            conv_change_loadingbar_modal('hide');
            jQuery('#convCreateFeedModal').modal('hide');
            if (response.id) {
                jQuery(".created_success").html('Feed Created Successfully');
                jQuery("#conv_save_success_txt_").html('Redirecting To Product List');
                jQuery("#conv_save_success_modal_").modal("show");
                setTimeout(function() {
                    if (edit !== '') {
                        location.reload(true);
                    } else {
                        window.location.replace(
                            "<?php echo esc_url_raw($site_url.'product_list&id='); ?>" +
                            response.id);
                    }

                }, 100);
            } else {
                jQuery("#conv_save_error_txt").html(response.message);
                jQuery("#conv_save_error_modal").modal("show");
            }
        }
    });

}
/*************************************Save Feed Data End***************************************************************************/
</script>
<script>
var selected = Array();
var cnt = <?php echo esc_js($cnt) ?>;
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
        option1 += '<option value="' + value.field + '">' + value.field + '</option>'
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
    jQuery('.select2').select2();
    jQuery('.add_additional_attr')[0].scrollIntoView(true);
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
            var $thisOption = jQuery(this);
            var valueToCompare = deleted;
            if ($thisOption.val() == valueToCompare) {
                $thisOption.removeAttr("disabled");
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
        var $thisOption = jQuery(this);
        var valueToCompare = sel;
        if ($thisOption.val() == valueToCompare) {
            $thisOption.attr("disabled", "disabled");
        }
    });
    var attr_choices = jQuery(".additinal_attr option:selected");
    jQuery(attr_choices).each(function(i, v) {
        selected.push(attr_choices.eq(i).val());
    })
    disableOptions();
})
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
            var $thisOption = jQuery(this);
            var valueToCompare = v;
            if (jQuery(this).parent().find(':selected').val() != v) {
                if ($thisOption.val() == valueToCompare) {
                    $thisOption.attr("disabled", "disabled");
                }
            }

        });
    })


}
</script>