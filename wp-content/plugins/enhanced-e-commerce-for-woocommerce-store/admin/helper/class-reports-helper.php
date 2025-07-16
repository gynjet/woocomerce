<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       
 * @since      1.0.0
 *
 * Woo Order Reports
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('Conversios_Reports_Helper')) {
	class Conversios_Reports_Helper
	{
		protected $ShoppingApi;
		protected $CustomApi;
		protected $TVC_Admin_Helper;
		protected $TVC_Admin_DB_Helper;
		public function __construct()
		{
			$this->req_int();
			$this->TVC_Admin_Helper = new TVC_Admin_Helper();
			$this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
			$this->ShoppingApi = new ShoppingApi();
			$this->CustomApi = new CustomApi();

			add_action('wp_ajax_set_email_configurationGA4', array($this, 'set_email_configurationGA4'));
			// add_action('wp_ajax_save_logo_attachment', array($this, 'save_logo_attachment'));
			//general ga4 reports
			add_action('wp_ajax_get_ga4_general_grid_reports', array($this, 'get_ga4_general_grid_reports'));
			add_action('wp_ajax_get_ga4_page_report', array($this, 'get_ga4_page_report'));
			add_action('wp_ajax_get_general_donut_reports', array($this, 'get_general_donut_reports'));
			add_action('wp_ajax_get_realtime_report', array($this, 'get_realtime_report'));
			add_action('wp_ajax_get_general_audience_report', array($this, 'get_general_audience_report'));
			add_action('wp_ajax_get_daily_visitors_report', array($this, 'get_daily_visitors_report'));
			add_action('wp_ajax_get_demographic_ga4_reports', array($this, 'get_demographic_ga4_reports'));
		}

		public function req_int()
		{
			if (!class_exists('ShoppingApi')) {
				require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/ShoppingApi.php');
			}
			if (!class_exists('CustomApi')) {
				require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
			}
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

		public function set_email_configurationGA4()
		{
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				$is_disabled = isset($_POST['is_disabled']) ? sanitize_text_field(wp_unslash($_POST['is_disabled'])) : "";
				$custom_email = isset($_POST['custom_email']) ? sanitize_text_field(wp_unslash($_POST['custom_email'])) : "";
				$email_frequency = isset($_POST['email_frequency']) ? sanitize_text_field(wp_unslash($_POST['email_frequency'])) : "";
				$save_email_bydefault = isset($_POST['save_email_bydefault']) ? sanitize_text_field(wp_unslash($_POST['save_email_bydefault'])) : "";
				$options = get_option("ee_options");
				if ($options) {
					$options = is_array($options) ? $options : unserialize($options);
					if (!isset($options['save_email_bydefault'])) {
						$options['save_email_bydefault'] = $save_email_bydefault;
						update_option('ee_options', serialize($options));
					}
				}
				if ($is_disabled != "" && $custom_email != "" && $email_frequency != "") {
					$api_rs = $this->ShoppingApi->set_email_configurationGA4($is_disabled, $custom_email, $email_frequency);
					echo wp_json_encode($api_rs);
				} else {
					echo wp_json_encode(array('error' => true, 'errors' => esc_html__("Invalid required fields", "enhanced-e-commerce-for-woocommerce-store")));
				}
			} else {
				echo wp_json_encode(array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
			}
			wp_die();
		}
		// function save_logo_attachment()
		// {
		// 	$attachment_id = intval($_POST['attachment_id']);
		// 	if ($attachment_id) {
		// 		update_option('ee_logo_attachment_id', $attachment_id);
		// 		wp_send_json_success(['message' => 'Logo saved successfully!']);
		// 	} else {
		// 		wp_send_json_error(['message' => 'Invalid attachment ID']);
		// 	}
		// }
		public function get_daily_visitors_report()
		{
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				$domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
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
				$api_rs = $this->ShoppingApi->ga4_general_daily_visitors_report($start_date, $end_date, $domain);

				if (isset($api_rs->error) && $api_rs->error == '') {
					if (isset($api_rs->data) && $api_rs->data != "") {
						$return = array('error' => false, 'data' => $api_rs->data);
					}
				} else {
					$return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
				}
			} else {
				$return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
			}
			echo wp_json_encode($return);
			wp_die();
		}
		public function get_general_audience_report()
		{
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				$domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
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
				$api_rs = $this->ShoppingApi->ga4_general_audience_report($start_date, $end_date, $domain);

				if (isset($api_rs->error) && $api_rs->error == '') {
					if (isset($api_rs->data) && $api_rs->data != "") {
						$return = array('error' => false, 'data' => $api_rs->data);
					}
				} else {
					$return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
				}
			} else {
				$return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
			}
			echo wp_json_encode($return);
			wp_die();
		}
		public function get_ga4_general_grid_reports()
		{
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				$domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
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
				$datediff = isset($_POST['datediff']) ? intval(wp_unslash($_POST['datediff'])) : "44";
				$old_end_date = sanitize_text_field(gmdate("Y-m-d", strtotime("-1 days", strtotime($start_date))));
				$old_start_date = sanitize_text_field(gmdate("Y-m-d", strtotime("-" . $datediff . " days", strtotime($old_end_date))));

				$api_rs_present = $this->ShoppingApi->ga4_general_grid_report($start_date, $end_date, $domain);

				if (isset($api_rs_present->error) && $api_rs_present->error == '') {
					if (isset($api_rs_present->data) && $api_rs_present->data != "") {
						//call for past data
						$api_rs_past = $this->ShoppingApi->ga4_general_grid_report($old_start_date, $old_end_date, $domain);

						if (isset($api_rs_past->error) && $api_rs_past->error == '') {
							if (isset($api_rs_past->data) && $api_rs_past->data != "") {
								$return = array('error' => false, 'data_present' => $api_rs_present->data, 'data_past' => $api_rs_past->data, 'errors' => '');
							}
						} else {
							$return = array('error' => false, 'data_present' => $api_rs_present->data, 'errors' => '');
						}
					}
				} else {
					$return = array('error' => true, 'errors' => isset($api_rs_present->message) ? $api_rs_present->message : '');
				}
			} else {
				$return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
			}
			echo wp_json_encode($return);
			wp_die();
		}
		public function get_realtime_report()
		{
			$domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				$api_rs = $this->ShoppingApi->ga4_realtime_report($domain);
				if (isset($api_rs->error) && $api_rs->error == '') {
					if (isset($api_rs->data)) {
						$return = array('error' => false, 'data' => $api_rs->data);
					}
				} else {
					$return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
				}
			} else {
				$return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
			}
			echo wp_json_encode($return);
			wp_die();
		}
		public function get_demographic_ga4_reports()
		{
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				$domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
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
				$report_name = isset($_POST['report_name']) ? sanitize_text_field(wp_unslash($_POST['report_name'])) : "";
				$api_rs = $this->ShoppingApi->ga4_demographics_report($start_date, $end_date, $domain, $report_name);

				if (isset($api_rs->error) && $api_rs->error == '') {
					if (isset($api_rs->data) && $api_rs->data != "") {
						$return = array('error' => false, 'data' => $api_rs->data);
					}
				} else {
					$return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
				}
			} else {
				$return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
			}
			echo wp_json_encode($return);
			wp_die();
		}
		public function get_general_donut_reports()
		{
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				$domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
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
				$report_name = isset($_POST['report_name']) ? sanitize_text_field(wp_unslash($_POST['report_name'])) : "";
				$api_rs = $this->ShoppingApi->ga4_general_donut_report($start_date, $end_date, $domain, $report_name);

				if (isset($api_rs->error) && $api_rs->error == '') {
					if (isset($api_rs->data) && $api_rs->data != "") {
						$return = array('error' => false, 'data' => $api_rs->data);
					}
				} else {
					$return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
				}
			} else {
				$return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
			}
			echo wp_json_encode($return);
			wp_die();
		}
		public function get_ga4_page_report()
		{
			$nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
			if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
				$domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
				$limit = isset($_POST['limit']) ? sanitize_text_field(wp_unslash($_POST['limit'])) : "10000";
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

				$api_rs = $this->ShoppingApi->ga4_page_report($start_date, $end_date, $domain, $limit);

				if (isset($api_rs->error) && $api_rs->error == '') {
					if (isset($api_rs->data) && $api_rs->data != "") {
						$return = array('error' => false, 'data' => $api_rs->data);
					}
				} else {
					$return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
				}
			} else {
				$return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
			}
			echo wp_json_encode($return);
			wp_die();
		}
	}
}
new Conversios_Reports_Helper();
