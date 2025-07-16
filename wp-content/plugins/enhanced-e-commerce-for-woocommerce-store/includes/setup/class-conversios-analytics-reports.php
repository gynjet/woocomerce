<?php
$TVC_Admin_Helper = new TVC_Admin_Helper();
$ee_options = unserialize(get_option('ee_options'));
$sch_email_toggle_check = isset($ee_options['sch_email_toggle_check']) ? sanitize_text_field($ee_options['sch_email_toggle_check']) : '1';
$sch_custom_email = isset($ee_options['sch_custom_email']) ? sanitize_text_field($ee_options['sch_custom_email']) : '';
$sch_email_frequency = isset($ee_options['sch_email_frequency']) ? sanitize_text_field($ee_options['sch_email_frequency']) : 'Weekly';
$g_mail = get_option('ee_customer_gmail');
$ga4_measurement_id = isset($ee_options['gm_id']) && $ee_options['gm_id'] != "" ? $ee_options['gm_id'] : "";
$google_ads_id = isset($ee_options['google_ads_id']) && $ee_options['google_ads_id'] != "" ? $ee_options['google_ads_id'] : "";
$last_fetched_prompt_date = isset($ee_options['last_fetched_prompt_date']) && $ee_options['last_fetched_prompt_date'] != "" ? $ee_options['last_fetched_prompt_date'] : "";
$ecom_reports_ga_currency = isset($ee_options['ecom_reports_ga_currency']) ? sanitize_text_field($ee_options['ecom_reports_ga_currency']) : '';
$ecom_reports_gads_currency = isset($ee_options['ecom_reports_gads_currency']) ? sanitize_text_field($ee_options['ecom_reports_gads_currency']) : '';

$subpage = (isset($_GET["subpage"]) && $_GET["subpage"] != "") ? sanitize_text_field(wp_unslash($_GET['subpage'])) : "ga4general";

$options = get_option("ee_options");
if ($options) {
    $options = is_array($options) ? $options : unserialize($options);
    if (!isset($options['save_email_bydefault'])) {
        $options['save_email_bydefault'] = null;
        update_option('ee_options', serialize($options));
    }
}
$report_settings_arr = array("ga4ecommerce", "gads", "ga4general");
if ($subpage == "ga4ecommerce") {
    $ga4page_cls = "btn-outline-primary";
    $gadspage_cls = "btn-outline-secondary alt-btn-reports";
    $ga4general_cls = "btn-outline-secondary alt-btn-reports";
} else if ($subpage == "gads") {
    $ga4page_cls = "btn-outline-secondary alt-btn-reports";
    $gadspage_cls = "btn-outline-primary";
    $ga4general_cls = "btn-outline-secondary alt-btn-reports";
} else if ($subpage == "ga4general") {
    $ga4page_cls = "btn-outline-secondary alt-btn-reports";
    $gadspage_cls = "btn-outline-secondary alt-btn-reports";
    $ga4general_cls = "btn-outline-primary";
}
?>
<div id="conv-report-main-div" class="container-fluid conv_report_mainbox p-4">

    <div class="row">
        <div class="d-flex">
            <div class="conv_pageheading d-flex align-items-end">
                <h2>
                    <?php esc_html_e("Analytics reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </h2>
                <h5 id="conv_pdf_logo" class="d-none ms-2">by <?php echo wp_kses(
                                                                    enhancad_get_plugin_image('/admin/images/logo.png', '', '', 'width:120px;'),
                                                                    array(
                                                                        'img' => array(
                                                                            'src' => true,
                                                                            'alt' => true,
                                                                            'class' => true,
                                                                            'style' => true,
                                                                        ),
                                                                    )
                                                                ); ?></h5>
            </div>
            <div class="ms-auto p-2 bd-highlight">
                <div id="reportrange" class="dshtpdaterange upgradetopro_badge d-flex" popupopener="generalreport">
                    <div class="dateclndicn">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/claendar-icon.png'),
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
                    <span class="daterangearea report_range_val"></span>
                    <div class="careticn">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/caret-down.png'),
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
            </div>
        </div>

        <div class="d-flex">

            <div class="conv_pageheading">
                <a href="admin.php?page=conversios-analytics-reports" class="btn <?php echo esc_attr($ga4general_cls); ?> bg-white me-3">
                    <?php esc_html_e("General Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </a>
                <a class="btn <?php echo esc_attr($ga4page_cls); ?> bg-white me-3" data-bs-toggle="modal" data-bs-target="#upgradetopromodalotherReports">
                    <?php esc_html_e("Ecommerce Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </a>
                <a class="btn <?php echo esc_attr($gadspage_cls); ?> bg-white me-3" data-bs-toggle="modal" data-bs-target="#upgradetopromodalotherReports">
                    <?php esc_html_e("Google Ads Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </a>
                <a class="btn <?php echo esc_attr($gadspage_cls); ?> bg-white me-3" data-bs-toggle="modal" data-bs-target="#upgradetopromodalotherReports">
                    <?php esc_html_e("Facebook (Meta) Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </a>
            </div>
            <?php if ($ga4_measurement_id != "" && $g_mail != "") { ?>
                <div id="conv_report_opright" class="ms-auto p-2 bd-highlight d-flex">
                    <h4 class="conv-link-blue d-flex pe-2" data-bs-toggle="modal" data-bs-target="#schedule_email_modal">
                        <span class="material-symbols-outlined conv-link-blue pe-1">check_circle</span>
                        <?php esc_html_e("Schedule Email", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </h4>
                    <h4 class="conv-link-blue d-flex" data-bs-toggle="modal" data-bs-target="#convpdflogoModal">
                        <span class="material-symbols-outlined conv-link-blue pe-1">cloud_download</span>
                        <?php esc_html_e("Download PDF", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </h4>
                </div>
            <?php } ?>
        </div>

        <?php if ($subpage == "ga4general" && (empty($g_mail) || empty($ga4_measurement_id))) { ?>
            <div class="alert alert-info mt-4 w-100" role="alert">
                <div class="mx-auto" style="max-width: 600px;">
                    <h5 class="alert-heading">Connect Google Analytics to View Reports</h5>
                    <p>To view reports in the plugin, please connect your Google account and complete the Google Analytics setup:</p>
                    <ol class="ms-0">
                        <li>Click the button below to start the connection.</li>
                        <li>Authorize access through the Google authentication screen.</li>
                        <li>Select your <strong>Google Analytics Account ID</strong>.</li>
                        <li>Choose your <strong>Measurement ID</strong> and click <strong>Save</strong>.</li>
                        <li>After saving, a success message will appear. Click <strong>"View Reports"</strong> to access your analytics.</li>
                    </ol>
                    <div class="mt-3">
                        <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage=gasettings"'); ?>" class="btn btn-primary">Click here to connect Google</a>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php
        if (in_array($subpage, $report_settings_arr)) {
            require_once(ENHANCAD_PLUGIN_DIR . "admin/partials/reports/" . $subpage . '.php');
        }
        ?>
        <!-- All report section -->

    </div>
</div>
</div>
<!-- logo modal -->
<div class="modal fade" id="convpdflogoModal" tabindex="-1" aria-labelledby="convpdflogoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="convpdflogoModalLabel">Download Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Size Message -->
            <div class="alert alert-success text-center text-underline text-success">
                <a href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=modal_setlogo&utm_campaign=upgrade" target="_blank">
                    Upgrade to Premium to set your logo in report PDF
                </a>
            </div>

            <!-- Modal Body -->
            <div class="modal-body d-flex justify-content-center align-items-center flex-column disabledsection">
                <!-- Image Preview Container -->
                <div id="image-preview-container" class="border d-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 36px; background-color: #f8f9fa;">
                    <span id="no-image-text" class="text-muted small">No image selected</span>
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/claendar-icon.png', 'Selected Media Preview', 'd-none img-fluid', 'max-width: 120px; max-height: 36px;', 'selected-media-preview'),
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

                <!-- Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <button id="select-media-button" class="btn btn-outline-primary me-2">
                        <i class="bi bi-upload"></i> Select Logo
                    </button>
                    <input type="hidden" id="attachment-id" name="attachment_id" value="">
                    <button id="save-logo-button" class="btn btn-success">
                        <i class="bi bi-save"></i> Save
                    </button>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" id="conv-download-pdf" class="btn btn-primary w-100">
                    <i class="bi bi-file-earmark-pdf"></i>Download Now
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Schedule Email Modal box -->
<div class="modal email-modal fade" id="schedule_email_modal" tabindex="-1" aria-labelledby="schedule_email_modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div id="loadingbar_blue" class="progress-materializecss" style="display: none;">
            <div class="indeterminate"></div>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <div class="scheduleemail-box">
                    <h2><?php esc_html_e("Smart Emails", "enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                    <p>
                        <?php esc_html_e("Schedule your Google Analytics 4 Insight Report email for", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <br>
                        <?php esc_html_e("data-driven insights", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <?php
                    if ($sch_email_toggle_check == '0') { //enabled
                        $switch_cls = 'convEmail_default_cls_enabled';
                        $switch_checked = 'checked';
                        $txtcls = "form-fields-dark";
                    } else { //disabled
                        $switch_cls = 'convEmail_default_cls_disabled';
                        $switch_checked = '';
                        $txtcls = "form-fields-light";
                    } ?>
                    <div class="schedule-formbox">
                        <div class="toggle-switch">
                            <div class="form-check form-switch">
                                <div class="form-check form-switch">
                                    <label id="email_toggle_btnLabel" for="email_toggle_btn" class="form-check-input switch <?php echo esc_attr($switch_cls); ?>" role="switch">
                                        <input id="email_toggle_btn" type="checkbox" class="<?php echo esc_attr($switch_cls); ?>" <?php echo esc_attr($switch_checked); ?>>
                                        <div></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-wholebox">
                            <div class="form-box">
                                <label for="custom_email" class="form-label llabel"><?php esc_html_e("Email address", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                                <input type="email" class="form-control icontrol <?php echo esc_attr($txtcls); ?>" id="custom_email" aria-describedby="emailHelp" placeholder="user@gmail.com" value="<?php echo esc_attr($g_mail); ?>" disabled readonly>
                            </div>
                            <div class="form-box">
                                <h5>
                                    <?php esc_html_e("To get emails on your alternate address. ", "enhanced-e-commerce-for-woocommerce-store"); ?><a style="color:  #1085F1;cursor: pointer;" href="https://www.conversios.io/pricing/?utm_source=EE+Plugin+User+Interface&amp;utm_medium=dashboard&amp;utm_campaign=Upsell+at+Conversios" target="_blank"><?php esc_html_e("Upgrade To Pro", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                </h5>
                            </div>
                            <div class="form-box">
                                <label for="email_frequency" class="form-label llabel">
                                    <?php esc_html_e("Email Frequency", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <input type="text" class="form-control icontrol <?php echo esc_attr($txtcls); ?>" id="email_frequency" value="<?php echo esc_attr($sch_email_frequency); ?>" disabled readonly>
                                <div id="email_frequency_arrow" class="down-arrow"></div>
                            </div>

                            <div class="form-box">
                                <h5>
                                    <?php esc_html_e("By default, you will receive a Weekly report in your email inbox.", "enhanced-e-commerce-for-woocommerce-store"); ?><br><?php esc_html_e("To get report ", "enhanced-e-commerce-for-woocommerce-store"); ?><strong>Daily</strong>
                                    . <a href="https://www.conversios.io/pricing/?utm_source=EE+Plugin+User+Interface&amp;utm_medium=dashboard&amp;utm_campaign=Upsell+at+Conversios" target="_blank" style="color:  #1085F1;"><?php esc_html_e("Upgrade To Pro", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                </h5>
                            </div>
                            <div class="form-box">
                                <div class="save">
                                    <button id="schedule_email_save_config" class="btn  save-btn"><?php esc_html_e("Save", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                                </div>
                            </div>
                            <div class="form-box">
                                <div class="save">
                                    <span id="err_sch_msg" style="display: none;color: red;position: absolute;top: -9px;"><?php esc_html_e("Something went wrong, please try again later.", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                                </div>
                            </div>

                            <div id="schedule_email_alert" class="d-none">
                                <div class="alert alert-info" role="alert">
                                    <div id="schedule_email_alert_msg"></div>
                                    <div role="button" class="fw-bold pt-3" data-bs-dismiss="modal">Click here to close
                                        the popup</div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!--schedule modal end-->


<div class="modal fade" id="upgradetopromodalotherReports" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="position:relative;border-radius:16px;">
            <div class="modal-body p-4 pb-0">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <img width="200" height="200"
                        src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/upgrade-pro-reporting.png'); ?>" />
                    <h2 class="text-fw-bold">Upgrade to Pro Now</h2>
                    <span class="text-secondary text-center">Unlock this premium report with our <span
                            class="fw-bold">Pro version!</span> Upgrade now for comprehensive insights and advanced
                        analytics.</span>
                </div>
            </div>
            <div class="border-0 pb-4 mb-1 pt-4 d-flex flex-row justify-content-center align-items-center p-2">
                <a class="btn bg-white text-black m-auto w-100 mx-2 ms-4 p-2" style="border: 1px solid black;" data-bs-dismiss="modal">
                    <?php esc_html_e("Close", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </a>
                <a id="upgradetopro_modal_link" class="btn conv-yellow-bg m-auto w-100 mx-2 me-4 p-2"
                    href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=modal_popup&utm_campaign=upgrade"
                    target="_blank">
                    <?php esc_html_e("Upgrade Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    var start = moment().subtract(45, 'days');
    var end = moment().subtract(1, 'days');
    var start_date = "";
    var end_date = "";

    <?php if (!$ga4_measurement_id == "" && !empty($g_mail)) { ?>
        cb(start, end);
    <?php } ?>


    // Schedule email
    function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!regex.test(email)) {
            return false;
        } else {
            return true;
        }
    }

    function save_local_data(email_toggle_check, custom_email, email_frequency) {
        var selected_vals = {};
        selected_vals['sch_email_toggle_check'] = email_toggle_check;
        selected_vals['sch_custom_email'] = custom_email;
        selected_vals['sch_email_frequency'] = email_frequency;
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "conv_save_pixel_data",
                pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                conv_options_data: selected_vals,
                conv_options_type: ["eeoptions"]
            },
            beforeSend: function() {},
            success: function(response) {
                console.log('Email setting saved in db');
            }
        });
    }
    jQuery(document).ready(function() {
        jQuery("#navbarSupportedContent ul li").removeClass("rich-blue");
        jQuery('#navbarSupportedContent ul > li').eq(0).addClass('rich-blue');

        var save_email_bydefault = '<?php echo esc_js($options["save_email_bydefault"] ?? ""); ?>';
        if (save_email_bydefault === "") {
            let email_toggle_check = '0'; //default
            let custom_email = '<?php echo esc_attr($g_mail); ?>';
            let email_frequency = "Weekly";
            let email_frequency_final = "7_day";
            var data = {
                "action": "set_email_configurationGA4",
                "is_disabled": email_toggle_check,
                "custom_email": custom_email,
                "email_frequency": email_frequency_final,
                "save_email_bydefault": "1",
                "conversios_nonce": '<?php echo esc_js(wp_create_nonce('conversios_nonce')); ?>'
            };
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {
                    jQuery("#loadingbar_blue").show();
                },
                success: function(response) {
                    if (response.error == false) {
                        jQuery("#err_sch_msg").hide();
                        jQuery("#loadingbar_blue").hide();
                        if (email_toggle_check == "0") {
                            jQuery("#schedule_email_alert_msg").html(
                                "Successfully subscribed to receive analytics reports in your email");
                        } else {
                            jQuery("#schedule_email_alert_msg").html("Successfully Unsubscribed");
                        }

                        jQuery("#schedule_email_alert").removeClass("d-none");

                        jQuery('#sch_ack_msg').show();
                        //local storage
                        save_local_data(email_toggle_check, custom_email, email_frequency);
                        if (email_toggle_check == '0') {
                            jQuery('#schedule_form_btn_set').show();
                            jQuery('#schedule_form_btn_raw').hide();
                        } else {
                            jQuery('#schedule_form_btn_set').hide();
                            jQuery('#schedule_form_btn_raw').show();
                        }
                    } else {
                        jQuery("#err_sch_msg").show();
                        jQuery("#loadingbar_blue").hide();
                    }
                    setTimeout(
                        function() {
                            jQuery("#sch_ack_msg").hide();
                        }, 8000);
                }
            });
        }
    });
    /*schedule email form submit event listner*/
    jQuery("#schedule_email_save_config").on("click", function() {
        let email_toggle_check = '0'; //default
        if (jQuery("#email_toggle_btn").prop("checked")) {
            email_toggle_check = '0'; //enabled
        } else {
            email_toggle_check = '1'; //disabled
        }
        let custom_email = '<?php echo esc_attr($g_mail); ?>';
        let email_frequency = "Weekly";
        let email_frequency_final = "7_day";
        var data = {
            "action": "set_email_configurationGA4",
            "is_disabled": email_toggle_check,
            "custom_email": custom_email,
            "email_frequency": email_frequency_final,
            "conversios_nonce": '<?php echo esc_js(wp_create_nonce('conversios_nonce')); ?>'
        };
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                jQuery("#loadingbar_blue").show();
            },
            success: function(response) {
                if (response.error == false) {
                    jQuery("#err_sch_msg").hide();
                    jQuery("#loadingbar_blue").hide();
                    if (email_toggle_check == "0") {
                        jQuery("#schedule_email_alert_msg").html(
                            "Successfully subscribed to receive analytics reports in your email");
                    } else {
                        jQuery("#schedule_email_alert_msg").html("Successfully Unsubscribed");
                    }

                    jQuery("#schedule_email_alert").removeClass("d-none");

                    jQuery('#sch_ack_msg').show();
                    //local storage
                    save_local_data(email_toggle_check, custom_email, email_frequency);
                    if (email_toggle_check == '0') {
                        jQuery('#schedule_form_btn_set').show();
                        jQuery('#schedule_form_btn_raw').hide();
                    } else {
                        jQuery('#schedule_form_btn_set').hide();
                        jQuery('#schedule_form_btn_raw').show();
                    }
                } else {
                    jQuery("#err_sch_msg").show();
                    jQuery("#loadingbar_blue").hide();
                }
                setTimeout(
                    function() {
                        jQuery("#sch_ack_msg").hide();
                    }, 8000);
            }
        });
    });
    jQuery("#sch_ack_msg_close").on("click", function() {
        jQuery("#sch_ack_msg").hide();
    });
    jQuery('#email_toggle_btn').change(function() {
        if (jQuery(this).prop("checked")) {
            jQuery("#email_toggle_btnLabel").addClass("convEmail_default_cls_enabled");
            jQuery("#email_toggle_btnLabel").removeClass("convEmail_default_cls_disabled");
            jQuery("#email_frequency,#custom_email").attr("style", "color: #2A2D2F !important");
            jQuery("#schedule_email_save_config").html('Save Changes');
        } else {
            jQuery("#email_toggle_btnLabel").addClass("convEmail_default_cls_disabled");
            jQuery("#email_toggle_btnLabel").removeClass("convEmail_default_cls_enabled");
            jQuery("#email_frequency,#custom_email").attr("style", "color: #94979A !important");
            jQuery("#schedule_email_save_config").html('Save Changes');
        }
    });
    jQuery(function() {
        jQuery('#conv-download-pdf').click(function() {
            jQuery("#conv_report_opright").addClass("d-none");
            jQuery("#conv-download-pdf").addClass("disabledsection");
            jQuery("#conv_pdf_logo").removeClass('d-none');
            const element = document.getElementById('conv-report-main-div');
            const watermarkURL = "<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logo.png'); ?>";

            html2canvas(element, {
                scale: 2,
                useCORS: true,
            }).then(function(canvas) {
                const imgData = canvas.toDataURL('image/jpeg');
                const {
                    jsPDF
                } = window.jspdf;

                const canvasWidth = canvas.width;
                const canvasHeight = canvas.height;

                const pdfWidth = (canvasWidth * 25.4) / 96; // Convert canvas width from px to mm
                const pdfHeight = (canvasHeight * 25.4) / 96;

                const pdf = new jsPDF('p', 'mm', [pdfWidth, pdfHeight]);

                // Add the main content image
                pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);

                // Load the watermark image and add it to the center
                const watermark = new Image();
                watermark.src = watermarkURL;
                watermark.onload = function() {
                    const wmWidth = pdfWidth * 0.7; // 50% of PDF width
                    const wmHeight = (watermark.height / watermark.width) * wmWidth;
                    const wmX = (pdfWidth - wmWidth) / 1.3; // Center horizontally
                    const wmY = (pdfHeight - wmHeight) / 1.6; // Center vertically

                    pdf.setGState(new pdf.GState({
                        opacity: 0.1
                    })); // Set low opacity
                    pdf.addImage(watermark, 'PNG', wmX, wmY, wmWidth, wmHeight, undefined, 'NONE', 45);
                    pdf.save('ConversiosGA4Report.pdf');
                    jQuery("#conv_pdf_logo").addClass('d-none');
                };
            });
            jQuery("#conv-download-pdf").removeClass("disabledsection");
            jQuery("#conv_report_opright").removeClass("d-none");
        });
    });
</script>