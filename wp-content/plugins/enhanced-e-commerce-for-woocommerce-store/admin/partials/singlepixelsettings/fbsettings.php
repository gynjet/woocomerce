<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$is_sel_disable = 'disabled';
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <form id="pixelsetings_form" class="convpixsetting-inner-box">
        <div>
            <!-- Facebook ID  -->
            <?php
            $fb_pixel_id = (isset($ee_options["fb_pixel_id"]) && $ee_options["fb_pixel_id"] != "") ? $ee_options["fb_pixel_id"] : "";
            ?>
            <div id="fbpixel_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-7">
                        <ul class="conv-green-checklis list-unstyled mb-3">
                            <li class="d-flex">
                                <span class="material-symbols-outlined text-success md-18">
                                    check_circle
                                </span>
                                <?php esc_html_e("All the e-commerce pixel tracking including Purchase", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="PageView, ViewContent, AddToCart, InitiateCheckout, AddPaymentInfo, Purchase">
                                    info
                                </span>
                            </li>
                            <li class="d-flex">
                                <span class="material-symbols-outlined text-success md-18">
                                    check_circle
                                </span>
                                <?php esc_html_e("All the lead generation pixel tracking including Form Submit", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Lead, email_click, phone_click, address_click">
                                    info
                                </span>
                            </li>
                        </ul>
                        <h5 class="d-flex align-items-center mb-1 text-dark">
                            <b><?php esc_html_e("Meta (Facebook) Pixel ID", "enhanced-e-commerce-for-woocommerce-store"); ?>:</b>
                            <?php if (!empty($fb_pixel_id)) { ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6">check_circle</span>
                            <?php } ?>
                            <!-- <sup>
                                <span class="material-symbols-outlined text-secondary md-18 ps-1" data-bs-toggle="tooltip" data-bs-placement="top" title="The Facebook pixel ID looks like. 518896233175751">info</span>
                            </sup> -->
                        </h5>
                        <input type="text" name="fb_pixel_id" id="fb_pixel_id" class="form-control valtoshow_inpopup_this w-75" value="<?php echo esc_attr($fb_pixel_id); ?>" placeholder="e.g. 518896233175751">
                    </div>

                </div>
            </div>
            <!-- Facebook ID End-->


            <!-- Facebook ID  -->

            <div class="pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="row row-x-0 d-flex justify-content-between align-items-center conv_create_gads_new_card rounded px-3 py-3" style="background: #caf3e3;">
                            <div class="mt-0 mb-2 col-3 d-flex justify-content-center">
                                <?php echo wp_kses(
                                    enhancad_get_plugin_image('/admin/images/fbcapiimpact.png', '', 'rounded shadow'),
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
                                <div class="fs-6 fw-bold text-primary">Facebook Conversion API (FCAPI) Benefits in Professional Plan</div>
                                <ul class="conv-green-checklis fb-kapi list-unstyled mt-1">
                                    <li class="d-flex fs-14 fw-bold">
                                        <span class="material-symbols-outlined text-success md-18">
                                            check_circle
                                        </span>
                                        <?php esc_html_e("Improves Event Match Quality scores by sending extra user data (e.g., email, phone number).", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </li>
                                    <li class="d-flex fs-14 fw-bold">
                                        <span class="material-symbols-outlined text-success md-18">
                                            check_circle
                                        </span>
                                        <?php esc_html_e("Highest Event Match Quality Score via Our plugin 9.3", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </li>
                                    <li class="d-flex fs-14 fw-bold">
                                        <span class="material-symbols-outlined text-success md-18">
                                            check_circle
                                        </span>
                                        <?php esc_html_e("Complete picture of user journeys, resulting in better conversion attribution, especially with iOS 14+ restrictions.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </li>
                                    <li class="d-flex fs-14 fw-bold">
                                        <span class="material-symbols-outlined text-success md-18">
                                            check_circle
                                        </span>
                                        <?php esc_html_e("Bypasses ad blockers and browser restrictions, ensuring more precise tracking of conversions.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </li>
                                </ul>
                                <a target="_blank" href="<?php echo esc_url('https://www.conversios.io/checkout/?pid=wpAIO_PY1&utm_source=woo_aiofree_plugin&utm_medium=innerfb&utm_campaign=capi'); ?>" class="align-middle btn btn-sm btn-primary fw-bold-500">
                                    <?php esc_html_e("Buy Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Facebook ID End-->

        </div>
    </form>
    <input type="hidden" id="valtoshow_inpopup" value="Meta (Facebook) Pixel ID:" />

</div>

<!-- Ecommerce Events -->
<div class="convcard p-4 mt-0 rounded-3 shadow-sm mt-3 d-none hidden">
    <div class="row">
        <h5 class="fw-normal mb-1">
            <?php esc_html_e("Ecommerce Events", "enhanced-e-commerce-for-woocommerce-store"); ?>
            <span class="fw-400 text-color fs-12">
                <span class="text-color fs-6"> *</span> <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Page view and purchase event tracking are available in free plan. For complete ecommerce tracking, upgrade to our pro plan">
                    info
                </span>
            </span>
        </h5>
    </div>
    <div class="row">
        <div class="col-md-4">
            <input type="checkbox" class="m-1" name="" style="-webkit-appearance: auto;" checked onclick="return false;">
            <?php esc_html_e("Page view", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </div>
        <div class="col-md-4">
            <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
                <span class="material-symbols-outlined lock-icon">
                    lock
                </span>
                <?php esc_html_e("View content", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
        </div>
        <div class="col-md-4 pr-0">
            <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
                <span class="material-symbols-outlined lock-icon">
                    lock
                </span>
                <?php esc_html_e("Initiate checkout", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <input type="checkbox" name="" class="m-1" style="-webkit-appearance: auto;" checked onclick="return false;">
            <?php esc_html_e("Purchase", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </div>
        <div class="col-md-4">
            <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
                <span class="material-symbols-outlined lock-icon">
                    lock
                </span>
                <?php esc_html_e("Add to cart", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </span>
        </div>
        <div class="col-md-4">
            <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
                <span class="material-symbols-outlined lock-icon">
                    lock
                </span>
                <?php esc_html_e("Add payment info", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
        </div>
    </div>

    <div class="row pt-3">
        <div class="col-md-12">
            <h5 class="fw-bold-500 conv-recommended-text" style="font-size: 17px;">
                <?php esc_html_e("Recommended:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h5>
            <h5 class="fw-normal mb-1">
                <?php esc_html_e("For complete FB ads tracking, consider switching to our Started plan.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                <span class="align-middle conv-link-blue ms-2 fw-bold-500 upgradetopro_badge" data-bs-toggle="modal" data-bs-target="#upgradetopromodal">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/logos/upgrade_badge.png'),
                        array(
                            'img' => array(
                                'src' => true,
                                'alt' => true,
                                'class' => true,
                                'style' => true,
                            ),
                        )
                    ); ?>
                    <?php esc_html_e("Available In Pro", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>
            </h5>
        </div>
    </div>
</div>

<script>
    jQuery(function() {
        jQuery("#upgradetopro_modal_link").attr("href",
            '<?php echo esc_url($TVC_Admin_Helper->get_conv_pro_link_adv("popup", "fbsettings",  "conv-link-blue fw-bold", "linkonly")); ?>'
        );

        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>