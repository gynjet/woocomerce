<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

class TVC_Admin_Helper
{
  protected $customApiObj;
  protected $ee_options_data = "";
  protected $e_options_settings = "";
  protected $merchantId = "";
  protected $main_merchantId = "";
  protected $subscriptionId = "";
  protected $time_zone = "";
  protected $connect_actual_link = "";
  protected $connect_url = "";
  protected $woo_country = "";
  protected $woo_currency = "";
  protected $currentCustomerId = "";
  protected $user_currency_symbol = "";
  protected $setting_status = "";
  protected $ee_additional_data = "";
  protected $TVC_Admin_DB_Helper;
  protected $store_data;
  protected $api_subscription_data;
  protected $onboarding_page_url;
  protected $plan_id;
  protected $tiktok_business_id;
  public function __construct()
  {
    $this->includes();
    $this->customApiObj = new CustomApi();
    $this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
    add_action('init', array($this, 'init'));
    add_action('init', array($this, 'tvc_upgrade_function'), 9999);
  }

  public function includes()
  {
    if (!class_exists('CustomApi.php')) {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
    }
    if (!class_exists('ShoppingApi')) {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/ShoppingApi.php');
    }
  }

  public function init()
  {
    add_filter('sanitize_option_ee_auto_update_id', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ee_remarketing_snippets', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ee_conversio_send_to', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ee_api_data', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ee_additional_data', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ee_options', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ee_msg_nofifications', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_google_ads_conversion_tracking', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ads_tracking_id', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ads_ert', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ads_edrt', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ee_customer_gmail', array($this, 'sanitize_option_ee_email'), 10, 2);
    add_filter('sanitize_option_ee_prod_mapped_cats', array($this, 'sanitize_option_ee_general'), 10, 2);
    add_filter('sanitize_option_ee_prod_mapped_attrs', array($this, 'sanitize_option_ee_general'), 10, 2);

    add_filter('sanitize_post_meta__tracked', array($this, 'sanitize_meta_ee_number'));
    add_filter('sanitize_option_tvc_tracked_refund', array($this, 'sanitize_option_ee_general'), 10, 2);
  }

  public function sanitize_meta_ee_number($value)
  {
    $value = (int) $value;
    if ($value < -1) {
      $value = abs($value);
    }
    return $value;
  }

  public function sanitize_option_ee_email($value, $option)
  {
    global $wpdb;
    $value = $wpdb->strip_invalid_text_for_column($wpdb->options, 'option_value', $value);
    if (is_wp_error($value)) {
      $error = $value->get_error_message();
    } else {
      $value = sanitize_email($value);
      if (!is_email($value)) {
        $error = esc_html__("The email address entered did not appear to be a valid email address. Please enter a valid email address.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    if (!empty($error)) {
      $value = get_option($option);
      if (function_exists('add_settings_error')) {
        add_settings_error($option, "invalid_{$option}", $error);
      }
    }
    return $value;
  }

  public function sanitize_option_ee_general($value, $option)
  {
    global $wpdb;
    $value = $wpdb->strip_invalid_text_for_column($wpdb->options, 'option_value', $value);
    if (is_wp_error($value)) {
      $error = $value->get_error_message();
    }
    if (!empty($error)) {
      $value = get_option($option);
      if (function_exists('add_settings_error')) {
        add_settings_error($option, "invalid_{$option}", $error);
      }
    }
    return $value;
  }
  public function tvc_upgrade_function()
  {
    $ee_additional_data = $this->get_ee_additional_data();
    $ee_p_version = isset($ee_additional_data['ee_p_version']) ? $ee_additional_data['ee_p_version'] : "";
    if ($ee_p_version == "") {
      $ee_p_version = "1.0.0";
    }
    if (version_compare($ee_p_version, PLUGIN_TVC_VERSION, ">=")) {
      return;
    } else {
      $this->update_app_status();
    }
    if (!isset($ee_additional_data['ee_p_version']) || empty($ee_additional_data)) {
      $ee_additional_data = array();
    }

    $ee_additional_data['ee_p_version'] = PLUGIN_TVC_VERSION;
    $this->set_ee_additional_data($ee_additional_data);
  }
  /*
   * verstion auto updated
   */
  public function need_auto_update_db()
  {
    global $wpdb;
    try {
      $table = $wpdb->prefix . "ee_prouct_pre_sync_data";
      $query = $wpdb->prepare('SHOW TABLES LIKE %s', '%' . $wpdb->esc_like($table) . '%');
      if ($wpdb->get_var($query) === $table) {
        //$table = esc_sql($table);
        $query1 = $wpdb->prepare("SHOW COLUMNS FROM {$wpdb->prefix}ee_prouct_pre_sync_data LIKE  %s", "%" . $wpdb->esc_like("create_date") . "%");
        if ($wpdb->get_var($query1) != esc_sql('create_date')) {
          $wpdb->query("ALTER TABLE {$wpdb->prefix}ee_prouct_pre_sync_data ADD `create_date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP AFTER `product_sync_profile_id`");
          $wpdb->query("ALTER TABLE {$wpdb->prefix}ee_prouct_pre_sync_data CHANGE `update_date` `update_date` DATETIME NULL");
        }
      }
    } catch (Exception $e) {
    }
    $new_ee_auto_update_id = esc_attr(sanitize_text_field("tvc_" . PLUGIN_TVC_VERSION));
    update_option("ee_auto_update_id",  $new_ee_auto_update_id);
  }
  /*
   * Check auto update time
   */
  public function is_need_to_update_api_to_db()
  {
    if ($this->get_subscriptionId() != "") {
      $google_detail = $this->get_ee_options_data();
      if (isset($google_detail['sync_time']) && $google_detail['sync_time']) {
        $current = sanitize_text_field(current_time('timestamp'));
        $diffrent_hours = floor(($current - $google_detail['sync_time']) / (60 * 60));
        if ($diffrent_hours > 11) {
          return true;
        }
      } else if (empty($google_detail)) {
        return true;
      }
    }
    return false;
  }
  /*
   * if user has subscription id  and if DB data is empty then call update data
   */
  public function is_ee_options_data_empty()
  {
    if ($this->get_subscriptionId() != "") {
      if (empty($this->get_ee_options_data())) {
        $this->set_update_api_to_db();
      }
    }
  }

  /*
   * Update user only subscription details in DB
   */
  public function update_subscription_details_api_to_db($googleDetail = null)
  {
    $google_detail = $this->customApiObj->getGoogleAnalyticDetail();
    if (property_exists($google_detail, "error") && $google_detail->error == false) {
      if (property_exists($google_detail, "data") && $google_detail->data != "") {
        $googleDetail = $google_detail->data;
      }
    }
    if (!empty($googleDetail)) {
      $get_ee_options_data = $this->get_ee_options_data();
      $get_ee_options_data["setting"] = $googleDetail;
      $this->set_ee_options_data($get_ee_options_data);
    }
  }
  /*
   * Update Google shopping product details in DB
   */
  public function update_gmc_product_to_db($next_page_token = "")
  {
    $merchantId = $this->get_merchantId();
    $syncProductStat = array("total" => 0, "approved" => 0, "disapproved" => 0, "pending" => 0);
    if ($merchantId != "") {
      $api_rs = $this->import_gmc_products_sync_in_db($next_page_token);
      $product_status = $this->TVC_Admin_DB_Helper->tvc_get_counts_groupby('ee_products_sync_list', 'google_status');
      if (!empty($product_status)) {
        foreach ($product_status as $key => $value) {
          if (isset($value['google_status'])) {
            $syncProductStat[$value['google_status']] = (isset($value['count']) && $value['count'] > 0) ? $value['count'] : 0;
          }
        }
        $syncProductStat["total"] = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_products_sync_list');
        $google_detail = $this->get_ee_options_data();
        $google_detail["prod_sync_status"] = (object) $syncProductStat;
        $this->set_ee_options_data($google_detail);
      }

      //}
      return array("error" => false, "api_rs" => $api_rs, "message" => esc_html__("Details updated successfully.", "enhanced-e-commerce-for-woocommerce-store"));
    }
  }
  /*
   * Update user subscription and shopping details in DB
   */
  public function set_update_api_to_db($googleDetail = null)
  {
    $google_detail = $this->customApiObj->getGoogleAnalyticDetail();
    if (property_exists($google_detail, "error") && $google_detail->error == false) {
      if (property_exists($google_detail, "data") && $google_detail->data != "") {
        $googleDetail = $google_detail->data;
      }
    } else {
      return array("error" => true, "message" => esc_html__("Please try after some time.", "enhanced-e-commerce-for-woocommerce-store"));
    }

    $campaigns_list = "";
    if (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != "") {
      $shopping_api = new ShoppingApi();
      $campaigns_list_res = $shopping_api->getCampaigns();
      if (isset($campaigns_list_res->data) && isset($campaigns_list_res->status) && $campaigns_list_res->status == 200) {
        if (isset($campaigns_list_res->data['data'])) {
          $campaigns_list = $campaigns_list_res->data['data'];
        }
      }
    }
    $syncProductStat = array("total" => 0, "approved" => 0, "disapproved" => 0, "pending" => 0);
    $google_detail_t = $this->get_ee_options_data();
    $prod_sync_status = isset($google_detail_t["prod_sync_status"]) ? $google_detail_t["prod_sync_status"] : $syncProductStat;
    $this->set_ee_options_data(array("setting" => $googleDetail, "prod_sync_status" => (object) $prod_sync_status, "campaigns_list" => $campaigns_list, "sync_time" => current_time('timestamp')));
    return array("error" => false, "message" => esc_html__("Details updated successfully.", "enhanced-e-commerce-for-woocommerce-store"));
  }
  /*
   * update remarketing snippets
   */
  public function update_remarketing_snippets()
  {

    $customer_id = $this->get_currentCustomerId();
    if ($customer_id != "") {
      $rs = $this->customApiObj->get_remarketing_snippets($customer_id);
      $remarketing_snippets = array();
      if (property_exists($rs, "error") && $rs->error == false) {
        if (property_exists($rs, "data") && $rs->data != "" && property_exists($rs->data, "snippets")) {
          $remarketing_snippets["snippets"] = base64_encode($rs->data->snippets);
          $remarketing_snippets["id"] = $rs->data->id;
        }
      }
      update_option("ee_remarketing_snippets", serialize($remarketing_snippets));
    }
  }
  public function get_conversion_label($con_string)
  {
    $con_string = trim(preg_replace('/\s\s+/', '', $con_string));
    $con_string = str_replace(" ", "", $con_string);
    $con_string = str_replace("'", "", $con_string);
    $con_string = str_replace("return false;", "", $con_string);
    $con_string = str_replace("event,conversion,{", ",event:conversion,", $con_string);
    $con_array = explode(",", $con_string);
    $con_val_array = array();
    if (!empty($con_array) && in_array("event:conversion", $con_array)) {
      foreach ($con_array as $key => $con_value) {
        $con_val_array = explode(":", $con_value);
        if (in_array("send_to", $con_val_array)) {
          return $con_val_array[1];
        }
      }
    }
  }
  /*
   * update conversion send_to dapricated version 4.8.2
   */
  // public function update_conversion_send_to() {}
  /*
   * import GMC products in DB
   */
  public function import_gmc_products_sync_in_db($next_page_token = null)
  {
    $merchant_id = $this->get_merchantId();
    if ($next_page_token == "") {
      $last_row = $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_products_sync_list', array("gmc_id"));
      /**
       * truncate table before import the GMC products
       */
      if (!empty($last_row) && isset($last_row['gmc_id']) && $last_row['gmc_id'] != $merchant_id) {
        global $wpdb;
        $tablename = $wpdb->prefix . "ee_products_sync_list";
        $this->TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
        $tablename = $wpdb->prefix . "ee_product_sync_data";
        $this->TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
        $tablename = $wpdb->prefix . "ee_product_sync_call";
        $this->TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
      }
    }

    if ($next_page_token == "") {
      global $wpdb;
      $tablename = $wpdb->prefix . "ee_products_sync_list";
      $this->TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
    }
    if ($merchant_id != "") {
      $args = array('merchant_id' => $merchant_id);
      if ($next_page_token != "") {
        $args["pageToken"] = sanitize_text_field($next_page_token);
      }
      $syncProduct_list_res = $this->customApiObj->getSyncProductList($args);
      if (isset($syncProduct_list_res->data) && isset($syncProduct_list_res->status) && $syncProduct_list_res->status == 200) {
        if (isset($syncProduct_list_res->data->products)) {
          $rs_next_page_token = $syncProduct_list_res->data->nextPageToken;
          $sync_product_list = $syncProduct_list_res->data->products;
          if (!empty($sync_product_list)) {
            foreach ($sync_product_list as $key => $value) {
              $googleStatus = $value->googleStatus;
              if ($value->googleStatus != "disapproved" && $value->googleStatus != "approved") {
                $googleStatus = "pending";
              }
              $t_data = array(
                'gmc_id' => esc_sql($merchant_id),
                'name' => esc_sql($value->name),
                'product_id' => esc_sql($value->productId),
                'google_status' => esc_sql($googleStatus),
                'image_link' => esc_sql($value->imageLink),
                'issues' => wp_json_encode($value->issues)
              );
              $key = "product_id";
              $value = esc_sql($value->productId);
              $row_count = $this->TVC_Admin_DB_Helper->tvc_check_row('ee_products_sync_list', $key, $value);
              if ($row_count == 0) {
                $this->TVC_Admin_DB_Helper->tvc_add_row('ee_products_sync_list', $t_data, array("%s", "%s", "%s", "%s", "%s", "%s"));
              }
            }
          }
          return array("sync_product" => count($sync_product_list), "next_page_token" => $rs_next_page_token);
        }
      }
    }
  }
  /*
   * get API data from DB
   */
  public function get_ee_options_data()
  {
    if (!empty($this->ee_options_data)) {
      return $this->ee_options_data;
    } else {
      $this->ee_options_data = unserialize(get_option('ee_api_data'));
      return $this->ee_options_data;
    }
  }


  /*
   * set API data in DB
   */
  public function set_ee_options_data($ee_options_data)
  {
    update_option("ee_api_data", serialize($ee_options_data));
  }
  /*
   * set additional data in DB
   */
  public function set_ee_additional_data($ee_additional_data)
  {
    update_option("ee_additional_data", serialize($ee_additional_data));
  }
  /*
   * get additional data from DB
   */
  public function get_ee_additional_data()
  {
    $this->ee_additional_data = unserialize(get_option('ee_additional_data'));
    return $this->ee_additional_data;
  }

  public function save_ee_options_settings($settings)
  {
    update_option("ee_options", serialize($settings));
  }
  /*
   * get plugin setting data from DB
   */
  public function get_ee_options_settings()
  {
    if (!empty($this->e_options_settings)) {
      return $this->e_options_settings;
    } else {
      $this->e_options_settings = unserialize(get_option('ee_options'));
      return $this->e_options_settings;
    }
  }

  /*
   * set selected pixel events
   */
  public function set_conv_selected_events($selected_events)
  {
    update_option("conv_selected_events", serialize($selected_events));
  }

  /*
   * get subscriptionId
   */
  public function get_subscriptionId()
  {
    if (!empty($this->subscriptionId)) {
      return $this->subscriptionId;
    } else {
      $ee_options_settings = $this->get_ee_options_settings();
      return $this->subscriptionId = (isset($ee_options_settings['subscription_id'])) ? $ee_options_settings['subscription_id'] : "";
    }
  }
  /*
   * get merchantId
   */
  public function get_merchantId()
  {
    if (!empty($this->merchantId)) {
      return $this->merchantId;
    } else {
      $tvc_merchant = "";
      $google_detail = $this->get_ee_options_data();
      return $this->merchantId = (isset($google_detail['setting']->google_merchant_center_id)) ? $google_detail['setting']->google_merchant_center_id : "";
    }
  }
  /*
   * get main_merchantId
   */
  public function get_main_merchantId()
  {
    if (!empty($this->main_merchantId)) {
      return $this->main_merchantId;
    } else {
      $main_merchantId = "";
      $google_detail = $this->get_ee_options_data();
      return $this->main_merchantId = (isset($google_detail['setting']->merchant_id)) ? $google_detail['setting']->merchant_id : "";
    }
  }
  /*
   * get admin time zone
   */
  public function get_time_zone()
  {
    if (!empty($this->time_zone)) {
      return $this->time_zone;
    } else {
      $timezone = get_option('timezone_string');
      if ($timezone == "") {
        $timezone = "America/New_York";
      }
      $this->time_zone = $timezone;
      return $this->time_zone;
    }
  }

  public function get_connect_actual_link()
  {
    if (!empty($this->connect_actual_link)) {
      return $this->connect_actual_link;
    } else {
      $this->connect_actual_link = get_site_url();
      return $this->connect_actual_link;
    }
  }

  /**
   * Wordpress store information
   */
  public function get_store_data()
  {
    if (!empty($this->store_data)) {
      return $this->store_data;
    } else {
      return $this->store_data = array(
        "subscription_id" => $this->get_subscriptionId(),
        "user_domain" => $this->get_connect_actual_link(),
        "currency_code" => $this->get_woo_currency(),
        "timezone_string" => $this->get_time_zone(),
        "user_country" => $this->get_woo_country(),
        "app_id" => CONV_APP_ID,
        "time" => gmdate("d-M-Y h:i:s A")
      );
    }
  }
  public function get_connect_url()
  {
    if (!empty($this->connect_url)) {
      return $this->connect_url;
    } else {
      $this->connect_url = "https://" . TVC_AUTH_CONNECT_URL . "/config3/ga_rdr_gmc.php?return_url=" . TVC_AUTH_CONNECT_URL . "/config3/ads-analytics-form.php?domain=" . $this->get_connect_actual_link() . "&amp;country=" . $this->get_woo_country() . "&amp;user_currency=" . $this->get_woo_currency() . "&amp;subscription_id=" . $this->get_subscriptionId() . "&amp;confirm_url=" . admin_url() . "&amp;timezone=" . $this->get_time_zone();
      return $this->connect_url;
    }
  }
  public function get_custom_connect_url($confirm_url = "")
  {
    if ($confirm_url == "") {
      $confirm_url = admin_url();
    }
    $this->connect_url = "https://" . TVC_AUTH_CONNECT_URL . "/config3/ga_rdr_gmc.php?return_url=" . TVC_AUTH_CONNECT_URL . "/config3/ads-analytics-form.php?domain=" . $this->get_connect_actual_link() . "&amp;country=" . $this->get_woo_country() . "&amp;user_currency=" . $this->get_woo_currency() . "&amp;subscription_id=" . $this->get_subscriptionId() . "&amp;confirm_url=" . $confirm_url . "&amp;timezone=" . $this->get_time_zone();
    return $this->connect_url;
  }

  public function get_custom_connect_url_wizard($confirm_url = "")
  {
    if ($confirm_url == "") {
      $confirm_url = admin_url();
    }
    $this->connect_url = "https://" . TVC_AUTH_CONNECT_URL . "/config3/ga_rdr_gmc.php?return_url=" . TVC_AUTH_CONNECT_URL . "/config3/ads-analytics-form.php?domain=" . $this->get_connect_actual_link() . "&amp;country=" . $this->get_woo_country() . "&amp;user_currency=" . $this->get_woo_currency() . "&amp;subscription_id=" . $this->get_subscriptionId() . "&amp;confirm_url=" . $confirm_url . "&amp;timezone=" . $this->get_time_zone();
    return $this->connect_url;
  }

  public function get_custom_connect_url_subpage($confirm_url = "", $subpage = "")
  {
    if (!empty($this->connect_url)) {
      return $this->connect_url;
    } else {
      if ($confirm_url == "") {
        $confirm_url = admin_url();
      }

      $this->connect_url = "https://" . TVC_AUTH_CONNECT_URL . "/config3/ga_rdr_gmc.php?return_url=" . TVC_AUTH_CONNECT_URL . "/config3/ads-analytics-form.php?domain=" . $this->get_connect_actual_link() . "&amp;country=" . $this->get_woo_country() . "&amp;user_currency=" . $this->get_woo_currency() . "&amp;subscription_id=" . $this->get_subscriptionId() . "&amp;confirm_url=" . $confirm_url . "&amp;subpage=" . $subpage . "&amp;timezone=" . $this->get_time_zone();
      return $this->connect_url;
    }
  }

  public function get_onboarding_page_url()
  {
    if (!empty($this->onboarding_page_url)) {
      return $this->onboarding_page_url;
    } else {
      $this->onboarding_page_url = admin_url("admin.php?page=conversios-google-analytics");
      return $this->onboarding_page_url;
    }
  }

  public function get_woo_currency()
  {
    if (!empty($this->woo_currency)) {
      return $this->woo_currency;
    } else {
      $this->woo_currency = get_option('woocommerce_currency');
      return $this->woo_currency;
    }
  }

  public function get_woo_country()
  {
    if (!empty($this->woo_country)) {
      return $this->woo_country;
    } else {
      $store_raw_country = get_option('woocommerce_default_country');
      $country = explode(":", $store_raw_country);
      $this->woo_country = (isset($country[0])) ? $country[0] : "";
      return $this->woo_country;
    }
  }

  public function get_api_customer_id()
  {
    $google_detail = $this->get_ee_options_data();
    if (isset($google_detail['setting'])) {
      $googleDetail = (array) $google_detail['setting'];
      return ((isset($googleDetail['customer_id'])) ? $googleDetail['customer_id'] : "");
    }
  }

  public function get_currentCustomerId()
  {
    if (!empty($this->currentCustomerId)) {
      return $this->currentCustomerId;
    } else {
      $ee_options_settings = $this->get_ee_options_settings();
      return $this->currentCustomerId = (isset($ee_options_settings['google_ads_id'])) ? $ee_options_settings['google_ads_id'] : "";
    }
  }
  public function get_user_currency_symbol()
  {
    if (!empty($this->user_currency_symbol)) {
      return $this->user_currency_symbol;
    } else {
      $currency_symbol = "";
      $currency_symbol_rs = $this->customApiObj->getCampaignCurrencySymbol(['customer_id' => $this->get_currentCustomerId()]);
      if (isset($currency_symbol_rs->data) && isset($currency_symbol_rs->data['status']) && $currency_symbol_rs->data['status'] == 200) {
        $currency_symbol = get_woocommerce_currency_symbol($currency_symbol_rs->data['data']->currency);
      } else {
        $currency_symbol = get_woocommerce_currency_symbol("USD");
      }
      $this->user_currency_symbol = $currency_symbol;
      return $this->user_currency_symbol;
    }
  }

  public function add_spinner_html()
  {
    $spinner_gif = ENHANCAD_PLUGIN_URL . '/admin/images/ajax-loader.gif';
    echo '<div class="feed-spinner" id="feed-spinner" style="display:none;">
				<img id="img-spinner" src="' . esc_url($spinner_gif) . '" alt="Loading" />
			</div>';
  }

  public function get_gmcAttributes()
  {
    $path = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/gmc_attrbutes.json';
    global $wp_filesystem;
    $str = $wp_filesystem->get_contents($path);
    $attributes = $str ? json_decode($str, true) : [];
    return $attributes;
  }
  public function get_gmc_countries_list()
  {
    $path = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/countries.json';
    global $wp_filesystem;
    $str = $wp_filesystem->get_contents($path);
    $attributes = $str ? json_decode($str, true) : [];
    return $attributes;
  }
  public function get_gmc_language_list()
  {
    $path = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/iso_lang.json';
    global $wp_filesystem;
    $str = $wp_filesystem->get_contents($path);

    $attributes = $str ? json_decode($str, true) : [];
    return $attributes;
  }
  /* start display form input*/
  public function tvc_language_select($name, $class_id = "", string $label = "Please Select", string $sel_val = "en", bool $require = false)
  {
    if ($sel_val == "en") {
      $sel_val = get_locale();
      if (strlen($sel_val) > 0) {
        $sel_val = explode('_', $sel_val)[0];
      }
    }
    if ($name) {
      $countries_list = $this->get_gmc_language_list();
?>
      <select style="width: 100%"
        class="fw-light text-secondary fs-6 form-control form-select-sm select2 <?php echo esc_attr($class_id); ?> <?php echo ($require == true) ? "field-required" : ""; ?>"
        name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($class_id); ?>">
        <option value=""><?php echo esc_html($label); ?></option>
        <?php foreach ($countries_list as $Key => $val) { ?>
          <option value="<?php echo esc_attr($val["code"]); ?>" <?php echo ($val["code"] == $sel_val) ? "selected" : ""; ?>>
            <?php echo esc_html($val["name"]) . " (" . esc_html($val["native_name"]) . ")"; ?></option>
        <?php
        } ?>
      </select>
    <?php
    }
  }
  public function tvc_countries_select($name, $class_id = "", string $label = "Please Select", bool $require = false)
  {
    if ($name) {
      $countries_list = $this->get_gmc_countries_list();
      $sel_val = $this->get_woo_country();
    ?>
      <select style="width: 100%"
        class="fw-light text-secondary fs-6 form-control form-select-sm select2 <?php echo esc_attr($class_id); ?> <?php echo ($require == true) ? "field-required" : ""; ?>"
        name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($class_id); ?>">
        <option value=""><?php echo esc_html($label); ?></option>
        <?php foreach ($countries_list as $Key => $val) { ?>
          <option value="<?php echo esc_attr($val["code"]); ?>" <?php echo ($val["code"] == $sel_val) ? "selected" : ""; ?>>
            <?php echo esc_html($val["name"]); ?></option>
        <?php
        } ?>
      </select>
    <?php
    }
  }
  public function tvc_select($name, $class_id = "", string $label = "Please Select", string $sel_val = null, bool $require = false, $option_list = array())
  {
    if (!empty($option_list) && $name) {
    ?>
      <select style="width: 100%"
        class="fw-light text-secondary fs-6 form-control form-select-sm select2 <?php echo esc_attr($class_id); ?> <?php echo ($require == true) ? "field-required" : ""; ?>"
        name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($class_id); ?>">
        <option value=""><?php echo esc_html($label); ?></option>
        <?php foreach ($option_list as $Key => $val) { ?>
          <option value="<?php echo esc_attr($val["field"]); ?>" <?php echo ($val["field"] == $sel_val) ? "selected" : ""; ?>>
            <?php echo esc_html($val["field"]); ?></option>
        <?php
        } ?>
      </select>
    <?php
    }
  }

  public function add_additional_option_in_tvc_select($tvc_select_option, $field)
  {
    if ($field == "brand") {
      $is_plugin = 'yith-woocommerce-brands-add-on/init.php';
      $is_plugin_premium = 'yith-woocommerce-brands-add-on-premium/init.php';
      $woocommerce_brand_is_active = 'woocommerce-brands/woocommerce-brands.php';
      $perfect_woocommerce_brand_is_active = 'perfect-woocommerce-brands/perfect-woocommerce-brands.php';
      $wpc_brands = 'wpc-brands/wpc-brands.php';
      if (is_plugin_active($is_plugin) || is_plugin_active($is_plugin_premium)) {
        $tvc_select_option[]["field"] = "yith_product_brand";
      } else if (in_array($woocommerce_brand_is_active, apply_filters('active_plugins', get_option('active_plugins')))) {
        $tvc_select_option[]["field"] = "woocommerce_product_brand";
      } else if (in_array($perfect_woocommerce_brand_is_active, apply_filters('active_plugins', get_option('active_plugins')))) {
        $tvc_select_option[]["field"] = "perfect_woocommerce_product_brand";
      } else if (in_array($wpc_brands, apply_filters('active_plugins', get_option('active_plugins')))) {
        $tvc_select_option[]["field"] = "wpc-brand";
      }
    }
    return $tvc_select_option;
  }

  public function add_additional_option_val_in_map_product_attribute($key, $product_id)
  {
    if ($key != "" && $product_id != "") {
      if ($key == "brand") {
        $is_plugin = 'yith-woocommerce-brands-add-on/init.php';
        $is_plugin_premium = 'yith-woocommerce-brands-add-on-premium/init.php';
        $woocommerce_brand_is_active = 'woocommerce-brands/woocommerce-brands.php';
        $perfect_woocommerce_brand_is_active = 'perfect-woocommerce-brands/perfect-woocommerce-brands.php';
        $wpc_brands = 'wpc-brands/wpc-brands.php';
        if (is_plugin_active($is_plugin) || is_plugin_active($is_plugin_premium)) {
          return $yith_product_brand = $this->get_custom_taxonomy_name($product_id, "yith_product_brand");
        } else if (in_array($woocommerce_brand_is_active, apply_filters('active_plugins', get_option('active_plugins')))) {
          return $product_brand = $this->get_custom_taxonomy_name($product_id, "product_brand");
        } else if (in_array($perfect_woocommerce_brand_is_active, apply_filters('active_plugins', get_option('active_plugins')))) {
          return $product_brand = $this->get_custom_taxonomy_name($product_id, "pwb-brand");
        } else if (in_array($wpc_brands, apply_filters('active_plugins', get_option('active_plugins')))) {
          return $product_brand = $this->get_custom_taxonomy_name($product_id, "wpc-brand");
        }
      }
    }
  }

  public function get_custom_taxonomy_name($product_id, $taxonomy = "product_cat", $separator = ", ")
  {
    $terms_ids = wp_get_post_terms($product_id, $taxonomy, array('fields' => 'ids'));
    // Loop though terms ids (product categories)    
    foreach ($terms_ids as $term_id) {
      // Loop through product category ancestors
      foreach (get_ancestors($term_id, $taxonomy) as $ancestor_id) {
        return get_term($ancestor_id, $taxonomy)->name;
        exit;
      }
      return get_term($term_id, $taxonomy)->name;
      exit;
      break;
    }
  }

  public function tvc_text($name, string $type = "text", string $class_id = "", string $label = null, $sel_val = null, bool $require = false)
  {
    ?>
    <input style="width:100%;" type="<?php echo esc_attr($type); ?>"
      <?php echo esc_attr($type) == 'number' ? 'min="0"' : '' ?> name="<?php echo esc_attr($name); ?>"
      class="tvc-text <?php echo esc_attr($class_id); ?>" id="<?php echo esc_attr($class_id); ?>"
      placeholder="<?php echo esc_attr($label); ?>" value="<?php echo esc_attr($sel_val); ?>">
    <?php
  }

  /* end from input*/

  public function is_current_tab_in($tabs)
  {
    if (isset($_GET['tab']) && is_array($tabs) && in_array(sanitize_text_field(wp_unslash($_GET['tab'])), $tabs)) {
      return true;
    } else if (isset($_GET['tab']) && sanitize_text_field(wp_unslash($_GET['tab'])) == $tabs) {
      return true;
    }
    return false;
  }

  public function get_tvc_product_cat_list()
  {
    $args = array(
      'hide_empty'   => 1,
      'taxonomy' => 'product_cat',
      'orderby'  => 'term_id'
    );
    $shop_categories_list = get_categories($args);
    $tvc_cat_id_list = [];
    foreach ($shop_categories_list as $key => $value) {
      $tvc_cat_id_list[] = $value->term_id;
    }
    return wp_json_encode($tvc_cat_id_list);
  }
  public function get_tvc_product_cat_list_with_name()
  {
    $args = array(
      'hide_empty' => 1,
      'taxonomy' => 'product_cat',
      'orderby'  => 'term_id'
    );
    $shop_categories_list = get_categories($args);
    $tvc_cat_id_list = [];
    foreach ($shop_categories_list as $key => $value) {
      $tvc_cat_id_list[$value->term_id] = $value->name;
    }
    return $tvc_cat_id_list;
  }

  public function call_tvc_site_verified_and_domain_claim()
  {
    $google_detail = $this->get_ee_options_data();
    if (!isset($_GET['welcome_msg']) && isset($google_detail['setting']) && $google_detail['setting']) {
      $googleDetail = $google_detail['setting'];
      $message = "";
      $title = "";
      if (isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id) {
        $title = "";
        $notice_text = "";
        $call_js_function_args = "";
        if (isset($googleDetail->is_site_verified) && isset($googleDetail->is_domain_claim) && $googleDetail->is_site_verified == '0' && $googleDetail->is_domain_claim == '0') {
          /*$title = esc_html__("Site verification and Domain claim for merchant center account failed.","enhanced-e-commerce-for-woocommerce-store");
	        $message = esc_html__("Without a verified and claimed website, your product will get disapproved.","enhanced-e-commerce-for-woocommerce-store");
	        $call_js_function_args = "both";*/
        } else if (isset($googleDetail->is_site_verified) && $googleDetail->is_site_verified == '0') {
          /*$title = esc_html__("Site verification for merchant center account failed.","enhanced-e-commerce-for-woocommerce-store");
	        $message = esc_html__("Without a verified website, your product will get disapproved.","enhanced-e-commerce-for-woocommerce-store");
	        $call_js_function_args = "site_verified";*/
        } else if (isset($googleDetail->is_domain_claim) && $googleDetail->is_domain_claim == '0') {
          /*$title = esc_html__("Site claimed website for merchant center account failed.","enhanced-e-commerce-for-woocommerce-store");
	        $message = esc_html__("Without a claimed website, your product will get disapproved.","enhanced-e-commerce-for-woocommerce-store");
	        $call_js_function_args = "domain_claim";*/
        }
        if ($message != "" && $title != "") {
    ?>
          <div class="errormsgtopbx claimalert">
            <div class="errmscntbx">
              <div class="errmsglft">
                <span class="errmsgicon"><img
                    src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/error-white-icon.png'); ?>"
                    alt="error" /></span>
              </div>
              <div class="erralertrigt">
                <h6><?php echo esc_html($title); ?></h6>
                <!--<p><?php echo esc_html($message); ?> <a href="javascript:void(0)" id="call_both_verification" onclick="call_tvc_site_verified_and_domain_claim('<?php echo esc_attr($call_js_function_args); ?>');"><?php esc_html_e("Click here", "enhanced-e-commerce-for-woocommerce-store"); ?></a> <?php esc_html_e("to verify and claim the domain.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>-->
              </div>
            </div>
          </div>
          <script>
            function call_tvc_site_verified_and_domain_claim(call_args) {
              var tvs_this = event.target;
              jQuery("#call_both_verification").css("visibility", "hidden");
              jQuery(tvs_this).after(
                '<div class="call_both_verification-spinner tvc-nb-spinner" id="both_verification-spinner"></div>');
              if (call_args == "domain_claim") {
                call_domain_claim_both();
              } else {
                jQuery.post(tvc_ajax_url, {
                  action: "tvc_call_site_verified",
                  SiteVerifiedNonce: "<?php echo esc_attr(wp_create_nonce('tvc_call_site_verified-nonce')); ?>"
                }, function(response) {
                  var rsp = JSON.parse(response);
                  if (rsp.status == "success") {
                    if (call_args == "site_verified") {
                      tvc_helper.tvc_alert("success", "", rsp.message);
                      location.reload();
                    } else {
                      call_domain_claim_both(rsp.message);
                    }
                  } else {
                    tvc_helper.tvc_alert("error", "", rsp.message);
                    jQuery("#both_verification-spinner").remove();
                  }
                });
              }
            }

            function call_domain_claim_both(first_message = null) {
              jQuery.post(tvc_ajax_url, {
                action: "tvc_call_domain_claim",
                apiDomainClaimNonce: "<?php echo esc_attr(wp_create_nonce('tvc_call_domain_claim-nonce')); ?>"
              }, function(response) {
                var rsp = JSON.parse(response);
                if (rsp.status == "success") {
                  if (first_message != "" || first_message == null) {
                    tvc_helper.tvc_alert("success", "", first_message, true, 4000);
                    setTimeout(function() {
                      tvc_helper.tvc_alert("success", "", rsp.message, true, 4000);
                      location.reload();
                    }, 4000);
                  } else {
                    tvc_helper.tvc_alert("success", "", rsp.message, true, 4000);
                    setTimeout(function() {
                      location.reload();
                    }, 4000);
                  }
                } else {
                  tvc_helper.tvc_alert("error", "", rsp.message, true, 10000)
                }
                jQuery("#both_verification-spinner").remove();
              });
            }
          </script>
        <?php
        }
      }
    }
  }
  public function call_domain_claim()
  {
    $googleDetail = [];
    $google_detail = $this->get_ee_options_data();
    if (isset($google_detail['setting']) && $google_detail['setting']) {
      $googleDetail = $google_detail['setting'];
      if ($googleDetail->is_site_verified == '0') {
        return array('error' => true, 'msg' => esc_html__("First need to verified your site. Click on site verification refresh icon to verified your site.", "enhanced-e-commerce-for-woocommerce-store"));
      } else if (property_exists($googleDetail, "is_domain_claim") && $googleDetail->is_domain_claim == '0') {
        //'website_url' => $googleDetail->site_url,
        $postData = [
          'merchant_id' => sanitize_text_field($googleDetail->merchant_id),
          'website_url' => get_site_url(),
          'subscription_id' => sanitize_text_field($googleDetail->id),
          'account_id' => sanitize_text_field($googleDetail->google_merchant_center_id)
        ];
        $claimWebsite = $this->customApiObj->claimWebsite($postData);
        if (isset($claimWebsite->error) && !empty($claimWebsite->errors)) {
          return array('error' => true, 'msg' => $claimWebsite->errors);
        } else {
          $this->update_subscription_details_api_to_db();
          return array('error' => false, 'msg' => esc_html__("Domain claimed successfully.", "enhanced-e-commerce-for-woocommerce-store"));
        }
      } else {
        return array('error' => false, 'msg' => esc_html__("Already domain claimed successfully.", "enhanced-e-commerce-for-woocommerce-store"));
      }
    }
  }


  public function call_site_verified()
  {
    $googleDetail = [];
    $google_detail = $this->get_ee_options_data();
    if (isset($google_detail['setting']) && $google_detail['setting']) {
      $googleDetail = $google_detail['setting'];
      if (property_exists($googleDetail, "is_site_verified")) {        //'website_url' => $googleDetail->site_url, 
        $postData = [
          'merchant_id' => sanitize_text_field($googleDetail->merchant_id),
          'website_url' => get_site_url(),
          'subscription_id' => sanitize_text_field($googleDetail->id),
          'account_id' => sanitize_text_field($googleDetail->google_merchant_center_id)
        ];
        $postData['method'] = "file";
        $siteVerificationToken = $this->customApiObj->siteVerificationToken($postData);
        if (isset($siteVerificationToken->error) && !empty($siteVerificationToken->errors)) {
          return array('error' => true, 'msg' => esc_attr($siteVerificationToken->errors));
        } else {
          $myFile = ABSPATH . $siteVerificationToken->data->token;
          global $wp_filesystem;
          if (!$wp_filesystem->exists($myFile)) {
            $wp_filesystem->put_contents($myFile, "google-site-verification: " . $siteVerificationToken->data->token);
            $wp_filesystem->chmod($myFile, 0777);
          }
          $postData['method'] = "file";
          $siteVerification = $this->customApiObj->siteVerification($postData);
          if (isset($siteVerification->error) && !empty($siteVerification->errors)) {
            //methd using tag
            $postData['method'] = "meta";
            $siteVerificationToken_tag = $this->customApiObj->siteVerificationToken($postData);
            if (isset($siteVerificationToken_tag->data->token) && $siteVerificationToken_tag->data->token) {
              $ee_additional_data = $this->get_ee_additional_data();
              $ee_additional_data['add_site_varification_tag'] = 1;
              $ee_additional_data['site_varification_tag_val'] = base64_encode(sanitize_text_field($siteVerificationToken_tag->data->token));

              $this->set_ee_additional_data($ee_additional_data);
              sleep(1);
              $siteVerification_tag = $this->customApiObj->siteVerification($postData);
              if (isset($siteVerification_tag->error) && !empty($siteVerification_tag->errors)) {
                return array('error' => true, 'msg' => esc_html($siteVerification_tag->errors));
              } else {
                $this->update_subscription_details_api_to_db();
                return array('error' => false, 'msg' => esc_html__("Site verification successfully.", "enhanced-e-commerce-for-woocommerce-store"));
              }
            } else {
              return array('error' => true, 'msg' => esc_html($siteVerificationToken_tag->errors));
            }
            // one more try
          } else {
            $this->update_subscription_details_api_to_db();
            return array('error' => false, 'msg' => esc_html__("Site verification successfully.", "enhanced-e-commerce-for-woocommerce-store"));
          }
        }
      } else {
        return array('error' => false, 'msg' => esc_html__("Already site verification successfully.", "enhanced-e-commerce-for-woocommerce-store"));
      }
    }
  }

  public function update_app_status($status = "1")
  {
    $this->customApiObj->update_app_status($status);
  }

  public function app_activity_detail($status = "")
  {
    $this->customApiObj->app_activity_detail($status);
  }
  public function get_tvc_popup_message()
  {
    return '<div id="tvc_popup_box">
		<span class="close" id="tvc_close_msg" onclick="tvc_helper.tvc_close_msg()"> × </span>
			<div id="box">
				<div class="tvc_msg_icon" id="tvc_msg_icon"></div>
				<h4 id="tvc_msg_title"></h4>
				<p id="tvc_msg_content"></p>
				<div id="tvc_closeModal"></div>
			</div>
		</div>';
  }

  public function get_auto_sync_time_space()
  {
    $ee_additional_data = $this->get_ee_additional_data();
    $product_sync_duration = (isset($ee_additional_data['product_sync_duration']) && $ee_additional_data['product_sync_duration']) ? $ee_additional_data['product_sync_duration'] : "";
    $pro_snyc_time_limit = (int)(isset($ee_additional_data['pro_snyc_time_limit']) && $ee_additional_data['pro_snyc_time_limit'] > 0) ? $ee_additional_data['pro_snyc_time_limit'] : "";
    if ($product_sync_duration != "" && $pro_snyc_time_limit > 0) {
      return strtotime($pro_snyc_time_limit . " " . $product_sync_duration, 0);
    } else {
      return strtotime("25 days", 0);
    }
  }

  public function get_first_auto_sync_timestamp()
  {
    $ee_additional_data = $this->get_ee_additional_data();
    $product_sync_duration = (isset($ee_additional_data['product_sync_duration']) && $ee_additional_data['product_sync_duration']) ? $ee_additional_data['product_sync_duration'] : "";
    $pro_snyc_time_limit = (int)(isset($ee_additional_data['pro_snyc_time_limit']) && $ee_additional_data['pro_snyc_time_limit'] > 0) ? $ee_additional_data['pro_snyc_time_limit'] : "";
    if ($product_sync_duration != "" && $pro_snyc_time_limit > 0) {
      return strtotime($pro_snyc_time_limit . " " . $product_sync_duration);
    } else {
      return strtotime("25 days");
    }
  }

  public function get_auto_sync_batch_size()
  {
    $ee_additional_data = $this->get_ee_additional_data();
    $product_sync_batch_size = (isset($ee_additional_data['product_sync_batch_size']) && $ee_additional_data['product_sync_batch_size']) ? $ee_additional_data['product_sync_batch_size'] : "";
    if ($product_sync_batch_size != "") {
      return $product_sync_batch_size;
    } else {
      return "50";
    }
  }

  public function get_last_auto_sync_product_info()
  {
    return $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_product_sync_call', array("total_sync_product", "create_sync", "next_sync", "status"));
  }

  public function tvc_get_post_meta($post_id)
  {
    $where = "post_id = " . $post_id;
    $rows = $this->TVC_Admin_DB_Helper->tvc_get_results_in_array('postmeta', $where, array('meta_key', 'meta_value'));
    $metas = array();
    if (!empty($rows)) {
      foreach ($rows as $val) {
        $metas[$val['meta_key']] = $val['meta_value'];
      }
    }
    return $metas;
  }

  public function getTableColumns($table)
  {
    global $wpdb;
    $table = esc_sql($table);
    return $wpdb->get_results("SELECT column_name as field FROM information_schema.columns WHERE table_name = '$table'");
  }

  public function getTableData($table = null, $columns = array())
  {
    global $wpdb;
    if ($table == "") {
      $table = $wpdb->prefix . 'postmeta';
    }
    $table = esc_sql($table);
    $columns = implode('`,`', $columns);
    return $wpdb->get_results("SELECT  DISTINCT `$columns` as field FROM `$table`");
  }
  /* message notification */
  public function set_ee_msg_nofification_list($ee_msg_list)
  {
    update_option("ee_msg_nofifications", serialize($ee_msg_list));
  }
  public function get_ee_msg_nofification_list()
  {
    return unserialize(get_option('ee_msg_nofifications'));
  }

  public function active_licence($licence_key, $subscription_id)
  {
    if ($licence_key != "") {
      $customObj = new CustomApi();
      return $customObj->active_licence_Key($licence_key, $subscription_id);
    }
  }

  public function get_pro_plan_site()
  {
    return "https://www.conversios.io/pricing/";
  }

  public function get_conversios_site_url()
  {
    return "https://conversios.io/";
  }

  public function is_show_tracking_method_options($subscription_id = 0)
  {
    if ($subscription_id > 0 && $subscription_id <= 31200) {
      return true;
    } else {
      return false;
    }
  }

  public function is_ga_property()
  {
    $data = $this->get_ee_options_settings();
    $is_connected = false;
    if ((isset($data['ga_id']) && $data['ga_id'] != '') || (isset($data['ga_id']) && $data['ga_id'] != '')) {
      return true;
    } else {
      return false;
    }
  }
  /*
   * get user plan id
   */
  public function get_plan_id()
  {
    if (!empty($this->plan_id)) {
      return $this->plan_id;
    } else {
      $plan_id = 1;
      $google_detail = $this->get_ee_options_data();
      if (isset($google_detail['setting'])) {
        $googleDetail = $google_detail['setting'];
        if (isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))) {
          $plan_id = $googleDetail->plan_id;
        }
      }
      return $this->plan_id = $plan_id;
    }
  }

  /*
   * get user plan id
   */
  public function get_user_subscription_data()
  {
    $google_detail = $this->get_ee_options_data();
    if (isset($google_detail['setting'])) {
      return $google_detail['setting'];
    }
  }



  //tvc_add_data_admin_notice function for adding the admin notices
  public function tvc_add_admin_notice($slug, $content, $status, $link_title = null, $link = null, $value = null, $title = null, $priority = "", $key = "")
  {
    $ee_additional_data = $this->get_ee_additional_data();
    if (!isset($ee_additional_data['admin_notices'][$slug])) {
      $ee_additional_data['admin_notices'][$slug] = array("link_title" => $link_title, "content" => $content, "status" => $status, "title" => $title, "value" => $value, "link" => $link, "priority" => $priority, "key" => $key);
      $this->set_ee_additional_data($ee_additional_data);
    }
  }
  //tvc_dismiss_admin_notice function for dismissing the admin notices
  public function tvc_dismiss_admin_notice($slug, $content, $status, $title = null,  $value = null)
  {
    $ee_additional_data = $this->get_ee_additional_data();
    if (isset($ee_additional_data['admin_notices'][$slug])) {
      $ee_additional_data['admin_notices'][$slug] = array("title" => $title, "content" => $content, "status" => $status, "value" => $value);
      $this->set_ee_additional_data($ee_additional_data);
    }
  }
 
  /*
   * conver curency code to currency symbols
   */
  public function get_currency_symbols($code)
  {
    $currency_symbols = array(
      'USD' => '$', // US Dollar
      'EUR' => '€', // Euro
      'CRC' => '₡', // Costa Rican Colón
      'GBP' => '£', // British Pound Sterling
      'ILS' => '₪', // Israeli New Sheqel
      'INR' => '₹', // Indian Rupee
      'JPY' => '¥', // Japanese Yen
      'KRW' => '₩', // South Korean Won
      'NGN' => '₦', // Nigerian Naira
      'PHP' => '₱', // Philippine Peso
      'PLN' => 'zł', // Polish Zloty
      'PYG' => '₲', // Paraguayan Guarani
      'THB' => '฿', // Thai Baht
      'UAH' => '₴', // Ukrainian Hryvnia
      'VND' => '₫' // Vietnamese Dong
    );
    if (isset($currency_symbols[$code]) && $currency_symbols[$code] != "") {
      return $currency_symbols[$code];
    } else {
      return $code;
    }
  }
  /*pixel validation */
  public function is_facebook_pixel_id($string)
  {
    if (empty($string)) {
      return true;
    }
    $re = '/^\d{14,16}$/m';
    return $this->con_validate_with_regex($re, $string);
  }
  public function is_bing_uet_tag_id($string)
  {
    if (empty($string)) {
      return true;
    }
    $re = '/^\d{7,9}$/m';
    return $this->con_validate_with_regex($re, $string);
  }
  public function is_twitter_pixel_id($string)
  {
    if (empty($string)) {
      return true;
    }
    $re = '/^[a-z0-9]{5,7}$/m';
    return $this->con_validate_with_regex($re, $string);
  }
  public function is_pinterest_pixel_id($string)
  {
    if (empty($string)) {
      return true;
    }
    $re = '/^\d{13}$/m';
    return $this->con_validate_with_regex($re, $string);
  }
  public function is_snapchat_pixel_id($string)
  {
    if (empty($string)) {
      return true;
    }
    $re = '/^[a-z0-9\-]*$/m';
    return $this->con_validate_with_regex($re, $string);
  }
  public function is_tiktok_pixel_id($string)
  {
    if (empty($string)) {
      return true;
    }
    $re = '/^[A-Z0-9]{20,20}$/m';
    return $this->con_validate_with_regex($re, $string);
  }
  public function con_validate_with_regex($re, $string)
  {
    // validate if string matches the regex $re
    if (preg_match($re, $string)) {
      return true;
    } else {
      return false;
    }
  }

  // public function validate_pixels()
  // {
  //   $errors = array();
  //   if (isset($_POST["fb_pixel_id"]) && $_POST["fb_pixel_id"] != "" && !$this->is_facebook_pixel_id(sanitize_text_field(wp_unslash($_POST["fb_pixel_id"])))) {
  //     unset($_POST["fb_pixel_id"]);
  //     $errors[] = array("error" => true, "message" => esc_html__("You entered wrong facebook pixel ID.", "enhanced-e-commerce-for-woocommerce-store"));
  //   }
  //   if (isset($_POST["microsoft_ads_pixel_id"]) && $_POST["microsoft_ads_pixel_id"] != "" && !$this->is_bing_uet_tag_id(sanitize_text_field(wp_unslash($_POST["microsoft_ads_pixel_id"])))) {
  //     unset($_POST["microsoft_ads_pixel_id"]);
  //     $errors[] =  array("error" => true, "message" => esc_html__("You entered wrong microsoft ads pixel ID.", "enhanced-e-commerce-for-woocommerce-store"));
  //   }
  //   if (isset($_POST["twitter_ads_pixel_id"]) && $_POST["twitter_ads_pixel_id"] != "" && !$this->is_twitter_pixel_id(sanitize_text_field(wp_unslash($_POST["twitter_ads_pixel_id"])))) {
  //     unset($_POST["twitter_ads_pixel_id"]);
  //     $errors[] =  array("error" => true, "message" => esc_html__("You entered wrong twitter ads pixel ID.", "enhanced-e-commerce-for-woocommerce-store"));
  //   }
  //   if (isset($_POST["pinterest_ads_pixel_id"]) && $_POST["pinterest_ads_pixel_id"] != "" && !$this->is_pinterest_pixel_id(sanitize_text_field(wp_unslash($_POST["pinterest_ads_pixel_id"])))) {
  //     unset($_POST["pinterest_ads_pixel_id"]);
  //     $errors[] =  array("error" => true, "message" => esc_html__("You entered wrong pinterest ads pixel ID.", "enhanced-e-commerce-for-woocommerce-store"));
  //   }
  //   if (isset($_POST["snapchat_ads_pixel_id"]) && $_POST["snapchat_ads_pixel_id"] != "" && !$this->is_snapchat_pixel_id(sanitize_text_field(wp_unslash($_POST["snapchat_ads_pixel_id"])))) {
  //     unset($_POST["snapchat_ads_pixel_id"]);
  //     $errors[] =  array("error" => true, "message" => esc_html__("You entered wrong napchat ads pixel ID.", "enhanced-e-commerce-for-woocommerce-store"));
  //   }
  //   if (isset($_POST["tiKtok_ads_pixel_id"]) && $_POST["tiKtok_ads_pixel_id"] != "" && !$this->is_tiktok_pixel_id(sanitize_text_field(wp_unslash($_POST["tiKtok_ads_pixel_id"])))) {
  //     unset($_POST["tiKtok_ads_pixel_id"]);
  //     $errors[] =  array("error" => true, "message" => esc_html__("You entered wrong tiKtok ads pixel ID.", "enhanced-e-commerce-for-woocommerce-store"));
  //   }
  //   return $errors;
  // }
  /*
  * Add Plugin logs
  */
  public function plugin_log($message, $file = 'plugin')
  {
    // Get WordPress uploads directory.
    if (is_array($message)) {
      $message = wp_json_encode($message);
    }
    $log = new WC_Logger();
    $log->add('Conversios Product Sync Log ', $message);
    //error_log($message);
    return true;
  }

  /*
   * get user roles from wp
   */
  function conv_get_user_roles()
  {
    $wp_usr_roles   = new WP_Roles();
    $user_roles_arr = array();
    foreach ($wp_usr_roles->get_names() as $slug => $name) {
      $user_roles_arr[$slug] = $name;
    }
    return $user_roles_arr;
  }

  /*
   * get user roles from wp
   */
  function conv_all_pixel_event()
  {
    $conv_pixel_events['ecommerce'] = array(
      "view_item_list" => "View item list",
      "select_item" => "Select item",
      "add_to_cart_list" => "Add to cart on item list",
      "view_item" => "View Item",
      "add_to_cart_single" => "Add to cart on single item",
      "view_cart" => "View cart",
      "remove_from_cart" => "Remove from cart",
      "begin_checkout" => "Begin checkout",
      "add_shipping_info" => "Add shipping info",
      "add_payment_info" => "Add payment info",
      "purchase" => "Purchase"
    );

    $conv_pixel_events['lead_generation'] = array(
      "form_submit" => "Form Submit",
      "email_click" => "Email Link Click",
      "phone_click" => "Phone Link Click",
      "address_click" => "Address Link Click",
    );

    ksort($conv_pixel_events);
    return $conv_pixel_events;
  }

  function get_conv_pro_link($advance_utm_medium = "", $advance_linkclass = "tvc-pro", $advance_linktype = "anchor", $upgradetopro_text_param = "Upgrade to Pro")
  {
    $conv_advanced_utm_arr = array(
      "pixel_setting" => "Pixel+Settings+upgrading+Pro+to+Use+Google+Ads+Enhanced+Conversion+Tracking+Link",
      "fb_pixel_setting" => "FB+Pixel+Settings+upgrading+Pro+to+Use+Google+Ads+Enhanced+Conversion+Tracking+Link",
      "onboarding" => "Onboarding+upgrading+Pro+to+Use+Google+Ads+conversion+tracking+Link",
      "dashboard" => "dashboard",
      "product_feed" => "All+In+One+Product+Feed",
      "top_bar" => "Top+Bar+upgrading+to+pro",
      "account_summary" => "AAccount+Summary+pro+version",
    );

    $conv_advance_plugin_link = esc_url($this->get_pro_plan_site() . "?utm_source=EE+Plugin+User+Interface&utm_medium=" . $conv_advanced_utm_arr[$advance_utm_medium] . "&utm_campaign=Upsell+at+Conversios");
    $conv_advance_plugin_link_return = "";
    $upgradetopro_text = sprintf('%s', esc_html($upgradetopro_text_param));
    if ($advance_linktype == "anchor") {
      $conv_advance_plugin_link_return = "<a href='" . $conv_advance_plugin_link . "' target='_blank' class='" . $advance_linkclass . "'> " . $upgradetopro_text . "</a>";
    }
    if ($advance_linktype == "linkonly") {
      $conv_advance_plugin_link_return = $conv_advance_plugin_link;
    }
    return $conv_advance_plugin_link_return;
  }

  function get_conv_pro_link_adv($advance_utm_medium = "popup", $advance_utm_campaign = "pixel_setting", $advance_linkclass = "tvc-pro", $advance_linktype = "anchor", $upgradetopro_text_param = "Upgrade to Pro")
  {
    $conv_advance_plugin_link = esc_url($this->get_pro_plan_site() . "?utm_source=woo_aiofree_plugin&utm_medium=" . $advance_utm_medium . "&utm_campaign=" . $advance_utm_campaign);
    $conv_advance_plugin_link_return = "";
    $upgradetopro_text = sprintf('%s', esc_html($upgradetopro_text_param));
    if ($advance_linktype == "anchor") {
      $conv_advance_plugin_link_return = "<a href='" . $conv_advance_plugin_link . "' target='_blank' class='" . $advance_linkclass . "'> " . $upgradetopro_text . "</a>";
    }
    if ($advance_linktype == "linkonly") {
      $conv_advance_plugin_link_return = $conv_advance_plugin_link;
    }
    return $conv_advance_plugin_link_return;
  }

  public function get_feed_status()
  {
    $google_detail = $this->get_ee_options_data();
    if (isset($google_detail['setting']->store_id)) {
      $data = array(
        "store_id" => $google_detail['setting']->store_id,
      );
      $response = $this->customApiObj->get_feed_status_by_store_id($data);
      //echo '<pre>'; print_r($response); echo '</pre>'; // woow 1424 - feed list
      if (isset($response->data)) {
        foreach ($response->data as $key => $val) {
          $profile_data = array(
            'status' => esc_sql($val->status_name), // woow 1426 - feed list
            'tiktok_status' => esc_sql($val->tiktok_status_name),
            'fb_status' => esc_sql($val->facebook_status_name),
            'ms_status' => esc_sql($val->microsoft_status_name),
          );
          $this->TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => $val->store_feed_id));
        }
      }
    }
    return true;
  }

  public function ee_get_results($table)
  {
    global $wpdb;
    if ($table == "") {
      return;
    } else {
      $tablename = esc_sql($wpdb->prefix . $table);
      $sql = $wpdb->prepare("select * from %i order by id desc", $tablename);
      return $wpdb->get_results($sql);
    }
  }

  public function ee_get_result_limit($table, $limit)
  {
    global $wpdb;
    if ($table == "") {
      return;
    } else {
      $tablename = esc_sql($wpdb->prefix . $table);
      $sql = $wpdb->prepare("select * from `$tablename` ORDER BY id DESC LIMIT %d", $limit);
      return $wpdb->get_results($sql);
    }
  }

  public function get_custom_connect_url_superfeed($confirm_url = "", $subpage = "")
  {
    $feedType = "superfeed";
    $connect_sf_url = "https://" . TVC_AUTH_CONNECT_URL . "/config3/ga_rdr_gmc.php?return_url=" . TVC_AUTH_CONNECT_URL . "/config3/ads-analytics-form.php?domain=" . $this->get_connect_actual_link() . "&amp;country=" . $this->get_woo_country() . "&amp;user_currency=" . $this->get_woo_currency() . "&amp;subscription_id=" . $this->get_subscriptionId() . "&amp;confirm_url=" . $confirm_url . "&amp;subpage=" . $subpage . "&amp;timezone=" . $this->get_time_zone() . "&amp;feedType=" . $feedType;
    return $connect_sf_url;
  }

  public function get_tiktok_business_id()
  {
    $tiktok_detail = $this->get_ee_options_settings();
    return $this->tiktok_business_id = (isset($tiktok_detail['tiktok_setting']['tiktok_business_id'])) ? $tiktok_detail['tiktok_setting']['tiktok_business_id'] : "";
  }

  public function generateRandomStringConv($length = 16)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[wp_rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }
}
