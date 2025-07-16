<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       tatvic.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/public
 * @author     Tatvic
 */
require_once(ENHANCAD_PLUGIN_DIR . 'public/class-con-settings.php');
class Enhanced_Ecommerce_Google_Analytics_Public extends Con_Settings
{
  /**
   * Init and hook in the integration.
   *
   * @access public
   * @return void
   */
  //set plugin version
  protected $plugin_name;
  protected $version;
  protected $fb_page_view_event_id;
  protected $gtm;

  /**
   * Enhanced_Ecommerce_Google_Analytics_Public constructor.
   * @param $plugin_name
   * @param $version
   */

  public function __construct($plugin_name, $version)
  {
    parent::__construct();
    $this->gtm = new Con_GTM_WC_Tracking($plugin_name, $version);
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->plugin_name = sanitize_text_field($plugin_name);
    $this->version  = sanitize_text_field($version);

    add_action("wp", array($this, "tvc_call_hooks"));

    $this->fb_page_view_event_id = $this->get_fb_event_id();

    /*
     * start tvc_options
     */
    $current_user = wp_get_current_user();
    //$current_user ="";
    $user_id = "";
    $user_type = "guest_user";
    if (isset($current_user->ID) && $current_user->ID != 0) {
      $user_id = $current_user->ID;
      $current_user_type = 'register_user';
    }
    $this->tvc_options = array(
      "feature_product_label" => esc_html__("Feature Product", "enhanced-e-commerce-for-woocommerce-store"),
      "on_sale_label" => esc_html__("On Sale", "enhanced-e-commerce-for-woocommerce-store"),
      "affiliation" => esc_js(get_bloginfo('name')),
      "local_time" => esc_js(time()),
      "is_admin" => esc_attr(is_admin()),
      "currency" => esc_js($this->ga_LC),
      "tracking_option" => esc_js($this->tracking_option),
      "property_id" => esc_js($this->ga_id),
      "measurement_id" => esc_js($this->gm_id),
      "google_ads_id" => esc_js($this->google_ads_id),
      "google_merchant_center_id" => esc_js($this->google_merchant_id),
      "o_add_gtag_snippet" => esc_js($this->ga_ST),
      "o_enhanced_e_commerce_tracking" => esc_js($this->ga_eeT),
      "o_log_step_gest_user" => esc_js($this->ga_gUser),
      "o_impression_thresold" => esc_js($this->ga_imTh),
      "o_ip_anonymization" => esc_js($this->ga_IPA),
      //"o_ga_OPTOUT"=>esc_js($this->ga_OPTOUT),
      "ads_tracking_id" => esc_js($this->ads_tracking_id),
      "remarketing_tags" => esc_js($this->ads_ert),
      "dynamic_remarketing_tags" => esc_js($this->ads_edrt),
      "google_ads_conversion_tracking" => esc_js($this->google_ads_conversion_tracking),
      "conversio_send_to" => esc_js($this->conversio_send_to),
      "ga_EC" => esc_js($this->ga_EC),
      "page_type" => esc_js($this->add_page_type()),
      "user_id" => esc_js($user_id),
      "user_type" => esc_js($user_type),
      "day_type" => esc_js($this->add_day_type()),
      "remarketing_snippet_id" => esc_js($this->remarketing_snippet_id),
      "fb_pixel_id" => esc_js($this->fb_pixel_id),
      "fb_event_id" => $this->get_fb_event_id(),
      "tvc_ajax_url" => esc_url(admin_url('admin-ajax.php')),
      "tiktok_event_id" => $this->generate_unique_event_id(),
      "snapchat_event_id" =>  $this->generate_unique_event_id(),
      "gads_remarketing_id" => esc_js($this->gads_remarketing_id)
    );
    /*
     * end tvc_options
     */


    //add_action('wp_ajax_stf_converstion_api', array($this, 'stf_converstion_api'));
  }
  public function tvc_call_hooks()
  {
    /**
     * add global site tag js or settings
     **/

    add_action("wp_head", array($this->gtm, "begin_userdata"));
    add_action("wp_body_open", array($this->gtm, "add_gtm_no_script"));

    add_action("wp_footer", array($this->gtm, "add_gtm_data_layer"));

    /**Bind Product data **/
    // product list collection method
    if (isset($this->c_t_o['tvc_product_list_data_collection_method']) && $this->c_t_o['tvc_product_list_data_collection_method']) {
      add_action($this->c_t_o['tvc_product_list_data_collection_method'], array($this->gtm, "product_list_view"));
      add_filter('woocommerce_blocks_product_grid_item_html', array($this->gtm, "conv_product_list_block_view"), 10, 3);
    } else {
      add_action("woocommerce_after_shop_loop_item", array($this->gtm, "product_list_view"));
      add_filter('woocommerce_blocks_product_grid_item_html', array($this->gtm, "conv_product_list_block_view"), 10, 3);
    }

    //Thnak you page collection method
    $tvc_thankyou_data_collection_method = isset($this->c_t_o['tvc_thankyou_data_collection_method']) ? $this->c_t_o['tvc_thankyou_data_collection_method'] : "woocommerce_thankyou";
    if ($tvc_thankyou_data_collection_method == "on_page") {
      add_action("wp_head", array($this->gtm, "product_thankyou_view"));
    } else if ($tvc_thankyou_data_collection_method) {
      add_action($tvc_thankyou_data_collection_method, array($this->gtm, "product_thankyou_view"));
    } else {
      add_action("woocommerce_thankyou", array($this->gtm, "product_thankyou_view"));
    }

    //product detail page collection method
    $tvc_product_detail_data_collection_method = isset($this->c_t_o['tvc_product_detail_data_collection_method']) ? $this->c_t_o['tvc_product_detail_data_collection_method'] : "woocommerce_after_single_product";
    if ($tvc_product_detail_data_collection_method == "on_page") {
      add_action("wp_head", array($this->gtm, "product_detail_view"));
    } else if ($tvc_product_detail_data_collection_method) {
      add_action($tvc_product_detail_data_collection_method, array($this->gtm, "product_detail_view"));
    } else {
      //product var init
      add_action("woocommerce_after_single_product", array($this->gtm, "product_detail_view"));
    }

    //view cart hook
    add_action("woocommerce_after_cart", array($this->gtm, "product_cart_view"));
    add_action("woocommerce_blocks_enqueue_cart_block_scripts_after", array($this->gtm, "product_cart_view"));

    //checkout page tracking
    $tvc_checkout_data_collection_method = isset($this->c_t_o['tvc_checkout_data_collection_method']) ? $this->c_t_o['tvc_checkout_data_collection_method'] : "woocommerce_before_checkout_form";
    if ($tvc_checkout_data_collection_method == "on_page" && is_checkout()) {
      add_action("wp_head", array($this->gtm, "checkout_step_view"));
    } else if ($tvc_checkout_data_collection_method) {
      add_action($tvc_checkout_data_collection_method, array($this->gtm, "checkout_step_view"));
    } else {
      add_action("woocommerce_before_checkout_form", array($this->gtm, "checkout_step_view"));
    }
  }

  //Need to check
  /**
   * Google Analytics content grouping
   * Pages: Home, Category, Product, Cart, Checkout, Search ,Shop, Thankyou and Others
   *
   * @access public
   * @return void
   */
  function add_page_type()
  {

    if (is_home() || is_front_page()) {
      $t_page_name = esc_html__("Home Page", "enhanced-e-commerce-for-woocommerce-store");
    } else if (is_product_category()) {
      $t_page_name = esc_html__("Category Pages", "enhanced-e-commerce-for-woocommerce-store");
    } else if (is_product()) {
      $t_page_name = esc_html__("Product Pages", "enhanced-e-commerce-for-woocommerce-store");
    } else if (is_cart()) {
      $t_page_name = esc_html__("Cart Page", "enhanced-e-commerce-for-woocommerce-store");
    } else if (is_order_received_page()) {
      $t_page_name = esc_html__("Thankyou Page", "enhanced-e-commerce-for-woocommerce-store");
    } else if (is_checkout()) {
      $t_page_name = esc_html__("Checkout Page", "enhanced-e-commerce-for-woocommerce-store");
    } else if (is_search()) {
      $t_page_name = esc_html__("Search Page", "enhanced-e-commerce-for-woocommerce-store");
    } else if (is_shop()) {
      $t_page_name = esc_html__("Shop Page", "enhanced-e-commerce-for-woocommerce-store");
    } else if (is_404()) {
      $t_page_name = esc_html__("404 Error Pages", "enhanced-e-commerce-for-woocommerce-store");
    } else {
      $t_page_name = esc_html__("Others", "enhanced-e-commerce-for-woocommerce-store");
    }
  }


  //Need to check
  /**
   * Google Analytics Day type
   *
   * @access public
   * @return void
   */
  function add_day_type()
  {
    $date = gmdate("Y-m-d");
    $day = strtolower(gmdate('l', strtotime($date)));
    if (($day == "saturday") || ($day == "sunday")) {
      $day_type = esc_html__("weekend", "enhanced-e-commerce-for-woocommerce-store");
    } else {
      $day_type = esc_html__("weekday", "enhanced-e-commerce-for-woocommerce-store");
    }
    return $day_type;
  }

  /*
   * Site verification using tag method
   */
  public function add_google_site_verification_tag()
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
    if (isset($ee_additional_data['add_site_varification_tag']) && isset($ee_additional_data['site_varification_tag_val']) && $ee_additional_data['add_site_varification_tag'] == 1 && $ee_additional_data['site_varification_tag_val'] != "") {
      echo wp_kses(
        html_entity_decode(base64_decode($ee_additional_data['site_varification_tag_val'])),
        array(
          'meta' => array(
            'name' => array(),
            'content' => array()
          )

        )
      );
    }
  }
  /**
   * Get store meta data for trouble shoot
   * @access public
   * @return void
   */
  function tvc_store_meta_data()
  {
    //only on home page
    global $woocommerce;
    $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    $googleDetail = array();
    if (isset($google_detail['setting'])) {
      $googleDetail = $google_detail['setting'];
    }
    $tvc_sMetaData = array(
      'tvc_wcv' => esc_js($woocommerce->version),
      'tvc_wpv' => esc_js(get_bloginfo('version')),
      'tvc_eev' => esc_js($this->tvc_eeVer),
      'tvc_cnf' => array(
        't_cg' => esc_js($this->ga_CG),
        't_ec' => esc_js($this->ga_EC),
        't_ee' => esc_js($this->ga_eeT),
        't_df' => esc_js($this->ga_DF),
        't_gUser' => esc_js($this->ga_gUser),
        't_UAen' => esc_js($this->ga_ST),
        't_thr' => esc_js($this->ga_imTh),
        't_IPA' => esc_js($this->ga_IPA),
        //'t_OptOut' => esc_js($this->ga_OPTOUT),
        't_PrivacyPolicy' => esc_js($this->ga_PrivacyPolicy)
      ),
      'tvc_sub_data' => array(
        'sub_id' => esc_js(isset($googleDetail->id) ? sanitize_text_field($googleDetail->id) : ""),
        'cu_id' => esc_js(isset($googleDetail->customer_id) ? sanitize_text_field($googleDetail->customer_id) : ""),
        'pl_id' => esc_js(isset($googleDetail->plan_id) ? sanitize_text_field($googleDetail->plan_id) : ""),
        'ga_tra_option' => esc_js(isset($googleDetail->tracking_option) ? sanitize_text_field($googleDetail->tracking_option) : ""),
        'ga_property_id' => esc_js(isset($googleDetail->property_id) ? sanitize_text_field($googleDetail->property_id) : ""),
        'ga_measurement_id' => esc_js(isset($googleDetail->measurement_id) ? sanitize_text_field($googleDetail->measurement_id) : ""),
        'ga_ads_id' => esc_js(isset($googleDetail->google_ads_id) ? sanitize_text_field($googleDetail->google_ads_id) : ""),
        'ga_gmc_id' => esc_js(isset($googleDetail->google_merchant_center_id) ? sanitize_text_field($googleDetail->google_merchant_center_id) : ""),
        'ga_gmc_id_p' => esc_js(isset($googleDetail->merchant_id) ? sanitize_text_field($googleDetail->merchant_id) : ""),
        'op_gtag_js' => esc_js(isset($googleDetail->add_gtag_snippet) ? sanitize_text_field($googleDetail->add_gtag_snippet) : ""),
        'op_en_e_t' => esc_js(isset($googleDetail->enhanced_e_commerce_tracking) ? sanitize_text_field($googleDetail->enhanced_e_commerce_tracking) : ""),
        'op_rm_t_t' => esc_js(isset($googleDetail->remarketing_tags) ? sanitize_text_field($googleDetail->remarketing_tags) : ""),
        'op_dy_rm_t_t' => esc_js(isset($googleDetail->dynamic_remarketing_tags) ? esc_attr($googleDetail->dynamic_remarketing_tags) : ""),
        'op_li_ga_wi_ads' => esc_js(isset($googleDetail->link_google_analytics_with_google_ads) ? sanitize_text_field($googleDetail->link_google_analytics_with_google_ads) : ""),
        'gmc_is_product_sync' => esc_js(isset($googleDetail->is_product_sync) ? sanitize_text_field($googleDetail->is_product_sync) : ""),
        'gmc_is_site_verified' => esc_js(isset($googleDetail->is_site_verified) ? sanitize_text_field($googleDetail->is_site_verified) : ""),
        'gmc_is_domain_claim' => esc_js(isset($googleDetail->is_domain_claim) ? sanitize_text_field($googleDetail->is_domain_claim) : ""),
        'gmc_product_count' => esc_js(isset($googleDetail->product_count) ? sanitize_text_field($googleDetail->product_count) : ""),
        'fb_pixel_id' => esc_js($this->fb_pixel_id),
        'tracking_method' => esc_js($this->tracking_method),
        'user_gtm_id' => ($this->tracking_method == 'gtm' && $this->want_to_use_your_gtm == 1) ? esc_js($this->use_your_gtm_id) : (($this->tracking_method == 'gtm') ? "conversios-gtm" : "")
      )
    );
    $this->wc_version_compare("tvc_smd=" . wp_json_encode($tvc_sMetaData) . ";");
  }


  /**
   * woocommerce version compare
   *
   * @access public
   * @return void
   */
  function wc_version_compare($codeSnippet)
  {
    global $woocommerce;
    if (version_compare($woocommerce->version, "2.1", ">=")) {
      wc_enqueue_js($codeSnippet);
    } else {
      $woocommerce->add_inline_js($codeSnippet);
    }
  }
}
/**
 * GTM Tracking Data Layer Push
 **/
class Con_GTM_WC_Tracking extends Con_Settings
{
  protected $plugin_name;
  protected $version;
  protected $user_data;
  public function __construct($plugin_name, $version)
  {
    parent::__construct();
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->tvc_options = array(
      "affiliation" => esc_js(get_bloginfo('name')),
      "is_admin" => esc_attr(is_admin()),
      "currency" => esc_js($this->ga_LC),
      "tracking_option" => esc_js($this->tracking_option),
      "property_id" => esc_js($this->ga_id),
      "measurement_id" => esc_js($this->gm_id),
      "google_ads_id" => esc_js($this->google_ads_id),
      "fb_pixel_id" => esc_js($this->fb_pixel_id),
      "fb_event_id" => $this->get_fb_event_id(),
      "tvc_ajax_url" => esc_url(admin_url('admin-ajax.php')),
      "snapchat_ads_pixel_id" => esc_js($this->snapchat_ads_pixel_id),
      "linkedin_insight_id" => esc_js($this->linkedin_insight_id),
      "snapchat_event_id" => $this->generate_unique_event_id(),
      "tiKtok_ads_pixel_id" => esc_js($this->tiKtok_ads_pixel_id),
      "tiktok_event_id" => $this->generate_unique_event_id(),
    );
    // Added filter to add data attributes to exclude cloud fare cache
    add_filter('script_loader_tag',  array($this, "exclude_congtm_from_cf_loader"), 10, 2);
  }



  /**
   * begin datalayer like settings
   **/
  public function begin_userdata()
  {
    if ($this->disable_tracking($this->ga_eeT)) {
      return;
    }
    /*start uset tracking*/
    $enhanced_conversion = array();
    global $woocommerce;
    if ($this->ga_EC) {
      //login user
      if (is_user_logged_in() && $this->ga_EC) {
        global $current_user;
        wp_get_current_user();
        $billing_country    = WC()->customer->get_billing_country();
        $calling_code = WC()->countries->get_country_calling_code($billing_country);
        $phone = get_user_meta($current_user->ID, 'billing_phone', true);
        if ($phone != "") {
          $phone = str_replace($calling_code, "", $phone);
          $phone = $calling_code . $phone;
          $enhanced_conversion["phone_number"] = esc_js($phone);
        }
        $email = esc_js($current_user->user_email);
        if ($email != "") {
          $enhanced_conversion["email"] = esc_js($email);
        }
        $first_name         = esc_js($current_user->user_firstname);
        if ($first_name != "") {
          $enhanced_conversion["address"]["first_name"] = esc_js($first_name);
        }
        $last_name          = $current_user->user_lastname;
        if ($last_name != "") {
          $enhanced_conversion["address"]["last_name"] = esc_js($last_name);
        }
        $billing_address_1  = WC()->customer->get_billing_address_1();
        if ($billing_address_1 != "") {
          $enhanced_conversion["address"]["street"] = esc_js($billing_address_1);
        }
        $billing_postcode   = WC()->customer->get_billing_postcode();
        if ($billing_postcode != "") {
          $enhanced_conversion["address"]["postal_code"] = esc_js($billing_postcode);
        }
        $billing_city       = WC()->customer->get_billing_city();
        if ($billing_city != "") {
          $enhanced_conversion["address"]["city"] = esc_js($billing_city);
        }
        $billing_state      = WC()->customer->get_billing_state();
        if ($billing_state != "") {
          $enhanced_conversion["address"]["region"] = esc_js($billing_state);
        }
        $billing_country    = WC()->customer->get_billing_country();
        if ($billing_country != "") {
          $enhanced_conversion["address"]["country"] = esc_js($billing_country);
        }
      } else if ($this->ga_EC == 1) { // get user       
        $order = "";
        $order_id = "";
        if ($order_id == null && is_order_received_page()) {
          $order = $this->tvc_get_order_from_order_received_page();
          $order_id = $order->get_id();
        }
        if ($order_id) {
          $billing_country  = $order->get_billing_country();
          $calling_code = WC()->countries->get_country_calling_code($billing_country);
          $billing_email  = $order->get_billing_email();
          if ($billing_email != "") {
            $enhanced_conversion["email"] = esc_js($billing_email);
          }
          $billing_phone  = $order->get_billing_phone();
          if ($billing_phone != "") {
            $billing_phone = str_replace($calling_code, "", $billing_phone);
            $billing_phone = $calling_code . $billing_phone;
            $enhanced_conversion["phone_number"] = esc_js($billing_phone);
          }
          $billing_first_name = $order->get_billing_first_name();
          if ($billing_first_name != "") {
            $enhanced_conversion["address"]["first_name"] = esc_js($billing_first_name);
          }
          $billing_last_name = $order->get_billing_last_name();
          if ($billing_last_name != "") {
            $enhanced_conversion["address"]["last_name"] = esc_js($billing_last_name);
          }
          $billing_address_1 = $order->get_billing_address_1();
          if ($billing_address_1 != "") {
            $enhanced_conversion["address"]["street"] = esc_js($billing_address_1);
          }
          $billing_city = $order->get_billing_city();
          if ($billing_city != "") {
            $enhanced_conversion["address"]["city"] = esc_js($billing_city);
          }
          $billing_state = $order->get_billing_state();
          if ($billing_state != "") {
            $enhanced_conversion["address"]["region"] = esc_js($billing_state);
          }
          $billing_postcode = $order->get_billing_postcode();
          if ($billing_postcode != "") {
            $enhanced_conversion["address"]["postal_code"] = esc_js($billing_postcode);
          }
          $billing_country = $order->get_billing_country();
          if ($billing_country != "") {
            $enhanced_conversion["address"]["country"] = esc_js($billing_country);
          }
        }
      }
      $this->user_data = $enhanced_conversion;
    }
  }


  /**
   * product list Block
   **/
  public function conv_product_list_block_view($html, $attributes, $product)
  {
    $listtype = '';
    if (isset($product->id)) {
      $this->con_product_list_item_extra_tag($product, $listtype);
    }
    return $html;
  }

  /**
   * product list page
   **/
  public function product_list_view()
  {
    global $product, $woocommerce_loop;
    if ($this->disable_tracking($this->ga_eeT)) {
      return;
    }
    $listtype = '';
    if (isset($woocommerce_loop['listtype']) && ('' !== $woocommerce_loop['listtype'])) {
      $listtype = $woocommerce_loop['listtype'];
    }
    $this->con_product_list_item_extra_tag($product, $listtype);
  }
  /**
   * product page
   **/
  public function product_detail_view()
  {
    if ($this->disable_tracking($this->ga_eeT) || !is_product()) {
      return;
    }
    global  $wp_query, $woocommerce, $product, $con_view_item;
    $con_view_item = $this->con_item_product(
      $product,
      array(
        'productlink'  => get_permalink()
      )
    );
  }
  /**
   * product cart page
   **/
  public function product_cart_view()
  {
    if ($this->disable_tracking($this->ga_eeT)) {
      return;
    }
    global $woocommerce, $con_cart_item_list;
    foreach ($woocommerce->cart->get_cart() as $key => $item) {
      $product_id = $item["product_id"];
      //$product = wc_get_product($product_id);

      $product = apply_filters('woocommerce_cart_item_product', $item['data'], $item, $key);

      $remove_cart_item_link = "";
      if (version_compare($woocommerce->version, "3.3", "<")) {
        $remove_cart_item_link = html_entity_decode($woocommerce->cart->get_remove_url($key));
      } else {
        $remove_cart_item_link = html_entity_decode(wc_get_cart_remove_url($key));
      }
      $con_cart_item_list[] = $this->con_item_product(
        $product,
        array(
          "productlink"  => get_permalink(),
          "quantity" => (float) $item["quantity"],
          "remove_cart_link" => $remove_cart_item_link
        )
      );
      $con_cart_item_list["value"] = WC()->cart->total;
    }
  }
  /**
   * product checkout page
   **/
  public function checkout_step_view()
  {
    global $woocommerce, $con_checkout_cart_item_list;
    foreach ($woocommerce->cart->get_cart() as $key => $item) {
      $product_id = $item["product_id"];
      $product_id = $item["product_id"];
      if ($item["variation_id"] != 0) {
        $product_id = $item["variation_id"];
      }
      //$product = wc_get_product($product_id);
      $product = apply_filters('woocommerce_cart_item_product', $item['data'], $item, $key);
      $con_checkout_cart_item_list[] = $this->con_item_product(
        $product,
        array(
          "productlink"  => get_permalink(),
          "quantity" => (float) $item["quantity"]
        )
      );
      $con_checkout_cart_item_list["value"] = WC()->cart->total;
    }
  }
  /**
   * Thank You page
   **/
  public function product_thankyou_view($order_id = null)
  {
    global $woocommerce, $con_ordered_item_list;
    $order = "";
    if ($order_id == null && is_order_received_page()) {
      $order = $this->tvc_get_order_from_order_received_page();
      $order_id = $order->get_id();
    } else {
      $order = new WC_Order($order_id);
    }

    if ($this->disable_tracking($this->ga_eeT) || get_post_meta($order_id, "_tracked", true) == 1 || $order->get_meta('_tracked') || !is_order_received_page()) {
      return;
    }

    if ($order->get_status() === 'failed') {
      return;
    }

    $order_items = $order->get_items();
    if ($order_items) {
      foreach ($order_items as $item) {
        $product = $item->get_product();

        $taxinc_product = ('incl' === get_option('woocommerce_tax_display_shop'));
        $productprice = round((float) $order->get_item_total($item, $taxinc_product), 2);

        $con_ordered_item_list[] = $this->con_item_product(
          $product,
          array(
            "productlink"  => get_permalink(),
            "quantity" => (float) $item["quantity"],
            "price" => $productprice
          )
        );
      }
      $con_ordered_item_list["value"] = esc_js($order->get_total());
      $con_ordered_item_list["transaction_id"] = esc_js($order->get_order_number());
      $con_ordered_item_list["affiliation"] = esc_js(get_bloginfo('name'));
      $con_ordered_item_list["tax"] = esc_js($order->get_total_tax());
      $con_ordered_item_list["shipping"] = esc_js($order->get_shipping_total());
      $con_ordered_item_list["coupon"] = esc_js(implode(', ', ($woocommerce->version > "3.7" ? $order->get_coupon_codes() : $order->get_used_coupons())));
    }
    $order->update_meta_data('_tracked', 1);
    $order->save();
    update_post_meta($order_id, "_tracked", 1);
  }
  /** 
   * dataLayer for setting and GTM global tag
   **/
  public function add_gtm_begin_datalayer_js($data_layer)
  {
    $gtm_id = "GTM-K7X94DG";
    $gtm_url = "https://www.googletagmanager.com";
    $has_html5_support    = current_theme_supports('html5');
    echo '<script data-cfasync="false" data-pagespeed-no-defer' . ($has_html5_support ? ' type="text/javascript"' : '') . '>
      window.dataLayer = window.dataLayer || [];
      dataLayer.push(' . wp_json_encode($data_layer) . ');
    </script>';
?>
    <!-- Google Tag Manager Conversios-->
    <script>
      (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
          'gtm.start': new Date().getTime(),
          event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
          j = d.createElement(s),
          dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
          '<?php echo esc_js($gtm_url); ?>/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
      })(window, document, 'script', 'dataLayer', '<?php echo esc_js($gtm_id); ?>');
    </script>
    <!-- End Google Tag Manager Conversios -->

  <?php
  }
  /** 
   * DataLayer to JS
   **/
  public function add_gtm_datalayer_js($data_layer)
  {
    $has_html5_support    = current_theme_supports('html5');
    echo '<script data-cfasync="false" data-pagespeed-no-defer' . ($has_html5_support ? ' type="text/javascript"' : '') . '>
      window.dataLayer = window.dataLayer || [];
      dataLayer.push({ecommerce: null});
      dataLayer.push(' . wp_json_encode($data_layer) . ');
    </script>
    ';
  }

  public function enqueue_scripts()
  {
    wp_enqueue_script(esc_js($this->plugin_name), esc_url(ENHANCAD_PLUGIN_URL . '/public/js/con-gtm-google-analytics.js'), array('jquery'), esc_js($this->version), false);
    $nonce = wp_create_nonce('conv_aio_nonce');
    wp_localize_script(esc_js($this->plugin_name), 'ConvAioGlobal', array('nonce' => $nonce));
  }

  public function exclude_congtm_from_cf_loader($tag, $handle)
  {
    $excluded_scripts = array(esc_js($this->plugin_name));
    if (in_array($handle, $excluded_scripts)) {
      $tag = str_replace('<script', '<script data-cfasync="false" data-no-optimize="1" data-pagespeed-no-defer', $tag);
    }
    return $tag;
  }
  public function add_gtm_no_script()
  {
    if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
      $base_country_code = WC()->countries->get_base_country();
      if ($base_country_code == "US") {
        $gtm_id = ($this->want_to_use_your_gtm && $this->use_your_gtm_id != "") ? $this->use_your_gtm_id : "GTM-NGTQ2D2P";
      } else {
        $gtm_id = ($this->want_to_use_your_gtm && $this->use_your_gtm_id != "") ? $this->use_your_gtm_id : "GTM-K7X94DG";
      }
    } else {
      $gtm_id = ($this->want_to_use_your_gtm && $this->use_your_gtm_id != "") ? $this->use_your_gtm_id : "GTM-K7X94DG";
    }
    $gtm_url = "https://www.googletagmanager.com";
  ?>
    <!-- Google Tag Manager (noscript) conversios -->
    <noscript><iframe src="<?php echo esc_js($gtm_url); ?>/ns.html?id=<?php echo esc_js($gtm_id); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) conversios -->
    <?php
  }

  /**
   * Creat DataLyer object for create JS data layer
   **/
  public function add_gtm_data_layer()
  {
    if ($this->disable_tracking($this->ga_eeT)) {
      return;
    }

    $affiliation = get_bloginfo('name');
    $impression_threshold = $this->ga_imTh;
    global $con_view_item_list, $con_view_item, $con_cart_item_list, $con_checkout_cart_item_list, $con_ordered_item_list;

    /**
     * Thankyou Page
     **/
    $fb_event_id = $this->get_fb_event_id();
    $tiktok_event_id = $this->generate_unique_event_id();
    $snapchat_event_id =  $this->generate_unique_event_id();
    if (empty($con_ordered_item_list)) {
      $con_ordered_item_list = array(); //define empty array so if empty
    } else {
      $dataLayer = array();
      $dataLayer["event"] = "purchase";
      if ($this->fb_pixel_id != "") {
        $dataLayer["fb_event_id"] = $fb_event_id;
      }

      if ($this->tiKtok_ads_pixel_id != "") {
        $dataLayer["tiktok_event_id"] = $tiktok_event_id;
      }
      $content_ids = array();
      $fb_contents = array();
      if (!empty($con_ordered_item_list)) {
        $trans_val_all = (isset($con_ordered_item_list["value"])) ? $con_ordered_item_list["value"] : 0;
        $trans_val_tax = (isset($con_ordered_item_list["tax"])) ? $con_ordered_item_list["tax"] : 0;
        $trans_val_shipping = (isset($con_ordered_item_list["shipping"])) ? $con_ordered_item_list["shipping"] : 0;

        $trans_val = (isset($con_ordered_item_list["value"])) ? $con_ordered_item_list["value"] : "";

        if (isset($this->net_revenue_setting) && !empty($this->net_revenue_setting)) {
          if (in_array('subtotal', $this->net_revenue_setting)) {
            $trans_val = $trans_val_all - $trans_val_tax - $trans_val_shipping;
          }
          if (in_array('tax', $this->net_revenue_setting)) {
            $trans_val = $trans_val + $trans_val_tax;
          }
          if (in_array('shipping', $this->net_revenue_setting)) {
            $trans_val = $trans_val + $trans_val_shipping;
          }
        }

        $dataLayer["ecommerce"]["transaction_id"] = (isset($con_ordered_item_list["transaction_id"])) ? $con_ordered_item_list["transaction_id"] : "";
        $dataLayer["ecommerce"]["value"] = round($trans_val, 2);
        $dataLayer["ecommerce"]["affiliation"] = (isset($con_ordered_item_list["affiliation"])) ? $con_ordered_item_list["affiliation"] : "";
        $dataLayer["ecommerce"]["tax"] = (isset($con_ordered_item_list["tax"])) ? (float) $con_ordered_item_list["tax"] : "";
        $dataLayer["ecommerce"]["shipping"] = (isset($con_ordered_item_list["shipping"])) ? (float) $con_ordered_item_list["shipping"] : "";
        $dataLayer["ecommerce"]["coupon"] = (isset($con_ordered_item_list["coupon"])) ? $con_ordered_item_list["coupon"] : "";

        $dataLayer["ecommerce"]["currency"] =  $this->ga_LC;
        unset($con_ordered_item_list["transaction_id"]);
        unset($con_ordered_item_list["value"]);
        unset($con_ordered_item_list["affiliation"]);
        unset($con_ordered_item_list["tax"]);
        unset($con_ordered_item_list["shipping"]);
        unset($con_ordered_item_list["coupon"]);

        foreach ($con_ordered_item_list as $key => $view_item) {
          $dataLayer["ecommerce"]["items"][] =
            array(
              "item_id" => isset($view_item["id"]) ? esc_js($view_item["id"]) : "",
              "item_name" => isset($view_item["name"]) ? esc_js($view_item["name"]) : "",
              "affiliation" => $affiliation,
              "currency" => $this->ga_LC,
              "item_category" => isset($view_item["category"]) ? esc_js($view_item["category"]) : "",
              "price" => isset($view_item["price"]) ? (float) esc_js($view_item["price"]) : "",
              "quantity" => isset($view_item["quantity"]) ? (float) esc_js($view_item["quantity"]) : ""
            );
          $content_ids[] = "product.id" . (isset($view_item["id"]) ? esc_js($view_item["id"]) : "");
          $fb_contents[] = array(
            "id" => isset($view_item["id"]) ? esc_js($view_item["id"]) : "",
            "quantity" => isset($view_item["quantity"]) ? esc_js($view_item["quantity"]) : "1",
            "item_price" => isset($view_item["price"]) ? esc_js($view_item["price"]) : "",
            //"delivery_category" => isset($view_item["category"])?esc_js($view_item["category"]):""
          );
          $snap_contents["item_ids"][]  =  isset($view_item['id']) ? sanitize_text_field($view_item['id']) : "";
          $snap_contents["quantity"][] = isset($view_item['quantity']) ? sanitize_text_field($view_item['quantity']) : "1";
          $snap_contents["item_price"][] = isset($view_item['price']) ? sanitize_text_field($view_item['price'])  : "";
          $snap_contents["productlink"][]  = get_permalink();
          $snap_contents["category"][] = isset($view_item['category']) ? sanitize_text_field($view_item['category']) : "";
          $snap_contents["currency"][] = $this->ga_LC;
        }
      }
      $this->add_gtm_datalayer_js($dataLayer);
    }
    /**
     * Checkout Page
     **/
    $fb_event_id = $this->get_fb_event_id();
    $tiktok_event_id = $this->generate_unique_event_id();
    $snapchat_event_id =  $this->generate_unique_event_id();
    if (empty($con_checkout_cart_item_list)) {
      $con_checkout_cart_item_list = array(); //define empty array so if empty
    } else {
      $dataLayer = array();
      $dataLayer["event"] = "begin_checkout";
      if ($this->fb_pixel_id != "") {
        $dataLayer["fb_event_id"] = $fb_event_id;
      }
      if ($this->tiKtok_ads_pixel_id != "") {
        $dataLayer["tiktok_event_id"] = $tiktok_event_id;
      }
      $content_ids = array();
      $fb_contents = array();
      if (!empty($con_checkout_cart_item_list)) {
        if (isset($con_checkout_cart_item_list["value"]) && $con_checkout_cart_item_list["value"]) {
          $dataLayer["ecommerce"]["value"] = (float) $con_checkout_cart_item_list["value"];
        }
        $dataLayer["ecommerce"]["currency"] =  $this->ga_LC;
        unset($con_checkout_cart_item_list["value"]);
        foreach ($con_checkout_cart_item_list as $key => $view_item) {
          $dataLayer["ecommerce"]["items"][] =
            array(
              "item_id" => isset($view_item["id"]) ? esc_js($view_item["id"]) : "",
              "item_name" => isset($view_item["name"]) ? esc_js($view_item["name"]) : "",
              "affiliation" => $affiliation,
              "currency" => $this->ga_LC,
              "item_category" => isset($view_item["category"]) ? esc_js($view_item["category"]) : "",
              "price" => isset($view_item["price"]) ? (float) esc_js($view_item["price"]) : "",
              "quantity" => isset($view_item["quantity"]) ? (int) esc_js($view_item["quantity"]) : ""
            );
          $content_ids[] = "product.id" . (isset($view_item["id"]) ? esc_js($view_item["id"]) : "");
          $fb_contents[] = array(
            "id" => isset($view_item["id"]) ? esc_js($view_item["id"]) : "",
            "quantity" => isset($view_item["quantity"]) ? esc_js($view_item["quantity"]) : "1",
            "item_price" => isset($view_item["price"]) ? esc_js($view_item["price"]) : "",
            //"delivery_category" => isset($view_item["category"])?esc_js($view_item["category"]):""
          );
          $snap_contents["item_ids"][]  =  isset($view_item['id']) ? sanitize_text_field($view_item['id']) : "";
          $snap_contents["quantity"][] = isset($view_item['quantity']) ? sanitize_text_field($view_item['quantity']) : "1";
          $snap_contents["item_price"][] = isset($view_item['price']) ? sanitize_text_field($view_item['price'])  : "";
          $snap_contents["productlink"][]  = get_permalink();
          $snap_contents["category"][] = isset($view_item['category']) ? sanitize_text_field($view_item['category']) : "";
          $snap_contents["currency"][] = $this->ga_LC;
        }
      }

      if (!$this->disable_tracking($this->ga_eeT, "begin_checkout")) {
        $this->add_gtm_datalayer_js($dataLayer);
      }

      $checkout_step_2_selector = (isset($this->c_t_o['tvc_checkout_step_2_selector']) && $this->c_t_o['tvc_checkout_step_2_selector'] == "custom") ? $this->c_t_o : array();
      $checkout_step_2_selector = $this->get_selector_val_from_array_for_gmt($checkout_step_2_selector, 'tvc_checkout_step_2_selector');
      $checkout_step_2_selector = ($checkout_step_2_selector) ? $checkout_step_2_selector : "input[name=billing_first_name], .wc-block-checkout__form #shipping-first_name, .wc-block-checkout__form #billing-first_name";

      $checkout_step_3_selector = (isset($this->c_t_o['tvc_checkout_step_3_selector']) && $this->c_t_o['tvc_checkout_step_3_selector'] == "custom") ? $this->c_t_o : array();
      $checkout_step_3_selector = $this->get_selector_val_from_array_for_gmt($checkout_step_3_selector, 'tvc_checkout_step_3_selector');
      $checkout_step_3_selector = ($checkout_step_3_selector) ? $checkout_step_3_selector : "#place_order";

    ?>
      <script data-cfasync="false" data-no-optimize="1" data-pagespeed-no-defer>
        <?php if (!$this->disable_tracking($this->ga_eeT, "add_shipping_info")) { ?>
          jQuery(document.body).on("focus", "<?php echo esc_js($checkout_step_2_selector); ?>", function(event) {
            tvc_js = new TVC_GTM_Enhanced(<?php echo wp_json_encode($this->tvc_options); ?>);
            tvc_js.checkout_step_2_tracking();
          });
        <?php } ?>

        <?php if (!$this->disable_tracking($this->ga_eeT, "add_payment_info")) { ?>
          jQuery(document.body).on("click", "<?php echo esc_js($checkout_step_3_selector); ?>", function(event) {
            tvc_js = new TVC_GTM_Enhanced(<?php echo wp_json_encode($this->tvc_options); ?>);
            tvc_js.checkout_step_3_tracking();
          });
        <?php } ?>
        jQuery(function() {
          if (window.wp && wp.hooks && typeof wp.hooks.addAction === 'function') {
            window.wp.hooks.addAction('experimental__woocommerce_blocks-checkout-submit', 'conv_apinfo_hook', function() {
              tvc_js = new TVC_GTM_Enhanced(<?php echo wp_json_encode($this->tvc_options); ?>);
              tvc_js.checkout_step_3_tracking();
            });
          }
        });
      </script>

      <?php

    }

    /**
     * Cart Page
     **/
    if (!$this->disable_tracking($this->ga_eeT, "view_cart")) {
      if (empty($con_cart_item_list)) {
        $con_cart_item_list = array(); //define empty array so if empty
      } else {
        $dataLayer = array();
        $dataLayer["event"] = "view_cart";
        if (!empty($con_cart_item_list)) {
          if (isset($con_cart_item_list["value"]) && $con_cart_item_list["value"]) {
            $dataLayer["ecommerce"]["value"] = (float) $con_cart_item_list["value"];
          }
          $dataLayer["ecommerce"]["currency"] =  $this->ga_LC;
          unset($con_cart_item_list["value"]);
          foreach ($con_cart_item_list as $key => $view_item) {
            $dataLayer["ecommerce"]["items"][] =
              array(
                "item_id" => isset($view_item["id"]) ? esc_js($view_item["id"]) : "",
                "item_name" => isset($view_item["name"]) ? esc_js($view_item["name"]) : "",
                "affiliation" => $affiliation,
                "currency" => $this->ga_LC,
                "item_category" => isset($view_item["category"]) ? esc_js($view_item["category"]) : "",
                "price" => isset($view_item["price"]) ? (float) esc_js($view_item["price"]) : "",
                "quantity" => isset($view_item["quantity"]) ? (float) esc_js($view_item["quantity"]) : ""
              );
          }
        }
        $this->add_gtm_datalayer_js($dataLayer);

        /*** Remove Cart item ***/
        if (!$this->disable_tracking($this->ga_eeT, "remove_from_cart")) {
      ?>
          <script data-cfasync="false" data-no-optimize="1" data-pagespeed-no-defer>
            window.conCarttList = window.productList || [];
            conCarttList.push(<?php echo wp_json_encode($con_cart_item_list); ?>);
            jQuery(document.body).on("click", "a[href*=\"?remove_item\"]", function(event) {
              tvc_js = new TVC_GTM_Enhanced(<?php echo wp_json_encode($this->tvc_options); ?>);
              tvc_js.remove_item_click(this);
            });
          </script>
        <?php
        }
      }
    }

    /**
     * Product detail page
     **/
    $fb_event_id = $this->get_fb_event_id();
    $tiktok_event_id = $this->generate_unique_event_id();
    $snapchat_event_id =  $this->generate_unique_event_id();
    if (empty($con_view_item)) {
      $con_view_item = array(); //define empty array so if empty
    } else {
      $dataLayer = array();
      $dataLayer["event"] = "view_item";
      if ($this->fb_pixel_id != "") {
        $dataLayer["fb_event_id"] = $fb_event_id;
      }
      if ($this->tiKtok_ads_pixel_id != "") {
        $dataLayer["tiktok_event_id"] = $tiktok_event_id;
      }
      $dataLayer["ecommerce"]["items"][] =
        array(
          "item_id" => isset($con_view_item["id"]) ? esc_js($con_view_item["id"]) : "",
          "item_name" => isset($con_view_item["name"]) ? esc_js($con_view_item["name"]) : "",
          "affiliation" => $affiliation,
          "currency" => $this->ga_LC,
          "item_category" => isset($con_view_item["category"]) ? esc_js($con_view_item["category"]) : "",
          "price" => isset($con_view_item["price"]) ? (float) esc_js($con_view_item["price"]) : "",
          "quantity" => 1
        );

      if (!$this->disable_tracking($this->ga_eeT, "view_item")) {
        $this->add_gtm_datalayer_js($dataLayer);
      }


      $fb_contents = array(
        "id" => isset($con_view_item["id"]) ? esc_js($con_view_item["id"]) : "",
        "quantity" => isset($con_view_item["quantity"]) ? esc_js($con_view_item["quantity"]) : "1",
        "item_price" => isset($con_view_item["price"]) ? esc_js($con_view_item["price"]) : "",
      );


      /*** Add to Cart product detail page ***/
      if (!$this->disable_tracking($this->ga_eeT, "add_to_cart_single")) {
        global $product, $woocommerce;
        $variations_data = array();
        if ($product && $product->is_type('variable')) {
          $variations_data['default_attributes'] = $product->get_default_attributes();
          $variations_data['available_variations'] = $product->get_available_variations(); //get all child variations
          $variations_data['available_attributes'] = $product->get_variation_attributes();
        }
        $product_detail_addtocart_selector = (isset($this->c_t_o['tvc_product_detail_addtocart_selector']) && $this->c_t_o['tvc_product_detail_addtocart_selector'] == "custom") ? $this->c_t_o : array();
        ?>
        <script data-cfasync="false" data-no-optimize="1" data-pagespeed-no-defer>
          window.addEventListener('load', call_tvc_enhanced, true);

          function call_tvc_enhanced() {
            tvc_js = new TVC_GTM_Enhanced(<?php echo wp_json_encode($this->tvc_options); ?>);
            tvc_js.singleProductaddToCartEventBindings(<?php echo wp_json_encode($variations_data); ?>,
              "<?php echo esc_js($this->get_selector_val_fron_array($product_detail_addtocart_selector, 'tvc_product_detail_addtocart_selector')); ?>"
            );
          }
        </script>
      <?php
      }
    }

    /**
     * view_item_list
     **/
    if (empty($con_view_item_list)) {
      $con_view_item_list = array(); //define empty array so if empty
    } else {
      $dataLayer = array();
      $dataLayer["event"] = "view_item_list";
      $items = array();
      if (!empty($con_view_item_list)) {
        foreach ($con_view_item_list as $key => $view_item) {
          $items[] = array(
            "item_id" => isset($view_item["id"]) ? esc_js($view_item["id"]) : "",
            "item_name" => isset($view_item["name"]) ? esc_js($view_item["name"]) : "",
            "affiliation" => $affiliation,
            "currency" => $this->ga_LC,
            "index" => ($key + 1),
            "item_category" => isset($view_item["category"]) ? esc_js($view_item["category"]) : "",
            "price" => isset($view_item["price"]) ? (float) esc_js($view_item["price"]) : "",
            "quantity" => 1
          );
          if (count($items) >= $impression_threshold || $key >= (count($con_view_item_list) - 1)) {
            $dataLayer["ecommerce"]["items"] = $items;
            if (!$this->disable_tracking($this->ga_eeT, "view_item_list")) {
              $this->add_gtm_datalayer_js($dataLayer);
            }
            $items = array();
          }
        }

        /*** Add to Cart, product List  page ***/
      ?>
        <script data-cfasync="false" data-no-optimize="1" data-pagespeed-no-defer>
          window.conProductList = window.productList || [];
          conProductList.push(<?php echo wp_json_encode($con_view_item_list); ?>);
          window.addEventListener('load', call_tvc_enhanced_1ist_product, true);

          function call_tvc_enhanced_1ist_product() {
            tvc_js = new TVC_GTM_Enhanced(<?php echo wp_json_encode($this->tvc_options); ?>);

            <?php if (!$this->disable_tracking($this->ga_eeT, "add_to_cart_list")) { ?>
              tvc_js.ListProductaddToCartEventBindings();
            <?php } ?>

            <?php if (!$this->disable_tracking($this->ga_eeT, "select_item")) { ?>
              tvc_js.ListProductSelectItemEventBindings();
            <?php } ?>

          }
        </script>
<?php
      }
    }
  }
  public function con_product_list_item_extra_tag($product, $listtype)
  {
    global $wp_query, $woocommerce_loop;
    global $con_view_item_list;

    if (!isset($product)) {
      return;
    }
    if (!($product instanceof WC_Product)) {
      return false;
    }
    $product_id = $product->get_id();
    /*$product_cat = '';
    if ( is_product_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();    
      $product_cat = $cat_obj->name;    
    } else {
      $product_cat = $this->con_get_product_category( $product_id );
    }*/
    $list_name = "";
    if (is_search()) {
      $list_name = __('Search Results', 'enhanced-e-commerce-for-woocommerce-store');
    } elseif ('' !== $listtype) {
      $list_name = $listtype;
    } else {
      $list_name = __('General Product List', 'enhanced-e-commerce-for-woocommerce-store');
    }
    $itemix = '';
    if (isset($woocommerce_loop['loop']) && ('' !== $woocommerce_loop['loop'])) {
      $itemix = $woocommerce_loop['loop'];
    }
    $paged          = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $posts_per_page = get_query_var('posts_per_page');
    if ($posts_per_page < 1) {
      $posts_per_page = 1;
    }
    $item = $this->con_item_product(
      $product,
      array(
        'productlink'  => get_permalink(),
        'listname'     => $list_name,
        'listposition' => (int) $itemix + ($posts_per_page * ($paged - 1)),
      )
    );
    $con_view_item_list[] = $item;
  }

  public function con_item_product($product, $additional_product_attributes)
  {
    if (!$product) {
      return false;
    }

    if (!($product instanceof WC_Product)) {
      return false;
    }

    $product_id     = (string)$product->get_id();
    $product_type   = $product->get_type();
    $remarketing_id = $product_id;
    $product_sku    = $product->get_sku();

    if ('variation' === $product_type) {
      $parent_product_id = $product->get_parent_id();
      $product_cat       = $this->con_get_product_category($parent_product_id);
    } else {
      $product_cat = $this->con_get_product_category($product_id);
    }

    $_temp_productdata = array(
      'id'         => $remarketing_id,
      'name'       => $product->get_title(),
      'sku'        => $product_sku ? $product_sku : $product_id,
      'category'   => $product_cat,
      'price'      => round((float) wc_get_price_to_display($product), 2),
      'stocklevel' => $product->get_stock_quantity(),
    );

    if ('variation' === $product_type) {
      $_temp_productdata['variant'] = implode(',', $product->get_variation_attributes());
    }
    return array_merge($_temp_productdata, $additional_product_attributes);
  }

  public function con_get_product_category_hierarchy($category_id)
  {
    $cat_hierarchy = '';

    $category_parent_list = get_term_parents_list(
      $category_id,
      'product_cat',
      array(
        'format'    => 'name',
        'separator' => '/',
        'link'      => false,
        'inclusive' => true,
      )
    );

    if (is_string($category_parent_list)) {
      $cat_hierarchy = trim($category_parent_list, '/');
    }

    return $cat_hierarchy;
  }

  public function con_get_product_category($product_id, $fullpath = false)
  {
    $product_cat = '';

    $_product_cats = wp_get_post_terms(
      $product_id,
      'product_cat',
      array(
        'orderby' => 'parent',
        'order'   => 'ASC',
      )
    );

    if ((is_array($_product_cats)) && (count($_product_cats) > 0)) {
      $first_product_cat = array_pop($_product_cats);
      if ($fullpath) {
        $product_cat = $this->con_get_product_category_hierarchy($first_product_cat->term_id);
      } else {
        $product_cat = $first_product_cat->name;
      }
    }

    return $product_cat;
  }
}
