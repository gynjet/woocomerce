<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$is_sel_disable = 'disabled';
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <form id="pixelsetings_form" class="convpixsetting-inner-box">
        <div>
            <!-- MS ClarityPixel -->
            <?php $msclarity_pixel_id = isset($ee_options['msclarity_pixel_id']) ? $ee_options['msclarity_pixel_id'] : ""; ?>
            <div id="msclarity_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-7">
                        <h5 class="d-flex align-items-center mb-1 text-dark">
                            <b><?php esc_html_e("Microsoft Clarity ID:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                            <?php if (!empty($msclarity_pixel_id)) { ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6">check_circle</span>
                            <?php } ?>
                            <!-- <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="The Microsoft Clarity ID looks like. ij312itarj">
                                info
                            </span> -->
                        </h5>
                        <input type="text" name="msclarity_pixel_id" id="msclarity_pixel_id"
                            class="form-control valtoshow_inpopup_this"
                            value="<?php echo esc_attr($msclarity_pixel_id); ?>" placeholder="e.g. ij312itarj"
                            popuptext="Microsoft Clarity ID:">
                    </div>
                </div>
            </div>
            <!-- MS Clarity Pixel End-->
        </div>
    </form>
    <input type="hidden" id="valtoshow_inpopup" value="Microsoft Clarity ID:" />

</div>

<script>
jQuery(function() {
    //jQuery("#upgradetopro_modal_link").attr("href", '<?php echo esc_url($TVC_Admin_Helper->get_conv_pro_link_adv("popup", "twittersettings",  "conv-link-blue fw-bold", "linkonly")); ?>');

    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>