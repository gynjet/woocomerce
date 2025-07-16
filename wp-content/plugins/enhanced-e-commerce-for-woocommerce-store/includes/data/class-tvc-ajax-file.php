<?php

/**
 * TVC Ajax File Class.
 *
 * @package TVC Product Feed Manager/Data/Classes
 */
if (!defined('ABSPATH')) {
  exit;
}

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

if (!class_exists('TVC_Ajax_File')) :
  /**
   * Ajax File Class
   */
  class TVC_Ajax_File extends TVC_Ajax_Calls
  {
    private $apiDomain;
    public function __construct()
    {
      parent::__construct();
      $this->apiDomain = TVC_API_CALL_URL;
      // hooks
      add_action('wp_ajax_tvcajax-get-campaign-categories', array($this, 'tvcajax_get_campaign_categories'));
      add_action('wp_ajax_tvcajax-update-campaign-status', array($this, 'tvcajax_update_campaign_status'));
      add_action('wp_ajax_tvcajax-delete-campaign', array($this, 'tvcajax_delete_campaign'));

      add_action('wp_ajax_tvcajax-gmc-category-lists', array($this, 'tvcajax_get_gmc_categories'));
      //add_action('wp_ajax_tvcajax-custom-metrics-dimension', array($this, 'tvcajax_custom_metrics_dimension'));
      add_action('wp_ajax_tvcajax-store-time-taken', array($this, 'tvcajax_store_time_taken'));

      // add_action('wp_ajax_tvc_call_api_sync', array($this, 'tvc_call_api_sync'));
      // add_action('wp_ajax_tvc_call_import_gmc_product', array($this, 'tvc_call_import_gmc_product'));
      add_action('wp_ajax_tvc_call_domain_claim', array($this, 'tvc_call_domain_claim'));
      add_action('wp_ajax_tvc_call_site_verified', array($this, 'tvc_call_site_verified'));
      // add_action('wp_ajax_tvc_call_notice_dismiss', array($this, 'tvc_call_notice_dismiss'));
      // add_action('wp_ajax_tvc_call_notice_dismiss_trigger', array($this, 'tvc_call_notice_dismiss_trigger'));
      // add_action('wp_ajax_tvc_call_notification_dismiss', array($this, 'tvc_call_notification_dismiss'));
      add_action('wp_ajax_auto_product_sync_setting', array($this, 'auto_product_sync_setting'));
      add_action('wp_ajax_con_get_conversion_list', array($this, 'con_get_conversion_list'));
      add_action('wp_ajax_conv_get_microsoft_ads_conversion', array($this, 'conv_get_microsoft_ads_conversion'));
      add_action('wp_ajax_tvc_call_active_licence', array($this, 'tvc_call_active_licence'));
      add_action('wp_ajax_tvc_call_add_survey', array($this, 'tvc_call_add_survey'));
      add_action('wp_ajax_cov_save_badge_settings', array($this, 'cov_save_badge_settings'));

      add_action('wp_ajax_tvc_call_add_customer_feedback', array($this, 'tvc_call_add_customer_feedback'));
      add_action('wp_ajax_tvc_call_add_customer_featurereq', array($this, 'tvc_call_add_customer_featurereq'));

      // Not in use after product sync from backend
      //add_action('wp_ajax_tvcajax_product_sync_bantch_wise', array($this, 'tvcajax_product_sync_bantch_wise'));//deprecated!
      add_action('wp_ajax_update_user_tracking_data', array($this, 'update_user_tracking_data'));
      add_action('init_product_sync_process_scheduler', array($this, 'tvc_call_start_product_sync_process'), 10, 1);
      add_action('wp_ajax_auto_product_sync_process_scheduler', array($this, 'tvc_call_start_product_sync_process'));

      // For new UIUX
      add_action('wp_ajax_conv_save_pixel_data', array($this, 'conv_save_pixel_data'));
      add_action('wp_ajax_conv_save_googleads_data', array($this, 'conv_save_googleads_data'));
      // add_action('wp_ajax_conv_get_conversion_list_gads', array($this, 'conv_get_conversion_list_gads'));
      add_action('wp_ajax_save_category_mapping', [$this, 'save_category_mapping']);
      add_action('wp_ajax_save_attribute_mapping', [$this, 'save_attribute_mapping']);
      add_action('wp_ajax_save_feed_data', [$this, 'save_feed_data']);
      add_action('wp_ajax_get_feed_data_by_id', [$this, 'get_feed_data_by_id']);
      add_action('wp_ajax_ee_duplicate_feed_data_by_id', [$this, 'ee_duplicate_feed_data_by_id']);
      add_action('wp_ajax_ee_get_product_details_for_table', [$this, 'ee_get_product_details_for_table']);
      add_action('wp_ajax_ee_delete_feed_data_by_id', [$this, 'ee_delete_feed_data_by_id']);
      add_action('wp_ajax_ee_delete_feed_gmc', [$this, 'ee_delete_feed_gmc']);
      add_action('wp_ajax_ee_get_product_status', [$this, 'ee_get_product_status']);
      add_action('wp_ajax_ee_syncProductCategory', [$this, 'ee_syncProductCategory']);
      add_action('wp_ajax_ee_feed_wise_product_sync_batch_wise', [$this, 'ee_feed_wise_product_sync_batch_wise']);
      add_action('init_feed_wise_product_sync_process_scheduler_ee', [$this, 'ee_call_start_feed_wise_product_sync_process']);
      add_action('auto_feed_wise_product_sync_process_scheduler_ee', [$this, 'ee_call_auto_feed_wise_product_sync_process']);
      add_action('wp_ajax_ee_super_AI_feed', [$this, 'ee_super_AI_feed']);
      add_action('wp_ajax_get_tiktok_business_account', [$this, 'get_tiktok_business_account']);
      add_action('wp_ajax_get_tiktok_user_catalogs', [$this, 'get_tiktok_user_catalogs']);
      add_action('wp_ajax_ee_getCatalogId', [$this, 'ee_getCatalogId']);
      add_action('wp_ajax_update_business_details', [$this, 'update_business_details']);

      // For EC
      add_action('wp_ajax_conv_create_ec_row', array($this, 'conv_create_ec_row'));
      // add_action('wp_ajax_conv_create_ec_row_update', array($this, 'conv_create_ec_row_update'));
      add_action('wp_ajax_ee_createPmaxCampaign', [$this, 'ee_createPmaxCampaign']);
      add_action('wp_ajax_ee_createPmaxCampaign_ms', [$this, 'ee_createPmaxCampaign_ms']);
      add_action('wp_ajax_ee_editPmaxCampaign', [$this, 'ee_editPmaxCampaign']);
      add_action('wp_ajax_ee_editPmaxCampaign_ms', [$this, 'ee_editPmaxCampaign_ms']);
      add_action('wp_ajax_ee_update_PmaxCampaign', [$this, 'ee_update_PmaxCampaign']);
      add_action('wp_ajax_ee_update_PmaxCampaign_ms', [$this, 'ee_update_PmaxCampaign_ms']);

      add_action('wp_ajax_conv_get_conversion_list_gads_bycat', [$this, 'conv_get_conversion_list_gads_bycat']);
      add_action('wp_ajax_conv_create_gads_conversion', [$this, 'conv_create_gads_conversion']);
      add_action('wp_ajax_conv_create_microsoft_ads_conversion', [$this, 'conv_create_microsoft_ads_conversion']);
      add_action('wp_ajax_conv_save_gads_conversion', [$this, 'conv_save_gads_conversion']);
      add_action('wp_ajax_conv_save_microsoft_ads_conversion', [$this, 'savemicrosoftadsconversions']);
      add_action('wp_ajax_get_pf_accordian_data', array($this, 'get_pf_accordian_data'));
      add_action('wp_ajax_get_category_for_filter', [$this, 'get_category_for_filter']);
      add_action('wp_ajax_get_product_filter_count', [$this, 'get_product_filter_count']);
      add_action('wp_ajax_get_attribute_mappingv_div', [$this, 'get_attribute_mappingv_div']);
      add_action('wp_ajax_create_dashboard_feed_data', [$this, 'create_dashboard_feed_data']);
      add_action('wp_ajax_get_user_businesses', array($this, 'get_user_businesses'));
      add_action('wp_ajax_get_fb_catalog_data', array($this, 'get_fb_catalog_data'));
      add_action('wp_ajax_conv_checkMcc', [$this, 'conv_checkMcc']);
      add_action('wp_ajax_conv_send_email', array($this, 'conv_send_email'));
      //add_action('wp_ajax_conv_convnewfeaturemodal_ajax', array($this, 'conv_convnewfeaturemodal_ajax'));
      add_action('wp_ajax_convert_budget_to_local_currency', [$this, 'convert_budget_to_local_currency']);
      // require_once(ENHANCAD_PLUGIN_DIR . 'admin/partials/customermatch/export.php');
      // $exporter = new Conv_Exporter();
      //add_action('wp_ajax_conv_export_users_csv', array($exporter, 'conv_export_users_csv'));
    }

    public function conv_create_ec_row()
    {
      if (
        isset($_POST['pix_sav_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pix_sav_nonce'])), 'pix_sav_nonce_val') &&
        $this->safe_ajax_call(sanitize_text_field(wp_unslash($_POST['pix_sav_nonce'])), 'pix_sav_nonce_val')
      ) {
        $customObj = new CustomApi();
        $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $ecrandomstring = isset($_POST['ecrandomstring']) ? sanitize_text_field(wp_unslash($_POST['ecrandomstring'])) : "";
        $formdata = array("subscription_id" => $subscription_id, "ec_token" => $ecrandomstring);
        echo wp_json_encode($customObj->conv_create_ec_row_apicall($formdata));
      }
      die();
    }

    public function conv_checkMcc()
    {
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'CONVNonce', FILTER_UNSAFE_RAW), 'conv_checkMcc-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $customApiObj = new CustomApi();

        if (isset($_POST['CONVNonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['CONVNonce'])), 'conv_checkMcc-nonce')) {
          $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
          $ads_accountId = isset($_POST['ads_accountId']) ? sanitize_text_field(wp_unslash($_POST['ads_accountId'])) : "";

          // Proceed with processing only after nonce verification
        }

        if ($subscription_id != "" && $ads_accountId != "") {
          $response = $customApiObj->ads_checkMcc($subscription_id, $ads_accountId);
          echo wp_json_encode($response);
        }
      }
      exit;
    }

    public function conv_send_email()
    {
      if (isset($_POST['sendmail_req_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sendmail_req_nonce'])), 'sendmail_req_nonce_val')) {
        $name = isset($sendmail_message['name']) ? sanitize_text_field($sendmail_message['name']) : '';
        $phone = isset($sendmail_message['phone']) ? sanitize_text_field($sendmail_message['phone']) : '';
        $email = isset($sendmail_message['email']) ? sanitize_text_field($sendmail_message['email']) : '';
        $businessName = isset($sendmail_message['businessName']) ? sanitize_text_field($sendmail_message['businessName']) : '';
        $emailmessage = "<ul><li><b>Name:</b> $name</li><li><b>Contact No:</b> $phone</li><li><b>Email:</b> $email</li><li><b>Business Name:</b> $businessName</li></ul>";
        $formdata = array();
        $formdata['to'] = isset($_POST["sendmail_to"]) ? sanitize_text_field(wp_unslash($_POST["sendmail_to"])) : "";
        $formdata['cc'] = isset($_POST["sendmail_cc"]) ? sanitize_text_field(wp_unslash($_POST["sendmail_cc"])) : "";
        $formdata['bcc'] = isset($_POST["sendmail_bcc"]) ? sanitize_text_field(wp_unslash($_POST["sendmail_bcc"])) : "";
        $formdata['subject'] = isset($_POST["sendmail_subject"]) ? sanitize_text_field(wp_unslash($_POST["sendmail_subject"])) : "";
        $formdata['message'] = $emailmessage;
        $formdata['subscription_id'] = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $customObj = new CustomApi();
        unset($_POST['action']);
        echo wp_json_encode($customObj->conv_send_email($formdata));
        die(1);
      } else {
        die(0);
      }
      die();
    }



    // Save data in ee_options
    public function conv_save_data_eeoption($data)
    {
      $ee_options = unserialize(get_option('ee_options'));
      foreach ($data['conv_options_data'] as $key => $conv_options_data) {
        if ($key == "conv_selected_events") {
          continue;
        }
        $key_name = $key;
        $key_name_arr = array();
        $key_name_arr["measurement_id"] = "gm_id";
        $key_name_arr["property_id"] = "ga_id";
        if (key_exists($key_name, $key_name_arr)) {
          $ee_options[$key_name_arr[$key_name]] = sanitize_text_field($conv_options_data);
        } else {

          if (is_array($conv_options_data)) {
            $posted_arr = $conv_options_data;
            $posted_arr_temp = [];
            if (!empty($posted_arr)) {
              $arr = $posted_arr;
              array_walk($arr, function (&$value) {
                $value = sanitize_text_field($value);
              });
              $posted_arr_temp = $arr;
              $ee_options[$key_name] = $posted_arr_temp;
            }
          } else {
            $ee_options[$key_name] = sanitize_text_field($conv_options_data);
          }
        }
      }
      update_option('ee_options', serialize($ee_options));
      //echo '<pre>'; print_r(unserialize(get_option('ee_options'))); echo '</pre>'; exit('ohh');
    }

    // Save data in ee_options
    public function conv_save_data_eeapidata($data)
    {
      $eeapidata = unserialize(get_option('ee_api_data'));
      $eeapidata_settings = $eeapidata['setting'];
      if (empty($eeapidata_settings)) {
        $eeapidata_settings = new stdClass();
      }

      foreach ($data['conv_options_data'] as $key => $conv_options_data) {
        if ($key == "conv_selected_events") {
          continue;
        }

        $key_name = $key;

        if (is_array($conv_options_data)) {
          $posted_arr = $conv_options_data;
          $posted_arr_temp = [];
          if (!empty($posted_arr)) {
            $arr = $posted_arr;
            array_walk($arr, function (&$value) {
              $value = sanitize_text_field($value);
            });
            $posted_arr_temp = $arr;
            $eeapidata_settings->$key_name = $posted_arr_temp;
          }
        } else {
          $eeapidata_settings->$key_name = sanitize_text_field($conv_options_data);
          if ($key_name == "google_merchant_center_id") {
            $eeapidata_settings->google_merchant_id = sanitize_text_field($conv_options_data);
          }
        }
      }
      $eeapidata['setting'] = $eeapidata_settings;
      update_option('ee_api_data', serialize($eeapidata));
    }

    //Save data in middleware
    public function conv_save_data_middleware($postDataFull = array())
    {
      $postData = $postDataFull['conv_options_data'];
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $google_detail = $TVC_Admin_Helper->get_ee_options_data();
      try {
        $url = $this->apiDomain . '/customer-subscriptions/update-detail';
        $header = array("Authorization: Bearer MTIzNA==", "Content-Type" => "application/json");
        $data = array();
        foreach ($postData as $key => $value) {
          $data[$key] = sanitize_text_field((isset($value)) ? $value : '');
        }
        $data['store_id'] = $google_detail['setting']->store_id;
        $data["subscription_id"] = $google_detail['setting']->id;
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = wp_remote_request(esc_url_raw($url), $args);
      } catch (Exception $e) {
        return $e->getMessage();
      }
    }

    // Save data in ee_convnotices
    public function conv_save_eeconvnotice($data)
    {
      $ee_eeconvnotice = get_option('ee_convnotice', array());
      $keyname = sanitize_text_field($data['conv_options_data']);
      $ee_eeconvnotice[$keyname] = "yes";
      update_option('ee_convnotice', $ee_eeconvnotice);
    }


    // All new functions for new UIUX
    public function conv_save_pixel_data()
    {
      if (
        isset($_POST['pix_sav_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pix_sav_nonce'])), 'pix_sav_nonce_val')
      ) {
        $post = array(
          "conv_options_data" => "",
          "conv_options_type" => "",
          "conv_tvc_data" => "",
          "update_site_domain" => "",
          "customer_subscription_id" => "",
          "conv_catalogData" => "",
        );
        $post = array_intersect_key($_POST, $post);

        $TVC_Admin_Helper = new TVC_Admin_Helper();
        if (isset($_POST['conv_options_type']) && in_array("eeoptions", $_POST['conv_options_type'])) {
          $this->conv_save_data_eeoption($post);
        }
        if (isset($_POST['conv_options_type']) && in_array("middleware", $_POST['conv_options_type'])) {
          $this->conv_save_data_middleware($post);
        }
        if (isset($_POST['conv_options_type']) && in_array("eeapidata", $_POST['conv_options_type'])) {
          $this->conv_save_data_eeapidata($post);
        }
        if (isset($_POST['conv_options_type']) && in_array("eeapidata", $_POST['conv_options_type'])) {
          if (isset($_POST['update_site_domain']) && $_POST['update_site_domain'] === 'update') {
            $post['conv_options_data']['is_site_verified'] = '0';
            $post['conv_options_data']['is_domain_claim'] = '0';
          }
          $this->conv_save_data_eeapidata($post);
        }
        if (isset($_POST['conv_options_data']['ga_GMC']) && $_POST['conv_options_data']['ga_GMC'] == '1' && isset($_POST['conv_options_data']['merchant_id'])) {
          $api_obj = new Conversios_Onboarding_ApiCall();
          $postData = [
            'subscription_id' => isset($_POST['conv_options_data']['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['subscription_id'])) : '',
            'merchant_id' => isset($_POST['conv_options_data']['merchant_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['merchant_id'])) : '',
            'account_id' => isset($_POST['conv_options_data']['google_merchant_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['google_merchant_id'])) : '',
            'adwords_id' => isset($_POST['conv_options_data']['google_ads_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['google_ads_id'])) : ''
          ];
          $api_obj->linkGoogleAdsToMerchantCenter($postData);
        }
        if (isset($_POST['conv_options_data']['ga_cid']) && $_POST['conv_options_data']['ga_cid'] == '1') {
          $api_obj = new Conversios_Onboarding_ApiCall();
          $api_obj->gaDimension();
        }
        //echo '<pre>'; print_r($_POST); echo '</pre>';
        if (isset($_POST['conv_options_data']['non_woo_tracking']) && $_POST['conv_options_data']['non_woo_tracking'] == '1') {
          $non_woo_data = array(
            'conv_track_page_scroll' => '1',
            'conv_track_file_download' => '1',
            'conv_track_author' => '1',
            'conv_track_signup' => '1',
            'conv_track_signin' => '1',
          );
          $data = get_option('ee_options');
          $data = $data ? maybe_unserialize($data) : array();
          $updated_data = array_merge($data, $non_woo_data);
          $serialized_data = maybe_serialize($updated_data);
          update_option('ee_options', $serialized_data);
        }
        if ((isset($_POST['conv_options_data']['non_woo_tracking']) || isset($_POST['conv_options_data']['conv_track_page_scroll']) || isset($_POST['conv_options_data']['conv_track_file_download']) || isset($_POST['conv_options_data']['conv_track_author']) || isset($_POST['conv_options_data']['conv_track_signup']) || isset($_POST['conv_options_data']['conv_track_signin']))) {
          $data = array();
          $data['conv_track_page_scroll'] = '1';
          $data['conv_track_file_download'] = '1';
          $data['conv_track_author'] = '1';
          $data['conv_track_signin'] = '1';
          $data['conv_track_signup'] = '1';
          $api_obj = new Conversios_Onboarding_ApiCall();
          $api_obj->additional_dimensions($data);
        }
        if (in_array("eeselectedevents", $_POST['conv_options_type']) && isset($_POST["conv_options_data"]["conv_selected_events"]['ga'])) {
          $selectedevents = is_array($_POST["conv_options_data"]["conv_selected_events"]['ga']) ? array_map('sanitize_text_field', wp_unslash($_POST["conv_options_data"]["conv_selected_events"]['ga'])) : sanitize_text_field(wp_unslash($_POST["conv_options_data"]["conv_selected_events"]['ga']));
          $selectedevents['ga'] = $selectedevents;
          update_option("conv_selected_events", serialize($selectedevents));
        }
        if (isset($_POST['conv_options_type']) && in_array("tiktokmiddleware", $_POST['conv_options_type'])) {
          $this->conv_save_tiktokmiddleware($post);
        }
        if (isset($_POST['conv_options_type']) && in_array("tiktokcatalog", $_POST['conv_options_type'])) {
          $this->conv_save_tiktokcatalog($post);
        }

        if (isset($_POST['conv_options_type']) && in_array("facebookmiddleware", $_POST['conv_options_type'])) {
          $this->conv_save_facebookmiddleware($_POST);
        }
        if (isset($_POST['conv_options_type']) && in_array("facebookcatalog", $_POST['conv_options_type'])) {
          $this->conv_save_facebookcatalog($_POST);
        }
        if (
          isset($_POST['conv_options_data']) &&
          (
            array_key_exists("microsoft_ads_manager_id", $_POST['conv_options_data'])
            || array_key_exists("microsoft_ads_subaccount_id", $_POST['conv_options_data'])
            || array_key_exists("microsoft_ads_pixel_id", $_POST['conv_options_data'])
            || array_key_exists("microsoft_merchant_center_id", $_POST['conv_options_data'])
            || array_key_exists("ms_catalog_id", $_POST['conv_options_data'])
          )
          &&
          (
            !empty($_POST['conv_options_data']['microsoft_ads_manager_id'])
            || !empty($_POST['conv_options_data']['microsoft_ads_subaccount_id'])
            || !empty($_POST['conv_options_data']['microsoft_ads_pixel_id'])
            || !empty($_POST['conv_options_data']['microsoft_merchant_center_id'])
            || !empty($_POST['conv_options_data']['ms_catalog_id'])
          )
        ) {
          $this->conv_save_microsoft($_POST['conv_options_data']);
        }


        if (isset($_POST['conv_options_type']) && in_array("eeconvnotice", $_POST['conv_options_type'])) {
          $this->conv_save_eeconvnotice($post);
        }

        $TVC_Admin_Helper->update_app_status();
        $TVC_Admin_Helper->update_subscription_details_api_to_db();
        echo "1";
      } else {
        echo "0";
      }
      exit;
    }
    // All new functions for new UIUX End

    // Save google ads settings
    public function conv_save_googleads_data()
    {
      if (
        isset($_POST['pix_sav_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pix_sav_nonce'])), 'pix_sav_nonce_val')
      ) {
        $conv_options_data = isset($_POST['conv_options_data']) && is_array($_POST['conv_options_data']) ? array_map('sanitize_text_field', wp_unslash($_POST['conv_options_data'])) : (isset($_POST['conv_options_data']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data'])) : '');
        $googleDetail_setting = array();
        if (isset($conv_options_data['remarketing_tags'])) {
          update_option('ads_ert', sanitize_text_field($conv_options_data['remarketing_tags']));
          $googleDetail_setting["remarketing_tags"] = sanitize_text_field($conv_options_data['remarketing_tags']);
        }
        if (isset($conv_options_data['dynamic_remarketing_tags'])) {
          update_option('ads_edrt', sanitize_text_field($conv_options_data['dynamic_remarketing_tags']));
          $googleDetail_setting["dynamic_remarketing_tags"] = sanitize_text_field($conv_options_data['dynamic_remarketing_tags']);
        }

        if (isset($conv_options_data['google_ads_conversion_tracking'])) {
          // update one flag in middleware also.
          update_option('google_ads_conversion_tracking', sanitize_text_field($conv_options_data['google_ads_conversion_tracking']));
          $googleDetail_setting["google_ads_conversion_tracking"] = sanitize_text_field($conv_options_data['google_ads_conversion_tracking']);
        }

        if (isset($conv_options_data['ga_EC'])) {
          update_option('ga_EC', sanitize_text_field($conv_options_data['ga_EC']));
        }

        if (isset($conv_options_data['ee_conversio_send_to'])) {
          update_option('ee_conversio_send_to', sanitize_text_field($conv_options_data['ee_conversio_send_to']));
          $googleDetail_setting["ee_conversio_send_to"] = sanitize_text_field($conv_options_data['ee_conversio_send_to']);
        }

        if (isset($conv_options_data['ee_conversio_send_to_static']) && !empty($conv_options_data['ee_conversio_send_to_static'])) {
          update_option('ee_conversio_send_to', sanitize_text_field($conv_options_data['ee_conversio_send_to_static']));
          $googleDetail_setting["ee_conversio_send_to"] = sanitize_text_field($conv_options_data['ee_conversio_send_to_static']);
        }

        if (isset($conv_options_data['link_google_analytics_with_google_ads'])) {
          $googleDetail_setting["link_google_analytics_with_google_ads"] = sanitize_text_field($conv_options_data['link_google_analytics_with_google_ads']);
        }

        $googleDetail_setting["subscription_id"] = sanitize_text_field($conv_options_data['subscription_id']);

        $data_eeoptions = array();
        $data_eeapidata = array();
        $data_middleware = array();

        $data_eeoptions['conv_options_data']['google_ads_id'] = $conv_options_data['google_ads_id'];
        if (isset($conv_options_data['ga_GMC']) && $conv_options_data['ga_GMC'] == '1') {
          $data_eeoptions['conv_options_data']['ga_GMC'] = sanitize_text_field($conv_options_data['ga_GMC']);
        }
        $this->conv_save_data_eeoption($data_eeoptions);

        $data_eeapidata['conv_options_data'] = $conv_options_data;
        $this->conv_save_data_eeapidata($data_eeapidata);

        $googleDetail_setting['google_ads_id'] = sanitize_text_field($conv_options_data['google_ads_id']);
        $data_middleware['conv_options_data'] = $googleDetail_setting;

        $this->conv_save_data_middleware($data_middleware);

        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->update_remarketing_snippets();
        $TVC_Admin_Helper->update_app_status();
        if (isset($conv_options_data['ga_GMC']) && $conv_options_data['ga_GMC'] == '1' && isset($_POST['conv_options_data']['merchant_id'])) {
          $api_obj = new Conversios_Onboarding_ApiCall();
          $postData = [
            'subscription_id' => isset($_POST['conv_options_data']['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['subscription_id'])) : '',
            'merchant_id' => isset($_POST['conv_options_data']['merchant_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['merchant_id'])) : '',
            'account_id' => isset($_POST['conv_options_data']['google_merchant_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['google_merchant_id'])) : '',
            'adwords_id' => isset($_POST['conv_options_data']['google_ads_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['google_ads_id'])) : '',
          ];
          $api_obj->linkGoogleAdsToMerchantCenter($postData);
        }

        if (isset($conv_options_data['link_google_analytics_with_google_ads'])) {
          $api_obj = new Conversios_Onboarding_ApiCall();
          $postData = [
            'ads_customer_id' => sanitize_text_field($conv_options_data['google_ads_id']),
            'web_property_id' => isset($conv_options_data['web_property_id']) ? sanitize_text_field($conv_options_data['web_property_id']) : "",
            'web_property' => isset($conv_options_data['web_property_id']) ? sanitize_text_field($conv_options_data['web_property_id']) : "",
            'subscription_id' => sanitize_text_field($conv_options_data['subscription_id'])
          ];
          $api_obj->linkAnalyticToAdsAccount($postData);
        }
      }
      echo "1";
      exit;
    }


    public function cov_save_badge_settings()
    {
      $nonce = isset($_POST['conv_nonce']) ? sanitize_text_field(wp_unslash($_POST['conv_nonce'])) : '';
      if (wp_verify_nonce($nonce, 'conv_nonce_val')) {

        $val = isset($_POST['bagdeVal']) ? sanitize_text_field(wp_unslash($_POST['bagdeVal'])) : "no";
        $data = array();
        $data = unserialize(get_option('ee_options'));
        $data['conv_show_badge'] = sanitize_text_field($val);
        if ($val == "yes") {
          $data['conv_badge_position'] = sanitize_text_field(wp_unslash("center"));
        } else {
          $data['conv_badge_position'] = "";
        }
        update_option('ee_options', serialize($data));
      }
      exit;
    }

    public function update_user_tracking_data()
    {
      $nonce = filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'update_user_tracking_data-nonce')) {
        $event_name = isset($_POST['event_name']) ? sanitize_text_field(wp_unslash($_POST['event_name'])) : "";
        $screen_name = isset($_POST['screen_name']) ? sanitize_text_field(wp_unslash($_POST['screen_name'])) : "";
        $error_msg = isset($_POST['error_msg']) ? sanitize_text_field(wp_unslash($_POST['error_msg'])) : "";
        $event_label = isset($_POST['event_label']) ? sanitize_text_field(wp_unslash($_POST['event_label'])) : "";
        // $timestamp = isset($_POST['timestamp'])?sanitize_text_field(wp_unslash($_POST['timestamp'])) : "";
        $timestamp = gmdate("YmdHis");
        $t_data = array(
          'event_name' => esc_sql($event_name),
          'screen_name' => esc_sql($screen_name),
          'timestamp' => esc_sql($timestamp),
          'error_msg' => esc_sql($error_msg),
          'event_label' => esc_sql($event_label),
        );
        if (!empty($t_data)) {

          $options_val = get_option('ee_ut');
          if (!empty($options_val)) {
            $odata = (array) maybe_unserialize($options_val);
            array_push($odata, $t_data);
            update_option("ee_ut", serialize($odata));
          } else {
            $t_d[] = $t_data;
            update_option("ee_ut", serialize($t_d));
          }
        }
        wp_die();
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      // IMPORTANT: don't forget to exit
      exit;
    }


    function tvc_call_start_product_sync_process()
    {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      try {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        as_unschedule_all_actions('init_product_sync_process_scheduler');
        as_schedule_single_action(time(), 'init_product_sync_process_scheduler');
        global $wpdb;
        if (!empty($ee_additional_data) && isset($ee_additional_data['is_mapping_update']) && $ee_additional_data['is_mapping_update'] == true) {
          $product_db_batch_size = 200; // batch size to insert in database
          $prouct_pre_sync_table = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
          $mappedCats = unserialize(get_option('ee_prod_mapped_cats'));
          // Add products in product pre sync table
          if (!empty($mappedCats)) {
            // truncate data from product pre sync table
            if ($TVC_Admin_DB_Helper->tvc_row_count("ee_prouct_pre_sync_data") > 0) {
              $TVC_Admin_DB_Helper->tvc_safe_truncate_table($prouct_pre_sync_table);
            }

            $batch_count = 0;
            $values = array();
            $place_holders = array();
            foreach ($mappedCats as $mc_key => $mappedCat) {
              $term = get_term_by('term_id', $mc_key, 'product_cat', 'ARRAY_A');
              //$TVC_Admin_Helper->plugin_log(" = = = =category id ".wp_json_encode($term), 'product_sync');
              //die;
              $total_page = 1;
              if (isset($term["count"]) && $term["count"] > 1000) {
                $total_page = ceil($term["count"] / 1000);
              }

              for ($i = 1; $i <= $total_page; $i++) {
                $TVC_Admin_Helper->plugin_log("Manual - category > " . $mappedCat['name'] . " > total_page " . wp_json_encode($total_page) . " page > " . $i, 'product_sync');
                $all_products = get_posts(
                  array(
                    'post_type' => 'product',
                    'posts_per_page' => 1000,
                    'paged' => $i,
                    'numberposts' => -1,
                    'post_status' => 'publish',
                    'fields' => 'ids',
                    'tax_query' => array(
                      array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $mc_key,
                        /* category name */
                        'operator' => 'IN',
                        'include_children' => false
                      )
                    )
                  )
                );
                $TVC_Admin_Helper->plugin_log("Manual - category id " . $mc_key . " gmc product name " . $mappedCat['name'] . " - product count - " . count($all_products), 'product_sync'); // Add logs
                if (!empty($all_products)) {
                  foreach ($all_products as $postvalue) {
                    $batch_count++;
                    array_push($values, esc_sql($postvalue), esc_sql($mc_key), esc_sql($mappedCat['id']), 1, gmdate('Y-m-d H:i:s', current_time('timestamp')));
                    $place_holders[] = "('%d', '%d', '%d','%d', '%s')";
                    if ($batch_count >= $product_db_batch_size) {
                      $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date) VALUES ";
                      $query .= implode(', ', $place_holders);
                      $wpdb->query($wpdb->prepare($query, $values));
                      $batch_count = 0;
                      $values = array();
                      $place_holders = array();
                    }
                  } //end product list loop
                } // end products if
              } // Pagination loop
            } //end category loop

            // Add products in database
            if ($batch_count > 0) {
              $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date) VALUES ";
              $query .= implode(', ', $place_holders);
              $wpdb->query($wpdb->prepare($query, $values));
            }
          }

          $ee_additional_data['is_mapping_update'] = false;
          $ee_additional_data['is_process_start'] = true;
          $ee_additional_data['product_sync_alert'] = "Product sync process is ready to start";
          $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
        }

        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        if (!empty($ee_additional_data) && isset($ee_additional_data['is_process_start']) && $ee_additional_data['is_process_start'] == true) {
          $TVC_Admin_Helper->plugin_log("Manual - product sync process start", 'product_sync');
          if (!class_exists('TVCProductSyncHelper')) {
            include(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php');
          }
          $TVCProductSyncHelper = new TVCProductSyncHelper();
          $response = $TVCProductSyncHelper->call_batch_wise_auto_sync_product();
          if (!empty($response) && isset($response['message'])) {
            $TVC_Admin_Helper->plugin_log("Manual - Batch wise auto sync process response " . $response['message'], 'product_sync');
          }

          $tablename = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
          $total_pending_pro = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) as a from {$wpdb->prefix}ee_prouct_pre_sync_data where `status` = 0"));
          if ($total_pending_pro == 0) {
            // Truncate pre sync table
            $TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);

            $ee_additional_data['is_process_start'] = false;
            $ee_additional_data['is_auto_sync_start'] = true;
            $ee_additional_data['product_sync_alert'] = NULL;
            $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
            $TVC_Admin_Helper->plugin_log("Manual - product sync process done", 'product_sync');
            as_unschedule_all_actions('init_product_sync_process_scheduler');
          } else {
            $TVC_Admin_Helper->plugin_log("Manual - recall product sync process for remaining " . $total_pending_pro . " products", 'product_sync');
          }
        } else {
          $TVC_Admin_Helper->plugin_log("Manual - Nothing to Sync", 'product_sync');
        }
        echo wp_json_encode(array('status' => 'success', "message" => esc_html__("Product sync process started successfully", "enhanced-e-commerce-for-woocommerce-store")));
        return true;
      } catch (Exception $e) {
        $ee_additional_data['product_sync_alert'] = $e->getMessage();
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
        $TVC_Admin_Helper->plugin_log("Manual - Error - " . $e->getMessage(), 'product_sync');
      }
      return true;
    }


    public function tvc_call_add_customer_featurereq()
    {
      if (isset($_POST['feature_req_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['feature_req_nonce'])), 'feature_req_nonce_val')) {
        $formdata = array();
        $formdata['feedback'] = isset($_POST['featurereq_message']) ? sanitize_text_field(wp_unslash($_POST['featurereq_message'])) : '';
        $formdata['subscription_id'] = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $customObj = new CustomApi();
        unset($_POST['action']);
        echo wp_json_encode($customObj->record_customer_featurereq($formdata));
        exit;
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      // IMPORTANT: don't forget to exit
      exit;
    }

    public function tvc_call_add_customer_feedback()
    {
      if (isset($_POST['conv_customer_feed_nonce_field']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['conv_customer_feed_nonce_field'])), 'conv_customer_feed_nonce_field_save')) {
        if (isset($_POST['que_one']) && isset($_POST['que_two']) && isset($_POST['que_three'])) {
          $formdata = array();
          $formdata['business_insights_index'] = sanitize_text_field(wp_unslash($_POST['que_one']));
          $formdata['automate_integrations_index'] = sanitize_text_field(wp_unslash($_POST['que_two']));
          $formdata['business_scalability_index'] = sanitize_text_field(wp_unslash($_POST['que_three']));
          $formdata['subscription_id'] = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
          $formdata['customer_id'] = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : "";
          $formdata['feedback'] = isset($_POST['feedback_description']) ? sanitize_text_field(wp_unslash($_POST['feedback_description'])) : "";
          $customObj = new CustomApi();
          unset($_POST['action']);
          echo wp_json_encode($customObj->record_customer_feedback($formdata));
          exit;
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Please answer the required questions", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      // IMPORTANT: don't forget to exit
      exit;
    }
    public function tvc_call_add_survey()
    {
      if (isset($_POST['tvc_call_add_survey']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tvc_call_add_survey'])), 'tvc_call_add_survey-nonce')) {
        if (!class_exists('CustomApi')) {
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
        }
        $customObj = new CustomApi();
        unset($_POST['action']);
        $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $customer_id = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : "";
        $radio_option_val = isset($_POST['radio_option_val']) ? sanitize_text_field(wp_unslash($_POST['radio_option_val'])) : "";
        $other_reason = isset($_POST['other_reason']) ? sanitize_text_field(wp_unslash($_POST['other_reason'])) : "";
        $site_url = isset($_POST['site_url']) ? sanitize_text_field(wp_unslash($_POST['site_url'])) : "";
        $plugin_name = isset($_POST['plugin_name']) ? sanitize_text_field(wp_unslash($_POST['plugin_name'])) : "";

        $post = array(
          "customer_id" => $customer_id,
          "subscription_id" => $subscription_id,
          "radio_option_val" => $radio_option_val,
          "other_reason" => $other_reason,
          "site_url" => $site_url,
          "plugin_name" => $plugin_name
        );
        echo wp_json_encode($customObj->add_survey_of_deactivate_plugin($post));
      } else {
        echo wp_json_encode(array('error' => true, "is_connect" => false, 'message' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      // IMPORTANT: don't forget to exit
      exit;
    }
    //active licence key
    public function tvc_call_active_licence()
    {
      if (isset($_POST['conv_licence_nonce']) && is_admin() && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['conv_licence_nonce'])), 'conv_lic_nonce')) {
        $licence_key = isset($_POST['licence_key']) ? sanitize_text_field(wp_unslash($_POST['licence_key'])) : "";
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $subscription_id = $TVC_Admin_Helper->get_subscriptionId();
        if ($subscription_id != "" && $licence_key != "") {
          $response = $TVC_Admin_Helper->active_licence($licence_key, $subscription_id);

          if ($response->error == false) {
            $TVC_Admin_Helper->update_subscription_details_api_to_db();
            echo wp_json_encode(array('error' => false, "is_connect" => true, 'message' => esc_html__("The licence key has been activated.", "enhanced-e-commerce-for-woocommerce-store")));
          } else {
            echo wp_json_encode(array('error' => true, "is_connect" => true, 'message' => $response->message));
          }
        } else if ($licence_key != "") {
          $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
          $ee_additional_data['temp_active_licence_key'] = $licence_key;
          $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
          echo wp_json_encode(array('error' => true, "is_connect" => false, 'message' => ""));
        } else {
          echo wp_json_encode(array('error' => true, "is_connect" => false, 'message' => esc_html__("Licence key is required.", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array('error' => true, "is_connect" => false, 'message' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }
    public function auto_product_sync_setting()
    {
      if (isset($_POST['auto_product_sync_setting']) && is_admin() && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['auto_product_sync_setting'])), 'auto_product_sync_setting-nonce')) {
        as_unschedule_all_actions('ee_auto_product_sync_check');
        $product_sync_duration = isset($_POST['product_sync_duration']) ? sanitize_text_field(wp_unslash($_POST['product_sync_duration'])) : "";
        $pro_snyc_time_limit = isset($_POST['pro_snyc_time_limit']) ? sanitize_text_field(wp_unslash($_POST['pro_snyc_time_limit'])) : "";
        $product_sync_batch_size = isset($_POST['product_sync_batch_size']) ? sanitize_text_field(wp_unslash($_POST['product_sync_batch_size'])) : "";
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        if ($product_sync_duration != "" && $pro_snyc_time_limit != "" && $product_sync_batch_size != "") {
          $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
          $ee_additional_data['product_sync_duration'] = $product_sync_duration;
          $ee_additional_data['pro_snyc_time_limit'] = $pro_snyc_time_limit;
          $ee_additional_data['product_sync_batch_size'] = $product_sync_batch_size;
          $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
          new TVC_Admin_Auto_Product_sync_Helper();
          echo wp_json_encode(array('error' => false, 'message' => esc_html__("Time interval and batch size successfully saved.", "enhanced-e-commerce-for-woocommerce-store")));
        } else {
          echo wp_json_encode(array('error' => true, 'message' => esc_html__("Error occured while saving the settings.", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array('error' => true, "is_connect" => false, 'message' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      // IMPORTANT: don't forget to exit
      exit;
    }

    public function con_get_conversion_list()
    {
      $nonce = isset($_POST['TVCNonce']) ? filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW) : '';

      if ($nonce && wp_verify_nonce($nonce, 'con_get_conversion_list-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $this->customApiObj = new CustomApi();
        $this->current_customer_id = $TVC_Admin_Helper->get_currentCustomerId();
        if ($this->current_customer_id != "") {
          $response = $this->customApiObj->get_conversion_list($this->current_customer_id);
          if (property_exists($response, "error") && $response->error == false) {
            if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
              $selected_conversio_send_to = get_option('ee_conversio_send_to');
              $conversion_label = array();
              foreach ($response->data as $key => $value) {
                $con_string = $value->tagSnippets;
                $conversion_label_check = $TVC_Admin_Helper->get_conversion_label($con_string);
                if ($conversion_label_check != "" && $conversion_label_check != null) {
                  $conversion_label[] = $TVC_Admin_Helper->get_conversion_label($con_string);
                }
              }
              echo wp_json_encode($conversion_label);
              exit;
            }
          }
        }
      }
      // IMPORTANT: don't forget to exit
      wp_die(0);
    }

    public function conv_get_microsoft_ads_conversion()
    {
      $nonce = isset($_POST['TVCNonce']) ? filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW) : '';

      if ($nonce && wp_verify_nonce($nonce, 'con_get_conversion_list-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $customApiObj = new CustomApi();
        $customer_id = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : '';
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '';
        $tag_id = isset($_POST['tag_id']) ? sanitize_text_field(wp_unslash($_POST['tag_id'])) : '';
        if ($customer_id != "") {
          $response = $customApiObj->get_microsoft_conversion_list($customer_id, $account_id, $tag_id);
          if (property_exists($response, "error") && $response->error == false) {
            if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
              echo wp_json_encode($response);
              exit;
            }
          }
        }
      }
      // IMPORTANT: don't forget to exit
      wp_die(0);
    }

    // public function conv_get_conversion_list_gads()
    // {
    //   if (isset($_POST['TVCNonce']) && $this->safe_ajax_call(filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW), 'con_get_conversion_list-nonce')) 
    //   {
    //     $TVC_Admin_Helper = new TVC_Admin_Helper();
    //     $this->customApiObj = new CustomApi();
    //     $current_customer_id = isset($_POST['gads_id']) ? sanitize_text_field(wp_unslash($_POST['gads_id'])) : '';
    //     if ($current_customer_id != "") {
    //       $response = $this->customApiObj->get_conversion_list($current_customer_id);
    //       if (property_exists($response, "error") && $response->error == false) {
    //         if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
    //           $selected_conversio_send_to = get_option('ee_conversio_send_to');
    //           $conversion_label = array();
    //           foreach ($response->data as $key => $value) {
    //             $con_string = $value->tagSnippets;
    //             $conversion_label_check = $TVC_Admin_Helper->get_conversion_label($con_string);
    //             if ($conversion_label_check != "" && $conversion_label_check != null) {
    //               $conversion_label[] = $TVC_Admin_Helper->get_conversion_label($con_string);
    //             }
    //           }
    //           echo wp_json_encode($conversion_label);
    //           exit;
    //         }
    //       }
    //     }
    //   }
    //   // IMPORTANT: don't forget to exit
    //   wp_die(0);
    // }

    public function conv_get_conversion_list_gads_bycat()
    {
      $nonce = isset($_POST['TVCNonce']) ? filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW) : '';

      if ($nonce && wp_verify_nonce($nonce, 'con_get_conversion_list-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $customApiObj = new CustomApi();

        $current_customer_id = isset($_POST['gads_id']) ? sanitize_text_field(wp_unslash($_POST['gads_id'])) : '';
        $conversionCategory = isset($_POST['conversionCategory']) ? sanitize_text_field(wp_unslash($_POST['conversionCategory'])) : '';

        if ($current_customer_id != "") {
          $response = $customApiObj->get_conversion_list($current_customer_id, $conversionCategory);
          if (property_exists($response, "error") && $response->error == false) {
            if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
              $selected_conversio_send_to = get_option('ee_conversio_send_to');
              $conversion_label = array();
              foreach ($response->data as $key => $value) {
                $con_string = $value->tagSnippets;
                $conversion_label_check = $TVC_Admin_Helper->get_conversion_label($con_string);
                if ($conversion_label_check != "" && $conversion_label_check != null) {
                  $conversion_label[$value->id . "(" . $value->name . ")"] = $TVC_Admin_Helper->get_conversion_label($con_string);
                }
              }
              echo wp_json_encode($conversion_label);
              exit;
            }
          }
        }
      }
      // IMPORTANT: don't forget to exit
      wp_die(0);
    }


    public function conv_create_gads_conversion()
    {
      $nonce = filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'con_get_conversion_list-nonce')) {        //$TVC_Admin_Helper = new TVC_Admin_Helper();
        $customApiObj = new CustomApi();

        $current_customer_id = isset($_POST['gads_id']) ? sanitize_text_field(wp_unslash($_POST['gads_id'])) : '';
        $conversionCategory = isset($_POST['conversionCategory']) ? sanitize_text_field(wp_unslash($_POST['conversionCategory'])) : '';
        $conversionName = isset($_POST['conversionName']) ? sanitize_text_field(wp_unslash($_POST['conversionName'])) : '';
        if ($current_customer_id != "") {
          $response = $customApiObj->conv_create_gads_conversion($current_customer_id, $conversionName, $conversionCategory);
          if (property_exists($response, "error") && $response->error == false) {
            if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
              echo wp_json_encode($response);
              exit;
            }
          }
        }
      }
    }

    public function conv_create_microsoft_ads_conversion()
    {
      $nonce = filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'con_get_conversion_list-nonce')) {        //$TVC_Admin_Helper = new TVC_Admin_Helper();
        $customApiObj = new CustomApi();
        $customer_id = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : '';
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '';
        $tag_id = isset($_POST['tag_id']) ? sanitize_text_field(wp_unslash($_POST['tag_id'])) : '';
        $conversionCategory = isset($_POST['conversionCategory']) ? sanitize_text_field(wp_unslash($_POST['conversionCategory'])) : '';
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $action_value = isset($_POST['action_value']) ? sanitize_text_field(wp_unslash($_POST['action_value'])) : '';
        if ($customer_id != "") {
          $response = $customApiObj->conv_create_microsoft_ads_conversion($customer_id, $account_id, $tag_id, $conversionCategory, $name, $action_value);
          if (property_exists($response, "error") && $response->error == false) {
            if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
              echo wp_json_encode($response);
              exit;
            }
          }
        }
      }
    }


    public function conv_save_gads_conversion()
    {
      $nonce = filter_input(INPUT_POST, 'CONVNonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_save_gads_conversion-nonce')) {
        $ee_options = unserialize(get_option('ee_options'));
        if (isset($_POST['cleargadsconversions']) && sanitize_text_field(wp_unslash($_POST['cleargadsconversions'])) == "yes") {
          unset($ee_options["gads_conversions"]);
          update_option('ee_options', serialize($ee_options));

          $google_ads_conversion_tracking = 0;
          $ga_EC = 0;
          $ee_conversio_send_to = "";

          update_option('google_ads_conversion_tracking', sanitize_text_field($google_ads_conversion_tracking));
          $googleDetail_setting["google_ads_conversion_tracking"] = sanitize_text_field($google_ads_conversion_tracking);

          update_option('ga_EC', sanitize_text_field($ga_EC));

          update_option('ee_conversio_send_to', sanitize_text_field($ee_conversio_send_to));
          $googleDetail_setting["ee_conversio_send_to"] = sanitize_text_field($ee_conversio_send_to);
        } else {
          $ee_options_gads_conversions = [];
          if (array_key_exists("gads_conversions", $ee_options)) {
            $ee_options_gads_conversions = $ee_options["gads_conversions"];
          }
          if (isset($_POST['conversion_category']) && isset($_POST['conversion_action'])) {
            $ee_options_gads_conversions[sanitize_text_field(wp_unslash($_POST['conversion_category']))] = sanitize_text_field(wp_unslash($_POST['conversion_action']));
          }
          $ee_options["gads_conversions"] = $ee_options_gads_conversions;
          update_option('ee_options', serialize($ee_options));

          if (isset($_POST['conversion_category']) && $_POST['conversion_category'] == "PURCHASE") {
            $google_ads_conversion_tracking = 1;
            $ga_EC = 1;
            $ee_conversio_send_to = sanitize_text_field(wp_unslash($_POST['conversion_action']));

            update_option('google_ads_conversion_tracking', sanitize_text_field($google_ads_conversion_tracking));
            $googleDetail_setting["google_ads_conversion_tracking"] = sanitize_text_field($google_ads_conversion_tracking);

            update_option('ga_EC', sanitize_text_field($ga_EC));

            update_option('ee_conversio_send_to', sanitize_text_field($ee_conversio_send_to));
            $googleDetail_setting["ee_conversio_send_to"] = sanitize_text_field($ee_conversio_send_to);
          }
        }
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->update_app_status();
        die('1');
      } else {
        die('Security nonce not matched');
      }
    }



    public function savemicrosoftadsconversions()
    {
      $nonce = filter_input(INPUT_POST, 'CONVNonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'conv_save_microsoft_ads_conversion-nonce')) {
        $ee_options = unserialize(get_option('ee_options'));
        if (isset($_POST['clearmicrosoftadsconversions']) && sanitize_text_field(wp_unslash($_POST['clearmicrosoftadsconversions'])) == "yes") {
          unset($ee_options["microsoft_ads_conversions"]);
          update_option('ee_options', serialize($ee_options));
          $microsoft_ads_conversions_tracking = 0;
          update_option('microsoft_ads_conversions_tracking', sanitize_text_field($microsoft_ads_conversions_tracking));
          $googleDetail_setting["microsoft_ads_conversions_tracking"] = sanitize_text_field($microsoft_ads_conversions_tracking);
        } else {
          $ee_options_microsoft_ads_conversions = $ee_options["microsoft_ads_conversions"] ?? [];

          if (!empty($_POST['category'])) {
            $categories = json_decode(stripslashes($_POST['category']), true);
            if (is_array($categories)) {
              foreach ($categories as $category => $value) {
                $cleanCategory = sanitize_text_field(wp_unslash($category));
                $ee_options_microsoft_ads_conversions[$cleanCategory] = "1";
              }
            }
          }

          $ee_options["microsoft_ads_conversions"] = $ee_options_microsoft_ads_conversions;
          update_option('ee_options', serialize($ee_options));

          if (!empty($ee_options_microsoft_ads_conversions)) {
            update_option('microsoft_ads_conversions_tracking', "1");
          }
        }
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->update_app_status();
        die('1');
      } else {
        die('Security nonce not matched');
      }
    }
    // public function tvc_call_notification_dismiss()
    // {
    //   if (isset($_POST['TVCNonce']) && $this->safe_ajax_call(filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW), 'tvc_call_notification_dismiss-nonce')) {
    //     $ee_dismiss_id = isset($_POST['data']['ee_dismiss_id']) ? sanitize_text_field(wp_unslash($_POST['data']['ee_dismiss_id'])) : "";
    //     if ($ee_dismiss_id != "") {
    //       $TVC_Admin_Helper = new TVC_Admin_Helper();
    //       $ee_msg_list = $TVC_Admin_Helper->get_ee_msg_nofification_list();
    //       if (isset($ee_msg_list[$ee_dismiss_id])) {
    //         unset($ee_msg_list[$ee_dismiss_id]);
    //         $ee_msg_list[$ee_dismiss_id]["active"] = 0;
    //         $TVC_Admin_Helper->set_ee_msg_nofification_list($ee_msg_list);
    //         echo wp_json_encode(array('status' => 'success', 'message' => ""));
    //       }
    //     }
    //   } else {
    //     echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
    //   }
    //   // IMPORTANT: don't forget to exit
    //   exit;
    // }
    // public function tvc_call_notice_dismiss()
    // {
    //   if (isset($_POST['apiNoticDismissNonce']) && $this->safe_ajax_call(filter_input(INPUT_POST, 'apiNoticDismissNonce', FILTER_UNSAFE_RAW), 'tvc_call_notice_dismiss-nonce')) {
    //     $ee_notice_dismiss_id = isset($_POST['data']['ee_notice_dismiss_id']) ? sanitize_text_field(wp_unslash($_POST['data']['ee_notice_dismiss_id'])) : "";
    //     $ee_notice_dismiss_id = sanitize_text_field($ee_notice_dismiss_id);
    //     if ($ee_notice_dismiss_id != "") {
    //       $TVC_Admin_Helper = new TVC_Admin_Helper();
    //       $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
    //       $ee_additional_data['dismissed_' . $ee_notice_dismiss_id] = 1;
    //       $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
    //       echo wp_json_encode(array('status' => 'success', 'message' => $ee_additional_data));
    //     }
    //   } else {
    //     echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
    //   }
    //   // IMPORTANT: don't forget to exit
    //   exit;
    // }

    // public function tvc_call_notice_dismiss_trigger()
    // {
    //   if (isset($_POST['apiNoticDismissNonce']) && $this->safe_ajax_call(filter_input(INPUT_POST, 'apiNoticDismissNonce', FILTER_UNSAFE_RAW), 'tvc_call_notice_dismiss-nonce')) {
    //     $ee_notice_dismiss_id_trigger = isset($_POST['data']['ee_notice_dismiss_id_trigger']) ? sanitize_text_field(wp_unslash($_POST['data']['ee_notice_dismiss_id_trigger'])) : "";
    //     $ee_notice_dismiss_id_trigger = sanitize_text_field($ee_notice_dismiss_id_trigger);
    //     if ($ee_notice_dismiss_id_trigger != "") {
    //       $TVC_Admin_Helper = new TVC_Admin_Helper();
    //       $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
    //       $slug = $ee_notice_dismiss_id_trigger;
    //       $title = "";
    //       $content = "";
    //       $status = "0";
    //       $TVC_Admin_Helper->tvc_dismiss_admin_notice($slug, $content, $status, $title);
    //     }
    //   } else {
    //     echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
    //   }
    //   // IMPORTANT: don't forget to exit
    //   exit;
    // }
    // public function tvc_call_import_gmc_product()
    // {
    //   if (isset($_POST['apiSyncupNonce']) && $this->safe_ajax_call(filter_input(INPUT_POST, 'apiSyncupNonce', FILTER_UNSAFE_RAW), 'tvc_call_api_sync-nonce')) {
    //     $next_page_token = isset($_POST['next_page_token']) ? sanitize_text_field(wp_unslash($_POST['next_page_token'])) : "";
    //     $TVC_Admin_Helper = new TVC_Admin_Helper();
    //     $api_rs = $TVC_Admin_Helper->update_gmc_product_to_db($next_page_token);
    //     if (isset($api_rs['error'])) {
    //       echo wp_json_encode($api_rs);
    //     } else {
    //       echo wp_json_encode(array('error' => true, 'message' => esc_html__("Please try after some time.", "enhanced-e-commerce-for-woocommerce-store")));
    //     }
    //   } else {
    //     echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
    //   }
    //   // IMPORTANT: don't forget to exit
    //   exit;
    // }
    // public function tvc_call_api_sync()
    // {
    //   if (isset($_POST['apiSyncupNonce']) && $this->safe_ajax_call(filter_input(INPUT_POST, 'apiSyncupNonce', FILTER_UNSAFE_RAW), 'tvc_call_api_sync-nonce')) {
    //     $TVC_Admin_Helper = new TVC_Admin_Helper();
    //     $api_rs = $TVC_Admin_Helper->set_update_api_to_db();
    //     if (isset($api_rs['error']) && isset($api_rs['message']) && sanitize_text_field($api_rs['message'])) {
    //       echo wp_json_encode($api_rs);
    //     } else {
    //       echo wp_json_encode(array('error' => true, 'message' => esc_html__("Please try after some time.", "enhanced-e-commerce-for-woocommerce-store")));
    //     }
    //   } else {
    //     echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
    //   }
    //   // IMPORTANT: don't forget to exit
    //   exit;
    // }
    public function tvc_call_site_verified()
    {
      $nonce = filter_input(INPUT_POST, 'SiteVerifiedNonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'tvc_call_site_verified-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $tvc_rs = [];
        $tvc_rs = $TVC_Admin_Helper->call_site_verified();
        if (isset($tvc_rs['error']) && $tvc_rs['error'] == 1) {
          echo wp_json_encode(array('status' => 'error', 'message' => sanitize_text_field($tvc_rs['msg'])));
        } else {
          echo wp_json_encode(array('status' => 'success', 'message' => sanitize_text_field($tvc_rs['msg'])));
        }
        exit;
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        exit;
      }
    }
    public function tvc_call_domain_claim()
    {
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'apiDomainClaimNonce', FILTER_UNSAFE_RAW), 'tvc_call_domain_claim-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $tvc_rs = $TVC_Admin_Helper->call_domain_claim();
        if (isset($tvc_rs['error']) && $tvc_rs['error'] == 1) {
          echo wp_json_encode(array('status' => 'error', 'message' => sanitize_text_field($tvc_rs['msg'])));
        } else {
          echo wp_json_encode(array('status' => 'success', 'message' => sanitize_text_field($tvc_rs['msg'])));
        }
        exit;
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        exit;
      }
    }

    /**
     * Update business details
     */
    public function update_business_details()
    {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : '';

      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $users = isset($_POST['users']) ? json_decode(stripslashes($_POST['users']), true) : [];
        $address = isset($_POST['address']) ? json_decode(stripslashes($_POST['address']), true) : [];
        $customerService = isset($_POST['customerService']) ? json_decode(stripslashes($_POST['customerService']), true) : [];

        $users_sanitized = [];
        if (!empty($users) && is_array($users)) {
          foreach ($users as $user) {
            // Accept both 'email' or 'emailAddress'
            $email = !empty($user['emailAddress']) ? $user['emailAddress'] : ($user['email'] ?? '');
            if (!empty($email)) {
              $users_sanitized[] = [
                'emailAddress' => sanitize_email($email),
                'admin' => isset($user['admin']) ? (bool)$user['admin'] : false,
                'reportingManager' => isset($user['reportingManager']) ? (bool)$user['reportingManager'] : false,
              ];
            }
          }
        }


        if (empty($users_sanitized)) {
          wp_send_json_error(['message' => esc_html__('No valid users provided.', 'enhanced-e-commerce-for-woocommerce-store')]);
          exit;
        }

        $CustomApi = new CustomApi();
        $response = $CustomApi->update_gmc_business_info([
          'store_id' => sanitize_text_field($_POST['store_id']),
          'subscription_id' => sanitize_text_field($_POST['subscription_id']),
          'account_id' => sanitize_text_field($_POST['account_id']),
          'name' => sanitize_text_field($_POST['name']),
          'phoneNumber' => sanitize_text_field($_POST['phoneNumber']),
          'users' => $users_sanitized,
          'address' => [
            'streetAddress' => sanitize_text_field($address['streetAddress']),
            'locality' => sanitize_text_field($address['locality']),
            'region' => sanitize_text_field($address['region']),
            'postalCode' => sanitize_text_field($address['postalCode']),
            'country' => sanitize_text_field($address['country']),
          ],
          'customerService' => [
            'email' => sanitize_email($customerService['email']),
            'phoneNumber' => sanitize_text_field($customerService['phoneNumber']),
            'url' => esc_url_raw($customerService['url']),
          ]
        ]);

        if ($response && !isset($response->error)) {
          wp_send_json_success(['message' => esc_html__('Business details updated successfully', 'enhanced-e-commerce-for-woocommerce-store')]);
        } else {
          $error_message = isset($response->message) ? $response->message : esc_html__('Error updating business details', 'enhanced-e-commerce-for-woocommerce-store');
          wp_send_json_error(['message' => $error_message]);
        }
      } else {
        wp_send_json_error(['message' => esc_html__('Security check failed', 'enhanced-e-commerce-for-woocommerce-store')]);
      }

      exit;
    }


    public function tvcajax_delete_campaign()
    {
      // make sure this call is legal
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignDeleteNonce', FILTER_UNSAFE_RAW), 'tvcajax_delete_campaign-nonce')) {

        $merchantId = filter_input(INPUT_POST, 'merchantId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $customerId = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $campaignId = filter_input(INPUT_POST, 'campaignId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $url = $this->apiDomain . '/campaigns/delete';
        $data = [
          'merchant_id' => sanitize_text_field($merchantId),
          'customer_id' => sanitize_text_field($customerId),
          'campaign_id' => sanitize_text_field($campaignId)
        ];
        $args = array(
          'headers' => array(
            'Authorization' => "Bearer MTIzNA==",
            'Content-Type' => 'application/json'
          ),
          'method' => 'DELETE',
          'body' => wp_json_encode($data)
        );
        // Send remote request
        $request = wp_remote_request(esc_url_raw($url), $args);

        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);
        $response_message = wp_remote_retrieve_response_message($request);
        $response_body = json_decode(wp_remote_retrieve_body($request));

        if ((isset($response_body->error) && $response_body->error == '')) {
          $message = $response_body->message;
          echo wp_json_encode(['status' => 'success', 'message' => $message]);
        } else {
          $message = is_array($response_body->errors) ? $response_body->errors[0] : "Face some unprocessable entity";
          echo wp_json_encode(['status' => 'error', 'message' => $message]);
          // return new WP_Error($response_code, $response_message, $response_body);
        }
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      // IMPORTANT: don't forget to exit
      exit;
    }

    /**
     * Update the campaign status pause/active
     */
    public function tvcajax_update_campaign_status()
    {
      // make sure this call is legal
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignStatusNonce', FILTER_UNSAFE_RAW), 'tvcajax-update-campaign-status-nonce')) {
        if (!class_exists('ShoppingApi')) {
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/ShoppingApi.php');
        }

        $header = array(
          "Authorization: Bearer MTIzNA==",
          "Content-Type" => "application/json"
        );

        $merchantId = filter_input(INPUT_POST, 'merchantId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $customerId = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $campaignId = filter_input(INPUT_POST, 'campaignId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $budgetId = filter_input(INPUT_POST, 'budgetId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $campaignName = filter_input(INPUT_POST, 'campaignName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $budget = filter_input(INPUT_POST, 'budget', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $curl_url = $this->apiDomain . '/campaigns/update';
        $shoppingObj = new ShoppingApi();
        $campaignData = $shoppingObj->getCampaignDetails($campaignId);

        $data = [
          'merchant_id' => sanitize_text_field($merchantId),
          'customer_id' => sanitize_text_field($customerId),
          'campaign_id' => sanitize_text_field($campaignId),
          'account_budget_id' => sanitize_text_field($budgetId),
          'campaign_name' => sanitize_text_field($campaignName),
          'budget' => sanitize_text_field($budget),
          'status' => sanitize_text_field($status),
          'target_country' => sanitize_text_field($campaignData->data['data']->targetCountry),
          'ad_group_id' => sanitize_text_field($campaignData->data['data']->adGroupId),
          'ad_group_resource_name' => sanitize_text_field($campaignData->data['data']->adGroupResourceName)
        ];

        $args = array(
          'headers' => $header,
          'method' => 'PATCH',
          'body' => wp_json_encode($data)
        );
        $request = wp_remote_request(esc_url_raw($curl_url), $args);
        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);
        $response_message = wp_remote_retrieve_response_message($request);
        $response = json_decode(wp_remote_retrieve_body($request));
        if (isset($response->error) && $response->error == false) {
          $message = $response->message;
          echo wp_json_encode(['status' => 'success', 'message' => $message]);
        } else {
          $message = is_array($response->errors) ? $response->errors[0] : esc_html__("Face some unprocessable entity", "enhanced-e-commerce-for-woocommerce-store");
          echo wp_json_encode(['status' => 'error', 'message' => $message]);
        }
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      // IMPORTANT: don't forget to exit
      exit;
    }

    /**
     * Returns the campaign categories from a selected country
     */
    public function tvcajax_get_campaign_categories()
    {
      // make sure this call is legal
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignCategoryListsNonce', FILTER_UNSAFE_RAW), 'tvcajax-campaign-category-lists-nonce')) {

        $country_code = filter_input(INPUT_POST, 'countryCode', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $customer_id = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $url = $this->apiDomain . '/products/categories';

        $data = [
          'customer_id' => sanitize_text_field($customer_id),
          'country_code' => sanitize_text_field($country_code)
        ];

        $args = array(
          'headers' => array(
            'Authorization' => "Bearer MTIzNA==",
            'Content-Type' => 'application/json'
          ),
          'body' => wp_json_encode($data)
        );

        // Send remote request
        $request = wp_remote_post(esc_url_raw($url), $args);

        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);
        $response_message = wp_remote_retrieve_response_message($request);
        $response_body = json_decode(wp_remote_retrieve_body($request));

        if ((isset($response_body->error) && $response_body->error == '')) {
          echo wp_json_encode($response_body->data);
        } else {
          echo wp_json_encode([]);
        }
      }
      // IMPORTANT: don't forget to exit
      exit;
    }

    /**
     * Returns the campaign categories from a selected country
     */
    public function tvcajax_get_gmc_categories()
    {
      // make sure this call is legal
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'gmcCategoryListsNonce', FILTER_UNSAFE_RAW), 'tvcajax-gmc-category-lists-nonce')) {

        $country_code = filter_input(INPUT_POST, 'countryCode', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $customer_id = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $parent = filter_input(INPUT_POST, 'parent', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $url = $this->apiDomain . '/products/gmc-categories';

        $data = [
          'customer_id' => sanitize_text_field($customer_id),
          'country_code' => sanitize_text_field($country_code),
          'parent' => sanitize_text_field($parent)
        ];

        $args = array(
          'headers' => array(
            'Authorization' => "Bearer MTIzNA==",
            'Content-Type' => 'application/json'
          ),
          'body' => wp_json_encode($data)
        );

        // Send remote request
        $request = wp_remote_post(esc_url_raw($url), $args);

        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);
        $response_message = wp_remote_retrieve_response_message($request);
        $response_body = json_decode(wp_remote_retrieve_body($request));

        if ((isset($response_body->error) && $response_body->error == '')) {
          echo wp_json_encode($response_body->data);
        } else {
          echo wp_json_encode([]);
        }

        //   echo wp_json_encode( $categories );
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        exit;
      }

      // IMPORTANT: don't forget to exit
      exit;
    }

    /**
     * function to Save Category Mapping
     * Hook used wp_ajax_save_category_mapping
     * Request Post
     * DB used ee_prod_mapped_cats
     */
    public function save_category_mapping()
    {
      if (is_admin() && wp_verify_nonce(filter_input(INPUT_POST, 'auto_product_sync_setting', FILTER_SANITIZE_FULL_SPECIAL_CHARS), 'auto_product_sync_setting-nonce')) {
        $data = isset($_POST['ee_data']) ? wp_unslash($_POST['ee_data']) : '';
        if (isset($data) && !empty($data)) {
          wp_parse_str($data, $formArray);
        } else {
          $formArray = array();
        }
        if (!empty($formArray)) {
          foreach ($formArray as $key => $value) {
            $formArray[$key] = $value;
          }

          foreach ($formArray as $key => $value) {
            if (preg_match("/^category-name-/i", $key)) {
              if ($value != '') {
                $keyArray = explode("name-", $key);
                $mappedCatsDB[$keyArray[1]]['name'] = $value;
              }
              unset($formArray[$key]);
            } else if (preg_match("/^category-/i", $key)) {
              if ($value != '' && $value > 0) {
                $keyArray = explode("-", $key);
                $mappedCats[$keyArray[1]] = $value;
                $mappedCatsDB[$keyArray[1]]['id'] = $value;
              }
              unset($formArray[$key]);
            }
          }
          $categories = unserialize(get_option('ee_prod_mapped_cats'));
          $countCategories = is_array($categories) ? count($categories) : 0;
          update_option("ee_prod_mapped_cats", serialize($mappedCatsDB));

          if ($countCategories == 0) {
            $customObj = new CustomApi();
            $customObj->update_app_status();
          }

          echo wp_json_encode(array('error' => false, 'message' => esc_html__("Category Mapping successfully saved.", "enhanced-e-commerce-for-woocommerce-store")));
        } else {
          echo wp_json_encode(array('error' => true, 'message' => esc_html__("Error!!! No Category selected.", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to Save Attribute Mapping
     * Hook used wp_ajax_ssave_attribute_mapping
     * Request Post
     * DB used ee_prod_mapped_attrs
     */
    public function save_attribute_mapping()
    {
      if (isset($_POST['auto_product_sync_setting']) && is_admin() && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['auto_product_sync_setting'])), 'auto_product_sync_setting-nonce')) {
        $data = isset($_POST['ee_data']) ? sanitize_text_field(urldecode(wp_unslash($_POST['ee_data']))) : "";
        wp_parse_str($data, $formArray);
        if (!empty($formArray)) {
          foreach ($formArray as $key => $value) {
            if ($key == 'additional_attr_') {
              $additional_attr = $value;
              unset($formArray['additional_attr_']);
            }
            if ($key == 'additional_attr_value_') {
              $additional_attr_value = $value;
              unset($formArray['additional_attr_value_']);
            }
            if (is_array($value) !== 1) {
              $formArray[$key] = sanitize_text_field($value);
            }
          }
          unset($formArray['additional_attr_']);
          unset($formArray['additional_attr_value_']);
          if (isset($additional_attr)) {
            foreach ($additional_attr as $key => $value) {
              $formArray[$value] = $additional_attr_value[$key];
            }
          }
          foreach ($formArray as $key => $value) {
            $mappedAttrs[$key] = sanitize_text_field($value);
          }
          $attributes = unserialize(get_option('ee_prod_mapped_attrs'));
          $countAttribute = is_array($attributes) ? count($attributes) : 0;
          //If cnt 
          unset($mappedAttrs['cnt']);
          update_option("ee_prod_mapped_attrs", serialize($mappedAttrs));

          if ($countAttribute == 0) {
            $customObj = new CustomApi();
            $customObj->update_app_status();
          }

          echo wp_json_encode(array('error' => false, 'message' => esc_html__("Attribute Mapping successfully saved.", "enhanced-e-commerce-for-woocommerce-store")));
        } else {
          echo wp_json_encode(array('error' => true, 'message' => esc_html__("Error!!! No Attribute selected.", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to get Product status by feed_id
     * Hook used wp_ajax_ee_get_product_status
     * Request Post
     * API call to get product status
     */
    public function ee_get_product_status()
    {
      $nonce = filter_input(INPUT_POST, 'conv_licence_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_licence-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        $merchantId = $TVC_Admin_Helper->get_merchantId();
        $data = array(
          "store_id" => $google_detail['setting']->store_id,
          "subscription_id" => $google_detail['setting']->id,
          "store_feed_id" => isset($_POST['feed_id']) ? sanitize_text_field(wp_unslash($_POST['feed_id'])) : '',
          "product_ids" => isset($_POST['product_list']) ? sanitize_text_field(wp_unslash($_POST['product_list'])) : '',
          "channel" => isset($_POST['channel_id']) ? sanitize_text_field(wp_unslash($_POST['channel_id'])) : '',
          "merchant_id" => $google_detail['setting']->google_merchant_id,
          "catalog_id" => isset($_POST['catalog_id']) ? sanitize_text_field(wp_unslash($_POST['catalog_id'])) : '',
          "tiktok_business_id" => isset($_POST['tiktok_business_id']) ? sanitize_text_field(wp_unslash($_POST['tiktok_business_id'])) : '',
          "tiktok_catalog_id" => isset($_POST['tiktok_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id'])) : '',
          "ms_store_id" => isset($_POST['ms_store_id']) ? sanitize_text_field(wp_unslash($_POST['ms_store_id'])) : '',
          "ms_catalog_id" => isset($_POST['ms_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['ms_catalog_id'])) : '',
        );
        if (!isset($_POST['product_list']) || (isset($_POST['product_list']) && sanitize_text_field(wp_unslash($_POST['product_list']))) == '') {
          echo wp_json_encode('Product does not exists');
          exit;
        }
        $CustomApi = new CustomApi();
        // $response = $CustomApi->getProductStatusByFeedId($data); 
        $response = $CustomApi->getProductStatusByChannelId($data);
        if (isset($response->errors)) {
          echo wp_json_encode($response->errors = 'Product does not exists');
        } else {
          echo wp_json_encode(isset($response->data->products) ? $response->data->products : 'Product not synced');
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to Save and Update Feed data
     * Hook used wp_ajax_save_feed_data
     * Request Post
     * DB used ee_product_feed
     * Schedule cron set_recurring_auto_sync_product_feed_wise on update for conditions
     */
    public function save_feed_data()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        $channel_id = array();
        if (isset($_POST['google_merchant_center']) && $_POST['google_merchant_center'] == 1) {
          $channel_id['google_merchant_center'] = sanitize_text_field(wp_unslash($_POST['google_merchant_center']));
        }
        if (isset($_POST['tiktok_id']) && sanitize_text_field(wp_unslash($_POST['tiktok_id'])) == 3) {
          $channel_id['tiktok_id'] = sanitize_text_field(wp_unslash($_POST['tiktok_id']));
        }
        if (isset($_POST['fb_catalog_id']) && $_POST['fb_catalog_id'] == 2) {
          $channel_id['fb_catalog_id'] = sanitize_text_field(wp_unslash($_POST['fb_catalog_id']));
        }
        if (isset($_POST['microsoft_merchant_center']) && $_POST['microsoft_merchant_center'] == 4) {
          $channel_id['microsoft_merchant_center'] = sanitize_text_field(wp_unslash($_POST['microsoft_merchant_center']));
        }

        $channel_ids = implode(',', $channel_id);

        $tiktok_catalog_id = '';
        if (isset($_POST['tiktok_catalog_id']) === TRUE && $_POST['tiktok_catalog_id'] !== '') {
          $tiktok_catalog_id = sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id']));
        }
        /**
         * Check catalog id available
         */
        if (isset($_POST['tiktok_catalog_id']) === TRUE && sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id'])) === 'Create New') {
          /**
           * Create catalog id
           */
          //$getCountris = @file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries_currency.json");

          global $wp_filesystem;
          $getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries_currency.json");

          $contData = json_decode($getCountris);
          $currency_code = '';
          foreach ($contData as $key => $data) {
            if (isset($_POST['target_country']) && $data->countryCode === $_POST['target_country']) {
              $currency_code = $data->currencyCode;
            }
          }
          $customer['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer['business_id'] = isset($_POST['tiktok_business_account']) ? sanitize_text_field(wp_unslash($_POST['tiktok_business_account'])) : '';
          $customer['catalog_name'] = isset($_POST['feedName']) ? sanitize_text_field(wp_unslash($_POST['feedName'])) : '';
          $customer['region_code'] = isset($_POST['target_country']) ? sanitize_text_field(wp_unslash($_POST['target_country'])) : '';
          $customer['currency'] = sanitize_text_field($currency_code);
          $customObj = new CustomApi();
          $result = $customObj->createCatalogs($customer);
          if (isset($result->error_data) === TRUE) {
            foreach ($result->error_data as $key => $value) {
              echo wp_json_encode(array("error" => true, "message" => $value->errors[0], "errorType" => "tiktok"));
              exit;
            }
          }

          if (isset($result->status) === TRUE && $result->status === 200) {
            $tiktok_catalog_id = $result->data->catalog_id;
            $values = array();
            $place_holders = array();
            global $wpdb;
            $ee_tiktok_catalog = esc_sql($wpdb->prefix . "ee_tiktok_catalog");
            if (isset($_POST['target_country']) && isset($tiktok_catalog_id) && isset($_POST['feedName'])) {
              array_push($values, esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))), esc_sql($tiktok_catalog_id), esc_sql(sanitize_text_field(wp_unslash($_POST['feedName']))), gmdate('Y-m-d H:i:s', current_time('timestamp')));
            }
            $place_holders[] = "('%s', '%s', '%s','%s')";
            $query = "INSERT INTO `$ee_tiktok_catalog` (country, catalog_id, catalog_name, created_date) VALUES ";
            $query .= implode(', ', $place_holders);
            $wpdb->query($wpdb->prepare($query, $values));

            /***Store Catalog data Middleware *****/
            //$this->storeNewCatalogMiddleware();
          }
        }

        if (isset($_POST['edit']) && $_POST['edit'] != '') {
          $next_schedule_date = NULL;
          as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['edit']))));
          if (isset($_POST['autoSync']) && isset($_POST['is_mapping_update']) && $_POST['autoSync'] != 0 && $_POST['is_mapping_update'] == 1) {
            $last_sync_date = isset($_POST['last_sync_date']) ? sanitize_text_field(wp_unslash($_POST['last_sync_date'])) : '';
            $next_schedule_date = gmdate('Y-m-d H:i:s', strtotime('+' . (isset($_POST['autoSyncIntvl']) ? absint(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : 0) . 'day', strtotime($last_sync_date)));
            // add scheduled cron job
            $autoSyncIntvl = isset($_POST['autoSyncIntvl']) ? absint(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : 0;
            $time_space = strtotime($autoSyncIntvl . " days", 0);
            $timestamp = strtotime($autoSyncIntvl . " days");
            as_schedule_recurring_action(esc_attr($timestamp), esc_attr($time_space), 'init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['edit']))), "product_sync");
          }
          $profile_data = array(
            'feed_name' => isset($_POST['feedName']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['feedName']))) : '',
            'channel_ids' => isset($channel_ids) ? esc_sql(sanitize_text_field($channel_ids)) : '',
            'auto_sync_interval' => isset($_POST['autoSyncIntvl']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : '',
            'auto_schedule' => isset($_POST['autoSync']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSync']))) : '',
            'updated_date' => esc_sql(gmdate('Y-m-d H:i:s', current_time('timestamp'))),
            'next_schedule_date' => $next_schedule_date,
            'target_country' => isset($_POST['target_country']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))) : '',
            'tiktok_catalog_id' => isset($tiktok_catalog_id) ? esc_sql(sanitize_text_field($tiktok_catalog_id)) : ''
          );

          if (isset($_POST['is_mapping_update']) && $_POST['is_mapping_update'] != 1) {
            $profile_data['status'] = strpos($channel_ids, '1') !== false ? esc_sql('Draft') : '';
            $profile_data['fb_status'] = strpos($channel_ids, '2') !== false ? esc_sql('Draft') : '';
            $profile_data['tiktok_status'] = strpos($channel_ids, '3') !== false ? esc_sql('Draft') : '';
            $profile_data['ms_status'] = strpos($channel_ids, '4') !== false ? esc_sql('Draft') : '';
          }
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => sanitize_text_field(wp_unslash($_POST['edit']))));
          $result = array(
            'id' => sanitize_text_field(wp_unslash($_POST['edit'])),
          );
          echo wp_json_encode($result);
        } else {
          $profile_data = array(
            'feed_name' => isset($_POST['feedName']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['feedName']))) : '',
            'channel_ids' => isset($channel_ids) ? esc_sql(sanitize_text_field($channel_ids)) : '',
            'auto_sync_interval' => isset($_POST['autoSyncIntvl']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : '',
            'auto_schedule' => isset($_POST['autoSync']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSync']))) : '',
            'created_date' => esc_sql(gmdate('Y-m-d H:i:s', current_time('timestamp'))),
            'status' => isset($channel_ids) && strpos(sanitize_text_field($channel_ids), '1') !== false ? esc_sql('Draft') : '',
            'target_country' => isset($_POST['target_country']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))) : '',
            'tiktok_catalog_id' => isset($tiktok_catalog_id) ? esc_sql(sanitize_text_field($tiktok_catalog_id)) : '',
            'fb_status' => isset($channel_ids) && strpos($channel_ids, '2') !== false ? esc_sql('Draft') : '',
            'tiktok_status' => isset($channel_ids) && strpos(sanitize_text_field($channel_ids), '3') !== false ? esc_sql('Draft') : '',
            'ms_status' => isset($channel_ids) && strpos($channel_ids, '4') !== false ? esc_sql('Draft') : '',
          );
          $TVC_Admin_DB_Helper->tvc_add_row("ee_product_feed", $profile_data, array("%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s"));
          $result = $TVC_Admin_DB_Helper->tvc_get_last_row("ee_product_feed", array("id"));
          echo wp_json_encode($result);
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to get Feed data by id
     * Hook used wp_ajax_get_feed_data_by_id
     * Request Post
     * DB used ee_product_feed
     */
    public function get_feed_data_by_id()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        if (isset($_POST['id'])) {
          $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['id'])));
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Id is missing.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        $filed = array(
          'id',
          'feed_name',
          'channel_ids',
          'auto_sync_interval',
          'auto_schedule',
          'status',
          'is_mapping_update',
          'last_sync_date',
          'target_country',
          'tiktok_catalog_id',
        );
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        echo wp_json_encode($result);
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to Duplicate Feed data by id
     * Hook used wp_ajax_ee_duplicate_feed_data_by_id
     * Request Post
     * DB used ee_product_feed
     */
    public function ee_duplicate_feed_data_by_id()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        if (isset($_POST['id'])) {
          $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['id'])));
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Id is missing.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        $filed = array(
          'feed_name',
          'channel_ids',
          'auto_sync_interval',
          'auto_schedule',
          'categories',
          'attributes',
          'filters',
          'include_product',
          'exclude_product',
          'total_product',
          'target_country',
          'tiktok_catalog_id',
        );
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        $profile_data = array(
          'feed_name' => esc_sql('Copy of - ' . $result[0]['feed_name']),
          'channel_ids' => esc_sql($result[0]['channel_ids']),
          'auto_sync_interval' => esc_sql($result[0]['auto_sync_interval']),
          'auto_schedule' => esc_sql($result[0]['auto_schedule']),
          'filters' => $result[0]['filters'],
          'include_product' => esc_sql($result[0]['include_product']),
          'exclude_product' => esc_sql($result[0]['exclude_product']),
          'created_date' => esc_sql(gmdate('Y-m-d H:i:s', current_time('timestamp'))),
          'status' => esc_sql('Draft'),
          'target_country' => esc_sql($result[0]['target_country']),
          'tiktok_catalog_id' => esc_sql($result[0]['tiktok_catalog_id']),
          'tiktok_status' => strpos($result[0]['channel_ids'], '3') !== false ? esc_sql('Draft') : '',
        );

        $TVC_Admin_DB_Helper->tvc_add_row("ee_product_feed", $profile_data, array("%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s"));
        echo wp_json_encode(array("error" => false, "message" => esc_html__("Duplicate Feed created successfully", "enhanced-e-commerce-for-woocommerce-store")));
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to Delete Feed and product from GMC
     * Hook used wp_ajax_ee_delete_feed_data_by_id
     * Request Post
     * DB used ee_product_feed
     * Delete by id
     * Unschedule set_recurring_auto_sync_product_feed_wise cron 
     * Api Call to delete product from GMC 
     */
    public function ee_delete_feed_data_by_id()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        if (isset($_POST['id'])) {
          $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['id'])));
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Id is missing.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        $filed = array('exclude_product', 'status', 'include_product', 'tiktok_status', 'fb_status', 'ms_status', 'is_mapping_update');
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        // if ($result[0]['status'] === 'Synced' || $result[0]['tiktok_status'] === 'Synced' || $result[0]['fb_status'] === 'Synced' || $result[0]['ms_status'] === 'Synced' ) {
        if (isset($_POST['id'])) {
          as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['id']))));
        }
        /**
         * Api call to delete GMC product
         */
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        $merchantId = $TVC_Admin_Helper->get_merchantId();
        $data = array(
          "merchant_id" => $merchantId,
          "store_id" => $google_detail['setting']->store_id,
          "store_feed_id" => (isset($_POST['id']) ? sanitize_text_field(wp_unslash($_POST['id'])) : ''),
          "product_ids" => ''
        );
        $CustomApi = new CustomApi();
        $response = $CustomApi->delete_from_channels($data);
        $TVC_Admin_Helper->plugin_log("Delete Feed from GMC" . wp_json_encode($response), 'product_sync');
        // }
        $soft_delete_id = array('status' => 'Deleted', 'tiktok_status' => 'Deleted', 'fb_status' => 'Deleted', 'ms_status' => 'Deleted', 'is_delete' => esc_sql(1), 'auto_schedule' => 0);
        if (isset($_POST['id'])) {
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $soft_delete_id, array("id" => sanitize_text_field(wp_unslash($_POST['id']))));
        }
        echo wp_json_encode(array("error" => false, "message" => esc_html__("Feed Deleted Successfully.", "enhanced-e-commerce-for-woocommerce-store")));
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to delete Product by product id from GMC
     * Hook used wp_ajax_ee_delete_feed_gmc
     * DB used ee_product_feed
     * Request Post product id and feedId
     * Api Call to delete product from GMC
     */
    public function ee_delete_feed_gmc()
    {
      if (isset($_POST['conv_onboarding_nonce']) && wp_verify_nonce($_POST['conv_onboarding_nonce'], 'conv_onboarding_nonce')) {
        $CONV_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        $where = 'id = ' . esc_sql(sanitize_text_field($_POST['feed_id']));
        $filed = array('exclude_product', 'status', 'include_product', 'total_product', 'product_id_prefix', 'tiktok_catalog_id');
        $result = $CONV_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        $totProdRem = $result[0]['total_product'] - 1;
        if ($result[0]['exclude_product'] != '' && $_POST['product_ids'] != '') {
          $allExclude = $result[0]['exclude_product'] . ',' . trim(str_replace($result[0]['product_id_prefix'], '', sanitize_text_field($_POST['product_ids'])));
          $profile_data = array(
            'exclude_product' => esc_sql($allExclude),
            'total_product' => $totProdRem >= 0 ? $totProdRem : 0,
          );
          $CONV_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => sanitize_text_field($_POST['feed_id'])));
        } else if ($result[0]['include_product'] != '' && $_POST['product_ids'] != '') {
          $include_product = explode(',', $result[0]['include_product']);
          if (($key = array_search(trim(str_replace($result[0]['product_id_prefix'], '', sanitize_text_field($_POST['product_ids']))), $include_product)) !== false) {
            unset($include_product[$key]);
          }
          $all_include = implode(',', $include_product);
          $profile_data = array(
            'include_product' => esc_sql($all_include),
            'total_product' => $totProdRem >= 0 ? $totProdRem : 0,
          );
          $CONV_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => sanitize_text_field($_POST['feed_id'])));
        } else {
          $profile_data = array(
            'exclude_product' => esc_sql(trim(str_replace($result[0]['product_id_prefix'], '', sanitize_text_field($_POST['product_ids'])))),
            'total_product' => $totProdRem >= 0 ? $totProdRem : 0,
          );
          $CONV_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => sanitize_text_field($_POST['feed_id'])));
        }
        $CONV_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $CONV_Admin_Helper->get_ee_options_data();
        $merchantId = $CONV_Admin_Helper->get_merchantId();

        $microsoft_catalog_id = '';
        $fb_catalog_id = '';
        $tiktok_catalog_id = '';

        $data = array(
          "merchant_id"     => $merchantId,
          "store_id"        => $google_detail['setting']->store_id,
          "store_feed_id"   => sanitize_text_field($_POST['feed_id']),
          "product_ids"     => sanitize_text_field($_POST['product_ids'])
        );
        if (!empty($result[0]) && isset($result[0]['tiktok_catalog_id'])) {
          $tiktok_catalog_id = sanitize_text_field($result[0]['tiktok_catalog_id']);
        }
        $tvc_admin_helper = new TVC_Admin_Helper();
        $ee_options = $tvc_admin_helper->get_ee_options_settings();
        if (!empty($ee_options['ms_catalog_id'])) {
          $microsoft_catalog_id = esc_html($ee_options['ms_catalog_id']);
        }
        $subscriptionId = $tvc_admin_helper->get_subscriptionId();
        $customApiObj = new CustomApi();
        $googledetail = $customApiObj->getGoogleAnalyticDetail($subscriptionId);
        if (!empty($googledetail->data->facebook_setting->fb_catalog_id)) {
          $fb_catalog_id = sanitize_text_field($googledetail->data->facebook_setting->fb_catalog_id);
        }
        $tiktok_business_id = isset($ee_options['tiktok_setting']['tiktok_business_id']) ? $ee_options['tiktok_setting']['tiktok_business_id'] : '';
        $data['tiktok_catalog_id'] = $tiktok_catalog_id;
        $data['tiktok_business_id'] = $tiktok_business_id;
        $data['ms_catalog_id']     = $microsoft_catalog_id;
        $data['catalog_id']     = $fb_catalog_id;
        /**
         * Api Call to delete product from GMC
         */
        $convCustomApi = new CustomApi();
        $response = $convCustomApi->delete_from_channels($data);
        echo json_encode($response);
        exit;
      } else {
        echo json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to show Feed wise woocommerce product data
     * Hook used wp_ajax_ee_get_product_details_for_table
     * Request Post
     * DB used Woo commerce db
     */
    public function ee_get_product_details_for_table()
    {
      $nonce = filter_input(INPUT_POST, 'product_details_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_product_details-nonce')) {
        $products_per_page = isset($_POST['length']) ? sanitize_text_field(absint($_POST['length'])) : 10;
        $page_number = isset($_POST['start']) ? sanitize_text_field(absint($_POST['start'])) : 1;
        $search = isset($_POST['searchName']) ? sanitize_text_field(wp_unslash($_POST['searchName'])) : '';
        $productSearch = isset($_POST['productData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['productData']))) : array();
        $conditionSearch = isset($_POST['conditionData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['conditionData']))) : array();
        $valueSearch = isset($_POST['valueData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['valueData']))) : array();
        $in_category_ids = array();
        $not_in_category_ids = array();
        $stock_status_to_fetch = array();
        $not_stock_status_to_fetch = $product_ids_to_exclude = $product_ids_to_include = array();
        // if (isset($_POST['searchName'])) {
        //   array_push($search, sanitize_text_field(wp_unslash($_POST['searchName'])));
        // }

        /*******************All filters mapping *****************/
        foreach ($productSearch as $key => $value) {
          switch ($value) {
            case 'product_cat':
              if ($conditionSearch[$key] == "=") {
                array_push($in_category_ids, $valueSearch[$key]);
              } else if ($conditionSearch[$key] == "!=") {
                array_push($not_in_category_ids, $valueSearch[$key]);
              }
              break;
            case '_stock_status':
              if (!empty($conditionSearch[$key]) && $conditionSearch[$key] == "=") {
                array_push($stock_status_to_fetch, $valueSearch[$key]);
              } else if (!empty($conditionSearch[$key]) && $conditionSearch[$key] == "!=") {
                array_push($not_stock_status_to_fetch, $valueSearch[$key]);
              }
              break;
            case 'ID':
              if ($conditionSearch[$key] == "=") {
                array_push($product_ids_to_include, $valueSearch[$key]);
              } else if ($conditionSearch[$key] == "!=") {
                array_push($product_ids_to_exclude, $valueSearch[$key]);
              }
              break;
          }
        }
        $tax_query = array();
        if (!empty($in_category_ids)) {
          $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $in_category_ids,
            'operator' => 'IN', // Retrieve products in any of the specified categories
          );
        }
        if (!empty($not_in_category_ids)) {
          $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $not_in_category_ids,
            'operator' => 'NOT IN', // Exclude products in any of the specified categories
          );
        }
        if (!empty($in_category_ids) && !empty($not_in_category_ids)) {
          $tax_query = array('relation' => 'AND');
        }
        $meta_query = array();
        if (!empty($stock_status_to_fetch)) {
          $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => $stock_status_to_fetch,
            'compare' => 'IN', // Include products with these stock statuses
          );
        }

        // Add not_stock_status_to_fetch condition
        if (!empty($not_stock_status_to_fetch)) {
          $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => $not_stock_status_to_fetch,
            'compare' => 'NOT IN', // Exclude products with these stock statuses
          );
        }
        if (!empty($stock_status_to_fetch) && !empty($not_stock_status_to_fetch)) {
          $meta_query = array('relation' => 'AND');
        }
        if (!isset($_POST['productData']) || $_POST['productData'] == "") {
          $pagination_count = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish', 's' => $search]))->found_posts;
          wp_reset_query();
        } else {
          $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            's'              => $search,
            'tax_query'      => $tax_query, // Dynamic tax query
            'meta_query'     => $meta_query,
            'post__not_in'   => $product_ids_to_exclude,
            'post__in'       => $product_ids_to_include,
          );

          $pagination_count  = (new WP_Query($args))->found_posts;
          wp_reset_query();
        }
        $p_id = isset($_POST['p_id']) ? sanitize_text_field(wp_unslash($_POST['p_id'])) : '';

        $args = array(
          'post_type'      => 'product',
          'posts_per_page' => $products_per_page,
          'post_status'    => 'publish',
          'offset'         => $page_number,
          'orderby'        => 'ID',
          'order'          => 'DESC',
          's'              => $search,
          'tax_query'      => $tax_query, // Dynamic tax query
          'meta_query'     => $meta_query,
          'post__not_in'   => $product_ids_to_exclude,
          'post__in'       => $product_ids_to_include,
        );
        $products = new WP_Query($args);
        $syncProductList = array();
        if ($products->have_posts()) {
          while ($products->have_posts()) {
            $products->the_post();
            $product_id =  get_the_ID();
            $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
            // Get product availability (stock status)
            $product_availability = get_post_meta($product_id, '_stock_status', true);

            // Get product quantity
            $product_quantity = get_post_meta($product_id, '_stock', true);
            $product_image_id = get_post_thumbnail_id($product_id);
            $product_image_src = wp_get_attachment_image_src($product_image_id, 'full');
            $product_image_url = isset($product_image_src[0]) ? $product_image_src[0] : "";
            $product_regular_price = get_post_meta($product_id, '_regular_price', true);
            $product_sale_price = get_post_meta($product_id, '_sale_price', true);
            $product_sku = get_post_meta($product_id, '_sku', true);
            if ($p_id == '_sku') {
              $proId = $product_sku;
            } elseif ($p_id == 'ID') {
              $proId = sanitize_text_field($product_id);
            } else {
              $proId = sanitize_text_field($product_id);
            }
            if ($proId == '') {
              $proId = sanitize_text_field($product_id);
            }
            $without_prefix = $proId;
            if (isset($_POST['prefix']) && !empty($_POST['prefix'])) {
              $proId = sanitize_text_field(wp_unslash($_POST['prefix'])) . $proId;
            }
            $type = get_post_meta($product_id, '_product_type', true);;

            $categories = '';
            foreach ($product_categories as $term) {
              $categories .= '<label class="fs-12 fw-400 defaultPointer">' . $term . '</label><br/>';
            }

            $syncProductList[] = array(
              'checkbox' => '<input class="checkbox" hidden type="checkbox" name="attrProduct"  id="attr_' . esc_html($product_id) . '" checked value="' . esc_html($proId) . '">
                                <div class="form-check form-check-custom">
                                <input class="form-check-input checkbox fs-17 syncProduct syncProduct_' . esc_html($without_prefix) . '" name="syncProduct" type="checkbox" value="' . esc_html($product_id) . '" id="sync_' . esc_html($product_id) . '" checked>
                                </div>',
              'product' => '<div class="d-flex flex-row bd-highlight">
                                <div class="p-2 pt-0 ps-0 bd-highlight image ">
                                  <img class="rounded image-w-h" src="' . esc_url($product_image_url) . '" />
                                </div>
                                <div class="p-3 pt-0 pb-0 bd-highlight">
                                <div class="text-truncate text-dark fs-12 fw-400" style="max-width: 200px;">' . sprintf(esc_html('%s'), esc_html(get_the_title())) . '</div>
                                <div class="fs-12 fw-400">Price: ' . get_woocommerce_currency_symbol() . " " . $product_regular_price . '</div>
                                <div class="fs-12 fw-400">Sale Price: ' . get_woocommerce_currency_symbol() . " " . $product_sale_price . '</div>
                                <div class="fs-12 fw-400">Product ID: ' . esc_html($product_id) . '</div>
                                <!--<div class="mt-1 text-dark"><abbr title="Get More Information" style="cursor: pointer;">More Info<abbr>
                                </div>-->
                                </div>
                                </div>',
              'category' => $categories,
              'availability' => '<label class="fs-12 fw-400 ' . esc_attr(ucfirst($product_availability)) . '">' . esc_html(ucfirst($product_availability)) . '</label>',
              'quantity' => '<label class="fs-12 fw-400">' . esc_html($product_quantity ? $product_quantity : '-') . '</label>',
              'channelstatus' => '<div class="channelStatus_ channelStatus_' . $proId . '"><div>
                  <button type="button" class="rounded-pill approved fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 approvedChannel"
                      data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover"
                      data-bs-trigger="hover focus">
                      Approved <span class="badge bg-light rounded-circle fs-7 approved-text ms-2 margin-badge approved_count_' . $proId . '"
                          style="top:0px;">0</span>
                  </button>
                  <div class="hidden approvedDivContent">
                      <div class="card custom-width rounded-5">
                          <div class="card-header bg-white channel_logo_' . $proId . '">                        
  
                          </div>
                      </div>
                  </div>
              </div>
              <div>
                  <button type="button"
                      class="rounded-pill pending fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 pendingIssues"
                      data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover"
                      data-bs-trigger="hover focus">
                      Pending&nbsp; <span class="badge bg-light rounded-circle fs-7 pending-text ms-2 margin-badge pending_count_' . $proId . '"
                          style="top:0px;">0</span>
                  </button>
                  <div class="hidden pendingDivContent">
                      <div class="card rounded-5">
                          <div class="card-header bg-warning-soft text-white">Pending Issues</div>
                          <div class="card-body pending_issue_text_' . $proId . '">
                              
                          </div>
                      </div>
                  </div>
              </div>
  
              <div>
                  <button type="button"
                      class="rounded-pill rejected fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 rejectIssues"
                      data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover"
                      data-bs-trigger="hover focus">
                      Rejected <span class="badge bg-light rounded-circle fs-7 rejected-text ms-2 margin-badge rejected_count_' . $proId . '"
                          style="top:0px;">0</span>
                  </button>
                  <div class="hidden rejectDivContent">
                      <div class="card rounded-5">
                          <div class="card-header bg-danger-soft text-white">Rejected Issues</div>
                          <div class="card-body rejected_issue_text_' . $proId . '">                        
                          </div>
                      </div>
                  </div>
              </div></div>',
              'action' => '<div class="fs-12 channel_' . $type . '_' . $proId . '" id="channel_action_' . $proId . '"></div><div class="innerSpinner action_" id="action_' . $product_id . '"><div class="call_both_verification-spinner tvc-nb-spinner"></div><p class="centered">Fetching...</p></div>',
            );
          }
        }
        wp_reset_postdata();
        $result = array(
          'draw' => isset($_POST['draw']) ? sanitize_text_field(wp_unslash($_POST['draw'])) : '',
          'recordsTotal' => sanitize_text_field($pagination_count),
          'recordsFiltered' => sanitize_text_field($pagination_count),
          'data' => $syncProductList
        );

        echo wp_json_encode($result);
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to get Product wise categories
     * Hook used wp_ajax_ee_syncProductCategory
     * Request Post
     */
    public function ee_syncProductCategory()
    {
      $nonce = filter_input(INPUT_POST, 'conv_syncprodcat_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_syncprodcat')) {
        $search = isset($_POST['searchName']) ? sanitize_text_field(wp_unslash($_POST['searchName'])) : '';
        $productSearch = isset($_POST['productData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['productData']))) : array();
        $conditionSearch = isset($_POST['conditionData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['conditionData']))) : array();
        $valueSearch = isset($_POST['valueData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['valueData']))) : array();
        $in_category_ids = array();
        $not_in_category_ids = array();
        $stock_status_to_fetch = array();
        $not_stock_status_to_fetch = $product_ids_to_exclude = $product_ids_to_include = array();

        /*******************All filters mapping *****************/
        foreach ($productSearch as $key => $value) {
          switch ($value) {
            case 'product_cat':
              if ($conditionSearch[$key] == "=") {
                array_push($in_category_ids, $valueSearch[$key]);
              } else if ($conditionSearch[$key] == "!=") {
                array_push($not_in_category_ids, $valueSearch[$key]);
              }
              break;
            case '_stock_status':
              if (!empty($conditionSearch[$key]) && $conditionSearch[$key] == "=") {
                array_push($stock_status_to_fetch, $valueSearch[$key]);
              } else if (!empty($conditionSearch[$key]) && $conditionSearch[$key] == "!=") {
                array_push($not_stock_status_to_fetch, $valueSearch[$key]);
              }
              break;
            case 'ID':
              if ($conditionSearch[$key] == "=") {
                array_push($product_ids_to_include, $valueSearch[$key]);
              } else if ($conditionSearch[$key] == "!=") {
                array_push($product_ids_to_exclude, $valueSearch[$key]);
              }
              break;
          }
        }
        if (isset($_POST['productArray']))
          $conv_productArray = is_array($_POST['productArray']) ? array_map('sanitize_text_field', wp_unslash($_POST['productArray'])) : sanitize_text_field(wp_unslash($_POST['productArray']));
        else
          $conv_productArray = "";

        if (isset($_POST['exclude']))
          $conv_productexclude = is_array($_POST['exclude']) ? array_map('sanitize_text_field', wp_unslash($_POST['exclude'])) : sanitize_text_field(wp_unslash($_POST['exclude']));
        else
          $conv_productexclude = "";

        if (isset($_POST['include']))
          $conv_productinclude = is_array($_POST['include']) ? array_map('sanitize_text_field', wp_unslash($_POST['include'])) : sanitize_text_field(wp_unslash($_POST['include']));
        else
          $conv_productinclude = "";

        if (!empty($conv_productArray) && is_array($conv_productArray)) {
          $a = array_filter($conv_productArray, function ($v) {
            if ($v != "syncAll") {
              return $v;
            }
          });
          foreach ($a as $key => $value) {
            array_push($product_ids_to_include, $value);
          }
        } else if (!empty($conv_productexclude) && is_array($conv_productexclude) && isset($_POST['inculdeExtraProduct']) && $_POST['inculdeExtraProduct'] == "") {
          $b = array_filter($conv_productexclude, function ($v) {
            if ($v != "syncAll") {
              return $v;
            }
          });
          foreach ($b as $key => $value) {
            array_push($product_ids_to_exclude, $value);
          }
        } else if (!empty($conv_productinclude) && is_array($conv_productinclude) && isset($_POST['inculdeExtraProduct']) && $_POST['inculdeExtraProduct'] == "") {
          $c = array_filter($conv_productinclude, function ($v) {
            if ($v != "syncAll") {
              return $v;
            }
          });
          foreach ($c as $key => $value) {
            array_push($product_ids_to_include, $value);
          }
        }
        $tax_query = array();
        if (!empty($in_category_ids)) {
          $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $in_category_ids,
            'operator' => 'IN', // Retrieve products in any of the specified categories
          );
        }
        if (!empty($not_in_category_ids)) {
          $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $not_in_category_ids,
            'operator' => 'NOT IN', // Exclude products in any of the specified categories
          );
        }
        if (!empty($in_category_ids) && !empty($not_in_category_ids)) {
          $tax_query = array('relation' => 'AND');
        }
        $meta_query = array();
        if (!empty($stock_status_to_fetch)) {
          $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => $stock_status_to_fetch,
            'compare' => 'IN', // Include products with these stock statuses
          );
        }

        // Add not_stock_status_to_fetch condition
        if (!empty($not_stock_status_to_fetch)) {
          $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => $not_stock_status_to_fetch,
            'compare' => 'NOT IN', // Exclude products with these stock statuses
          );
        }
        if (!empty($stock_status_to_fetch) && !empty($not_stock_status_to_fetch)) {
          $meta_query = array('relation' => 'AND');
        }

        if (!isset($_POST['productData']) || $_POST['productData'] == "") {
          $count = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish', 's' => $search]))->found_posts;
          wp_reset_query();
        } else {
          $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            's'              => $search,
            'tax_query'      => $tax_query, // Retrieve products in any of the specified categories          
            'meta_query'     => $meta_query,
            'post__not_in'   => $product_ids_to_exclude,
            'post__in'       => $product_ids_to_include,
          );

          $count = (new WP_Query($args))->found_posts;
        }

        $allowed_count = 200;
        if ($count <= $allowed_count) {
          $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $allowed_count,
            'post_status'    => 'publish',
            'offset'         => 0,
            's'              => $search,
            'tax_query'      => $tax_query, // Retrieve products in any of the specified categories          
            'meta_query'     => $meta_query,
            'post__not_in'   => $product_ids_to_exclude,
            'post__in'       => $product_ids_to_include,
          );
          $products  = new WP_Query($args);
          if ($products->have_posts()) {
            while ($products->have_posts()) {
              $products->the_post();
              $product_id =  get_the_ID();
              $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
              foreach ($product_categories as $term) {
                $cat[$term->term_id] = $term->term_id;
              }
            }
          }
        } else {
          $allowed_count = 200;
          $page_number = 0;
          while ($count > 0) {
            $args = array(
              'post_type'      => 'product',
              'posts_per_page' => $allowed_count,
              'post_status'    => 'publish',
              'offset'         => $page_number,
              's'              => $search,
              'tax_query'      => $tax_query, // Dynamic tax query
              'meta_query'     => $meta_query,
              'post__not_in'   => $product_ids_to_exclude,
              'post__in'       => $product_ids_to_include,
            );

            $products  = new WP_Query($args);
            if ($products->have_posts()) {
              while ($products->have_posts()) {
                $products->the_post();
                $product_id =  get_the_ID();

                $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
                foreach ($product_categories as $term) {
                  $cat[$term->term_id] = $term->term_id;
                }
              }
            }
            $page_number =  $page_number + 200;
            $count = $count - $allowed_count;
          }
        }
        wp_reset_postdata();
        echo wp_json_encode(array_values($cat));
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /************************************ All function for Feed Wise Product Sync Start ******************************************************************/
    /**
     * Ajax Call Feed wise product sync
     * Store category/attribute into options
     * Store Feed setting data into DB
     * initiated by ajax
     * Database Table used `ee_product_feed` 
     */
    function ee_feed_wise_product_sync_batch_wise()
    {
      $w_cat_id = $g_cat_id = '';
      $nonce = filter_input(INPUT_POST, 'conv_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'conv_ajax_product_sync_bantch_wise-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->plugin_log("Start", 'product_sync');
        $conv_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        try {
          $selecetedCat = [];
          $feed_MappedCat = [];
          $mappedCats = [];
          $mappedAttrs = [];
          $mappedCatsDB = [];
          $product_batch_size = isset($_POST['product_batch_size']) ? sanitize_text_field(wp_unslash($_POST['product_batch_size'])) : "25"; // barch size for inser product in GMC
          $product_id_prefix = isset($_POST['product_id_prefix']) ? sanitize_text_field(wp_unslash($_POST['product_id_prefix'])) : "";
          $data = isset($_POST['conv_data']) ? wp_unslash($_POST['conv_data']) : "";
          wp_parse_str($data, $formArray);

          $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();

          if (!empty($formArray)) {
            foreach ($formArray as $key => $value) {
              //$formArray[$key] = sanitize_text_field($value);
              if ($key == 'additional_attr_') {
                $additional_attr = $value;
                unset($formArray['additional_attr_']);
              }
              if ($key == 'additional_attr_value_') {
                $additional_attr_value = $value;
                unset($formArray['additional_attr_value_']);
              }
              if (is_array($value) !== 1) {
                $formArray[$key] = $value;
              }
            }
            unset($formArray['additional_attr_']);
            unset($formArray['additional_attr_value_']);
            if (isset($additional_attr)) {
              foreach ($additional_attr as $key => $value) {
                $formArray[$value] = $additional_attr_value[$key];
              }
            }
          }
          /**
           * Filter data
           */
          $productFilter = isset($_POST['productData']) && $_POST['productData'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['productData']))) : '';
          $conditionFilter = isset($_POST['conditionData']) && $_POST['conditionData'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['conditionData']))) : '';
          $valueFilter = isset($_POST['valueData']) && $_POST['valueData'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['valueData']))) : '';
          $filters = array();
          if (!empty($productFilter)) {
            foreach ($productFilter as $key => $val) {
              $filters[$key]['attr'] = sanitize_text_field($val);
              $filters[$key]['condition'] = sanitize_text_field($conditionFilter[$key]);
              $filters[$key]['value'] = sanitize_text_field($valueFilter[$key]);
            }
          }
          $selecetedCat = explode(',', $formArray['selectedCategory']);
          /*
           * Collect Attribute/Categories Mapping
           */
          foreach ($formArray as $key => $value) {
            if (preg_match("/^category-name-/i", $key)) {
              if ($value != '') {
                $keyArray = explode("name-", $key);
                $mappedCatsDB[$keyArray[1]]['name'] = $value;
                if (in_array($keyArray[1], $selecetedCat)) {
                  $feed_MappedCat[$keyArray[1]]['name'] = $value;
                }
              }
              unset($formArray[$key]);
            } else if (preg_match("/^category-/i", $key)) {
              if ($value != '' && $value > 0) {
                $keyArray = explode("-", $key);
                $mappedCats[$keyArray[1]] = $value;
                $mappedCatsDB[$keyArray[1]]['id'] = $value;
                if (in_array($keyArray[1], $selecetedCat)) {
                  $feed_MappedCat[$keyArray[1]]['id'] = $value;
                  $w_cat_id = $keyArray[1];
                  $g_cat_id = $value;
                }
              }
              unset($formArray[$key]);
            } else {
              if ($value && $key != 'selectedCategory') {
                $mappedAttrs[$key] = $value;
              }
            }
          }

          //add/update data in default profile
          $profile_data = array("profile_title" => esc_sql("Default"), "g_attribute_mapping" => wp_json_encode($mappedAttrs), "update_date" => gmdate('Y-m-d H:i:s', current_time('timestamp')));
          if ($TVC_Admin_DB_Helper->tvc_row_count("ee_product_sync_profile") == 0) {
            $TVC_Admin_DB_Helper->tvc_add_row("ee_product_sync_profile", $profile_data, array("%s", "%s", "%s"));
          } else {
            $TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_profile", $profile_data, array("id" => 1));
          }

          // Update settings Product Mapping
          update_option("ee_prod_mapped_cats", serialize($mappedCatsDB));
          update_option("ee_prod_mapped_attrs", serialize($mappedAttrs));

          // Batch settings
          $conv_additional_data['is_mapping_update'] = true;
          $conv_additional_data['is_process_start'] = false;
          $conv_additional_data['is_auto_sync_start'] = false;
          $conv_additional_data['product_sync_batch_size'] = sanitize_text_field($product_batch_size);
          $conv_additional_data['product_id_prefix'] = sanitize_text_field($product_id_prefix);
          $conv_additional_data['product_sync_alert'] = sanitize_text_field("Product sync settings updated successfully");
          $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
          $google_detail = $TVC_Admin_Helper->get_ee_options_data();
          $CustomApi = new CustomApi();
          if (!class_exists('TVCProductSyncHelper')) {
            include ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php';
          }
          $TVCProductSyncHelper = new TVCProductSyncHelper();

          $TVC_Admin_Helper->plugin_log("wooww 2411 Update Product Feed Table", 'product_sync');
          //Update Product Feed Table          
          $key = "id";
          $val = isset($_POST['feedId']) ? sanitize_text_field(wp_unslash($_POST['feedId'])) : '';
          if ($TVC_Admin_DB_Helper->tvc_check_row("ee_product_feed", $key, $val)) {
            /***Single product sync for already synced product feed ******/
            if (isset($_POST['inculdeExtraProduct']) && $_POST['inculdeExtraProduct'] != '') {

              $TVC_Admin_Helper->plugin_log("woow 2419 Single product sync for already synced product feed", 'product_sync');
              $feed_datas = array(
                "attributes" => wp_json_encode($mappedAttrs),
              );

              //update attribute in ee_product_feed table
              $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_datas, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));

              global $wpdb;
              $product_batch_size = (isset($conv_additional_data['product_sync_batch_size']) && $conv_additional_data['product_sync_batch_size']) ? $conv_additional_data['product_sync_batch_size'] : 100;
              $tvc_currency = sanitize_text_field($TVC_Admin_Helper->get_woo_currency());
              $merchantId = sanitize_text_field($TVC_Admin_Helper->get_merchantId());
              $accountId = sanitize_text_field($TVC_Admin_Helper->get_main_merchantId());
              $subscriptionId = sanitize_text_field(sanitize_text_field($TVC_Admin_Helper->get_subscriptionId()));
              $product_batch_size = esc_sql(intval($product_batch_size));
              if (isset($_POST['inculdeExtraProduct'])) {
                $products[0]['w_product_id'] = sanitize_text_field(wp_unslash($_POST['inculdeExtraProduct']));
              }
              $tiktok_catalog_id = '';
              $tiktok_business_id = sanitize_text_field($TVC_Admin_Helper->get_tiktok_business_id());
              $object = array(
                '0' => (object) array(
                  'w_product_id' => isset($_POST['inculdeExtraProduct']) ? sanitize_text_field(wp_unslash($_POST['inculdeExtraProduct'])) : '',
                  'w_cat_id' => $w_cat_id,
                  'g_cat_id' => $g_cat_id
                )
              );

              //map each product with category and attribute
              $p_map_attribute = $TVCProductSyncHelper->conv_get_feed_wise_map_product_attribute($object, $tvc_currency, $merchantId, $product_batch_size, $mappedAttrs, $product_id_prefix);

              $TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
              //update ee_product_sync_data
              if (isset($p_map_attribute['valid_products'])) {
                $TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db_batch_wise($p_map_attribute['valid_products'], sanitize_text_field(wp_unslash($_POST['feedId']))); //Add data in sync product database
              }
              if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {
                $ee_options = $TVC_Admin_Helper->get_ee_options_settings();
                // call product sync API
                $data = [
                  'merchant_id' => sanitize_text_field($accountId),
                  'account_id' => sanitize_text_field($merchantId),
                  'subscription_id' => sanitize_text_field($subscriptionId),
                  'store_feed_id' => isset($_POST['feedId']) ? sanitize_text_field(wp_unslash($_POST['feedId'])) : '',
                  'is_on_gmc' => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '1') !== false ? true : false,
                  'is_on_microsoft' => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '4') !== false ? true : false,
                  'is_on_tiktok' => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '3') !== false ? true : false,
                  'tiktok_catalog_id' => isset($_POST['tiktok_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id'])) : '',
                  'tiktok_business_id' => sanitize_text_field($tiktok_business_id),
                  'is_on_facebook' => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '2') !== false ? true : false,
                  'business_id' =>  isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '2') !== false ? sanitize_text_field($ee_options['facebook_setting']['fb_business_id']) : '',
                  'catalog_id' =>  isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '2') !== false ? sanitize_text_field($ee_options['facebook_setting']['fb_catalog_id']) : '',
                  'ms_catalog_id' =>  strpos($_POST['channel_ids'], '4') !== false ? $ee_options['ms_catalog_id'] : '',
                  'ms_store_id' =>  strpos($_POST['channel_ids'], '4') !== false ? $ee_options['microsoft_merchant_center_id'] : '',
                  'entries' => $p_map_attribute['items']
                ];
                /**************************** API Call to GMC ****************************************************************************/

                $response = $CustomApi->feed_wise_products_sync($data, 'call_by_includes/data/class-tvc-ajax-file.php 2447');
                $TVC_Admin_Helper->plugin_log("woow 2477 callback-feed_wise_products_sync()", 'product_sync');

                $endTime = new DateTime();
                $startTime = new DateTime();
                $diff = $endTime->diff($startTime);
                $responseData['time_duration'] = $diff;
                update_option("ee_prod_response", serialize($responseData));

                //echo '<pre>'; print_r($response); echo '</pre>';
                if ($response->error == false) {
                  if (isset($_POST['feedId'])) {
                    $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['feedId'])));
                  } else {
                    $where = '';
                  }
                  $filed = ['total_product'];
                  $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
                  $totalProduct = 0;
                  if ($result[0]['total_product'] !== NULL) {
                    $totalProduct = $result[0]['total_product'] + 1;
                  }
                  $feed_data = array(
                    "exclude_product" => isset($_POST['exclude']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['exclude']))) : '',
                    "include_product" => isset($_POST['include']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['include']))) : '',
                    "product_sync_alert" => NULL,
                    'total_product' => $totalProduct,
                  );
                  $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));

                  $syn_data = array(
                    'status' => 1
                  );
                  if (isset($_POST['feedId'])) {
                    $TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_data", $syn_data, array("feedId" => sanitize_text_field(wp_unslash($_POST['feedId']))));
                  }
                  $sync_message = esc_html__("By ajax Initiated, products are being synced to Merchant Center. Do not refresh..", "enhanced-e-commerce-for-woocommerce-store");
                  $sync_message = sprintf(esc_html('%s'), esc_html($sync_message));
                  $sync_progressive_data = array("sync_message" => $sync_message);
                  echo wp_json_encode(array('status' => 'success', "sync_progressive_data" => $sync_progressive_data));
                  exit;
                } else {
                  $TVC_Admin_Helper->plugin_log("woow 2518 Error in Sync", 'product_sync');
                  return wp_json_encode(array('error' => true, 'message' => esc_attr('Error in Sync...')));
                }
              } else {
                $TVC_Admin_Helper->plugin_log("woow 2522 Error in Sync", 'product_sync');
                return wp_json_encode(array('error' => true, 'message' => esc_attr('Error in Sync woow-2523...')));
              }
            } else {

              $TVC_Admin_Helper->plugin_log("woow 2527 Update feed data in DB start", 'product_sync');
              /*******Update feed data in DB start**********************/
              $feed_data = array(
                "categories" => wp_json_encode($feed_MappedCat),
                "attributes" => wp_json_encode($mappedAttrs),
                "filters" => wp_json_encode($filters),
                "include_product" => esc_sql(sanitize_text_field(wp_unslash($_POST['include']))),
                "exclude_product" => isset($_POST['exclude']) && $_POST['exclude'] != '' ? esc_sql(sanitize_text_field(wp_unslash($_POST['exclude']))) : '',
                "is_mapping_update" => true,
                "is_process_start" => false,
                "is_auto_sync_start" => false,
                "product_sync_batch_size" => esc_sql($product_batch_size),
                "product_id_prefix" => esc_sql($product_id_prefix),
                "product_sync_alert" => sanitize_text_field("Product sync settings updated successfully"),
                "status" => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '1') !== false ? esc_sql('In Progress') : null,
                "is_default" => esc_sql(0),
                "fb_status" => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '2') !== false ? esc_sql('In Progress') : null,
                "tiktok_status" => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '3') !== false ? esc_sql('In Progress') : null,
                "ms_status" => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '4') !== false ? esc_sql('In Progress') : null,
              );
              $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));
              /*******Update feed data in DB end**********************/

              // if $feed_MappedCat['condition'] is not set then set 'new' as default
              if (!isset($feed_MappedCat['condition'])) {
                $feed_MappedCat['condition'] = 'new';
              }

              /*******Update feed data in laravel start**********************/
              $feed_data_api = array(
                "store_id" => $google_detail['setting']->store_id,
                "store_feed_id" => sanitize_text_field(wp_unslash($_POST['feedId'])),
                "map_categories" => wp_json_encode($feed_MappedCat),
                "map_attributes" => wp_json_encode($mappedAttrs),
                "filter" => wp_json_encode($filters),
                "include" => isset($_POST['include']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['include']))) : '',
                "exclude" => isset($_POST['exclude']) && $_POST['exclude'] != '' ? esc_sql(sanitize_text_field(wp_unslash($_POST['exclude']))) : '',
                "channel_ids" => isset($_POST['channel_ids']) ? sanitize_text_field(wp_unslash($_POST['channel_ids'])) : '',
                "interval" => isset($_POST['autoSyncInterval']) ? sanitize_text_field(wp_unslash($_POST['autoSyncInterval'])) : '',
                "tiktok_catalog_id" => isset($_POST['tiktok_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id'])) : '',
              );



              $TVC_Admin_Helper->plugin_log("mapping saved and product sync process scheduled", 'product_sync'); // Add logs
              $TVC_Admin_Helper->plugin_log("sending data to api" . wp_json_encode($feed_data_api), 'feed_data_api');
              $CustomApi = new CustomApi();
              $CustomApi->ee_create_product_feed($feed_data_api);
              /*******Update feed data in laravel End *********************/

              /********Manual Product sync Start ******************/
              if (isset($_POST['feedId'])) {
                as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['feedId']))));
                $isSyncComplete = $TVCProductSyncHelper->manualProductSync(sanitize_text_field(wp_unslash($_POST['feedId'])));
              } else {
                $isSyncComplete = '';
              }
              if ($isSyncComplete['status'] === 'success') {

                if (isset($_POST['feedId'])) {
                  $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['feedId'])));
                } else {
                  $where = '';
                }
                $filed = ['auto_sync_interval', 'auto_schedule'];
                $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
                $last_sync_date = gmdate('Y-m-d H:i:s', current_time('timestamp'));
                $next_schedule_date = NULL;
                if ($result[0]['auto_schedule'] == 1) {
                  $next_schedule_date = gmdate('Y-m-d H:i:s', strtotime('+' . $result[0]['auto_sync_interval'] . 'day', current_time('timestamp')));
                  $time_space = strtotime($result[0]['auto_sync_interval'] . " days", 0);
                  $timestamp = strtotime($result[0]['auto_sync_interval'] . " days");
                  $TVC_Admin_Helper->plugin_log("recurring cron set", 'product_sync'); // Add logs 
                  as_schedule_recurring_action(esc_attr($timestamp), esc_attr($time_space), 'init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['feedId']))), "product_sync");
                }

                $feed_data = array(
                  "product_sync_alert" => NULL,
                  "is_process_start" => false,
                  "is_auto_sync_start" => false,
                  "last_sync_date" => esc_sql($last_sync_date),
                  "next_schedule_date" => $next_schedule_date,
                );
                $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));
              } else {
                $feed_data = array(
                  "product_sync_alert" => $isSyncComplete['message'],
                  "is_process_start" => false,
                  "is_auto_sync_start" => false,
                  "is_mapping_update" => false,
                );
                $TVC_Admin_Helper->plugin_log("error-1", 'product_sync'); // Add logs
                $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));
                echo wp_json_encode(array("error" => true, "message" =>  $isSyncComplete['message']));
                exit;
              }

              /********Manual Product sync End ******************/
            }
          } else {
            echo wp_json_encode(array("error" => true, "message" => esc_html__("Feed data is missing.", "enhanced-e-commerce-for-woocommerce-store")));
            exit;
          }
          $sync_message = esc_html__("Via Ajax Initiated, products are being synced to Merchant Center, Do not refresh..", "enhanced-e-commerce-for-woocommerce-store");
          $sync_message = sprintf(esc_html('%s'), esc_html($sync_message));
          $sync_progressive_data = array("sync_message" => $sync_message);
          echo wp_json_encode(array('status' => 'success', "sync_progressive_data" => $sync_progressive_data));
          exit;
        } catch (Exception $e) {
          $TVC_Admin_Helper->plugin_log("woow 2627 catch method", 'product_sync');
          $conv_additional_data['product_sync_alert'] = $e->getMessage();
          $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
          $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
          $feed_data = array(
            "product_sync_alert" => $e->getMessage(),
            "is_process_start" => false,
            "is_auto_sync_start" => false,
            "is_mapping_update" => false,
          );
          if (isset($_POST['feedId'])) {
            $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));
          }
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * Cron used for Feed wise product sync
     * Store data into Database 
     * hook used init_feed_wise_product_sync_process_scheduler
     * initiated by init_feed_wise_product_sync_process_scheduler_ee hook
     * Database Table used `ee_prouct_pre_sync_data` 
     * parameter int $feedId
     */
    function ee_call_start_feed_wise_product_sync_process($feedId)
    {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $TVC_Admin_Helper->plugin_log("Process to store data into ee_prouct_pre_sync_data table at " . gmdate('Y-m-d H:i:s', current_time('timestamp')) . " feed Id " . $feedId, 'product_sync'); // Add logs 
      $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      try {
        $subscriptionId = $TVC_Admin_Helper->get_subscriptionId();
        $customApiObj = new CustomApi();
        $googledetail = $customApiObj->getGoogleAnalyticDetail($subscriptionId);
        $googleDetail = $googledetail->data;

        global $wpdb;
        $where = '`id` = ' . esc_sql($feedId);
        $filed = ['feed_name', 'channel_ids', 'auto_sync_interval', 'auto_schedule', 'categories', 'attributes', 'filters', 'include_product', 'exclude_product', 'is_mapping_update', 'tiktok_catalog_id'];
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        $gmc_id = isset($googleDetail->google_merchant_center_id) === TRUE ? $googleDetail->google_merchant_center_id : '';
        if (strpos($result[0]['channel_ids'], '1') && ($gmc_id == '' || $gmc_id == null)) {
          $feed_data = array(
            "product_sync_alert" => 'GMC Id missing',
            "is_process_start" => false,
            "is_auto_sync_start" => false,
            "is_mapping_update" => false,
            "status" => strpos($result[0]['channel_ids'], '1') !== false ? esc_sql('Draft') : null,
            "fb_status" => strpos($result[0]['channel_ids'], '2') !== false ? esc_sql('Draft') : null,
            "tiktok_status" => strpos($result[0]['channel_ids'], '3') !== false ? esc_sql('Draft') : null,
            "ms_status" => strpos($result[0]['channel_ids'], '4') !== false ? esc_sql('Draft') : null,
          );
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
          $TVC_Admin_Helper->plugin_log('GMC Id missing', 'product_sync');
          exit;
        }
        if (strpos($result[0]['channel_ids'], '3') && ($result[0]['tiktok_catalog_id'] == '' || $result[0]['tiktok_catalog_id'] == null)) {
          $feed_data = array(
            "product_sync_alert" => 'Tiktok Catalog missing',
            "is_process_start" => false,
            "is_auto_sync_start" => false,
            "is_mapping_update" => false,
            "status" => strpos($result[0]['channel_ids'], '1') !== false ? esc_sql('Draft') : null,
            "tiktok_status" => strpos($result[0]['channel_ids'], '3') !== false ? esc_sql('Draft') : null,
          );
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
          $TVC_Admin_Helper->plugin_log('Tiktok Catalog missing', 'product_sync');
          exit;
        }
        if (!empty($result) && isset($result) && $result[0]['is_mapping_update'] == '1') {
          $prouct_pre_sync_table = esc_sql("ee_prouct_pre_sync_data");
          if ($TVC_Admin_DB_Helper->tvc_row_count($prouct_pre_sync_table) > 0) {
            $TVC_Admin_DB_Helper->tvc_safe_truncate_table($wpdb->prefix . $prouct_pre_sync_table);
          }

          $product_db_batch_size = 200; // batch size to insert in database
          $batch_count = 0;
          $values = array();
          $place_holders = array();

          if ($result) {
            $TVC_Admin_Helper->plugin_log("Fetched feed data by ID", 'product_sync'); // Add logs       
            $filters = json_decode($result[0]['filters']);
            $filters_count = is_array($filters) ? count($filters) : '';
            $categories = json_decode($result[0]['categories']);
            $attributes = json_decode($result[0]['attributes']);
            $include = $result[0]['include_product'] != '' ? explode(",", $result[0]['include_product']) : '';
            $exclude = explode(",", $result[0]['exclude_product']);

            $in_category_ids = array();
            $not_in_category_ids = array();
            $stock_status_to_fetch = array();
            $not_stock_status_to_fetch = $product_ids_to_exclude = $product_ids_to_include = $search = array();
            $product_count = 0;
            if ($filters_count != '') {
              for ($i = 0; $i < $filters_count; $i++) {
                switch ($filters[$i]->attr) {
                  case 'product_cat':
                    if ($filters[$i]->condition == "=") {
                      array_push($in_category_ids, $filters[$i]->value);
                    } else if ($filters[$i]->condition == "!=") {
                      array_push($not_in_category_ids, $filters[$i]->value);
                    }
                    break;
                  case '_stock_status':
                    if (!empty($filters[$i]->condition) && $filters[$i]->condition == "=") {
                      array_push($stock_status_to_fetch, $filters[$i]->value);
                    } else if (!empty($filters[$i]->condition) && $filters[$i]->condition == "!=") {
                      array_push($not_stock_status_to_fetch, $filters[$i]->value);
                    }
                    break;
                  case 'ID':
                    if ($filters[$i]->condition == "=") {
                      array_push($product_ids_to_include, $filters[$i]->value);
                    } else if ($filters[$i]->condition == "!=") {
                      array_push($product_ids_to_exclude, $filters[$i]->value);
                    }
                    break;
                }
              }
            }

            if ($include == '') {
              $tax_query = array();
              if (!empty($in_category_ids)) {
                $tax_query[] = array(
                  'taxonomy' => 'product_cat',
                  'field'    => 'term_id',
                  'terms'    => $in_category_ids,
                  'operator' => 'IN', // Retrieve products in any of the specified categories
                );
              }
              if (!empty($not_in_category_ids)) {
                $tax_query[] = array(
                  'taxonomy' => 'product_cat',
                  'field'    => 'term_id',
                  'terms'    => $not_in_category_ids,
                  'operator' => 'NOT IN', // Exclude products in any of the specified categories
                );
              }
              if (!empty($in_category_ids) && !empty($not_in_category_ids)) {
                $tax_query = array('relation' => 'AND');
              }
              $meta_query = array();
              if (!empty($stock_status_to_fetch)) {
                $meta_query[] = array(
                  'key'     => '_stock_status',
                  'value'   => $stock_status_to_fetch,
                  'compare' => 'IN', // Include products with these stock statuses
                );
              }

              // Add not_stock_status_to_fetch condition
              if (!empty($not_stock_status_to_fetch)) {
                $meta_query[] = array(
                  'key'     => '_stock_status',
                  'value'   => $not_stock_status_to_fetch,
                  'compare' => 'NOT IN', // Exclude products with these stock statuses
                );
              }
              if (!empty($stock_status_to_fetch) && !empty($not_stock_status_to_fetch)) {
                $meta_query = array('relation' => 'AND');
              }
              if (empty($tax_query) && empty($meta_query) && empty($product_ids_to_exclude) && empty($product_ids_to_include)) {
                $count = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish', 's' => $search]))->found_posts;
                wp_reset_query();
              } else {
                $args = array(
                  'post_type'      => 'product',
                  'post_status'    => 'publish',
                  's'              => $search,
                  'tax_query'      => $tax_query, // Retrieve products in any of the specified categories          
                  'meta_query'     => $meta_query,
                  'post__not_in'   => $product_ids_to_exclude,
                  'post__in'       => $product_ids_to_include,
                );

                $count = (new WP_Query($args))->found_posts;
                wp_reset_query();
              }

              $allowed_count = 200;
              if ($count <= $allowed_count) {
                $args = array(
                  'post_type'      => 'product',
                  'posts_per_page' => 200,
                  'post_status'    => 'publish',
                  'offset'         => 0,
                  's'              => $search,
                  'tax_query'      => $tax_query, // Retrieve products in any of the specified categories          
                  'meta_query'     => $meta_query,
                  'post__not_in'   => $product_ids_to_exclude,
                  'post__in'       => $product_ids_to_include,
                );
                $products  = new WP_Query($args);
                if ($products->have_posts()) {
                  $all_cat = array();
                  foreach ($categories as $cat_key => $cat_val) {
                    $all_cat[$cat_key] = $cat_key;
                  }
                  while ($products->have_posts()) {
                    $products->the_post();
                    $product_id =  get_the_ID();
                    if (!in_array($product_id, $exclude)) {
                      $product_count++;
                      $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
                      foreach ($product_categories as $term) {
                        $cat_id = $term->term_id;
                        if ($term->term_id == $all_cat[$cat_id]) {
                          $cat_matched_id = $term->term_id;
                          $have_cat = true;
                        }
                      }
                      if ($have_cat == true) {
                        array_push($values, esc_sql($product_id), esc_sql($cat_matched_id), esc_sql($categories->$cat_matched_id->id), 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                        $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                      } else {
                        array_push($values, esc_sql($product_id), esc_sql($cat_id), '', 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                        $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                      }
                    }
                  }
                  $query = "INSERT INTO `$wpdb->prefix$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date, feedId) VALUES ";
                  $query .= implode(', ', $place_holders);
                  $wpdb->query($wpdb->prepare($query, $values));
                  wp_reset_postdata();
                  as_schedule_single_action(time() + 5, 'auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
                }
              } else {
                $allowed_count = 200;
                $page_number = 0;
                while ($count > 0) {
                  $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => $allowed_count,
                    'post_status'    => 'publish',
                    'offset'         => $page_number,
                    's'              => $search,
                    'tax_query'      => $tax_query, // Dynamic tax query
                    'meta_query'     => $meta_query,
                    'post__not_in'   => $product_ids_to_exclude,
                    'post__in'       => $product_ids_to_include,
                  );

                  $products  = new WP_Query($args);

                  if ($products->have_posts()) {
                    $all_cat = array();
                    foreach ($categories as $cat_key => $cat_val) {
                      $all_cat[$cat_key] = $cat_key;
                    }
                    $batch_count = 0;
                    while ($products->have_posts()) {
                      $products->the_post();
                      $product_id =  get_the_ID();
                      if (!in_array($product_id, $exclude)) {
                        $product_count++;
                        $batch_count++;
                        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
                        foreach ($product_categories as $term) {
                          $cat_id = $term->term_id;
                          if ($term->term_id == $all_cat[$cat_id]) {
                            $cat_matched_id = $term->term_id;
                            $have_cat = true;
                          }
                        }
                        if ($have_cat == true) {
                          array_push($values, esc_sql($product_id), esc_sql($cat_matched_id), esc_sql($categories->$cat_matched_id->id), 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                          $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                        } else {
                          array_push($values, esc_sql($product_id), esc_sql($cat_id), '', 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                          $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                        }
                      }
                    }
                    $query = "INSERT INTO `$wpdb->prefix$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date, feedId) VALUES ";
                    $query .= implode(', ', $place_holders);
                    $wpdb->query($wpdb->prepare($query, $values));
                    $batch_count = 0;
                    $values = array();
                    $place_holders = array();
                  }
                  $page_number =  $page_number + 200;
                  $count = $count - $allowed_count;
                  wp_reset_postdata();
                }
                $TVC_Admin_Helper->plugin_log("All Data stored in ee_prouct_pre_sync_data table at " . gmdate('Y-m-d H:i:s', current_time('timestamp')) . " feed Id " . $feedId, 'product_sync'); // Add logs 
                as_schedule_single_action(time() + 5, 'auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
              }
            } else {
              $TVC_Admin_Helper->plugin_log("Only include product", 'product_sync'); // Add logs               
              foreach ($include as $val) {
                $allResult[]['ID'] = $val;
              }

              if (!empty($allResult)) {
                $all_cat = array();

                foreach ($categories as $cat_key => $cat_val) {
                  $all_cat[$cat_key] = $cat_key;
                }
                //$product_count = 0;
                $a = 0;
                foreach ($allResult as $postvalue) {
                  $have_cat = false;
                  if (!in_array($postvalue['ID'], $exclude)) {
                    $terms = get_the_terms(sanitize_text_field($postvalue['ID']), 'product_cat');
                    foreach ($terms as $key => $term) {
                      $cat_id = $term->term_id;
                      if ($term->term_id == $all_cat[$cat_id]) {
                        $cat_matched_id = $term->term_id;
                        $have_cat = true;
                      }
                    }

                    if ($have_cat == true) {
                      $product_count++;
                      $batch_count++;
                      array_push($values, esc_sql($postvalue['ID']), esc_sql($cat_matched_id), esc_sql($categories->$cat_matched_id->id), 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                      $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                      $query = "INSERT INTO `$wpdb->prefix$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date, feedId) VALUES ";
                      $query .= implode(', ', $place_holders);
                      $wpdb->query($wpdb->prepare($query, $values));
                    } else {
                      $product_count++;
                      array_push($values, esc_sql($postvalue['ID']), esc_sql($cat_id), '', 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                      $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                      $query = "INSERT INTO `$wpdb->prefix$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date, feedId) VALUES ";
                      $query .= implode(', ', $place_holders);
                      $wpdb->query($wpdb->prepare($query, $values));
                    }
                  }
                } //end product list loop

                $TVC_Admin_Helper->plugin_log("All Data stored in ee_prouct_pre_sync_data table at " . gmdate('Y-m-d H:i:s', current_time('timestamp')) . " feed Id " . $feedId, 'product_sync'); // Add logs 
                as_schedule_single_action(time() + 5, 'auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
              } // end products if
            }

            $TVC_Admin_Helper->plugin_log("is_process_start", 'product_sync'); // Add logs
            $feed_data = array(
              "total_product" => $product_count,
              "is_process_start" => true,
              "product_sync_alert" => sanitize_text_field("Product sync process is ready to start"),
            );
            $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
          } else {
            $TVC_Admin_Helper->plugin_log("Data is missing for feed id = " . $feedId, 'product_sync'); // Add logs 
          }
        } else {
          $TVC_Admin_Helper->plugin_log("Empty result for feed id = " . $feedId, 'product_sync'); // Add logs 
        }
      } catch (Exception $e) {
        $feed_data = array(
          "product_sync_alert" => $e->getMessage(),
          "is_process_start" => false,
          "is_auto_sync_start" => false,
          "is_mapping_update" => false,
        );
        $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
        $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
      }

      return true;
    }

    /**
     * Cron used for Feed wise product sync
     * Store data into Database 
     * hook used auto_feed_wise_product_sync_process_scheduler_ee
     * initiated by init_feed_wise_product_sync_process_scheduler hook
     * Database Table used `ee_prouct_pre_sync_data`, `conv_product_sync_data`
     * parameter int $feedId
     */
    function ee_call_auto_feed_wise_product_sync_process($feedId)
    {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $TVC_Admin_Helper->plugin_log("EE Feed wise product sync process Start", 'product_sync');
      $conv_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
      $conv_additional_data['product_sync_alert'] = NULL;
      $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
      $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      $feed_data = array(
        "product_sync_alert" => NULL,
      );
      $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));

      try {
        global $wpdb;
        $where = '`id` = ' . esc_sql($feedId);
        $filed = array(
          'feed_name',
          'channel_ids',
          'is_process_start',
          'auto_sync_interval',
          'auto_schedule',
          'categories',
          'attributes',
          'filters',
          'include_product',
          'exclude_product',
          'is_mapping_update'
        );
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        $TVC_Admin_Helper->plugin_log("EE auto feed wise product sync process start", 'product_sync');
        if (!empty($result) && isset($result[0]['is_process_start']) && $result[0]['is_process_start'] == true) {
          $TVC_Admin_Helper->plugin_log("EE call_batch_wise_auto_sync_product_feed", 'product_sync');
          if (!class_exists('TVCProductSyncHelper')) {
            include ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php';
          }
          $TVCProductSyncHelper = new TVCProductSyncHelper();
          $response = $TVCProductSyncHelper->call_batch_wise_auto_sync_product_feed_ee($feedId);
          if (!empty($response) && isset($response['message'])) {
            $TVC_Admin_Helper->plugin_log("EE Batch wise auto sync process response " . $response['message'], 'product_sync');
          }

          $tablename = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
          $total_pending_pro = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) as a from {$wpdb->prefix}ee_prouct_pre_sync_data where `feedId` = %d AND `status` = 0", $feedId));
          if ($total_pending_pro == 0) {
            // Truncate pre sync table
            $TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);

            $conv_additional_data['is_process_start'] = false;
            $conv_additional_data['is_auto_sync_start'] = true;
            $conv_additional_data['product_sync_alert'] = NULL;
            $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
            $last_sync_date = gmdate('Y-m-d H:i:s', current_time('timestamp'));
            $next_schedule_date = NULL;
            as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
            if ($result[0]['auto_schedule'] == 1) {
              $next_schedule_date = gmdate('Y-m-d H:i:s', strtotime('+' . $result[0]['auto_sync_interval'] . 'day', current_time('timestamp')));
              // add scheduled cron job      
              /***
               * Add recurring cron here
               *  
               * */
              $time_space = strtotime($result[0]['auto_sync_interval'] . " days", 0);
              $timestamp = strtotime($result[0]['auto_sync_interval'] . " days");
              //as_schedule_single_action($next_schedule_date, 'set_recurring_auto_sync_product_feed_wise', array("feedId" => $feedId));
              as_schedule_recurring_action(esc_attr($timestamp), esc_attr($time_space), 'init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId), "product_sync");
            }
            $feed_data = array(
              "product_sync_alert" => NULL,
              "is_process_start" => false,
              "is_auto_sync_start" => true,
              "last_sync_date" => esc_sql($last_sync_date),
              "next_schedule_date" => $next_schedule_date,
            );
            $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
            $TVC_Admin_Helper->plugin_log("EE product sync process done", 'product_sync');
          } else {
            // add scheduled cron job            
            as_schedule_single_action(time() + 5, 'auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
            $TVC_Admin_Helper->plugin_log("EE recall product sync process", 'product_sync');
          }
        } else {
          // add scheduled cron job
          as_unschedule_all_actions('auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
        }
        echo wp_json_encode(array('status' => 'success', "message" => esc_html__("Feed wise product sync process started successfully", "enhanced-e-commerce-for-woocommerce-store")));
        return true;
      } catch (Exception $e) {
        $feed_data = array(
          "product_sync_alert" => $e->getMessage(),
        );
        $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
        $conv_additional_data['product_sync_alert'] = $e->getMessage();
        $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
        $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
        return true;
      }
    }

    function ee_super_AI_feed()
    {
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'create_superFeed_nonce', FILTER_UNSAFE_RAW), 'create_superFeed_nonce_val')) {

        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->plugin_log('Super AI Feed start', 'product_sync');
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        $CustomApi = new CustomApi();
        if (!class_exists('TVCProductSyncHelper')) {
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php');
        }
        $TVCProductSyncHelper = new TVCProductSyncHelper();

        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        // if(isset($google_detail['setting']->conwizenablesuperfeed))
        // {
        //   echo wp_json_encode(array("error" => true, "message" => esc_html__("Super Already Created.", "enhanced-e-commerce-for-woocommerce-store")));
        //   exit;
        // }
        $mappedAttrs = unserialize(get_option('ee_prod_mapped_attrs'));
        $categories = is_array(unserialize(get_option('ee_prod_mapped_cats'))) ? unserialize(get_option('ee_prod_mapped_cats')) : array();
        if ($mappedAttrs == '') {
          $sel_val = get_locale();
          if (strlen($sel_val) > 0) {
            $sel_val = explode('_', $sel_val)[0];
          }
          $mappedAttrs = [
            'id' => 'ID',
            'title' => 'post_title',
            'description' => 'post_excerpt',
            'price' => '_regular_price',
            'sale_price' => '_sale_price',
            'content_language' => $sel_val,
            'availability' => '_stock_status',
            'target_country' => $TVC_Admin_Helper->get_woo_country(),
          ];
          update_option("ee_prod_mapped_attrs", serialize($mappedAttrs));
        }
        $TVC_Admin_Helper->plugin_log('Attribute is mapped', 'product_sync');
        $feed_data = array(
          'feed_name' => esc_sql('Conversios Super Feed'),
          'channel_ids' => esc_sql(1),
          'auto_sync_interval' => esc_sql(25),
          'auto_schedule' => esc_sql(0),
          'created_date' => esc_sql(gmdate('Y-m-d H:i:s', current_time('timestamp'))),
          'status' => esc_sql('Draft'),
          'target_country' => esc_sql($TVC_Admin_Helper->get_woo_country()),
          "categories" => wp_json_encode($categories),
          "attributes" => wp_json_encode($mappedAttrs),
          "is_mapping_update" => true,
          "is_process_start" => false,
          "is_auto_sync_start" => false,
          "product_sync_batch_size" => esc_sql(100),
          "product_sync_alert" => sanitize_text_field("Product sync settings updated successfully"),
          "is_default" => esc_sql(0),
          "is_super_feed" => esc_sql(1)
        );


        $TVC_Admin_DB_Helper->tvc_add_row("ee_product_feed", $feed_data, array("%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%d", "%d", "%d", "%s", "%s", "%d", "%d"));
        $result = $TVC_Admin_DB_Helper->tvc_get_last_row("ee_product_feed", array("id"));
        $TVC_Admin_Helper->plugin_log("Feed data stored in DB", 'product_sync'); // Add logs

        /*******Update feed data in DB end**********************/

        /*******Update feed data in laravel start**********************/
        $feed_data_api = array(
          "store_id" => $google_detail['setting']->store_id,
          "store_feed_id" => $result['id'],
          "map_categories" => wp_json_encode($categories),
          "map_attributes" => wp_json_encode($mappedAttrs),
          "filter" => '',
          "include" => '',
          "exclude" => '',
          "channel_ids" => 1,
          "interval" => 25,
          "is_superfeed" => 1,
        );
        $TVC_Admin_Helper->plugin_log("Feed Data stored in Laravel", 'product_sync'); // Add logs

        $CustomApi->ee_create_product_feed($feed_data_api);
        /*****************Super Feed Product Sync ***************************************/
        $TVC_Admin_Helper->plugin_log('Call laravel API', 'product_sync');
        $isSyncComplete = $TVCProductSyncHelper->superFeedProductSync($result['id']);
        $totalProduct = 0;
        if (isset($isSyncComplete['status']) && $isSyncComplete['status'] == 'success') {
          $last_sync_date = gmdate('Y-m-d H:i:s', current_time('timestamp'));
          $next_schedule_date = NULL;

          $feed_data = array(
            "product_sync_alert" => NULL,
            "is_process_start" => false,
            "is_auto_sync_start" => false,
            "last_sync_date" => esc_sql($last_sync_date),
            "next_schedule_date" => $next_schedule_date,
          );
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $result['id']));
          $where = '`id` = ' . esc_sql($result['id']);
          $filed = array(
            'total_product'
          );
          $data = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
          $totalProduct = $data[0]['total_product'];
          echo wp_json_encode(array('status' => 'success', 'message' => 'Product Sync Done!!!!', 'total_product' => $totalProduct));
        } else {
          $feed_data = array(
            "product_sync_alert" => "Super feed has been initiated",
            "is_process_start" => false,
            "is_auto_sync_start" => false,
            "is_mapping_update" => false,
          );
          $TVC_Admin_Helper->plugin_log("error-2", 'product_sync'); // Add logs
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $result['id']));
          echo wp_json_encode(array('status' => 'error', 'message' => 'Error in Product Sync'));
        }

        exit;
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * Function used for to get TikTok Business Account by subcription id
     * hook used wp_ajax_get_tiktok_business_account
     * Type POST
     * parameter $subcriptionid
     */
    function get_tiktok_business_account()
    {
      $nonce = filter_input(INPUT_POST, 'conversios_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        if (isset($_POST['subscriptionId']) === TRUE && $_POST['subscriptionId'] !== '') {
          $customer_subscription_id['customer_subscription_id'] = sanitize_text_field(wp_unslash($_POST['subscriptionId']));
          $customObj = new CustomApi();
          $result = $customObj->get_tiktok_business_account($customer_subscription_id);
          $tikTokData = [];
          if (isset($result->status) && $result->status === 200 && is_array($result->data) && $result->data != '') {
            foreach ($result->data as $value) {
              if ($value->bc_info->status === 'ENABLE') {
                $tikTokData[$value->bc_info->bc_id] = $value->bc_info->name;
              }
            }

            echo wp_json_encode(array("error" => false, "data" => $tikTokData));
          } else {
            echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
          }
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * Function used for to get TikTok Catalog Id business id
     * hook used wp_ajax_get_tiktok_user_catalogs
     * Type POST
     * parameter $businessId
     */
    // function get_tiktok_user_catalogs()
    // {
    //   if ($this->safe_ajax_call(filter_input(INPUT_POST, 'conversios_onboarding_nonce', FILTER_UNSAFE_RAW), 'conversios_onboarding_nonce')) {
    //     $customer_subscription_id = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
    //     $business_id = isset($_POST['business_id']) ? sanitize_text_field(wp_unslash($_POST['business_id'])) : '';
    //     if ($customer_subscription_id !== '' && $business_id !== '') {
    //       $customer_subscription_id['customer_subscription_id'] = sanitize_text_field(wp_unslash($_POST['customer_subscription_id']));
    //       $customer_subscription_id['business_id'] = isset($_POST['business_id']) ? sanitize_text_field(wp_unslash($_POST['business_id'])) : '';
    //       $customObj = new CustomApi();
    //       $result = $customObj->get_tiktok_user_catalogs($customer_subscription_id);
    //       $tikTokData = [];
    //       if (isset($result->status) && $result->status === 200 && is_array($result->data) && $result->data != '') {            
    //         foreach ($result->data as $key => $value) {
    //           $tikTokData[$value->catalog_conf->country][$value->catalog_id] = $value->catalog_name;
    //         }

    //         foreach ($tikTokData as &$subArray) {
    //           arsort($subArray);
    //         }

    //         echo wp_json_encode(array("error" => false, "data" => $tikTokData));
    //       } else {
    //         echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
    //       }
    //     } else {
    //       echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
    //     }
    //   } else {
    //     echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
    //   }
    //   exit;
    // }


    function get_tiktok_user_catalogs()
    {
      $nonce = filter_input(INPUT_POST, 'conversios_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {        // Initialize as an array instead of a string
        $customer_subscription_id = [];

        // Get sanitized input fields
        $subscription_id = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
        $business_id = isset($_POST['business_id']) ? sanitize_text_field(wp_unslash($_POST['business_id'])) : '';

        // Check if the necessary data is available
        if ($subscription_id !== '' && $business_id !== '') {
          // Assign values to the array
          $customer_subscription_id['customer_subscription_id'] = $subscription_id;
          $customer_subscription_id['business_id'] = $business_id;

          // Call the Custom API
          $customObj = new CustomApi();
          $result = $customObj->get_tiktok_user_catalogs($customer_subscription_id);

          // Process the result
          $tikTokData = [];
          if (isset($result->status) && $result->status === 200 && is_array($result->data) && !empty($result->data)) {
            foreach ($result->data as $key => $value) {
              $tikTokData[$value->catalog_conf->country][$value->catalog_id] = $value->catalog_name;
            }

            // Sort the catalogs by country
            foreach ($tikTokData as &$subArray) {
              arsort($subArray);
            }

            // Return success response
            echo wp_json_encode(array("error" => false, "data" => $tikTokData));
          } else {
            echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
          }
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }


    public function conv_save_tiktokmiddleware($post)
    {
      $nonce = filter_input(INPUT_POST, 'pix_sav_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        if (isset($post['customer_subscription_id']) === TRUE && $post['customer_subscription_id'] !== '' && $post['conv_options_data']['tiktok_setting']['tiktok_business_id'] !== '') {
          $customer_subscription_id['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer_subscription_id['business_id'] = $post['conv_options_data']['tiktok_setting']['tiktok_business_id'];
          $customObj = new CustomApi();
          $result = $customObj->store_business_center($customer_subscription_id);
          return $result;
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        wp_die();
      }
    }
    public function conv_save_tiktokcatalog($post)
    {
      $catArr = [];
      $i = 0;
      $values = array();
      $place_holders = array();

      foreach ($post['conv_catalogData'] as $key => $value) {
        $catArr[$i]["region_code"] = $key;
        $catArr[$i++]["catalog_id"] = $value[0];
        array_push($values, esc_sql($key), esc_sql($value[0]), esc_sql($value[1]), gmdate('Y-m-d H:i:s', current_time('timestamp')));
        $place_holders[] = "('%s', '%s', '%s','%s')";
      }

      $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      global $wpdb;
      $ee_tiktok_catalog = esc_sql($wpdb->prefix . "ee_tiktok_catalog");

      if ($TVC_Admin_DB_Helper->tvc_row_count("ee_tiktok_catalog") > 0) {
        $TVC_Admin_DB_Helper->tvc_safe_truncate_table($ee_tiktok_catalog);
      }
      //Insert tiktok catalog data into db
      $query = "INSERT INTO `$ee_tiktok_catalog` (country, catalog_id, catalog_name, created_date) VALUES ";
      $query .= implode(', ', $place_holders);
      $wpdb->query($wpdb->prepare($query, $values));
      $nonce = filter_input(INPUT_POST, 'pix_sav_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        if (isset($post['customer_subscription_id']) === TRUE && $post['customer_subscription_id'] !== '' && $post['conv_options_data']['tiktok_setting']['tiktok_business_id'] !== '') {
          $customer_subscription_id['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer_subscription_id['business_id'] = $post['conv_options_data']['tiktok_setting']['tiktok_business_id'];
          $customer_subscription_id['catalogs'] = $catArr;
          $customObj = new CustomApi();
          $result = $customObj->store_user_catalog($customer_subscription_id);
          return $result;
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        wp_die();
      }
    }

    public function ee_getCatalogId()
    {
      $nonce = filter_input(INPUT_POST, 'conv_country_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_country_nonce')) {
        if (isset($_POST['countryCode']) === TRUE && $_POST['countryCode'] !== '') {
          $country_code = sanitize_text_field(wp_unslash($_POST['countryCode']));
          global $wpdb;
          $table_name = $wpdb->prefix . 'ee_tiktok_catalog';
          $country_code = $country_code;
          $query = $wpdb->prepare(
            "SELECT catalog_id FROM $table_name WHERE `country` = %s",
            $country_code
          );
          $result = $wpdb->get_results($query, ARRAY_A);
          $catalog_id['catalog_id'] = isset($result[0]['catalog_id']) === TRUE && isset($result[0]['catalog_id']) !== '' ? $result[0]['catalog_id'] : '';
          echo wp_json_encode(array("error" => false, "data" => $catalog_id));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * Function used for to create Pmax Campaign
     * hook used wp_ajax_ee_createPmaxCampaign
     * Type POST
     * parameter POST value
     */
    public function ee_createPmaxCampaign()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        if (isset($_POST['subscription_id']) && sanitize_text_field(wp_unslash($_POST['subscription_id'])) == '') {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Subscription Id is missing. Contact plugin Admin", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        if (!isset($_POST['google_merchant_id']) || $_POST['google_merchant_id'] == '') {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Google Merchant Id is missing. Please map Google Merchant Id.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        if (!isset($_POST['google_ads_id']) || $_POST['google_ads_id'] == '') {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Google Ads Id is missing. Please map Google Ads Id.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        if (!isset($_POST['store_id']) || $_POST['store_id'] == '') {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Store Id is missing. Contact plugin Admin.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        if (isset($_POST['productSource']) === TRUE && $_POST['productSource'] == 'All Product') {

          $post = array(
            'campaign_name' => isset($_POST['campaign_name']) ? sanitize_text_field(wp_unslash($_POST['campaign_name'])) : '',
            'budget' => isset($_POST['budget']) ? sanitize_text_field(wp_unslash($_POST['budget'])) : '',
            'target_country' => isset($_POST['target_country']) ? sanitize_text_field(wp_unslash($_POST['target_country'])) : '',
            'final_url_suffix' => '',
            'start_date' => isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '',
            'end_date' => isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '',
            'merchant_id' => isset($_POST['google_merchant_id']) ? sanitize_text_field(wp_unslash($_POST['google_merchant_id'])) : '',
            'customer_id' => isset($_POST['google_ads_id']) ? sanitize_text_field(wp_unslash($_POST['google_ads_id'])) : '',
            'target_roas' => isset($_POST['target_roas']) && !empty($_POST['target_roas']) ? (float)sanitize_text_field(wp_unslash($_POST['target_roas'])) / 100 : '',
            'status' => isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '',
          );

          if (!class_exists('Conversios_PMax_Helper')) {
            require_once(ENHANCAD_PLUGIN_DIR . 'admin/helper/class-pmax-helper.php');
          }
          $Conversios_PMax_Helper = new Conversios_PMax_Helper();
          $result = $Conversios_PMax_Helper->create_pmax_campaign_callapi($post);
          if (isset($result->error) && $result->error == '') {
            //print_r($api_rs->data);
            if (isset($result->data->results[0]->resourceName) && $result->data != "") {
              $resource_name = $result->data->results[0]->resourceName;
              echo wp_json_encode(array('error' => false, 'message' => "Campaign Created Successfully with resource name - " . $resource_name));
            } else if (isset($result->data)) {
              echo wp_json_encode(array('error' => false, 'data' => $result->data));
            }
          } else {
            $errormsg = "";
            if (!is_array($result->errors) && is_string($result->errors)) {
              $errormsg = $result->errors;
            } else {
              $errormsg = isset($result->errors[0]) ? $result->errors[0] : "";
            }
            echo wp_json_encode(array('error' => true, 'message' => $errormsg,  'status' => $result->status));
          }
        } else {
          $customObj = new CustomApi();
          $post = array(
            "campaign_name" => isset($_POST['campaign_name']) ? sanitize_text_field(wp_unslash($_POST['campaign_name'])) : '',
            "budget" => isset($_POST['budget']) ? sanitize_text_field(wp_unslash($_POST['budget'])) : '',
            "target_country" => isset($_POST['target_country']) ? sanitize_text_field(wp_unslash($_POST['target_country'])) : '',
            "start_date" => isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '',
            "end_date" => isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '',
            "target_roas" => isset($_POST['target_roas']) ? sanitize_text_field(sanitize_text_field(wp_unslash($_POST['target_roas']))) : '',
            "status" => isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '',
            "subscription_id" => isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : '',
            "google_merchant_id" => isset($_POST['google_merchant_id']) ? sanitize_text_field(wp_unslash($_POST['google_merchant_id'])) : '',
            "google_ads_id" => isset($_POST['google_ads_id']) ? sanitize_text_field(wp_unslash($_POST['google_ads_id'])) : '',
            "sync_item_ids" => isset($_POST['sync_item_ids']) ? sanitize_text_field(wp_unslash($_POST['sync_item_ids'])) : '',
            "domain" => isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : '',
            "store_id" => isset($_POST['store_id']) ? sanitize_text_field(wp_unslash($_POST['store_id'])) : '',
            "sync_type" => isset($_POST['sync_type']) ? sanitize_text_field(wp_unslash($_POST['sync_type'])) : '',
          );
          $result = $customObj->createPmaxCampaign($post);
          if (isset($result->data->request_id) && $result->data->request_id !== '') {
            $values = array();
            $place_holders = array();
            global $wpdb;
            $ee_pmax_campaign = esc_sql($wpdb->prefix . "ee_pmax_campaign");
            $place_holders[] = "('%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s')";
            array_push($values, isset($_POST['campaign_name']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['campaign_name']))) : '', isset($_POST['budget']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['budget']))) : '', isset($_POST['target_country']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))) : '', isset($_POST['target_roas']) ? esc_sql(sanitize_text_field(sanitize_text_field(wp_unslash($_POST['target_roas'])))) : '', isset($_POST['start_date']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['start_date']))) : '', isset($_POST['end_date']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['end_date']))) : '', isset($_POST['status']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['status']))) : '', isset($_POST['sync_item_ids']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['sync_item_ids']))) : '', isset($result->data->request_id) ? esc_sql(sanitize_text_field($result->data->request_id)) : '', gmdate('Y-m-d H:i:s', current_time('timestamp')));
            //Insert Campaign data into db
            $query = "INSERT INTO `$ee_pmax_campaign` (campaign_name, daily_budget, target_country_campaign, target_roas, start_date, end_date, status, feed_id, request_id, created_date) VALUES ";
            $query .= implode(', ', $place_holders);
            $wpdb->query($wpdb->prepare($query, $values));
            echo wp_json_encode(array("error" => false, "data" => $result->data));
          } else {
            echo wp_json_encode(array("error" => true, "message" => esc_html($result->error_data[411]->errors[0])));
          }
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    public function ee_createPmaxCampaign_ms()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        if (isset($_POST['subscription_id']) && sanitize_text_field(wp_unslash($_POST['subscription_id'])) == '') {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Subscription Id is missing. Contact plugin Admin", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        /*if (!isset($_POST['ms_catalog_id']) || $_POST['ms_catalog_id'] == '') {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Google Merchant Id is missing. Please map Google Merchant Id.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }*/
        if (!isset($_POST['microsoft_ads_id']) || $_POST['microsoft_ads_id'] == '') {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Microsoft Ads Id is missing!", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        if (isset($_POST['productSource']) === TRUE && $_POST['productSource'] == 'All Product') {

          $customObj = new CustomApi();
          $post = array(
            "name" => isset($_POST['campaign_name']) ? sanitize_text_field(wp_unslash($_POST['campaign_name'])) : '',
            "daily_budget" => isset($_POST['budget']) ? sanitize_text_field(wp_unslash($_POST['budget'])) : '',
            "budget_type" => "DailyBudgetStandard",
            "campaign_type" => "PerformanceMax",
            "languages" => ['English'],
            "time_zone" => "",
            //"target_country" => isset($_POST['target_country']) ? sanitize_text_field(wp_unslash($_POST['target_country'])) : '',
            //"start_date" => isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '',
            //"end_date" => isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '',
            //"target_roas" => isset($_POST['target_roas']) ? sanitize_text_field(sanitize_text_field(wp_unslash($_POST['target_roas']))) : '',
            "status" => isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '',
            "customer_subscription_id" => isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : '',
            //"ms_catalog_id" => isset($_POST['ms_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['ms_catalog_id'])) : '',
            "customer_id" => isset($_POST['microsoft_ads_id']) ? sanitize_text_field(wp_unslash($_POST['microsoft_ads_id'])) : '',
            "account_id" => isset($_POST['microsoft_ads_sub_id']) ? sanitize_text_field(wp_unslash($_POST['microsoft_ads_sub_id'])) : '',
            //"sync_item_ids" => isset($_POST['sync_item_ids']) ? sanitize_text_field(wp_unslash($_POST['sync_item_ids'])) : '',
            //"sync_type" => isset($_POST['sync_type']) ? sanitize_text_field(wp_unslash($_POST['sync_type'])) : '',
          );
          //$post['customer_id'] = 252077508; // 
          //$post['account_id'] = 180741369; // 
          $result = $customObj->createPmaxCampaign_ms($post);

          if (isset($result->data) && $result->data !== '') {
            if (isset($result->data) && $result->data != "") {
              echo wp_json_encode(array('error' => false, 'data' => $result->data));
            } else {
              echo wp_json_encode(array('error' => true, 'message' => wp_json_encode($result)));
            }
          } else {
            if (isset($result->error_data) && $result->error_data != "") {
              echo wp_json_encode(array('error' => true, 'message' => wp_json_encode($result->error_data)));
            } else {
              echo wp_json_encode(array('error' => true, 'message' => wp_json_encode($result)));
            }
          }
        } else {
          $customObj = new CustomApi();
          $post = array(
            "campaign_name" => isset($_POST['campaign_name']) ? sanitize_text_field(wp_unslash($_POST['campaign_name'])) : '',
            "budget" => isset($_POST['budget']) ? sanitize_text_field(wp_unslash($_POST['budget'])) : '',
            //"target_country" => isset($_POST['target_country']) ? sanitize_text_field(wp_unslash($_POST['target_country'])) : '',
            //"start_date" => isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '',
            //"end_date" => isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '',
            //"target_roas" => isset($_POST['target_roas']) ? sanitize_text_field(sanitize_text_field(wp_unslash($_POST['target_roas']))) : '',
            "status" => isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '',
            "customer_subscription_id" => isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : '',
            //"ms_catalog_id" => isset($_POST['ms_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['ms_catalog_id'])) : '',
            "microsoft_ads_id" => isset($_POST['microsoft_ads_id']) ? sanitize_text_field(wp_unslash($_POST['microsoft_ads_id'])) : '',
            "microsoft_ads_sub_id" => isset($_POST['microsoft_ads_sub_id']) ? sanitize_text_field(wp_unslash($_POST['microsoft_ads_sub_id'])) : '',
            "sync_item_ids" => isset($_POST['sync_item_ids']) ? sanitize_text_field(wp_unslash($_POST['sync_item_ids'])) : '',
            //"sync_type" => isset($_POST['sync_type']) ? sanitize_text_field(wp_unslash($_POST['sync_type'])) : '',
          );
          $result = $customObj->createPmaxCampaign_ms($post);
          if (isset($result->data) && $result->data !== '') {

            /*$values = array();
            $place_holders = array();
            global $wpdb;
            $ee_pmax_campaign = esc_sql($wpdb->prefix . "ee_pmax_campaign");
            $place_holders[] = "('%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s')";
            array_push($values, isset($_POST['campaign_name']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['campaign_name']))) : '', isset($_POST['budget']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['budget']))) : '', isset($_POST['target_country']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))) : '', isset($_POST['target_roas']) ? esc_sql(sanitize_text_field(sanitize_text_field(wp_unslash($_POST['target_roas'])))) : '', isset($_POST['start_date']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['start_date']))) : '', isset($_POST['end_date']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['end_date']))) : '', isset($_POST['status']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['status']))) : '', isset($_POST['sync_item_ids']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['sync_item_ids']))) : '', isset($result->data->request_id) ? esc_sql(sanitize_text_field($result->data->request_id)) : '', gmdate('Y-m-d H:i:s', current_time('timestamp')));
            //Insert Campaign data into db
            $query = "INSERT INTO `$ee_pmax_campaign` (campaign_name, daily_budget, target_country_campaign, target_roas, start_date, end_date, status, feed_id, request_id, created_date) VALUES ";
            $query .= implode(', ', $place_holders);
            $wpdb->query($wpdb->prepare($query, $values));*/

            if (isset($result->data) && $result->data != "") {
              echo wp_json_encode(array('error' => false, 'data' => $result->data));
            } else {
              echo wp_json_encode(array('error' => true, 'message' => wp_json_encode($result)));
            }
          } else {
            echo wp_json_encode(array('error' => true, 'message' => wp_json_encode($result)));
          }
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }
    public function get_pf_accordian_data()
    {
      if (isset($_POST['conv_licence_nonce']) && is_admin() && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['conv_licence_nonce'])), 'conv_lic_nonce')) {
        $ee_prod_mapped_attrs = get_option("ee_prod_mapped_attrs");
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->get_feed_status();
        $feed_data = $TVC_Admin_Helper->ee_get_result_limit('ee_product_feed', 2);
        $feed_count = !empty($feed_data) ? count($feed_data) : 0;
        echo wp_json_encode(array("feed_count" => $feed_count, "ee_prod_mapped_attrs" => unserialize($ee_prod_mapped_attrs), 'feed_data' => $feed_data));
      }
      exit;
    }
    public function get_category_for_filter()
    {
      $nonce = filter_input(INPUT_POST, 'get_category_for_filter', FILTER_UNSAFE_RAW);
      if (isset($_POST['get_category_for_filter']) && ($nonce && wp_verify_nonce($nonce, 'get_category_for_filter-nonce'))) {
        if (isset($_POST['type'])) {
          switch ($_POST['type']) {
            case 'category':
              $tvc_admin_helper = new TVC_Admin_Helper();
              $category = $tvc_admin_helper->get_tvc_product_cat_list_with_name();
              echo wp_json_encode($category);
              break;
            case 'all_product':
              $total_products = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish']))->found_posts;
              echo wp_json_encode($total_products);
              break;
            case 'total_synced_product_count':
              $total_products = get_option('ee_conv_total_synced_product_count');
              if (empty($total_products)) {
                global $wpdb;
                $table = 'ee_product_feed';
                $tablename = esc_sql($wpdb->prefix . $table);
                $sql = "select SUM(total_synced_product_count) AS total_synced_products from `$tablename` WHERE status = 'Synced'";
                $results =  $wpdb->get_results($sql);
                if (isset($results[0])) {
                  $total_products = $results[0]->total_synced_products;
                } else {
                  $total_products = 0; // if no data found then say limit is over
                }
              }
              echo json_encode($total_products);
              break;
            case "getAllChannel":
              $arrData = array();
              $ee_options = unserialize(get_option("ee_options"));
              $fb_catalog_id = isset($ee_options['facebook_setting']['fb_catalog_id']) === TRUE ? $ee_options['facebook_setting']['fb_catalog_id'] : '';
              $google_merchant_center_id = isset($ee_options['google_merchant_center_id']) === TRUE ? $ee_options['google_merchant_center_id'] : '';
              $arrData['fb_catalog_id'] = $fb_catalog_id;
              $arrData['google_merchant_center_id'] = $google_merchant_center_id;
              $arrData['tiktok_setting'] = 0;
              if (isset($ee_options['tiktok_setting']['tiktok_business_id'])) {
                $country_code = isset($_POST['target_country']) ? sanitize_text_field(wp_unslash($_POST['target_country'])) : '';
                $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
                $where = '`country` = "' . esc_sql($country_code) . '"';
                $filed = array('catalog_id');
                $arrData['tiktok_setting'] = 1;
                $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_tiktok_catalog", $where, $filed);
                $arrData['titkok_catalog_id'] = isset($result[0]['catalog_id']) === TRUE && isset($result[0]['catalog_id']) !== '' ? $result[0]['catalog_id'] : '';
              }
              echo wp_json_encode(array("error" => false, "data" => $arrData));
              break;
            case "getProgressCount":
              $tvc_admin_helper = new TVC_Admin_Helper();
              $feed_data = $tvc_admin_helper->ee_get_result_limit('ee_product_feed', 1);
              $feed_count = !empty($feed_data) ? count($feed_data) : 0;
              $ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
              $ee_prod_mapped_cats = unserialize(get_option('ee_prod_mapped_cats'));
              $isAttrMapped = 0;
              if ($ee_mapped_attrs) {
                $isAttrMapped = 1;
              }
              $iscatMapped = 0;
              if ($ee_prod_mapped_cats) {
                $iscatMapped = 1;
              }
              $ee_options = unserialize(get_option("ee_options"));
              $is_channel_connected = 0;
              if (isset($ee_options['google_merchant_center_id']) && $ee_options['google_merchant_center_id'] !== '') {
                $is_channel_connected = 1;
              }
              if (isset($ee_options['facebook_setting']) && $ee_options['facebook_setting']['fb_business_id'] !== '' && $is_channel_connected == 0) {
                $is_channel_connected = 1;
              }
              if (isset($ee_options['tiktok_setting']) && $ee_options['tiktok_setting']['tiktok_business_id'] !== '' && $is_channel_connected == 0) {
                $is_channel_connected = 1;
              }
              echo wp_json_encode(array("error" => false, "feed_count" => $feed_count, "isAttrMapped" => $isAttrMapped, "is_channel_connected" => $is_channel_connected, "iscatMapped" => $iscatMapped));
              break;
            case "getFeedList":
              $tvc_admin_helper = new TVC_Admin_Helper();
              $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
              $where = '`status` IN ("Synced","Draft", "In Progress","Failed")';
              $filed = array(
                'id',
                'feed_name',
                'total_product',
                'status'
              );
              $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array_feed("ee_product_feed", $where, $filed);
              $ee_options = unserialize(get_option("ee_options"));
              $google_ads_id = '';
              $currency_symbol = '';
              if (isset($ee_options['google_ads_id']) === TRUE && $ee_options['google_ads_id'] !== '') {
                $google_ads_id = $ee_options['google_ads_id'];
                $PMax_Helper = new Conversios_PMax_Helper();
                $currency_code_rs = $PMax_Helper->get_campaign_currency_code($google_ads_id);
                if (isset($currency_code_rs->data->currencyCode)) {
                  $currency_code = $currency_code_rs->data->currencyCode;
                  $currency_symbol = $tvc_admin_helper->get_currency_symbols($currency_code);
                }
              }

              $href = esc_url_raw('admin.php?page=conversios&wizard=productFeedOdd'); //Odd
              echo wp_json_encode(array("data" => $result, "currency_symbol" => $currency_symbol, "href" => $href));
              break;
            case "get_campaign_accordan":
              $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
              $count = $TVC_Admin_DB_Helper->tvc_row_count('ee_pmax_campaign');
              echo wp_json_encode(array("count" => $count));
              break;
            case "getProgressCount_campaign":
              $ee_options = unserialize(get_option("ee_options"));
              $is_channel_connected = false;
              if (isset($ee_options['google_merchant_center_id']) && $ee_options['google_merchant_center_id'] !== '' && isset($ee_options['google_ads_id']) && $ee_options['google_ads_id'] !== '') {
                $is_channel_connected = true;
              }
              $tvc_admin_helper = new TVC_Admin_Helper();
              $feed_data = $tvc_admin_helper->ee_get_result_limit('ee_product_feed', 2);
              $feed_count = !empty($feed_data) ? count($feed_data) : 0;
              echo wp_json_encode(array("feed_count" => $feed_count, "is_channel_connected" => $is_channel_connected));
              break;
          }
        }
        exit;
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }
    public function get_attribute_mappingv_div()
    {
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'fb_business_nonce', FILTER_UNSAFE_RAW), 'fb_business_nonce')) {
        if (!class_exists('TVCProductSyncHelper')) {
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php');
        }
        $TVCProductSyncHelper = new TVCProductSyncHelper();
        $wooCommerceAttributes = array_map("unserialize", array_unique(array_map("serialize", $TVCProductSyncHelper->wooCommerceAttributes())));
        $ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
        $tempAddAttr = $ee_mapped_attrs;
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $gmcAttributes = $TVC_Admin_Helper->get_gmcAttributes();
        $html = '';
        $path = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/iso_lang.json';
        //$str = file_get_contents($path);

        global $wp_filesystem;
        $str = $wp_filesystem->get_contents($path);

        $countries_list = $str ? json_decode($str, true) : [];
        foreach ($gmcAttributes as $key => $attribute) {
          if (is_array($tempAddAttr)) {
            unset($tempAddAttr[$attribute["field"]]);
          }
          $html .= '<div class="col-6 mt-2" style="text-align: left">
                  <span class="ps-3 fw-400 text-color fs-12">
                      ' . esc_attr($attribute["field"]) . '' . (isset($attribute["required"]) === TRUE && esc_attr($attribute["required"]) === '1' ? '<span class="text-color fs-6"> *</span>' : "") . '
                      <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                          data-bs-placement="right"
                          title="' . (isset($attribute['desc']) === TRUE ? esc_attr($attribute['desc']) : '') . '">
                          info
                      </span>
                  </span>
              </div><div class="col-5 mt-2">';
          $sel_val = "";
          $ee_select_option = $TVC_Admin_Helper->add_additional_option_in_tvc_select($wooCommerceAttributes, $attribute["field"]);
          $require = FALSE;
          if (isset($attribute['required']) === TRUE) {
            $require = TRUE;
          }
          $sel_val_def = "";
          if (isset($attribute['wAttribute']) === TRUE) {
            $sel_val_def = $attribute['wAttribute'];
          }
          if ($attribute["field"] === 'link') {
            "product link";
          } else if ($attribute["field"] === 'shipping') {
            $sel_val = esc_attr($sel_val_def);
            if (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) {
              $sel_val = esc_attr($ee_mapped_attrs[$attribute["field"]]);
            }
            $html .= '<input style="width:100%;" type="number" min="0" name="' . esc_attr($attribute["field"]) . '" class="form-control from-control-overload fw-light text-secondary fs-6 tvc-text" id="" placeholder="' . esc_attr('Add shipping flat rate') . '" value="' . esc_attr($sel_val) . '">';
          } else if ($attribute["field"] === 'tax') {
            $sel_val = esc_attr($sel_val_def);
            if (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) {
              $sel_val = esc_attr($ee_mapped_attrs[$attribute["field"]]);
            }
            $html .= '<input style="width:100%;" type="number" min="0" name="' . esc_attr($attribute["field"]) . '" class="form-control from-control-overload fw-light text-secondary fs-6 tvc-text" id="" placeholder="' . esc_attr('Add TAX flat (%)') . '" value="' . esc_attr($sel_val) . '">';
          } else if ($attribute["field"] === 'content_language') {
            $sel_val = get_locale();
            if (strlen($sel_val) > 0) {
              $sel_val = explode('_', $sel_val)[0];
            }

            $requiredtext = '';
            if ($require == true) {
              $requiredtext = 'field-required';
            }
            $html .= '<select style="width: 100%" class="fw-light text-secondary fs-6 form-control form-select-sm select_modal content_language ' . $requiredtext . '" name="' . $attribute["field"] . '" id="content_language">
                          <option value="">Please Select Attribute</option>';
            foreach ($countries_list as $Key => $val) {
              $selText = '';
              if ($val["code"] == $sel_val) {
                $selText = 'selected';
              }
              $html .= '<option value="' . esc_attr($val["code"]) . '" ' . $selText . ' > ' . esc_html($val["name"]) . " (" . esc_attr($val["native_name"]) . ")" . '</option>';
            }
            $html .= '</select>';
          } else if ($attribute["field"] === 'target_country') {
            $store_raw_country = get_option('woocommerce_default_country');
            $country = explode(":", $store_raw_country);
            $sel_val = (isset($country[0])) ? $country[0] : "";
            $requiredtext = '';
            if ($require == true) {
              $requiredtext = 'field-required';
            }
            //$countries_list = $this->get_gmc_countries_list();
            $path = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/countries.json';
            //$str = file_get_contents($path);

            global $wp_filesystem;
            $str = $wp_filesystem->get_contents($path);

            $countries_list = $str ? json_decode($str, true) : [];
            //$sel_val = $this->get_woo_country();
            $html .= '<select style="width: 100%" class="fw-light text-secondary fs-6 form-control form-select-sm select_modal target_country ' . $requiredtext . '" name="' . $attribute["field"] . '" id="target_country">
                          <option value="">Please Select Attribute</option>';
            foreach ($countries_list as $Key => $val) {
              $selText = '';
              if ($val["code"] == $sel_val) {
                $selText = 'selected';
              }
              $html .= '<option value="' . esc_attr($val["code"]) . '" ' . $selText . '>' . esc_html($val["name"]) . '</option>';
            }
            $html .= '</select>';
          } else {
            if (isset($attribute['fixed_options']) === TRUE && $attribute['fixed_options'] !== "") {
              $ee_select_option_t = explode(",", $attribute['fixed_options']);
              $ee_select_option = [];
              foreach ($ee_select_option_t as $o_val) {
                $ee_select_option[]['field'] = esc_attr($o_val);
              }

              $sel_val = $sel_val_def;
              $requiredtext = '';
              if ($require == true) {
                $requiredtext = 'field-required';
              }
              if (!empty($ee_select_option) && $attribute["field"]) {
                $html .= '<select style="width: 100%" class="fw-light text-secondary fs-6 form-control form-select-sm select_modal ' . $attribute["field"] . ' ' . $requiredtext . '" name="' . $attribute["field"] . '" id="' . $attribute["field"] . '">';
                $html .= '<option value="">Please Select Attribute</option>';
                foreach ($ee_select_option as $Key => $val) {
                  $selText = '';
                  if ($val["field"] == $sel_val) {
                    $selText = 'selected';
                  }
                  $html .= '<option value="' . esc_attr($val["field"]) . '" ' . $selText . '>' . esc_html($val["field"]) . '</option>';
                }
                if ($attribute["field"] == 'brand') {
                  $html .= '<option value="product_cat" ' . ("product_cat" == $sel_val) ? "selected" : "" . '>product_cat</option>';
                }
              }
              $html .= '</select>';
            } else {
              $sel_val = esc_attr($sel_val_def);
              if (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) {
                $sel_val = esc_attr($ee_mapped_attrs[$attribute["field"]]);
              }
              $requiredtext = '';
              if ($require == true) {
                $requiredtext = 'field-required';
              }
              if (!empty($ee_select_option) && $attribute["field"]) {
                $html .= '<select style="width: 100%" class="fw-light text-secondary fs-6 form-control form-select-sm select_modal ' . $attribute["field"] . ' ' . $requiredtext . '" name="' . $attribute["field"] . '" id="' . $attribute["field"] . '">';
                $html .= '<option value="">Please Select Attribute</option>';
                foreach ($ee_select_option as $Key => $val) {
                  $selText = '';
                  if ($val["field"] == $sel_val) {
                    $selText = 'selected';
                  }
                  $html .= '<option value="' . esc_attr($val["field"]) . '" ' . $selText . '>' . esc_html($val["field"]) . '</option>';
                }
                if ($attribute["field"] == 'brand') {
                  $html .= '<option value="product_cat" ' . ("product_cat" == $sel_val) ? "selected" : "" . '>product_cat</option>';
                }
              }
              $html .= '</select>';
            }
          }
          $html .= '</div>';
        }
        $html .= '<div class="col-12 m-0 p-0 additinal_attr_main_div">';
        $cnt = 0;
        if (!empty($tempAddAttr)) {
          $additionalAttribute = array(
            'condition',
            'shipping_weight',
            'product_weight',
            'gender',
            'sizes',
            'color',
            'age_group',
            'additional_image_links',
            'sale_price_effective_date',
            'material',
            'pattern',
            'product_types',
            'availability_date',
            'expiration_date',
            'adult',
            'ads_redirect',
            'shipping_length',
            'shipping_width',
            'shipping_height',
            'custom_label_0',
            'custom_label_1',
            'custom_label_2',
            'custom_label_3',
            'custom_label_4',
            'mobile_link',
            'energy_efficiency_class',
            'is_bundle',
            'loyalty_points',
            'unit_pricing_measure',
            'unit_pricing_base_measure',
            'promotion_ids',
            'shipping_label',
            'excluded_destinations',
            'included_destinations',
            'tax_category',
            'multipack',
            'installment',
            'min_handling_time',
            'max_handling_time',
            'min_energy_efficiency_class',
            'max_energy_efficiency_class',
            'identifier_exists',
            'cost_of_goods_sold'
          );
          $count_arr = count($additionalAttribute);
          foreach ($tempAddAttr as $key => $value) {
            $options = '<option>Please Select Attribute</option>';
            foreach ($additionalAttribute as $val) {
              $selected = "";
              $disabled = "";
              if ($val == $key) {
                $selected = "selected";
              } else {
                if (array_key_exists($val, $tempAddAttr)) {
                  $disabled = "disabled";
                }
              }

              $options .= '<option value="' . $val . '" ' . $selected . ' ' . $disabled . '>' . $val . '</option>';
            }
            $option1 = '<option>Please Select Attribute</option>';
            $fixed_att_select_list = ["gender", "age_group", "condition", "adult", "is_bundle", "identifier_exists"];
            if (in_array($key, $fixed_att_select_list)) {
              if ($key == 'gender') {
                $gender = ['male' => 'Male', 'female' => 'Female', 'unisex' => 'Unisex'];
                foreach ($gender as $genKey => $genVal) {
                  $selected = "";
                  if ($genKey == $value) {
                    $selected = "selected";
                  }
                  $option1 .= '<option value="' . $genKey . '" ' . $selected . '>' . $genVal . '</option>';
                }
              }
              if ($key == 'condition') {
                $conArr = ['new' => 'New', 'refurbished' => 'Refurbished', 'used' => 'Used'];
                foreach ($conArr as $conKey => $conVal) {
                  $selected = "";
                  if ($conKey == $value) {
                    $selected = "selected";
                  }
                  $option1 .= '<option value="' . $conKey . '" ' . $selected . '>' . $conVal . '</option>';
                }
              }
              if ($key == 'age_group') {
                $ageArr = ['newborn' => 'Newborn', 'infant' => 'Infant', 'toddler' => 'Toddler', 'kids' => 'Kids', 'adult' => 'Adult'];
                foreach ($ageArr as $ageKey => $ageVal) {
                  $selected = "";
                  if ($ageKey == $value) {
                    $selected = "selected";
                  }
                  $option1 .= '<option value="' . $ageKey . '" ' . $selected . '>' . $ageVal . '</option>';
                }
              }
              if ($key == 'adult' || $key == 'is_bundle' || $key == 'identifier_exists') {
                $boolArr = ['yes' => 'Yes', 'no' => 'No'];
                foreach ($boolArr as $boolKey => $boolVal) {
                  $selected = "";
                  if ($boolKey == $value) {
                    $selected = "selected";
                  }
                  $option1 .= '<option value="' . $boolKey . '" ' . $selected . '>' . $boolVal . '</option>';
                }
              }
            } else {
              foreach ($wooCommerceAttributes as $valattr) {
                $selected = "";
                if ($valattr['field'] == $value) {
                  $selected = "selected";
                }
                $option1 .= '<option value="' . $valattr['field'] . '" ' . $selected . '>' . $valattr['field'] . '</option>';
              }
            }
            $html .= '<div class="row additinal_attr_div m-0 p-0" ><div class="col-6 mt-2">';
            $html .= '<select style="width:100%" id="' . $cnt++ . '" name="additional_attr_[]" class="additinal_attr fw-light text-secondary fs-6 form-control form-select-sm select_modal select2-hidden-accessible">';
            $html .= $options;
            $html .= '</select></div>';
            $html .= '<div class="col-5 mt-2">';
            $html .= '<select style="width:100%" id="" name="additional_attr_value_[]" class="additional_attr_value fw-light text-secondary fs-6 form-control form-select-sm select_modal select2-hidden-accessible">';
            $html .= $option1;
            $html .= '</select></div>';
            $html .= '<div class="col-1 mt-2"><span class="material-symbols-outlined text-danger remove_additional_attr fs-5 mt-2" title="Add Additional Attribute" style="cursor: pointer; margin-right:35px;">delete</span></div></div>';
          }
        }
        $cntTxt = '';
        if (isset($count_arr) && $count_arr == $cnt) {
          $cntTxt = 'd-none';
        }
        $html .= '</div><div class="row add_additional_attr_div m-0 p-0" >
                <div class="add_additional_attr_div mt-2" style="display: flex; justify-content: start">
                <button type="button" class="px-5 fs-12 btn btn-primary add_additional_attr ' . $cntTxt . '" title="Add Attribute"> Add Attributes
                </button><input type="hidden" name="cnt" id="cnt" value="' . $cnt . '"></div></div>';
        //echo $html;

        echo wp_kses($html, array(
          "div" => array(
            'class' => array(),
            'style' => array(),
            'id' => array(),
            'title' => array(),
          ),
          "button" => array(
            'type' => array(),
            'class' => array(),
            'style' => array(),
            'id' => array(),
            'title' => array(),
          ),
          "option" => array(
            'value' => array(),
            'selected' => array(),
          ),
          "span" => array(
            'class' => array(),
            'style' => array(),
            'id' => array(),
            'title' => array(),
            'data-bs-toggle' => array(),
            'data-bs-placement' => array(),
          ),
          "input" => array(
            'type' => array(),
            'name' => array(),
            'class' => array(),
            'id' => array(),
            'placeholder' => array(),
          ),
          "select" => array(
            'name' => array(),
            'class' => array(),
            'id' => array(),
          ),
          "form" => array(
            'method' => array(),
            'class' => array(),
            'id' => array(),
            'name' => array(),
          ),
        ));
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }
    public function get_product_filter_count()
    {
      $nonce = filter_input(INPUT_POST, 'getFilterCount', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'getFilterCount-nonce')) {
        global $wpdb;
        $where = array();
        $wherePriJoin = $whereSKUJoin = $conditionprod = $condition = $conditionSKU = $conditionContent = $conditionExcerpt = $conditionPrice = $conditionRegPrice = $whereStockJoin = $conditionStock = '';
        $product_cat1 = $product_cat2 = $product_id1 = $product_id2 = $whereCond = $whereCondsku = $whereCondcontent = $whereExcerpt = $whereCondregPri = $whereCondPri = $wherestock = array();


        $productSearch = isset($_POST['productVal']) ? explode(',', sanitize_text_field(wp_unslash($_POST['productVal']))) : array();
        $conditionSearch = isset($_POST['conditionVal']) ? explode(',', sanitize_text_field(wp_unslash($_POST['conditionVal']))) : array();
        $valueSearch = isset($_POST['valueVal']) ? explode(',', sanitize_text_field(wp_unslash($_POST['valueVal']))) : array();

        $in_category_ids = array();
        $not_in_category_ids = array();
        $stock_status_to_fetch = array();
        $not_stock_status_to_fetch = $product_ids_to_exclude = $product_ids_to_include = $search = array();
        foreach ($productSearch as $key => $value) {
          switch ($value) {
            case 'product_cat':
              if ($conditionSearch[$key] == "=") {
                array_push($in_category_ids, $valueSearch[$key]);
              } else if ($conditionSearch[$key] == "!=") {
                array_push($not_in_category_ids, $valueSearch[$key]);
              }
              break;
            case '_stock_status':
              if (!empty($conditionSearch[$key]) && $conditionSearch[$key] == "=") {
                array_push($stock_status_to_fetch, $valueSearch[$key]);
              } else if (!empty($conditionSearch[$key]) && $conditionSearch[$key] == "!=") {
                array_push($not_stock_status_to_fetch, $valueSearch[$key]);
              }
              break;
            case 'ID':
              if ($conditionSearch[$key] == "=") {
                array_push($product_ids_to_include, $valueSearch[$key]);
              } else if ($conditionSearch[$key] == "!=") {
                array_push($product_ids_to_exclude, $valueSearch[$key]);
              }
              break;
          }
        }

        $tax_query = array();
        if (!empty($in_category_ids)) {
          $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $in_category_ids,
            'operator' => 'IN', // Retrieve products in any of the specified categories
          );
        }
        if (!empty($not_in_category_ids)) {
          $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $not_in_category_ids,
            'operator' => 'NOT IN', // Exclude products in any of the specified categories
          );
        }
        if (!empty($in_category_ids) && !empty($not_in_category_ids)) {
          $tax_query = array('relation' => 'AND');
        }
        $meta_query = array();
        if (!empty($stock_status_to_fetch)) {
          $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => $stock_status_to_fetch,
            'compare' => 'IN', // Include products with these stock statuses
          );
        }

        // Add not_stock_status_to_fetch condition
        if (!empty($not_stock_status_to_fetch)) {
          $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => $not_stock_status_to_fetch,
            'compare' => 'NOT IN', // Exclude products with these stock statuses
          );
        }
        if (!empty($stock_status_to_fetch) && !empty($not_stock_status_to_fetch)) {
          $meta_query = array('relation' => 'AND');
        }

        $args = array(
          'post_type'      => 'product',
          'post_status'    => 'publish',
          's'              => $search,
          'tax_query'      => $tax_query, // Dynamic tax query
          'meta_query'     => $meta_query,
          'post__not_in'   => $product_ids_to_exclude,
          'post__in'       => $product_ids_to_include,
        );

        $pagination_count  = (new WP_Query($args))->found_posts;

        wp_reset_postdata();
        echo wp_json_encode($pagination_count);
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }
    public function create_dashboard_feed_data()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        //$this->ee_call_start_feed_wise_product_sync_process(4);
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        $channel_id = array();
        if (isset($_POST['google_merchant_center']) && $_POST['google_merchant_center'] == 1) {
          $channel_id['google_merchant_center'] = sanitize_text_field(wp_unslash($_POST['google_merchant_center']));
        }
        if (isset($_POST['fb_catalog_id']) && $_POST['fb_catalog_id'] == 2) {
          $channel_id['fb_catalog_id'] = sanitize_text_field(wp_unslash($_POST['fb_catalog_id']));
        }
        if (isset($_POST['tiktok_id']) && $_POST['tiktok_id'] == 3) {
          $channel_id['tiktok_id'] = sanitize_text_field(wp_unslash($_POST['tiktok_id']));
        }
        if (isset($_POST['microsoft_merchant_center']) && $_POST['microsoft_merchant_center'] == 4) {
          $channel_id['microsoft_merchant_center'] = sanitize_text_field(wp_unslash($_POST['microsoft_merchant_center']));
        }

        $channel_ids = implode(',', $channel_id);

        $tiktok_catalog_id = '';
        if (isset($_POST['tiktok_catalog_id']) === TRUE && $_POST['tiktok_catalog_id'] !== '') {
          $tiktok_catalog_id = sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id']));
        }
        /**
         * Check catalog id available
         */
        if (isset($_POST['tiktok_catalog_id']) === TRUE && $_POST['tiktok_catalog_id'] === 'Create New') {
          /**
           * Create catalog id
           */
          global $wp_filesystem;
          $getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries_currency.json");

          $contData = json_decode($getCountris);
          $currency_code = '';
          foreach ($contData as $key => $data) {
            if (isset($_POST['target_country']) && $data->countryCode === $_POST['target_country']) {
              $currency_code = $data->currencyCode;
            }
          }
          $customer['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer['business_id'] = isset($_POST['tiktok_business_account']) ? sanitize_text_field(wp_unslash($_POST['tiktok_business_account'])) : '';
          $customer['catalog_name'] = isset($_POST['feedName']) ? sanitize_text_field(wp_unslash($_POST['feedName'])) : '';
          $customer['region_code'] = isset($_POST['target_country']) ? sanitize_text_field(wp_unslash($_POST['target_country'])) : '';
          $customer['currency'] = isset($currency_code) ? sanitize_text_field($currency_code) : '';
          $customObj = new CustomApi();
          $result = $customObj->createCatalogs($customer);
          if (isset($result->error_data) === TRUE) {
            foreach ($result->error_data as $key => $value) {
              echo wp_json_encode(array("error" => true, "message" => $value->errors[0], "errorType" => "tiktok"));
              exit;
            }
          }

          if (isset($result->status) === TRUE && $result->status === 200) {
            $tiktok_catalog_id = $result->data->catalog_id;
            $values = array();
            $place_holders = array();
            global $wpdb;
            $ee_tiktok_catalog = esc_sql($wpdb->prefix . "ee_tiktok_catalog");
            array_push($values, esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))), esc_sql($tiktok_catalog_id), esc_sql(sanitize_text_field(wp_unslash($_POST['feedName']))), gmdate('Y-m-d H:i:s', current_time('timestamp')));
            $place_holders[] = "('%s', '%s', '%s','%s')";
            $query = "INSERT INTO `$ee_tiktok_catalog` (country, catalog_id, catalog_name, created_date) VALUES ";
            $query .= implode(', ', $place_holders);
            $wpdb->query($wpdb->prepare($query, $values));
          }
        }

        $mappedAttrs = false;
        $categories = is_array(unserialize(get_option('ee_prod_mapped_cats'))) ? unserialize(get_option('ee_prod_mapped_cats')) : false;
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        $product_selection = isset($_POST['product_selection']) ? sanitize_text_field(wp_unslash($_POST['product_selection'])) : '';
        if ($product_selection !== 'specific_product') {
          $mappedAttrs = unserialize(get_option('ee_prod_mapped_attrs'));
          if ($mappedAttrs == '') {
            $sel_val = get_locale();
            if (strlen($sel_val) > 0) {
              $sel_val = explode('_', $sel_val)[0];
            }
            $mappedAttrs = [
              'id' => 'ID',
              'title' => 'post_title',
              'description' => 'post_excerpt',
              'price' => '_regular_price',
              'sale_price' => '_sale_price',
              'gtin' => '',
              'mpn' => '',
              'tax' => '',
              'content_language' => $sel_val,
              'availability' => '_stock_status',
              'condition' => 'new',
            ];
            update_option("ee_prod_mapped_attrs", serialize($mappedAttrs));
          }
        }

        $productFilter = isset($_POST['productVal']) && $_POST['productVal'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['productVal']))) : '';
        $conditionFilter = isset($_POST['conditionVal']) && $_POST['conditionVal'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['conditionVal']))) : '';
        $valueFilter = isset($_POST['valueVal']) && $_POST['valueVal'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['valueVal']))) : '';
        $filters = array();
        if (!empty($productFilter)) {
          foreach ($productFilter as $key => $val) {
            $filters[$key]['attr'] = sanitize_text_field($val);
            $filters[$key]['condition'] = sanitize_text_field($conditionFilter[$key]);
            $filters[$key]['value'] = sanitize_text_field($valueFilter[$key]);
          }
        }
        $profile_data = array(
          'feed_name' => isset($_POST['feedName']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['feedName']))) : '',
          'channel_ids' => esc_sql($channel_ids),
          'auto_sync_interval' => isset($_POST['autoSyncIntvl']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : '',
          'auto_schedule' => isset($_POST['autoSync']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSync']))) : '',
          'created_date' => esc_sql(gmdate('Y-m-d H:i:s', current_time('timestamp'))),
          'status' => strpos($channel_ids, '1') !== false ? esc_sql('Draft') : '',
          'target_country' => isset($_POST['target_country']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))) : '',
          'categories' => $categories !== false ? wp_json_encode($categories) : null,
          'attributes' => $mappedAttrs !== false ? wp_json_encode($mappedAttrs) : $mappedAttrs,
          'filters' => wp_json_encode($filters),
          'fb_status' => strpos($channel_ids, '2') !== false ? esc_sql('Draft') : '',
          'tiktok_catalog_id' => esc_sql($tiktok_catalog_id),
          'tiktok_status' => strpos($channel_ids, '3') !== false ? esc_sql('Draft') : '',
          'is_mapping_update' => isset($_POST['product_selection']) && ($_POST['product_selection'] == 'all_product' || $_POST['product_selection'] == 'filter_product') ? true : false,
          'is_process_start' => false,
          'is_auto_sync_start' => false,
          'product_sync_batch_size' => esc_sql(100),
          'ms_status' => strpos($channel_ids, '4') !== false ? esc_sql('Draft') : '',
        );
        $TVC_Admin_DB_Helper->tvc_add_row("ee_product_feed", $profile_data, array("%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%d", "%d", "%d", "%s", "%s", "%s"));
        $result = $TVC_Admin_DB_Helper->tvc_get_last_row("ee_product_feed", array("id"));
        $profile_data = array("profile_title" => esc_sql("Default"), "g_attribute_mapping" => wp_json_encode($mappedAttrs), "update_date" => gmdate('Y-m-d'));
        if ($TVC_Admin_DB_Helper->tvc_row_count("ee_product_sync_profile") == 0) {
          $TVC_Admin_DB_Helper->tvc_add_row("ee_product_sync_profile", $profile_data, array("%s", "%s", "%s"));
        } else {
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_profile", $profile_data, array("id" => 1));
        }
        if (isset($_POST['product_selection'])) {
          switch ($_POST['product_selection']) {
            case 'all_product':
            case 'filter_product':
              $feed_data_api = array(
                "store_id" => $google_detail['setting']->store_id,
                "store_feed_id" => $result['id'],
                "map_categories" => $categories !== false ? wp_json_encode($categories) : null,
                "map_attributes" => wp_json_encode($mappedAttrs),
                "filter" => wp_json_encode($filters),
                "include" => '',
                "exclude" => '',
                "channel_ids" => esc_sql($channel_ids),
                "interval" => isset($_POST['autoSyncIntvl']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : '',
                "tiktok_catalog_id" => esc_sql($tiktok_catalog_id),
              );
              $CustomApi = new CustomApi();
              $CustomApi->ee_create_product_feed($feed_data_api);
              /********Manual Product sync Start ******************/
              as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $result['id']));
              if (!class_exists('TVCProductSyncHelper')) {
                include(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php');
              }
              $TVCProductSyncHelper = new TVCProductSyncHelper();
              $isSyncComplete = $TVCProductSyncHelper->manualProductSync($result['id']);
              if (isset($isSyncComplete['status']) && $isSyncComplete['status'] === 'success') {
                $last_sync_date = gmdate('Y-m-d H:i:s', current_time('timestamp'));
                $next_schedule_date = NULL;
                if (isset($_POST['autoSync']) && $_POST['autoSync'] == 1) {
                  $autoSyncIntvl = isset($_POST['autoSyncIntvl']) ? absint($_POST['autoSyncIntvl']) : 0;
                  $next_schedule_date = gmdate('Y-m-d H:i:s', strtotime('+' . $autoSyncIntvl . 'day', current_time('timestamp')));
                  $time_space = strtotime($autoSyncIntvl . " days", 0);
                  $timestamp = strtotime($autoSyncIntvl . " days");
                  $TVC_Admin_Helper->plugin_log("recurring cron set", 'product_sync'); // Add logs 
                  as_schedule_recurring_action(esc_attr($timestamp), esc_attr($time_space), 'init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $result['id']), "product_sync");
                }

                $feed_data = array(
                  "product_sync_alert" => NULL,
                  "is_process_start" => false,
                  "is_auto_sync_start" => false,
                  "last_sync_date" => esc_sql($last_sync_date),
                  "next_schedule_date" => $next_schedule_date,
                );
                $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $result['id']));
              } else {
                $feed_data = array(
                  "product_sync_alert" => isset($isSyncComplete['message']) ? $isSyncComplete['message'] : '',
                  "is_process_start" => false,
                  "is_auto_sync_start" => false,
                  "is_mapping_update" => false,
                );
                $TVC_Admin_Helper->plugin_log("error-3", 'product_sync'); // Add logs
                $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $result['id']));
              }

              /********Manual Product sync End ******************/
              echo wp_json_encode($result);
              break;
            case 'specific_product':
              echo wp_json_encode($result);
              break;
          }
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    public function get_user_businesses()
    {
      $nonce = filter_input(INPUT_POST, 'fb_business_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'fb_business_nonce')) {
        $data = array(
          "customer_subscription_id" => isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '',
        );
        $convCustomApi = new CustomApi();
        $result = $convCustomApi->getUserBusinesses($data);
        echo wp_json_encode($result);
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    public function get_fb_catalog_data()
    {
      $nonce = filter_input(INPUT_POST, 'fb_business_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'fb_business_nonce')) {
        $data = array(
          "customer_subscription_id" => isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '',
          "business_id" => isset($_POST['fb_business_id']) ? sanitize_text_field(wp_unslash($_POST['fb_business_id'])) : '',
        );
        /**
         * Api Call to store user FB Business
         */
        $convCustomApi = new CustomApi();
        // $response = $convCustomApi->storeUserBusiness($data);
        $result = $convCustomApi->getCatalogList($data);
        // $response->CatalogList = $result->data;
        echo wp_json_encode($result->data);
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }
    public function conv_save_facebookmiddleware($post)
    {
      $nonce = filter_input(INPUT_POST, 'pix_sav_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        if (isset($post['customer_subscription_id']) === TRUE && $post['customer_subscription_id'] !== '' && isset($post['conv_options_data']['facebook_setting']['fb_business_id']) && $post['conv_options_data']['facebook_setting']['fb_business_id'] !== '') {
          $customer_data['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer_data['business_id'] = $post['conv_options_data']['facebook_setting']['fb_business_id'];
          $customObj = new CustomApi();
          $result = $customObj->storeUserBusiness($customer_data);
          return $result;
        }
      }
    }

    public function conv_save_facebookcatalog($post)
    {
      $nonce = filter_input(INPUT_POST, 'pix_sav_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        if (isset($post['customer_subscription_id']) === TRUE && $post['customer_subscription_id'] !== '' && isset($post['conv_options_data']['facebook_setting']['fb_business_id']) && $post['conv_options_data']['facebook_setting']['fb_business_id'] !== '') {
          $customer_data['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer_data['business_id'] = $post['conv_options_data']['facebook_setting']['fb_business_id'];
          $customer_data['catalog_id'] = $post['conv_options_data']['facebook_setting']['fb_catalog_id'];
          $customObj = new CustomApi();
          $result = $customObj->storeUserCatalog($customer_data);
          return $result;
        }
      }
    }

    public function conv_save_microsoft($post)
    {
      $customer_data = [];
      if (isset($post['microsoft_ads_manager_id']) && $post['microsoft_ads_manager_id'] !== '') {
        $customer_data['customer_id'] = sanitize_text_field(wp_unslash($post['microsoft_ads_manager_id']));
      }
      if (isset($post['microsoft_ads_subaccount_id']) && $post['microsoft_ads_subaccount_id'] !== '') {
        $customer_data['account_id'] = sanitize_text_field(wp_unslash($post['microsoft_ads_subaccount_id']));
      }
      if (isset($post['microsoft_ads_pixel_id']) && $post['microsoft_ads_pixel_id'] !== '') {
        $customer_data['pixel_id'] = sanitize_text_field(wp_unslash($post['microsoft_ads_pixel_id']));
      }
      if (isset($post['microsoft_merchant_center_id']) && $post['microsoft_merchant_center_id'] !== '') {
        $customer_data['mmc_id'] = sanitize_text_field(wp_unslash($post['microsoft_merchant_center_id']));
      }
      if (isset($post['ms_catalog_id']) && $post['ms_catalog_id'] !== '') {
        $customer_data['catalog_id'] = sanitize_text_field(wp_unslash($post['ms_catalog_id']));
      }

      if (!empty($customer_data)) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();

        $customObj = new CustomApi();

        if (isset($post['subscription_id']) && $post['subscription_id'] !== '') {
          $customer_data['customer_subscription_id'] = sanitize_text_field(wp_unslash($post['subscription_id']));
        }
        if (isset($google_detail['setting']->store_id) && $google_detail['setting']->store_id !== '') {
          $customer_data['store_id'] = sanitize_text_field(wp_unslash($google_detail['setting']->store_id));
        }
        $result = $customObj->updateMicrosoftDetail($customer_data);
        return $result;
      }
    }

    public function ee_editPmaxCampaign()
    {
      $nonce = sanitize_text_field(wp_unslash(filter_input(INPUT_POST, 'conv_onboarding_nonce')));
      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        if (!class_exists('Conversios_PMax_Helper')) {
          require_once(ENHANCAD_PLUGIN_DIR . 'admin/helper/class-pmax-helper.php');
        }
        $PMax_Helper = new Conversios_PMax_Helper();
        if (isset($_POST['google_ads_id']) && isset($_POST['id'])) {
          $rs = $PMax_Helper->campaign_pmax_detail(sanitize_text_field(wp_unslash($_POST['google_ads_id'])), sanitize_text_field(wp_unslash($_POST['id'])));
        }
        if (isset($rs->data)) {
          if (isset($rs->data->campaign)) {
            $campaign = $rs->data->campaign;
          }
          if (isset($rs->data->campaign_budget)) {
            $campaign_budget = $rs->data->campaign_budget;
          }

          $sale_country = isset($campaign->shoppingSetting->feedLabel) ? $campaign->shoppingSetting->feedLabel : "";
          $budget_micro = isset($campaign_budget->amountMicros) ? $campaign_budget->amountMicros : "";
          if ($budget_micro > 0) {
            $budget = $budget_micro / 1000000;
          }
          $maximizeconversionvalue = isset($campaign->maximizeConversionValue) ? $campaign->maximizeConversionValue : "";
          $target_roas = $this->object_value($maximizeconversionvalue, "targetRoas") * 100;
          $startDate = $this->object_value($campaign, "startDate");
          $endDate = $this->object_value($campaign, "endDate");
          $status = $campaign->status;
          $resourceName = $campaign->resourceName;
          $campaignBudget = $campaign->campaignBudget;
          $campaignName = $this->object_value($campaign, "name");
          $data = array(
            'campaignName' => $campaignName,
            'budget' => $budget,
            'sale_country' => $sale_country,
            'target_roas' => $target_roas,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
            'resourceName' => $resourceName,
            'campaignBudget' => $campaignBudget,
            'id' => $campaign->id
          );
          echo wp_json_encode(array("error" => false, "result" => $data));
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("No record found for the selected Campaign.", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    public function ee_editPmaxCampaign_ms()
    {
      $nonce = sanitize_text_field(wp_unslash(filter_input(INPUT_POST, 'conv_onboarding_nonce')));
      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        if (!class_exists('Conversios_PMax_Helper')) {
          require_once(ENHANCAD_PLUGIN_DIR . 'admin/helper/class-pmax-helper.php');
        }
        $PMax_Helper = new Conversios_PMax_Helper();
        if (isset($_POST['microsoft_ads_id']) && isset($_POST['id'])) {
          $rs = $PMax_Helper->campaign_pmax_detail_ms(
            sanitize_text_field(wp_unslash($_POST['subscription_id'])),
            sanitize_text_field(wp_unslash($_POST['microsoft_ads_id'])),
            sanitize_text_field(wp_unslash($_POST['microsoft_ads_sub_id'])),
            sanitize_text_field(wp_unslash($_POST['id']))
          );
        }
        if (isset($rs->data)) {

          if (isset($rs->data)) {
            $campaign = $rs->data[0];
          }

          //$sale_country = isset($campaign->shoppingSetting->feedLabel) ? $campaign->shoppingSetting->feedLabel : "";
          $budget = $campaign->DailyBudget;
          //$maximizeconversionvalue = isset($campaign->maximizeConversionValue) ? $campaign->maximizeConversionValue : "";
          //$target_roas = $this->object_value($maximizeconversionvalue, "targetRoas") * 100;
          //$startDate = $this->object_value($campaign, "startDate");
          //$endDate = $this->object_value($campaign, "endDate");
          $status = $campaign->Status;
          //$resourceName = $campaign->resourceName;
          //$campaignBudget = $campaign->campaignBudget;
          $campaignName = $campaign->Name;
          $data = array(
            'campaignName' => $campaignName,
            'budget' => $budget,
            //'sale_country' => $sale_country,
            //'target_roas' => $target_roas,
            //'startDate' => $startDate,
            //'endDate' => $endDate,
            'status' => $status,
            //'resourceName' => $resourceName,
            //'campaignBudget' => $campaignBudget,
            'id' => $campaign->Id
          );
          echo wp_json_encode(array("error" => false, "result" => $data));
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("No record found for the selected Campaign.", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    public function object_value($obj, $key)
    {
      if (!empty($obj) && $key && isset($obj->$key)) {
        return $obj->$key;
      }
    }

    public function ee_update_PmaxCampaign()
    {
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      $return = array();
      $formArry = array();
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        foreach ($_POST as $key => $val) {
          if ($key == 'action' || $key == 'conversios_nonce') {
            continue;
          }
          $formArry[$key] = sanitize_text_field($val);
        }
        if (isset($formArry["customer_id"])) {
          $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
          $where = '`campaign_name` = "' . esc_sql($formArry['campaign_name']) . '"';
          $filed = [
            'request_id',
          ];
          $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_pmax_campaign", $where, $filed);
          if (isset($result[0]['request_id']) &&  $result[0]['request_id'] !== '') {
            $formArry['request_id'] = $result[0]['request_id'];
            $profile_data = array(
              "daily_budget" => esc_sql($formArry['budget']),
              "target_country_campaign" => esc_sql($formArry['target_country']),
              "target_roas" => esc_sql($formArry['target_roas']),
              "start_date" => esc_sql($formArry['start_date']),
              "end_date" => esc_sql($formArry['end_date']),
              "status" => esc_sql($formArry['status']),
              "updated_date" => gmdate('Y-m-d H:i:s', current_time('timestamp')),
            );
            $TVC_Admin_DB_Helper->tvc_update_row("convpfm_pmax_campaign", $profile_data, array("campaign_name" => $formArry['campaign_name']));
          }
          $PMax_Helper = new Conversios_PMax_Helper();
          if ($formArry["status"] == "REMOVED") {
            $removeArr = array("customer_id" => $formArry["customer_id"], "resource_name" => $formArry["resource_name"]);
            if (isset($result[0]['request_id']) &&  $result[0]['request_id'] !== '') {
              $removeArr['request_id'] = $result[0]['request_id'];
            }
            $api_rs = $PMax_Helper->delete_pmax_campaign_callapi($removeArr);
          } else {
            $api_rs = $PMax_Helper->edit_pmax_campaign_callapi($formArry);
          }
          if (isset($api_rs->error) && $api_rs->error == '') {
            if (isset($api_rs->data->results[0]->resourceName) && $api_rs->data != "") {
              $resource_name = $api_rs->data->results[0]->resourceName;
              if ($formArry["status"] == "REMOVED") {
                echo wp_json_encode(array('error' => false, 'message' => "Campaign Removed Successfully with resource name - " . $resource_name));
              } else {
                echo wp_json_encode(array('error' => false, 'message' => "Campaign Edited Successfully with resource name - " . $resource_name));
              }
            } else if (isset($api_rs->data)) {
              echo wp_json_encode(array('error' => false, 'data' => $api_rs->data));
            }
          } else {
            $errormsg = "";
            if (!is_array($api_rs->errors) && is_string($api_rs->errors)) {
              $errormsg = $api_rs->errors;
            } else {
              $errormsg = isset($api_rs->errors[0]) ? $api_rs->errors[0] : "";
            }
            echo wp_json_encode(array('error' => true, 'message' => $errormsg,  'status' => $api_rs->status));
          }
        }
      } else {
        echo wp_json_encode(array('error' => true, 'message' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    public function ee_update_PmaxCampaign_ms()
    {
      $nonce = sanitize_text_field(wp_unslash(filter_input(INPUT_POST, 'conv_onboarding_nonce')));
      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        if (!class_exists('Conversios_PMax_Helper')) {
          require_once(ENHANCAD_PLUGIN_DIR . 'admin/helper/class-pmax-helper.php');
        }
        $PMax_Helper = new Conversios_PMax_Helper();
        if (isset($_POST['microsoft_ads_id']) && isset($_POST['campaign_id'])) {
          $rs = $PMax_Helper->update_campaign_pmax_detail_ms(
            sanitize_text_field(wp_unslash($_POST['subscription_id'])),
            sanitize_text_field(wp_unslash($_POST['microsoft_ads_id'])),
            sanitize_text_field(wp_unslash($_POST['microsoft_ads_sub_id'])),
            sanitize_text_field(wp_unslash($_POST['campaign_id'])),
            sanitize_text_field(wp_unslash($_POST['daily_budget'])),
            sanitize_text_field(wp_unslash($_POST['status'])),
          );
          if (isset($rs->error) && $rs->error == false) {
            echo wp_json_encode(array("error" => false, "message" => esc_html__("Campaign updated successfully.", "enhanced-e-commerce-for-woocommerce-store")));
          } else {
            if (isset($rs->errors)) {
              echo wp_json_encode(array("error" => true, "message" => wp_json_encode($rs->errors)));
            } else {
              echo wp_json_encode(array("error" => true, "message" => esc_html__("No record found for the selected Campaign.", "enhanced-e-commerce-for-woocommerce-store")));
            }
          }
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Parameters are missing.", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    public function convert_budget_to_local_currency()
    {
      $nonce = sanitize_text_field(wp_unslash(filter_input(INPUT_POST, 'conv_onboarding_nonce')));
      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $currency = sanitize_text_field(wp_unslash($_POST['currency']));
        if ($currency !== '') {
          $custom_api = new CustomApi();
          $result = $custom_api->get_local_currency_rate_live($currency);
          echo wp_json_encode(array("error" => false, "message" => $result));
        }
      }
    }
  }
// End of TVC_Ajax_File_Class
endif;
$tvcajax_file_class = new TVC_Ajax_File();
