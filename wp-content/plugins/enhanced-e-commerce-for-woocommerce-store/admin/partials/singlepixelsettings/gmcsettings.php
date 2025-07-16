<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

$is_sel_disable = 'disabled';
$google_merchant_center_id = "";
if (isset($googleDetail->google_merchant_center_id) === TRUE && $googleDetail->google_merchant_center_id !== "") {
    $google_merchant_center_id = $googleDetail->google_merchant_center_id;
}

$microsoft_merchant_center_id = "";
if (isset($googleDetail->microsoft_merchant_center_id) === TRUE && $googleDetail->microsoft_merchant_center_id !== "") {
    $microsoft_merchant_center_id = $googleDetail->microsoft_merchant_center_id;
}

$google_ads_id = "";
if (isset($googleDetail->google_ads_id) === TRUE && $googleDetail->google_ads_id !== "") {
    $google_ads_id = $googleDetail->google_ads_id;
}

$cust_g_email = "";
if (isset($tvc_data['g_mail']) === TRUE && esc_attr($subscriptionId) !== '') {
    $cust_g_email = esc_attr($tvc_data['g_mail']);
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
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
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
    $connect_url = $TVC_Admin_Helper->get_custom_connect_url_subpage(admin_url() . 'admin.php?page=conversios-google-shopping-feed', "gmcsettings");
    require_once "googlesignin.php";
    ?>

    <form id="gmcsetings_form" class="convpixsetting-inner-box mt-4">
        <div id="analytics_box_UA" class="py-1">
            <label class="text-dark fw-bold-500">
                <?php esc_html_e("Select Google Merchant Center Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </label>
            <div class="row pt-2 conv-gmcsettings">
                <div class="col-6">
                    <select id="google_merchant_center_id" name="google_merchant_center_id" class="form-select form-select-lg mb-3 selecttwo valtoshow_inpopup_this" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                        <?php if (!empty($google_merchant_center_id)) { ?>
                            <option value="<?php echo esc_attr($google_merchant_center_id); ?>" selected>
                                <?php echo esc_attr($google_merchant_center_id); ?>
                            </option>
                        <?php } ?>
                        <option value="">Select Google Merchant Center Account</option>
                    </select>
                </div>
                <div class="col-1 btn btn-primary conv-enable-selection">
                    <label class="fs-6 text"><?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                </div>
            </div>
            <div class="col-12 flex-row pt-3">
                <div class="col-12 py-2">
                    <label>Do not have an account?</label>
                    <a id="conv_create_gmc_new_btn" class="" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#conv_create_gmc_new">
                        <?php esc_html_e("Create New", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </a>
                </div>
            </div>
            <div class="col-12 flex-row pt-3 row">
                <div class="col-5">
                    <label class="text-dark">Site Verified (Click red icon to verify now)</label>
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
                        <span class="material-symbols-outlined text-danger fs-3 site_verified" onclick="call_site_verified()" style="cursor:pointer">
                            sync_problem
                        </span>
                    <?php }
                    ?>
                </div>
            </div>
            <div class="col-12 flex-row pt-3 row domain_claimDiv">
                <div class="col-5">
                    <label class="text-dark">Domain Claim (Click red icon to claim now)</label>
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
                        <span class="material-symbols-outlined text-danger fs-3 domain_claim" onclick="call_domain_claim()" style="cursor:pointer">
                            sync_problem
                        </span>
                    <?php }
                    ?>
                </div>
            </div>


        </div>
    </form>

    <input type="hidden" id="valtoshow_inpopup" value="Google Merchant Center Account:" />
    <input type="hidden" id="ads-account" value="<?php echo esc_attr($google_ads_id); ?>" />
    <input type="hidden" id="conversios_onboarding_nonce" value="<?php echo esc_attr(wp_create_nonce('conversios_onboarding_nonce')); ?>" />
    <input type="hidden" id="feedType" name="feedType" value="<?php echo isset($_GET['feedType']) && $_GET['feedType'] != '' ? esc_attr(sanitize_text_field(wp_unslash($_GET['feedType']))) : '' ?>" />

</div>


<!-- Create New Ads Account Modal -->
<div class="modal fade" id="conv_create_gmc_new" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body text-start">
                <div class="row">
                    <div class="col-7 pe-4">
                        <div id="before_gadsacccreated_text" class="mb-1 fs-6 before-gmc-acc-creation">
                            <h5 class="modal-title my-3" id="staticBackdropLabel">
                                <span id="before_gadsacccreated_title">
                                    <?php esc_html_e("Create New Google Merchant Center Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                                <span id="after_gadsacccreated_title" class="d-none after-ads-acc-creation">
                                    <?php esc_html_e("New Google Merchant Center Account Created", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </span>
                            </h5>
                            <div class="alert d-flex align-items-cente p-0" role="alert">
                                <div class="text-light conv-info-bg rounded-start d-flex">
                                    <span class="p-2 material-symbols-outlined align-self-center">info</span>
                                </div>

                                <div class="p-2 w-100 rounded-end border border-start-0 shadow-sm conv-notification-alert bg-white">
                                    <span>
                                        <?php esc_html_e("To upload your product data, it is necessary to go through a process of verifying and claiming your store's website URL. This step of claiming your website URL links it with your Google Merchant Center Account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </span>
                                </div>
                            </div>
                            <div id="create_gmc_error" class="alert alert-danger d-none" role="alert">
                                <small></small>
                            </div>
                            <form id="conv_form_new_gmc">
                                <div class="mb-3">
                                    <input class="form-control mb-4" type="text" id="gmc_website_url" name="website_url" value="<?php echo esc_attr($tvc_data['user_domain']); ?>" placeholder="Enter Website" required>

                                    <input class="form-control mb-4" type="text" id="gmc_email_address" name="email_address" value="<?php echo isset($tvc_data['g_mail']) === TRUE ? esc_attr($tvc_data['g_mail']) : ""; ?>" placeholder="Enter email address" required>

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="gmc_adult_content" name="adult_content" value="1" style="float:none">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            <?php esc_html_e("My site contain", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            <b>
                                                <?php esc_html_e("Adult Content", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </b>
                                        </label>
                                    </div>

                                    <input class="form-control mb-0" type="text" id="gmc_store_name" name="store_name" value="" placeholder="Enter Store Name" required>
                                    <small class="mb-4">
                                        <?php esc_html_e("This name will appear in your Shopping Ads.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </small>

                                    <div class="mb-3" id="conv_create_gmc_selectthree">
                                        <select id="gmc_country" name="country" class="form-select form-select-lg mb-3 selectthree" style="width: 100%" placeholder="Select Country" required>
                                            <option value="">Select Country</option>
                                            <?php
                                            //$getCountris = file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");
                                            global $wp_filesystem;
                                            $getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");

                                            $contData = json_decode($getCountris);
                                            foreach ($contData as $key => $value) {
                                            ?>
                                                <option value="<?php echo esc_attr($value->code) ?>" <?php echo $tvc_data['user_country'] === $value->code ? 'selected = "selecetd"' :
                                                                                                            '' ?>>
                                                    <?php echo esc_attr($value->name) ?>
                                                </option>"
                                            <?php
                                            }

                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-check mb-4">
                                        <input id="gmc_concent" name="concent" class="form-check-input" type="checkbox" value="1" required style="float:none">
                                        <label class="form-check-label" for="concent">
                                            <?php esc_html_e("I accept the", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            <a target="_blank" href="<?php echo esc_url("
                                                https://support.google.com/merchants/answer/160173?hl=en"); ?>">
                                                <?php esc_html_e("terms & conditions", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </a>
                                        </label>
                                    </div>

                                </div>

                            </form>
                        </div>

                        <!-- Show this after creation -->
                        <div class="onbrdpp-body alert alert-primary text-start d-none after-gmc-acc-creation">
                            New Google Merchant Center Account With Id: <span id="new_gmc_id"></span> is created
                            successfully.
                        </div>


                    </div>
                    <div class="col-5 ps-4 border-start">
                        <div>
                            <h6>
                                <?php esc_html_e("To use Google Shopping, your website must meet these requirements:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </h6>
                            <ul class="p-0">
                                <li><a target="_blank" href="<?php echo esc_url("
                                        https://support.google.com/merchants/answer/6149970?hl=en"); ?>">
                                        <?php esc_html_e("Google Shopping ads policies", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </a>
                                </li>
                                <li><a target="_blank" href="<?php echo esc_url("
                                        https://support.google.com/merchants/answer/6150127"); ?>">
                                        <?php esc_html_e("Accurate Contact Information", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </a>
                                </li>
                                <li><a target="_blank" href="<?php echo esc_url("
                                        https://support.google.com/merchants/answer/6150122"); ?>">
                                        <?php esc_html_e("Secure collection of process and personal data", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </a>
                                </li>
                                <li><a target="_blank" href="<?php echo esc_url("
                                        https://support.google.com/merchants/answer/6150127"); ?>">
                                        <?php esc_html_e("Return Policy", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </a>
                                </li>
                                <li><a target="_blank" href="<?php echo esc_url("
                                        https://support.google.com/merchants/answer/6150127"); ?>">
                                        <?php esc_html_e("Billing terms & conditions", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </a>
                                </li>
                                <li><a target="_blank" href="<?php echo esc_url("
                                        https://support.google.com/merchants/answer/6150118"); ?>">
                                        <?php esc_html_e("Complete checkout process", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <div class="me-auto">
                    <button id="create_merchant_account_new" class="btn conv-blue-bg text-white before-gmc-acc-creation me-auto">
                        <span id="gadsinviteloader" class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
                        <?php esc_html_e("Create", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>

                    <button type="button" class="ms-3 btn btn-secondary me-auto" data-bs-dismiss="modal" id="model_close_gmc_creation">
                        <?php esc_html_e("Close", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                </div>
            </div>
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
                ); ?>
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
                ); ?>
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
                        ); ?>
                        <span> <?php esc_html_e("Woo Commerce", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                    </div>
                    <div class="items">
                        <span class="material-symbols-outlined text-primary">
                            arrow_forward
                        </span>
                    </div>
                    <div class="items">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/logos/popup_gmc_logo.png', '', '', 'width:35px;'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                        <span><?php esc_html_e("Google Merchant Center", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                    </div>
                </div>

            </div>
            <div class="modal-body text-center p-4">
                <div class="connected-content">
                    <h4><?php esc_html_e("Saved Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?></h4>
                    <p><span class="fw-bolder">Google Merchant Center Account -</span> <span class="gmcAccount fw-bolder"></span>
                        Has Been Saved Successfully</p>
                    <p class="my-3"><?php esc_html_e("By this step you have expanded your product presence on Google Search, Google
                        Shopping,
                        Google Images, YouTube, Google Maps, and more, you're maximizing your reach and unlocking new
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
                                        ); ?>
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
                            <span>
                                <a href="<?php echo esc_url('admin.php?page=conversios-google-shopping-feed&subpage=metasettings'); ?>">Connect
                                    to Facebook Business Account</a>
                            </span>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
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
                                        ); ?>
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
                                        ); ?>
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
    var gmc_id = "<?php echo esc_html($google_merchant_center_id) ?>";
    var mmc_id = "<?php echo esc_html($microsoft_merchant_center_id) ?>";

    /**
     * Get Google Merchant Center List
     */
    function list_google_merchant_account(tvc_data, selelement, new_gmc_id = "", new_merchant_id = "") {
        conv_change_loadingbar("show");
        jQuery(".conv-enable-selection").addClass('hidden');
        var selectedValue = '0';
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "list_google_merchant_account",
                tvc_data: tvc_data,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                var btn_cam = 'gmc_list';
                if (response.error === false) {
                    var error_msg = 'null';
                    jQuery('#google_merchant_center_id').empty();
                    // jQuery('#google_merchant_center_id').append(jQuery('<option>', {
                    //     value: "",
                    //     text: "Select Google Merchant Center Account"
                    // }));
                    if (response.data.length > 0) {
                        jQuery.each(response.data, function(key, value) {
                            if (selectedValue == value.account_id) {
                                jQuery('#google_merchant_center_id').append(jQuery('<option>', {
                                    value: value.account_id,
                                    "data-merchant_id": value.merchant_id,
                                    text: value.account_id,
                                    selected: "selected"
                                }));
                            } else {
                                if (selectedValue == "" && key == 0) {
                                    jQuery('#google_merchant_center_id').append(jQuery('<option>', {
                                        value: value.account_id,
                                        "data-merchant_id": value.merchant_id,
                                        text: value.account_id,
                                        selected: "selected"
                                    }));
                                } else {
                                    jQuery('#google_merchant_center_id').append(jQuery('<option>', {
                                        value: value.account_id,
                                        "data-merchant_id": value.merchant_id,
                                        text: value.account_id,
                                    }));
                                }
                            }
                        });

                        if (new_gmc_id != "" && new_gmc_id != undefined) {
                            jQuery('#google_merchant_center_id').append(jQuery('<option>', {
                                value: new_gmc_id,
                                "data-merchant_id": new_merchant_id,
                                text: new_gmc_id,
                                selected: "selected"
                            }));

                            jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
                            jQuery(".conv-btn-connect").removeClass("conv-btn-disconnect");
                            jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-gmc");
                        }

                        jQuery('#tvc-gmc-acc-edit').hide();
                    } else {
                        if (new_gmc_id != "" && new_gmc_id != undefined) {
                            jQuery('#google_merchant_center_id').append(jQuery('<option>', {
                                value: new_gmc_id,
                                "data-merchant_id": new_merchant_id,
                                text: new_gmc_id,
                                selected: "selected"
                            }));
                        }
                        //add_message("error", "There are no Google merchant center accounts associated with email.");
                        // console.log("error",
                        //     "There are no Google merchant center accounts associated with email.");
                    }

                } else {
                    var error_msg = response.errors;
                    //add_message("error", "There are no Google merchant center accounts associated with email.");
                    // console.log("error",
                    //     "There are no Google merchant center  accounts associated with email.");
                }
                jQuery('#google_merchant_center_id').select2();
                setTimeout(function() {}, 2000);
                jQuery('#google_merchant_center_id').prop('disabled', false);
                conv_change_loadingbar("hide");
                jQuery("#google_merchant_center_id").trigger('change');
            }
        });
    }

    function link_google_Ads_to_merchant_center(link_data, tvc_data, subscription_id) {
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: link_data,
            beforeSend: function() {},
            success: function(response) {
                if (response.error === false) {} else if (response.error == true && response.errors != undefined) {} else {
                    // console.log("error", "There was an error while link account");
                }
            }
        });
    }

    function save_merchant_data(google_merchant_center_id, merchant_id, tvc_data, subscription_id, plan_id, is_skip =
        fals) {
        if (google_merchant_center_id || is_skip == true) {
            var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
            var website_url = "<?php echo esc_url(site_url()); ?>";
            var customer_id = "<?php echo esc_html($googleDetail->customer_id); ?>";
            let google_ads_id = jQuery('#ads-account').val();
            var data = {
                action: "save_merchant_data",
                subscription_id: subscription_id,
                google_merchant_center: google_merchant_center_id,
                account_id: google_merchant_center_id,
                merchant_id: merchant_id,
                website_url: website_url,
                customer_id: customer_id,
                tvc_data: tvc_data,
                adwords_id: google_ads_id,
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
            //add_message("warning", "Missing Google Merchant Center account.");
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
        jQuery('#google_merchant_center_id').select2();
        //override back button link to GMC Channel Configuration 
        jQuery('.hreflink').attr('href', 'admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page');

        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
        var tvc_ajax_url = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        let subscription_id = "<?php echo esc_attr($subscriptionId); ?>";
        let plan_id = "<?php echo esc_attr($plan_id); ?>";
        let app_id = "<?php echo esc_attr(CONV_APP_ID); ?>";
        let bagdeVal = "yes";
        let convBadgeVal = "<?php echo esc_attr($convBadgeVal); ?>";
        let google_merchant_center_id = "<?php echo esc_attr($google_merchant_center_id); ?>";


        jQuery(document).on('show.bs.modal', '#conv_create_gmc_new', function() {
            jQuery.fn.modal.Constructor.prototype.enforceFocus = function() {};
            jQuery.fn.modal.Constructor.prototype._enforceFocus = function() {};

            jQuery(".selectthree").select2({
                minimumResultsForSearch: 5,
                dropdownParent: jQuery('#conv_create_gmc_selectthree'),
                placeholder: function() {
                    jQuery(this).data('placeholder');
                }
            });
        })


        jQuery(".conv-enable-selection").click(function() {
            conv_change_loadingbar("show");
            jQuery(".conv-enable-selection").addClass('hidden');
            var selele = jQuery(".conv-enable-selection").closest(".conv-gmcsettings").find(
                "select.google_merchant_center_id");
            var currele = jQuery(this).closest(".conv-gmcsettings").find(
                "select.google_merchant_center_id");
            list_google_merchant_account(tvc_data, selele);
        });


        jQuery(document).on("change", "form#gmcsetings_form", function() {
            <?php if ($cust_g_email !== "") { ?>
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
                jQuery(".conv-btn-connect").removeClass("conv-btn-disconnect");
                jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-gmc");
                jQuery(".conv-btn-connect").addClass("btn-primary");
                jQuery(".conv-btn-connect").text('Save');
            <?php } else { ?>
                jQuery(".tvc_google_signinbtn").trigger("click");
            <?php } ?>
        });

        <?php if ($cust_g_email === "") { ?>
            jQuery("#conv_create_gmc_new_btn").addClass("disabled");
            jQuery(".conv-enable-selection").addClass("d-none");
        <?php } ?>


        <?php if ((isset($_GET['subscription_id']) === TRUE && esc_attr(sanitize_text_field(wp_unslash($_GET['subscription_id']))) !== '') || (empty($google_merchant_center_id) && !empty($cust_g_email))) { ?>
            list_google_merchant_account(tvc_data);
        <?php } ?>

        //Save GMC id
        jQuery(document).on("click", ".conv-btn-connect-enabled-gmc", function() {
            var feedType = jQuery('#feedType').val();
            var valtoshow_inpopup = jQuery("#valtoshow_inpopup").val() + " " + jQuery(
                ".valtoshow_inpopup_this").val();
            var selected_vals = {};
            selected_vals["subscription_id"] = "<?php echo esc_html($tvc_data['subscription_id']) ?>";

            jQuery('form#gmcsetings_form select').each(function() {
                selected_vals[jQuery(this).attr("name")] = jQuery(this).val();
            });
            var merchant_idd = jQuery('#google_merchant_center_id').find(':selected').data('merchant_id');
            selected_vals["google_merchant_id"] = jQuery("#google_merchant_center_id").val();
            selected_vals["google_merchant_center_id"] = jQuery("#google_merchant_center_id").val();
            selected_vals["merchant_id"] = merchant_idd;
            selected_vals["website_url"] = "<?php echo esc_url(get_site_url()); ?>";
            let google_ads_id = jQuery('#ads-account').val();
            if (google_ads_id !== '') {
                selected_vals["ga_GMC"] = 1;
                selected_vals["google_ads_id"] = google_ads_id;
            }
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
                    jQuery(".conv-btn-connect-enabled-gmc").text("Saving...");
                    jQuery(".conv-btn-connect-enabled-gmc").addClass('disabled');
                },
                success: function(response) {
                    var user_modal_txt =
                        "Congratulations, you have successfully connected your <br>" +
                        valtoshow_inpopup;
                    if (response == "0" || response == "1") {
                        let google_merchant_center_id = jQuery('#google_merchant_center_id').val();
                        let merchant_id = jQuery('#google_merchant_center_id').find(':selected').data('merchant_id');
                        // save_merchant_data(google_merchant_center_id, merchant_id, tvc_data, subscription_id, plan_id, true).then((res) => {
                        // if (feedType !== '') {
                        //    // createSuperAIFeed();
                        // } else {
                        conv_change_loadingbar("hide");
                        jQuery(".conv-btn-connect-enabled-gmc").text("Save");
                        jQuery(".conv-btn-connect-enabled-gmc").removeClass('disabled');
                        jQuery('.gmcAccount').html(selected_vals["google_merchant_id"])
                        jQuery("#conv_save_success_modal_cta").modal("show");
                        // }
                        // });
                    }
                }

            });
        });

        jQuery("#create_merchant_account_new").on("click", function() {
            var is_valide = true;

            var website_url = jQuery("#gmc_website_url").val();
            var email_address = jQuery("#gmc_email_address").val();
            var store_name = jQuery("#gmc_store_name").val();
            var country = jQuery("#gmc_country").val();
            var customer_id = '<?php echo esc_html($googleDetail->customer_id); ?>';
            var adult_content = jQuery("#gmc_adult_content").is(':checked');


            if (website_url == "") {
                jQuery("#create_gmc_error").removeClass("d-none");
                jQuery("#create_gmc_error small").text("Missing value of website url");
                //add_message("error", "Missing value of website url.");
                is_valide = false;
            } else if (email_address == "") {
                jQuery("#create_gmc_error").removeClass("d-none");
                jQuery("#create_gmc_error small").text("Missing value of email address.");
                //add_message("error", "Missing value of email address.");
                is_valide = false;
            } else if (store_name == "") {
                jQuery("#create_gmc_error").removeClass("d-none");
                jQuery("#create_gmc_error small").text("Missing value of store name.");
                //add_message("error", "Missing value of store name.");
                is_valide = false;
            } else if (country == "") {
                jQuery("#create_gmc_error").removeClass("d-none");
                jQuery("#create_gmc_error small").text("Missing value of country.");
                //add_message("error", "Missing value of country.");
                is_valide = false;
            } else if (jQuery('#gmc_concent').prop('checked') == false) {
                jQuery("#create_gmc_error").removeClass("d-none");
                jQuery("#create_gmc_error small").text("Please accept the terms and conditions.");
                //add_message("error", "Please I accept the terms and conditions.");
                is_valide = false;
            }

            if (is_valide == true) {
                var data = {
                    action: "create_google_merchant_center_account",
                    website_url: website_url,
                    email_address: email_address,
                    store_name: store_name,
                    country: country,
                    concent: 1,
                    customer_id: "<?php echo esc_html($googleDetail->customer_id); ?>",
                    adult_content: adult_content,
                    tvc_data: tvc_data,
                    conversios_onboarding_nonce: "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>"
                };
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: data,
                    beforeSend: function() {
                        jQuery("#gadsinviteloader").removeClass("d-none");
                    },
                    success: function(response, status) {
                        jQuery('#model_close_gmc_creation, .closeButton').removeClass('disabled')
                        if (response.error === true) {
                            var error_msg = 'Check your inputs!!!';
                            jQuery("#create_gmc_error").removeClass("d-none");
                            jQuery('#create_gmc_error small').text(error_msg)
                            jQuery('#create_merchant_account_new').removeClass('disabled')
                        } else if (response.account.id) {
                            jQuery("#new_gmc_id").text(response.account.id);
                            jQuery(".before-gmc-acc-creation").addClass("d-none");
                            jQuery(".after-gmc-acc-creation").removeClass("d-none");
                            jQuery("#model_close_gmc_creation").text("Ok, close");
                            var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
                            list_google_merchant_account(tvc_data, "", response.account.id, response.merchant_id);
                        } else if (response.error === true) {
                            const errors = JSON.parse(response.errors[0]);
                            var error_msg = response.errors;
                        } else {
                            //add_message("error", "There was error to create merchant center account");
                        }

                    }
                });

            }
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
                    jQuery('.syncSuccessMessage').html('Your latest ' + response.total_product + ' products are synced to your Google Merchant Center account.')
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
    /********************Modal POP up validation on click remove**********************************/
    jQuery(document).on('click', '#gmc_id', function(e) {
        jQuery('.errorChannel').css('border', '');
    });
    jQuery(document).on('click', '#tiktok_id', function(e) {
        jQuery('.errorChannel').css('border', '');
    });
</script>