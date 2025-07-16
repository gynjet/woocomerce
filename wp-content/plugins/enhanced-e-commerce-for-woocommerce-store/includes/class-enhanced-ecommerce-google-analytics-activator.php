<?php

/**
 * Fired during plugin activation
 *
 * @link       test.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics_Activator
 * @subpackage Enhanced_Ecommerce_Google_Analytics_Activator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Enhanced_Ecommerce_Google_Analytics_Activator
 * @subpackage Enhanced_Ecommerce_Google_Analytics_Activator/includes
 * @author     Tatvic
 */

class Enhanced_Ecommerce_Google_Analytics_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        $ee_options_settings = unserialize(get_option('ee_options'));

        $subscriptionId = (isset($ee_options_settings['subscription_id'])) ? $ee_options_settings['subscription_id'] : "";

        $apiDomain = TVC_API_CALL_URL;

        $header = array(
            "Authorization: Bearer 'MTIzNA=='",
            "Content-Type" => "application/json"
        );

        $current_user = wp_get_current_user();

        if (empty($subscriptionId)) {
            $current_user = wp_get_current_user();

            // Do customer login
            $url = $apiDomain . '/customers/login';
            $header = array("Authorization: Bearer MTIzNA==", "content-type: application/json");
            $postData = [
                'first_name' => "",
                'last_name' => "",
                'email' => $current_user->user_email,
                'sign_in_type' => 1,
                'app_id' => CONV_APP_ID,
                'platform_id' => 1
            ];
            $args = array(
                'headers' => $header,
                'method' => 'POST',
                "timeout" => 1000,
                'body' => $postData
            );
            $dologin_response = wp_remote_post(esc_url_raw($url), $args);


            // Update token to subs
            $url = $apiDomain . '/customer-subscriptions/update-token';
            $header = array("Authorization: Bearer MTIzNA==", "content-type: application/json");
            $postData = [
                'subscription_id' => "",
                'gmail' => $current_user->user_email,
                'domain' => get_site_url(),
                'app_id' =>  CONV_APP_ID,
                'platform_id' => 1
            ];
            $args = array(
                'headers' => $header,
                'method' => 'POST',
                "timeout" => 1000,
                'body' => $postData
            );
            $request = wp_remote_post(esc_url_raw($url), $args);
            $updatetoken_response = json_decode(wp_remote_retrieve_body($request));


            //Get subscription details
            $url = $apiDomain . '/customer-subscriptions/subscription-detail';
            $header = array("Authorization: Bearer MTIzNA==", "content-type: application/json");
            $postData = [
                'subscription_id' => $updatetoken_response->data->customer_subscription_id,
                'domain' => get_site_url(),
                'app_id' => CONV_APP_ID,
                'platform_id' => 1
            ];
            $args = array(
                'headers' => $header,
                'method' => 'POST',
                "timeout" => 1000,
                'body' => $postData
            );
            $request = wp_remote_post(esc_url_raw($url), $args);
            $subsdetails_response = json_decode(wp_remote_retrieve_body($request));

            $eeapidata = array("setting" => $subsdetails_response->data);
            update_option("ee_api_data", serialize($eeapidata));

            $subscriptiondata = $subsdetails_response->data;

            $eeoptions = array();
            $eeoptions["subscription_id"] = (isset($subscriptiondata->id) && $subscriptiondata->id != "") ? sanitize_text_field($subscriptiondata->id) : "";
            $eeoptions["ga_eeT"] = "on";
            $eeoptions["ga_ST"] = "on";
            $eeoptions["gm_id"] = (isset($subscriptiondata->measurement_id) && $subscriptiondata->measurement_id != "") ? sanitize_text_field($subscriptiondata->measurement_id) : "";
            $eeoptions["ga_id"] = (isset($subscriptiondata->property_id) && $subscriptiondata->property_id != "") ? sanitize_text_field($subscriptiondata->property_id) : "";
            $eeoptions["google_ads_id"] = (isset($subscriptiondata->google_ads_id) && $subscriptiondata->google_ads_id != "") ? sanitize_text_field($subscriptiondata->google_ads_id) : "";
            $eeoptions["google_merchant_id"] = (isset($subscriptiondata->google_merchant_center_id) && $subscriptiondata->google_merchant_center_id != "") ? sanitize_text_field($subscriptiondata->google_merchant_center_id) : "";
            $eeoptions["tracking_option"] = (isset($subscriptiondata->tracking_option) && $subscriptiondata->tracking_option != "") ? sanitize_text_field($subscriptiondata->tracking_option) : "";
            $eeoptions["ga_gUser"] = "on";
            $eeoptions["ga_Impr"] = "6";
            $eeoptions["ga_IPA"] = "on";
            $eeoptions["ga_PrivacyPolicy"] = "on";
            $eeoptions["google-analytic"] = "";
            $eeoptions["ga4_api_secret"] = "";
            $eeoptions["ga_CG"] = "";
            $eeoptions["ga_optimize_id"] = "";
            $eeoptions["tracking_method"] = (isset($subscriptiondata->tracking_method) && $subscriptiondata->tracking_method != "") ? sanitize_text_field($subscriptiondata->tracking_method) : "";
            $eeoptions["tvc_product_list_data_collection_method"] = (isset($subscriptiondata->tvc_product_list_data_collection_method) && $subscriptiondata->tvc_product_list_data_collection_method != "") ? sanitize_text_field($subscriptiondata->tvc_product_list_data_collection_method) : "";
            $eeoptions["tvc_product_detail_data_collection_method"] = (isset($subscriptiondata->tvc_product_detail_data_collection_method) && $subscriptiondata->tvc_product_detail_data_collection_method != "") ? sanitize_text_field($subscriptiondata->tvc_product_detail_data_collection_method) : "";
            $eeoptions["tvc_checkout_data_collection_method"] = (isset($subscriptiondata->tvc_checkout_data_collection_method) && $subscriptiondata->tvc_checkout_data_collection_method != "") ? sanitize_text_field($subscriptiondata->tvc_checkout_data_collection_method) : "";
            $eeoptions["tvc_thankyou_data_collection_method"] = (isset($subscriptiondata->tvc_thankyou_data_collection_method) && $subscriptiondata->tvc_thankyou_data_collection_method != "") ? sanitize_text_field($subscriptiondata->tvc_thankyou_data_collection_method) : "";
            $eeoptions["tvc_product_detail_addtocart_selector"] = (isset($subscriptiondata->tvc_product_detail_addtocart_selector) && $subscriptiondata->tvc_product_detail_addtocart_selector != "") ? sanitize_text_field($subscriptiondata->tvc_product_detail_addtocart_selector) : "";
            $eeoptions["tvc_product_detail_addtocart_selector_type"] = (isset($subscriptiondata->tvc_product_detail_addtocart_selector_type) && $subscriptiondata->tvc_product_detail_addtocart_selector_type != "") ? sanitize_text_field($subscriptiondata->tvc_product_detail_addtocart_selector_type) : "";
            $eeoptions["tvc_product_detail_addtocart_selector_val"] = (isset($subscriptiondata->tvc_product_detail_addtocart_selector_val) && $subscriptiondata->tvc_product_detail_addtocart_selector_val != "") ? sanitize_text_field($subscriptiondata->tvc_product_detail_addtocart_selector_val) : "";
            $eeoptions["tvc_checkout_step_2_selector"] = (isset($subscriptiondata->tvc_checkout_step_2_selector) && $subscriptiondata->tvc_checkout_step_2_selector != "") ? sanitize_text_field($subscriptiondata->tvc_checkout_step_2_selector) : "";
            $eeoptions["tvc_checkout_step_2_selector_type"] = (isset($subscriptiondata->tvc_checkout_step_2_selector_type) && $subscriptiondata->tvc_checkout_step_2_selector_type != "") ? sanitize_text_field($subscriptiondata->tvc_checkout_step_2_selector_type) : "";
            $eeoptions["tvc_checkout_step_2_selector_val"] = (isset($subscriptiondata->tvc_checkout_step_2_selector_val) && $subscriptiondata->tvc_checkout_step_2_selector_val != "") ? sanitize_text_field($subscriptiondata->tvc_checkout_step_2_selector_val) : "";
            $eeoptions["tvc_checkout_step_3_selector"] = (isset($subscriptiondata->tvc_checkout_step_3_selector) && $subscriptiondata->tvc_checkout_step_3_selector != "") ? sanitize_text_field($subscriptiondata->tvc_checkout_step_3_selector) : "";
            $eeoptions["tvc_checkout_step_3_selector_type"] = (isset($subscriptiondata->tvc_checkout_step_3_selector_type) && $subscriptiondata->tvc_checkout_step_3_selector_type != "") ? sanitize_text_field($subscriptiondata->tvc_checkout_step_3_selector_type) : "";
            $eeoptions["tvc_checkout_step_3_selector_val"] = (isset($subscriptiondata->tvc_checkout_step_3_selector_val) && $subscriptiondata->tvc_checkout_step_3_selector_val != "") ? sanitize_text_field($subscriptiondata->tvc_checkout_step_3_selector_val) : "";
            $eeoptions["want_to_use_your_gtm"] = (isset($subscriptiondata->want_to_use_your_gtm) && $subscriptiondata->want_to_use_your_gtm != "") ? sanitize_text_field($subscriptiondata->want_to_use_your_gtm) : "";
            $eeoptions["use_your_gtm_id"] = (isset($subscriptiondata->use_your_gtm_id) && $subscriptiondata->use_your_gtm_id != "") ? sanitize_text_field($subscriptiondata->use_your_gtm_id) : "";
            $eeoptions["fb_pixel_id"] = (isset($subscriptiondata->fb_pixel_id) && $subscriptiondata->fb_pixel_id != "") ? sanitize_text_field($subscriptiondata->fb_pixel_id) : "";
            $eeoptions["microsoft_ads_pixel_id"] = (isset($subscriptiondata->microsoft_ads_pixel_id) && $subscriptiondata->microsoft_ads_pixel_id != "") ? sanitize_text_field($subscriptiondata->microsoft_ads_pixel_id) : "";
            $eeoptions["twitter_ads_pixel_id"] = (isset($subscriptiondata->twitter_ads_pixel_id) && $subscriptiondata->twitter_ads_pixel_id != "") ? sanitize_text_field($subscriptiondata->twitter_ads_pixel_id) : "";
            $eeoptions["pinterest_ads_pixel_id"] = (isset($subscriptiondata->pinterest_ads_pixel_id) && $subscriptiondata->pinterest_ads_pixel_id != "") ? sanitize_text_field($subscriptiondata->pinterest_ads_pixel_id) : "";
            $eeoptions["snapchat_ads_pixel_id"] = (isset($subscriptiondata->snapchat_ads_pixel_id) && $subscriptiondata->snapchat_ads_pixel_id != "") ? sanitize_text_field($subscriptiondata->snapchat_ads_pixel_id) : "";
            $eeoptions["tiKtok_ads_pixel_id"] = (isset($subscriptiondata->tiKtok_ads_pixel_id) && $subscriptiondata->tiKtok_ads_pixel_id != "") ? sanitize_text_field($subscriptiondata->tiKtok_ads_pixel_id) : "";
            $eeoptions["fb_conversion_api_token"] = (isset($subscriptiondata->fb_conversion_api_token) && $subscriptiondata->fb_conversion_api_token != "") ? sanitize_text_field($subscriptiondata->fb_conversion_api_token) : "";

            $eeoptions["msclarity_pixel_id"] = (isset($subscriptiondata->msclarity_pixel_id) && $subscriptiondata->msclarity_pixel_id != "") ? sanitize_text_field($subscriptiondata->msclarity_pixel_id) : "";
            $eeoptions["msbing_conversion"] = (isset($subscriptiondata->msbing_conversion) && $subscriptiondata->msbing_conversion != "") ? sanitize_text_field($subscriptiondata->msbing_conversion) : "";
            $eeoptions["crazyegg_pixel_id"] = (isset($subscriptiondata->crazyegg_pixel_id) && $subscriptiondata->crazyegg_pixel_id != "") ? sanitize_text_field($subscriptiondata->crazyegg_pixel_id) : "";
            $eeoptions["hotjar_pixel_id"] = (isset($subscriptiondata->hotjar_pixel_id) && $subscriptiondata->hotjar_pixel_id != "") ? sanitize_text_field($subscriptiondata->hotjar_pixel_id) : "";

            update_option("ee_options", serialize($eeoptions));
        }
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->update_app_status();
        $TVC_Admin_Helper->app_activity_detail("activate");
    }
}