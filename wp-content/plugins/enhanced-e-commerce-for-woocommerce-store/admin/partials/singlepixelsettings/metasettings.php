<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
$is_sel_disable = 'disabled';
$google_merchant_center_id = (isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id != "") ? $googleDetail->google_merchant_center_id : "";
$microsoft_merchant_center_id = "";
if (isset($googleDetail->microsoft_merchant_center_id) === TRUE && $googleDetail->microsoft_merchant_center_id !== "") {
    $microsoft_merchant_center_id = $googleDetail->microsoft_merchant_center_id;
}

$site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
$store_country = get_option('woocommerce_default_country');
$store_country = explode(":", $store_country);
if ($store_country[0]) {
    $country = $store_country[0];
} else {
    $country = '';
}
$woo_currency = get_option('woocommerce_currency');
$timezone = get_option('timezone_string');
$confirm_url = "admin.php?page=conversios-google-shopping-feed&subpage=metasettings";
$fb_mail = isset($ee_options['facebook_setting']['fb_mail']) === TRUE ? esc_html($ee_options['facebook_setting']['fb_mail']) : '';

if (isset($_GET['g_mail']) && !empty($_GET['g_mail'])) {
    $fb_mail = sanitize_email(wp_unslash($_GET['g_mail']));
    update_option('ee_customer_fbmail', $fb_mail);
}

//echo '<pre>'; print_r($ee_options); echo '</pre>';

// $error = '';
// if(isset($_GET['error'])) {
//     $error = $_GET['error'];
// }
$fb_business_id = isset($ee_options['facebook_setting']['fb_business_id']) === TRUE ? esc_html($ee_options['facebook_setting']['fb_business_id']) : '';
$fb_catalog_id = isset($ee_options['facebook_setting']['fb_catalog_id']) === TRUE ? esc_html($ee_options['facebook_setting']['fb_catalog_id']) : '';
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
    <div class="alert d-flex align-items-cente p-0">
        <div class="convpixsetting-inner-box">
            <span>
                <?php echo esc_html($fb_mail);
                $businessId = '';
                $subId = isset($_GET['subscription_id']) ? sanitize_text_field(wp_unslash($_GET['subscription_id'])) : sanitize_text_field($subscriptionId);
                $facebook_auth_url = TVC_API_CALL_URL_TEMP . '/auth/facebook?domain=' . esc_url_raw(get_site_url()) . '&app_id=' . $app_id . '&country=' . $country . '&user_currency=' . $woo_currency . '&subscription_id=' . $subId . '&confirm_url=' . admin_url() . $confirm_url . '&timezone=' . $timezone . '&scope=productFeed';

                if (isset($_GET['subscription_id']) || $fb_business_id !== '') {
                    $data = array(
                        "customer_subscription_id" => esc_html($subId)
                    );
                    $businessId =  $customApiObj->getUserBusinesses($data);
                }
                if ($fb_business_id !== '') {
                    $cat_data = array(
                        "customer_subscription_id" => esc_html($subId),
                        "business_id" => esc_html($fb_business_id),
                    );
                    $catalogId = $customApiObj->getCatalogList($cat_data);
                }
                ?>
                <span class="conv-link-blue ps-2 facebookLogin" id="facebookLogin">
                    <a onclick="window.open('<?php echo esc_url($facebook_auth_url); ?>','MyWindow','width=800,height=700,left=300, top=150'); return false;" href="#">
                        <?php if (isset($ee_options['facebook_setting']['fb_business_id']) || isset($_GET['subscription_id'])) {
                            echo 'Change';
                        } else {
                            echo '<button class="btn conv-blue-bg text-white"><img style="width:24px" src="' . esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png') . '" /> &nbsp;Sign In with Facebook</button>';
                        } ?>
                    </a>
                </span>
            </span>
        </div>
    </div>

    <form id="gmcsetings_form" class="convpixsetting-inner-box mt-4">
        <div id="analytics_box_UA" class="py-1 row">
            <div class="col-5">
                <label class="text-dark fw-bold-500">
                    <?php esc_html_e("Facebook Business ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
                <div class="pt-2 conv-metasettings">
                    <div class="col-12">
                        <select class="select2" id="fb_business_id" name="fb_business_id" style="width:100%" <?php echo isset($_GET['subscription_id']) ? '' : "disabled" ?>>
                            <option value="">Select Business Id</option>
                            <?php
                            $selectedBusId = '';
                            $selectBusChek = '';
                            if (isset($businessId) && $businessId != '') {
                                foreach ($businessId as $key => $businessVal) {
                                    $selectedBusId = isset($ee_options['facebook_setting']['fb_business_id']) && $ee_options['facebook_setting']['fb_business_id'] == $key ?  "selected" : '';
                                    if ($selectedBusId == 'selected') {
                                        $selectBusChek = 'selected';
                                    }
                            ?>
                                    <option value="<?php echo esc_attr($key) ?>" <?php echo isset($ee_options['facebook_setting']['fb_business_id']) && $ee_options['facebook_setting']['fb_business_id'] == $key ?  "selected" : '' ?>><?php echo esc_html($businessVal) ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <label class="text-dark fw-bold-500">
                    <?php esc_html_e("Facebook Catalog ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
                <div class="pt-2 conv-metasettings">
                    <div class="col-12">
                        <select class="select2" id="fb_catalog_id" name="fb_catalog_id" style="width:100%" <?php echo isset($_GET['subscription_id']) ? '' : "disabled" ?>>
                            <option value="">Select Catalog Id</option>
                            <?php
                            $selectChek = '';
                            $selected = '';
                            if (isset($catalogId->data)) {
                                foreach ($catalogId->data as $key => $catalogVal) {
                                    $selected = isset($ee_options['facebook_setting']['fb_catalog_id']) && $ee_options['facebook_setting']['fb_catalog_id'] == $catalogVal->id ?  "selected" : '';
                                    if ($selected == 'selected') {
                                        $selectChek = 'selected';
                                    }
                            ?>
                                    <option value="<?php echo esc_attr($catalogVal->id) ?>" <?php echo isset($ee_options['facebook_setting']['fb_catalog_id']) && $ee_options['facebook_setting']['fb_catalog_id'] == $catalogVal->id ?  "selected" : '' ?>><?php echo esc_html($catalogVal->id) . '-' . esc_html($catalogVal->name) ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-2 editDiv <?php echo isset($ee_options['facebook_setting']['fb_business_id']) ? '' : 'd-none' ?>">
                <div class="conv-enable-selection text-primary pt-4-5">
                    <span class="material-symbols-outlined">edit</span><label class="mb-2 fs-12 text">Edit</label>
                </div>
            </div>
        </div>
        <input type="hidden" id="fb_mail" value="<?php echo esc_attr($fb_mail) ?>" />


        <div class="row row-x-0 d-flex justify-content-between align-items-center conv_create_gads_new_card rounded px-3 py-3 mt-3" style="background: #caf3e3;">
            <div class="mt-0 mb-2 col-10">
                <div class="fs-6 fw-bold text-primary">Unlock Google First Party Mode with Server Side Tagging</div>
                <ul class="conv-green-checklis fb-kapi list-unstyled mt-1">
                    <li class="d-flex fs-14 fw-bold">
                        <span class="material-symbols-outlined text-success md-18">check_circle</span>
                        Improves Event Match Quality scores by sending extra user data (e.g., email, phone number).
                    </li>
                    <li class="d-flex fs-14 fw-bold">
                        <span class="material-symbols-outlined text-success md-18">
                            check_circle
                        </span>
                        Capture events like purchases and form submissions directly from your server, regardless of browser restrictions.
                    </li>
                    <li class="d-flex fs-14 fw-bold">
                        <span class="material-symbols-outlined text-success md-18">check_circle</span>
                        Complete picture of user journeys, resulting in better conversion attribution, especially with iOS 14+ restrictions.
                    </li>
                    <li class="d-flex fs-14 fw-bold">
                        <span class="material-symbols-outlined text-success md-18">check_circle</span>
                        Bypasses ad blockers and browser restrictions, ensuring more precise tracking of conversions.
                    </li>
                </ul>
                <a target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&amp;utm_medium=snapinnersetting&amp;utm_campaign=sstcapi&amp;plugin_name=aio" class="align-middle btn btn-sm btn-primary fw-bold-500">
                    Buy Professional Plan Now! </a>
            </div>
        </div>
    </form>

</div>

<!-------------------------CTA POP up Start ---------------------------------->
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
                        <span>
                            <?php esc_html_e("WooCommerce", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </span>
                    </div>
                    <div class="items">
                        <span class="material-symbols-outlined text-primary">
                            arrow_forward
                        </span>
                    </div>
                    <div class="items">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/logos/fb_channel_logo.png', '', '', 'width:35px;'),
                            array(
                                'img' => array(
                                    'src' => true,
                                    'alt' => true,
                                    'class' => true,
                                    'style' => true,
                                ),
                            )
                        ); ?>
                        <span>
                            <?php esc_html_e("Facebook Business Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </span>
                    </div>
                </div>

            </div>
            <div class="modal-body text-center p-4">
                <div class="connected-content">
                    <h4>
                        <?php esc_html_e("Saved Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h4>
                    <p><span class="fw-bolder">Facebook Business Account -</span> <span class="gmcAccount fw-bolder"></span>
                        Has Been Saved Successfully</p>
                    <p class="my-3">
                        <?php esc_html_e("Success! Your product feed is now linked to Facebook's powerful catalog, unlocking vast global audiences and maximizing your sales potential through our plugin.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
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
                            <div class="col-12">
                                <div class="" style="justify-content: center">
                                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage=gmcsettings'); ?>">Connect
                                        to Google Merchant Center</a>
                                </div>
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
    $google_merchant_center_id = esc_html($googleDetail->google_merchant_center_id);
}
$tiktok_business_account = '';
if (isset($googleDetail->tiktok_setting->tiktok_business_id) === TRUE && $googleDetail->tiktok_setting->tiktok_business_id !== '') {
    $tiktok_business_account = esc_html($googleDetail->tiktok_setting->tiktok_business_id);
}

?>
<script>
    //Onload functions
    jQuery(function() {
        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
        var tvc_ajax_url = '<?php echo esc_url_raw(admin_url('admin-ajax.php')); ?>';
        let subscription_id = "<?php echo esc_attr($subId); ?>";
        let plan_id = "<?php echo esc_attr($plan_id); ?>";
        let app_id = "<?php echo esc_attr($app_id); ?>";
        let bagdeVal = "yes";
        let convBadgeVal = "<?php echo esc_attr($convBadgeVal); ?>";
        let fb_business_id = "<?php echo esc_attr($fb_business_id); ?>";
        jQuery('#fb_catalog_id').select2({
            dropdownCssClass: "fs-12"
        });
        jQuery('#fb_business_id').select2({
            dropdownCssClass: "fs-12"
        });
        jQuery('.hreflink').attr('href', 'admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page');

        jQuery(document).on("change", "form#gmcsetings_form", function() {
            jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            jQuery(".conv-btn-connect").addClass("btn-primary");
            jQuery(".conv-btn-connect").text('Save');
        });

        <?php
        if (isset($_GET['subscription_id'])) { ?>
            jQuery('.editDiv').addClass('d-none')
            jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            jQuery(".conv-btn-connect").addClass("btn-primary");
            jQuery(".conv-btn-connect").text('Save');
        <?php  }
        ?>

        // Save data
        jQuery(document).on("click", ".conv-btn-connect", function() {
            var selected_vals = {};
            var facebook_data = {};
            facebook_data["fb_mail"] = jQuery('#fb_mail').val();
            facebook_data["fb_business_id"] = jQuery('#fb_business_id').find(":selected").val();
            facebook_data["fb_catalog_id"] = jQuery('#fb_catalog_id').find(":selected").val();
            selected_vals["facebook_setting"] = facebook_data;
            if (facebook_data["fb_business_id"] === '') {
                jQuery('.selection').find("[aria-labelledby='select2-fb_business_id-container']").addClass('selectError');
                return false;
            }
            if (facebook_data["fb_catalog_id"] === '') {
                jQuery('.selection').find("[aria-labelledby='select2-fb_catalog_id-container']").addClass('selectError');
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
                    customer_subscription_id: "<?php echo esc_html($subId) ?>",
                    conv_options_type: ["eeoptions", "facebookmiddleware", "facebookcatalog"],
                },
                beforeSend: function() {
                    conv_change_loadingbar("show");
                    jQuery(".conv-btn-connect").text("Saving...");
                    jQuery(".conv-btn-connect").addClass('disabled');
                },
                success: function(response) {
                    conv_change_loadingbar("hide");
                    if (response == "0" || response == "1") {
                        jQuery(".conv-btn-connect").text("Save");
                        jQuery('.gmcAccount').html(facebook_data["fb_business_id"])
                        jQuery("#conv_save_success_modal_cta").modal("show");
                    }
                }
            });

        });
        /************************************* Auto Sync Toggle Button End*************************************************************************/
    });
    jQuery(document).on('change', '#fb_business_id', function() {
        jQuery('.selection').find("[aria-labelledby='select2-fb_business_id-container']").removeClass('selectError');
        var fb_business = jQuery('#fb_business_id').find(":selected").val();
        if (fb_business != '') {
            var data = {
                action: "get_fb_catalog_data",
                customer_subscription_id: <?php echo esc_html($subId) ?>,
                fb_business_id: fb_business,
                fb_business_nonce: "<?php echo esc_js(wp_create_nonce('fb_business_nonce')); ?>"
            }
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {
                    conv_change_loadingbar('show')
                },
                success: function(response) {
                    var cat_id = "<?php echo isset($ee_options['facebook_setting']['fb_catalog_id']) ? esc_js($ee_options['facebook_setting']['fb_catalog_id']) : '' ?>";
                    $html = '<option value="">Select Catalog Id</option>';
                    $.each(response, function(index, value) {
                        var selected = (value.id == cat_id) ? 'selected' : '';
                        $html += '<option value="' + value.id + '" ' + selected + '>' + value.id + '-' + value.name + '</option>';
                    });
                    $('#fb_catalog_id').html($html);
                    conv_change_loadingbar('hide')
                }
            });
        } else {
            $html = '<option value="">Select Catalog Id</option>';
            $('#fb_catalog_id').html($html);
        }
    })
    jQuery(document).on('click', '.conv-enable-selection', function() {
        jQuery('#fb_business_id').removeAttr('disabled')
        jQuery('#fb_catalog_id').removeAttr('disabled')
        jQuery('.conv-enable-selection').addClass('d-none')
        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled")
        jQuery(".conv-btn-connect").addClass("btn-primary")
    })

    /*************************************Save Feed Data End***************************************************************************/
    function conv_change_loadingbar(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
            jQuery('#submitFeed').attr('disabled', true);
        } else {
            jQuery("#loadingbar_blue").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
            jQuery('#submitFeed').attr('disabled', false);
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
                        jQuery('.tiktok_catalog_id').text('You do not have a catalog associated with the selected target country. Do not worry we will create a new catalog for you.');
                    }
                }
                conv_change_loadingbar_modal('hide');
            }
        });
    }
</script>