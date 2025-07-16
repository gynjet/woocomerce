<div style="position:relative;">
    <div class="modal fade" id="upgradetopromodalotherReports" data-bs-keyboard="false" data-bs-backdrop="static"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                    <a id="upgradetopro_modal_link" class="btn bg-white text-black m-auto w-100 mx-2 ms-4 p-2"
                        href="admin.php?page=conversios-analytics-reports" style="border: 1px solid black;">
                        <?php esc_html_e("Back", "enhanced-e-commerce-for-woocommerce-store"); ?>
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
    <div id="imgcontainerconv">
        <?php echo wp_kses(
            enhancad_get_plugin_image('/admin/images/web-reports-gads.png', '', 'w-100'),
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
<script>
    function cb(start, end) {
        start_date = start.format('DD/MM/YYYY') || 0,
            end_date = end.format('DD/MM/YYYY') || 0;
        jQuery('span.daterangearea').html(start_date + ' - ' + end_date);
    }
    jQuery(function() {
        jQuery("#upgradetopromodalotherReports").modal('show');
        jQuery("body.modal-open").css("overflow", "auto !important");

    });
    jQuery(document).on('show.bs.modal', '#upgradetopromodalotherReports', function() {
        setTimeout(function() {
            jQuery("body.modal-open").addClass("overflow-auto");
        }, 1000);
    });
</script>