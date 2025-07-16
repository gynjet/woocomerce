<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$is_sel_disable = 'disabled';
$cust_g_email =  (isset($tvc_data['g_mail']) && esc_attr($subscriptionId)) ? esc_attr($tvc_data['g_mail']) : "";
?>


<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <?php
    $connect_url = $TVC_Admin_Helper->get_custom_connect_url_subpage(admin_url() . 'admin.php?page=conversios-google-analytics', "gasettings");
    require_once("googlesignin.php");
    ?>

    <form id="gasettings_form" class="convpixsetting-inner-box mt-3">

        <?php
        $tracking_option = (isset($ee_options['tracking_option']) && $ee_options['tracking_option'] != "") ? $ee_options['tracking_option'] : "";
        ?>
        <div>
            <!-- Google Analytics 3 -->
            <?php
            $ua_analytic_account_id = (isset($googleDetail->ua_analytic_account_id) && $googleDetail->ua_analytic_account_id != "") ? $googleDetail->ua_analytic_account_id : "";
            $property_id = (isset($googleDetail->property_id) && $googleDetail->property_id != "") ? $googleDetail->property_id : "";
            ?>
            <!-- Google Analytics 3 End-->

            <!-- Google Analytics 4 -->
            <?php
            $ga4_analytic_account_id = (isset($googleDetail->ga4_analytic_account_id) && $googleDetail->ga4_analytic_account_id != "") ? $googleDetail->ga4_analytic_account_id : "";
            $measurement_id = (isset($googleDetail->measurement_id) && $googleDetail->measurement_id != "") ? $googleDetail->measurement_id : "";
            ?>
            <div class="alert alert-warning d-flex align-items-top" role="alert">
                <strong class="h6 me-3">Note:</strong>
                <div>
                    When you set the Google Analytics 4 Measurement ID here, it will be used for all tracking.
                    <br>All website activity will be sent to this same ID.
                </div>
            </div>
            <div id="analytics_box_GA4" class="py-1">
                <div class="row pt-3 conv-hideme-gasettings">
                    <div class="col-5">
                        <h5 class="mb-1 d-flex align-items-center">
                            <b><?php esc_html_e("GA4 Account ID:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                            <?php if (!empty($ga4_analytic_account_id)) { ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6">check_circle</span>
                            <?php } ?>
                        </h5>
                        <select id="ga4_analytic_account_id" name="ga4_analytic_account_id" acctype="GA4" class="form-select form-select-lg mb-3 ga_analytic_account_id ga_analytic_account_id_ga4 selecttwo_search" style="width: 100%" <?php echo esc_html($is_sel_disable); ?>>
                            <?php if (!empty($ga4_analytic_account_id)) { ?>
                                <option selected><?php echo esc_html($ga4_analytic_account_id); ?></option>
                            <?php } ?>
                            <option value="">Select GA4 Account ID</option>
                        </select>
                    </div>
                    <div class="col-5">
                        <h5 class="mb-1 d-flex align-items-center">
                            <b><?php esc_html_e("GA4 Measurement ID:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                            <?php if (!empty($measurement_id)) { ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6">check_circle</span>
                            <?php } ?>
                        </h5>
                        <select id="ga4_property_id" name="measurement_id" class="form-select form-select-lg mb-3 selecttwo_search" style="width: 100%" <?php echo esc_html($is_sel_disable); ?>>
                            <option value="">Select Measurement ID</option>
                            <?php if (!empty($measurement_id)) { ?>
                                <option selected><?php echo esc_html($measurement_id); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-2 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-sm d-flex conv-enable-selection align-items-center">
                            <span class="px-1"><?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        </button>
                    </div>

                </div>
            </div>
            <!-- Google Analytics 4 End -->


            <!-- GA4 API Secret  -->
            <?php
            $ga4_api_secret = (isset($ee_options["ga4_api_secret"]) && $ee_options["ga4_api_secret"] != "") ? $ee_options["ga4_api_secret"] : "";
            ?>
            <div id="ga4apisecret_box" class="py-3 <?php echo $tracking_option === 'UA' || !CONV_IS_WC ? 'd-none' : ''; ?>">
                <div class="row pt-2">
                    <div class="col-5">
                        <h5 class="d-flex fw-normal mb-1 text-dark">
                            <b><?php esc_html_e("GA4 API Secret", "enhanced-e-commerce-for-woocommerce-store"); ?></b>&nbsp;<?php esc_html_e("(To track refund order)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <span class="ms-1 align-middle conv-link-blue fw-bold-500 upgradetopro_badge" popupopener="ga4apisecret_box_inner">&nbsp;
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/logos/upgrade_badge.png', '', 'rounded shadow'),
                                    array(
                                        'img' => array(
                                            'src' => true,
                                            'alt' => true,
                                            'class' => true,
                                            'style' => true,
                                        ),
                                    )
                                ); ?> &nbsp; Available
                                In Pro</span>
                        </h5>
                        <input readonly type="text" name="ga4_api_secret" id="ga4_api_secret" class="form-control disabled" value="<?php echo esc_attr($ga4_api_secret); ?>" placeholder="e.g. CnTrpcbsStWFU5-TmSuhuS">
                    </div>

                </div>
            </div>
            <!-- GA4 API Secret End-->
            <div id="additional_tracking" class="py-3">
                <?php
                // Fetch stored values
                //$ee_api_data_all = unserialize(get_option("ee_api_data"));
                $conv_scroll_tracking = $ee_options['conv_track_page_scroll'] ?? "1";
                $conv_file_download_tracking = $ee_options['conv_track_file_download'] ?? "1";
                $conv_author_tracking = $ee_options['conv_track_author'] ?? "1";
                $conv_signin_tracking = $ee_options['conv_track_signin'] ?? "1";
                $conv_signup_tracking = $ee_options['conv_track_signup'] ?? "1";
                ?>
                <div class="row pt-1">
                    <div class="col-12">
                        <h5 class="d-flex fw-normal mb-1 text-dark">
                            <b><?php esc_html_e("Additional Tracking:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                        </h5>
                    </div>
                    <div class="col-12 mx-3 my-2">
                        <div class="row">
                            <!-- Page Scroll Tracking -->
                            <div class="form-check mt-2 col-4">
                                <input class="form-check-input me-2" type="checkbox" id="conv_track_page_scroll"
                                    <?php echo $conv_scroll_tracking ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_scroll_tracking">
                                    <?php esc_html_e("Page Scroll Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <span class="material-symbols-outlined text-secondary md-18" data-bs-toggle="tooltip" data-bs-placement="top" title="
                                Measure how far users scroll on your site to analyze engagement and optimize content placement." data-bs-original-title="">
                                    info
                                </span>
                            </div>
                            <!-- File Download Tracking -->
                            <div class="form-check mt-2 col-4">
                                <input class="form-check-input me-2" type="checkbox" id="conv_track_file_download"
                                    <?php echo $conv_file_download_tracking ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_file_download_tracking">
                                    <?php esc_html_e("File Download Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <span class="material-symbols-outlined text-secondary md-18" data-bs-toggle="tooltip" data-bs-placement="top" title="Track when users download files to measure engagement and improve content performance." data-bs-original-title="">
                                    info
                                </span>
                            </div>
                            <!-- Author Tracking -->
                            <div class="form-check mt-2 col-4">
                                <input class="form-check-input me-2" type="checkbox" id="conv_track_author"
                                    <?php echo $conv_author_tracking ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enable_author_tracking">
                                    <?php esc_html_e("Author Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <span class="material-symbols-outlined text-secondary md-18" data-bs-toggle="tooltip" data-bs-placement="top" title="Measures user interactions with author content to improve audience targeting and content strategy." data-bs-original-title="">
                                    info
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <!-- SignIn Event Tracking -->
                                <div class="form-check mt-2 col-4">
                                    <input class="form-check-input me-2" type="checkbox" id="conv_track_signin"
                                        <?php echo $conv_signin_tracking ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enable_signin_tracking">
                                        <?php esc_html_e("Login Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </label>
                                    <span class="material-symbols-outlined text-secondary md-18" data-bs-toggle="tooltip" data-bs-placement="top" title=" Track when users log in to understand engagement and improve user retention.
                                    " data-bs-original-title="">
                                        info
                                    </span>
                                </div>
                                <!-- SignUp Event Tracking -->
                                <div class="form-check mt-2 col-4">
                                    <input class="form-check-input me-2" type="checkbox" id="conv_track_signup"
                                        <?php echo $conv_signup_tracking ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="enable_signup_tracking">
                                        <?php esc_html_e("SignUp Event Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </label>
                                    <span class="material-symbols-outlined text-secondary md-18" data-bs-toggle="tooltip" data-bs-placement="top" title="Track how many people sign up on your site to understand what attracts new users." data-bs-original-title="">
                                        info
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="row row-x-0 d-flex justify-content-between align-items-center conv_create_gads_new_card rounded px-3 py-3 mt-5" style="background: #caf3e3;">
        <div class="mt-0 mb-2 col-3 d-flex justify-content-center">
            <?php echo wp_kses(
                enhancad_get_plugin_image('/admin/images/sstimpact.png', '', 'rounded shadow'),
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
        <div class="mt-0 mb-2 col-9">
            <div class="fs-6 fw-bold text-primary">Increase conversions by 40% with the Server-Side Tagging Enterprise Plan</div>
            <ul class="conv-green-checklis fb-kapi list-unstyled mt-1">
                <li class="d-flex fs-14 fw-bold">
                    <span class="material-symbols-outlined text-success md-18">check_circle</span>
                    Full automation for server-side tracking and web container setup including Datalayer setup
                </li>
                <li class="d-flex fs-14 fw-bold">
                    <span class="material-symbols-outlined text-success md-18">
                        check_circle
                    </span>
                    Custom GTM loader with First party mode enable
                </li>
                <li class="d-flex fs-14 fw-bold">
                    <span class="material-symbols-outlined text-success md-18">check_circle</span>
                    Server-side tracking for GA4, Google Ads, Facebook CAPI, Snapchat, and TikTok Events API
                </li>
                <li class="d-flex fs-14 fw-bold">
                    <span class="material-symbols-outlined text-success md-18">check_circle</span>
                    Faster load time with server-side tracking

                </li>
            </ul>
            <a target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&amp;utm_medium=gainnersetting&amp;utm_campaign=sstnudge&amp;plugin_name=aio" class="align-middle btn btn-sm btn-primary fw-bold-500">
                Buy Now! </a>
        </div>
    </div>
</div>

<script>
    // get list of google analytics account
    function list_analytics_account(tvc_data, selelement, currele, page = 1) {
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "get_analytics_account_list",
                tvc_data: tvc_data,
                page: page,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                // console.log(response);
                if (response && response.error == false) {
                    var error_msg = 'null';
                    if (response?.data?.items.length > 0) {
                        <?php if (isset($_GET['subscription_id'])) : ?>
                            jQuery('#ga4_analytic_account_id').html('<option value="">Select GA4 Account ID</option>');
                        <?php endif; ?>
                        var AccOptions = '';
                        var selected = '';
                        response?.data?.items.forEach(function(item) {
                            AccOptions = AccOptions + '<option value="' + item.id + '"> ' + item.name +
                                '-' + item.id + '</option>';
                        });

                        jQuery('#ga4_analytic_account_id').append(AccOptions); //GA4 
                        selelement.prop("disabled", false);
                        jQuery(".conv-enable-selection").addClass('d-none');

                    } else {
                        // console.log("error1", "There are no Google Analytics accounts associated with this email.");
                        getAlertMessageAll(
                            'info',
                            'Error',
                            message = 'There are no Google Analytics accounts associated with this email.',
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc = '<?php echo wp_kses(
                                                enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png'),
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
                    }

                } else if (response && response.error == true && response.error != undefined) {
                    const errors = response.errors;
                    getAlertMessageAll(
                        'info',
                        'Error',
                        message = errors,
                        icon = 'info',
                        buttonText = 'Ok',
                        buttonColor = '#FCCB1E',
                        iconImageSrc = '<?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png'),
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
                    var error_msg = errors;
                } else {
                    getAlertMessageAll(
                        'info',
                        'Error',
                        message = 'There are no Google Analytics accounts associated with this email.',
                        icon = 'info',
                        buttonText = 'Ok',
                        buttonColor = '#FCCB1E',
                        iconImageSrc = '<?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png'),
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
                }
                jQuery("#tvc-ga4-acc-edit-acc_box")?.removeClass('tvc-disable-edits');
                conv_change_loadingbar("hide");
                jQuery(".conv-enable-selection").removeClass('disabled');
            }
        });
    }


    // get list properties dropdown options
    function list_analytics_web_properties(type, tvc_data, account_id, thisselid) {
        jQuery("#ga4_property_id").prop("disabled", true);
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "get_analytics_web_properties",
                account_id: account_id,
                type: type,
                tvc_data: tvc_data,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                if (response && response.error == false) {
                    var error_msg = 'null';


                    if (type == "GA4") {
                        jQuery('#ga4_property_id').empty().trigger("change");
                        jQuery('#both_ga4_property_id').empty().trigger("change");
                        if (response?.data?.wep_measurement.length > 0) {
                            var streamOptions = '<option value="">Select Measurement Id</option>';
                            var selected = '';
                            response?.data?.wep_measurement.forEach(function(item) {
                                let dataName = item.name.split("/");
                                streamOptions = streamOptions + '<option value="' + item.measurementId +
                                    '">' + item.measurementId + ' - ' + item.displayName + '</option>';
                            });
                            jQuery('#ga4_property_id').append(streamOptions);
                            jQuery('#both_ga4_property_id').append(streamOptions);
                        } else {
                            var streamOptions = '<option value="">No GA4 Property Found</option>';
                            jQuery('#ga3_property_id').append(streamOptions);
                            jQuery('#both_ga3_property_id').append(streamOptions);
                            getAlertMessageAll(
                                'info',
                                'Error',
                                message =
                                'There are no Google Analytics 4 Properties associated with this analytics account.',
                                icon = 'info',
                                buttonText = 'Ok',
                                buttonColor = '#FCCB1E',
                                iconImageSrc = '<?php echo wp_kses(
                                                    enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png'),
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
                        }
                        jQuery(".ga_analytic_account_id_ga4:not(#" + thisselid + ")").val(account_id).trigger(
                            "change");
                    }

                } else if (response && response.error == true && response.error != undefined) {
                    const errors = response.error[0];
                    getAlertMessageAll(
                        'info',
                        'Error',
                        message = errors,
                        icon = 'info',
                        buttonText = 'Ok',
                        buttonColor = '#FCCB1E',
                        iconImageSrc = '<?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png'),
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
                    //add_message("error", errors);
                    var error_msg = errors;
                } else {
                    //add_message("error", "There are no Google Analytics Properties associated with this email.");
                    getAlertMessageAll(
                        'info',
                        'Error',
                        message = 'There are no Google Analytics Properties associated with this email.',
                        icon = 'info',
                        buttonText = 'Ok',
                        buttonColor = '#FCCB1E',
                        iconImageSrc = '<?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png'),
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
                }
                conv_change_loadingbar("hide");
                jQuery("#ga4_property_id").prop("disabled", false);
            }
        });
    }

    function load_ga_accounts(tvc_data) {
        conv_change_loadingbar("show");
        jQuery(".conv-enable-selection").addClass('disabled');
        var selele = jQuery(".conv-enable-selection").closest(".conv-hideme-gasettings").find(
            "select.ga_analytic_account_id");
        var currele = jQuery(this).closest(".conv-hideme-gasettings").find("select.ga_analytic_account_id");
        list_analytics_account(tvc_data, selele, currele);
    }

    //Onload functions
    jQuery(function() {

        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        jQuery("#upgradetopro_modal_link").attr("href",
            '<?php echo esc_url($TVC_Admin_Helper->get_conv_pro_link_adv("popup", "gasettings",  "conv-link-blue fw-bold", "linkonly")); ?>'
        );


        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
        var tvc_ajax_url = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        let subscription_id = "<?php echo esc_html($subscriptionId); ?>";
        let plan_id = "<?php echo esc_html($plan_id); ?>";
        let app_id = "<?php echo esc_html(CONV_APP_ID); ?>";
        let bagdeVal = "yes";
        let convBadgeVal = "<?php echo esc_html($convBadgeVal); ?>";


        jQuery(".selecttwo_search").select2({
            minimumResultsForSearch: 1,
            placeholder: function() {
                jQuery(this).data('placeholder');
            }
        });


        jQuery('input[type=radio][name=tracking_option]').change(function() {
            jQuery(".conv-hideme-gasettings").addClass('d-none');
            jQuery(this).parent().find(".conv-hideme-gasettings").removeClass('d-none');
            var tracking_option = jQuery(this).val();
            if (tracking_option == "BOTH" || tracking_option == "GA4") {
                jQuery("#ga4apisecret_box").removeClass("d-none");
            }
            if (tracking_option == "UA") {
                jQuery("#ga4apisecret_box").addClass("d-none");
            }
        });

        <?php
        if ($cust_g_email != "" || isset($_GET['subscription_id'])) {
            if ($ga4_analytic_account_id == "" || isset($_GET['subscription_id'])) {
        ?>
                load_ga_accounts(tvc_data);
                jQuery('#ga4_property_id').html('<option value="">Select Measurement ID</option>');
        <?php
            }
        }

        ?>



        jQuery(".conv-enable-selection").click(function() {
            conv_change_loadingbar("show");
            jQuery(".conv-enable-selection").addClass('disabled');
            var selele = jQuery(".conv-enable-selection").closest(".conv-hideme-gasettings").find(
                "select.ga_analytic_account_id");
            var currele = jQuery(this).closest(".conv-hideme-gasettings").find(
                "select.ga_analytic_account_id");
            list_analytics_account(tvc_data, selele, currele);
        });

        jQuery(document).on('select2:select', '.ga_analytic_account_id', function(e) {
            if (jQuery(this).val() != "" && jQuery(this).val() != undefined) {
                conv_change_loadingbar("show");
                var account_id = jQuery(e.target).val();
                var acctype = jQuery(e.target).attr('acctype');
                var thisselid = e.target.getAttribute('id');
                // console.log(acctype);
                list_analytics_web_properties(acctype, tvc_data, account_id, thisselid);
                jQuery(".ga_analytic_account_id").closest(".conv-hideme-gasettings").find("select").prop(
                    "disabled", false);
            } else {
                jQuery(".ga_analytic_account_id").closest(".conv-hideme-gasettings").find("select").prop(
                    "disabled", false);
            }

        });

        jQuery(document).on("change", "form#gasettings_form", function() {
            <?php if ($cust_g_email != "") { ?>
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
                jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-google");
                jQuery(".conv-btn-connect").text('Save');
            <?php } else { ?>
                jQuery(".tvc_google_signinbtn").trigger("click");
            <?php } ?>
        });

        // Save data
        jQuery(document).on("click", ".conv-btn-connect-enabled-google", function() {
            var tracking_option = 'GA4'; //jQuery('input[type=radio][name=tracking_option]:checked').val();
            var box_id = "#analytics_box_" + tracking_option;
            var has_error = 0;
            var selected_vals = {};
            selected_vals["ua_analytic_account_id"] = "<?php echo esc_html($ua_analytic_account_id); ?>";
            selected_vals["property_id"] = "<?php echo esc_html($property_id); ?>";
            selected_vals["ga4_analytic_account_id"] = "";
            selected_vals["measurement_id"] = "";
            selected_vals["subscription_id"] = "<?php echo esc_html($tvc_data['subscription_id']) ?>";
            //= {ua_analytic_account_id: "", property_id: "", ga4_analytic_account_id: "", measurement_id: ""};
            jQuery(box_id).find("select").each(function() {
                if (!jQuery(this).val() || jQuery(this).val() == "" || jQuery(this).val() ==
                    "undefined") {
                    has_error = 1;
                    return;
                } else {
                    selected_vals[jQuery(this).attr('name')] = jQuery(this).val();
                }
            });
            selected_vals["tracking_option"] = tracking_option;
            selected_vals["ga4_api_secret"] = jQuery("#ga4_api_secret").val();
            selected_vals["conv_track_author"] = document.getElementById('conv_track_author').checked ? "1" : "0";
            selected_vals["conv_track_signin"] = document.getElementById('conv_track_signin').checked ? "1" : "0";
            selected_vals["conv_track_signup"] = document.getElementById('conv_track_signup').checked ? "1" : "0";
            selected_vals["conv_track_page_scroll"] = document.getElementById('conv_track_page_scroll').checked ? "1" : "0";
            selected_vals["conv_track_file_download"] = document.getElementById('conv_track_file_download').checked ? "1" : "0";
            // console.log(selected_vals);
            if (has_error == 1) {
                jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-enabled-google");
                jQuery(".conv-btn-connect").text('Save');
                alert("Please select required fields to continue.");
            } else {
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: {
                        action: "conv_save_pixel_data",
                        pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                        conv_options_data: selected_vals,
                        conv_options_type: ["eeoptions", "eeapidata", "middleware"],
                        conv_tvc_data: tvc_data,
                    },
                    beforeSend: function() {
                        jQuery(".conv-btn-connect-enabled-google").text("Saving...");
                        conv_change_loadingbar("show");
                        jQuery(this).addClass('disabled');
                    },
                    success: function(response) {
                        var user_modal_txt =
                            "Congratulations, you have successfully connected your";
                        var user_modal_txt2 = "<br>GA3 Account ID: " + selected_vals[
                            'property_id'];
                        var user_modal_txt3 = "<br>GA4 account ID: " + selected_vals[
                            'measurement_id'];

                        if (tracking_option == "BOTH") {
                            user_modal_txt = user_modal_txt + " " + user_modal_txt2 + " " +
                                user_modal_txt3;
                        }
                        if (tracking_option == "UA") {
                            user_modal_txt = user_modal_txt + " " + user_modal_txt2;
                        }
                        if (tracking_option == "GA4") {
                            user_modal_txt = user_modal_txt + " " + user_modal_txt3;
                        }

                        if (response == "0" || response == "1") {
                            jQuery(".conv-btn-connect-enabled-google").text("Connect");
                            jQuery("#conv_save_success_txt").html(user_modal_txt);
                            jQuery("#conv_save_success_modal").modal("show");
                        }

                    }
                });
            }

        });

    });
</script>