<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

$microsoft_ads_manager_id = isset($ee_options['microsoft_ads_manager_id']) ? $ee_options['microsoft_ads_manager_id'] : "";
$microsoft_ads_subaccount_id = isset($ee_options['microsoft_ads_subaccount_id']) ? $ee_options['microsoft_ads_subaccount_id'] : "";
$microsoft_merchant_center_id = isset($ee_options['microsoft_merchant_center_id']) ? $ee_options['microsoft_merchant_center_id'] : "";
$ms_catalog_id = isset($ee_options['ms_catalog_id']) ? $ee_options['ms_catalog_id'] : "";

$store_country = get_option('woocommerce_default_country');
$store_country = explode(":", $store_country);
if ($store_country[0]) {
    $country = $store_country[0];
} else {
    $country = '';
}

$is_sel_disable = 'disabled';

$microsoft_ads_pixel_id = "";
if (isset($googleDetail->microsoft_ads_pixel_id) === TRUE && $googleDetail->microsoft_ads_pixel_id !== "") {
    $microsoft_ads_pixel_id = $googleDetail->microsoft_ads_pixel_id;
}

$cust_ms_email = "";
if (isset($tvc_data['microsoft_mail']) === TRUE && esc_attr($subscriptionId) !== '') {
    $cust_ms_email = esc_attr($tvc_data['microsoft_mail']);
}

$is_domain_claim = "";
if (isset($googleDetail->is_domain_claim) === TRUE) {
    $is_domain_claim = esc_attr($googleDetail->is_domain_claim);
}

$is_site_verified = "";
if (isset($googleDetail->is_site_verified) === TRUE) {
    $is_site_verified = esc_attr($googleDetail->is_site_verified);
}

$site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
$TVC_Admin_Helper = new TVC_Admin_Helper();
$conv_data = $TVC_Admin_Helper->get_store_data();
//$getCountris = @file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");

global $wp_filesystem;
$getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");

$contData = json_decode($getCountris);
$required_bing = false;
?>
<style>
    .tooltip-inner {
        max-width: 500px !important;
    }

    body {
        max-height: 100%;
        background: #f0f0f1;
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
</style>

<?php if (empty($microsoft_ads_manager_id) || empty($microsoft_ads_subaccount_id) || empty($microsoft_ads_pixel_id)) {
    $required_bing = true; ?>
    <div class="alert alert-danger mb-3 d-flex align-items-center justify-content-end fs-6">
        <span>Please connect your Microsoft Bing Ads account with all properties.</span>
        <a href="<?php echo esc_url(admin_url() . 'admin.php?page=conversios-google-analytics&subpage=bingsettings'); ?>" class="btn btn-soft-primary float-end ms-2 fs-14 fw-500 ">
            <?php esc_html_e("Connect You Bing Ads", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </a>
    </div>
<?php } ?>

<div class="convcard p-4 mt-0 rounded-3 shadow-sm <?php echo $required_bing ? 'disabledsection' : ''; ?>">
    <?php if (isset($pixel_settings_arr[$subpage]['topnoti']) === TRUE && $pixel_settings_arr[$subpage]['topnoti'] !== "") { ?>
        <div class="alert d-flex align-items-cente p-0" role="alert">
            <div class="text-light conv-success-bg rounded-start d-flex">
                <span class="p-2 material-symbols-outlined align-self-center">verified</span>
            </div>
            <div class="p-2 w-100 rounded-end border border-start-0 shadow-sm conv-notification-alert bg-white">
                <div class="">
                    <?php printf('%s', esc_html($pixel_settings_arr[$subpage]['topnoti'])); ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php
    $connect_url = $TVC_Admin_Helper->get_custom_connect_url_subpage(admin_url() . 'admin.php?page=conversios-google-shopping-feed', "mmcsettings");

    // not needed for now as addd in ms-signin.php $confirm_url = "admin.php?page=conversios-google-shopping-feed&subpage=mmcsettings"; // return to page after login success
    require_once "ms-signin.php";
    ?>

    <form id="mmcsetings_form" class="convpixsetting-inner-box mt-4">
        <div id="analytics_box_UA" class="py-1">
            <div class="row">
                <div class="col-6">
                    <label class="text-dark fw-bold-500">
                        <?php esc_html_e("Select Microsoft Merchant Center Store", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <div class="row pt-2 conv-mmcsettings">
                        <div class="col-9">
                            <select id="microsoft_merchant_center_id" name="microsoft_merchant_center_id" class="form-select form-select-lg mb-3 selecttwo valtoshow_inpopup_this" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                                <?php if (!empty($microsoft_merchant_center_id)) { ?>
                                    <option value="<?php echo esc_attr($microsoft_merchant_center_id); ?>" selected>
                                        <?php echo esc_attr($microsoft_merchant_center_id); ?>
                                    </option>
                                <?php } ?>
                                <option value="">Select Microsoft Merchant Center Store</option>
                            </select>
                        </div>
                        <div class="col-1 btn btn-sm btn-primary ms4 conv-enable-selection conv-link-blue">
                            <label class="fs-6 text">
                                <?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                        </div>
                    </div>
                    <label class="text-dark fw-bold-500 mt-4">
                        <?php esc_html_e("Select Microsoft Merchant Catalog Id", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <div class="row pt-2 conv-mc-settings <?php echo !empty($microsoft_merchant_center_id) ? '' : 'disabledsection' ?>">
                        <div class="col-10">
                            <select id="ms_catalog_id" name="ms_catalog_id" class="form-select form-select-lg mb-3 selecttwo valtoshow_inpopup_this" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                                <?php if (!empty($ms_catalog_id)) { ?>
                                    <option value="<?php echo esc_attr($ms_catalog_id); ?>" selected>
                                        <?php echo esc_attr($ms_catalog_id); ?>
                                    </option>
                                <?php } ?>
                                <option value="">Select Microsoft Merchant Catalog ID</option>
                            </select>
                        </div>
                        <div class="col-2 conv-enable-catalog-selection conv-link-blue">
                            <span class="material-symbols-outlined pt-1 ps-2">edit</span><label class="mb-2 fs-6 text">Edit</label>
                        </div>
                    </div>
                </div>
                <div class="col-6 d-flex justify-content-between align-items-center conv_create_new_bing_card rounded px-3 py-3">
                    <div class="col-12 py-2">
                        <!-- Show this after creation -->
                        <div class="alert-message d-flex">
                            <div class="alert alert-success text-start d-none after-mmc-acc-creation">
                                New Microsoft Merchant Center Store With Id: <span id="new_mmc_id"></span> is created
                                successfully.
                            </div>
                        </div>
                        <?php
                        if (isset($is_site_verified) === TRUE && $is_site_verified === '1') {  ?>
                            <!-- <span class="material-symbols-outlined text-success fs-5 site_verified" style="cursor:default">
                                check_circle
                            </span> -->
                        <?php } else { ?>

                            <h5 class="p-1 alert alert-danger d-flex align-items-center justify-content-center">
                                You site is not verified yet!
                                <span class="material-symbols-outlined text-danger fs-5 site_verified">
                                    sync_problem
                                </span>
                            </h5>

                        <?php } ?>
                        <div class="d-flex justify-content-between align-items-center before-mmc-acc-creation
                            <?php echo (isset($is_site_verified) === TRUE && $is_site_verified === '1') ? '' : 'disabledsection'; ?>">

                            <div class="pe-2" style="max-width:300px">
                                <h5 class="text-dark mb-0">Do not have an store?</h5>
                                <span class="text-dark fs-12">
                                    By using Pmax Campaign and Feed Sync with a Microsoft Bing Ads account, you can simplify campaign management, improve conversions, and increase product visibility, ultimately driving more sales and revenue for your business.
                                </span>
                            </div>
                            <div class="align-self-center conv_create_mmc_new_when_notexist <?php echo !empty($microsoft_merchant_center_id) ? 'disabledsection' : '' ?>" bis_skin_checked="1">
                                <a id="conv_create_mmc_new_btn" class="btn btn-primary px-5" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#conv_create_new_bing">
                                    <?php esc_html_e("Create New", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 flex-row pt-3 row d-none for now">
                <div class="col-5">
                    <label class="text-dark">Site Verified</label>
                    <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" data-container="body" title="When you verify your website, you let Google know that you're the owner of the website. You're the website owner if you have the ability to make edits to your website content. Not the website owner? Work together with your website owner or admin to verify the website.">
                        info
                    </span>
                </div>
                <div class="col-6 site_verifiedDiv">
                    <?php
                    if (isset($is_site_verified) === TRUE && $is_site_verified === '1') { ?>
                        <span class="material-symbols-outlined text-success fs-5 site_verified" style="cursor:default">
                            check_circle
                        </span>
                    <?php } else { ?>
                        <span class="material-symbols-outlined text-danger fs-5 site_verified" onclick="call_site_verified()" style="cursor:pointer">
                            sync_problem
                        </span>
                    <?php }
                    ?>
                </div>
            </div>
            <div class="col-12 flex-row pt-3 row domain_claimDiv  d-none for now">
                <div class="col-5">
                    <label class="text-dark">Domain Claim</label>
                    <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" data-container="body" title="When you claim your website, it gives you the right to use your website in connection with your Merchant Center account. First you need to verify your website and then you can claim it. Only the user who verified the website can claim it.">
                        info
                    </span>
                </div>
                <div class="col-6">
                    <?php if ($is_domain_claim === '1') { ?>
                        <span class="material-symbols-outlined text-success fs-5 domain_claim" style="cursor:default">
                            check_circle
                        </span>
                    <?php } else { ?>
                        <span class="material-symbols-outlined text-danger fs-5 domain_claim" onclick="call_domain_claim()" style="cursor:pointer">
                            sync_problem
                        </span>
                    <?php }
                    ?>
                </div>
            </div>


        </div>
    </form>

    <input type="hidden" id="valtoshow_inpopup" value="Microsoft Merchant Center Store:" />
    <input type="hidden" id="ads-account" value="<?php echo esc_attr($microsoft_ads_pixel_id); ?>" />
    <input type="hidden" id="conversios_onboarding_nonce" value="<?php echo esc_attr(wp_create_nonce('conversios_onboarding_nonce')); ?>" />
    <input type="hidden" id="feedType" name="feedType" value="<?php echo isset($_GET['feedType']) && $_GET['feedType'] != '' ? esc_attr(sanitize_text_field(wp_unslash($_GET['feedType']))) : '' ?>" />

</div>


<!-- Create New Ads Account Modal -->
<div class="modal fade" id="conv_create_new_bing" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="conv_form_new_bing">
                <div class="modal-body text-start">
                    <div class="row">
                        <div class="col-12 pe-4">
                            <div id="before_gadsacccreated_text" class="mb-1 fs-6 before-bing-acc-creation">
                                <h5 class="modal-title my-3" id="staticBackdropLabel">
                                    <span id="before_gadsacccreated_title">
                                        <?php esc_html_e("Create New Microsoft Merchant Center Store", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </span>
                                    <span id="after_gadsacccreated_title" class="d-none after-ads-acc-creation">
                                        <?php esc_html_e("New Microsoft Merchant Center Store Created", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </span>
                                </h5>
                                <div id="create_bing_error" class="alert alert-danger d-none" role="alert">
                                    <small></small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-group mt-2">
                                        <span class="inner-text">Store Name</span> <span class="text-danger">*</span>
                                        <input class="form-control mb-2" type="text" id="store_name" name="store_name" value="" placeholder="" required>
                                    </div>

                                    <div class="form-group mt-3">
                                        <span class="inner-text">Notification E-mail</span> <span class="text-danger">*</span>
                                        <input class="form-control mb-2" type="text" id="notification_email" name="notification_email" value="<?php echo esc_attr($cust_ms_email); ?>" placeholder="" required>
                                    </div>
                                    <div class="form-group mt-3">
                                        <span class="inner-text">Store URL</span> <span class="text-danger">*</span>
                                        <input class="form-control mb-0" readonly type="text" id="store_url" name="store_url" value="<?php echo esc_url(home_url()); ?>" required>
                                    </div>

                                    <input type="hidden" id="notification_language" name="notification_language" value="en-US" required>

                                    <div class="form-group mt-3">
                                        <input id="bing_concent" name="concent" class="form-check-input" type="checkbox" value="1" required style="float:none">
                                        <label class="form-check-label fs-12" for="concent">
                                            <?php esc_html_e("I accept the", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            <a class="fs-14" target="_blank" href="<?php echo esc_url("https://www.microsoft.com/en-gb/servicesagreement"); ?>"><?php esc_html_e("terms & conditions", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                            <span class="text-danger"> *</span>
                                        </label>
                                    </div>

                                </div>

                            </div>
                            <!-- Show this after creation -->
                            <div class="onbrdpp-body alert alert-primary text-start d-none after-bing-acc-creation">
                                New Microsoft Bing Ads Account With Id: <span id="new_bing_id"></span> is created
                                successfully.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="me-auto">
                        <button id="create_merchant_account_new" class="btn conv-blue-bg text-white before-mmc-acc-creation me-auto">
                            <span id="gadsinviteloader" class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
                            <?php esc_html_e("Create", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </button>

                        <button type="button" class="ms-3 btn btn-secondary me-auto" data-bs-dismiss="modal" id="model_close_mmc_creation">
                            <?php esc_html_e("Close", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Error Save Modal -->
<div class="modal fade" id="conv_save_error_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 99999">
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
                ); ?>;
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
<div class="modal fade" id="conv_save_success_modal_" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 99999">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
            </div>
            <div class="modal-body text-center p-0">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/logos/successImg.png', '', '', 'width:184px;'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>;
                <h3 class="fw-normal pt-3 created_success">
                    <?php esc_html_e("Updated Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h3>
                <span id="conv_save_success_txt_" class="mb-1 lh-lg d-flex px-2"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button type="button" class="btn conv-blue-bg m-auto text-white" data-bs-dismiss="modal">Done!</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="conv_save_success_modal_cta" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="connection-box">
                    <div class="items">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/logos/popup_woocommerce_logo.png', '', '', 'width:35px;'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>;
                        <span> <?php esc_html_e("Woo Commerce", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                    </div>
                    <div class="items">
                        <span class="material-symbols-outlined text-primary">
                            arrow_forward
                        </span>
                    </div>
                    <div class="items">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/logos/ms_channel_logo.svg', '', '', 'width:35px;'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>;
                        <span><?php esc_html_e("Microsoft Merchant Center", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                    </div>
                </div>

            </div>
            <div class="modal-body text-center p-4">
                <div class="connected-content">
                    <h4><?php esc_html_e("Saved Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?></h4>
                    <p><span class="fw-bolder">Microsoft Merchant Center Store -</span> <span class="mmcAccount fw-bolder"></span>
                        Has Been Saved Successfully</p>
                    <p class="my-3"><?php esc_html_e("By this step you have expanded your product presence on Microsoft Search, Microsoft
                        Shopping,
                        Microsoft Images, YouTube, Microsoft Maps, and more, you're maximizing your reach and unlocking new
                        potential for increased visibility and sales.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                </div>
                <div>
                    <div class="attributemapping-box">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                                <div class="attribute-box mb-3">
                                    <div class="attribute-icon">
                                        <?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/Manage_feed.png', '', '', 'width:35px;'),
                                            array(
                                                'img' => array(
                                                    'src' => true,
                                                    'alt' => true,
                                                    'class' => true,
                                                    'style' => true,
                                                ),
                                            )
                                        ); ?>;
                                    </div>
                                    <div class="attribute-content para">
                                        <h3><?php esc_html_e("Manage Feeds", "enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                                        <span class="fs-14"><?php esc_html_e("Create Feed to start Syncing your products to your linked Feed channel.", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                                        <p>
                                            <?php esc_html_e("A feed management tool centralizes updates, optimizes listings, and boosts data quality, streamlining product feed management for better efficiency and effectiveness.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </p>
                                        <div class="attribute-btn">
                                            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&tab=feed_list&createfeed=yes'); ?>" class="btn btn-primary common-bt">Create Feed</a>
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
<?php
$google_merchant_center_id = '';
if (isset($googleDetail->google_merchant_center_id) === TRUE && $googleDetail->google_merchant_center_id !== '') {
    $google_merchant_center_id = esc_html($googleDetail->google_merchant_center_id);
}

$tiktok_business_account = '';
if (isset($googleDetail->tiktok_setting->tiktok_business_id) === TRUE && $googleDetail->tiktok_setting->tiktok_business_id !== '') {
    $tiktok_business_account = $googleDetail->tiktok_setting->tiktok_business_id;
}
$facebook_business_account = '';
$fb_catalog_id = '';
if (isset($googleDetail->facebook_setting->fb_business_id) === TRUE && $googleDetail->facebook_setting->fb_business_id !== '') {
    $facebook_business_account = $googleDetail->facebook_setting->fb_business_id;
    $fb_catalog_id = $googleDetail->facebook_setting->fb_catalog_id;
}
?>
<!-------------------------CTA super_feed_modal Start ---------------------------------->
<div class="modal fade" id="conv_super_feed_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 99999">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header justify-content-end connection-header border-0 pb-0">
                <button type="button" style="margin: 0px; padding:0px;" class="btn-close close_feed_modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="connected-content">
                    <h3>
                        <?php esc_html_e("Congratulations!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h3>
                    <p class="my-3 syncSuccessMessage" style="font-size: 20px;">

                    </p>
                    <p><?php esc_html_e("And that's not all.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    <h4 class="mb-3">
                        <?php esc_html_e("Embrace these amazing features:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h4>
                </div>
                <div>
                    <div class="attributemapping-box">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-6">
                                <div class="attribute-box mb-3">
                                    <div class="attribute-icon">
                                        <?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/Campaign-Management.svg', '', '', 'width:35px;filter: drop-shadow(3px 3px 3px #ccc);'),
                                            array(
                                                'img' => array(
                                                    'src' => true,
                                                    'alt' => true,
                                                    'class' => true,
                                                    'style' => true,
                                                ),
                                            )
                                        ); ?>;
                                    </div>
                                    <div class="attribute-content para">
                                        <h3>
                                            <?php esc_html_e("Effortless Feed Management:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </h3>
                                        <p>
                                            <?php esc_html_e("Generate custom feeds, apply filters, and ensure up-to-date product data with auto-sync for targeted regions and successful promotions.", "enhanced-e-commerce-for-woocommerce-store"); ?>

                                        </p>
                                        <div class="attribute-btn">
                                            <a href="<?php echo esc_url('admin.php?page=conversios-google-shopping-feed&tab=feed_list'); ?>" class="btn btn-dark">Manage Feeds</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-6">
                                <div class="attribute-box mb-3">
                                    <div class="attribute-icon">
                                        <?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/Integrations.svg', '', '', 'width:35px;filter: drop-shadow(3px 3px 3px #ccc);'),
                                            array(
                                                'img' => array(
                                                    'src' => true,
                                                    'alt' => true,
                                                    'class' => true,
                                                    'style' => true,
                                                ),
                                            )
                                        ); ?>;
                                    </div>
                                    <div class="attribute-content para">
                                        <h3>
                                            <?php esc_html_e("Seamless Integration:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </h3>
                                        <p>
                                            <?php esc_html_e("Connect more channels to sync your product data. Map your WooCommerce product attributes and categories for an optimized product feed process.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </p>
                                        <div class="attribute-btn">
                                            <a href="<?php echo esc_url('admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page'); ?>" class="btn btn-dark">Configure Now</a>
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
<!--------------------------------super_feed_modal End -------------------------------------->
<script>
    var get_sub = "<?php echo isset($_GET['subscription_id']) && $_GET['subscription_id'] !== '' ? esc_html(sanitize_text_field(wp_unslash($_GET['subscription_id']))) : '' ?>";
    var mmc_id = "<?php echo esc_html($microsoft_merchant_center_id) ?>";
    let subscription_id = "<?php echo esc_attr($subscriptionId); ?>";

    jQuery(document).on('select2:select', '#microsoft_merchant_center_id', function(e) {
        if (jQuery(this).val() != "" && jQuery(this).val() != undefined) {
            conv_change_loadingbar("show");
            list_microsoft_catalog_account();
        }
    });
    /**
     * Get Microsoft Merchant Center List
     */
    function list_microsoft_merchant_account(tvc_data, selelement, new_mmc_id = "", new_merchant_id = "") {
        conv_change_loadingbar("show");
        jQuery(".conv-enable-selection").addClass('hidden');
        var selectedValue = '0';
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "list_microsoft_merchant_account",
                tvc_data: tvc_data,
                account_id: '<?php echo esc_js($microsoft_ads_manager_id); ?>',
                subaccount_id: '<?php echo esc_js($microsoft_ads_subaccount_id); ?>',
                subscription_id: subscription_id,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                var btn_cam = 'mmc_list';
                if (response.error === false) {
                    var error_msg = 'null';
                    jQuery('#microsoft_merchant_center_id').empty();
                    // jQuery('#microsoft_merchant_center_id').append(jQuery('<option>', {
                    //     value: "",
                    //     text: "Select Microsoft Merchant Center Store"
                    // }));
                    if (response.data.length > 0) {
                        jQuery.each(response.data, function(key, value) {

                            jQuery('#microsoft_merchant_center_id').append(jQuery('<option>', {
                                value: value.merchantId,
                                "data-merchant_id": value.merchantId,
                                text: value.storeName + ' (' + value.merchantId + ')',
                                selected: "selected"
                            }));

                        });
                        jQuery(".conv-mc-settings").removeClass("disabledsection");
                        list_microsoft_catalog_account();

                        //jQuery('#tvc-mmc-acc-edit').hide();
                    } else {
                        if (new_mmc_id != "" && new_mmc_id != undefined) {
                            jQuery('#microsoft_merchant_center_id').append(jQuery('<option>', {
                                value: new_mmc_id,
                                "data-merchant_id": new_mmc_id,
                                text: storeName + ' (' + new_mmc_id + ')',
                                selected: "selected"
                            }));
                            jQuery(".conv-mc-settings").removeClass("disabledsection");
                            list_microsoft_catalog_account()

                        } else {
                            jQuery(".conv_create_mmc_new_when_notexist").removeClass("disabledsection");
                            getAlertMessageAll(
                                'info',
                                'Error',
                                message = 'There are no Microsoft merchant center stores associated with email.',
                                icon = 'info',
                                buttonText = 'Ok',
                                buttonColor = '#FCCB1E',
                                iconImageSrc = '<?php echo wp_kses(
                                                    enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png', '', '', ''),
                                                    array(
                                                        'img' => array(
                                                            'src' => true,
                                                            'alt' => true,
                                                            'class' => true,
                                                            'style' => true,
                                                        ),
                                                    )
                                                ); ?>'
                            );
                            console.log("error", "There are no Microsoft merchant center stores associated with email.");
                        }
                    }

                } else {
                    var error_msg = response.errors;
                    console.log("error", error_msg);
                    //add_message("error", "There are no Microsoft merchant center stores associated with email.");
                    // console.log("error",
                    //     "There are no Microsoft merchant center  stores associated with email.");
                }
                jQuery('#microsoft_merchant_center_id').select2();
                setTimeout(function() {}, 2000);
                jQuery('#microsoft_merchant_center_id').prop('disabled', false);
                conv_change_loadingbar("hide");
            }
        });
    }

    /**
     * Get Microsoft Merchant Center List
     */
    function list_microsoft_catalog_account() {
        conv_change_loadingbar("show");
        jQuery(".conv-enable-catalog-selection").addClass('hidden');
        var selectedValue = '0';
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "list_microsoft_catalog_account",
                account_id: '<?php echo esc_js($microsoft_ads_manager_id); ?>',
                subaccount_id: '<?php echo esc_js($microsoft_ads_subaccount_id); ?>',
                microsoft_merchant_center_id: jQuery('#microsoft_merchant_center_id').val(),
                subscription_id: subscription_id,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {

                if (response.error === false) {
                    var error_msg = 'null';
                    jQuery('#ms_catalog_id').empty();
                    // jQuery('#ms_catalog_id').append(jQuery('<option>', {
                    //     value: "",
                    //     text: "Select Microsoft Merchant Catalog Id"
                    // }));
                    if (response.data.length > 0) {
                        jQuery.each(response.data, function(key, value) {

                            jQuery('#ms_catalog_id').append(jQuery('<option>', {
                                value: value.id,
                                "data-merchant_id": value.id,
                                text: value.name + ' (' + value.id + ')',
                                selected: "selected"
                            }));

                        });

                        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
                        jQuery(".conv-btn-connect").removeClass("conv-btn-disconnect");
                        jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-mmc");

                        //jQuery('#tvc-mmc-acc-edit').hide();
                    } else {

                        getAlertMessageAll(
                            'info',
                            'Error',
                            message = 'There are no Microsoft merchant catalog associated with email.',
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc = '<?php echo wp_kses(
                                                enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png', '', '', ''),
                                                array(
                                                    'img' => array(
                                                        'src' => true,
                                                        'alt' => true,
                                                        'class' => true,
                                                        'style' => true,
                                                    ),
                                                )
                                            ); ?>'
                        );
                        console.log("error", "There are no Microsoft merchant catalog associated with email.");

                    }

                } else {
                    var error_msg = response.errors;
                    //add_message("error", "There are no Microsoft merchant center stores associated with email.");
                    // console.log("error",
                    //     "There are no Microsoft merchant center  stores associated with email.");
                }
                jQuery('#ms_catalog_id').select2();
                setTimeout(function() {}, 2000);
                jQuery('#ms_catalog_id').prop('disabled', false);
                conv_change_loadingbar("hide");
            }
        });
    }

    function save_merchant_data(microsoft_merchant_center_id, merchant_id, tvc_data, subscription_id, plan_id, is_skip =
        fals) {
        if (microsoft_merchant_center_id || is_skip == true) {
            var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
            var website_url = "<?php echo esc_url(site_url()); ?>";
            var customer_id = "<?php echo esc_html($googleDetail->customer_id); ?>";
            let microsoft_ads_pixel_id = jQuery('#ads-account').val();
            var data = {
                action: "save_merchant_data",
                subscription_id: subscription_id,
                google_merchant_center: microsoft_merchant_center_id,
                store_id: microsoft_merchant_center_id,
                merchant_id: merchant_id,
                website_url: website_url,
                customer_id: customer_id,
                tvc_data: tvc_data,
                adwords_id: microsoft_ads_pixel_id,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            };
            return jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {},
                success: function(response) {

                }
            });
        } else {
            //add_message("warning", "Missing Microsoft Merchant Center store.");
        }
    }

    function call_site_verified() {
        conv_change_loadingbar("show");
        jQuery("#wpbody").css("pointer-events", "none");
        jQuery.post(tvc_ajax_url, {
            action: "tvc_call_site_verified",
            SiteVerifiedNonce: "<?php echo esc_js(wp_create_nonce('tvc_call_site_verified-nonce')); ?>"
        }, function(response) {
            conv_change_loadingbar("hide");
            jQuery("#wpbody").css("pointer-events", "auto");
            var rsp = JSON.parse(response);
            if (rsp.status == "success") {
                jQuery(".created_success").html('Updated Successfully');
                jQuery("#conv_save_success_txt_").html(rsp.message);
                jQuery("#conv_save_success_modal_").modal("show");
                location.reload();
            } else {
                jQuery("#conv_save_error_txt").html(rsp.message);
                jQuery("#conv_save_error_modal").modal("show");
            }
            user_tracking_data('refresh_call', 'null', 'product-feed-manager-for-woocommerce',
                'call_site_verified');
        });
    }

    function call_domain_claim() {
        conv_change_loadingbar("show");
        jQuery("#wpbody").css("pointer-events", "none");
        jQuery.post(tvc_ajax_url, {
            action: "tvc_call_domain_claim",
            apiDomainClaimNonce: "<?php echo esc_js(wp_create_nonce('tvc_call_domain_claim-nonce')); ?>"
        }, function(response) {
            conv_change_loadingbar("hide");
            jQuery("#wpbody").css("pointer-events", "auto");
            var rsp = JSON.parse(response);
            if (rsp.status == "success") {
                jQuery(".created_success").html('Updated Successfully');
                jQuery("#conv_save_success_txt_").html(rsp.message);
                jQuery("#conv_save_success_modal_").modal("show");
                location.reload();
            } else {
                jQuery("#conv_save_error_txt").html(rsp.message);
                jQuery("#conv_save_error_modal").modal("show");
            }
            user_tracking_data('refresh_call', 'null', 'product-feed-manager-for-woocommerce', 'call_domain_claim');
        });
    }
    //Onload functions
    jQuery(function() {
        jQuery(".navinfotopnav ul li").removeClass('active');
        jQuery(".navinfotopnav ul li:nth-child(3)").addClass('active');
        jQuery(".navinfotopnav ul li:nth-child(2) img").css('filter', 'grayscale(100%)');

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        jQuery('#microsoft_merchant_center_id').select2();
        //override back button link to GMC Channel Configuration 
        jQuery('.hreflink').attr('href', 'admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page');

        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
        var tvc_ajax_url = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        let subscription_id = "<?php echo esc_attr($subscriptionId); ?>";
        let plan_id = "<?php echo esc_attr($plan_id); ?>";
        let app_id = "<?php echo esc_attr(CONV_APP_ID); ?>";
        let bagdeVal = "yes";
        let convBadgeVal = "<?php echo esc_attr($convBadgeVal); ?>";
        let microsoft_merchant_center_id = "<?php echo esc_attr($microsoft_merchant_center_id); ?>";


        jQuery(document).on('show.bs.modal', '#conv_create_new_bing', function() {
            jQuery.fn.modal.Constructor.prototype.enforceFocus = function() {};
            jQuery.fn.modal.Constructor.prototype._enforceFocus = function() {};

            jQuery(".selectthree").select2({
                minimumResultsForSearch: 5,
                dropdownParent: jQuery('#conv_create_mmc_selectthree'),
                placeholder: function() {
                    jQuery(this).data('placeholder');
                }
            });
        })


        jQuery(".conv-enable-selection").click(function() {
            conv_change_loadingbar("show");
            jQuery(".conv-enable-selection").addClass('hidden');

            var selele = jQuery(".conv-enable-selection").closest(".conv-mmcsettings").find(
                "select.microsoft_merchant_center_id");

            list_microsoft_merchant_account(tvc_data, selele);
        });

        jQuery(".conv-enable-catalog-selection").click(function() {
            conv_change_loadingbar("show");
            jQuery(".conv-enable-catalog-selection").addClass('hidden');

            var selele = jQuery(".conv-enable-catalog-selection").closest(".conv-mc-settings").find(
                "select.microsoft_merchant_center_id");

            list_microsoft_catalog_account(tvc_data, selele);
        });


        jQuery(document).on("change", "form#mmcsetings_form", function() {
            <?php if ($cust_ms_email !== "") { ?>
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
                jQuery(".conv-btn-connect").removeClass("conv-btn-disconnect");
                jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-mmc");
                jQuery(".conv-btn-connect").addClass("btn-primary");
                jQuery(".conv-btn-connect").text('Save');
            <?php } else { ?>
                jQuery(".tvc_google_signinbtn").trigger("click");
            <?php } ?>
        });

        <?php if ($cust_ms_email === "") { ?>
            jQuery("#conv_create_mmc_new_btn").addClass("disabled");
            jQuery(".conv-enable-selection").addClass("d-none");
        <?php } ?>

        <?php if ((isset($_GET['subscription_id']) === TRUE && esc_attr(sanitize_text_field(wp_unslash($_GET['subscription_id']))) !== '') ||
            (empty($microsoft_merchant_center_id) &&
                !empty($cust_ms_email) &&
                !empty($microsoft_ads_manager_id) &&
                !empty($microsoft_ads_subaccount_id) &&
                !empty($microsoft_ads_pixel_id))
        ) { ?>

            list_microsoft_merchant_account(tvc_data);
        <?php } ?>

        //Save GMC id
        jQuery(document).on("click", ".conv-btn-connect-enabled-mmc", function() {
            var feedType = jQuery('#feedType').val();
            var valtoshow_inpopup = jQuery("#valtoshow_inpopup").val() + " " + jQuery(
                ".valtoshow_inpopup_this").val();
            var selected_vals = {};
            selected_vals["subscription_id"] = "<?php echo esc_html($tvc_data['subscription_id']) ?>";

            jQuery('form#mmcsetings_form select').each(function() {
                selected_vals[jQuery(this).attr("name")] = jQuery(this).val();
            });
            //var merchant_idd = jQuery('#microsoft_merchant_center_id').find(':selected').data('merchant_id');
            selected_vals["ms_catalog_id"] = jQuery("#ms_catalog_id").val();
            selected_vals["microsoft_merchant_center_id"] = jQuery("#microsoft_merchant_center_id").val();
            //////selected_vals["merchant_id"] = merchant_idd;
            selected_vals["website_url"] = "<?php echo esc_url(get_site_url()); ?>";
            let microsoft_ads_pixel_id = jQuery('#ads-account').val();
            if (microsoft_ads_pixel_id !== '') {
                selected_vals["ga_GMC"] = 1;
                selected_vals["microsoft_ads_pixel_id"] = microsoft_ads_pixel_id;
            }
            console.log("selected_vals", selected_vals);
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: {
                    action: "conv_save_pixel_data",
                    pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                    conv_options_data: selected_vals,
                    conv_options_type: ["eeoptions", "eeapidata", "middleware"],
                },
                beforeSend: function() {
                    conv_change_loadingbar("show");
                    jQuery(".conv-btn-connect-enabled-mmc").text("Saving...");
                    jQuery(".conv-btn-connect-enabled-mmc").addClass('disabled');
                },
                success: function(response) {
                    var user_modal_txt =
                        "Congratulations, you have successfully connected your <br>" +
                        valtoshow_inpopup;
                    if (response == "0" || response == "1") {
                        let microsoft_merchant_center_id = jQuery('#microsoft_merchant_center_id').val();
                        let merchant_id = jQuery('#microsoft_merchant_center_id').find(':selected').data('merchant_id');
                        // save_merchant_data(microsoft_merchant_center_id, merchant_id, tvc_data, subscription_id, plan_id, true).then((res) => {
                        // if (feedType !== '') {
                        //    // createSuperAIFeed();
                        // } else {
                        conv_change_loadingbar("hide");
                        jQuery(".conv-btn-connect-enabled-mmc").text("Save");
                        jQuery(".conv-btn-connect-enabled-mmc").removeClass('disabled');
                        jQuery('.mmcAccount').html(selected_vals["ms_catalog_id"])
                        jQuery("#conv_save_success_modal_cta").modal("show");
                        // }
                        // });
                    }
                }

            });
        });

        jQuery("#conv_form_new_bing").on('submit', function(e) {
            e.preventDefault();

            var store_name = jQuery("#store_name").val();
            var store_url = jQuery("#store_url").val();
            var notification_email = jQuery("#notification_email").val();
            var notification_language = jQuery("#mmnotification_languagec_country").val();

            var data = {
                action: "create_microsoft_merchant_center_account",
                store_name: store_name,
                store_url: store_url,
                notification_email: notification_email,
                notification_language: notification_language,
                account_id: '<?php echo esc_js($microsoft_ads_manager_id); ?>',
                subaccount_id: '<?php echo esc_js($microsoft_ads_subaccount_id); ?>',
                subscription_id: subscription_id,
                //tvc_data: tvc_data,
                conversios_onboarding_nonce: "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>"
            };

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {
                    jQuery('#gadsinviteloader').removeClass('d-none');
                },
                success: function(response, status) { //console.log(response);
                    jQuery('#model_close_mmc_creation, .closeButton').removeClass('disabled')
                    if (response.error === true) {
                        jQuery("#conv_create_new_bing").modal('hide');

                        var error_msg = "There was error to create merchant center store:" + response.errors;

                        getAlertMessageAll(
                            'info',
                            'Error',
                            message = error_msg,
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc = '<?php echo wp_kses(
                                                enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png', '', '', ''),
                                                array(
                                                    'img' => array(
                                                        'src' => true,
                                                        'alt' => true,
                                                        'class' => true,
                                                        'style' => true,
                                                    ),
                                                )
                                            ); ?>'
                        );

                    } else {
                        jQuery("#conv_create_new_bing").modal('hide');

                        jQuery("#new_mmc_id").text(response.data.merchantId);
                        jQuery(".before-mmc-acc-creation").addClass("d-none");
                        jQuery(".after-mmc-acc-creation").removeClass("d-none");
                        jQuery("#model_close_mmc_creation").text("Created, close now");
                        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
                        list_microsoft_merchant_account(tvc_data, "", response.data.merchantId, response.data.storeName);
                    }

                },
                complete: function() {
                    jQuery('#gadsinviteloader').addClass('d-none');
                }
            });

        });

        /****************Submit Feed call end***********************************/
        jQuery(document).on('click', '#gmc_id, #tiktok_id, #fb_id, #mmc_id', function(e) {
            jQuery('.errorChannel').css('border', '');
        });
    });

    /*************************************Save Feed Data End***************************************************************************/
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
    /*************************Create Super AI Feed Start ************************************************************************/
    function createSuperAIFeed() {
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "ee_super_AI_feed",
                create_superFeed_nonce: "<?php echo esc_js(wp_create_nonce('create_superFeed_nonce_val')); ?>",
                type: 'GMC',
            },
            success: function(response) {
                conv_change_loadingbar("hide");
                if (response.status == 'success') {
                    jQuery('.syncSuccessMessage').html('Your latest ' + response.total_product + ' products are synced to your Microsoft Merchant Center store.')
                    jQuery("#conv_super_feed_modal").modal("show");
                }
            },
            error: function(error) {

            }
        });
    }
    /*************************Create Super AI Feed End ***************************************************************************/
    jQuery(document).on('click', '.close_feed_modal', function() {
        jQuery("#conv_super_feed_modal").modal("hide");
        location.reload();
    })
    /*************************************************************************************************************************** */
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
</script>