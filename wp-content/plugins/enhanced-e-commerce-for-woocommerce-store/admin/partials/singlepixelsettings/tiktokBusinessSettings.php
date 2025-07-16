<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
require_once ENHANCAD_PLUGIN_DIR . 'admin/class-tvc-admin-helper.php';
$tvcAdminHelper = new TVC_Admin_DB_Helper();
$catalogData = $tvcAdminHelper->tvc_get_results('ee_tiktok_catalog');
$catalogCountry = array();
$catalog_business_id = array();
if (is_array($catalogData) && !empty($catalogData)) {
    foreach ($catalogData as $key => $value) {
        $catalogCountry[$key] = $value->country;
        $catalog_business_id[$key] = $value->catalog_id;
    }
}

$is_sel_disable = 'disabled';
$tiktok_mail = isset($ee_options['tiktok_setting']['tiktok_mail']) === TRUE ? $ee_options['tiktok_setting']['tiktok_mail'] : '';
$tiktok_user_id = isset($ee_options['tiktok_setting']['tiktok_user_id']) === TRUE ? $ee_options['tiktok_setting']['tiktok_user_id'] : '';
$tiktok_business_id = isset($ee_options['tiktok_setting']['tiktok_business_id']) === TRUE ? $ee_options['tiktok_setting']['tiktok_business_id'] : '';
$tiktok_business_name = isset($ee_options['tiktok_setting']['tiktok_business_name']) === TRUE ? $ee_options['tiktok_setting']['tiktok_business_name'] : '';
if (isset($_GET['tiktok_mail']) == TRUE) {
    $tiktok_mail = sanitize_email(wp_unslash($_GET['tiktok_mail']));
}
if (isset($_GET['tiktok_user_id']) == TRUE) {
    $tiktok_user_id = sanitize_text_field(wp_unslash($_GET['tiktok_user_id']));
}

$site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
$TVC_Admin_Helper = new TVC_Admin_Helper();
$plan_id = $TVC_Admin_Helper->get_plan_id();
$conv_data = $TVC_Admin_Helper->get_store_data();
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
    <?php if (isset($pixel_settings_arr[$subpage]['topnoti']) && $pixel_settings_arr[$subpage]['topnoti'] != "") { ?>
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
    $connect_url = $TVC_Admin_Helper->get_custom_connect_url_subpage(admin_url() . 'admin.php?page=conversios-google-shopping-feed', "tiktokBusinessSettings");
    /**************Tiktok Auth start ********************************************************/
    $confirm_url = "admin.php?page=conversios-google-shopping-feed&subpage=tiktokBusinessSettings";
    $state = ['confirm_url' => admin_url() . $confirm_url, 'subscription_id' => $subscriptionId];
    $tiktok_auth_url = "https://ads.tiktok.com/marketing_api/auth?app_id=7233778425326993409&redirect_uri=https://connect.conversios.io/laravelapi/public/auth/tiktok/callback&rid=q6uerfg9osn&state=" . urlencode(wp_json_encode($state));

    if ($tiktok_mail === '' && $tiktok_user_id === '') { ?>
        <a onclick='window.open("<?php echo esc_js(esc_url($tiktok_auth_url)); ?>","MyWindow","width=800,height=700,left=300, top=150"); return false;'
            href="#">
            <button class="btn btn-outline-dark" id="facebookLogin"><img style="width:19px"
                    src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_tiktok_logo.png'); ?>">
                &nbsp;<?php esc_html_e("Continue with TikTok", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
        </a>
        <p class="mt-2" style="font-size: 10px;">
            <?php esc_html_e("Please login to the Email account linked to your TikTok Business account so that we can get you business accounts and catalogs and use Chrome for best experience.", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </p>

    <?php } else { ?>
        <h5 class="fw-normal mb-1">
            <?php esc_html_e("Successfully signed in with account:", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </h5>
        <?php echo esc_html($tiktok_mail) . ', <b>User Id: </b>' . esc_html($tiktok_user_id) . ' '; ?>
        <a onclick='window.open("<?php echo esc_js(esc_url($tiktok_auth_url)); ?>","MyWindow","width=800,height=700,left=300, top=150"); return false;'
            href="#">Change</a>
    <?php }
    /**************Tiktok Auth end **********************************************************/
    ?>

    <form id="gmcsetings_form" class="convpixsetting-inner-box mt-4">
        <div id="" class="py-1">
            <label class="text-dark fw-bold-500">
                <?php esc_html_e("Select TikTok Business Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </label>
            <div class="row pt-2 conv-gmcsettings">
                <div class="col-8">
                    <select id="tiktok_business_id" name="tiktok_business_id"
                        class="form-select form-select-lg mb-3 selecttwo valtoshow_inpopup_this" style="width: 100%"
                        <?php echo esc_attr($is_sel_disable); ?>>
                        <?php if (!empty($tiktok_business_id)) { ?>
                            <option value="<?php echo esc_attr($tiktok_business_id); ?>" selected
                                data-business_name="<?php echo esc_attr($tiktok_business_name) ?>">
                                <?php echo esc_attr($tiktok_business_id); ?> - <?php echo esc_attr($tiktok_business_name) ?>
                            </option>
                        <?php } ?>
                        <option value="">Select TikTok Business Account</option>
                    </select>
                </div>
                <?php if ($tiktok_user_id !== '') {
                    $enable = ($tiktok_user_id !== '' ? 'conv-enable-selection' : ''); ?>
                    <div class="col-2 <?php echo esc_attr($enable) ?> conv-link-blue">
                        <span class="material-symbols-outlined pt-2">edit</span><label class="mb-2 fs-6 text">Edit</label>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="py-1">
            <label class="text-dark fw-bold-500">
                <?php esc_html_e("Map Catalog For Target Country", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </label>
            <div class="row pt-2">
                <div class="col-12">
                    <table class="table table-bordered" id="map_catalog_table" style="width:100%">
                        <thead>
                            <tr class="">
                                <th scope="col" class="text-start ">
                                    <?php esc_html_e("Target Country", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </th>
                                <th scope="col" class="text-start">
                                    <?php esc_html_e("Catalog Id", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table-body">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>

    <input type="hidden" id="valtoshow_inpopup" value="TikTok Business Account:" />
    <input type="hidden" id="tiktok_user_id" value="<?php echo esc_attr($tiktok_user_id) ?>" />
    <input type="hidden" id="tiktok_mail" value="<?php echo esc_attr($tiktok_mail) ?>" />
    <input type="hidden" id="conversios_onboarding_nonce"
        value="<?php echo esc_attr(wp_create_nonce('conversios_onboarding_nonce')); ?>" />

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

<div class="modal fade" id="conv_save_success_modal_" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;"
                    src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/successImg.png'); ?>">
                <h3 class="fw-normal pt-3">
                    <?php esc_html_e("Updated Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h3>
                <span id="conv_save_success_txt_" class="mb-1 lh-lg d-flex px-2"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-blue-bg m-auto text-white" data-bs-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>

<!-------------------------CTA POP up Start ---------------------------------->
<div class="modal fade" id="conv_save_success_modal_cta" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="connection-box">
                    <div class="items">
                        <img style="width:35px;"
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/popup_woocommerce_logo.png'); ?>">
                        <span>
                            <?php esc_html_e("Woo Commerce", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </span>
                    </div>
                    <div class="items">
                        <span class="material-symbols-outlined text-primary">
                            arrow_forward
                        </span>
                    </div>
                    <div class="items">
                        <img style="width:35px;"
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png'); ?>">
                        <span>
                            <?php esc_html_e("TikTok Business Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </span>
                    </div>
                </div>

            </div>
            <div class="modal-body text-center p-4">
                <div class="connected-content">
                    <h4>
                        <?php esc_html_e("Saved Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h4>
                    <p><span
                            class="fw-bolder"><?php esc_html_e("TikTok Business Account -", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <span class="gmcAccount fw-bolder"></span>
                        <?php esc_html_e("Has Been Saved Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p class="my-3">
                        <?php esc_html_e("Success! Your product feed is now linked to TikTok's powerful catalog, unlocking vast global audiences and maximizing your sales potential through our plugin.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
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
                                        <h3>
                                            <?php esc_html_e("Manage Feeds", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </h3>
                                        <p>
                                            <?php esc_html_e("A feed management tool offers benefits such as centralized product updates,
                                            optimized product listings, and improved data quality, ultimately enhancing
                                            the efficiency and effectiveness of your product feed management process.", "enhanced-e-commerce-for-woocommerce-store"); ?>

                                        </p>
                                        <div class="attribute-btn">
                                            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&tab=feed_list&createfeed=yes'); ?>" class="btn btn-primary common-bt">Create Feed</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="" style="justify-content: center">
                                <a
                                    href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=gmcsettings'); ?>"><?php esc_html_e("Connect
                                to Google Merchant Center", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                <span>OR</span>
                                <a
                                    href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=metasettings'); ?>"><?php esc_html_e("Connect
                                to Facebook Business Account", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--------------------------------CTA popup End -------------------------------------->
<?php
$google_merchant_center_id = '';
if (isset($googleDetail->google_merchant_center_id) === TRUE && $googleDetail->google_merchant_center_id !== '') {
    $google_merchant_center_id = $googleDetail->google_merchant_center_id;
}
$facebook_business_account = '';
$fb_catalog_id = '';
if (isset($googleDetail->facebook_setting->fb_business_id) === TRUE && $googleDetail->facebook_setting->fb_business_id !== '') {
    $facebook_business_account = $googleDetail->facebook_setting->fb_business_id;
    $fb_catalog_id = $googleDetail->facebook_setting->fb_catalog_id;
}
$microsoft_merchant_center_id = "";
if (isset($googleDetail->microsoft_merchant_center_id) === TRUE && $googleDetail->microsoft_merchant_center_id !== "") {
    $microsoft_merchant_center_id = $googleDetail->microsoft_merchant_center_id;
}
$tiktok_business_account = '';
if (isset($googleDetail->tiktok_setting->tiktok_business_id) === TRUE && $googleDetail->tiktok_setting->tiktok_business_id !== '') {
    $tiktok_business_account = esc_html($googleDetail->tiktok_setting->tiktok_business_id);
}
?>
<script>
    /**
     * Get TikTok business Account List
     */
    function list_tiktok_business_account() {
        conv_change_loadingbar("show");
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "get_tiktok_business_account",
                subscriptionId: "<?php echo esc_js($subscriptionId) ?>",
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                if (response.error === false) {
                    jQuery('#tiktok_business_id').empty();
                    jQuery('#tiktok_business_id').append(jQuery('<option>', {
                        value: "",
                        text: "Select TikTok Business Account"
                    }));
                    if (response.data) {
                        var tiktok_business_id = "<?php echo esc_js($tiktok_business_id) ?>";
                        jQuery.each(response.data, function(key, value) {
                            jQuery('#tiktok_business_id').append(jQuery('<option>', {
                                value: key,
                                "data-business_name": value,
                                text: key + ' - ' + value,
                                selected: (key === tiktok_business_id)
                            }));
                        });
                        jQuery('#tiktok_business_id').prop('disabled', false);
                        jQuery("#tiktok_business_id").select2({
                            dropdownCssClass: "fs-12"
                        })
                        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
                        jQuery(".conv-btn-connect").removeClass("conv-btn-disconnect");
                        jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-gmc");
                    }
                }
                conv_change_loadingbar("hide");
            }
        })
    }
    /*************************function to save tiktok data in ee_option start ******************************************************************************************/
    function saveTiktokUser() {
        var tiktok_user_id =
            "<?php echo isset($ee_options['tiktok_setting']['tiktok_user_id']) === TRUE ? esc_js($ee_options['tiktok_setting']['tiktok_user_id']) : ''; ?>"
        var selected_vals = {};
        var tiktok_data = {};
        tiktok_data["tiktok_mail"] = "<?php echo esc_js($tiktok_mail) ?>";
        tiktok_data["tiktok_user_id"] = "<?php echo esc_js($tiktok_user_id) ?>";
        selected_vals["tiktok_setting"] = tiktok_data;
        if (tiktok_user_id == tiktok_data["tiktok_user_id"]) {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: {
                    action: "conv_save_pixel_data",
                    pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                    conv_options_data: selected_vals,
                    customer_subscription_id: "<?php echo esc_js($subscriptionId) ?>",
                    conv_options_type: ["eeoptions"],
                },
                beforeSend: function() {
                    conv_change_loadingbar("show");
                },
                success: function(response) {
                    conv_change_loadingbar("hide");
                }
            });
        }
    }
    /*************************function to save tiktok data in ee_option end ******************************************************************************************/
    /*************************get user catalog id on tiktok business id change start ******************************************************************************************/
    jQuery(document).on("change", "#tiktok_business_id", function() {
        var catalogCountry = <?php echo wp_json_encode($catalogCountry) ?>;
        var catalog_business_id = <?php echo wp_json_encode($catalog_business_id) ?>;
        conv_change_loadingbar("show");
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        var business_id = jQuery('#tiktok_business_id').find(":selected").val();
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "get_tiktok_user_catalogs",
                customer_subscription_id: "<?php echo esc_js($subscriptionId) ?>",
                business_id: business_id,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                if (response.error === false) {
                    if (response.data) {
                        jQuery('#table-body').empty();
                        var tableBody = '';
                        jQuery.each(response.data, function(key, value) {
                            if (jQuery.inArray(key, catalogCountry) === -1 && catalogCountry
                                .length > 0) {
                                jQuery(".conv-btn-connect").removeClass(
                                    "conv-btn-connect-disabled");
                                jQuery(".conv-btn-connect").removeClass("conv-btn-disconnect");
                                jQuery(".conv-btn-connect").addClass(
                                    "conv-btn-connect-enabled-gmc");
                            }
                            tableBody += '<tr>';
                            tableBody += '<td class="align-middle text-start">' + key + '</td>';
                            tableBody +=
                                '<td class="align-middle text-start"><select id="" name="catalogId[]" class="form-select form-select-lg mb-3 catalogId" style="width: 100%">';
                            jQuery.each(value, function(valKey, ValValue) {
                                var selected = "";
                                if (jQuery.inArray(valKey, catalog_business_id) !== -
                                    1 && catalog_business_id.length > 0) {
                                    var selected = 'selected="selected"';
                                }
                                tableBody += '<option value="' + valKey +
                                    '"  data-catalog_country="' + key +
                                    '" data-catalog_name="' + ValValue + '" ' +
                                    selected + '>' + valKey + ' - ' + ValValue +
                                    '</option>';
                            })
                            tableBody += '</select></td></tr>';
                        });
                        jQuery('#table-body').html(tableBody);
                        jQuery(".catalogId").select2({
                            dropdownCssClass: "fs-12"
                        })
                    }
                }
                conv_change_loadingbar("hide");
            }
        })
    });
    /*************************get user catalog id on tiktok business id change end ******************************************************************************************/
    /**************************Enable save button on catalog is change start **********************************************/
    jQuery(document).on("change", ".catalogId", function() {
        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
        jQuery(".conv-btn-connect").removeClass("conv-btn-disconnect");
        jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-gmc");

    });
    /**************************Enable save button on catalog is change end **********************************************/
    //Onload functions
    jQuery(function() {
        jQuery(".navinfotopnav ul li").removeClass('active');
        jQuery(".navinfotopnav ul li:nth-child(3)").addClass('active');
        jQuery(".navinfotopnav ul li:nth-child(2) img").css('filter', 'grayscale(100%)');

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        jQuery(".catalogId").select2()
        //override back button link to GMC Channel Configuration 
        jQuery('.hreflink').attr('href', 'admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page');

        jQuery(".conv-enable-selection").click(function() {
            conv_change_loadingbar("show");
            jQuery(".conv-enable-selection").addClass('hidden');
            list_tiktok_business_account();
        });

        <?php if ((isset($_GET['subscription_id']) === TRUE && sanitize_text_field(wp_unslash($_GET['subscription_id']))) || (isset($_GET['tiktok_mail']) === TRUE && !empty($_GET['tiktok_mail']))) { ?>
            <?php if (isset($ee_options['tiktok_setting']['tiktok_mail']) && ($_GET['tiktok_mail'] !== $ee_options['tiktok_setting']['tiktok_mail'])) { ?>
                saveTiktokUser();
            <?php } ?>
            list_tiktok_business_account();
            jQuery(".conv-enable-selection").addClass("d-none");
        <?php } ?>

        <?php if (!empty($tiktok_business_id)) { ?>
            jQuery('#tiktok_business_id').trigger("change");
        <?php } ?>
        jQuery(document).on('change', '#tiktok_business_id', function() {
            jQuery('.selection').find("[aria-labelledby='select2-tiktok_business_id-container']")
                .removeClass('selectError');
        });
        /********************************Save TikTok id and other data  start ********************************************************/
        jQuery(document).on("click", ".conv-btn-connect-enabled-gmc", function() {
            var catalogData = {};
            jQuery('.catalogId').each(function() {
                catalogData[jQuery(this).find(":selected").data("catalog_country")] = [jQuery(this)
                    .find(":selected").val(), jQuery(this).find(":selected").data(
                        "catalog_name"), jQuery(this).find(":selected").data("catalog_country")
                ];
            })
            var selected_vals = {};
            var tiktok_data = {};
            tiktok_data["tiktok_mail"] = jQuery('#tiktok_mail').val();
            tiktok_data["tiktok_user_id"] = jQuery('#tiktok_user_id').val();
            tiktok_data["tiktok_business_id"] = jQuery('#tiktok_business_id').find(":selected").val();
            tiktok_data["tiktok_business_name"] = jQuery('#tiktok_business_id').find(":selected").data(
                "business_name")
            selected_vals["tiktok_setting"] = tiktok_data;
            if (tiktok_data["tiktok_business_id"] === '') {
                jQuery('.selection').find("[aria-labelledby='select2-tiktok_business_id-container']")
                    .addClass('selectError');
                return false;
            }

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: {
                    action: "conv_save_pixel_data",
                    pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                    conv_options_data: selected_vals,
                    conv_catalogData: catalogData,
                    customer_subscription_id: "<?php echo esc_js($subscriptionId) ?>",
                    conv_options_type: ["eeoptions", "tiktokmiddleware", "tiktokcatalog"],
                },
                beforeSend: function() {
                    conv_change_loadingbar("show");
                    jQuery(".conv-btn-connect-enabled-gmc").text("Saving...");
                    jQuery(".conv-btn-connect-enabled-gmc").addClass('disabled');
                },
                success: function(response) {
                    conv_change_loadingbar("hide");
                    if (response == "0" || response == "1") {
                        jQuery(".conv-btn-connect-enabled-gmc").text("Save");
                        jQuery('.gmcAccount').html(tiktok_data["tiktok_user_id"])
                        jQuery("#conv_save_success_modal_cta").modal("show");
                    }
                }
            });
        });
    });
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
</script>