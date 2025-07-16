<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
$is_sel_disable = 'disabled';
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <form id="pixelsetings_form" class="convpixsetting-inner-box">
        <div>
            <!-- Linkedin Insight -->
            <?php $linkedin_insight_id = isset($ee_options['linkedin_insight_id']) ? $ee_options['linkedin_insight_id'] : ""; ?>
            <div id="pintrest_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-7">
                        <h5 class="d-flex align-items-center mb-1 text-dark">
                            <b><?php esc_html_e("Linkedin Insight ID:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                            <?php if (!empty($linkedin_insight_id)) { ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6">check_circle</span>
                            <?php } ?>
                            <!-- <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="The Pinterest Ads pixel ID looks like. 2612831678022">
                                info
                            </span> -->
                        </h5>
                        <input type="text" name="linkedin_insight_id" id="linkedin_insight_id" class="form-control valtoshow_inpopup_this" value="<?php echo esc_attr($linkedin_insight_id); ?>" placeholder="e.g. 2612831678022">
                    </div>
                </div>
            </div>
            <!-- Linkedin Insight End-->
        </div>
        <div class="row row-x-0 d-flex justify-content-between align-items-center conv_create_gads_new_card rounded px-3 py-3 mt-4" style="background: #caf3e3;">
            <div class="mt-0 mb-2 col-3 d-flex justify-content-center">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/sstimpact.png','','rounded shadow'),
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
                <a target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&amp;utm_medium=linkedinnersetting&amp;utm_campaign=sstnudge&amp;plugin_name=aio" class="align-middle btn btn-sm btn-primary fw-bold-500">
                    Buy Now! </a>
            </div>
        </div>
    </form>
    <input type="hidden" id="valtoshow_inpopup" value="Linkedin Insight ID:" />

</div>