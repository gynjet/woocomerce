<?php
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

class CustomApi
{
  private $apiDomain;
  private $token;
  public function __construct()
  {
    $this->apiDomain = TVC_API_CALL_URL;
    $this->token = 'MTIzNA==';
  }

  public function tc_wp_remot_call_post($url, $args)
  {
    try {
      if (!empty($args)) {
        // Send remote request
        $args['timeout'] = "1000";
        $request = wp_remote_post($url, $args);

        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);

        $response_message = wp_remote_retrieve_response_message($request);
        $response_body = json_decode(wp_remote_retrieve_body($request));

        if ((isset($response_body->error) && $response_body->error == '')) {
          if (isset($response_body->data) && $response_body->data != '') {
            return new WP_REST_Response($response_body->data);
          } elseif (isset($response_body->message) && $response_body->message != '') {
            return new WP_REST_Response($response_body->message);
          } else {
            return new WP_REST_Response($response_body);
          }
        } else {
          return new WP_Error($response_code, $response_message, $response_body);
        }
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function is_allow_call_api()
  {
    $ee_options_data = unserialize(get_option('ee_options'));
    if (isset($ee_options_data['subscription_id'])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Update Google Merchant Center business information
   */
  public function update_gmc_business_info($business_data)
  {
    $url = $this->apiDomain . '/gmc/update-merchant-business-detail';
    $args = [
      'method' => 'POST',
      'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $this->token
      ],
      'body' => json_encode($business_data) // ✅ send flat structure
    ];

    $response = $this->tc_wp_remot_call_post($url, $args);

    if (is_wp_error($response)) {
      return (object)['error' => true, 'message' => $response->get_error_message()];
    }

    return $response;
  }


  public function update_app_status($status = 1)
  {
    try {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = sanitize_text_field($TVC_Admin_Helper->get_subscriptionId());
      if ($subscription_id != "") {
        $url = $this->apiDomain . '/customer-subscriptions/update-app-status';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $options = unserialize(get_option('ee_options'));
        $fb_pixel_enable = "0";
        if (isset($options['fb_pixel_id']) && $options['fb_pixel_id'] != "") {
          $fb_pixel_enable = "1";
        }
        $woocomm_version = "0";
        if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
          global $woocommerce;
          $woocomm_version = $woocommerce->version;
        }
        $store_country = get_option('woocommerce_default_country');
        $store_country = explode(":", $store_country);

        $attributes = unserialize(get_option('ee_prod_mapped_attrs'));
        $categories = unserialize(get_option('ee_prod_mapped_cats'));
        $countAttribute = is_array($attributes) ? count($attributes) : 0;
        $countCategories = is_array($categories) ? count($categories) : 0;

        $postData = array(
          "subscription_id" => $subscription_id,
          "domain" => esc_url(get_site_url()),
          "app_status_data" => array(
            "app_settings" => array(
              "app_status" => sanitize_text_field($status),
              "fb_pixel_enable" => $fb_pixel_enable,
              "app_verstion" => PLUGIN_TVC_VERSION,
              "domain" => esc_url(get_site_url()),
              "product_settings" => unserialize(get_option('ee_options')),
              "attributeMapping" => $countAttribute,
              "categoryMapping" => $countCategories,
              "update_date" => gmdate("Y-m-d")
            ),
            "store" => array(
              "country" => isset($store_country[0]) ? $store_country[0] : "",
              "state" => isset($store_country[1]) ? $store_country[1] : ""
            ),
            "walk_through" => isset($options['walk_through']) ? $options['walk_through'] : "",
            "woocomm_version" => $woocomm_version,
          )
        );
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        wp_remote_post(esc_url_raw($url), $args);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function app_activity_detail($status)
  {
    try {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = sanitize_text_field($TVC_Admin_Helper->get_subscriptionId());
      if (isset($subscription_id) && $status != "") {
        $url = $this->apiDomain . '/customer-subscriptions/app_activity_detail';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $postData = array(
          "subscription_id" => $subscription_id,
          "domain" => esc_url(get_site_url()),
          "app_status" => sanitize_text_field($status),
          "app_data" => array(
            "app_version" => PLUGIN_TVC_VERSION,
            "app_id" => CONV_APP_ID,
          )
        );
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function getGoogleAnalyticDetail($subscription_id = null)
  {
    try {

      $url = $this->apiDomain . '/customer-subscriptions/subscription-detail';
      $header = array(
        "Authorization: Bearer " . $this->token,
        "Content-Type" => "application/json"
      );
      $ee_options_data = unserialize(get_option('ee_options'));
      if ($subscription_id == null && isset($ee_options_data['subscription_id'])) {
        $subscription_id = sanitize_text_field($ee_options_data['subscription_id']);
      }
      $data = [
        'subscription_id' => sanitize_text_field($subscription_id),
        'domain' => get_site_url()
      ];
      if ($subscription_id == "") {
        $return = new \stdClass();
        $return->error = true;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $return = new \stdClass();
      if (isset($result->status)) {
        if ($result->status == 200) {
          $return->status = $result->status;
          $return->data = $result->data;
          $return->error = false;
          return $return;
        } else {
          $return->error = true;
          $return->data = $result->data;
          $return->status = $result->status;
          return $return;
        }
      } else {
        $return->error = true;
        $return->data = 'something went wrong please try again';
        $return->status = '404';
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function updateTrackingOption($postData)
  {
    try {
      $url = $this->apiDomain . '/customer-subscriptions/tracking-options';

      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json'
        ),
        'method' => 'PATCH',
        'body' => wp_json_encode($postData)
      );

      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response_body = json_decode(wp_remote_retrieve_body($request));

      if ((isset($response_body->error) && $response_body->error == '')) {

        return new WP_REST_Response(
          array(
            'status' => $response_code,
            'message' => $response_message,
            'data' => $response_body->data
          )
        );
      } else {
        return new WP_Error($response_code, $response_message, $response_body);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function add_survey_of_deactivate_plugin($postData)
  {
    try {
      $url = $this->apiDomain . "/customersurvey";
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $args = array(
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($postData)
      );
      $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);

      $return = new \stdClass();
      if ($result->status == 200) {
        $return->status = $result->status;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = $result->data;
        $return->status = $result->status;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function active_licence_Key($licence_key, $subscription_id)
  {
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/licence/activation";
      $data = [
        'key' => sanitize_text_field($licence_key),
        'domain' => get_site_url(),
        'subscription_id' => sanitize_text_field($subscription_id),
        'app_id' => 1
      ];
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($response->error) && $response->error == '')) {
        //$return->status = $result->status;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        if (isset($response->data)) {
          $return->error = false;
          $return->data = $response->data;
          $return->message = $response->message;
        } else {
          $return->error = true;
          $return->data = [];
          if (isset($response->errors->key[0])) {
            $return->message = $response->errors->key[0];
          } else {
            $return->message = esc_html__("Check your entered licese key.", "enhanced-e-commerce-for-woocommerce-store");
          }
        }
        return $return;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function get_remarketing_snippets($customer_id)
  {
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/google-ads/remarketing-snippets";
      $data = [
        'customer_id' => sanitize_text_field($customer_id)
      ];
      $args = array(
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);

      $return = new \stdClass();
      if ($result->status == 200) {
        $return->status = $result->status;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = $result->data;
        $return->status = $result->status;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function get_conversion_list($customer_id, $conversionCategory = "")
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/google-ads/conversion-list";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = $TVC_Admin_Helper->get_subscriptionId();
      $data = [
        'customer_id' => sanitize_text_field($customer_id),
        'conversionCategory' => sanitize_text_field($conversionCategory),
        'subscription_id' => sanitize_text_field($subscription_id),
        'store_id' => $google_detail['setting']->store_id
      ];
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );

      // $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $request = wp_remote_post(esc_url_raw($url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->status = $response_code;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->errors = $result->errors;
        //$return->error = $result->data;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_microsoft_conversion_list($customer_id, $account_id, $tag_id)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/microsoft/getConversionGoals";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = $TVC_Admin_Helper->get_subscriptionId();
      $data = [
        'customer_id' => sanitize_text_field($customer_id),
        'account_id' => sanitize_text_field($account_id),
        'subscription_id' => sanitize_text_field($subscription_id),
        'tag_id' => sanitize_text_field($tag_id)
      ];
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );

      // $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $request = wp_remote_post(esc_url_raw($url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->status = $response_code;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->errors = $result->errors;
        //$return->error = $result->data;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function conv_create_microsoft_ads_conversion($customer_id, $account_id, $tag_id, $conversionCategory, $name, $action_value)
  {
    $currency_code = "";
    if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
      $currency_code = get_woocommerce_currency();
    }
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/microsoft/createConversionGoals";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = $TVC_Admin_Helper->get_subscriptionId();
      $data = [
        'customer_id' => sanitize_text_field($customer_id),
        'account_id' => sanitize_text_field($account_id),
        'tag_id' => sanitize_text_field($tag_id),
        'conversion_category' => sanitize_text_field($conversionCategory),
        'name' => sanitize_text_field($name),
        'subscription_id' => $subscription_id,
        'currency_code' => $currency_code,
        'action_value' => $action_value
      ];
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );

      // $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $request = wp_remote_post(esc_url_raw($url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->status = $response_code;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->errors = $result->errors;
        //$return->error = $result->data;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function conv_create_gads_conversion($customer_id, $conversionName, $conversionCategory = "")
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/google-ads/create-conversion";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = $TVC_Admin_Helper->get_subscriptionId();
      $data = [
        'customer_id' => sanitize_text_field($customer_id),
        'name' => sanitize_text_field($conversionName),
        'subscription_id' => sanitize_text_field($subscription_id),
        'conversionCategory' => sanitize_text_field($conversionCategory),
        'store_id' => $google_detail['setting']->store_id
      ];
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );

      // $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $request = wp_remote_post(esc_url_raw($url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->status = $response_code;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->errors = $result->errors;
        //$return->error = $result->data;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   * @since 4.6.8
   * Get  google analytics reports call using reporting API
   */
  public function get_google_analytics_reports_ga4($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $postData['store_id'] = $google_detail['setting']->store_id;
    $postData['subscription_id'] = $google_detail['setting']->id;
    try {
      $url = $this->apiDomain . "/actionable-dashboard/google-analytics-reports-ga4";
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($postData)
      );
      $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $return = new \stdClass();
      if (isset($result->status) === TRUE && $result->status == 200) {
        $return->status = $result->status;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = isset($result->data) === TRUE ? $result->data : '';
        $return->status = isset($result->status) === TRUE ? $result->status : '';
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  /**
   * @since 4.6.8
   * Get Property ID for GA4 reporting API
   */
  public function analytics_get_ga4_property_id($postData)
  {
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/actionable-dashboard/analytics-get-ga4-property-id";
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      return json_decode(wp_remote_retrieve_body($request));
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function setGmcCategoryMapping($postData)
  {
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $url = $this->apiDomain . '/gmc/gmc-category-mapping';

      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json'
        ),
        'method' => 'POST',
        'body' => wp_json_encode($postData)
      );

      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response_body = json_decode(wp_remote_retrieve_body($request));

      if ((isset($response_body->error) && $response_body->error == '')) {
        return new WP_REST_Response(
          array(
            'status' => $response_code,
            'message' => $response_message,
            'data' => $response_body->data
          )
        );
      } else {
        return new WP_Error($response_code, $response_message, $response_body);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function setGmcAttributeMapping($postData)
  {
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $url = $this->apiDomain . '/gmc/gmc-attribute-mapping';

      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json'
        ),
        'method' => 'POST',
        'body' => wp_json_encode($postData)
      );

      // Send remote request
      $request = wp_remote_post($url, $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response_body = json_decode(wp_remote_retrieve_body($request));

      if ((isset($response_body->error) && $response_body->error == '')) {

        return new WP_REST_Response(
          array(
            'status' => $response_code,
            'message' => $response_message,
            'data' => $response_body->data
          )
        );
      } else {
        return new WP_Error($response_code, $response_message, $response_body);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function products_sync($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          if (in_array($key, array("merchant_id", "account_id", "subscription_id"))) {
            $postData[$key] = sanitize_text_field($value);
          }
        }
      }
      $postData['store_id'] = $google_detail['setting']->store_id;
      $postData['subscription_id'] = $google_detail['setting']->id;
      $url = $this->apiDomain . "/products/batch";
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->error = false;
        $return->products_sync = count($response->data->entries);
        return $return;
      } else {
        $return->error = true;
        $return->arges =  $args;
        if (isset($response->errors)) {
          foreach ($response->errors as $err) {
            $return->message = $err;
            break;
          }
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getSyncProductList($postData)
  {
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $url = $this->apiDomain . "/products/list";
      $postData["maxResults"] = 50;
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));

      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->error = false;
        $return->data = $response->data;
        $return->message = $response->message;
        return $return;
      } else {
        $return->status = $response_code;
        $return->error = true;
        if (isset($response->errors)) {
          foreach ($response->errors as $err) {
            $return->message = $err;
            break;
          }
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function conv_send_email($postData)
  {
    try {
      $url = $this->apiDomain . '/customersurvey/sendInquiryEmail';
      $args = array(
        'timeout' => 10000,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json',
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->message = "Your email successfully sent.";
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function getCampaignCurrencySymbol($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $postData['store_id'] = $google_detail['setting']->store_id;
    $postData['subscription_id'] = $google_detail['setting']->id;
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $url = $this->apiDomain . '/campaigns/currency-symbol';

      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($postData)
      );

      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response_body = json_decode(wp_remote_retrieve_body($request));
      if ((isset($response_body->error) && $response_body->error == '')) {

        return new WP_REST_Response(
          array(
            'status' => $response_code,
            'message' => $response_message,
            'data' => $response_body->data
          )
        );
      } else {
        return new WP_Error($response_code, $response_message, $response_body);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function record_customer_featurereq($postData)
  {
    try {
      $url = $this->apiDomain . '/customerfeedback';
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json',
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->message = "Your feedback was successfully recoded.";
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function record_customer_feedback($postData)
  {
    try {
      $url = $this->apiDomain . '/customerfeedback';
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->message = "Your feedback was successfully recoded.";
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function check_if_basesixtyfour($data)
  {
    if (base64_encode(base64_decode($data, true)) === $data) {
      return true;
    } else {
      return false;
    }
  }


  public function siteVerificationToken($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $url = $this->apiDomain . '/gmc/site-verification-token';
      $data = [
        'merchant_id' => sanitize_text_field($postData['merchant_id']),
        'website' => sanitize_text_field($postData['website_url']),
        'account_id' => sanitize_text_field($postData['account_id']),
        'method' => sanitize_text_field($postData['method']),
        'store_id' => $google_detail['setting']->store_id,
        'subscription_id' => $google_detail['setting']->id
      ];

      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $return = new \stdClass();
      if (isset($result->status) && $result->status == 200) {
        $return->status = $result->status;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        if (is_array($result->errors)) {
          if (count($result->errors) != count($result->errors, COUNT_RECURSIVE)) {
            $return->errors = implode("&", array_map(function ($a) {
              return implode("~", $a);
            }, $result->errors));
          } else {
            $return->errors = implode(" ", $result->errors);
          }
        } else {
          $return->errors = $result->errors;
        }
        $return->data = (isset($result->data) ? $result->data : null);
        $return->status = (isset($result->status) ? $result->status : null);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function siteVerification($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $url = $this->apiDomain . '/gmc/site-verification';
      $data = [
        'merchant_id' => sanitize_text_field($postData['merchant_id']),
        'website' => esc_url_raw($postData['website_url']),
        'subscription_id' => sanitize_text_field($postData['subscription_id']),
        'account_id' => sanitize_text_field($postData['account_id']),
        'method' => sanitize_text_field($postData['method']),
        'store_id' => $google_detail['setting']->store_id
      ];

      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {

        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        if (is_array($result->errors)) {
          if (count($result->errors) != count($result->errors, COUNT_RECURSIVE)) {
            $return->errors = implode("&", array_map(function ($a) {
              return implode("~", $a);
            }, $result->errors));
          } else {
            $return->errors = implode(" ", $result->errors);
          }
        } else {
          $return->errors = $result->errors;
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function claimWebsite($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $url = $this->apiDomain . '/gmc/claim-website';
      $data = [
        'merchant_id' => sanitize_text_field($postData['merchant_id']),
        'account_id' => sanitize_text_field($postData['account_id']),
        'website' => esc_url_raw($postData['website_url']),
        'subscription_id' => sanitize_text_field($postData['subscription_id']),
        'store_id' => $google_detail['setting']->store_id
      ];
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));

      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {

        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        if (is_array($result->errors)) {
          if (count($result->errors) != count($result->errors, COUNT_RECURSIVE)) {
            $return->errors = implode("&", array_map(function ($a) {
              return implode("~", $a);
            }, $result->errors));
          } else {
            $return->errors = implode(" ", $result->errors);
          }
        } else {
          $return->errors = $result->errors;
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_resource_center_data($postData)
  {
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/resourceCenter/list";
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      return json_decode(wp_remote_retrieve_body($request));
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getProductStatusByFeedId($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/products/list';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getProductStatusByChannelId($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/products/status-list';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        //echo '<pre>'; print_r($args); print_r($result); echo '</pre>'; exit('wow'); // woow on syncu propduct status (ee_get_product_status())
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_feed_status_by_store_id($data)
  {
    try {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = sanitize_text_field($TVC_Admin_Helper->get_subscriptionId());
      if (isset($subscription_id) && $data != "") {
        $url = $this->apiDomain . '/products/feed-list';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function delete_from_channels($data)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = sanitize_text_field($TVC_Admin_Helper->get_subscriptionId());
      if (isset($subscription_id) && $data != "") {
        $url = $this->apiDomain . '/products/batch';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $data['store_id'] = $google_detail['setting']->store_id;
        $data['subscription_id'] = $google_detail['setting']->id;
        $args = array(
          'headers' => $header,
          'method' => 'DELETE',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        $return = new \stdClass();
        if ($result->status == 200) {
          $return->status = $result->status;
          $return->data = $result->data;
          $return->error = false;
          return $return;
        } else {
          $return->error = true;
          $return->data = $result->data;
          $return->status = $result->status;
          return $return;
        }
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ee_create_product_feed($data)
  {
    try {
      $CONV_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = sanitize_text_field($CONV_Admin_Helper->get_subscriptionId());
      if (isset($subscription_id) && $data != "") {
        $url = $this->apiDomain . '/products/feed';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function feed_wise_products_sync($postData, $callby = 'no-reference-give')
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          if (in_array($key, array("merchant_id", "account_id", "subscription_id", "store_feed_id", "is_on_gmc", "is_on_microsoft", "is_on_facebook", "is_on_tiktok"))) {
            $postData[$key] = sanitize_text_field($value);
          }
        }
      }
      $postData['store_id'] = $google_detail['setting']->store_id;
      $postData['subscription_id'] = $google_detail['setting']->id;
      $url = $this->apiDomain . "/products/batch-all";
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);

      //echo '<pre>'.$callby; print_r(json_decode(wp_remote_retrieve_body($request))); echo '</pre>'; // 


      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->error = false;
        return $return;
      } else {

        $TVC_Admin_Helper->plugin_log("woow 1232 error-feed_wise_products_sync():" . json_encode($response), 'product_sync');
        $return->error = true;
        $return->arges =  $args;
        if (isset($response->errors)) {
          foreach ($response->errors as $err) {
            $return->message = $err;
            break;
          }
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_tiktok_business_account($postData)
  {
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/getBusinessCenter';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_tiktok_user_catalogs($postData)
  {
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/getUserCatalogs';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function store_business_center($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $postData['store_id'] = $google_detail['setting']->store_id;
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/storeBusinessCenter';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function store_user_catalog($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $postData['store_id'] = $google_detail['setting']->store_id;
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/storeUserCatalog';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function createCatalogs($postData)
  {
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/createCatalogs';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function conv_create_ec_row_apicall($postData)
  {
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/usertracking/ec-journey';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function createPmaxCampaign($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $postData['store_id'] = $google_detail['setting']->store_id;
    $postData['subscription_id'] = $google_detail['setting']->id;
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/pmax/retail';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function createPmaxCampaign_ms($postData)
  {
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/microsoft/createCampaign';
        $header = array(
          'Authorization' => "Bearer MTIzNA==",
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function pMaxRetailStatus($data)
  {
    try {
      if ($data != "") {
        $url = $this->apiDomain . '/pmax/retailStatus';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getUserBusinesses($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/facebook/getUserBusinesses';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        $dataResult = array();
        if (isset($result->data)) {
          foreach ($result->data as $val) {
            $dataResult["$val->id"] = $val->id . '-' . $val->name;
          }
        }

        return $dataResult;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getCatalogList($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/facebook/getUserCatalogs';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function storeUserBusiness($data)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/facebook/storeUserBusiness';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $data['store_id'] = $google_detail['setting']->store_id;
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function storeUserCatalog($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/facebook/storeUserCatalog';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function updateMicrosoftDetail($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/microsoft/updateMicrosoftDetail';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }


  public function ads_checkMcc($subscription_id, $ads_accountId)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      if ($subscription_id != "" && $ads_accountId != "") {
        $url = $this->apiDomain . '/adwords/check-manager-account';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $data = array(
          "subscription_id" => $subscription_id,
          "customer_id" => $ads_accountId,
          'store_id' => $google_detail['setting']->store_id
        );
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      } else {
        return "Invalid Request";
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_gads_info($google_ads_id, $api_type)
  {
    try {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = sanitize_text_field($TVC_Admin_Helper->get_subscriptionId());
      $google_detail = $TVC_Admin_Helper->get_ee_options_data();
      if (isset($subscription_id)) {
        $url = $this->apiDomain . '/adwords/get-google-ads-status';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $postData = array(
          "subscription_id" => $subscription_id,
          "store_id" => $google_detail['setting']->store_id,
          "api_type" => $api_type,
          "customer_id" => $google_ads_id
        );
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result->data;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_gmc_business_info($google_merchant_id)
  {
    try {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = sanitize_text_field($TVC_Admin_Helper->get_subscriptionId());
      $google_detail = $TVC_Admin_Helper->get_ee_options_data();
      if (isset($subscription_id)) {
        $url = $this->apiDomain . '/gmc/get-gmc-business-detail';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $postData = array(
          "subscription_id" => $subscription_id,
          "store_id" => $google_detail['setting']->store_id,
          "account_id" => $google_merchant_id
        );
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        // echo '<pre>'; print_r($args); echo '</pre>';
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        // echo '<pre>'; print_r($result);die('sdvgdasgb'); echo '</pre>';
        return $result->data;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function get_local_currency_rate_live($currency)
  {
    $url = esc_url_raw("https://query1.finance.yahoo.com/v8/finance/chart/USD{$currency}=X");
    $args = [
      'timeout' => 15,
      'headers' => [
        'Accept' => 'application/json',
      ],
    ];
    try {
      $response = wp_remote_get($url, $args);

      if (is_wp_error($response)) {
        return (object) [
          'status'  => 500,
          'error'   => true,
          'message' => $response->get_error_message(),
        ];
      }

      $body = wp_remote_retrieve_body($response);
      $data = json_decode($body);
      if (
        isset($data->chart->result[0]->meta->regularMarketPrice)
      ) {
        return (object) [
          'status' => 200,
          'rate'   => $data->chart->result[0]->meta->regularMarketPrice,
          'error'  => false,
        ];
      } else {
        return (object) [
          'status'  => 500,
          'error'   => true,
          'message' => 'Invalid data received from Yahoo Finance',
        ];
      }
    } catch (Exception $e) {
      return (object) [
        'status'  => 500,
        'error'   => true,
        'message' => $e->getMessage(),
      ];
    }
  }
}
