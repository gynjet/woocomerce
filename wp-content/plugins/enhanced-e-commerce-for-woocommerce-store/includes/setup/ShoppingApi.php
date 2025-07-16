<?php

class ShoppingApi
{

    private $customerId;
    private $merchantId;
    private $apiDomain;
    private $token;
    protected $TVC_Admin_Helper;
    private $customApiObj;
    private $subscriptionId;

    public function __construct()
    {
        $this->TVC_Admin_Helper = new TVC_Admin_Helper();
        $this->customApiObj = new CustomApi();
        //$queries = new TVC_Queries();
        $this->apiDomain = TVC_API_CALL_URL;
        $this->token = 'MTIzNA==';
        $this->merchantId = sanitize_text_field($this->TVC_Admin_Helper->get_merchantId());
        $this->customerId = sanitize_text_field($this->TVC_Admin_Helper->get_currentCustomerId());
        $this->subscriptionId = sanitize_text_field($this->TVC_Admin_Helper->get_subscriptionId());
    }

    public function getCampaigns()
    {
        try {
            $url = $this->apiDomain . '/campaigns/list';

            $data = [
                'merchant_id' => sanitize_text_field($this->merchantId),
                'customer_id' => sanitize_text_field($this->customerId)
            ];
            $args = array(
                'timeout' => 300,
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
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

                return new WP_REST_Response(
                    array(
                        'status' => $response_code,
                        'message' => esc_attr($response_message),
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

    public function getCategories($country_code)
    {
        try {
            $url = $this->apiDomain . '/products/categories';

            $data = [
                'customer_id' => sanitize_text_field($this->customerId),
                'country_code' => sanitize_text_field($country_code)
            ];

            $args = array(
                'timeout' => 300,
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
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

                return new WP_REST_Response(
                    array(
                        'status' => $response_code,
                        'message' => esc_attr($response_message),
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

    public function getCampaignDetails($campaign_id = '')
    {
        try {
            $url = $this->apiDomain . '/campaigns/detail';

            $data = [
                'merchant_id' => sanitize_text_field($this->merchantId),
                'customer_id' => sanitize_text_field($this->customerId),
                'campaign_id' => sanitize_text_field($campaign_id)
            ];

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
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
            if (!is_wp_error($request) && (isset($response_body->error) && $response_body->error == '')) {
                $response_body->data->category_id = (isset($response_body->data->category_id)) ? $response_body->data->category_id : '0';
                $response_body->data->category_level = (isset($response_body->data->category_level)) ? $response_body->data->category_level : '0';
                return new WP_REST_Response(
                    array(
                        'status' => $response_code,
                        'message' => esc_attr($response_message),
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
    public function createCampaign($campaign_name = '', $budget = 0, $target_country = 'US', $all_products = 0, $category_id = '', $category_level = '')
    {
        try {
            $header = array(
                "Authorization: Bearer MTIzNA==",
                "Content-Type" => "application/json"
            );
            $curl_url = $this->apiDomain . "/campaigns/create";
            $data = [
                'merchant_id' => sanitize_text_field($this->merchantId),
                'customer_id' => sanitize_text_field($this->customerId),
                'campaign_name' => sanitize_text_field($campaign_name),
                'budget' => sanitize_text_field($budget),
                'target_country' => sanitize_text_field($target_country),
                'all_products' => sanitize_text_field($all_products),
                'filter_by' => 'category',
                'filter_data' => ["id" => sanitize_text_field($category_id), "level" => sanitize_text_field($category_level)]
            ];

            $args = array(
                'timeout' => 300,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            $request = wp_remote_post(esc_url_raw($curl_url), $args);

            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response = json_decode(wp_remote_retrieve_body($request));

            $return = new \stdClass();
            if (isset($response->error) && $response->error == false) {
                $return->error = false;
                $return->message = esc_attr($response->message);
                $return->data = $response->data;
                return $return;
            } else {
                $return->error = true;
                $return->errors = $response->errors;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateCampaign($campaign_name = '', $budget = 0, $campaign_id = '', $budget_id = '', $target_country = '', $all_products = 0, $category_id = '', $category_level = '', $ad_group_id = '', $ad_group_resource_name = '')
    {
        try {
            $header = array(
                "Authorization: Bearer MTIzNA==",
                "Content-Type" => "application/json"
            );
            $curl_url = $this->apiDomain . '/campaigns/update';
            $data = [
                'merchant_id' => sanitize_text_field($this->merchantId),
                'customer_id' => sanitize_text_field($this->customerId),
                'campaign_id' => sanitize_text_field($campaign_id),
                'account_budget_id' => sanitize_text_field($budget_id),
                'campaign_name' => sanitize_text_field($campaign_name),
                'target_country' => sanitize_text_field($target_country),
                'budget' => sanitize_text_field($budget),
                'status' => 2, // ENABLE => 2, PAUSED => 3, REMOVED => 4
                'all_products' => sanitize_text_field($all_products),
                'ad_group_id' => sanitize_text_field($ad_group_id),
                'ad_group_resource_name' => sanitize_text_field($ad_group_resource_name),
                'filter_by' => 'category',
                'filter_data' => ["id" => sanitize_text_field($category_id), "level" => sanitize_text_field($category_level)]
            ];

            $args = array(
                'timeout' => 300,
                'headers' => $header,
                'method' => 'PATCH',
                'body' => wp_json_encode($data)
            );
            $request = wp_remote_post(esc_url_raw($curl_url), $args);
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response = json_decode(wp_remote_retrieve_body($request));
            $return = new \stdClass();
            if (isset($response->error) && $response->error == false) {
                $return->error = false;
                $return->message = esc_attr($response->message);
                $return->data = $response->data;
                return $return;
            } else {
                $return->error = true;
                $return->errors = $response->errors;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*set configuration for Schedule email ga4*/
    public function set_email_configurationGA4($is_disabled, $custom_email = '', $email_frequency = '')
    {
        try {
            $data = array('is_disabled' => $is_disabled, 'subscription_id' => sanitize_text_field($this->subscriptionId), 'custom_email' => $custom_email, 'emailFrequency' => $email_frequency);
            $curl_url = $this->apiDomain . '/actionable-dashboard/update-ga4-email-schedule';
            $header = array(
                "Authorization: Bearer $this->token",
                "Content-Type" => "application/json"
            );
            $args = array(
                'timeout' => 300,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            $request = wp_remote_post(esc_url_raw($curl_url), $args);
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response = json_decode(wp_remote_retrieve_body($request));
            $return = new \stdClass();
            if (isset($response->error) && $response->error == false) {
                $return->error = false;
                $return->message = esc_attr($response->message);
                $return->data = $response->data;
                return $return;
            } else {
                $return->error = true;
                $return->errors = $response->errors;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    //ga4general reports
    public function ga4_general_grid_report($from_date = '', $to_date = '', $domain = '')
    {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        try {
            $url = $this->apiDomain . '/ga-general-reports/get-ga-grid-report';
            $data = [
                'start_date' => sanitize_text_field($from_date),
                'end_date' => sanitize_text_field($to_date),
                'subscription_id' => sanitize_text_field($this->subscriptionId),
                'domain' => $domain,
                'store_id' => $google_detail['setting']->store_id
            ];
            $header = array(
                "Authorization: Bearer $this->token",
                "Content-Type" => "application/json"
            );
            $args = array(
                'timeout' => 10000,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            // Send remote request
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
                $return->errors = $result->errors;
                $return->status = $response_code;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function ga4_realtime_report($domain = '')
    {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        try {
            $url = $this->apiDomain . '/ga-general-reports/get-ga-realtime-reports';
            $data = [
                'subscription_id' => sanitize_text_field($this->subscriptionId),
                'domain' => $domain,
                'store_id' => $google_detail['setting']->store_id
            ];
            $header = array(
                "Authorization: Bearer $this->token",
                "Content-Type" => "application/json"
            );
            $args = array(
                'timeout' => 10000,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            // Send remote request
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
                $return->errors = $result->errors;
                $return->status = $response_code;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function ga4_general_daily_visitors_report($from_date = '', $to_date = '', $domain = '')
    {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
 	    $store_id = $google_detail['setting']->store_id;
        try {
            $url = $this->apiDomain . '/ga-general-reports/get-ga-daily-visitors-report';
            $data = [
                'start_date' => sanitize_text_field($from_date),
                'end_date' => sanitize_text_field($to_date),
                'subscription_id' => sanitize_text_field($this->subscriptionId),
                'domain' => $domain,
                'store_id' => $store_id
            ];
            $header = array(
                "Authorization: Bearer $this->token",
                "Content-Type" => "application/json"
            );
            $args = array(
                'timeout' => 10000,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            // Send remote request
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
                $return->errors = $result->errors;
                $return->status = $response_code;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function ga4_general_audience_report($from_date = '', $to_date = '', $domain = '')
    {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
 	    $store_id = $google_detail['setting']->store_id;
        try {
            $url = $this->apiDomain . '/ga-general-reports/get-ga-audience-report';
            $data = [
                'start_date' => sanitize_text_field($from_date),
                'end_date' => sanitize_text_field($to_date),
                'subscription_id' => sanitize_text_field($this->subscriptionId),
                'domain' => $domain,
                'store_id' => $store_id
            ];
            $header = array(
                "Authorization: Bearer $this->token",
                "Content-Type" => "application/json"
            );
            $args = array(
                'timeout' => 10000,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            // Send remote request
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
                $return->errors = $result->errors;
                $return->status = $response_code;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function ga4_demographics_report($from_date = '', $to_date = '', $domain = '', $report_name = '')
    {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
 	    $store_id = $google_detail['setting']->store_id;
        try {
            $url = $this->apiDomain . '/ga-general-reports/get-ga-demographics-reports';
            $data = [
                'start_date' => sanitize_text_field($from_date),
                'end_date' => sanitize_text_field($to_date),
                'subscription_id' => sanitize_text_field($this->subscriptionId),
                'domain' => $domain,
                'dimension' => $report_name,
                'limit' => 5,
                'store_id' => $store_id
            ];
            $header = array(
                "Authorization: Bearer $this->token",
                "Content-Type" => "application/json"
            );
            $args = array(
                'timeout' => 10000,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            // Send remote request
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
                $return->errors = $result->errors;
                $return->status = $response_code;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function ga4_general_donut_report($from_date = '', $to_date = '', $domain = '', $report_name = '')
    {
        try {
            $endpoint = "get-ga-devicebreakdown-report";
            if ($report_name == "conv_users_chart") {
                $endpoint = "get-ga-users-report";
            }
            $url = $this->apiDomain . '/ga-general-reports/' . $endpoint;
            $data = [
                'start_date' => sanitize_text_field($from_date),
                'end_date' => sanitize_text_field($to_date),
                'subscription_id' => sanitize_text_field($this->subscriptionId),
                'domain' => $domain
            ];
            $header = array(
                "Authorization: Bearer $this->token",
                "Content-Type" => "application/json"
            );
            $args = array(
                'timeout' => 10000,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            // Send remote request
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
                $return->errors = $result->errors;
                $return->status = $response_code;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ga4_page_report($from_date = '', $to_date = '', $domain = '', $limit = '')
    {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        try {
            $url = $this->apiDomain . '/ga-general-reports/get-ga-pages-report';
            $data = [
                'start_date' => sanitize_text_field($from_date),
                'end_date' => sanitize_text_field($to_date),
                'subscription_id' => sanitize_text_field($this->subscriptionId),
                'domain' => $domain,
                'dimension' => 'pagePath',
                'limit' => $limit,
                'orderbymetric' => 'screenPageViews',
                'offset' => '0',
                'store_id' => $google_detail['setting']->store_id
            ];
            $header = array(
                "Authorization: Bearer $this->token",
                "Content-Type" => "application/json"
            );
            $args = array(
                'timeout' => 10000,
                'headers' => $header,
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            // Send remote request
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
                $return->errors = $result->errors;
                $return->status = $response_code;
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}