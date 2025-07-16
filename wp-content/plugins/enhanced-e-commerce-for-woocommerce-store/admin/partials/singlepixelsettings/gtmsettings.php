<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly 

$ga3_id = isset($ee_options['ga_id']) && $ee_options['ga_id'] != "" ? $ee_options['ga_id'] : "";
$ga4_id = isset($ee_options['gm_id']) && $ee_options['gm_id'] != "" ? $ee_options['gm_id'] : "";
$google_ads_id = isset($ee_options['google_ads_id']) && $ee_options['google_ads_id'] != "" ? $ee_options['google_ads_id'] : "";
$fb_pixel_id = isset($ee_options['fb_pixel_id']) && $ee_options['fb_pixel_id'] != "" ? $ee_options['fb_pixel_id'] : "";
$showfeaturepopup = "";
if ($ga4_id == "" || $google_ads_id == "" || $fb_pixel_id == "") {
  $showfeaturepopup = "yes";
}
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">

  <?php if (isset($pixel_settings_arr[$subpage]['topnoti']) && $pixel_settings_arr[$subpage]['topnoti'] != "") { ?>
    <div class="alert d-flex align-items-cente p-0" role="alert">
      <div class="text-light conv-success-bg rounded-start d-flex">
        <span class="p-2 material-symbols-outlined align-self-center">verified</span>
      </div>
      <div class="p-2 w-100 rounded-end border border-start-0 shadow-sm conv-notification-alert bg-white">
        <div class="">
          <?php esc_html_e("Unlock the power of your own Google Tag Manager. Boost page speed, and customize events effortlessly.", "enhanced-e-commerce-for-woocommerce-store"); ?>
          <a class="conv-link-blue" target="_blank" href="<?php echo esc_url($TVC_Admin_Helper->get_conv_pro_link_adv("notice", "gtmsettings",  "", "linkonly")); ?>" class="conv_link_blue">
            <?php esc_html_e("Upgrade now for greater control and performance!", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </a>
        </div>
      </div>
    </div>
  <?php } ?>

  <form id="gtmsettings_form">

    <div class="convpixsetting-inner-box mt-4">
      <h5 class="fw-normal mb-1">
        <?php esc_html_e("Select Google Tag Manager Container:", "enhanced-e-commerce-for-woocommerce-store"); ?>
      </h5>
      <?php
      $tracking_method = (isset($ee_options['tracking_method']) && $ee_options['tracking_method'] != "") ? $ee_options['tracking_method'] : "";
      $want_to_use_your_gtm = "";
      if ($tracking_method == "gtm") {
        $want_to_use_your_gtm = (isset($ee_options['want_to_use_your_gtm']) && $ee_options['want_to_use_your_gtm'] != "") ? $ee_options['want_to_use_your_gtm'] : "0";
      }
      $use_your_gtm_id = isset($ee_options['use_your_gtm_id']) ? $ee_options['use_your_gtm_id'] : "";
      ?>
      <div>
        <div class="py-2">
          <input type="radio" <?php echo esc_attr(($want_to_use_your_gtm == "0" || $want_to_use_your_gtm == "1") ? 'checked="checked"' : ''); ?> name="want_to_use_your_gtm" id="want_to_use_your_gtm_default" value="0">
          <label class="form-check-label ps-2" for="want_to_use_your_gtm_default">
            <?php esc_html_e("Conversios Global Container (GTM-K7X94DG)", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </label>
        </div>
        <div class="py-4">
          <input type="radio" name="want_to_use_your_gtm" id="want_to_use_your_gtm_own" value="0" readonly disabled class="align-top">
          <label class="form-check-label ps-2" for="want_to_use_your_gtm_own">
            <?php esc_html_e("Use Your Google Tag Manager Container", "enhanced-e-commerce-for-woocommerce-store"); ?>
            <span class="conv-link-blue ms-2 fw-bold-500 upgradetopro_badge" popupopener="gtmpro_inner">
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
            <br />
            <small>(Faster Page Speed & Tag Customization)</small>
          </label>
        </div>

        <div id="use_your_gtm_id_box" class="use_your_gtm_id py-1 <?php echo esc_attr(($want_to_use_your_gtm == "0") || $want_to_use_your_gtm == "" ? 'd-none' : ''); ?>">
          <input type="hidden" class="form-control-lg display-6" name="use_your_gtm_id" id="use_your_gtm_id" value="<?php echo esc_attr($use_your_gtm_id); ?>">
        </div>

      </div>
      <div class="event-setting-div py-3 border-top">
        <div class="row">
          <h5><?php esc_html_e("Plugin Configurations", "enhanced-e-commerce-for-woocommerce-store"); ?></h5>
          <div class="py-3 d-none">
            <h5 class="fw-normal mb-1">
              <?php esc_html_e("Select User Roles to Disable Tracking:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h5>
            <select class="form-select mb-3 selecttwo w-100" id="conv_disabled_users" name="conv_disabled_users[]" multiple="multiple" data-placeholder="Select role">
              <?php foreach ($TVC_Admin_Helper->conv_get_user_roles() as $slug => $name) {
                $is_selected = "";
                if (!empty($ee_options['conv_disabled_users'])) {
                  $is_selected = in_array($slug, $ee_options['conv_disabled_users']) ? "selected" : "";
                }
              ?>
                <option value="<?php echo esc_attr($slug); ?>" <?php echo esc_html($is_selected); ?>><?php echo esc_html($name); ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="py-2 conv_global_configs">
            <h5 class="fw-normal mb-1">
              <?php esc_html_e("Ecommerce Events to Track:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h5>
            <?php if (!CONV_IS_WC) : ?>
              <small><?php esc_html_e("To utilize these events, you'll need WooCommerce", "enhanced-e-commerce-for-woocommerce-store") ?></small>
            <?php endif; ?>
            <div style="<?php echo !CONV_IS_WC ? 'opacity:0.5' : ''; ?>">
              <select class="form-select mb-3 selecttwo_configs w-100" id="ga_selected_events_ecomm" name="conv_selected_events[ga][]" multiple="multiple" required data-placeholder="Select event" <?php echo CONV_IS_WC ? '' : 'disabled' ?>>
                <?php
                $conv_selected_events = unserialize(get_option('conv_selected_events'));
                $conv_all_pixel_event = $TVC_Admin_Helper->conv_all_pixel_event();
                foreach ($conv_all_pixel_event['ecommerce'] as $slug => $name) {
                  $is_selected = empty($conv_selected_events) ? "selected" : "";
                  if (!empty($conv_selected_events['ga'])) {
                    $is_selected =  in_array($slug, $conv_selected_events['ga']) ? "selected" : "";
                  }
                ?>
                  <option value="<?php echo esc_attr($slug); ?>" <?php echo esc_html($is_selected); ?>><?php echo esc_attr($name); ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="py-2 net_revenue_setting_box">
            <div class="d-flex">
              <h5 class="fw-normal mb-1">
                <?php esc_html_e("Revenue Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </h5>
              <span class="material-symbols-outlined text-secondary md-18 ps-2 align-self-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Select metrics from below that will be calculated for revenue tracking on the purchase event. For Example, if you select Product subtotal and Shipping then order revenue = product subtotal + shipping.">
                info
              </span>
            </div>
            <?php if (!CONV_IS_WC) : ?>
              <small><?php esc_html_e("To utilize these tracking, you'll need WooCommerce", "enhanced-e-commerce-for-woocommerce-store") ?></small>
            <?php endif; ?>
            <div style="<?php echo !CONV_IS_WC ? 'opacity:0.5;pointer-events:none;' : ''; ?>padding:0 0.5em;">
              <div class="form-check form-check-inline">
                <input class="form-check-input conv_revnue_checkinput" type="checkbox" id="conv_revnue_subtotal" value="subtotal" <?php echo isset($ee_options['net_revenue_setting']) ? 'checked onclick="return false" style="opacity:0.5"' : ''; ?>>
                <label class="form-check-label" for="conv_revnue_subtotal">
                  <?php esc_html_e("Product subtotal (Sum of Product prices)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input conv_revnue_checkinput" type="checkbox" id="conv_revnue_shipping" value="shipping" <?php echo isset($ee_options['net_revenue_setting']) && in_array('shipping', $ee_options['net_revenue_setting']) ? "checked" : "" ?>>
                <label class="form-check-label" for="conv_revnue_shipping">
                  <?php esc_html_e("Include Shipping", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input conv_revnue_checkinput" type="checkbox" id="conv_revnue_tax" value="tax" <?php echo isset($ee_options['net_revenue_setting']) && in_array('tax', $ee_options['net_revenue_setting']) ? "checked" : "" ?>>
                <label class="form-check-label" for="conv_revnue_tax">
                  <?php esc_html_e("Include Tax", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
              </div>
            </div>
          </div>

          <div class="py-2 conv_global_configs">
            <h5 class="fw-normal mb-1">
              <?php esc_html_e("Lead Generation Events to Track:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h5>
            <small><?php esc_html_e("Track form submit event for Contact Form 7, WpForm, Ninja Form, Gravity form, Formidable form and many more", "enhanced-e-commerce-for-woocommerce-store") ?></small>
            <select class="form-select mb-3 selecttwo_configs w-100" id="ga_selected_events_leadgen" name="conv_selected_events[ga][]" multiple="multiple" required data-placeholder="Select event">
              <?php
              $conv_selected_events = unserialize(get_option('conv_selected_events'));
              $conv_all_pixel_event = $TVC_Admin_Helper->conv_all_pixel_event();
              foreach ($conv_all_pixel_event['lead_generation'] as $slug => $name) {
                $is_selected = empty($conv_selected_events) ? "selected" : "";
                if (!empty($conv_selected_events['ga'])) {
                  $is_selected =  in_array($slug, $conv_selected_events['ga']) ? "selected" : "";
                }
              ?>
                <option value="<?php echo esc_attr($slug); ?>" <?php echo esc_attr($is_selected); ?>><?php echo esc_attr($name); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>

      <input type="hidden" name="tracking_method" id="tracking_method" value="gtm">
    </div>

  </form>
</div>

<!-- Ecommerce Events -->
<div class="convcard p-4 mt-0 rounded-3 shadow-sm mt-3 d-none">
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
        <?php esc_html_e("Select iten", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
    </div>
    <div class="col-md-4">
      <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
        <span class="material-symbols-outlined lock-icon">
          lock
        </span>
        <?php esc_html_e("Remove from cart", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
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
        <?php esc_html_e("Add to cart on item list", "enhanced-e-commerce-for-woocommerce-store"); ?>
      </span>
    </div>
    <div class="col-md-4">
      <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
        <span class="material-symbols-outlined lock-icon">
          lock
        </span>
        <?php esc_html_e("Add shipping info", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 pr-0">
      <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
        <span class="material-symbols-outlined lock-icon">
          lock
        </span>
        <?php esc_html_e("View item list", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
    </div>
    <div class="col-md-4 pr-0">
      <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
        <span class="material-symbols-outlined lock-icon">
          lock
        </span>
        <?php esc_html_e("Add to cart on single item", "enhanced-e-commerce-for-woocommerce-store"); ?>
      </span>
    </div>
    <div class="col-md-4">
      <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
        <span class="material-symbols-outlined lock-icon">
          lock
        </span><?php esc_html_e(" Add payment Info", "enhanced-e-commerce-for-woocommerce-store"); ?>
      </span>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
        <span class="material-symbols-outlined lock-icon">
          lock
        </span>
        <?php esc_html_e("View item", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
    </div>
    <div class="col-md-4">
      <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
        <span class="material-symbols-outlined lock-icon">
          lock
        </span>
        <?php esc_html_e("View cart", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
    </div>
    <div class="col-md-4">
      <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Available with Pro Plan">
        <span class="material-symbols-outlined lock-icon">
          lock
        </span>
        <?php esc_html_e("Begin checkout", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
    </div>
  </div>

  <div class="row pt-3">
    <div class="col-md-12">
      <h5 class="fw-bold-500 conv-recommended-text" style="font-size: 17px;">
        <?php esc_html_e("Recommended:", "enhanced-e-commerce-for-woocommerce-store"); ?>
      </h5>
      <h5 class="fw-normal mb-1">
        <?php esc_html_e("For complete ecommerce tracking and user browsing behavior for your Woo Shop, switch to our Starter plan.", "enhanced-e-commerce-for-woocommerce-store"); ?>
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

<!-- Feature adaption modal -->
<div class="modal fade" id="conv_featureadptmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="conv_featureadptmodalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-body p-2 py-5">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 text-center">
              <?php echo wp_kses(
                enhancad_get_plugin_image('/admin/images/logos/conv_gtm_logo.png', '', 'mb-3'),
                array(
                  'img' => array(
                    'src' => true,
                    'alt' => true,
                    'class' => true,
                    'style' => true,
                  ),
                )
              ); ?>
              <h4 class="fw-normal fs-4 my-3">
                <?php esc_html_e("Saved Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </h4>
              <div class="fs-6 lh-3 lh-base">
                <span class="fw-bold-500">
                  <?php esc_html_e("Conversios Container - GTM-K7X94DG", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>
                <span>
                  <?php esc_html_e("has been successfully connected with", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>
                <br>
                <span>
                  <?php esc_html_e("your store. Connect respective channels to automate ecommerce events and", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>
                <br>
                <span>
                  <?php esc_html_e("conversion tracking for your store.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>
              </div>

            </div>
          </div>
          <div class="row mt-5 featureadptboxes">
            <div class="col-md-4 text-center py-3 <?php echo $ga4_id == "" ? "" : "convboxactive"; ?>">
              <div class="convborder p-2 rounded">
                <?php echo wp_kses(
                  enhancad_get_plugin_image('/admin/images/logos/conv_ganalytics_logo.png', '', 'mb-3 img-32'),
                  array(
                    'img' => array(
                      'src' => true,
                      'alt' => true,
                      'class' => true,
                      'style' => true,
                    ),
                  )
                ); ?>
                <h4 class="fw-normal fs-6">
                  <?php esc_html_e("Google Analytics 4", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h4>
                <p class="py-2 text-12px">
                  <?php esc_html_e("Start collecting all the ecommerce events & conversions for data driven decision making by adding Google Analytics 4 measurement id.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </p>
                <?php if ($ga4_id == "") { ?>
                  <a href="<?php echo esc_url("admin.php?page=conversios-google-analytics&subpage=gasettings"); ?>" class="btn w-100 conv-blue-bg text-white btn-lg fs-6">
                    <?php esc_html_e("Connect Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                  </a>
                <?php } else { ?>
                  <h4 class="fw-normal fs-6 d-flex justify-content-center pt-2">
                    <?php esc_html_e("Connected", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <?php echo wp_kses(
                      enhancad_get_plugin_image('/admin/images/icon/selected.png', '', 'align-self-center ps-2'),
                      array(
                        'img' => array(
                          'src' => true,
                          'alt' => true,
                          'class' => true,
                          'style' => true,
                        ),
                      )
                    ); ?>
                  </h4>
                <?php } ?>
              </div>
            </div>

            <div class="col-md-4 text-center py-3 <?php echo $google_ads_id == "" ? "" : "convboxactive"; ?>">
              <div class="convborder p-2 rounded">
                <?php echo wp_kses(
                  enhancad_get_plugin_image('/admin/images/logos/conv_gads_logo.png', '', 'mb-3 img-32'),
                  array(
                    'img' => array(
                      'src' => true,
                      'alt' => true,
                      'class' => true,
                      'style' => true,
                    ),
                  )
                ); ?>
                <h4 class="fw-normal fs-6">
                  <?php esc_html_e("Google Ads Conversion Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h4>
                <p class="py-2 text-12px">
                  <?php esc_html_e("Optimize and improve campaign sales by automating Google Ads Conversion Tracking.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </p>

                <?php if ($google_ads_id == "") { ?>
                  <a href="<?php echo esc_url("admin.php?page=conversios-google-analytics&subpage=gadssettings"); ?>" class="btn w-100 conv-blue-bg text-white btn-lg fs-6">
                    <?php esc_html_e("Connect Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                  </a>
                <?php } else { ?>
                  <h4 class="fw-normal fs-6 d-flex justify-content-center pt-2">
                    <?php esc_html_e("Connected", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <?php echo wp_kses(
                      enhancad_get_plugin_image('/admin/images/icon/selected.png', '', 'align-self-center ps-2'),
                      array(
                        'img' => array(
                          'src' => true,
                          'alt' => true,
                          'class' => true,
                          'style' => true,
                        ),
                      )
                    ); ?>
                  </h4>
                <?php } ?>

              </div>
            </div>

            <div class="col-md-4 text-center py-3 <?php echo $fb_pixel_id == "" ? "" : "convboxactive"; ?>">
              <div class="convborder p-2 rounded">
                <?php echo wp_kses(
                  enhancad_get_plugin_image('/admin/images/logos/conv_fb_logo.png', '', 'mb-3 img-32'),
                  array(
                    'img' => array(
                      'src' => true,
                      'alt' => true,
                      'class' => true,
                      'style' => true,
                    ),
                  )
                ); ?>
                <h4 class="fw-normal fs-6">
                  <?php esc_html_e("Facebook Pixel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h4>
                <p class="py-2 text-12px">
                  <?php esc_html_e("Improve sales from FB ads and build remarketing audience by setting up FB pixel and FB Conversions API integration.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </p>

                <?php if ($fb_pixel_id == "") { ?>
                  <a href="<?php echo esc_url("admin.php?page=conversios-google-analytics&subpage=fbsettings"); ?>" class="btn w-100 conv-blue-bg text-white btn-lg fs-6">
                    <?php esc_html_e("Connect Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                  </a>
                <?php } else { ?>
                  <h4 class="fw-normal fs-6 d-flex justify-content-center pt-2">
                    <?php esc_html_e("Connected", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <?php echo wp_kses(
                      enhancad_get_plugin_image('/admin/images/icon/selected.png', '', 'align-self-center ps-2'),
                      array(
                        'img' => array(
                          'src' => true,
                          'alt' => true,
                          'class' => true,
                          'style' => true,
                        ),
                      )
                    ); ?>
                  </h4>
                <?php } ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Feature adaption modal End -->


<script>
  jQuery(function() {
    jQuery('#gtm_account_container_list').select2({
      width: '100%'
    });
    jQuery(".selecttwo_configs").select2({
      width: '100%'
    });

    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })


    //jQuery("#conv_featureadptmodal").modal("show");
    jQuery("#upgradetopro_modal_link").attr("href", '<?php echo esc_url($TVC_Admin_Helper->get_conv_pro_link_adv("popup", "gtmsettings",  "conv-link-blue fw-bold", "linkonly")); ?>');
    // Conversios JS
    jQuery('input[type=radio][name=want_to_use_your_gtm]').change(function() {
      if (this.value == '0') {
        jQuery("#use_your_gtm_id_box").hide();
        jQuery("#use_your_gtm_id_box").addClass('d-none');
      } else if (this.value == '1') {
        jQuery("#use_your_gtm_id_box").show();
        jQuery("#use_your_gtm_id_box").removeClass('d-none');
      }
    });

    jQuery(document).on('change', 'form#gtmsettings_form', function() {
      var want_to_use_your_gtm = jQuery('input[type=radio][name=want_to_use_your_gtm]:checked').val();
      var use_your_gtm_id = jQuery('#use_your_gtm_id').val();

      if (want_to_use_your_gtm == 1 && use_your_gtm_id == "") {
        jQuery('#use_your_gtm_id').addClass("conv-border-danger");
        jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-enabled-google abc");
        jQuery(".conv-btn-connect").text('Save');
      } else {
        jQuery('#use_your_gtm_id').removeClass("conv-border-danger");
        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
        jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-google abc");
        jQuery(".conv-btn-connect").text('Save');
      }

    });

    jQuery(document).on("click", ".conv-btn-connect-enabled-google", function() {

      conv_change_loadingbar("show");
      jQuery(this).addClass('disabled');
      var want_to_use_your_gtm = jQuery('input[type=radio][name=want_to_use_your_gtm]:checked').val();
      var use_your_gtm_id = jQuery('#use_your_gtm_id').val();
      var conv_disabled_users_arr = jQuery("#conv_disabled_users").val();
      var conv_disabled_users = conv_disabled_users_arr.length ? conv_disabled_users_arr : [""];

      var ga_selected_events_pre = jQuery("#ga_selected_events_ecomm").val();
      jQuery.merge(ga_selected_events_pre, jQuery("#ga_selected_events_leadgen").val())
      var conv_selected_events_arr = {
        ga: ga_selected_events_pre
      };
      var conv_selected_events = conv_selected_events_arr;

      var tracking_method = jQuery('#tracking_method').val();

      var net_revenue_setting = [];
      jQuery(".conv_revnue_checkinput").each(function() {
        if (jQuery(this).is(":checked")) {
          net_revenue_setting.push(jQuery(this).val());
        }
      });

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: {
          action: "conv_save_pixel_data",
          pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
          conv_options_data: {
            want_to_use_your_gtm: want_to_use_your_gtm,
            use_your_gtm_id: use_your_gtm_id,
            conv_disabled_users: conv_disabled_users,
            conv_selected_events: conv_selected_events,
            tracking_method: tracking_method,
            net_revenue_setting: net_revenue_setting,
          },
          conv_options_type: ["eeoptions", "eeapidata", "eeselectedevents"],
        },
        beforeSend: function() {
          jQuery(".conv-btn-connect-enabled-google").text("Saving...");
        },
        success: function(response) {
          var user_modal_txt = "Conversios Container - GTM-K7X94DG";
          if (want_to_use_your_gtm == "1") {
            user_modal_txt = "Your own GTM Container - " + use_your_gtm_id;
          }
          if (response == "0" || response == "1") {
            jQuery(".conv-btn-connect-enabled-google").text("Save");
            <?php if ($showfeaturepopup == "yes") { ?>
              jQuery("#conv_featureadptmodal").modal("show");
            <?php } else { ?>
              jQuery("#conv_save_success_txt").html('Congratulations, you have successfully connected your <br> Google Tag Manager account with <br> ' + user_modal_txt);
              jQuery("#conv_save_success_modal").modal("show");
            <?php } ?>

            conv_change_loadingbar("hide");
          }

        }
      });
    });

  });
</script>