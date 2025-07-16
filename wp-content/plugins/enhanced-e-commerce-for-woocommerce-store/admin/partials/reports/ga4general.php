<div class="conv_reportsec_box g-0">
    <div id="conv_gridrepbox_row" class="row px-0">
        <input type="hidden" id="ecom_reports_ga_currency" value="">
        <h4 class="pt-3"><?php esc_html_e("Overview Report", "enhanced-e-commerce-for-woocommerce-store") ?></h4>
        <div class="col-xs-12 col-sm-6 col-md-3 col-xl-3 col-xxl conv_rep_gridbox mb-4 conv_rgrid_sessions">
            <div class="bg-white conv_rgrid_in conv_bordershadow rounded py-2 px-3 placeholder-glow">
                <h5 class="conv-text-blue mb-0 text-truncate d-flex">
                    <?php esc_html_e("Sessions", "enhanced-e-commerce-for-woocommerce-store") ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("A session is a collection of user interactions with your site or app within a specified time frame.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                        info
                    </span>
                </h5>
                <div class="conv_rgrid_dydata">
                    <div class="conv_rgrid_data d-flex align-items-center py-2">
                        <div class="conv_rgrid_data_num">N/A</div>
                        <div class="conv_rgrid_data_icon">|</div>
                        <div class="conv_rgrid_data_per">N/A</div>
                    </div>
                    <div class="conv_rgrid_data_compari"></div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3 col-xl-3 col-xxl conv_rep_gridbox mb-4 conv_rgrid_newUsers">
            <div class="bg-white conv_rgrid_in conv_bordershadow rounded py-2 px-3 placeholder-glow">
                <h5 class="conv-text-blue mb-0  text-truncate d-flex">
                    <?php esc_html_e("New Users", "enhanced-e-commerce-for-woocommerce-store") ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Users visiting your site/app for the first time.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                        info
                    </span>
                </h5>
                <div class="conv_rgrid_dydata">
                    <div class="conv_rgrid_data d-flex align-items-center py-2">
                        <div class="conv_rgrid_data_num">N/A</div>
                        <div class="conv_rgrid_data_icon">|</div>
                        <div class="conv_rgrid_data_per">N/A</div>
                    </div>
                    <div class="conv_rgrid_data_compari"></div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3 col-xl-3 col-xxl conv_rep_gridbox mb-4 conv_rgrid_avgduration">
            <div class="bg-white conv_rgrid_in conv_bordershadow rounded py-2 px-3 placeholder-glow">
                <h5 class="conv-text-blue mb-0  text-truncate d-flex">
                    <?php esc_html_e("Average Session Duration", "enhanced-e-commerce-for-woocommerce-store") ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Average Session Duration is the average time users spend on a website or app.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                        info
                    </span>
                </h5>
                <div class="conv_rgrid_dydata">
                    <div class="conv_rgrid_data d-flex align-items-center py-2">
                        <div class="conv_rgrid_data_num">N/A</div>
                        <div class="conv_rgrid_data_icon">|</div>
                        <div class="conv_rgrid_data_per">N/A</div>
                    </div>
                    <div class="conv_rgrid_data_compari"></div>
                </div>
            </div>
        </div>


        <div class="col-xs-12 col-sm-6 col-md-3 col-xl-3 col-xxl conv_rep_gridbox mb-4 conv_rgrid_bounce">
            <div class="bg-white conv_rgrid_in conv_bordershadow rounded py-2 px-3 placeholder-glow">
                <h5 class="conv-text-blue mb-0 text-truncate d-flex">
                    <?php esc_html_e("Bounce Rate", "enhanced-e-commerce-for-woocommerce-store") ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Percentage of single-page sessions with no conversions or additional page views within 10 seconds.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                        info
                    </span>
                </h5>
                <div class="conv_rgrid_dydata">
                    <div class="conv_rgrid_data d-flex align-items-center py-2">
                        <div class="conv_rgrid_data_num">N/A</div>
                        <div class="conv_rgrid_data_icon">|</div>
                        <div class="conv_rgrid_data_per">N/A</div>
                    </div>
                    <div class="conv_rgrid_data_compari"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row px-0">
        <!-- Audience reports Start-->

        <div class="col-xs-12 col-sm-12 col-md-12 col-xl-12 col-xxl-12 conv_report_tabs">
            <h4 class="pt-3 d-flex">
                <?php esc_html_e("Audience Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Analyze user demographics, interests, and behavior with the Audience report to understand user groups interacting with your website or app.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                    info
                </span>
            </h4>
            <div class="bg-white conv_bordershadow rounded">
                <div class="text-secondary text-center pt-3 fs-7 fw-bold">
                    <?php esc_html_e("Click the colored tabs below to show or hide data.", "enhanced-e-commerce-for-woocommerce-store") ?>
                </div>
                <div>
                    <canvas id="conv_audience_chart" class="p-3 pt-1" height="300"></canvas>
                </div>
            </div>

            <!-- Audience reports End -->
        </div>
        <div class="row px-0">
            <!-- Path reports Start-->

            <div class="col-xs-12 col-sm-12 col-md-12 col-xl-12 col-xxl-12 conv_report_tabs">
                <h4 class="pt-3 d-flex">
                    <?php esc_html_e("Top Pages", "enhanced-e-commerce-for-woocommerce-store") ?>
                    <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Shows the sequence of pages visited by users and the actions performed.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                        info
                    </span>
                </h4>
                <div class="tab-pane fade show bg-white conv_bordershadow rounded" id="pills-convsomedr" role="tabpanel" aria-labelledby="pills-convsomedr-tab">
                    <div class="table-responsive-fixed h-auto" style="border-radius: 20px;">
                        <table id="conv_page_report_tbl" class="table-borderless w-100" style="border-radius: 20px;">
                            <thead class="border-bottom">
                                <tr>
                                    <th class="text-start p-3 pt-2 text-truncate d-flex justify-content-start">
                                        <?php esc_html_e("Page Title", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </div>
                    </th>
                    <th class="text-end p-1 text-truncate">
                        <?php esc_html_e("Page Path", "enhanced-e-commerce-for-woocommerce-store") ?></th>
                    <th class="text-end p-1 text-truncate d-flex flex-row-reverse">
                        <div class="conv_rgrid_data_icon conv_rgrid_data_icon_black">
                            <?php echo wp_kses(
                                enhancad_get_plugin_image('/admin/images/green-up.png'),
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
                        <?php esc_html_e("Page Views", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </th>
                    <th class="text-end p-1 text-truncate">
                        <?php esc_html_e("Users", "enhanced-e-commerce-for-woocommerce-store") ?></th>
                    <th class="text-end p-1 text-truncate">
                        <?php esc_html_e("Sessions", "enhanced-e-commerce-for-woocommerce-store") ?></th>
                    <th class="text-end p-1 text-truncate">
                        <?php esc_html_e("Average Session Duration", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </th>
                    <th class="text-end p-1 text-truncate">
                        <?php esc_html_e("Events", "enhanced-e-commerce-for-woocommerce-store") ?></th>
                    <th class="text-end p-1 text-truncate">
                        <?php esc_html_e("Bounce Rate", "enhanced-e-commerce-for-woocommerce-store") ?></th>
                    </tr>
                    </thead>
                    <tbody class="placeholder-glow fixedh_tbody">
                        <tr class="placeholder d-table-row border-bottom">
                            <td colspan="8" class="p-3">1</td>
                        </tr>
                        <tr class="placeholder d-table-row border-bottom">
                            <td colspan="8" class="p-3">1</td>
                        </tr>
                        <tr class="placeholder d-table-row border-bottom">
                            <td colspan="8" class="p-3">1</td>
                        </tr>
                        <tr class="placeholder d-table-row border-bottom">
                            <td colspan="8" class="p-3">1</td>
                        </tr>
                        <tr class="placeholder d-table-row border-bottom">
                            <td colspan="8" class="p-3">1</td>
                        </tr>

                    </tbody>
                    </table>
                    <a class="conv_viewfre_link conv-link-blue float-end p-3 fw-bold upgradetopro_badge" popupopener="generalreport">
                        View Full Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Path reports End -->
    </div>
    <div class="row px-0">
        <!-- Device used reports Start-->
        <div class="col-xs-4 col-sm-4 col-md-4 col-xl-4 col-xxl-4 conv_report_tabs">
            <h4 class="pt-3 d-flex">
                <?php esc_html_e("Device Breakdown", "enhanced-e-commerce-for-woocommerce-store") ?>
                <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Groups users by device type (desktop, mobile, etc.).", "enhanced-e-commerce-for-woocommerce-store") ?>">
                    info
                </span>
            </h4>
            <div class="bg-white conv_bordershadow rounded p-2">
                <canvas id="conv_device_chart" height="300"></canvas>
            </div>
        </div>
        <!-- device used reports End -->

        <!-- top country Start-->
        <div class="col-xs-4 col-sm-4 col-md-4 col-xl-4 col-xxl-4 conv_report_tabs">
            <h4 class="pt-3 d-flex">
                <?php esc_html_e("Top Countries", "enhanced-e-commerce-for-woocommerce-store") ?>
                <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Identifies geographic locations of your users based on their countries.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                    info
                </span>
            </h4>
            <div class="tab-pane fade show bg-white conv_bordershadow rounded" id="pills-convsomedr" role="tabpanel" aria-labelledby="pills-convsomedr-tab" style="height: 317px;">
                <div class="table-responsive-fixed" style="border-radius: 20px;">
                    <table id="conv_top_country_tbl" class="table-borderless w-100">
                        <thead class="border-bottom">
                            <tr>
                                <th class="text-start p-3 pt-2 text-truncate d-flex justify-content-start">
                                    <?php esc_html_e("Countries", "enhanced-e-commerce-for-woocommerce-store") ?>
                </div>
                </th>
                <th class="text-end p-1 text-truncate">
                    <?php esc_html_e("Users", "enhanced-e-commerce-for-woocommerce-store") ?></th>
                <th class="text-end p-1 text-truncate d-flex flex-row-reverse">
                    <?php esc_html_e("Page Views", "enhanced-e-commerce-for-woocommerce-store") ?></th>
                </tr>
                </thead>
                <tbody class="placeholder-glow fixedh_tbody">
                    <tr class="placeholder d-table-row border-bottom">
                        <td colspan="3" class="p-3">1</td>
                    </tr>
                    <tr class="placeholder d-table-row border-bottom">
                        <td colspan="3" class="p-3">1</td>
                    </tr>
                    <tr class="placeholder d-table-row border-bottom">
                        <td colspan="3" class="p-3">1</td>
                    </tr>
                    <tr class="placeholder d-table-row border-bottom">
                        <td colspan="3" class="p-3">1</td>
                    </tr>
                    <tr class="placeholder d-table-row border-bottom">
                        <td colspan="3" class="p-3">1</td>
                    </tr>
                </tbody>
                </table>
                <!--<a href="admin.php?page=conversios-analytics-reports&tab=campaign-performance-report" class="conv_viewfre_link conv-link-blue float-end p-3 fw-bold">View Full Report</a> -->
            </div>
        </div>
    </div>
    <!-- top country End -->

    <!-- top city Start-->
    <div class="col-xs-4 col-sm-4 col-md-4 col-xl-4 col-xxl-4 conv_report_tabs">
        <h4 class="pt-3 d-flex">
            <?php esc_html_e("Top Cities", "enhanced-e-commerce-for-woocommerce-store") ?>
            <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Identifies geographic locations of your users based on their cities.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                info
            </span>
        </h4>
        <div class="tab-pane fade show bg-white conv_bordershadow rounded" id="pills-convsomedr" role="tabpanel" aria-labelledby="pills-convsomedr-tab" style="height: 317px;">
            <div class="table-responsive-fixed" style="border-radius: 20px;">
                <table id="conv_top_city_tbl" class="table-borderless w-100">
                    <thead class="border-bottom">
                        <tr>
                            <th class="text-start p-3 pt-2 text-truncate d-flex justify-content-start">
                                <?php esc_html_e("Cities", "enhanced-e-commerce-for-woocommerce-store") ?>
            </div>
            </th>
            <th class="text-end p-1 text-truncate">
                <?php esc_html_e("Users", "enhanced-e-commerce-for-woocommerce-store") ?></th>
            <th class="text-end p-1 text-truncate d-flex flex-row-reverse">
                <?php esc_html_e("Page Views", "enhanced-e-commerce-for-woocommerce-store") ?></th>
            </tr>
            </thead>
            <tbody class="placeholder-glow fixedh_tbody">
                <tr class="placeholder d-table-row border-bottom">
                    <td colspan="3" class="p-3">1</td>
                </tr>
                <tr class="placeholder d-table-row border-bottom">
                    <td colspan="3" class="p-3">1</td>
                </tr>
                <tr class="placeholder d-table-row border-bottom">
                    <td colspan="3" class="p-3">1</td>
                </tr>
                <tr class="placeholder d-table-row border-bottom">
                    <td colspan="3" class="p-3">1</td>
                </tr>
                <tr class="placeholder d-table-row border-bottom">
                    <td colspan="3" class="p-3">1</td>
                </tr>
            </tbody>
            </table>
            <!--<a href="admin.php?page=conversios-analytics-reports&tab=page-path-report" class="conv_viewfre_link conv-link-blue float-end p-3 fw-bold">View Full Report</a> -->
        </div>
    </div>
</div>
<!-- top city End -->
</div>
<div class="row px-0">
    <!-- Realtime reports Start-->
    <div class="col-xs-8 col-sm-8 col-md-8 col-xl-8 col-xxl-8 conv_report_tabs">
        <h4 class="pt-3 d-flex">
            <?php esc_html_e("Realtime Report (Last 30 minutes)", "enhanced-e-commerce-for-woocommerce-store") ?>
            <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Shows user activity on your site/app as it happens in last 30 minutes.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                info
            </span>
        </h4>
        <div class="bg-white conv_bordershadow rounded p-2">

            <canvas id="conv_realtime_chart" height="300"></canvas>
        </div>
    </div>
    <!-- Realtime reports end -->

    <!-- User report start-->
    <div class="col-xs-4 col-sm-4 col-md-4 col-xl-4 col-xxl-4 conv_report_tabs">
        <h4 class="pt-3 d-flex">
            <?php esc_html_e("Users Report", "enhanced-e-commerce-for-woocommerce-store") ?>
            <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Shows bifurcation of users in terms of new and returning users.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                info
            </span>
        </h4>
        <div class="bg-white conv_bordershadow rounded p-2">
            <canvas id="conv_users_chart" height="300"></canvas>
        </div>
    </div>
    <!-- User report end-->
</div>
<div class="row px-0">
    <!-- daily visitors start-->
    <div class="col-xs-12 col-sm-12 col-md-12 col-xl-12 col-xxl-12 conv_report_tabs">
        <h4 class="pt-3 d-flex">
            <?php esc_html_e("Daily Vistors Report", "enhanced-e-commerce-for-woocommerce-store") ?>
            <span class="material-symbols-outlined text-secondary md-18 ps-1 align-self-center" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="<?php esc_html_e("Shows the number of users who visited your site/app each day.", "enhanced-e-commerce-for-woocommerce-store") ?>">
                info
            </span>
        </h4>
        <div class="bg-white conv_bordershadow rounded">
            <div class="d-flex">
                <div id="conv_date_diff" class="p-2 fs-4 fw-bold"></div>
                <div id="conv_daily_visitors_total" class="p-2 fs-4 fw-bold"></div>
            </div>


            <div>
                <canvas id="conv_daily_visitors_chart" class="p-3" height="300"></canvas>
            </div>
        </div>
    </div>
    <!-- daily visitors end-->
</div>
</div>


<?php if (!$ga4_measurement_id == "" && !empty($g_mail)) { ?>

    <script>
        jQuery(document).ready(function($) {
            // Check if the bar has been closed before using localStorage
            if (!localStorage.getItem('feedbackConvBarClosed')) {
                setTimeout(function() {
                    $('#feedback-bar').addClass('show');
                    console.log('show');
                }, 5000);
            }

            // Close the bar and never show again
            $('#close-feedback-bar').click(function() {
                $('#feedback-bar').removeClass('show'); // Hide the bar
                localStorage.setItem('feedbackConvBarClosed', 'true'); // Store the close action
            });
        });

        jQuery('#conv_reporttab_pill').on('show.bs.tab', function() {
            let tabid = event.target.id;

            jQuery("#conv_reporttab_pillContent .conv_ai_tab").addClass('d-none');

            if (tabid == "pills-convprodperr-tab") {
                jQuery("#pills-convprodperr-ai").removeClass("d-none");
                jQuery("#conv_aibox_subheading").html("Product Performance");

                if (jQuery("#pills-convprodperr-ai-check").val() == "0") {
                    conv_generate_ai_insight("ProductConv15");
                    jQuery("#pills-convprodperr-ai-check").val("1");
                }
            }

            if (tabid == "pills-convopr-tab") {
                jQuery("#pills-convopr-ai").removeClass("d-none");
                jQuery("#conv_aibox_subheading").html("Order Performance");

                if (jQuery("#pills-convopr-ai-check").val() == 0) {
                    conv_generate_ai_insight("OrderPerformanceAnalysis");
                    jQuery("#pills-convopr-ai-check").val("1");
                }
            }

            if (tabid == "pills-convsomedr-tab") {
                jQuery("#pills-convsomedr-ai").removeClass("d-none");
                jQuery("#conv_aibox_subheading").html("Source & Medium Performance");
            }
        });

        function conv_GetCurrencySymbol(locale, currency) {
            return (0).toLocaleString(
                locale, {
                    style: 'currency',
                    currency: currency,
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }
            ).replace(/\d/g, '').trim()
        }

        function conv_changeplaceholder(status = "hide") {
            if (status == "hide") {
                jQuery(".conv_rgrid_dydata").removeClass("placeholder bg-light");
            }
            if (status == "show") {
                jQuery(".conv_rgrid_dydata").addClass("placeholder bg-light");
            }
        }

        function conv_format_revenue(conv_val) {
            return parseFloat(conv_val).toFixed(2);
        }

        function conv_format_revenue_percent(conv_val) {
            return parseFloat(conv_val * 100).toFixed(2);
        }

        function conv_report_percentage(partialValue, totalValue) {
            if (partialValue != 0 && totalValue != 0) {
                var reportperc = (100 * parseInt(partialValue)) / parseInt(totalValue);
                reportperc = "(" + reportperc.toFixed(2) + "%" + ")";
                return reportperc;
            } else {
                return "(0%)";
            }
        }

        function conv_setupdownarrow(gvalue, classname) {
            gvalue = String(gvalue);
            if (gvalue !== 0 && gvalue !== undefined && gvalue !== null && gvalue !== "0") {
                if (gvalue.startsWith("-")) {
                    jQuery("." + classname + " .conv_rgrid_data_icon").html(
                        '<img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/red-down.png'); ?>" alt="" />'
                    );
                } else {
                    jQuery("." + classname + " .conv_rgrid_data_icon").html(
                        '<img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/green-up.png'); ?>" alt="" />'
                    );
                }
            } else {
                jQuery("." + classname + " .conv_rgrid_data_icon").html('|');
            }

        }

        function save_currency_local(ga_currency) {
            var selected_vals = {};
            selected_vals['ecom_reports_ga_currency'] = ga_currency;
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
                    console.log('currency saved', response);
                }
            });
        }

        function get_ga4_general_grid_reports(post_data) {
            conv_changeplaceholder("show");
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: post_data,
                success: function(response) {
                    console.log("grid response", response);
                    if (response?.error == false) {

                        let presentdata = response.data_present[0];
                        let pastdata = response.data_past[0];
                        //console.log("check",presentdata);
                        //console.log("check",pastdata);
                        if (presentdata != undefined && pastdata != undefined) {
                            let sessions_diff = presentdata.sessions - pastdata.sessions;
                            let sessions_cmp = (sessions_diff != 0 && pastdata.sessions != 0) ? (sessions_diff / pastdata.sessions) * 100 : 0;

                            jQuery(".conv_rgrid_sessions .conv_rgrid_data_num").html(presentdata.sessions);
                            jQuery(".conv_rgrid_sessions .conv_rgrid_data_per").html(sessions_cmp.toFixed(2) + "%");
                            conv_setupdownarrow(sessions_cmp, 'conv_rgrid_sessions');

                            let newUsers_diff = presentdata.newUsers - pastdata.newUsers;
                            let newUsers_cmp = (newUsers_diff != 0 && pastdata.newUsers != 0) ? (newUsers_diff / pastdata.newUsers) * 100 : 0;

                            jQuery(".conv_rgrid_newUsers .conv_rgrid_data_num").html(presentdata.newUsers);
                            jQuery(".conv_rgrid_newUsers .conv_rgrid_data_per").html(newUsers_cmp.toFixed(2) + "%");
                            conv_setupdownarrow(newUsers_cmp, 'conv_rgrid_newUsers');

                            let avgduration_diff = presentdata.averageSessionDuration - pastdata.averageSessionDuration;
                            let avgduration_cmp = (avgduration_diff != 0 && pastdata.averageSessionDuration != 0) ? (avgduration_diff / pastdata.averageSessionDuration) * 100 : 0;

                            jQuery(".conv_rgrid_avgduration .conv_rgrid_data_num").html((parseFloat(presentdata.averageSessionDuration)).toFixed(2));
                            jQuery(".conv_rgrid_avgduration .conv_rgrid_data_per").html(avgduration_cmp.toFixed(2) + "%");
                            conv_setupdownarrow(avgduration_cmp, 'conv_rgrid_avgduration');

                            let bounce_diff = (presentdata.bounceRate * 100) - (pastdata.bounceRate * 100);
                            let bounce_cmp = (bounce_diff != 0 && pastdata.bounceRate != 0) ? (bounce_diff / pastdata.bounceRate) * 100 : 0;

                            jQuery(".conv_rgrid_bounce .conv_rgrid_data_num").html((parseFloat(presentdata.bounceRate) * 100).toFixed(2));
                            jQuery(".conv_rgrid_bounce .conv_rgrid_data_per").html(bounce_cmp.toFixed(2) + "%");
                            conv_setupdownarrow(bounce_cmp, 'conv_rgrid_bounce');

                            conv_set_gridboxwidth();
                            conv_changeplaceholder("hide");
                        } else if (presentdata != undefined && pastdata == undefined) {
                            jQuery(".conv_rgrid_sessions .conv_rgrid_data_num").html(presentdata.sessions);


                            jQuery(".conv_rgrid_newUsers .conv_rgrid_data_num").html(presentdata.newUsers);

                            jQuery(".conv_rgrid_avgduration .conv_rgrid_data_num").html((parseFloat(presentdata.averageSessionDuration)).toFixed(2));


                            jQuery(".conv_rgrid_bounce .conv_rgrid_data_num").html((parseFloat(presentdata.bounceRate) * 100).toFixed(2));
                            conv_set_gridboxwidth();
                            conv_changeplaceholder("hide");
                        } else {
                            jQuery(".conv_rgrid_sessions .conv_rgrid_data_num").html('N/A');
                            jQuery(".conv_rgrid_newUsers .conv_rgrid_data_num").html('N/A');
                            jQuery(".conv_rgrid_avgduration .conv_rgrid_data_num").html('N/A');
                            jQuery(".conv_rgrid_bounce .conv_rgrid_data_num").html('N/A');
                            jQuery(".conv_rgrid_data_per").html('N/A');
                            console.log("No data");
                        }
                    } else {
                        console.log("Error in data fetching");
                    }
                },
            });
        }

        function conv_daily_visitors_create_chart(data) {
            let chartStatus_dailyVisitors = Chart.getChart("conv_daily_visitors_chart"); // <canvas> id
            if (chartStatus_dailyVisitors != undefined) {
                chartStatus_dailyVisitors.destroy();
            }
            var dateArray = [];
            var visitorsArray = [];
            dailyVisitors_count = 0;
            //console.log("visitor response",data);
            jQuery.each(data, function(index, obj) {
                jQuery.each(obj, function(key, value) {
                    dateArray.push(key);
                    visitorsArray.push(value);
                    dailyVisitors_count = dailyVisitors_count + +value;
                });
            });
            //console.log("key array",dateArray);
            //console.log("value array",visitorsArray);
            jQuery("#conv_daily_visitors_total").html("Total Users " + dailyVisitors_count);
            const visitors_graphData = {
                labels: dateArray,
                datasets: [{
                        label: "Daily Visitors",
                        data: visitorsArray,
                        borderColor: '#1085F1',
                        backgroundColor: '#1085F1',
                        order: 0
                    }

                ]
            };
            var options = {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: "Daily Visitors"
                }
            };

            var graphTarget = jQuery("#conv_daily_visitors_chart");
            var barGraph = new Chart(graphTarget, {
                type: 'bar',
                data: visitors_graphData,
                options: options,
            });

        }

        function conv_realtime_create_chart(data) {
            let chartStatus_realtime = Chart.getChart("conv_realtime_chart"); // <canvas> id
            if (chartStatus_realtime != undefined) {
                chartStatus_realtime.destroy();
            }
            var minutesAgoArray = [];
            var activeUsersArray = [];
            var screenPageViewsArray = [];
            var conversionsArray = [];
            var eventCountArray = [];

            jQuery.each(data, function(index, item) {
                minutesAgoArray[index] = item.minutesAgo;
                activeUsersArray[index] = item.activeUsers;
                screenPageViewsArray[index] = item.screenPageViews;
                conversionsArray[index] = item.conversions;
                eventCountArray[index] = item.eventCount;
            });

            const graphData = {
                labels: minutesAgoArray,
                datasets: [{
                        label: "Active Users",
                        data: activeUsersArray,
                        fill: false,
                        borderColor: '#1085F1',
                        tension: 0.1
                    },
                    {
                        label: "Page Views",
                        data: screenPageViewsArray,
                        fill: false,
                        borderColor: '#FCCB1E',
                        tension: 0.1
                    },
                    {
                        label: "Conversions",
                        data: conversionsArray,
                        fill: false,
                        borderColor: '#09BD83',
                        tension: 0.1
                    },
                    {
                        label: "Events",
                        data: eventCountArray,
                        fill: false,
                        borderColor: '#F43E56',
                        tension: 0.1
                    }
                ]
            };
            var options = {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: "Last 30 minutes"
                }
            };

            var graphTarget = jQuery("#conv_realtime_chart");
            var barGraph = new Chart(graphTarget, {
                type: 'line',
                data: graphData,
                options: options,
                minHeight: 300
            });
        }

        function fillMissingDates(arr, audience_name, first, last) {
            var filledArray = [];
            var dateMap = {};

            // Populate dateMap with existing array data
            arr.forEach(function(item) {
                dateMap[item.date] = item;
            });

            // Generate dates between start and end date
            var first_Date = moment(first, "YYYY-MM-DD");
            var last_Date = moment(last, "YYYY-MM-DD");
            var dat = first_Date;
            var dateString = "";

            //console.log("end",last_Date);
            //console.log("start",first_Date);

            while (first_Date.isSameOrBefore(last_Date)) {
                dateString = dat.format('YYYY-MM-DD');
                if (dateMap[dateString]) {
                    filledArray.push(dateMap[dateString]);
                } else {
                    filledArray.push({
                        date: dateString,
                        audienceName: audience_name,
                        activeUsers: '0',
                        screenPageViews: '0',
                        conversions: '0'
                    });
                }
                dat.add(1, 'days');
            }
            return filledArray;
        }

        function conv_changeDateFormat(dateStr) {
            // Split the date string by '-'
            var parts = dateStr.split('-');
            // Rearrange the parts and return the new format
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }

        function conv_audience_create_chart(data, req_data) {
            let chartStatus_audience = Chart.getChart("conv_audience_chart"); // <canvas> id
            if (chartStatus_audience != undefined) {
                chartStatus_audience.destroy();
            }

            //console.log("full data",data);

            var AllUsers_array = [];
            var AllPurchasers_array = [];
            var users_count = 0;
            var purchasers_count = 0;

            jQuery.each(data, function(index, item) {
                if (item.audienceName === "All Users") {
                    AllUsers_array[users_count] = item;
                    users_count++;
                } else if (item.audienceName === "Purchasers") {
                    AllPurchasers_array[purchasers_count] = item;
                    purchasers_count++;
                }
            });
            //console.log("indivial users array",AllUsers_array);
            //console.log("individual purchasers array",AllPurchasers_array);

            var first_date = conv_changeDateFormat(req_data.start_date);
            var last_date = conv_changeDateFormat(req_data.end_date);

            //console.log("original dates",first_date+"-"+last_date);
            var purchasers_FinalArr = fillMissingDates(AllPurchasers_array, "Purchasers", first_date, last_date);
            //console.log("check purchasers final array",purchasers_FinalArr);

            var users_FinalArr = fillMissingDates(AllUsers_array, "All Users", first_date, last_date);
            //console.log("check users final array",users_FinalArr);

            //Now, seperate date array and parameters array
            var activeUsersAllUsers = [];
            var screenPageViewsAllUsers = [];
            var activeUsersPurchasers = [];
            var screenPageViewsPurchasers = [];
            var mergedDateArray = [];
            jQuery.each(users_FinalArr, function(index, item) {
                screenPageViewsAllUsers.push(parseInt(item.screenPageViews));
                activeUsersAllUsers.push(parseInt(item.activeUsers));
                mergedDateArray.push(item.date);
            });
            jQuery.each(purchasers_FinalArr, function(index, item) {
                screenPageViewsPurchasers.push(parseInt(item.screenPageViews));
                activeUsersPurchasers.push(parseInt(item.activeUsers));
            });
            const audience_data = {
                labels: mergedDateArray,
                datasets: [{
                        label: 'All Users',
                        data: activeUsersAllUsers,
                        borderColor: '#2A2D2F',
                        backgroundColor: '#2A2D2F',
                        order: 0
                    },
                    {
                        label: 'All Users Page Views',
                        data: screenPageViewsAllUsers,
                        borderColor: '#1085F1',
                        backgroundColor: '#1085F1',
                        order: 1
                    },
                    {
                        label: 'Purchasers',
                        data: activeUsersPurchasers,
                        borderColor: '#FCCB1E',
                        backgroundColor: '#FCCB1E',
                        type: 'line',
                        order: 2
                    },
                    {
                        label: 'Purchasers Page Views',
                        data: screenPageViewsPurchasers,
                        borderColor: '#09BD83',
                        backgroundColor: '#09BD83',
                        type: 'line',
                        order: 3
                    }
                ]
            };
            var options = {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: "Audience Reports"
                }
            };

            var graphTarget = jQuery("#conv_audience_chart");
            var barGraph = new Chart(graphTarget, {
                type: 'bar',
                data: audience_data,
                options: options,
            });


        }

        function conv_donut_create(selector, data) {
            var label = "";
            var date_slug = "";
            var value_slug = "";
            var dateArr = [];
            var valueArr = [];
            var donut_color_Arr = [];
            if (selector == "conv_device_chart") {
                label = "Active Users";
                date_slug = "deviceCategory";
                value_slug = "activeUsers";

                let chartStatus_device = Chart.getChart("conv_device_chart"); // <canvas> id
                if (chartStatus_device != undefined) {
                    chartStatus_device.destroy();
                }
                jQuery.each(data, function(index, propValue) {
                    dateArr[index] = propValue[date_slug];
                    valueArr[index] = propValue[value_slug];
                });
                donut_color_Arr = ['#1085F1', '#2A2D2F'];

            } else if (selector == "conv_users_chart") {
                label = "New Vs Returning Users";

                let chartStatus_users = Chart.getChart("conv_users_chart"); // <canvas> id
                if (chartStatus_users != undefined) {
                    chartStatus_users.destroy();
                }
                var newTotal = 0;
                var returningTotal = 0;
                var notSetTotal = 0;

                jQuery.each(data, function(index, item) {
                    if (item?.new > 0) {
                        newTotal += parseInt(item.new);
                    }
                    if (item?.returning > 0) {
                        returningTotal += parseInt(item.returning);
                    }
                    if (item["(not set)"] > 0) {
                        notSetTotal += parseInt(item["(not set)"]);
                    }

                });
                dateArr = ["New", "Returning", "(not set)"];
                valueArr = [newTotal, returningTotal, notSetTotal];
                donut_color_Arr = ['#1085F1', '#2A2D2F', '#94979A'];
            }

            const donut_labels = dateArr;
            const donut_graphData = {
                labels: donut_labels,
                datasets: [{
                    label: label,
                    data: valueArr,
                    backgroundColor: donut_color_Arr,
                    hoverOffset: 4,
                    weight: 600,
                    aspectRatio: 1,
                    minHeight: 300
                }]
            };
            var options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: label
                    }
                }
            };

            var graphTarget = jQuery("#" + selector);
            var barGraph = new Chart(graphTarget, {
                type: 'doughnut',
                data: donut_graphData,
                options: options,
            });
        }

        function conv_get_donut_reports(post_data, selector) {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: post_data,
                success: function(response) { //console.log("donut response",response);
                    if (response?.error == false) {
                        let device_data = response.data;
                        if (device_data.length != 0) {
                            if (selector == "conv_device_chart") {
                                conv_donut_create("conv_device_chart", device_data);
                            } else {
                                conv_donut_create("conv_users_chart", device_data);
                            }

                        } else {
                            let noimgsrc =
                                "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                            let row = "<div class='noresultimg' style='height:300px;'><img src='" + noimgsrc +
                                "' /></div>";
                            jQuery("#" + selector).addClass("d-none");
                            jQuery("#" + selector).after(row);
                        }
                    } else {
                        let noimgsrc =
                            "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                        let row = "<div class='noresultimg' style='height:300px;'><img src='" + noimgsrc +
                            "' /></div>";
                        jQuery("#" + selector).addClass("d-none");
                        jQuery("#" + selector).after(row);
                    }
                }
            });
        }

        function conv_audience_report(post_data) {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: post_data,
                success: function(response) { //console.log("audience response",response);
                    if (response?.error == false) {
                        let audience_data = response.data;

                        if (audience_data.length != 0) {
                            conv_audience_create_chart(audience_data, post_data);
                        } else {
                            let noimgsrc =
                                "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                            let row = "<div class='noresultimg'><img src='" + noimgsrc + "' /></div>";
                            jQuery("#conv_audience_chart").addClass("d-none");
                            jQuery("#conv_audience_chart").after(row);
                        }
                    } else {
                        let noimgsrc =
                            "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                        let row = "<div class='noresultimg'><img src='" + noimgsrc + "' /></div>";
                        jQuery("#conv_audience_chart").addClass("d-none");
                        jQuery("#conv_audience_chart").after(row);
                    }
                }
            });
        }

        function conv_daily_visitors_report(post_data) {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: post_data,
                success: function(response) { //console.log("realtime response",response);
                    if (response?.error == false) {
                        let daily_visitors_data = response.data;

                        if (daily_visitors_data.length != 0) {
                            conv_daily_visitors_create_chart(daily_visitors_data);
                        } else {
                            let noimgsrc =
                                "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                            let row = "<div class='noresultimg'><img src='" + noimgsrc + "' /></div>";
                            jQuery("#conv_daily_visitors_chart").addClass("d-none");
                            jQuery("#conv_daily_visitors_chart").after(row);
                        }
                    } else {
                        let noimgsrc =
                            "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                        let row = "<div class='noresultimg'><img src='" + noimgsrc + "' /></div>";
                        jQuery("#conv_daily_visitors_chart").addClass("d-none");
                        jQuery("#conv_daily_visitors_chart").after(row);
                    }
                }
            });
        }

        function conv_get_realtime_report(post_data) {
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: post_data,
                success: function(response) { //console.log("realtime response",response);
                    if (response?.error == false) {
                        let realtime_data = response.data;

                        if (realtime_data.length != 0) {
                            conv_realtime_create_chart(realtime_data);
                        } else {
                            let noimgsrc =
                                "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                            let row = "<div class='noresultimg' style='height:261px;'><img src='" + noimgsrc +
                                "' /></div>";
                            jQuery("#conv_realtime_chart").addClass("d-none");
                            jQuery("#conv_realtime_chart").after(row);
                        }
                    } else {
                        let noimgsrc =
                            "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                        let row = "<div class='noresultimg' style='height:261px;'><img src='" + noimgsrc +
                            "' /></div>";
                        jQuery("#conv_realtime_chart").addClass("d-none");
                        jQuery("#conv_realtime_chart").after(row);
                    }
                }
            });
        }

        function conv_get_demographics_report(post_data) {
            var demographics_selector = "conv_top_city_tbl";
            var dimension = "city";
            if (post_data['report_name'] == "country") {
                dimension = "country";
                demographics_selector = "conv_top_country_tbl";
            }
            post_data['limit'] = '7';
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: post_data,
                success: function(response) { //console.log("demographics response",response);
                    if (response?.error == false) {
                        //console.log("response is", response);
                        if (response?.error == false) {
                            let opr_data = response.data;
                            var tableBody = jQuery('#' + demographics_selector + ' tbody');
                            tableBody.empty();
                            if (opr_data.length != 0) {
                                jQuery.each(opr_data, function(index, transaction) {
                                    if (index <= 4) {
                                        if (dimension == "country") {
                                            var row =
                                                "<tr class='border-bottom text-secondary'><td class='text-start p-1 text-truncate' title='" +
                                                transaction.country + "'>" + transaction.country +
                                                "</td><td class='text-end p-1' >" + transaction
                                                .activeUsers + "</td><td class='text-end p-1'>" +
                                                transaction.screenPageViews + "</td></tr>";
                                        } else {
                                            var row =
                                                "<tr class='border-bottom text-secondary'><td class='text-start p-1 text-truncate' title='" +
                                                transaction.city + "'>" + transaction.city +
                                                "</td><td class='text-end p-1' >" + transaction
                                                .activeUsers + "</td><td class='text-end p-1'>" +
                                                transaction.screenPageViews + "</td></tr>";
                                        }
                                        tableBody.append(row);
                                    }
                                });
                            } else {
                                var noimgsrc =
                                    "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                                var row = "<div class='noresultimg'><img src='" + noimgsrc + "' /></div>";
                                jQuery('#' + demographics_selector).after(row);
                            }
                        } else {
                            var noimgsrc =
                                "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                            var row = "<div class='noresultimg'><img src='" + noimgsrc + "' /></div>";
                            jQuery('#' + demographics_selector).after(row);
                        }
                    }
                }
            });
        }

        function conv_get_ga4_page_report(post_data) {
            post_data['limit'] = '5';
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: post_data,
                success: function(response) { //console.log("page response",response);
                    if (response?.error == false) {
                        //console.log("response is", response);
                        if (response?.error == false) {
                            let opr_data = response.data;
                            var tableBody = jQuery('#conv_page_report_tbl tbody');
                            tableBody.empty();
                            if (opr_data.length != 0) {
                                jQuery.each(opr_data, function(index, transaction) {
                                    if (index <= 4) {
                                        var row =
                                            "<tr class='border-bottom text-secondary'><td class='text-start p-1 text-truncate' title='" +
                                            transaction.pagetitle + "'>" + transaction.pagetitle +
                                            "</td><td class='text-end p-1 text-truncate' title='" +
                                            transaction.path + "'>" + transaction.path +
                                            "</td><td class='text-end p-1'>" + transaction.screenPageViews +
                                            "</td><td class='text-end p-1'>" + transaction.activeUsers +
                                            "</td><td class='text-end p-1'>" + transaction.sessions +
                                            "</td><td class='text-end p-1'>" + parseFloat(transaction
                                                .averageSessionDuration).toFixed(2) +
                                            " seconds </td><td class='text-end p-1'>" + transaction
                                            .eventCount + "</td><td class='text-end p-1'>" + (parseFloat(
                                                transaction.bounceRate) * 100).toFixed(2) + "% </td></tr>";
                                        tableBody.append(row);
                                    }
                                });
                            } else {
                                var noimgsrc =
                                    "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                                var row = "<div class='noresultimg'><img src='" + noimgsrc + "' /></div>";
                                jQuery('#conv_page_report_tbl').after(row);
                                //jQuery('#conv_viewfre_link').hide();
                            }
                        } else {
                            var noimgsrc =
                                "<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/no-results.png'); ?>";
                            var row = "<div class='noresultimg'><img src='" + noimgsrc + "' /></div>";
                            jQuery('#conv_page_report_tbl').after(row);
                        }
                    }
                }
            });
        }

        function conv_set_gridboxwidth() {
            var gridboxwidth = jQuery("#conv_gridrepbox_row").outerWidth();
            if (gridboxwidth <= 1500) {
                jQuery('.conv_rep_gridbox').each(function() {
                    var colWidth = jQuery(this).outerWidth();
                    if (colWidth > 175) {
                        jQuery(".conv_rep_gridbox").addClass("col-xxl-3");
                    }
                });
            } else {
                jQuery(".conv_rep_gridbox").removeClass("col-xxl-3");
            }

        }


        function cb(start, end) {
            start_date = start.format('DD/MM/YYYY') || 0,
                end_date = end.format('DD/MM/YYYY') || 0;
            var datdiff = end.diff(start, 'days') + 1;
            jQuery(".conv_rgrid_data_compari").html("From last " + datdiff + " days");
            jQuery("#conv_date_diff").html("Last " + datdiff + " days: ");
            jQuery('span.daterangearea').html(start_date + ' - ' + end_date);
            var data = {
                action: 'get_ga4_general_grid_reports',
                subscription_id: '<?php echo esc_attr($ee_options["subscription_id"]); ?>',
                start_date: jQuery.trim(start_date.replace(/\//g, "-")),
                end_date: jQuery.trim(end_date.replace(/\//g, "-")),
                conversios_nonce: '<?php echo esc_js(wp_create_nonce('conversios_nonce')); ?>',
                domain: '<?php echo esc_js(get_site_url()); ?>'
            };
            jQuery(".noresultimg").remove();
            jQuery("#conv_checkoutfunnel_chart").removeClass("d-none");
            jQuery("#conv_conversionfunnel_chart").removeClass("d-none");

            get_ga4_general_grid_reports(data);

            data["action"] = "get_ga4_page_report";
            conv_get_ga4_page_report(data);

            data["action"] = "get_general_donut_reports";
            data["report_name"] = "conv_device_chart";
            conv_get_donut_reports(data, "conv_device_chart");

            data["report_name"] = "conv_users_chart";
            conv_get_donut_reports(data, "conv_users_chart");

            data["report_name"] = "country";
            data["action"] = "get_demographic_ga4_reports";
            conv_get_demographics_report(data);

            data["report_name"] = "city";
            data["action"] = "get_demographic_ga4_reports";
            conv_get_demographics_report(data);

            data["action"] = "get_realtime_report";
            data["report_name"] = "";
            conv_get_realtime_report(data);

            data["action"] = "get_general_audience_report";
            conv_audience_report(data);

            data["action"] = "get_daily_visitors_report";
            conv_daily_visitors_report(data);
        }

        jQuery(function() {
            jQuery(window).resize(function() {
                conv_set_gridboxwidth();
            });

            //For tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
<?php
} else {
?>
    <script>
        jQuery(function() {
            jQuery("#reportrange").addClass('d-none');
            jQuery(".conv_reportsec_box").addClass("opacity-50");
        });
    </script>

<?php
}
?>