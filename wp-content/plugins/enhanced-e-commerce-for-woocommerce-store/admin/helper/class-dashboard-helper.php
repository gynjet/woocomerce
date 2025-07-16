<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * Conversios Dashboard
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('Conversios_Dashboard_Helper')) {
	class Conversios_Dashboard_Helper
	{
		protected $ShoppingApi;
		protected $TVC_Admin_Helper;
		protected $CustomApi;
		protected $apiDomain;
		protected $token;
		public function __construct()
		{
			$this->TVC_Admin_Helper = new TVC_Admin_Helper();
			$this->CustomApi = new CustomApi();
			add_action('wp_ajax_get_google_analytics_reports_dashboard', array($this, 'get_google_analytics_reports_dashboard'));
		}

		protected function admin_safe_ajax_call($nonce, $registered_nonce_name)
		{
			// only return results when the user is an admin with manage options
			if (is_admin() && wp_verify_nonce($nonce, $registered_nonce_name)) {
				return true;
			} else {
				return false;
			}
		}

		public function get_google_analytics_reports_dashboard()
		{
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				// $post_data = (object)$_POST;
				$subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
				$start_date = str_replace(' ', '', (isset($_POST['start_date'])) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : "");
				if ($start_date != "") {
					$date = DateTime::createFromFormat('d-m-Y', $start_date);
					$start_date = $date->format('Y-m-d');
				}
				$start_date == (false !== strtotime($start_date)) ? gmdate('Y-m-d', strtotime($start_date)) : gmdate('Y-m-d', strtotime('-1 month'));

				$end_date = str_replace(' ', '', (isset($_POST['end_date'])) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : "");
				if ($end_date != "") {
					$date = DateTime::createFromFormat('d-m-Y', $end_date);
					$end_date = $date->format('Y-m-d');
				}
				$end_date == (false !== strtotime($end_date)) ? gmdate('Y-m-d', strtotime($end_date)) : gmdate('Y-m-d', strtotime('now'));
				$start_date = sanitize_text_field($start_date);
				$end_date = sanitize_text_field($end_date);
				$return = array();
				if ($subscription_id != "") {
					$api_rs = "";
					$data = array(
						'subscription_id' => sanitize_text_field($subscription_id),
						'start_date' => $start_date,
						'end_date' => $end_date
					);

					$api_rs = $this->CustomApi->get_google_analytics_reports_ga4($data);
					if (isset($api_rs->error) && $api_rs->error == '') {
						if (isset($api_rs->data) && $api_rs->data != "") {
							$return = array('error' => false, 'data' => $api_rs->data, 'errors' => '');
						}
					} else {
						$return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
					}
				} else {
					$return = array('error' => true, 'errors' => esc_html__("Subscription id is null.", "enhanced-e-commerce-for-woocommerce-store"));
				}
			} else {
				$return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
			}
			echo wp_json_encode($return);
			wp_die();
		}
	}
}
new Conversios_Dashboard_Helper();
